Baik, mari kita mulai implementasi sistem settings yang sudah kita diskusikan. Kita akan mulai dengan membuat controller dan view untuk settings.

Pertama, saya akan membuat controller untuk settings:

```php

<?php
/**
 * Controller untuk mengelola System Settings
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
    public function register_settings() {
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
    public function render_reset_period_field() {
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

```

Selanjutnya, saya akan membuat view untuk halaman settings:

```php
<?php
/**
 * View untuk System Settings
 */

// Prevent direct access
defined('ABSPATH') || exit;

// Get active tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'system';
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <h2 class="nav-tab-wrapper">
        <a href="?page=customer-settings&tab=system"
           class="nav-tab <?php echo $active_tab == 'system' ? 'nav-tab-active' : ''; ?>">
            System Settings
        </a>
        <a href="?page=customer-settings&tab=permissions"
           class="nav-tab <?php echo $active_tab == 'permissions' ? 'nav-tab-active' : ''; ?>">
            Permissions
        </a>
    </h2>

    <?php settings_errors(); ?>

    <?php if ($active_tab == 'system'): ?>

        <form method="post" action="options.php">
            <?php
            settings_fields('crm_system_settings');
            do_settings_sections('crm_system_settings');
            submit_button('Save Settings');
            ?>
        </form>

        <!-- Preview Section -->
        <div class="customer-code-preview" style="margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ccc;">
            <h3>Customer Code Preview</h3>
            <p>Based on your current settings, the next customer code will be:</p>
            <code id="customer-code-preview" style="font-size: 16px; padding: 10px; background: #f5f5f5; display: inline-block;">
                <!-- Will be updated via JavaScript -->
            </code>
        </div>

    <?php elseif ($active_tab == 'permissions'): ?>
        <?php
        $settings = new Customer\Controllers\Settings_Controller();
        $settings->render_settings_page();
        ?>
    <?php endif; ?>
</div>

<!-- JavaScript untuk preview -->
<script type="text/javascript">
jQuery(document).ready(function($) {
    function updatePreview() {
        var prefix = $('#customer_code_prefix').val() || 'CUST';
        var format = $('input[name="customer_settings[format_type]"]:checked').val() || '1';
        var digits = $('#customer_code_digit_length').val() || '5';

        var now = new Date();
        var year = now.getFullYear();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var number = '1'.padStart(digits, '0');

        var preview = '';
        switch(format) {
            case '1':
                preview = `${prefix}/${year}/${month}/${number}`;
                break;
            case '2':
                preview = `${prefix}/${year.toString().substr(-2)}${month}/${number}`;
                break;
            case '3':
                preview = `${prefix}-${number}`;
                break;
            case '4':
                preview = `${prefix}-${year}.${month}.${number}`;
                break;
        }

        $('#customer-code-preview').text(preview);
    }

    // Update preview when settings change
    $('#customer_code_prefix').on('input', updatePreview);
    $('input[name="customer_settings[format_type]"]').on('change', updatePreview);
    $('#customer_code_digit_length').on('input', updatePreview);

    // Initial preview
    updatePreview();
});
</script>


```
