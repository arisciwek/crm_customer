<?php

/**
 * Customer Management System - Admin Controller
 *
 * Handles all administration functionality including menu creation,
 * script/style loading, and main page rendering. This controller
 * serves as the main entry point for the admin interface.
 *
 * Functions:
 * - Creates admin menus and submenus
 * - Loads required scripts and styles
 * - Renders main admin pages
 * - Manages permissions and settings pages
 *
 * @package     CustomerManagement
 * @subpackage  Controllers
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        controllers/class-admin-controller.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */
 
namespace Customer\Controllers;

class Admin_Controller {
    private $system_settings_controller;
    private $permission_settings_controller; // Added permission settings controller

    public function __construct() {
        // Initialize controllers
        $this->system_settings_controller = new System_Settings_Controller();
        $this->permission_settings_controller = new Permission_Settings_Controller(); // New initialization

        // Hooking into admin_menu
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

        // Submenu - will be added later
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
            array($this->system_settings_controller, 'render_settings_page')
        );

        // Permissions submenu
        add_submenu_page(
            'customer-management',     // Parent slug
            'Permissions',            // Page title
            'Permissions',            // Menu title
            'manage_options',         // Capability
            'customer-permissions',   // Menu slug
            array($this->permission_settings_controller, 'render_settings_page') // Updated to use permission settings controller
        );
    }

    public function enqueue_scripts($hook) {
        // Check specific admin page
        if (strpos($hook, 'customer-management') === false &&
            strpos($hook, 'customer-settings') === false &&
            strpos($hook, 'customer-permissions') === false) {
            return;
        }

        // Customer Management List
        if ($hook === 'toplevel_page_customer-management') {
            // DataTables
            wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
            wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'));

            // Plugin assets
            wp_enqueue_style('customer-css', CUSTOMER_URL . 'assets/css/customer.css', array(), CUSTOMER_VERSION);
            wp_enqueue_script('customer-js', CUSTOMER_URL . 'assets/js/customer.js', array('jquery', 'datatables-js'), CUSTOMER_VERSION);

            wp_localize_script('customer-js', 'customerPlugin', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('customer_plugin_nonce')
            ));
        }

        // Settings page
        if ($hook === 'customers_page_customer-settings' || $hook === 'customers_page_customer-permissions') {
            wp_enqueue_style('settings-css', CUSTOMER_URL . 'assets/css/settings.css', array(), CUSTOMER_VERSION);
            wp_enqueue_script('settings-js', CUSTOMER_URL . 'assets/js/settings.js', array('jquery'), CUSTOMER_VERSION, true);

            // Localize script for settings page
            wp_localize_script('settings-js', 'customerSettings', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('customer_settings_nonce')
            ));
        }
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
}
