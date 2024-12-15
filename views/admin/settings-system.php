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
         <form method="post" action="options.php">
             <?php settings_fields('crm_system_settings'); ?>

             <div class="settings-container">
                 <!-- Customer Code Settings Section -->
                 <div class="settings-section">
                     <h2>Customer Code Settings</h2>
                     <?php
                     $customer_settings = get_option('customer_settings', array());
                     ?>
                     <table class="form-table">
                         <tr>
                             <th scope="row">Customer Code Prefix</th>
                             <td>
                                 <input type="text"
                                        id="customer_code_prefix"
                                        name="customer_settings[prefix]"
                                        value="<?php echo esc_attr(isset($customer_settings['prefix']) ? $customer_settings['prefix'] : 'CUST'); ?>"
                                        class="regular-text">
                                 <p class="description">Prefix yang akan digunakan di awal kode customer</p>
                             </td>
                         </tr>
                         <tr>
                             <th scope="row">Format Type</th>
                             <td>
                                 <?php
                                 $current_format = isset($customer_settings['format_type']) ? $customer_settings['format_type'] : 1;
                                 $formats = array(
                                     1 => 'PREFIX/YYYY/MM/XXXXX (e.g., CUST/2024/12/00001)',
                                     2 => 'PREFIX/YYMM/XXXXX (e.g., CUST/2412/00001)',
                                     3 => 'PREFIX-XXXXX (e.g., CUST-00001)',
                                     4 => 'PREFIX-YYYY.MM.XXXXX (e.g., CUST-2024.12.00001)'
                                 );
                                 foreach ($formats as $value => $label):
                                 ?>
                                 <div class="radio-group">
                                     <label>
                                         <input type="radio"
                                                name="customer_settings[format_type]"
                                                value="<?php echo esc_attr($value); ?>"
                                                <?php checked($current_format, $value); ?>>
                                         <?php echo esc_html($label); ?>
                                     </label>
                                 </div>
                                 <?php endforeach; ?>
                                 <p class="description">Pilih format untuk kode customer</p>
                             </td>
                         </tr>
                         <tr>
                             <th scope="row">Number Length</th>
                             <td>
                                 <input type="number"
                                        id="digit_length"
                                        name="customer_settings[digit_length]"
                                        value="<?php echo esc_attr(isset($customer_settings['digit_length']) ? $customer_settings['digit_length'] : 5); ?>"
                                        min="3"
                                        max="10"
                                        class="small-text">
                                 <p class="description">Panjang angka dalam kode (3-10 digit)</p>
                             </td>
                         </tr>
                         <tr>
                             <th scope="row">Reset Period</th>
                             <td>
                                 <?php
                                 $current_period = isset($customer_settings['reset_period']) ? $customer_settings['reset_period'] : 'monthly';
                                 ?>
                                 <div class="radio-group">
                                     <label>
                                         <input type="radio"
                                                name="customer_settings[reset_period]"
                                                value="monthly"
                                                <?php checked($current_period, 'monthly'); ?>>
                                         Monthly
                                     </label>
                                 </div>
                                 <div class="radio-group">
                                     <label>
                                         <input type="radio"
                                                name="customer_settings[reset_period]"
                                                value="yearly"
                                                <?php checked($current_period, 'yearly'); ?>>
                                         Yearly
                                     </label>
                                 </div>
                                 <p class="description">Kapan nomor urut direset</p>
                             </td>
                         </tr>
                     </table>

                     <!-- Preview Section -->
                     <div class="customer-code-preview">
                         <h3>
                             Customer Code Preview
                             <span class="description">(Live preview based on your settings)</span>
                         </h3>
                         <div class="preview-box">
                             <code id="customer-code-preview">
                                 <!-- Will be updated via JavaScript -->
                             </code>
                         </div>
                     </div>
                 </div>

                 <!-- Cache Settings Section -->
                 <div class="settings-section">
                     <h2>Cache Settings</h2>
                     <?php
                     $cache_settings = get_option('cache_settings', array());
                     ?>
                     <table class="form-table">
                         <tr>
                             <th scope="row">Enable Cache</th>
                             <td>
                                 <label>
                                     <input type="checkbox"
                                            name="cache_settings[enabled]"
                                            value="1"
                                            <?php checked(isset($cache_settings['enabled']) ? $cache_settings['enabled'] : true); ?>>
                                     Enable caching system
                                 </label>
                                 <p class="description">Enable/disable system caching</p>
                             </td>
                         </tr>
                         <tr>
                             <th scope="row">Cache Duration</th>
                             <td>
                                 <?php
                                 $current_duration = isset($cache_settings['expiration']) ? $cache_settings['expiration'] : 3600;
                                 $durations = array(
                                     1800 => '30 Minutes',
                                     3600 => '1 Hour',
                                     7200 => '2 Hours',
                                     21600 => '6 Hours',
                                     43200 => '12 Hours',
                                     86400 => '24 Hours'
                                 );
                                 foreach ($durations as $value => $label):
                                 ?>
                                 <div class="radio-group">
                                     <label>
                                         <input type="radio"
                                                name="cache_settings[expiration]"
                                                value="<?php echo esc_attr($value); ?>"
                                                <?php checked($current_duration, $value); ?>>
                                         <?php echo esc_html($label); ?>
                                     </label>
                                 </div>
                                 <?php endforeach; ?>
                                 <p class="description">How long to keep items in cache</p>
                             </td>
                         </tr>
                     </table>
                 </div>

                 <!-- DataTable Settings Section -->
                 <div class="settings-section">
                     <h2>DataTable Settings</h2>
                     <?php
                     $datatable_settings = get_option('datatable_settings', array());
                     ?>
                     <table class="form-table">
                         <tr>
                             <th scope="row">Rows Per Page</th>
                             <td>
                                 <?php
                                 $current_rows = isset($datatable_settings['per_page']) ? $datatable_settings['per_page'] : 25;
                                 $rows_options = array(10, 25, 50, 100);
                                 foreach ($rows_options as $value):
                                 ?>
                                 <div class="radio-group">
                                     <label>
                                         <input type="radio"
                                                name="datatable_settings[per_page]"
                                                value="<?php echo esc_attr($value); ?>"
                                                <?php checked($current_rows, $value); ?>>
                                         <?php echo esc_html($value . ' rows'); ?>
                                     </label>
                                 </div>
                                 <?php endforeach; ?>
                                 <p class="description">Number of rows to display per page</p>
                             </td>
                         </tr>
                         <tr>
                             <th scope="row">Default Sort</th>
                             <td>
                                 <?php
                                 $current_sort = isset($datatable_settings['sort_direction']) ? $datatable_settings['sort_direction'] : 'desc';
                                 ?>
                                 <div class="radio-group">
                                     <label>
                                         <input type="radio"
                                                name="datatable_settings[sort_direction]"
                                                value="asc"
                                                <?php checked($current_sort, 'asc'); ?>>
                                         Ascending
                                     </label>
                                 </div>
                                 <div class="radio-group">
                                     <label>
                                         <input type="radio"
                                                name="datatable_settings[sort_direction]"
                                                value="desc"
                                                <?php checked($current_sort, 'desc'); ?>>
                                         Descending
                                     </label>
                                 </div>
                                 <p class="description">Default sorting direction</p>
                             </td>
                         </tr>
                     </table>
                 </div>
             </div>

             <?php submit_button('Save Settings'); ?>
       </form>
     <?php elseif ($active_tab == 'permissions'): ?>
         <?php
         $settings = new Customer\Controllers\Settings_Controller();
         $settings->render_settings_page();
         ?>
     <?php endif; ?>

 </div>
