# Customer Management System

A comprehensive WordPress plugin for managing customers with staff management capabilities.

## Features

### Customer Management
- Complete CRUD operations for customer data
- Automatic customer code generation with configurable formats
- DataTables integration for better data presentation
- Advanced search and filtering capabilities
- Customer details viewing with tabbed interface

### Customer's Staff Management
- Manage staff members associated with customers
- Role-based access control
- Department and position management
- Staff hierarchy support

### System Features
- Built-in caching system for improved performance
- Configurable DataTable settings
- Role-based permissions management
- Membership level management
- Custom capability system

## Installation

1. Upload the plugin files to the `/wp-content/plugins/customer-management` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure the plugin settings via the 'Customers > Settings' menu

## Configuration

### System Settings
Access system settings through the WordPress admin panel:
1. Go to Customers > Settings
2. Configure the following:
   - Customer code format
   - Cache settings
   - DataTable display options

### Permissions
Configure role-based permissions:
1. Go to Customers > Settings > Permissions
2. Assign capabilities to different user roles
3. Available roles:
   - CRM Manager
   - CRM Staff
   - Custom WordPress roles

## Customer Code Format

The plugin supports multiple customer code formats:
1. PREFIX/YYYY/MM/XXXXX (e.g., CUST/2024/12/00001)
2. PREFIX/YYMM/XXXXX (e.g., CUST/2412/00001)
3. PREFIX-XXXXX (e.g., CUST-00001)
4. PREFIX-YYYY.MM.XXXXX (e.g., CUST-2024.12.00001)

## Database Structure

### Tables
- `wp_crm_customers` - Main customer data
- `wp_crm_customer_staff` - Staff member data
- `wp_crm_customer_membership_levels` - Membership level definitions
- `wp_crm_customer_staff_roles` - Staff role definitions

## Technical Details

### Technology Stack
- PHP 7.4+
- WordPress 5.0+
- jQuery
- DataTables
- Modern CSS (Flexbox)

### Architecture
- MVC pattern implementation
- Namespace usage
- WordPress coding standards compliant
- Security best practices implementation

### Caching
- Built-in caching system using WordPress Transients API
- Configurable cache duration
- Group-based cache management
- Cache clearing functionality

## Security Features

- WordPress nonce implementation
- Capability checks
- Data sanitization and validation
- SQL prepared statements
- XSS prevention

## Development

### File Structure
```
customer-management/
├── assets/
│   ├── css/
│   └── js/
├── controllers/
├── includes/
├── models/
├── sqls/
│   └── tables/
└── views/
    └── admin/
```

### Key Classes
- `Customer_Controller` - Main customer operations
- `Admin_Controller` - Admin interface management
- `Settings_Controller` - Permission management
- `System_Settings_Controller` - System settings
- `Cache_Manager` - Cache operations
- `Customer_Code_Generator` - Code generation logic

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Modern web browser with JavaScript enabled

## Support

For support inquiries:
- Create an issue in the repository
- Contact: arisciwek@gmail.com

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

GPL-2.0-or-later

## Credits

Developed by arisciwek

## Changelog

### 1.0.0
- Initial release
- Basic customer management
- Staff management framework
- Permission system
- Cache management
- System settings
