<?php
namespace Customer\Controllers;

class Admin_Controller {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function add_menu_pages() {
        // Main menu
        add_menu_page(
            'Customer Management',  // Page title
            'Customers',           // Menu title
            'manage_options',      // Capability
            'customer-management', // Menu slug
            array($this, 'render_main_page'),
            'dashicons-groups',
            6
        );

        // Submenu - akan ditambahkan nanti
        add_submenu_page(
            'customer-management',     // Parent slug
            'Customer List',           // Page title
            'Customer List',           // Menu title
            'manage_options',          // Capability
            'customer-management',     // Menu slug (same as parent for main submenu)
            array($this, 'render_main_page')
        );

        // Settings submenu
        add_submenu_page(
            'customer-management',     // Parent slug
            'Settings',               // Page title
            'Settings',               // Menu title
            'manage_options',         // Capability
            'customer-settings',      // Menu slug
            array($this, 'render_settings_page')
        );

    }

    public function enqueue_scripts() {
        $screen = get_current_screen();
        if (!$screen) return;

        // Customer Management List
        if ($screen->id === 'toplevel_page_customer-management') {
            // DataTables
            wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
            wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'));

            // Plugin assets
            wp_enqueue_style('customer-css', CUSTOMER_URL . 'assets/css/customer.css', array(), CUSTOMER_VERSION);
            wp_enqueue_script('customer-js', CUSTOMER_URL . 'assets/js/customer.js', array('jquery', 'datatables-js'), CUSTOMER_VERSION);
        }

        // Settings & Permissions page
        if ($screen->id === 'customers_page_customer-settings') {
            wp_enqueue_style('settings-css', CUSTOMER_URL . 'assets/css/settings.css', array(), CUSTOMER_VERSION);
            wp_enqueue_script('settings-js', CUSTOMER_URL . 'assets/js/settings.js', array('jquery'), CUSTOMER_VERSION, true);

            $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'system';
            if ($current_tab === 'permissions') {
                wp_enqueue_script('permissions-js', CUSTOMER_URL . 'assets/js/permissions.js', array('jquery'), CUSTOMER_VERSION, true);
                // Tambahkan ini jika diperlukan CSS khusus untuk permissions
                //wp_enqueue_style('permissions-css', CUSTOMER_URL . 'assets/css/permissions.css', array('settings-css'), CUSTOMER_VERSION);
            }
        }

        wp_localize_script('customer-js', 'customerPlugin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('customer_plugin_nonce')
        ));
    }

    public function render_main_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $view_file = CUSTOMER_PATH . 'views/admin/customer-list.php';
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            error_log('View file not found: ' . $view_file);
            echo 'Error: View file not found.';
        }
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $view_file = CUSTOMER_PATH . 'views/admin/settings-system.php';
        if (file_exists($view_file)) {
            include $view_file;
        } else {
            error_log('Settings view file not found: ' . $view_file);
            echo 'Error: Settings view file not found.';
        }
    }

}
