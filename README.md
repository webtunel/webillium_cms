# :rocket: Webillium CMS - Laravel CRUD Generator (New)

> Laravel CRUD Generator, Make a Web Application Just In Minutes, Even With Less Code and fewer Steps!

# About Webillium CMS

Webillium CMS is a powerful CRUD generator based on Crudbooster with the modern Stisla admin template. This package has been updated to be fully compatible with newer Laravel versions (10.x). It provides an easy-to-use administration panel generator that allows you to rapidly build backend systems with minimal coding.

## Installation Guide

This guide will walk you through setting up Webillium CMS in your Laravel project.

### Prerequisites

Before installing Webillium CMS, make sure your environment meets these requirements:

- PHP 7.3 or higher
- Laravel 8.0 or higher
- Composer installed
- Database connection configured in your `.env` file

### Option 1: One-Line Installation (Recommended for New Projects)

This is the fastest way to install Webillium CMS in an existing Laravel project:

1. Open your terminal in the root directory of your Laravel project
2. Run the following command to download and execute the installation script:

```bash
curl -s https://raw.githubusercontent.com/webtunel/webillium_cms/v5.6/install-webillium.sh | bash
```

This script will:
- Verify your PHP and Laravel versions
- Add the Webillium CMS repository to your composer.json
- Install the package and its dependencies
- Run migrations and create necessary database tables
- Set up a default admin user

### Option 2: Step-by-Step Quick Installation

If you prefer to run commands individually, follow these steps:

1. Add the Webillium CMS repository to your composer.json:

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

The quick installer will:
- Check your system requirements
- Publish all necessary assets and configuration files
- Run database migrations
- Create a default admin user
- Display login credentials when complete

### Option 3: Manual Installation

For advanced users who want full control over the installation process:

1. Manually add the repository and requirement to your composer.json file:

```json
"require": {
    "webtunel/webilliumcms": "v5.6.x-dev"
},
"repositories": [
    {
        "url": "https://github.com/webtunel/webillium_cms.git",
        "type": "git"
    }
]
```

2. Update your dependencies:

```bash
composer update --prefer-source
```

3. Run the installation command:

```bash
php artisan webillium:install
```

4. If needed, manually publish assets:

```bash
php artisan vendor:publish --provider="webtunel\webilliumcms\CRUDBoosterServiceProvider" --tag=cb_all
```

5. Run migrations and seed the database:

```bash
php artisan migrate
php artisan db:seed --class=CBSeeder
```

### Post-Installation

After successfully installing Webillium CMS:

1. Access your admin panel at: `http://yourdomain.com/admin`
2. Log in with the default credentials:
   - Email: admin@admin.com
   - Password: 123456
3. Change the default password immediately for security

### Troubleshooting

If you encounter issues during installation:

- Make sure your database connection is properly configured in your `.env` file
- Check if your server meets all the system requirements
- Ensure all composer dependencies are properly installed
- Clear configuration cache with `php artisan config:clear`
- Check Laravel log files for specific errors

## Documentation

For full usage documentation, please refer to our [documentation guide](/docs/en/index.md).

The documentation covers:
- Creating new modules
- Customizing forms and views
- Adding new field types
- User management
- Role-based access control
- And much more!

## Features

### Core Features
- Powerful CRUD generator with minimal coding required
- Modern responsive admin template (Stisla)
- Role-based access control
- API Generator
- Export/Import data
- Multiple database support
- Form validation
- File manager
- Notification system

### Form Types
Webillium CMS supports various form input types to meet all your needs:
- Text, Textarea, Select, Radio, Checkbox
- Date, DateTime, Time
- Email, Password, Money, Number
- Wysiwyg editor, Upload, Select2
- File Manager, Google Maps integration
- JSON editor
- Child form (nested forms)
- Many-to-many relationships

## Updates for Laravel 10.x

This fork of CRUDBooster includes several improvements to support newer Laravel versions:

1. Updated facade references to use the new structure (Facade namespace changes)
2. Replaced deprecated `str_random()` with `Str::random()`
3. Updated middleware registration to use `::class` syntax for better IDE support
4. Fixed controller action references using array syntax for Laravel 10.x compatibility
5. Updated model generation path for modern Laravel applications
6. Renamed the package from "crudbooster" to "webilliumcms"
7. Added quick installation options for easier setup
8. Updated dependency requirements in composer.json

## Technical Requirements

Webillium CMS requires:
- PHP 7.3 or higher
- Laravel 8.0, 9.x or 10.x
- MySQL, PostgreSQL, SQL Server, or SQLite database
- GD Library, OpenSSL, PDO, Mbstring, Tokenizer, XML, Fileinfo PHP Extensions

## Credits

1. Thanks to all contributors and funders
2. Original CRUDBooster team for the initial development
3. Stisla Template for the admin interface design

## License

Webillium CMS is under [MIT License](https://opensource.org/licenses/MIT)

### :star: Your Stars Make Us Do More :star:

As always if you found this package useful and you want to encourage us to maintain and work on it. Just press the star button to declare your willing.
