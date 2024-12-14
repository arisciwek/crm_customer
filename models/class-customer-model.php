<?php
/**
 * Customer Management System - Customer Model
 *
 * Handles all database operations for customer data including CRUD operations.
 * This model interacts with the crm_customers table and provides
 * abstraction for customer data management.
 *
 * @package     CustomerManagement
 * @subpackage  Models
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        models/class-customer-model.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */
 
namespace Customer\Models;

class Customer_Model {
    private $table_name;
    private $cache_manager;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'crm_customers';
        $this->cache_manager = new \Customer\Includes\Cache_Manager();
    }

    /**
     * Get semua customer dengan cache
     */
    public function get_all_customers() {
        // Cek cache dulu
        $cache_key = 'all_customers';
        $customers = $this->cache_manager->get($cache_key, 'customer');

        if ($customers === false) {
            global $wpdb;
            $customers = $wpdb->get_results(
                "SELECT * FROM {$this->table_name} WHERE status = 'active'",
                ARRAY_A
            );

            // Simpan ke cache
            $this->cache_manager->set($cache_key, $customers, 'customer');
        }

        return $customers;
    }

    /**
     * Get single customer dengan cache
     */
    public function get_customer($id) {
        $cache_key = 'customer_' . $id;
        $customer = $this->cache_manager->get($cache_key, 'customer');

        if ($customer === false) {
            global $wpdb;
            $customer = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM {$this->table_name} WHERE id = %d",
                    $id
                ),
                ARRAY_A
            );

            if ($customer) {
                $this->cache_manager->set($cache_key, $customer, 'customer');
            }
        }

        return $customer;
    }

    /**
     * Create customer dan invalidate cache
     */
    public function create_customer($data) {
        global $wpdb;

        // Generate customer code
        $generator = new Customer_Code_Generator();
        $customer_code = $generator->generate_unique_code();

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'customer_code' => $customer_code,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'notes' => $data['notes'],
                'created_by' => get_current_user_id()
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%d')
        );

        if ($result) {
            $id = $wpdb->insert_id;
            // Invalidate cache
            $this->invalidate_customer_cache();
            return $id;
        }

        return false;
    }

    /**
     * Update customer dan invalidate cache
     */
    public function update_customer($id, $data) {
        global $wpdb;

        $result = $wpdb->update(
            $this->table_name,
            array(
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'notes' => $data['notes']
            ),
            array('id' => $id),
            array('%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );

        if ($result !== false) {
            // Invalidate cache
            $this->invalidate_customer_cache($id);
        }

        return $result;
    }

    /**
     * Delete customer dan invalidate cache
     */
    public function delete_customer($id) {
        global $wpdb;

        // Soft delete dengan update status
        $result = $wpdb->update(
            $this->table_name,
            array('status' => 'inactive'),
            array('id' => $id),
            array('%s'),
            array('%d')
        );

        if ($result) {
            // Invalidate cache
            $this->invalidate_customer_cache($id);
        }

        return $result;
    }

    /**
     * Invalidate cache untuk customer tertentu atau semua customer
     */
    private function invalidate_customer_cache($id = null) {
        if ($id) {
            // Hapus cache untuk customer spesifik
            $this->cache_manager->delete('customer_' . $id, 'customer');
        }

        // Hapus cache daftar semua customer
        $this->cache_manager->delete('all_customers', 'customer');
    }
}
