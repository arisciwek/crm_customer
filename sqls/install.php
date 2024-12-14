<?php
/**
 * Customer Management System - Database Installation Handler
 *
 * Handles the installation of all database tables in the correct order,
 * respecting foreign key dependencies.
 *
 * @package     CustomerManagement
 * @subpackage  Database
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        sqls/install.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Sqls;

// Prevent direct access
defined('ABSPATH') || exit;

class Install {
    /**
     * Run the installation process
     *
     * @return void
     */
    public static function run() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Load all table classes
        require_once CUSTOMER_PATH . 'sqls/tables/customer-membership-levels.php';
        require_once CUSTOMER_PATH . 'sqls/tables/customer-staff-roles.php';
        require_once CUSTOMER_PATH . 'sqls/tables/customers.php';
        require_once CUSTOMER_PATH . 'sqls/tables/customer-staff.php';

        // Create tables in correct order
        dbDelta(Tables\Customer_Membership_Levels::get_schema());
        dbDelta(Tables\Customer_Staff_Roles::get_schema());
        dbDelta(Tables\Customers::get_schema());
        dbDelta(Tables\Customer_Staff::get_schema());

        // Add foreign key constraints
        self::add_foreign_keys();

        // Insert default data
        Tables\Customer_Membership_Levels::insert_defaults();
        Tables\Customer_Staff_Roles::insert_defaults();
    }

    /**
     * Add foreign key constraints after tables are created
     *
     * @return void
     */
    private static function add_foreign_keys() {
        global $wpdb;

        $constraints = array(
            // Customers foreign keys
            "ALTER TABLE {$wpdb->prefix}crm_customers
            ADD CONSTRAINT fk_customer_membership
            FOREIGN KEY (membership_level_id)
            REFERENCES {$wpdb->prefix}crm_customer_membership_levels(id)
            ON DELETE RESTRICT",

            // Customer staff foreign keys
            "ALTER TABLE {$wpdb->prefix}crm_customer_staff
            ADD CONSTRAINT fk_staff_customer
            FOREIGN KEY (customer_id)
            REFERENCES {$wpdb->prefix}crm_customers(id)
            ON DELETE CASCADE",

            "ALTER TABLE {$wpdb->prefix}crm_customer_staff
            ADD CONSTRAINT fk_staff_role
            FOREIGN KEY (role_id)
            REFERENCES {$wpdb->prefix}crm_customer_staff_roles(id)
            ON DELETE RESTRICT",

            // Customer staff roles foreign keys
            "ALTER TABLE {$wpdb->prefix}crm_customer_staff_roles
            ADD CONSTRAINT fk_role_membership
            FOREIGN KEY (membership_level_id)
            REFERENCES {$wpdb->prefix}crm_customer_membership_levels(id)
            ON DELETE SET NULL"
        );

        foreach ($constraints as $sql) {
            $wpdb->query($sql);
        }
    }
}
