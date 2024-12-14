<?php
// views/admin/tabs/customer-details.php
if (!isset($customer) || !is_array($customer)) {
    return;
}
?>
<div class="customer-details-tab">
    <div class="customer-header">
        <h2><?php echo esc_html($customer['name']); ?></h2>
        <div class="customer-actions">
            <button class="button edit-customer" data-id="<?php echo esc_attr($customer['id']); ?>">
                <span class="dashicons dashicons-edit"></span> Edit
            </button>
            <button class="button delete-customer" data-id="<?php echo esc_attr($customer['id']); ?>">
                <span class="dashicons dashicons-trash"></span> Delete
            </button>
        </div>
    </div>

    <div class="customer-info-grid">
        <div class="customer-detail">
            <label>Email</label>
            <div class="value">
                <a href="mailto:<?php echo esc_attr($customer['email']); ?>">
                    <?php echo esc_html($customer['email']); ?>
                </a>
            </div>
        </div>

        <div class="customer-detail">
            <label>Phone</label>
            <div class="value">
                <a href="tel:<?php echo esc_attr($customer['phone']); ?>">
                    <?php echo esc_html($customer['phone']); ?>
                </a>
            </div>
        </div>

        <div class="customer-detail">
            <label>Address</label>
            <div class="value">
                <?php echo nl2br(esc_html($customer['address'])); ?>
            </div>
        </div>

        <div class="customer-detail full-width">
            <label>Notes</label>
            <div class="value">
                <?php echo nl2br(esc_html($customer['notes'])); ?>
            </div>
        </div>

        <div class="customer-detail">
            <label>Created At</label>
            <div class="value">
                <?php echo esc_html(date('F j, Y g:i a', strtotime($customer['created_at']))); ?>
            </div>
        </div>
    </div>

    <!-- Customer Edit Form (Hidden by default) -->
    <div id="customer-edit-form" class="customer-edit-form" style="display: none;">
        <form id="edit-customer-form">
            <input type="hidden" name="customer_id" value="<?php echo esc_attr($customer['id']); ?>">

            <div class="form-row">
                <label for="customer_name">Name</label>
                <input type="text" id="customer_name" name="name" value="<?php echo esc_attr($customer['name']); ?>" required>
            </div>

            <div class="form-row">
                <label for="customer_email">Email</label>
                <input type="email" id="customer_email" name="email" value="<?php echo esc_attr($customer['email']); ?>" required>
            </div>

            <div class="form-row">
                <label for="customer_phone">Phone</label>
                <input type="tel" id="customer_phone" name="phone" value="<?php echo esc_attr($customer['phone']); ?>" required>
            </div>

            <div class="form-row">
                <label for="customer_address">Address</label>
                <textarea id="customer_address" name="address" rows="3"><?php echo esc_textarea($customer['address']); ?></textarea>
            </div>

            <div class="form-row">
                <label for="customer_notes">Notes</label>
                <textarea id="customer_notes" name="notes" rows="4"><?php echo esc_textarea($customer['notes']); ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Save Changes</button>
                <button type="button" class="button cancel-edit">Cancel</button>
            </div>
        </form>
    </div>
</div>
