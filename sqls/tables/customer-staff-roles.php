<?php
/**
 * Customer Management System - Customer Staff Roles Table Schema
 *
 * Defines the database schema for customer staff roles table.
 * These roles determine permissions and capabilities specifically
 * for staff members belonging to customers.
 *
 * @package     CustomerManagement
 * @subpackage  Database/Tables
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        sqls/tables/customer-staff-roles.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Sqls\Tables;

// Prevent direct access
defined('ABSPATH') || exit;

class Customer_Staff_Roles {
    /**
     * Get the table creation SQL using WordPress dbDelta format
     *
     * @return string SQL for table creation
     */
    public static function get_schema() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customer_staff_roles';
        $charset_collate = $wpdb->get_charset_collate();

        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL auto_increment,
            name varchar(50) NOT NULL,
            slug varchar(50) NOT NULL,
            description text,
            capabilities text,
            membership_level_id bigint(20) unsigned,
            created_by bigint(20) unsigned,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status enum('active','inactive') DEFAULT 'active',
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug),
            KEY membership_level_id (membership_level_id),
            KEY created_by (created_by)
        ) $charset_collate;";
    }

    /**
     * Insert default customer staff roles
     *
     * @return void
     */
    public static function insert_defaults() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customer_staff_roles';

        $defaults = array(
            array(
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator dengan akses penuh untuk mengelola staff customer',
                'capabilities' => json_encode(array(
                    'manage_customer_staff' => true,
                    'manage_departments' => true,
                    'view_reports' => true
                ))
            ),
            array(
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff dengan akses terbatas untuk operasional sehari-hari',
                'capabilities' => json_encode(array(
                    'view_reports' => true
                ))
            )
        );

        foreach ($defaults as $role) {
            $wpdb->insert($table_name, $role);
        }
    }
}
