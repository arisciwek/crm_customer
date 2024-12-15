# Rekomendasi Perbaikan Settings:
beberapa rekomendasi perbaikan

## Analisis 4 file terakhir yang berpotensi masalah:

### 1. `class-admin-controller.php`:
Tidak ada masalah serius, tapi ada beberapa poin yang bisa dioptimalkan:
- Pengecekan kondisional pada enqueue scripts bisa disederhanakan
- Pengecekan file existence berulang kali bisa dioptimalkan
- Loading script permissions.js tidak perlu dibungkus dalam kondisional karena sudah ada pengecekan hook sebelumnya

### 2. `class-permission-settings-controller.php`:
Ada beberapa masalah:
- Method `initialize_default_caps()` dipanggil tapi tidak ada dalam class tersebut (ada di class Customer_Capabilities)
- Tidak ada error handling yang spesifik dalam try-catch block
- Tidak ada validasi return value saat memanggil delete_option()

### 3. `class-customer-capabilities.php`:
Beberapa potensi masalah:
- Tidak ada pengecekan apakah role berhasil dibuat saat add_role()
- Tidak ada error handling jika get_role() return null
- Method initialize_default_caps() melakukan banyak operasi database yang bisa dioptimalkan

### 4. `permissions.js`:
Ada beberapa masalah:
- Menggunakan variabel tidak terdefinisi `ajaxurl` (seharusnya `customerPermissions.ajaxurl`)
- Mengakses `customer_permissions_nonce.nonce` yang tidak benar (seharusnya `customerPermissions.nonce`)
- Tidak ada error handling spesifik untuk berbagai tipe error AJAX
- Tidak ada pengecekan apakah response memiliki property yang dibutuhkan sebelum diakses

## Rekomendasi perbaikan:

### 1. Untuk `class-admin-controller.php`:
```php
public function enqueue_scripts($hook) {
    // Simplify condition
    $valid_pages = ['customer-management', 'customer-settings', 'customer-permissions'];
    if (!in_array(str_replace('customers_page_', '', $hook), $valid_pages)) {
        return;
    }
    // Rest of the code...
}
```

### 2. Untuk `class-permission-settings-controller.php`:
```php
public function handle_reset_customer_permissions() {
    try {
        if (!check_ajax_referer('customer_permissions_nonce', 'nonce', false)) {
            throw new \Exception('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            throw new \Exception('Insufficient permissions');
        }

        // Reset capabilities
        if (!\Customer\Includes\Customer_Capabilities::initialize_default_caps()) {
            throw new \Exception('Failed to reset capabilities');
        }

        // Reset option
        if (false === delete_option($this->option_name)) {
            throw new \Exception('Failed to delete options');
        }

        wp_send_json_success(['message' => 'Permissions reset successfully']);
    } catch (\Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}
```

### 3. Untuk `class-customer-capabilities.php`:
```php
public static function initialize_default_caps() {
    try {
        $admin = get_role('administrator');
        if (!$admin) {
            throw new \Exception('Administrator role not found');
        }

        // Batch the database operations
        $roles_to_update = [
            'administrator' => self::$capabilities,
            'crm_manager' => $manager_caps,
            'crm_staff' => $staff_caps
        ];

        foreach ($roles_to_update as $role_name => $caps) {
            $role = get_role($role_name);
            if (!$role) {
                $role = add_role($role_name, ucfirst($role_name));
                if (!$role) {
                    throw new \Exception("Failed to create role: $role_name");
                }
            }
            // Update caps
            foreach ($caps as $cap => $label) {
                $role->add_cap($cap);
            }
        }
        return true;
    } catch (\Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}
```

### 4. Untuk `permissions.js`:
```javascript
jQuery(document).ready(function($) {
    $('#reset-customer-permissions').on('click', function(e) {
        e.preventDefault();

        if (!customerPermissions || !customerPermissions.ajaxurl) {
            console.error('Required configuration missing');
            return;
        }

        if (!confirm('Are you sure you want to reset permissions?')) {
            return;
        }

        const $button = $(this);
        const originalText = $button.html();

        $button.html('<span class="dashicons dashicons-update-alt spin"></span> Resetting...')
               .prop('disabled', true);

        $.ajax({
            url: customerPermissions.ajaxurl,
            type: 'POST',
            data: {
                action: 'reset_customer_permissions',
                nonce: customerPermissions.nonce
            },
            success: function(response) {
                if (response && response.success) {
                    showNotice('success', response.data?.message || 'Permissions reset successfully');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotice('error', response.data?.message || 'Failed to reset permissions');
                    resetButton();
                }
            },
            error: function(xhr, status, error) {
                showNotice('error', `Error: ${error}`);
                resetButton();
            }
        });

        function resetButton() {
            $button.html(originalText).prop('disabled', false);
        }

        function showNotice(type, message) {
            const notice = $(`<div class="notice notice-${type} is-dismissible"><p>${message}</p></div>`);
            $('.wrap > h2').after(notice);
        }
    });
});
```
