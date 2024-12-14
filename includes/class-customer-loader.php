<?php
/**
 * Customer Management System - Core Plugin Loader
 *
 * Handles loading all required dependencies, controllers and models.
 * This class is responsible for bootstrapping the plugin functionality
 * and initializing all core components.
 *
 * @package     CustomerManagement
 * @subpackage  Includes
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        includes/class-customer-loader.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */
namespace Customer\Includes;

class Customer_Loader {
    private $controllers = array();

    public function __construct() {
        $this->load_dependencies();
        $this->init_controllers();
    }

    private function load_dependencies() {
        // Load controllers
        require_once CUSTOMER_PATH . 'controllers/class-customer-controller.php';
        require_once CUSTOMER_PATH . 'controllers/class-staff-controller.php';

        // Load models
        require_once CUSTOMER_PATH . 'models/class-customer-model.php';
        require_once CUSTOMER_PATH . 'models/class-staff-model.php';
    }

    private function init_controllers() {
        $this->controllers['admin'] = new \Customer\Controllers\Admin_Controller();
        $this->controllers['customer'] = new \Customer\Controllers\Customer_Controller();
    }

    public function run() {
        // Add any initialization that should happen when the plugin starts running
        do_action('customer_plugin_loaded');
    }
}
