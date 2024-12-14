<?php
/**
 * Customer Management System - Permissions Settings View
 *
 * This view file renders the permissions matrix interface in WordPress admin.
 * Provides UI for managing role-based capabilities across the CRM system.
 *
 * Features:
 * - Displays all custom CRM capabilities in a matrix format
 * - Shows all WordPress roles including custom CRM roles
 * - Allows administrators to assign/revoke capabilities per role
 * - Integrates with WordPress settings API
 *
 * @package     CustomerManagement
 * @subpackage  Views/Admin
 * @author      arisciwek <arisciwek@gmail.com>
 * @copyright   2024 arisciwek
 * @license     GPL-2.0-or-later
 * @since       1.0.0
 *
 * @wordpress-plugin
 * Path:        views/admin/settings-permissions.php
 * Created:     2024-12-14
 * Modified:    2024-12-14
 */

// Prevent direct access
defined('ABSPATH') || exit;
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php settings_fields('crm_permissions_options'); ?>

        <table class="widefat fixed striped" id="capabilities-matrix">
            <thead>
                <tr>
                    <th>Capability</th>
                    <th>Administrator</th>
                    <th>CRM Manager</th>
                    <th>CRM Staff</th>
                    <?php
                    // Show other WordPress roles
                    foreach(get_editable_roles() as $role_name => $role_info):
                        if (!in_array($role_name, ['administrator', 'crm_manager', 'crm_staff'])):
                    ?>
                        <th><?php echo esc_html(translate_user_role($role_info['name'])); ?></th>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $capabilities = Customer\Includes\Customer_Capabilities::get_all_caps();
                foreach ($capabilities as $cap => $label):
                ?>
                <tr>
                    <td><?php echo esc_html($label); ?></td>
                    <?php
                    foreach(get_editable_roles() as $role_name => $role_info):
                        $role = get_role($role_name);
                        $checked = $role->has_cap($cap);
                        $disabled = $role_name === 'administrator';
                    ?>
                    <td>
                        <input type="checkbox"
                               name="crm_role_caps[<?php echo esc_attr($role_name); ?>][<?php echo esc_attr($cap); ?>]"
                               <?php checked($checked); ?>
                               <?php disabled($disabled); ?>>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php submit_button('Save Permissions'); ?>
    </form>
</div>
