<?php

namespace webtunel\webilliumcms\helpers;


use webtunel\webilliumcms\middlewares\CBAuthAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use DB;

class CBRouter
{
    private static $cb_namespace = '\webtunel\webilliumcms\controllers';

    public static function getCBControllerFiles() {
        $controllers = glob(__DIR__.'/../controllers/*.php');
        $result = [];
        foreach ($controllers as $file) {
            $result[] = str_replace('.php', '', basename($file));
        }
        return $result;
    }

    private static function apiRoute() {
        // API Authentication
        Route::group(['middleware'=>['api'],'namespace'=>static::$cb_namespace], function() {
            Route::post("api/get-token","ApiAuthorizationController@postGetToken");
        });

        Route::group(['middleware' => ['api', CBAuthAPI::class], 'namespace' => 'App\Http\Controllers'], function () {

            $dir = scandir(base_path("app/Http/Controllers"));
            foreach ($dir as $v) {
                $v = str_replace('.php', '', $v);
                $names = array_filter(preg_split('/(?=[A-Z])/', str_replace('Controller', '', $v)));
                $names = strtolower(implode('_', $names));

                if (substr($names, 0, 4) == 'api_') {
                    $names = str_replace('api_', '', $names);
                    Route::any('api/'.$names, $v.'@execute_api');
                }
            }

        });
    }

    private static function uploadRoute() {
        Route::group(['middleware' => ['web'], 'namespace' => static::$cb_namespace], function () {
            Route::get('api-documentation', ['uses' => 'ApiCustomController@apiDocumentation', 'as' => 'apiDocumentation']);
            Route::get('download-documentation-postman', ['uses' => 'ApiCustomController@getDownloadPostman', 'as' => 'downloadDocumentationPostman']);
            Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => 'FileController@getPreview', 'as' => 'fileControllerPreview']);
        });
    }

    private static function authRoute() {
        Route::group(['middleware' => ['web'], 'prefix' => config('crudbooster.ADMIN_PATH'), 'namespace' => static::$cb_namespace], function () {
            Route::post('forgot', ['uses' => 'AdminController@postForgot', 'as' => 'postForgot']);
            Route::get('forgot', ['uses' => 'AdminController@getForgot', 'as' => 'getForgot']);
            Route::post('register', ['uses' => 'AdminController@postRegister', 'as' => 'postRegister']);
            Route::get('register', ['uses' => 'AdminController@getRegister', 'as' => 'getRegister']);
            Route::get('logout', ['uses' => 'AdminController@getLogout', 'as' => 'getLogout']);
            Route::post('login', ['uses' => 'AdminController@postLogin', 'as' => 'postLogin']);
            Route::get('login', ['uses' => 'AdminController@getLogin', 'as' => 'getLogin']);
        });
    }

    private static function userControllerRoute() {
        Route::group([
            'middleware' => ['web', \webtunel\webilliumcms\middlewares\CBBackend::class],
            'prefix' => config('crudbooster.ADMIN_PATH'),
            'namespace' => 'App\Http\Controllers',
        ], function () {

            $modules = [];
            try {
                // Todo: change table
                $modules = db('cms_moduls')
                    ->where('path', '!=', '')
                    ->where('controller', '!=', '')
                    ->whereNotNull("path")
                    ->whereNotNull("controller")
                    ->where('is_protected', 0)
                    ->where('deleted_at', null)
                    ->get();
            } catch (\Exception $e) {
                Log::error("Load cms moduls is failed. Caused = " . $e->getMessage());
            }

            foreach ($modules as $v) {
                if (@$v->path && @$v->controller) {
                    try {
                        CRUDBooster::routeController($v->path, $v->controller);
                    } catch (\Exception $e) {
                        Log::error("Path = ".$v->path."\nController = ".$v->controller."\nError = ".$e->getMessage());
                    }
                }
            }
        });
    }

    private static function cbRoute() {
        Route::group([
            'middleware' => ['web', \webtunel\webilliumcms\middlewares\CBBackend::class],
            'prefix' => config('crudbooster.ADMIN_PATH'),
            'namespace' => static::$cb_namespace,
        ], function () {

            // Todo: change table
            if (request()->is(config('crudbooster.ADMIN_PATH'))) {
                $menus = db('cms_menus')->where('is_dashboard', 1)->first();
                if ($menus) {

                } else {
                    CRUDBooster::routeController('/', 'AdminController', static::$cb_namespace);
                }
            }


            CRUDBooster::routeController('api_generator', 'ApiCustomController', static::$cb_namespace);

            Route::get('generate-models',function (){
                $protected = [
                    'cms_apicustom',
                    'cms_apikey',
                    'cms_dashboard',
                    'cms_email_queues',
                    'cms_email_templates',
                    'cms_logs',
                    'cms_menus',
                    'cms_menus_privileges',
                    'cms_moduls',
                    'cms_notifications',
                    'cms_privileges',
                    'cms_privileges_roles',
                    'cms_settings',
                    'cms_statistics',
                    'cms_statistic_components',
                    'migrations',
                    'failed_jobs',
                    'password_resets'
                ];
                $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
                foreach($tables as $table){
                    if (in_array($table, $protected)) {

                    } else {
                        if (!file_exists(app_path('Models'))) {
                            mkdir(app_path('Models'), 0777, true);
                        }
                        $modalName = ucwords(str_replace("_", " ", $table));
                        $modalName = str_replace(" ", "", $modalName);

                        if (file_exists(app_path('Models/'.$modalName.'.php'))) {
                            echo 'Exist '.app_path('Models/'.$modalName.'.php<br>');
                        } else {
                            echo 'NOT Exist '.app_path('Models/'.$modalName.'.php<br>');
                            // Create a basic model file manually instead of using krlove:generate:model
                            $modelContent = "<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass {$modalName} extends Model\n{\n    protected \$table = '{$table}';\n}\n";
                            file_put_contents(app_path('Models/'.$modalName.'.php'), $modelContent);
                            echo 'Created model: '.app_path('Models/'.$modalName.'.php<br>');
                        }
                    }
                }
                echo '<script>window.history.back()</script>';

            });
            // Todo: change table
            $modules = [];
            try {
                $modules = db('cms_moduls')->whereIn('controller', CBRouter::getCBControllerFiles())->get();
            } catch (\Exception $e) {
                Log::error("Load cms moduls is failed. Caused = " . $e->getMessage());
            }

            foreach ($modules as $v) {
                if (@$v->path && @$v->controller) {
                    try {
                        CRUDBooster::routeController($v->path, $v->controller, static::$cb_namespace);
                    } catch (\Exception $e) {
                        Log::error("Path = ".$v->path."\nController = ".$v->controller."\nError = ".$e->getMessage());
                    }
                }
            }
        });
    }

    public static function route() {

        static::apiRoute();
        static::uploadRoute();
        static::authRoute();
        static::userControllerRoute();
        static::cbRoute();
    }

}
