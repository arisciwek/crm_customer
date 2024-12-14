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
     /**
      * Constructor
      */
     public function __construct() {
         add_action('admin_init', array($this, 'register_settings'));
     }

     /**
      * Register semua settings yang diperlukan
      */
     public function __construct() {
         // Ensure default settings
         $this->ensure_default_settings();

         // Register settings
         add_action('admin_init', array($this, 'register_settings'));

         // Register AJAX handlers
         $this->register_ajax_handlers();

         // Register settings link di plugin page
         add_filter(
             'plugin_action_links_customer-management/customer.php',
             array($this, 'add_settings_link')
         );
     }

     /**
      * Tambahkan link settings di halaman plugins
      */
     public function add_settings_link($links) {
         $settings_link = sprintf(
             '<a href="%s">%s</a>',
             admin_url('admin.php?page=customer-settings'),
             __('Settings')
         );
         array_unshift($links, $settings_link);
         return $links;
     }

     public function register_settings() {
         // Register sections
         add_settings_section(
             'cache_section',
             'Cache Settings',
             array($this, 'render_cache_section'),
             'crm_system_settings'
         );

         add_settings_section(
             'customer_code_section',
             'Customer Code Settings',
             array($this, 'render_customer_code_section'),
             'crm_system_settings'
         );

         add_settings_section(
             'datatable_section',
             'DataTable Settings',
             array($this, 'render_datatable_section'),
             'crm_system_settings'
         );

         // Register customer code settings
         register_setting(
             'crm_system_settings',
             'customer_settings',
             array(
                 'type' => 'array',
                 'sanitize_callback' => array($this, 'sanitize_customer_settings')
             )
         );

         // Register cache settings
         register_setting(
             'crm_system_settings',
             'cache_settings',
             array(
                 'type' => 'array',
                 'sanitize_callback' => array($this, 'sanitize_cache_settings')
             )
         );

         // Register datatable settings
         register_setting(
             'crm_system_settings',
             'datatable_settings',
             array(
                 'type' => 'array',
                 'sanitize_callback' => array($this, 'sanitize_datatable_settings')
             )
         );

         // Tambah section untuk Customer Code
         add_settings_section(
             'customer_code_section',
             'Customer Code Settings',
             array($this, 'render_customer_code_section'),
             'crm_system_settings'
         );

         // Tambah field untuk Customer Code
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
                 'default' => 'CUST'
             )
         );

         add_settings_field(
             'customer_code_format',
             'Format Type',
             array($this, 'render_format_field'),
             'crm_system_settings',
             'customer_code_section'
         );

         add_settings_field(
             'customer_code_digit_length',
             'Number Digit Length',
             array($this, 'render_number_field'),
             'crm_system_settings',
             'customer_code_section',
             array(
                 'label_for' => 'customer_code_digit_length',
                 'option_name' => 'customer_settings',
                 'field_name' => 'digit_length',
                 'min' => 1,
                 'max' => 10,
                 'default' => 5
             )
         );

         add_settings_field(
             'customer_code_reset_period',
             'Reset Period',
             array($this, 'render_reset_period_field'),
             'crm_system_settings',
             'customer_code_section'
         );

         // DataTable settings fields
         add_settings_field(
             'datatable_rows_per_page',
             'Rows Per Page',
             array($this, 'render_rows_per_page_field'),
             'crm_system_settings',
             'datatable_section'
         );

         add_settings_field(
             'datatable_sort_direction',
             'Default Sort Direction',
             array($this, 'render_sort_direction_field'),
             'crm_system_settings',
             'datatable_section'
         );

         add_settings_field(
             'datatable_responsive',
             'Responsive Mode',
             array($this, 'render_responsive_field'),
             'crm_system_settings',
             'datatable_section'
         );
     }

     /**
      * Sanitize customer settings
      */
     public function sanitize_customer_settings($input) {
         $output = array();

         // Sanitize prefix (alphanumeric and dash only)
         $output['prefix'] = preg_replace('/[^a-zA-Z0-9-]/', '', $input['prefix']);

         // Sanitize format type (1-4 only)
         $output['format_type'] = intval($input['format_type']);
         if ($output['format_type'] < 1 || $output['format_type'] > 4) {
             $output['format_type'] = 1;
         }

         // Sanitize digit length (1-10 only)
         $output['digit_length'] = intval($input['digit_length']);
         if ($output['digit_length'] < 1 || $output['digit_length'] > 10) {
             $output['digit_length'] = 5;
         }

         // Sanitize reset period (monthly or yearly only)
         $output['reset_period'] = sanitize_text_field($input['reset_period']);
         if (!in_array($output['reset_period'], array('monthly', 'yearly'))) {
             $output['reset_period'] = 'monthly';
         }

         // Keep existing next_number and last_reset if exists
         $existing = get_option('customer_settings', array());
         $output['next_number'] = isset($existing['next_number']) ? intval($existing['next_number']) : 1;
         $output['last_reset'] = isset($existing['last_reset']) ? $existing['last_reset'] : current_time('mysql');

         return $output;
     }

     /**
      * Render section untuk customer code
      */
     /**
      * Render cache section
      */
     /**
      * Set default settings jika belum ada
      */
     protected function ensure_default_settings() {
         // Customer settings
         if (!get_option('customer_settings')) {
             update_option('customer_settings', array(
                 'prefix' => 'CUST',
                 'format_type' => 1,
                 'digit_length' => 5,
                 'reset_period' => 'monthly',
                 'next_number' => 1,
                 'last_reset' => current_time('mysql')
             ));
         }

         // Cache settings
         if (!get_option('cache_settings')) {
             update_option('cache_settings', array(
                 'enabled' => true,
                 'expiration' => 3600,
                 'groups' => array('customer', 'staff')
             ));
         }

         // Datatable settings
         if (!get_option('datatable_settings')) {
             update_option('datatable_settings', array(
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
     }

     public function render_cache_section($args) {
         ?>
         <p>Configure caching settings to improve system performance.</p>
         <?php
     }

     /**
      * Render cache enabled field
      */
     public function render_cache_enabled_field() {
         $options = get_option('cache_settings');
         $enabled = isset($options['enabled']) ? $options['enabled'] : true;
         ?>
         <div>
             <label>
                 <input type="checkbox"
                        name="cache_settings[enabled]"
                        value="1"
                        <?php checked($enabled, true); ?>>
                 Enable caching system
             </label>
             <p class="description">
                 Enable or disable the caching system for better performance.
             </p>
         </div>
         <?php
     }

     /**
      * Render cache expiration field
      */
     public function render_cache_expiration_field() {
         $options = get_option('cache_settings');
         $expiration = isset($options['expiration']) ? $options['expiration'] : 3600;
         $choices = array(
             1800 => '30 minutes',
             3600 => '1 hour',
             7200 => '2 hours',
             21600 => '6 hours',
             43200 => '12 hours',
             86400 => '24 hours'
         );

         foreach ($choices as $value => $label) {
             ?>
             <div>
                 <label>
                     <input type="radio"
                            name="cache_settings[expiration]"
                            value="<?php echo esc_attr($value); ?>"
                            <?php checked($expiration, $value); ?>>
                     <?php echo esc_html($label); ?>
                 </label>
             </div>
             <?php
         }
     }

     /**
      * Render cache clear button
      */
     public function render_cache_clear_field() {
         ?>
         <button type="button"
                 id="clear-cache-button"
                 class="button button-secondary">
             Clear All Cache
         </button>
         <span id="cache-clear-status" style="margin-left: 10px;"></span>
         <script>
         jQuery(document).ready(function($) {
             $('#clear-cache-button').on('click', function() {
                 var button = $(this);
                 var status = $('#cache-clear-status');

                 button.prop('disabled', true);
                 status.html('Clearing cache...');

                 $.ajax({
                     url: ajaxurl,
                     type: 'POST',
                     data: {
                         action: 'clear_crm_cache',
                         nonce: '<?php echo wp_create_nonce('clear_cache_nonce'); ?>'
                     },
                     success: function(response) {
                         if (response.success) {
                             status.html('Cache cleared successfully!');
                         } else {
                             status.html('Failed to clear cache.');
                         }
                     },
                     error: function() {
                         status.html('Error occurred while clearing cache.');
                     },
                     complete: function() {
                         button.prop('disabled', false);
                         setTimeout(function() {
                             status.html('');
                         }, 3000);
                     }
                 });
             });
         });
         </script>
         <?php
     }

     /**
      * Helper untuk render input text
      */
     protected function render_input_field($args) {
         $option_name = $args['option_name'];
         $field_name = $args['field_name'];
         $type = isset($args['type']) ? $args['type'] : 'text';
         $default = isset($args['default']) ? $args['default'] : '';
         $min = isset($args['min']) ? $args['min'] : false;
         $max = isset($args['max']) ? $args['max'] : false;

         $options = get_option($option_name, array());
         $value = isset($options[$field_name]) ? $options[$field_name] : $default;

         $attrs = array(
             'type' => $type,
             'id' => $args['label_for'],
             'name' => "{$option_name}[{$field_name}]",
             'value' => esc_attr($value),
             'class' => isset($args['class']) ? $args['class'] : 'regular-text'
         );

         if ($min !== false) {
             $attrs['min'] = $min;
         }
         if ($max !== false) {
             $attrs['max'] = $max;
         }

         $html = '<input';
         foreach ($attrs as $key => $value) {
             $html .= sprintf(' %s="%s"', $key, esc_attr($value));
         }
         $html .= '>';

         if (isset($args['description'])) {
             $html .= sprintf(
                 '<p class="description">%s</p>',
                 esc_html($args['description'])
             );
         }

         echo $html;
     }

     /**
      * Helper untuk render select field
      */
     protected function render_select_field($args) {
         $option_name = $args['option_name'];
         $field_name = $args['field_name'];
         $options = $args['options'];
         $default = isset($args['default']) ? $args['default'] : '';

         $settings = get_option($option_name, array());
         $value = isset($settings[$field_name]) ? $settings[$field_name] : $default;

         $html = sprintf(
             '<select id="%s" name="%s[%s]" class="%s">',
             esc_attr($args['label_for']),
             esc_attr($option_name),
             esc_attr($field_name),
             isset($args['class']) ? esc_attr($args['class']) : ''
         );

         foreach ($options as $key => $label) {
             $html .= sprintf(
                 '<option value="%s"%s>%s</option>',
                 esc_attr($key),
                 selected($value, $key, false),
                 esc_html($label)
             );
         }

         $html .= '</select>';

         if (isset($args['description'])) {
             $html .= sprintf(
                 '<p class="description">%s</p>',
                 esc_html($args['description'])
             );
         }

         echo $html;
     }

     /**
      * Validasi input
      */
     protected function validate_input($value, $rules = array()) {
         $errors = array();

         foreach ($rules as $rule => $params) {
             switch ($rule) {
                 case 'required':
                     if (empty($value)) {
                         $errors[] = 'This field is required';
                     }
                     break;

                 case 'min':
                     if ($value < $params) {
                         $errors[] = "Minimum value is {$params}";
                     }
                     break;

                 case 'max':
                     if ($value > $params) {
                         $errors[] = "Maximum value is {$params}";
                     }
                     break;

                 case 'regex':
                     if (!preg_match($params, $value)) {
                         $errors[] = 'Invalid format';
                     }
                     break;

                 case 'in':
                     if (!in_array($value, $params)) {
                         $errors[] = 'Invalid value selected';
                     }
                     break;
             }
         }

         return empty($errors) ? true : $errors;
     }

     public function render_customer_code_section($args) {
         ?>
         <p>Configure how customer codes are generated in the system.</p>
         <?php
     }

     /**
      * Render input text field
      */
     public function render_text_field($args) {
         $option_name = $args['option_name'];
         $field_name = $args['field_name'];
         $default = $args['default'];

         $options = get_option($option_name, array());
         $value = isset($options[$field_name]) ? $options[$field_name] : $default;
         ?>
         <input type="text"
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo esc_attr($option_name . '[' . $field_name . ']'); ?>"
                value="<?php echo esc_attr($value); ?>"
                class="regular-text">
         <?php
     }

     /**
      * Render format selection field
      */
     public function render_format_field() {
         $options = get_option('customer_settings', array());
         $current = isset($options['format_type']) ? $options['format_type'] : 1;

         $formats = array(
             1 => 'PREFIX/YYYY/MM/XXXXX (e.g., CUST/2024/12/00001)',
             2 => 'PREFIX/YYMM/XXXXX (e.g., CUST/2412/00001)',
             3 => 'PREFIX-XXXXX (e.g., CUST-00001)',
             4 => 'PREFIX-YYYY.MM.XXXXX (e.g., CUST-2024.12.00001)'
         );

         foreach ($formats as $value => $label) {
             ?>
             <div>
                 <label>
                     <input type="radio"
                            name="customer_settings[format_type]"
                            value="<?php echo esc_attr($value); ?>"
                            <?php checked($current, $value); ?>>
                     <?php echo esc_html($label); ?>
                 </label>
             </div>
             <?php
         }
     }

     /**
      * Render input number field
      */
     public function render_number_field($args) {
         $option_name = $args['option_name'];
         $field_name = $args['field_name'];
         $default = $args['default'];
         $min = $args['min'];
         $max = $args['max'];

         $options = get_option($option_name, array());
         $value = isset($options[$field_name]) ? $options[$field_name] : $default;
         ?>
         <input type="number"
                id="<?php echo esc_attr($args['label_for']); ?>"
                name="<?php echo esc_attr($option_name . '[' . $field_name . ']'); ?>"
                value="<?php echo esc_attr($value); ?>"
                min="<?php echo esc_attr($min); ?>"
                max="<?php echo esc_attr($max); ?>"
                class="small-text">
         <?php
     }

     /**
      * Render reset period selection field
      */
     /**
      * Render DataTable settings section
      */
     public function render_datatable_section($args) {
         ?>
         <p>Configure how data tables are displayed throughout the system.</p>
         <?php
     }

     /**
      * Render rows per page field for DataTable
      */
     public function render_rows_per_page_field() {
         $options = get_option('datatable_settings');
         $current = isset($options['per_page']) ? $options['per_page'] : 25;
         $choices = array(10, 25, 50, 100);

         foreach ($choices as $value) {
             ?>
             <div>
                 <label>
                     <input type="radio"
                            name="datatable_settings[per_page]"
                            value="<?php echo esc_attr($value); ?>"
                            <?php checked($current, $value); ?>>
                     <?php echo esc_html($value . ' rows'); ?>
                 </label>
             </div>
             <?php
         }
     }

     /**
      * Render sort direction field for DataTable
      */
     public function render_sort_direction_field() {
         $options = get_option('datatable_settings');
         $current = isset($options['sort_direction']) ? $options['sort_direction'] : 'desc';
         ?>
         <div>
             <label>
                 <input type="radio"
                        name="datatable_settings[sort_direction]"
                        value="asc"
                        <?php checked($current, 'asc'); ?>>
                 Ascending
             </label>
         </div>
         <div>
             <label>
                 <input type="radio"
                        name="datatable_settings[sort_direction]"
                        value="desc"
                        <?php checked($current, 'desc'); ?>>
                 Descending
             </label>
         </div>
         <?php
     }

     /**
      * Render responsive mode field for DataTable
      */
     public function render_responsive_field() {
         $options = get_option('datatable_settings');
         $current = isset($options['responsive']) ? $options['responsive'] : true;
         ?>
         <div>
             <label>
                 <input type="checkbox"
                        name="datatable_settings[responsive]"
                        value="1"
                        <?php checked($current, true); ?>>
                 Enable responsive mode
             </label>
         </div>
         <?php
     }

     /**
      * Sanitize cache settings
      */
     public function sanitize_cache_settings($input) {
         $output = array();

         // Sanitize enabled (boolean)
         $output['enabled'] = isset($input['enabled']);

         // Sanitize expiration (integer, minimal 300 detik/5 menit)
         $output['expiration'] = isset($input['expiration'])
             ? max(300, absint($input['expiration']))
             : 3600;

         // Pastikan groups adalah array
         $output['groups'] = array('customer', 'staff');

         return $output;
     }

     /**
      * Sanitize datatable settings
      */
     public function sanitize_datatable_settings($input) {
         $output = array();

         // Sanitize per page (integer, antara 10-100)
         $output['per_page'] = isset($input['per_page'])
             ? min(100, max(10, absint($input['per_page'])))
             : 25;

         // Sanitize sort direction (asc atau desc)
         $output['sort_direction'] = isset($input['sort_direction'])
             && in_array($input['sort_direction'], array('asc', 'desc'))
             ? $input['sort_direction']
             : 'desc';

         // Sanitize responsive mode (boolean)
         $output['responsive'] = isset($input['responsive']);

         // Sanitize default sort (hanya column yang valid)
         $valid_columns = array('id', 'customer_code', 'name', 'email', 'phone');
         $output['default_sort'] = isset($input['default_sort'])
             && in_array($input['default_sort'], $valid_columns)
             ? $input['default_sort']
             : 'id';

         return $output;
     }

     /**
      * Handle ajax clear cache
      */
     public function handle_clear_cache() {
         check_ajax_referer('clear_cache_nonce', 'nonce');

         if (!current_user_can('manage_options')) {
             wp_send_json_error(array(
                 'message' => 'Permission denied'
             ));
             return;
         }

         try {
             $cache_manager = new \Customer\Includes\Cache_Manager();
             $result = $cache_manager->clear_all();

             if ($result) {
                 wp_send_json_success(array(
                     'message' => 'Cache cleared successfully'
                 ));
             } else {
                 wp_send_json_error(array(
                     'message' => 'Failed to clear cache'
                 ));
             }
         } catch (\Exception $e) {
             wp_send_json_error(array(
                 'message' => 'Error: ' . $e->getMessage()
             ));
         }
     }

     /**
      * Register ajax handlers
      */
     public function register_ajax_handlers() {
         add_action('wp_ajax_clear_crm_cache', array($this, 'handle_clear_cache'));
     }
         $options = get_option('customer_settings', array());
         $current = isset($options['reset_period']) ? $options['reset_period'] : 'monthly';
         ?>
         <div>
             <label>
                 <input type="radio"
                        name="customer_settings[reset_period]"
                        value="monthly"
                        <?php checked($current, 'monthly'); ?>>
                 Monthly
             </label>
         </div>
         <div>
             <label>
                 <input type="radio"
                        name="customer_settings[reset_period]"
                        value="yearly"
                        <?php checked($current, 'yearly'); ?>>
                 Yearly
             </label>
         </div>
         <?php
     }

     /**
      * Render halaman settings
      */
     public function render_settings_page() {
         if (!current_user_can('manage_options')) {
             wp_die(__('You do not have sufficient permissions to access this page.'));
         }

         // Load view
         require_once CUSTOMER_PATH . 'views/admin/settings-system.php';
     }
 }
