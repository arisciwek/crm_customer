<?php
/**
 * Customer Management System - DataTable Manager
 *
 * Handles DataTables configuration and settings management.
 * Provides centralized control for all DataTables instances in the system.
 *
 * Functions:
 * - Manages DataTables settings
 * - Provides default configurations
 * - Handles responsive settings
 * - Controls pagination and sorting
 *
 * @package     CustomerManagement
 * @subpackage  Includes
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        includes/class-datatable-manager.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Includes;

defined('ABSPATH') || exit;

class DataTable_Manager {
    private $settings;

    public function __construct() {
        $this->settings = get_option('datatable_settings', array(
            'per_page' => 25,
            'default_sort' => 'id',
            'sort_direction' => 'desc',
            'responsive' => true,
            'columns' => array(
                'customer' => array(
                    'id' => true,
                    'customer_code' => true,
                    'name' => true,
                    'email' => true,
                    'phone' => true,
                    'actions' => true
                ),
                'staff' => array(
                    'id' => true,
                    'name' => true,
                    'position' => true,
                    'department' => true,
                    'actions' => true
                )
            )
        ));
    }

    /**
     * Get konfigurasi DataTable untuk tabel tertentu
     *
     * @param string $table_id ID tabel (customer/staff)
     * @return array Konfigurasi DataTable
     */
    public function get_config($table_id) {
        return array(
            'pageLength' => $this->settings['per_page'],
            'order' => array(array(
                $this->get_sort_column_index($table_id),
                $this->settings['sort_direction']
            )),
            'responsive' => $this->settings['responsive'],
            'columns' => $this->get_columns_config($table_id)
        );
    }

    /**
     * Get index kolom untuk sorting default
     *
     * @param string $table_id ID tabel
     * @return int Index kolom
     */
    private function get_sort_column_index($table_id) {
        $columns = $this->settings['columns'][$table_id];
        $sort_column = $this->settings['default_sort'];

        $index = 0;
        foreach ($columns as $column => $visible) {
            if ($visible && $column === $sort_column) {
                return $index;
            }
            if ($visible) {
                $index++;
            }
        }

        return 0; // default ke kolom pertama
    }

    /**
     * Get konfigurasi kolom untuk DataTable
     *
     * @param string $table_id ID tabel
     * @return array Konfigurasi kolom
     */
    private function get_columns_config($table_id) {
        $config = array();
        $columns = $this->settings['columns'][$table_id];

        foreach ($columns as $column => $visible) {
            if ($visible) {
                $config[] = array(
                    'data' => $column,
                    'visible' => true
                );
            }
        }

        return $config;
    }

    /**
     * Update pengaturan DataTable
     *
     * @param array $new_settings Setting baru
     * @return bool True jika berhasil
     */
    public function update_settings($new_settings) {
        $this->settings = wp_parse_args($new_settings, $this->settings);
        return update_option('datatable_settings', $this->settings);
    }

    /**
     * Get current settings
     *
     * @return array Current settings
     */
    public function get_settings() {
        return $this->settings;
    }

    /**
     * Get available page length options
     *
     * @return array Options for page length
     */
    public function get_page_length_options() {
        return array(10, 25, 50, 100);
    }

    /**
     * Get available sort directions
     *
     * @return array Sort direction options
     */
    public function get_sort_directions() {
        return array(
            'asc' => 'Ascending',
            'desc' => 'Descending'
        );
    }
}
