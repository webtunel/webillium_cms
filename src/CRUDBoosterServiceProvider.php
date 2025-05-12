<?php namespace webtunel\webilliumcms;

use webtunel\webilliumcms\commands\CrudboosterVersionCommand;
use webtunel\webilliumcms\commands\Mailqueues;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use webtunel\webilliumcms\commands\CrudboosterInstallationCommand;
use webtunel\webilliumcms\commands\CrudboosterUpdateCommand;
use webtunel\webilliumcms\commands\WebilliumQuickInstallCommand;
use Illuminate\Foundation\AliasLoader;
use App;

class CRUDBoosterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * Call when after all packages has been loaded
     *
     * @return void
     */

    public function boot()
    {

        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/localization','crudbooster');
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Register PostgreSQL array type mappings globally
        try {
            if (\DB::connection()->getDriverName() === 'pgsql') {
                $platform = \DB::getDoctrineSchemaManager()->getDatabasePlatform();
                if (method_exists($platform, 'registerDoctrineTypeMapping')) {
                    $platform->registerDoctrineTypeMapping('_text', 'string');
                    $platform->registerDoctrineTypeMapping('_int4', 'integer');
                    $platform->registerDoctrineTypeMapping('_numeric', 'float');
                    $platform->registerDoctrineTypeMapping('_bool', 'boolean');
                    $platform->registerDoctrineTypeMapping('_varchar', 'string');
                    $platform->registerDoctrineTypeMapping('_json', 'json');
                    $platform->registerDoctrineTypeMapping('_jsonb', 'json');
                    $platform->registerDoctrineTypeMapping('_timestamp', 'datetime');
                    $platform->registerDoctrineTypeMapping('_date', 'date');
                }
            }
        } catch (\Exception $e) {
            // Log the error but continue
            \Log::warning("Failed to register PostgreSQL array types: " . $e->getMessage());
        }

        if($this->app->runningInConsole()) {
            $this->registerSeedsFrom(__DIR__.'/database/seeds');
            $this->publishes([__DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')], 'cb_config');
            $this->publishes([__DIR__.'/userfiles/controllers/CBHook.php' => app_path('Http/Controllers/CBHook.php')], 'cb_hook');
            $this->publishes([__DIR__.'/userfiles/controllers/AdminCmsUsersController.php' => app_path('Http/Controllers/AdminCmsUsersController.php')], 'cb_user_controller');
            $this->publishes([__DIR__.'/assets'=>public_path('vendor/crudbooster')], 'cb_asset');

            // Publish all assets in one go
            $this->publishes([
                __DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php'),
                __DIR__.'/userfiles/controllers/CBHook.php' => app_path('Http/Controllers/CBHook.php'),
                __DIR__.'/userfiles/controllers/AdminCmsUsersController.php' => app_path('Http/Controllers/AdminCmsUsersController.php'),
                __DIR__.'/assets'=>public_path('vendor/crudbooster')
            ], 'cb_all');
        }

        $this->customValidation();
    }

    /**
     * Register the application services.
     * Call when this package is first time loaded
     *
     * @return void
     */
    public function register()
    {                                   
        require __DIR__.'/helpers/Helper.php';      

        $this->mergeConfigFrom(__DIR__.'/configs/crudbooster.php','crudbooster');

        $this->registerSingleton();

        if($this->app->runningInConsole()) {
            $this->commands('crudboosterinstall');
            $this->commands('crudboosterupdate');
            $this->commands('crudboosterVersionCommand');
            $this->commands('crudboosterMailQueue');
            $this->commands('webilliumQuickInstall');
        }

        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', \Barryvdh\DomPDF\Facade\Pdf::class);
        $loader->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
        $loader->alias('Image', \Intervention\Image\Facades\Image::class);
        $loader->alias('CRUDBooster', \webtunel\webilliumcms\helpers\CRUDBooster::class);
        $loader->alias('CB', \webtunel\webilliumcms\helpers\CB::class);
    }
   
    private function registerSingleton()
    {
        $this->app->singleton('crudbooster', function ()
        {
            return true;
        });

        $this->app->singleton('crudboosterinstall',function() {
            return new CrudboosterInstallationCommand;
        });
        
        $this->app->singleton('crudboosterupdate',function() {
            return new CrudboosterUpdateCommand;
        });

        $this->app->singleton("crudboosterVersionCommand", function() {
            return new CrudboosterVersionCommand;
        });

        $this->app->singleton("crudboosterMailQueue", function() {
            return new Mailqueues;
        });

        $this->app->singleton("webilliumQuickInstall", function() {
            return new WebilliumQuickInstallCommand;
        });
    }

    protected function registerSeedsFrom($path)
    {
        foreach (glob("$path/*.php") as $filename)
        {
            include $filename;
            $classes = get_declared_classes();
            $class = end($classes);

            $command = request()->server('argv', null);
            if (is_array($command)) {
                $command = implode(' ', $command);
                if ($command == "artisan db:seed") {
                    Artisan::call('db:seed', ['--class' => $class]);
                }
            }

        }
    }

    private function customValidation() {
        Validator::extend('alpha_spaces', function ($attribute, $value) {
            // This will only accept alpha and spaces.
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s]+$/u', $value);
        },'The :attribute should be letters and spaces only');

        Validator::extend('alpha_num_spaces', function ($attribute, $value) {
            // This will only accept alphanumeric and spaces.
            return preg_match('/^[a-zA-Z0-9\s]+$/', $value);
        },'The :attribute should be alphanumeric characters and spaces only');
    }
}
