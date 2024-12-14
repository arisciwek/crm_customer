<!-- views/admin/customer-list.php -->
<div class="wrap">
    <h1>Customer Management</h1>

    <div class="customer-container">
        <!-- Left side - DataTable -->
        <div class="customer-table-container">
            <table id="customers-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Right side - Details Panel -->
        <div id="right-panel" class="customer-right-panel">
            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-button active" data-tab="customer-details">Customer Details</button>
                <button class="tab-button" data-tab="staff-management">Staff Management</button>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <div id="customer-details" class="tab-pane active">
                    <!-- Customer details will be loaded here -->
                    <div class="placeholder-message">
                        Select a customer to view details
                    </div>
                </div>
                <div id="staff-management" class="tab-pane">
                    <!-- Staff management will be implemented later -->
                    <div class="placeholder-message">
                        Staff management feature coming soon
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
