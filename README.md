# 🚀 Webillium CMS - Modern Laravel Admin Generator

<p align="center">
  <img src="https://raw.githubusercontent.com/webtunel/webillium_cms/v5.6/src/assets/images/webillium_logo.png" alt="Webillium CMS Logo" width="300">
</p>

<p align="center">
  <strong>Build powerful admin panels in minutes, not months!</strong><br>
  A modern CRUD generator fully compatible with Laravel 10.x
</p>

<p align="center">
  <a href="#key-features">Features</a> •
  <a href="#quick-installation">Installation</a> •
  <a href="#documentation">Documentation</a> •
  <a href="#examples">Examples</a> •
  <a href="#support">Support</a>
</p>

## ✨ Why Webillium CMS?

Webillium CMS transforms the way you build web applications by:

- **Saving Development Time**: Create full CRUD modules in minutes instead of hours or days
- **Reducing Code**: Minimize boilerplate with powerful generators and ready-to-use components
- **Modern UI**: Beautiful, responsive admin interface built with Stisla
- **Laravel 10.x Ready**: Fully compatible with the latest Laravel versions

## 🔥 Key Features

- **Powerful CRUD Generator** - Build complete admin modules with minimal code
- **API Generator** - Create RESTful APIs with automatic documentation
- **Role-Based Access Control** - Fine-grained user permissions system
- **Multiple Field Types** - 20+ form input components for every need
- **Export/Import** - Easy data transfer to/from Excel, CSV, and PDF
- **File Manager** - Integrated media library with upload support
- **Relation Support** - Handle one-to-many, many-to-many relationships with ease
- **Responsive Design** - Mobile-friendly interface works on all devices

## 🚀 Quick Installation

### Prerequisites

- PHP 7.3 or higher
- Laravel 8.0, 9.x or 10.x
- Composer installed
- Database connection configured in your `.env` file

### One-Line Installation (Recommended)

```bash
curl -s https://raw.githubusercontent.com/webtunel/webillium_cms/v5.6/install-webillium.sh | bash
```

### Manual Installation

1. Add the repository to your composer.json:

```bash
composer config repositories.webilliumcms git https://github.com/webtunel/webillium_cms.git
```

2. Install the package:

```bash
composer require webtunel/webilliumcms:v5.6.x-dev
```

3. Run the quick installer command:

```bash
php artisan webillium:quick-install
```

### Access Your Admin Panel

Once installed, access your new admin panel at: `http://yourdomain.com/admin`

Default credentials:
- Email: `admin@admin.com`
- Password: `123456`

> 🔒 **Security Tip:** Change the default password immediately after your first login!

## 📖 Documentation

Our comprehensive documentation covers everything you need to know about using Webillium CMS:

- [Getting Started Guide](/docs/en/index.md)
- [Creating New Modules](/docs/en/how-to-create-module.md)
- [Custom Form Types](/docs/en/form-custom.md)
- [Relationship Management](/docs/en/how-to-one-to-many.md)
- [Advanced Configuration](/docs/en/how-custom-view-add.md)

## 🎯 Examples

### Create a Module in 3 Steps

1. Define your database table structure
2. Generate the module with the wizard
3. Customize your fields and relationships

```php
// Example custom controller code
public function hook_query(&$query) {
    $query->where('created_by', CRUDBooster::myId());
}

public function hook_before_add(&$data) {
    $data['created_by'] = CRUDBooster::myId();
}
```

## 💻 Available Form Types

Webillium CMS includes a wide variety of form components out of the box:

| Basic | Advanced | Special |
|-------|----------|---------|
| Text | Wysiwyg | Child Form |
| Textarea | Select2 | Datamodal |
| Select | Upload | Custom |
| Radio | Money | Google Maps |
| Checkbox | Date/Time | JSON Editor |
| Email | Number | File Manager |
| Password | Color | Multi-text |

## 🛠️ Technical Requirements

- PHP 7.3 or higher
- Laravel 8.0, 9.x or 10.x
- MySQL, PostgreSQL, SQL Server, or SQLite database
- GD Library, OpenSSL, PDO, Mbstring, Tokenizer, XML, Fileinfo PHP Extensions

## ❓ Support

Need help? We've got you covered:

- Check the [documentation](/docs/en/index.md) for detailed guides
- [Create an issue](https://github.com/webtunel/webillium_cms/issues) on GitHub
- Join our community forum for support

## 📜 License

Webillium CMS is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

## 👏 Credits

- Thanks to all contributors and supporters
- Original CRUDBooster team for the initial development
- Stisla Template for the admin interface design

---

### ⭐ Your Stars Make Us Do More ⭐

If you find this package useful, please star it on GitHub to show your support and help others discover it!