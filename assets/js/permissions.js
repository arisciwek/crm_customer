/**
 * Customer Management System - Permissions JavaScript
 *
 * Handles AJAX functionality for the permissions settings page:
 * - Reset permissions to default values
 * - Display loading states
 * - Show success/error messages
 *
 * @package     CustomerManagement
 * @subpackage  Assets/JS
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * Path:        assets/js/permissions.js
 * Created:     2024-12-15
 * Modified:    2024-12-15
 */
 jQuery(document).ready(function($) {
     $('#reset-customer-permissions').on('click', function(e) {
         e.preventDefault();

         if (!confirm('Are you sure you want to reset all permissions to default? This will restore the initial permission settings for all roles.')) {
             return;
         }

         const $button = $(this);
         const originalText = $button.html();

         $button.html('<span class="dashicons dashicons-update-alt spin"></span> Resetting...').prop('disabled', true);

         $.ajax({
             url: ajaxurl,
             type: 'POST',
             data: {
                 action: 'reset_customer_permissions',
                 nonce: customerPermissions.nonce // Menggunakan nonce yang benar dari localized script
             },
             success: function(response) {
                 if (response.success) {
                     // Tampilkan pesan sukses
                     const notice = $('<div class="notice notice-success is-dismissible"><p>' + response.data.message + '</p></div>');
                     $('.wrap > h2').after(notice);

                     // Reload halaman setelah delay singkat
                     setTimeout(function() {
                         window.location.reload();
                     }, 1500);
                 } else {
                     const notice = $('<div class="notice notice-error is-dismissible"><p>' + response.data.message + '</p></div>');
                     $('.wrap > h2').after(notice);
                     $button.html(originalText).prop('disabled', false);
                 }
             },
             error: function() {
                 const notice = $('<div class="notice notice-error is-dismissible"><p>Failed to reset permissions. Please try again.</p></div>');
                 $('.wrap > h2').after(notice);
                 $button.html(originalText).prop('disabled', false);
             }
         });
     });
 });
