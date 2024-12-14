<?php
/**
 * Customer Management System - Customer Staff Table Schema
 *
 * Defines the database schema for customer staff table.
 * Contains staff members that belong to customers.
 *
 * @package     CustomerManagement
 * @subpackage  Database/Tables
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        sqls/tables/customer-staff.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Sqls\Tables;

// Prevent direct access
defined('ABSPATH') || exit;

class Customer_Staff {
    /**
     * Get the table creation SQL using WordPress dbDelta format
     *
     * @return string SQL for table creation
     */
    public static function get_schema() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customer_staff';
        $charset_collate = $wpdb->get_charset_collate();

        return "CREATE TABLE {$table_name} (
            id bigint(20) unsigned NOT NULL auto_increment,
            customer_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20) NOT NULL,
            position varchar(100),
            department varchar(100),
            role_id bigint(20) unsigned NOT NULL,
            created_by bigint(20) unsigned,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            status enum('active','inactive') DEFAULT 'active',
            PRIMARY KEY  (id),
            KEY customer_id (customer_id),
            KEY role_id (role_id),
            KEY created_by (created_by)
        ) $charset_collate;";
    }
}
