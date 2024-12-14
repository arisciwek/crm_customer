<?php
/**
 * Customer Management System - Plugin Activator
 *
 * Handles plugin activation tasks including database installation
 * and capability setup. This class is triggered when the plugin
 * is activated in WordPress.
 *
 * @package     CustomerManagement
 * @subpackage  Includes
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        includes/class-customer-activator.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */
namespace Customer\Includes;

class Customer_Activator {
    public static function activate() {
        // Load and run database installation
        require_once CUSTOMER_PATH . 'sqls/install.php';
        \Customer\Sqls\Install::run();

        // Add capabilities
        Customer_Capabilities::add_caps();
    }
}
