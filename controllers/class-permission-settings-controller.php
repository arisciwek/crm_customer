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
 * - Reset permissions to default
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
     private $option_group = 'crm_permissions_options';
     private $option_name = 'crm_role_caps';

     public function __construct() {
         add_action('admin_init', array($this, 'register_settings'));
         add_action('wp_ajax_reset_customer_permissions', array($this, 'handle_reset_customer_permissions')); // Updated action name
     }

     /**
      * Register all settings fields and sections
      */
     public function register_settings() {
         // Register the settings
         register_setting(
             $this->option_group,    // Option group
             $this->option_name,     // Option name
             array(
                 'type' => 'array',
                 'sanitize_callback' => array($this, 'sanitize_role_caps'),
                 'default' => array()
             )
         );

         // Add settings section
         add_settings_section(
             'crm_permissions_section',
             'Role Permissions',
             array($this, 'render_section_description'),
             $this->option_group
         );
     }

     /**
      * Sanitize role capabilities
      */
      public function sanitize_role_caps($input) {
      error_log('Input data: ' . print_r($input, true));
          if (!current_user_can('manage_options')) {
              return false;
          }

          $sanitized_input = array();
          $capabilities = \Customer\Includes\Customer_Capabilities::get_all_caps();
          $roles = get_editable_roles();

          foreach ($roles as $role_name => $role_info) {
              if ($role_name === 'administrator') {
                  continue;
              }

              $role = get_role($role_name);
              if (!$role) continue;

              foreach ($capabilities as $cap => $label) {
                  // Cek apakah capability ini ada di input
                  if (isset($input[$role_name][$cap])) {
                      $role->add_cap($cap);
                      $sanitized_input[$role_name][$cap] = true;
                  } else {
                      $role->remove_cap($cap);
                  }
              }
          }

          return $sanitized_input;
      }

     /**
      * Render section description
      */
     public function render_section_description() {
         echo '<p>Configure permissions for different user roles in the CRM system.</p>';
     }

     /**
      * Render the settings page
      */
     public function render_settings_page() {
         if (!current_user_can('manage_options')) {
             wp_die(__('You do not have sufficient permissions to access this page.'));
         }

         require_once CUSTOMER_PATH . 'views/admin/settings-permissions.php';
     }

      /**
       * Handle reset permissions ajax request
       */
       public function handle_reset_customer_permissions() {
           check_ajax_referer('customer_permissions_nonce', 'nonce');

           if (!current_user_can('manage_options')) {
               wp_send_json_error(array(
                   'message' => 'You do not have permission to perform this action.'
               ));
               return;
           }

           try {
               // Reset ke default capabilities
               \Customer\Includes\Customer_Capabilities::initialize_default_caps();

               // Reset option di database
               delete_option($this->option_name);

               wp_send_json_success(array(
                   'message' => 'Permissions have been reset to default values successfully.'
               ));
           } catch (\Exception $e) {
               wp_send_json_error(array(
                   'message' => 'Failed to reset permissions: ' . $e->getMessage()
               ));
           }
       }

 }
