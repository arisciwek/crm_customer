<?php
/**
 * Customer Management System - System Settings View
 *
 * Renders the system settings interface with the following features:
 * - Customer code format configuration
 * - Cache settings management
 * - DataTable display options
 * - Live preview of customer code format
 *
 * This view provides a tabbed interface for:
 * - System Settings (customer code, cache, DataTable)
 * - Permissions Matrix
 *
 * @package     CustomerManagement
 * @subpackage  Views/Admin
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        views/admin/settings-system.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

 // Prevent direct access
 defined('ABSPATH') || exit;

 // Get active tab
 $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'system';

 // Get current settings
 $customer_settings = get_option('customer_settings', array());
 $cache_settings = get_option('cache_settings', array());
 $datatable_settings = get_option('datatable_settings', array());
 ?>

 <div class="wrap">
     <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

     <div class="nav-tab-wrapper">
         <a href="?page=customer-settings&tab=system"
            class="nav-tab <?php echo $active_tab == 'system' ? 'nav-tab-active' : ''; ?>">
             System Settings
         </a>
         <a href="?page=customer-settings&tab=permissions"
            class="nav-tab <?php echo $active_tab == 'permissions' ? 'nav-tab-active' : ''; ?>">
             Permissions
         </a>
     </div>

     <?php settings_errors(); ?>

     <?php if ($active_tab == 'system'): ?>

         <div class="settings-container" style="margin-top: 20px;">
             <form method="post" action="options.php">
                 <?php settings_fields('crm_system_settings'); ?>

                 <!-- Customer Code Settings Section -->
                 <div class="settings-section">
                     <?php do_settings_sections('crm_system_settings'); ?>

                     <!-- Preview Section -->
                     <div class="customer-code-preview" style="margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
                         <h3>
                             <span>Customer Code Preview</span>
                             <span class="description" style="font-size: 13px; font-weight: normal; margin-left: 10px;">
                                 (Live preview based on your settings)
                             </span>
                         </h3>
                         <div class="preview-box" style="margin-top: 10px;">
                             <code id="customer-code-preview" style="font-size: 16px; padding: 10px; background: #f5f5f5; display: inline-block;">
                                 <!-- Will be updated via JavaScript -->
                             </code>
                         </div>
                     </div>
                 </div>

                 <?php submit_button('Save Settings'); ?>
             </form>
         </div>

         <style>
             .settings-section {
                 background: #fff;
                 padding: 20px;
                 border: 1px solid #ddd;
                 border-radius: 4px;
                 margin-bottom: 20px;
             }
             .form-table th {
                 width: 250px;
                 padding: 20px;
             }
             .settings-section h2 {
                 margin-top: 0;
                 padding-bottom: 10px;
                 border-bottom: 1px solid #eee;
             }
             .preview-box {
                 margin-top: 15px;
             }
             .description {
                 color: #666;
                 font-style: italic;
             }
         </style>

         <!-- JavaScript untuk live preview -->
         <script type="text/javascript">
         jQuery(document).ready(function($) {
             // Elements
             const prefixInput = $('#customer_code_prefix');
             const formatTypeInputs = $('input[name="customer_settings[format_type]"]');
             const digitLengthInput = $('#customer_code_digit_length');
             const previewElement = $('#customer-code-preview');

             // Format date functions
             function padZero(num) {
                 return String(num).padStart(2, '0');
             }

             function getFormattedDate() {
                 const now = new Date();
                 return {
                     year: now.getFullYear(),
                     shortYear: String(now.getFullYear()).slice(-2),
                     month: padZero(now.getMonth() + 1)
                 };
             }

             // Update preview function
             function updatePreview() {
                 const prefix = prefixInput.val() || 'CUST';
                 const formatType = $('input[name="customer_settings[format_type]"]:checked').val() || '1';
                 const digits = parseInt(digitLengthInput.val() || '5');
                 const date = getFormattedDate();
                 const number = '1'.padStart(digits, '0');

                 let preview = '';
                 switch(formatType) {
                     case '1': // PREFIX/YYYY/MM/XXXXX
                         preview = `${prefix}/${date.year}/${date.month}/${number}`;
                         break;
                     case '2': // PREFIX/YYMM/XXXXX
                         preview = `${prefix}/${date.shortYear}${date.month}/${number}`;
                         break;
                     case '3': // PREFIX-XXXXX
                         preview = `${prefix}-${number}`;
                         break;
                     case '4': // PREFIX-YYYY.MM.XXXXX
                         preview = `${prefix}-${date.year}.${date.month}.${number}`;
                         break;
                 }

                 // Update preview dengan animasi
                 previewElement.fadeOut(200, function() {
                     $(this).text(preview).fadeIn(200);
                 });
             }

             // Event listeners
             prefixInput.on('input', updatePreview);
             formatTypeInputs.on('change', updatePreview);
             digitLengthInput.on('input', updatePreview);

             // Initial preview
             updatePreview();

             // Tambahkan validasi untuk prefix
             prefixInput.on('input', function() {
                 let value = $(this).val();
                 // Hanya ijinkan huruf, angka, dan dash
                 value = value.replace(/[^a-zA-Z0-9-]/g, '');
                 $(this).val(value);
             });

             // Cache clear button handler
             $('#clear-cache-button').on('click', function() {
                 const button = $(this);
                 const status = $('#cache-clear-status');

                 if (button.prop('disabled')) {
                     return;
                 }

                 button.prop('disabled', true)
                       .addClass('updating-message');
                 status.html('Clearing cache...')
                       .css('color', '#666');

                 $.ajax({
                     url: ajaxurl,
                     type: 'POST',
                     data: {
                         action: 'clear_crm_cache',
                         nonce: $('#_wpnonce').val()
                     },
                     success: function(response) {
                         if (response.success) {
                             status.html('Cache cleared successfully!')
                                   .css('color', '#46b450');
                         } else {
                             status.html('Failed to clear cache.')
                                   .css('color', '#dc3232');
                         }
                     },
                     error: function() {
                         status.html('Error occurred while clearing cache.')
                               .css('color', '#dc3232');
                     },
                     complete: function() {
                         button.prop('disabled', false)
                               .removeClass('updating-message');
                         setTimeout(function() {
                             status.fadeOut(500, function() {
                                 $(this).html('').show();
                             });
                         }, 3000);
                     }
                 });
             });
         });
         </script>

     <?php elseif ($active_tab == 'permissions'): ?>
         <?php
         $settings = new Customer\Controllers\Settings_Controller();
         $settings->render_settings_page();
         ?>
     <?php endif; ?>
 </div>
