#!/bin/bash

# WebilliumCMS Easy Installation Script
echo "╔══════════════════════════════════════════════════════════╗"
echo "║          WebilliumCMS Installation Script                ║"
echo "╚══════════════════════════════════════════════════════════╝"

# Check if Laravel is installed
if [ ! -f "artisan" ]; then
    echo "❌ Error: Laravel does not appear to be installed in this directory"
    echo "Please run this script from the root of your Laravel project"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✓ PHP version: $PHP_VERSION"

# Check Laravel version
LARAVEL_VERSION=$(php artisan --version | grep -oP '(?<=Laravel Framework )[0-9.]+')
echo "✓ Laravel version: $LARAVEL_VERSION"

echo "Adding WebilliumCMS repository to composer.json..."
composer config repositories.webilliumcms git https://github.com/webtunel/webillium_cms.git

echo "Installing WebilliumCMS package..."
composer require webtunel/webilliumcms:v5.6.x-dev

echo "Running WebilliumCMS quick installer..."
php artisan webillium:quick-install

echo "╔══════════════════════════════════════════════════════════╗"
echo "║          WebilliumCMS Installation Complete!             ║"
echo "║                                                          ║"
echo "║  Access your admin panel at: http://localhost/admin      ║"
echo "║  Default login: admin@admin.com / 123456                 ║"
echo "╚══════════════════════════════════════════════════════════╝"