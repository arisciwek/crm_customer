/**
 * Customer Management System - Settings JavaScript
 *
 * Handles all JavaScript functionality for the settings pages including:
 * - Customer code preview generation
 * - Cache management operations
 * - Real-time settings preview updates
 *
 * Functions:
 * - Live preview of customer code format
 * - Cache clearing functionality
 * - Settings form interactions
 *
 * @package     CustomerManagement
 * @subpackage  Assets/JS
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        assets/js/settings.js
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

jQuery(document).ready(function($) {
    // Customer Code Preview Functionality
    class CustomerCodePreview {
        constructor() {
            this.elements = {
                prefix: $('#customer_code_prefix'),
                formatType: $('input[name="customer_settings[format_type]"]'),
                digitLength: $('#digit_length'),
                resetPeriod: $('input[name="customer_settings[reset_period]"]'),
                preview: $('#customer-code-preview')
            };

            this.initEventListeners();
            this.updatePreview(); // Initial preview
        }

        initEventListeners() {
            this.elements.prefix.on('input', () => this.updatePreview());
            this.elements.formatType.on('change', () => this.updatePreview());
            this.elements.digitLength.on('input', () => this.updatePreview());
            this.elements.resetPeriod.on('change', () => this.updatePreview());
        }

        padZero(num) {
            return String(num).padStart(2, '0');
        }

        getFormattedDate() {
            const now = new Date();
            return {
                year: now.getFullYear(),
                shortYear: String(now.getFullYear()).slice(-2),
                month: this.padZero(now.getMonth() + 1)
            };
        }

        updatePreview() {
            const prefix = this.elements.prefix.val() || 'CUST';
            const formatType = this.elements.formatType.filter(':checked').val() || '1';
            const digits = parseInt(this.elements.digitLength.val() || '5');
            const date = this.getFormattedDate();
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

            // Update preview with animation
            this.elements.preview.fadeOut(200, function() {
                $(this).text(preview).fadeIn(200);
            });
        }
    }

    // Cache Management Functionality
    class CacheManager {
        constructor() {
            this.elements = {
                clearButton: $('#clear-cache-button'),
                status: $('#cache-clear-status')
            };

            this.initEventListeners();
        }

        initEventListeners() {
            this.elements.clearButton.on('click', () => this.handleCacheClear());
        }

        async handleCacheClear() {
            const button = this.elements.clearButton;
            const status = this.elements.status;

            if (button.prop('disabled')) return;

            button.prop('disabled', true)
                  .addClass('updating-message');
            status.html('Clearing cache...')
                  .css('color', '#666');

            try {
                const response = await $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'clear_crm_cache',
                        nonce: $('#_wpnonce').val()
                    }
                });

                if (response.success) {
                    status.html('Cache cleared successfully!')
                          .css('color', '#46b450');
                } else {
                    status.html('Failed to clear cache.')
                          .css('color', '#dc3232');
                }
            } catch (error) {
                status.html('Error occurred while clearing cache.')
                      .css('color', '#dc3232');
                console.error('Cache clear error:', error);
            } finally {
                button.prop('disabled', false)
                      .removeClass('updating-message');

                setTimeout(() => {
                    status.fadeOut(500, function() {
                        $(this).html('').show();
                    });
                }, 3000);
            }
        }
    }

    // Initialize all functionality
    function initSettings() {
        new CustomerCodePreview();
        new CacheManager();
    }

    // Start the application
    initSettings();
});
