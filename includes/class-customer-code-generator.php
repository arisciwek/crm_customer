<?php
/**
 * Customer Management System - Customer Code Generator
 *
 * Handles the generation of unique customer codes based on configured settings.
 * Supports multiple format types and automatic reset periods.
 *
 * Functions:
 * - Generates unique customer codes
 * - Manages number sequences
 * - Handles periodic resets
 * - Validates code uniqueness
 *
 * @package     CustomerManagement
 * @subpackage  Includes
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        includes/class-customer-code-generator.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

namespace Customer\Includes;

defined('ABSPATH') || exit;

class Customer_Code_Generator {
    private $settings;

    public function __construct() {
        $this->settings = get_option('customer_settings', array(
            'prefix' => 'CUST',
            'format_type' => 1,
            'digit_length' => 5,
            'reset_period' => 'monthly',
            'next_number' => 1,
            'last_reset' => current_time('mysql')
        ));

        // Check if reset needed
        $this->check_reset_period();
    }

    /**
     * Generate kode customer baru
     *
     * @return string Kode customer yang unik
     */
    public function generate_code() {
        $number = $this->settings['next_number'];
        $prefix = $this->settings['prefix'];
        $digit_length = $this->settings['digit_length'];

        // Format number dengan leading zeros
        $formatted_number = str_pad($number, $digit_length, '0', STR_PAD_LEFT);

        // Generate code sesuai format yang dipilih
        switch($this->settings['format_type']) {
            case 1: // PREFIX/YYYY/MM/XXXXX
                $code = sprintf(
                    '%s/%s/%s/%s',
                    $prefix,
                    date('Y'),
                    date('m'),
                    $formatted_number
                );
                break;

            case 2: // PREFIX/YYMM/XXXXX
                $code = sprintf(
                    '%s/%s%s/%s',
                    $prefix,
                    date('y'),
                    date('m'),
                    $formatted_number
                );
                break;

            case 3: // PREFIX-XXXXX
                $code = sprintf(
                    '%s-%s',
                    $prefix,
                    $formatted_number
                );
                break;

            case 4: // PREFIX-YYYY.MM.XXXXX
                $code = sprintf(
                    '%s-%s.%s.%s',
                    $prefix,
                    date('Y'),
                    date('m'),
                    $formatted_number
                );
                break;

            default:
                $code = sprintf(
                    '%s/%s/%s/%s',
                    $prefix,
                    date('Y'),
                    date('m'),
                    $formatted_number
                );
        }

        // Update next number
        $this->update_next_number();

        return $code;
    }

    /**
     * Check apakah perlu reset counter berdasarkan period
     */
    private function check_reset_period() {
        $last_reset = new \DateTime($this->settings['last_reset']);
        $now = new \DateTime();

        $need_reset = false;

        if ($this->settings['reset_period'] === 'monthly') {
            // Reset if month changed
            if ($last_reset->format('Ym') !== $now->format('Ym')) {
                $need_reset = true;
            }
        } else { // yearly
            // Reset if year changed
            if ($last_reset->format('Y') !== $now->format('Y')) {
                $need_reset = true;
            }
        }

        if ($need_reset) {
            $this->reset_counter();
        }
    }

    /**
     * Reset counter ke 1 dan update last reset time
     */
    private function reset_counter() {
        $this->settings['next_number'] = 1;
        $this->settings['last_reset'] = current_time('mysql');

        update_option('customer_settings', $this->settings);
    }

    /**
     * Update next number setelah generate code
     */
    private function update_next_number() {
        $this->settings['next_number']++;
        update_option('customer_settings', $this->settings);
    }

    /**
     * Check apakah kode sudah ada di database
     *
     * @param string $code Kode yang akan dicek
     * @return bool True jika kode sudah ada
     */
    public function is_code_exists($code) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customers';

        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE customer_code = %s",
                $code
            )
        );

        return (bool) $exists;
    }

    /**
     * Generate kode yang dijamin unik
     * Jika kode sudah ada, akan generate ulang dengan increment number
     *
     * @return string Kode customer yang unik
     */
    public function generate_unique_code() {
        $code = $this->generate_code();

        // Check uniqueness
        while ($this->is_code_exists($code)) {
            $code = $this->generate_code();
        }

        return $code;
    }
}
