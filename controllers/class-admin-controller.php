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

        // Tambahkan submenu Permissions
        add_submenu_page(
            'customer-management',     // Parent slug
            'Permissions',            // Page title
            'Permissions',            // Menu title
            'manage_options',         // Capability
            'customer-permissions',   // Menu slug
            array($this, 'render_permissions_page')
        );

    }

    public function render_permissions_page() {
        $settings = new Settings_Controller();
        $settings->render_settings_page();
    }

    public function enqueue_scripts($hook) {
            // Only load on our plugin pages
            if (strpos($hook, 'customer-management') === false) {
                return;
            }

            // DataTables
            wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
            wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'));

            // Plugin assets
            wp_enqueue_style('customer-css', CUSTOMER_URL . 'assets/css/customer.css', array(), CUSTOMER_VERSION);
            wp_enqueue_script('customer-js', CUSTOMER_URL . 'assets/js/customer.js', array('jquery', 'datatables-js'), CUSTOMER_VERSION);

            // Settings page specific
            if (strpos($hook, 'customer-settings') !== false) {
                wp_enqueue_style('settings-css', CUSTOMER_URL . 'assets/css/settings.css', array(), CUSTOMER_VERSION);
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
/*
    public function render_settings_page() {
      if (!current_user_can('manage_options')) {
          wp_die(__('You do not have sufficient permissions to access this page.'));
      }

      // Inisialisasi System Settings Controller
      $system_settings = new System_Settings_Controller();
      $system_settings->render_settings_page();
  }
  */

}
