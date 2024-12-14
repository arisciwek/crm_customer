<?php
/**
 * Customer Management System - Customers Table Schema
 *
 * Defines the database schema for customers table.
 * Main table containing customer information and their membership levels.
 *
 * @package     CustomerManagement
 * @subpackage  Database/Tables
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        sqls/tables/customers.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Sqls\Tables;

// Prevent direct access
defined('ABSPATH') || exit;

class Customers {
    /**
     * Get the table creation SQL using WordPress dbDelta format
     *
     * @return string SQL for table creation
     */
    public static function get_schema() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customers';
        $charset_collate = $wpdb->get_charset_collate();

        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL auto_increment,
            customer_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            email varchar(50) NOT NULL,
            phone varchar(20) NOT NULL,
            address text,
            province_id bigint(20) unsigned,
            regency_id bigint(20) unsigned,
            membership_level_id bigint(20) unsigned NOT NULL,
            primary_staff_id bigint(20) unsigned,
            notes text,
            created_by bigint(20) unsigned,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status enum('active','inactive') DEFAULT 'active',
            PRIMARY KEY  (id),
            KEY customer_code (customer_code),
            KEY created_by (created_by),
            KEY province_id (province_id),
            KEY regency_id (regency_id),
            KEY membership_level_id (membership_level_id),
            KEY primary_staff_id (primary_staff_id)
        ) $charset_collate;";
    }
}
