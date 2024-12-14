<?php
/**
 * Customer Management System - Customer Membership Levels Table Schema
 *
 * Defines the database schema for customer membership levels table.
 * These membership levels represent different subscription tiers
 * available specifically for customers in the system.
 *
 * @package     CustomerManagement
 * @subpackage  Database/Tables
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        sqls/tables/customer-membership-levels.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Sqls\Tables;

// Prevent direct access
defined('ABSPATH') || exit;

class Customer_Membership_Levels {
    /**
     * Get the table creation SQL using WordPress dbDelta format
     *
     * @return string SQL for table creation
     */
    public static function get_schema() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customer_membership_levels';
        $charset_collate = $wpdb->get_charset_collate();

        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL auto_increment,
            name varchar(50) NOT NULL,
            slug varchar(50) NOT NULL,
            description text,
            max_staff int NOT NULL DEFAULT 2,
            capabilities text,
            created_by bigint(20) unsigned,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status enum('active','inactive') DEFAULT 'active',
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug),
            KEY created_by (created_by)
        ) $charset_collate;";
    }

    /**
     * Insert default customer membership levels
     *
     * @return void
     */
    public static function insert_defaults() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customer_membership_levels';

        $defaults = array(
            array(
                'name' => 'Reguler',
                'slug' => 'reguler',
                'description' => 'Paket dasar untuk manajemen staff',
                'max_staff' => 2,
                'capabilities' => json_encode(array(
                    'can_add_staff' => true,
                    'max_departments' => 2
                ))
            ),
            array(
                'name' => 'Prioritas',
                'slug' => 'prioritas',
                'description' => 'Paket menengah dengan fitur lebih lengkap',
                'max_staff' => 5,
                'capabilities' => json_encode(array(
                    'can_add_staff' => true,
                    'can_export_data' => true,
                    'max_departments' => 5
                ))
            ),
            array(
                'name' => 'Eksklusif',
                'slug' => 'eksklusif',
                'description' => 'Paket lengkap tanpa batasan',
                'max_staff' => -1, // unlimited
                'capabilities' => json_encode(array(
                    'can_add_staff' => true,
                    'can_export_data' => true,
                    'can_create_custom_roles' => true,
                    'max_departments' => -1
                ))
            )
        );

        foreach ($defaults as $level) {
            $wpdb->insert($table_name, $level);
        }
    }
}
