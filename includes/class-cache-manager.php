<?php
/**
 * Customer Management System - Cache Manager
 *
 * Handles caching functionality for the CRM system using WordPress Transients API.
 * Provides methods for storing and retrieving cached data with configurable expiration.
 *
 * Functions:
 * - Manages cache settings
 * - Handles data caching and retrieval
 * - Provides cache clearing functionality
 * - Supports group-based cache management
 *
 * @package     CustomerManagement
 * @subpackage  Includes
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        includes/class-cache-manager.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Includes;

defined('ABSPATH') || exit;

class Cache_Manager {
    private $settings;
    private $prefix = 'crm_cache_';

    public function __construct() {
        $this->settings = get_option('cache_settings', array(
            'enabled' => true,
            'expiration' => 3600, // 1 jam default
            'groups' => array('customer', 'staff')
        ));
    }

    /**
     * Set data ke cache
     *
     * @param string $key Key untuk cache
     * @param mixed $data Data yang akan disimpan
     * @param string $group Group cache (optional)
     * @return bool True jika berhasil
     */
    public function set($key, $data, $group = '') {
        if (!$this->settings['enabled']) {
            return false;
        }

        $cache_key = $this->build_key($key, $group);
        return set_transient(
            $cache_key,
            $data,
            $this->settings['expiration']
        );
    }

    /**
     * Get data dari cache
     *
     * @param string $key Key untuk cache
     * @param string $group Group cache (optional)
     * @return mixed Data dari cache atau false jika tidak ada
     */
    public function get($key, $group = '') {
        if (!$this->settings['enabled']) {
            return false;
        }

        $cache_key = $this->build_key($key, $group);
        return get_transient($cache_key);
    }

    /**
     * Delete specific cache
     *
     * @param string $key Key untuk cache
     * @param string $group Group cache (optional)
     * @return bool True jika berhasil
     */
    public function delete($key, $group = '') {
        $cache_key = $this->build_key($key, $group);
        return delete_transient($cache_key);
    }

    /**
     * Clear semua cache dalam group tertentu
     *
     * @param string $group Group yang akan di-clear
     * @return bool True jika berhasil
     */
    public function clear_group($group) {
        global $wpdb;

        $group_prefix = $this->build_key('', $group);

        // Get semua transient dengan prefix group ini
        $sql = $wpdb->prepare(
            "DELETE FROM $wpdb->options
            WHERE option_name LIKE %s
            AND option_name LIKE %s",
            $wpdb->esc_like('_transient_' . $group_prefix) . '%',
            $wpdb->esc_like('_transient_timeout_' . $group_prefix) . '%'
        );

        return $wpdb->query($sql);
    }

    /**
     * Clear semua cache CRM
     *
     * @return bool True jika berhasil
     */
    public function clear_all() {
        global $wpdb;

        // Delete semua transient dengan prefix CRM
        $sql = $wpdb->prepare(
            "DELETE FROM $wpdb->options
            WHERE option_name LIKE %s
            OR option_name LIKE %s",
            $wpdb->esc_like('_transient_' . $this->prefix) . '%',
            $wpdb->esc_like('_transient_timeout_' . $this->prefix) . '%'
        );

        return $wpdb->query($sql);
    }

    /**
     * Build cache key dengan prefix dan group
     *
     * @param string $key Original key
     * @param string $group Cache group
     * @return string Final cache key
     */
    private function build_key($key, $group = '') {
        if (!empty($group)) {
            return $this->prefix . $group . '_' . $key;
        }
        return $this->prefix . $key;
    }

    /**
     * Update cache settings
     *
     * @param array $new_settings Setting baru
     * @return bool True jika berhasil
     */
    public function update_settings($new_settings) {
        $this->settings = wp_parse_args($new_settings, $this->settings);
        return update_option('cache_settings', $this->settings);
    }

    /**
     * Get current cache settings
     *
     * @return array Current settings
     */
    public function get_settings() {
        return $this->settings;
    }
}
