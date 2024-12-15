<?php
/**
 * Customer Management System - System Settings Controller
 *
 * Handles all system settings functionality including customer code format,
 * cache settings, and DataTable configurations.
 *
 * Functions:
 * - Manages customer code generation settings
 * - Controls cache configuration
 * - Handles DataTable display settings
 * - Provides settings page interface
 *
 * @package     CustomerManagement
 * @subpackage  Controllers
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        controllers/class-system-settings-controller.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

 namespace Customer\Controllers;

 class System_Settings_Controller {
     public function __construct() {
         add_action('admin_init', array($this, 'register_settings'));
     }

     public function register_settings() {
         // Customer Code Section
         add_settings_section(
             'customer_code_section',
             'Customer Code Settings',
             array($this, 'render_customer_code_section'),
             'crm_system_settings'
         );

         // Customer Code Fields
         add_settings_field(
             'customer_code_prefix',
             'Customer Code Prefix',
             array($this, 'render_text_field'),
             'crm_system_settings',
             'customer_code_section',
             array(
                 'label_for' => 'customer_code_prefix',
                 'option_name' => 'customer_settings',
                 'field_name' => 'prefix',
                 'default' => 'CUST',
                 'description' => 'Prefix yang akan digunakan di awal kode customer'
             )
         );

         add_settings_field(
             'format_type',
             'Format Type',
             array($this, 'render_format_field'),
             'crm_system_settings',
             'customer_code_section',
             array(
                 'label_for' => 'format_type',
                 'option_name' => 'customer_settings',
                 'field_name' => 'format_type',
                 'description' => 'Pilih format untuk kode customer'
             )
         );

         add_settings_field(
             'digit_length',
             'Number Length',
             array($this, 'render_number_field'),
             'crm_system_settings',
             'customer_code_section',
             array(
                 'label_for' => 'digit_length',
                 'option_name' => 'customer_settings',
                 'field_name' => 'digit_length',
                 'min' => 3,
                 'max' => 10,
                 'default' => 5,
                 'description' => 'Panjang angka dalam kode (3-10 digit)'
             )
         );

         add_settings_field(
             'reset_period',
             'Reset Period',
             array($this, 'render_reset_period_field'),
             'crm_system_settings',
             'customer_code_section',
             array(
                 'label_for' => 'reset_period',
                 'option_name' => 'customer_settings',
                 'field_name' => 'reset_period',
                 'description' => 'Kapan nomor urut direset'
             )
         );

         // Cache Section
         add_settings_section(
             'cache_section',
             'Cache Settings',
             array($this, 'render_cache_section'),
             'crm_system_settings'
         );

         add_settings_field(
             'cache_enabled',
             'Enable Cache',
             array($this, 'render_cache_enabled_field'),
             'crm_system_settings',
             'cache_section',
             array(
                 'label_for' => 'cache_enabled',
                 'option_name' => 'cache_settings',
                 'field_name' => 'enabled',
                 'description' => 'Enable/disable system caching'
             )
         );

         add_settings_field(
             'cache_expiration',
             'Cache Duration',
             array($this, 'render_cache_expiration_field'),
             'crm_system_settings',
             'cache_section',
             array(
                 'label_for' => 'cache_expiration',
                 'option_name' => 'cache_settings',
                 'field_name' => 'expiration',
                 'description' => 'How long to keep items in cache'
             )
         );

         // DataTable Section
         add_settings_section(
             'datatable_section',
             'DataTable Settings',
             array($this, 'render_datatable_section'),
             'crm_system_settings'
         );

         add_settings_field(
             'rows_per_page',
             'Rows Per Page',
             array($this, 'render_rows_per_page_field'),
             'crm_system_settings',
             'datatable_section',
             array(
                 'label_for' => 'rows_per_page',
                 'option_name' => 'datatable_settings',
                 'field_name' => 'per_page',
                 'description' => 'Number of rows to display per page'
             )
         );

         add_settings_field(
             'sort_direction',
             'Default Sort',
             array($this, 'render_sort_direction_field'),
             'crm_system_settings',
             'datatable_section',
             array(
                 'label_for' => 'sort_direction',
                 'option_name' => 'datatable_settings',
                 'field_name' => 'sort_direction',
                 'description' => 'Default sorting direction'
             )
         );

         // Register settings
         register_setting('crm_system_settings', 'customer_settings');
         register_setting('crm_system_settings', 'cache_settings');
         register_setting('crm_system_settings', 'datatable_settings');
     }

     public function render_customer_code_section() {
         echo '<p>Configure how customer codes are generated in the system.</p>';
     }

     public function render_cache_section() {
         echo '<p>Configure system caching settings.</p>';
     }

     public function render_datatable_section() {
         echo '<p>Configure how data tables are displayed throughout the system.</p>';
     }

     // Render Fields
     public function render_text_field($args) {
         $options = get_option($args['option_name']);
         $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : $args['default'];
         ?>
         <input type="text"
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                value="<?php echo esc_attr($value); ?>"
                class="regular-text">
         <p class="description"><?php echo esc_html($args['description']); ?></p>
         <?php
     }

     public function render_format_field($args) {
         $options = get_option($args['option_name']);
         $current = isset($options[$args['field_name']]) ? $options[$args['field_name']] : 1;

         $formats = array(
             1 => 'PREFIX/YYYY/MM/XXXXX (e.g., CUST/2024/12/00001)',
             2 => 'PREFIX/YYMM/XXXXX (e.g., CUST/2412/00001)',
             3 => 'PREFIX-XXXXX (e.g., CUST-00001)',
             4 => 'PREFIX-YYYY.MM.XXXXX (e.g., CUST-2024.12.00001)'
         );

         foreach ($formats as $value => $label) {
             ?>
             <div class="radio-group">
                 <label>
                     <input type="radio"
                            name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                            value="<?php echo esc_attr($value); ?>"
                            <?php checked($current, $value); ?>>
                     <?php echo esc_html($label); ?>
                 </label>
             </div>
             <?php
         }
         echo '<p class="description">' . esc_html($args['description']) . '</p>';
     }

     public function render_number_field($args) {
         $options = get_option($args['option_name']);
         $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : $args['default'];
         ?>
         <input type="number"
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                value="<?php echo esc_attr($value); ?>"
                min="<?php echo esc_attr($args['min']); ?>"
                max="<?php echo esc_attr($args['max']); ?>"
                class="small-text">
         <p class="description"><?php echo esc_html($args['description']); ?></p>
         <?php
     }

     public function render_reset_period_field($args) {
         $options = get_option($args['option_name']);
         $current = isset($options[$args['field_name']]) ? $options[$args['field_name']] : 'monthly';
         ?>
         <div class="radio-group">
             <label>
                 <input type="radio"
                        name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                        value="monthly"
                        <?php checked($current, 'monthly'); ?>>
                 Monthly
             </label>
         </div>
         <div class="radio-group">
             <label>
                 <input type="radio"
                        name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                        value="yearly"
                        <?php checked($current, 'yearly'); ?>>
                 Yearly
             </label>
         </div>
         <p class="description"><?php echo esc_html($args['description']); ?></p>
         <?php
     }

     public function render_cache_enabled_field($args) {
         $options = get_option($args['option_name']);
         $enabled = isset($options[$args['field_name']]) ? $options[$args['field_name']] : true;
         ?>
         <label>
             <input type="checkbox"
                    name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                    value="1"
                    <?php checked($enabled); ?>>
             Enable caching system
         </label>
         <p class="description"><?php echo esc_html($args['description']); ?></p>
         <?php
     }

     public function render_cache_expiration_field($args) {
         $options = get_option($args['option_name']);
         $current = isset($options[$args['field_name']]) ? $options[$args['field_name']] : 3600;
         $durations = array(
             1800 => '30 Minutes',
             3600 => '1 Hour',
             7200 => '2 Hours',
             21600 => '6 Hours',
             43200 => '12 Hours',
             86400 => '24 Hours'
         );

         foreach ($durations as $value => $label) {
             ?>
             <div class="radio-group">
                 <label>
                     <input type="radio"
                            name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                            value="<?php echo esc_attr($value); ?>"
                            <?php checked($current, $value); ?>>
                     <?php echo esc_html($label); ?>
                 </label>
             </div>
             <?php
         }
         echo '<p class="description">' . esc_html($args['description']) . '</p>';
     }

     public function render_rows_per_page_field($args) {
         $options = get_option($args['option_name']);
         $current = isset($options[$args['field_name']]) ? $options[$args['field_name']] : 25;
         $choices = array(10, 25, 50, 100);

         foreach ($choices as $value) {
             ?>
             <div class="radio-group">
                 <label>
                     <input type="radio"
                            name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                            value="<?php echo esc_attr($value); ?>"
                            <?php checked($current, $value); ?>>
                     <?php echo esc_html($value . ' rows'); ?>
                 </label>
             </div>
             <?php
         }
         echo '<p class="description">' . esc_html($args['description']) . '</p>';
     }

     public function render_sort_direction_field($args) {
         $options = get_option($args['option_name']);
         $current = isset($options[$args['field_name']]) ? $options[$args['field_name']] : 'desc';
         ?>
         <div class="radio-group">
             <label>
                 <input type="radio"
                        name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                        value="asc"
                        <?php checked($current, 'asc'); ?>>
                 Ascending
             </label>
         </div>
         <div class="radio-group">
             <label>
                 <input type="radio"
                        name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                        value="desc"
                        <?php checked($current, 'desc'); ?>>
                 Descending
             </label>
         </div>
         <p class="description"><?php echo esc_html($args['description']); ?></p>
         <?php
     }

     public function render_settings_page() {
         if (!current_user_can('manage_options')) {
             wp_die(__('You do not have sufficient permissions to access this page.'));
         }

         require_once CUSTOMER_PATH . 'views/admin/settings-system.php';
     }
 }
