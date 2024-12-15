<?php
/**
 * Customer Management System - Capabilities Management
 *
 * This class handles all capability and role management for the CRM system.
 * It defines custom capabilities, manages their assignment to roles, and provides
 * functionality for adding/removing these capabilities during plugin lifecycle.
 *
 * @package     CustomerManagement
 * @subpackage  Includes
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        includes/class-customer-capabilities.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 *
 * Capabilities defined:
 * - Standard CRUD: view, add, edit, delete customers
 * - Own data: view, edit, delete own customers
 * - Staff: manage and view staff
 * - Administrative: manage settings, view reports, export data
 *
 * Default Roles Created:
 * - CRM Manager: Full access except settings
 * - CRM Staff: Limited to own customer management
 */

namespace Customer\Includes;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Customer_Capabilities {
    // Daftar semua capabilities yang akan digunakan
    private static $capabilities = array(
        // Standard CRUD
        'crm_view_customers' => 'View Customers List',
        'crm_add_customer' => 'Add New Customer',
        'crm_edit_customer' => 'Edit Customer',
        'crm_delete_customer' => 'Delete Customer',

        // Own data permissions
        'crm_view_own_customers' => 'View Own Customers',
        'crm_edit_own_customer' => 'Edit Own Customer',
        'crm_delete_own_customer' => 'Delete Own Customer',

        // Staff permissions
        'crm_manage_staff' => 'Manage Staff',
        'crm_view_staff' => 'View Staff List',

        // Settings & Reports
        'crm_manage_settings' => 'Manage CRM Settings',
        'crm_view_reports' => 'View Reports',
        'crm_export_data' => 'Export Customer Data'
    );

    /**
     * Add capabilities to roles during plugin activation
     *
     * Assigns all defined capabilities to the administrator role and
     * creates two new roles with preset capabilities:
     * - CRM Manager
     * - CRM Staff
     *
     * @since 1.0.0
     * @return void
     */
    public static function add_caps() {
        // Add all capabilities to administrator
        $admin = get_role('administrator');
        foreach (self::$capabilities as $cap => $label) {
            $admin->add_cap($cap);
        }

        // Create CRM Manager role
        add_role('crm_manager', 'CRM Manager', array(
            'read' => true,
            'crm_view_customers' => true,
            'crm_add_customer' => true,
            'crm_edit_customer' => true,
            'crm_view_staff' => true,
            'crm_view_reports' => true
        ));

        // Create CRM Staff role
        add_role('crm_staff', 'CRM Staff', array(
            'read' => true,
            'crm_view_own_customers' => true,
            'crm_edit_own_customer' => true,
            'crm_view_staff' => true
        ));
    }

    /**
     * Remove capabilities during plugin deactivation
     *
     * Removes all custom capabilities from the administrator role
     * and removes the custom roles created by this plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public static function remove_caps() {
        // Remove caps from administrator
        $admin = get_role('administrator');
        foreach (self::$capabilities as $cap => $label) {
            $admin->remove_cap($cap);
        }

        // Remove custom roles
        remove_role('crm_manager');
        remove_role('crm_staff');
    }
    
    public static function initialize_default_caps() {
        // Reset administrator capabilities
        $admin = get_role('administrator');
        foreach (self::$capabilities as $cap => $label) {
            $admin->add_cap($cap);
        }

        // Reset CRM Manager capabilities
        $manager = get_role('crm_manager');
        if (!$manager) {
            $manager = add_role('crm_manager', 'CRM Manager', array('read' => true));
        }
        // Reset ke default capabilities untuk CRM Manager
        foreach (self::$capabilities as $cap => $label) {
            $manager->remove_cap($cap); // Hapus semua caps dulu
        }
        // Tambahkan default caps
        $manager_caps = array(
            'crm_view_customers',
            'crm_add_customer',
            'crm_edit_customer',
            'crm_view_staff',
            'crm_view_reports'
        );
        foreach ($manager_caps as $cap) {
            $manager->add_cap($cap);
        }

        // Reset CRM Staff capabilities
        $staff = get_role('crm_staff');
        if (!$staff) {
            $staff = add_role('crm_staff', 'CRM Staff', array('read' => true));
        }
        // Reset ke default capabilities untuk CRM Staff
        foreach (self::$capabilities as $cap => $label) {
            $staff->remove_cap($cap); // Hapus semua caps dulu
        }
        // Tambahkan default caps
        $staff_caps = array(
            'crm_view_own_customers',
            'crm_edit_own_customer',
            'crm_view_staff'
        );
        foreach ($staff_caps as $cap) {
            $staff->add_cap($cap);
        }
    }

    /**
     * Get all defined capabilities with their labels
     *
     * @since 1.0.0
     * @return array Associative array of capability names and labels
     */
    public static function get_all_caps() {
        return self::$capabilities;
    }
}
