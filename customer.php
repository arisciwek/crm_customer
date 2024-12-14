<?php
/**
 * Plugin Name: Customer Management
 * Description: Customer management system with staff functionality
 * Version: 1.0.0
 * Author: arisciwek
 */

// Prevent direct access
defined('ABSPATH') || exit;

// Plugin Constants
define('CUSTOMER_PATH', plugin_dir_path(__FILE__));
define('CUSTOMER_URL', plugin_dir_url(__FILE__));
define('CUSTOMER_VERSION', '1.0.0');

// Load the activator class first
require_once CUSTOMER_PATH . 'includes/class-customer-activator.php';

// Improved Autoloader with better path handling
spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $prefix = 'Customer\\';
    $base_dir = CUSTOMER_PATH;

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Convert namespace separators to directory separators
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $relative_class);

    // Build the full path with class- prefix
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    $class_name = array_pop($parts); // Get last part (the class name)
    $file_name = 'class-' . strtolower(str_replace('_', '-', $class_name)) . '.php';

    if (!empty($parts)) {
        $file = $base_dir . strtolower(implode(DIRECTORY_SEPARATOR, $parts)) . DIRECTORY_SEPARATOR . $file_name;
    } else {
        $file = $base_dir . $file_name;
    }

    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
        return true;
    }

    // Log if file not found for debugging
    error_log("Attempted to load: $file for class: $class");
    return false;
});

// Initialize Plugin
function init_customer_plugin() {
    if (class_exists('Customer\\Includes\\Customer_Loader')) {
        $plugin = new Customer\Includes\Customer_Loader();
        $plugin->run();
    } else {
        error_log('Customer_Loader class not found. Check autoloader and file structure.');
    }
}
add_action('plugins_loaded', 'init_customer_plugin');

// Activation Hook
register_activation_hook(__FILE__, array('Customer\\Includes\\Customer_Activator', 'activate'));
