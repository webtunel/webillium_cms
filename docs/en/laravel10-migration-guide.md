# Laravel 10.x Migration Guide

This guide explains the changes made to Webillium CMS to ensure compatibility with Laravel 10.x. Whether you're upgrading an existing application or starting fresh, this document outlines the key modifications and considerations.

## Key Updates

WebilliumCMS has been updated to work with Laravel 10.x through the following improvements:

1. **Namespace Changes**
   - Package renamed from `crocodicstudio/crudbooster` to `webtunel/webilliumcms`
   - All namespaces updated from `crocodicstudio\crudbooster\*` to `webtunel\webilliumcms\*`

2. **Facade Updates**
   - Updated all facade usage to use proper imports
   - Added `use Illuminate\Support\Facades\*` for all Laravel facades 
   - Replaced direct facade access with the proper namespaced versions

3. **String Helper Functions**
   - Replaced deprecated `str_random()` with `Str::random()`
   - Replaced `str_slug()` with `Str::slug()`
   - Added `use Illuminate\Support\Str` where required

4. **Route Definitions**
   - Updated route registration syntax for Laravel 10.x
   - Fixed controller action references to use array notation: `[$controller, 'method']`
   - Updated middleware references to use `::class` notation

5. **Service Provider Updates**
   - Updated service provider registration in `config/app.php`
   - Fixed asset publishing methods

## Upgrading from Previous Versions

If you're upgrading from a previous version of CRUDBooster, follow these steps:

1. **Update composer.json**

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

2. **Update the Service Provider**

   In `config/app.php`, change:

   ```php
   // From
   crocodicstudio\crudbooster\CRUDBoosterServiceProvider::class,

   // To
   webtunel\webilliumcms\CRUDBoosterServiceProvider::class,
   ```

3. **Update Any Custom Controllers**

   If you've created custom controllers that extend CRUDBooster:

   ```php
   // From
   use crocodicstudio\crudbooster\controllers\CBController;

   // To
   use webtunel\webilliumcms\controllers\CBController;
   ```

4. **Update Helper References**

   If you use the CRUDBooster or CB helpers:

   ```php
   // From
   use crocodicstudio\crudbooster\helpers\CRUDBooster;
   use crocodicstudio\crudbooster\helpers\CB;

   // To
   use webtunel\webilliumcms\helpers\CRUDBooster;
   use webtunel\webilliumcms\helpers\CB;
   ```

5. **Run the Install/Update Command**

   ```bash
   php artisan webillium:quick-install
   ```

## Compatibility Concerns

While we've made significant efforts to ensure compatibility with Laravel 10.x, be aware of these potential issues:

1. **Session Handling**: Laravel's session handling has changed. If you encounter session-related errors, you may need to update session storage configurations.

2. **Mail Configuration**: The mail configuration syntax has changed in newer Laravel versions. Update your email templates accordingly.

3. **Database Schema Builder**: Some schema builder methods have changed. If you use schema-related operations in custom code, ensure they're compatible.

4. **Custom Extensions**: If you've built custom modules or extensions, they may need adjustments to work with the updated package.

## Best Practices for Laravel 10.x

When using Webillium CMS with Laravel 10.x:

1. Use proper namespaced imports instead of facade aliases
2. Use typed properties when possible (PHP 7.4+)
3. Take advantage of Laravel 10's route improvements
4. Use Laravel's dependency injection features whenever possible
5. Follow PSR-12 coding standards

## Troubleshooting

If you encounter issues after upgrading:

1. **Clear All Caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   php artisan route:clear
   ```

2. **Check Logs**
   Laravel logs are located at `storage/logs/laravel.log`

3. **Verify Dependencies**
   Ensure all dependencies are updated to versions compatible with Laravel 10.x

4. **Common Errors**
   - "Class not found" errors are typically related to namespaces
   - Method not found errors may indicate you're using deprecated functions
   - Controller action errors usually relate to route definition changes

## Need More Help?

If you encounter persistent issues, please file a bug report on our [GitHub repository](https://github.com/webtunel/webillium_cms/issues) with detailed information about your environment and the steps to reproduce the problem.