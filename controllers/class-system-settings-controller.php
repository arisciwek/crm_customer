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
         // Register settings di init untuk memastikan timing yang tepat
         add_action('init', array($this, 'register_settings_init'));
         add_action('admin_init', array($this, 'register_settings_fields'));
     }

     // Mendaftarkan settings ke whitelist WordPress
     public function register_settings_init() {
         register_setting(
             'crm_system_settings',        // Option group
             'customer_settings',          // Option name
             array(
                 'type' => 'array',
                 'sanitize_callback' => array($this, 'sanitize_settings')
             )
         );

         register_setting(
             'crm_system_settings',
             'cache_settings',
             array('type' => 'array')
         );

         register_setting(
             'crm_system_settings',
             'datatable_settings',
             array('type' => 'array')
         );
     }

     // Sanitize callback
     public function sanitize_settings($input) {
         if (isset($input['next_number'])) {
             $input['next_number'] = absint($input['next_number']);
             if ($input['next_number'] < 1) {
                 $input['next_number'] = 1;
             }
         }
         return $input;
     }

     // Register semua fields settings
     public function register_settings_fields() {
         // Customer Code Section
         add_settings_section(
             'customer_code_section',
             'Customer Code Settings',
             array($this, 'render_customer_code_section'),
             'crm_system_settings'
         );

         // Add settings fields for customer code
         $this->add_customer_code_fields();

         // Cache Section
         add_settings_section(
             'cache_section',
             'Cache Settings',
             array($this, 'render_cache_section'),
             'crm_system_settings'
         );

         // Add cache settings fields
         $this->add_cache_fields();

         // DataTable Section
         add_settings_section(
             'datatable_section',
             'DataTable Settings',
             array($this, 'render_datatable_section'),
             'crm_system_settings'
         );

         // Add datatable settings fields
         $this->add_datatable_fields();
     }

     // Helper untuk menambahkan customer code fields
     private function add_customer_code_fields() {
         $fields = array(
             'customer_code_prefix' => array(
                 'title' => 'Customer Code Prefix',
                 'callback' => 'render_text_field',
                 'args' => array(
                     'label_for' => 'customer_code_prefix',
                     'option_name' => 'customer_settings',
                     'field_name' => 'prefix',
                     'default' => 'CUST',
                     'description' => 'Prefix yang akan digunakan di awal kode customer'
                 )
             ),
             'format_type' => array(
                 'title' => 'Format Type',
                 'callback' => 'render_format_field',
                 'args' => array(
                     'label_for' => 'format_type',
                     'option_name' => 'customer_settings',
                     'field_name' => 'format_type',
                     'description' => 'Pilih format untuk kode customer'
                 )
             ),
             'digit_length' => array(
                 'title' => 'Number Length',
                 'callback' => 'render_number_field',
                 'args' => array(
                     'label_for' => 'digit_length',
                     'option_name' => 'customer_settings',
                     'field_name' => 'digit_length',
                     'min' => 3,
                     'max' => 10,
                     'default' => 5,
                     'description' => 'Panjang angka dalam kode (3-10 digit)'
                 )
             ),
             'reset_period' => array(
                 'title' => 'Reset Period',
                 'callback' => 'render_reset_period_field',
                 'args' => array(
                     'label_for' => 'reset_period',
                     'option_name' => 'customer_settings',
                     'field_name' => 'reset_period',
                     'description' => 'Kapan nomor urut direset'
                 )
             ),
             'next_number' => array(
                 'title' => 'Next Number',
                 'callback' => 'render_next_number_field',
                 'args' => array(
                     'label_for' => 'next_number',
                     'option_name' => 'customer_settings',
                     'field_name' => 'next_number',
                     'default' => 1,
                     'min' => 1,
                     'description' => 'Nomor urut berikutnya yang akan digunakan'
                 )
             )
         );

         foreach ($fields as $id => $field) {
             add_settings_field(
                 $id,
                 $field['title'],
                 array($this, $field['callback']),
                 'crm_system_settings',
                 'customer_code_section',
                 $field['args']
             );
         }
     }

     // Helper untuk menambahkan cache fields
     private function add_cache_fields() {
         $fields = array(
             'cache_enabled' => array(
                 'title' => 'Enable Cache',
                 'callback' => 'render_cache_enabled_field',
                 'args' => array(
                     'label_for' => 'cache_enabled',
                     'option_name' => 'cache_settings',
                     'field_name' => 'enabled',
                     'description' => 'Enable/disable system caching'
                 )
             ),
             'cache_expiration' => array(
                 'title' => 'Cache Duration',
                 'callback' => 'render_cache_expiration_field',
                 'args' => array(
                     'label_for' => 'cache_expiration',
                     'option_name' => 'cache_settings',
                     'field_name' => 'expiration',
                     'description' => 'How long to keep items in cache'
                 )
             )
         );

         foreach ($fields as $id => $field) {
             add_settings_field(
                 $id,
                 $field['title'],
                 array($this, $field['callback']),
                 'crm_system_settings',
                 'cache_section',
                 $field['args']
             );
         }
     }

     // Helper untuk menambahkan datatable fields
     private function add_datatable_fields() {
         $fields = array(
             'rows_per_page' => array(
                 'title' => 'Rows Per Page',
                 'callback' => 'render_rows_per_page_field',
                 'args' => array(
                     'label_for' => 'rows_per_page',
                     'option_name' => 'datatable_settings',
                     'field_name' => 'per_page',
                     'description' => 'Number of rows to display per page'
                 )
             ),
             'sort_direction' => array(
                 'title' => 'Default Sort',
                 'callback' => 'render_sort_direction_field',
                 'args' => array(
                     'label_for' => 'sort_direction',
                     'option_name' => 'datatable_settings',
                     'field_name' => 'sort_direction',
                     'description' => 'Default sorting direction'
                 )
             )
         );

         foreach ($fields as $id => $field) {
             add_settings_field(
                 $id,
                 $field['title'],
                 array($this, $field['callback']),
                 'crm_system_settings',
                 'datatable_section',
                 $field['args']
             );
         }
     }

     // Render section descriptions
     public function render_customer_code_section() {
         echo '<p>Configure how customer codes are generated in the system.</p>';
     }

     public function render_cache_section() {
         echo '<p>Configure system caching settings.</p>';
     }

     public function render_datatable_section() {
         echo '<p>Configure how data tables are displayed throughout the system.</p>';
     }

     // Render fields
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

     public function render_next_number_field($args) {
         $options = get_option($args['option_name']);
         $value = isset($options[$args['field_name']]) ? $options[$args['field_name']] : $args['default'];
         ?>
         <input type="number"
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo esc_attr($args['option_name'] . '[' . $args['field_name'] . ']'); ?>"
                value="<?php echo esc_attr($value); ?>"
                min="<?php echo esc_attr($args['min']); ?>"
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
