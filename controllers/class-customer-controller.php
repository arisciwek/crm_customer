<?php
namespace Customer\Controllers;

class Customer_Controller {
    private $model;

        public function __construct() {
            add_action('init', array($this, 'init'));

            // AJAX handlers
            add_action('wp_ajax_get_customer_details', array($this, 'get_customer_details'));
            add_action('wp_ajax_get_customers_data', array($this, 'get_customers_data'));
            add_action('wp_ajax_create_customer', array($this, 'create_customer'));
            add_action('wp_ajax_update_customer', array($this, 'update_customer'));
            add_action('wp_ajax_delete_customer', array($this, 'delete_customer'));
            add_action('wp_ajax_get_datatable_config', array($this, 'get_datatable_config'));
            add_action('wp_ajax_clear_crm_cache', array($this, 'clear_crm_cache'));
    }

    public function init() {
        $this->model = new \Customer\Models\Customer_Model();
    }

    public function get_customers_data() {
        check_ajax_referer('customer_plugin_nonce', 'nonce');

        $customers = $this->model->get_all_customers();
        $data = array();

        foreach ($customers as $customer) {
            $actions = sprintf(
                '<button onclick="Customer.loadDetails(%d)" class="button">View Details</button>',
                $customer['id']
            );

            $data[] = array(
                'id' => $customer['id'],
                'name' => $customer['name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
                'actions' => $actions
            );
        }

        wp_send_json(array('data' => $data));
    }

    public function get_customer_details() {
        check_ajax_referer('customer_plugin_nonce', 'nonce');

        $customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $customer = $this->model->get_customer($customer_id);

        if ($customer) {
            ob_start();
            include CUSTOMER_PATH . 'views/admin/tabs/customer-details.php';
            $html = ob_get_clean();

            wp_send_json_success(array(
                'html' => $html,
                'data' => $customer
            ));
        } else {
            wp_send_json_error('Customer not found');
        }
    }

    public function create_customer() {
        check_ajax_referer('customer_plugin_nonce', 'nonce');

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_textarea_field($_POST['address']),
            'notes' => sanitize_textarea_field($_POST['notes'])
        );

        $result = $this->model->create_customer($data);

        if ($result) {
            wp_send_json_success(array('id' => $result));
        } else {
            wp_send_json_error('Failed to create customer');
        }
    }

    public function update_customer() {
        check_ajax_referer('customer_plugin_nonce', 'nonce');

        $customer_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address' => sanitize_textarea_field($_POST['address']),
            'notes' => sanitize_textarea_field($_POST['notes'])
        );

        $result = $this->model->update_customer($customer_id, $data);

        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to update customer');
        }
    }

    public function delete_customer() {
        check_ajax_referer('customer_plugin_nonce', 'nonce');

        $customer_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $result = $this->model->delete_customer($customer_id);

        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Failed to delete customer');
        }
    }

    /**
     * Clear all CRM cache
     */
    public function clear_crm_cache() {
        check_ajax_referer('clear_cache_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
            return;
        }

        $cache_manager = new \Customer\Includes\Cache_Manager();
        $result = $cache_manager->clear_all();

        if ($result) {
            wp_send_json_success('Cache cleared successfully');
        } else {
            wp_send_json_error('Failed to clear cache');
        }
    }

    public function get_datatable_config() {
        try {
            // Verify nonce
            check_ajax_referer('customer_plugin_nonce', 'nonce');

            // Check if user has permission to view customers
            if (!current_user_can('crm_view_customers') && !current_user_can('crm_view_own_customers')) {
                wp_send_json_error(array(
                    'message' => 'You do not have permission to view customer data'
                ));
                return;
            }

            // Get DataTable configuration
            $datatable_manager = new \Customer\Includes\DataTable_Manager();
            $config = $datatable_manager->get_config('customer');

            if (!$config) {
                wp_send_json_error(array(
                    'message' => 'Failed to retrieve DataTable configuration'
                ));
                return;
            }

            wp_send_json_success($config);

        } catch (\Exception $e) {
            wp_send_json_error(array(
                'message' => 'Error retrieving DataTable configuration: ' . $e->getMessage()
            ));
        }
    }



}
