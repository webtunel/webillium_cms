# Welcome To Webillium CMS

Webillium CMS is a powerful CRUD Generator for Laravel, offering essential features for rapid web application development. It's easy to use, flexible, and customizable.

## System Requirements and Technical Prerequisites
- Web Server:
  - Apache 2.4.x or higher with mod_rewrite enabled
  - Nginx 1.11.x or higher
- Database (Laravel compatible):
  - MySQL
  - PostgreSQL
  - SQLite
  - SQL Server
- Composer
- Laravel 8.* / 9.* / 10.*
- PHP 7.3 or higher with the following extensions:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - FileInfo
  - GD Library

## Installation Options

### Option 1: One-Line Installation (Recommended)

0. Make sure you have a fresh Laravel installation. Follow the [Laravel installation guide](https://laravel.com/docs/10.x/installation)

1. In your Laravel project directory, run this single command:

```bash
curl -s https://raw.githubusercontent.com/webtunel/webillium_cms/v5.6/install-webillium.sh | bash
```

### Option 2: Manual Installation

0. Ensure you have a working Laravel project.

1. Open the terminal, navigate to your Laravel project directory.

```bash
# Add the repository
composer config repositories.webilliumcms git https://github.com/webtunel/webillium_cms.git

# Install the package
composer require webtunel/webilliumcms:v5.6.x-dev
```

2. Configure your database connection in the .env file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

3. Run the installation command:

```bash
php artisan webillium:quick-install
```

## Accessing Your Admin Panel

After installation, you can access your admin panel at:

```
http://your-domain.com/admin
```

Default login credentials:
- **Email:** admin@admin.com
- **Password:** 123456

**Important:** For security reasons, change these credentials immediately after your first login.

## What's Next
- [How To Create A Module (CRUD)](./how-to-create-module.md)

## Table Of Contents
- [Back To Index](./index.md)
