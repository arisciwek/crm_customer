<?php
/**
 * Customer Management System - Settings Controller
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
 * Path:        controllers/class-settings-controller.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */
namespace Customer\Controllers;

defined('ABSPATH') || exit;

class Settings_Controller {
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        register_setting(
            'crm_permissions_options',
            'crm_role_caps',
            array($this, 'sanitize_role_caps')
        );
    }

    public function sanitize_role_caps($input) {
        if (!current_user_can('manage_options')) {
            return false;
        }

        $capabilities = \Customer\Includes\Customer_Capabilities::get_all_caps();
        $roles = get_editable_roles();

        foreach ($roles as $role_name => $role_info) {
            if ($role_name === 'administrator') {
                continue; // Skip administrator role
            }

            $role = get_role($role_name);

            foreach ($capabilities as $cap => $label) {
                if (isset($input[$role_name][$cap])) {
                    $role->add_cap($cap);
                } else {
                    $role->remove_cap($cap);
                }
            }
        }

        return $input;
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        require_once CUSTOMER_PATH . 'views/admin/settings-permissions.php';
    }
}
