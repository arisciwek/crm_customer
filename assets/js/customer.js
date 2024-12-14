// assets/js/customer.js
const Customer = {
    init: function() {
        this.initDataTable();
        this.initTabs();
    },

    initDataTable: function() {
        // Ambil konfigurasi DataTable dari server
        jQuery.ajax({
            url: customerPlugin.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_datatable_config',
                nonce: customerPlugin.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Inisialisasi DataTable dengan konfigurasi dari server
                    const config = response.data;

                    const defaultConfig = {
                        processing: true,
                        serverSide: false,
                        ajax: {
                            url: customerPlugin.ajaxurl,
                            type: 'POST',
                            data: function(d) {
                                d.action = 'get_customers_data';
                                d.nonce = customerPlugin.nonce;
                            }
                        },
                        columns: [
                            { data: 'id' },
                            { data: 'customer_code' },
                            { data: 'name' },
                            { data: 'email' },
                            { data: 'phone' },
                            { data: 'actions' }
                        ],
                        // Gunakan konfigurasi dari server
                        pageLength: config.pageLength,
                        order: config.order,
                        responsive: config.responsive,
                        language: {
                            lengthMenu: 'Show _MENU_ entries per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                            search: 'Search:',
                            paginate: {
                                first: 'First',
                                last: 'Last',
                                next: 'Next',
                                previous: 'Previous'
                            }
                        },
                        // Tambahan konfigurasi untuk responsif
                        responsive: {
                            details: {
                                type: 'column',
                                target: 'tr'
                            }
                        },
                        // Callback setelah data dimuat
                        drawCallback: function(settings) {
                            // Re-init tooltips atau elemen UI lainnya jika ada
                        }
                    };

                    // Gabungkan konfigurasi default dengan konfigurasi dari server
                    const finalConfig = {...defaultConfig, ...config};

                    // Inisialisasi DataTable
                    jQuery('#customers-table').DataTable(finalConfig);
                } else {
                    console.error('Failed to load DataTable configuration');
                    // Fallback ke konfigurasi default jika gagal
                    Customer.initDataTableWithDefaults();
                }
            },
            error: function() {
                console.error('Error loading DataTable configuration');
                // Fallback ke konfigurasi default jika error
                Customer.initDataTableWithDefaults();
            }
        });
    },

    // Fungsi fallback dengan konfigurasi default
    initDataTableWithDefaults: function() {
        jQuery('#customers-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: customerPlugin.ajaxurl,
                type: 'POST',
                data: function(d) {
                    d.action = 'get_customers_data';
                    d.nonce = customerPlugin.nonce;
                }
            },
            columns: [
                { data: 'id' },
                { data: 'customer_code' },
                { data: 'name' },
                { data: 'email' },
                { data: 'phone' },
                { data: 'actions' }
            ],
            pageLength: 25,
            order: [[0, 'desc']],
            responsive: true
        });
    },

    initTabs: function() {
        jQuery('.tab-button').on('click', function() {
            const tabId = jQuery(this).data('tab');

            // Update buttons
            jQuery('.tab-button').removeClass('active');
            jQuery(this).addClass('active');

            // Update content
            jQuery('.tab-pane').removeClass('active');
            jQuery('#' + tabId).addClass('active');
        });
    },

    loadDetails: function(customerId) {
        jQuery.ajax({
            url: customerPlugin.ajaxurl,
            type: 'GET',
            data: {
                action: 'get_customer_details',
                id: customerId,
                nonce: customerPlugin.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update customer details tab
                    jQuery('#customer-details').html(response.data.html);

                    // Show right panel if hidden
                    jQuery('#right-panel').addClass('active');

                    // Ensure customer details tab is active
                    jQuery('.tab-button[data-tab="customer-details"]').click();
                } else {
                    alert('Error loading customer details');
                }
            },
            error: function() {
                alert('Error connecting to server');
            }
        });
    }
};

jQuery(document).ready(function() {
    Customer.init();
});
