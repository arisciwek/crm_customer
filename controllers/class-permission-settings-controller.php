<?php
/**
 * Customer Management System - Permission Settings Controller
 *
 * Handles all settings-related functionality for the CRM system.
 * This controller manages permission settings, role capabilities,
 * and integration with WordPress Settings API.
 *
 * Functions:
 * - Registers settings in WordPress
 * - Handles permission matrix updates
 * - Validates and sanitizes settings data
 * - Manages role capability assignments
 *
 * @package     CustomerManagement
 * @subpackage  Controllers
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        controllers/class-permission-settings-controller.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

 namespace Customer\Controllers;

 defined('ABSPATH') || exit;

 class Permission_Settings_Controller {
     public function __construct() {
         add_action('admin_init', array($this, 'register_settings'));
     }

     public function register_settings() {
         // Register the settings group and option
         register_setting(
             'crm_permissions_settings',  // Option group
             'crm_role_caps',            // Option name
             array(
                 'type' => 'array',
                 'sanitize_callback' => array($this, 'sanitize_role_caps'),
                 'default' => array()
             )
         );

         // Add settings section
         add_settings_section(
             'crm_permissions_section',          // ID
             'Role Permissions',                 // Title
             array($this, 'render_permissions_section'), // Callback
             'crm_permissions_settings'          // Page
         );

         // Add settings field
         add_settings_field(
             'crm_role_capabilities',           // ID
             'Role Capabilities',               // Title
             array($this, 'render_capabilities_field'), // Callback
             'crm_permissions_settings',        // Page
             'crm_permissions_section'          // Section
         );
     }

     public function sanitize_role_caps($input) {
         if (!current_user_can('manage_options')) {
             return false;
         }

         $capabilities = \Customer\Includes\Customer_Capabilities::get_all_caps();
         $roles = get_editable_roles();
         $sanitized_input = array();

         foreach ($roles as $role_name => $role_info) {
             if ($role_name === 'administrator') {
                 continue; // Skip administrator role
             }

             $role = get_role($role_name);

             foreach ($capabilities as $cap => $label) {
                 if (isset($input[$role_name][$cap])) {
                     $role->add_cap($cap);
                     $sanitized_input[$role_name][$cap] = true;
                 } else {
                     $role->remove_cap($cap);
                     $sanitized_input[$role_name][$cap] = false;
                 }
             }
         }

         return $sanitized_input;
     }

     public function render_permissions_section() {
         echo '<p>Configure permissions for different user roles in the CRM system.</p>';
     }

     public function render_capabilities_field() {
         // This will be handled in the view file
     }

     public function render_settings_page() {
         if (!current_user_can('manage_options')) {
             wp_die(__('You do not have sufficient permissions to access this page.'));
         }

         // Get capabilities and roles
         $capabilities = \Customer\Includes\Customer_Capabilities::get_all_caps();
         $roles = get_editable_roles();

         // Include the view file
         require_once CUSTOMER_PATH . 'views/admin/settings-permissions.php';
     }
 }
