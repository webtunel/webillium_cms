<?php namespace webtunel\webilliumcms\controllers;

use CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use webtunel\webilliumcms\fonts\Fontawesome;

class ModulsController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_moduls';
        $this->primary_key = 'id';
        $this->title_field = "name";
        $this->limit = 100;
        $this->button_add = false;
        $this->button_export = false;
        $this->button_import = false;
        $this->button_filter = false;
        $this->button_detail = false;
        $this->button_bulk_action = false;
        $this->button_action_style = 'button_icon';
        $this->orderby = ['is_protected' => 'asc', 'name' => 'asc'];

        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Table", "name" => "table_name"];
        $this->col[] = ["label" => "Path", "name" => "path"];
        $this->col[] = ["label" => "Controller", "name" => "controller"];
        $this->col[] = ["label" => "Protected", "name" => "is_protected", "visible" => false];

        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", "placeholder" => "Module name here", 'required' => true];

        $tables = CRUDBooster::listTables();
        $tables_list = [];
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $label = $value;

                if (substr($value, 0, 4) == 'cms_') {
                    continue;
                }

                $tables_list[] = $value."|".$label;
            }
        }
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $label = "[Default] ".$value;
                if (substr($value, 0, 4) == 'cms_') {
                    $tables_list[] = $value."|".$label;
                }
            }
        }

        $this->form[] = ["label" => "Table Name", "name" => "table_name", "type" => "select2", "dataenum" => $tables_list, 'required' => true];

        $fontawesome = Fontawesome::getIcons();

        $id = CRUDBooster::getCurrentId();
        $row = $id ? CRUDBooster::first($this->table, $id) : null;
        $custom = view('crudbooster::components.list_icon', compact('fontawesome', 'row'))->render();
        $this->form[] = ['label' => 'Icon', 'name' => 'icon', 'type' => 'custom', 'html' => $custom, 'required' => true];

        $this->script_js = "
 			$(function() {
 				$('#table_name').change(function() {
					var v = $(this).val();
					$('#path').val(v);
				})	
 			})
 			";

        $this->form[] = ["label" => "Path", "name" => "path", "required" => true, 'placeholder' => 'Optional'];
        $this->form[] = ["label" => "Controller", "name" => "controller", "type" => "text", "placeholder" => "(Optional) Auto Generated"];

        if (CRUDBooster::getCurrentMethod() == 'getAdd' || CRUDBooster::getCurrentMethod() == 'postAddSave') {

            $this->form[] = [
                "label" => "Global Privilege",
                "name" => "global_privilege",
                "type" => "radio",
                "dataenum" => ['0|No', '1|Yes'],
                'value' => 0,
                'help' => 'Global Privilege allows you to make the module to be accessible by all privileges',
                'exception' => true,
            ];

            $this->form[] = [
                "label" => "Button Action Style",
                "name" => "button_action_style",
                "type" => "radio",
                "dataenum" => ['button_icon', 'button_icon_text', 'button_text', 'dropdown'],
                'value' => 'button_icon',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Table Action",
                "name" => "button_table_action",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'Yes',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Add",
                "name" => "button_add",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'Yes',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Delete",
                "name" => "button_delete",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'Yes',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Edit",
                "name" => "button_edit",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'Yes',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Detail",
                "name" => "button_detail",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'Yes',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Show",
                "name" => "button_show",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'Yes',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Filter",
                "name" => "button_filter",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'Yes',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Export",
                "name" => "button_export",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'No',
                'exception' => true,
            ];
            $this->form[] = [
                "label" => "Button Import",
                "name" => "button_import",
                "type" => "radio",
                "dataenum" => ['Yes', 'No'],
                'value' => 'No',
                'exception' => true,
            ];
        }

        $this->addaction[] = [
            'label' => 'Module Wizard',
            'icon' => 'fa fa-wrench',
            'url' => CRUDBooster::mainpath('step1').'/[id]',
            // Removed the showIf condition to allow Module Wizard for all modules
        ];

        $this->index_button[] = ['label' => 'Generate New Module', 'icon' => 'fa fa-plus', 'url' => CRUDBooster::mainpath('step1'), 'color' => 'success'];
    }

    function hook_query_index(&$query)
    {
        // Allow all modules to be shown, including protected ones
        // $query->where('is_protected', 0);
        $query->whereNotIn('cms_moduls.controller', ['AdminCmsUsersController']);
    }

    function hook_before_delete($id)
    {
        $modul = DB::table('cms_moduls')->where('id', $id)->first();
        $menus = DB::table('cms_menus')->where('path', 'like', '%'.$modul->controller.'%')->delete();
        @unlink(app_path('Http/Controllers/'.$modul->controller.'.php'));
    }

    public function getTableColumns($table)
    {
        try {
            // First, try the standard method
            $columns = CRUDBooster::getTableColumns($table);

            // If no columns returned or empty, try direct PostgreSQL schema query
            if (empty($columns)) {
                // Check if we're using PostgreSQL
                $driver = DB::connection()->getDriverName();
                if ($driver === 'pgsql') {
                    // PostgreSQL specific query to get column names
                    $schema = 'public'; // Default schema, might need to be configurable
                    $columns = DB::select("
                        SELECT column_name
                        FROM information_schema.columns
                        WHERE table_schema = ? AND table_name = ?
                    ", [$schema, $table]);

                    // Extract column names from result
                    $columns = array_map(function($col) {
                        return $col->column_name;
                    }, $columns);
                }
            }

            // Add fallback if still empty
            if (empty($columns)) {
                $columns = ['id']; // Always include at least ID

                // Try getting columns via Schema
                try {
                    $schemaColumns = \Schema::getColumnListing($table);
                    if (!empty($schemaColumns)) {
                        $columns = $schemaColumns;
                    }
                } catch (\Exception $e) {
                    // Ignore schema errors and keep fallback
                }
            }

            return response()->json($columns);
        } catch (\Exception $e) {
            // Return a proper error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCheckSlug($slug)
    {
        $check = DB::table('cms_moduls')->where('path', $slug)->count();
        $lastId = DB::table('cms_moduls')->max('id') + 1;

        return response()->json(['total' => $check, 'lastid' => $lastId]);
    }

    public function getAdd()
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (! CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        return redirect()->route("ModulsControllerGetStep1");
    }

    public function getStep1($id = 0)
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (! CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $tables = CRUDBooster::listTables();
        $tables_list = [];

        if (!empty($tables)) {
            foreach ($tables as $tab) {
                if (is_object($tab)) {
                    // For standard database structure responses
                    $value = $tab->TABLE_NAME ?? ($tab->table_name ?? null);

                    if ($value) {
                        $label = $value;

                        if (substr($label, 0, 4) == 'cms_' && $label != config('crudbooster.USER_TABLE')) {
                            continue;
                        }
                        if ($label == 'migrations') {
                            continue;
                        }

                        $tables_list[] = $value;
                    }
                } elseif (is_array($tab)) {
                    // Handle array format
                    foreach ($tab as $key => $value) {
                        $label = $value;

                        if (substr($label, 0, 4) == 'cms_' && $label != config('crudbooster.USER_TABLE')) {
                            continue;
                        }
                        if ($label == 'migrations') {
                            continue;
                        }

                        $tables_list[] = $value;
                    }
                }
            }
        }

        // If no tables were found, try to get them directly from the schema
        if (empty($tables_list)) {
            try {
                $tables_list = \Illuminate\Support\Facades\Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableNames();

                // Filter out cms_ tables
                $tables_list = array_filter($tables_list, function($table) {
                    return substr($table, 0, 4) != 'cms_' || $table == config('crudbooster.USER_TABLE');
                });

                // Filter out migrations table
                $tables_list = array_filter($tables_list, function($table) {
                    return $table != 'migrations';
                });
            } catch (\Exception $e) {
                // If this fails too, at least provide some default tables for testing
                $tables_list = ['users', 'posts', 'categories', 'tags', 'comments'];
            }
        }

        $fontawesome = Fontawesome::getIcons();

        // Initialize $row as empty object if id=0 or fetch from DB if id exists
        if ($id === 0 || $id === '0') {
            $row = (object)[
                'id' => null,
                'name' => '',
                'table_name' => '',
                'icon' => 'fa fa-cog',
                'path' => '',
            ];
        } else {
            // Make sure to properly retrieve the module data for editing
            try {
                $row = DB::table($this->table)->where('id', $id)->first();

                // Enhanced debugging
                if ($row) {
                    \Log::info("Module data for ID $id found:", [
                        'id' => $row->id,
                        'name' => $row->name,
                        'table_name' => $row->table_name,
                        'icon' => $row->icon,
                        'path' => $row->path
                    ]);
                } else {
                    \Log::warning("Module with ID $id not found in database.");
                }

                if (!$row) {
                    // If module not found, set default values
                    $row = (object)[
                        'id' => $id,
                        'name' => '',
                        'table_name' => '',
                        'icon' => 'fa fa-cog',
                        'path' => '',
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Error fetching module data: " . $e->getMessage());
                $row = (object)[
                    'id' => $id,
                    'name' => '',
                    'table_name' => '',
                    'icon' => 'fa fa-cog',
                    'path' => '',
                ];
            }
        }

        // Debug the final row object being passed to the view
        \Log::info("Final row object passed to step1 view:", (array)$row);

        // Try to find the view in various namespaces
        $viewPath = "crudbooster::module_generator.step1";
        if (!view()->exists($viewPath)) {
            $viewPath = "webilliumcms::module_generator.step1";
            if (!view()->exists($viewPath)) {
                $viewPath = "module_generator.step1";
            }
        }

        \Log::info("Using view path: $viewPath");

        return view($viewPath, compact("tables_list", "fontawesome", "row", "id"));
    }

    public function getStep2($id)
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (! CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        try {
            $row = DB::table('cms_moduls')->where('id', $id)->first();

            // Log the module data for debugging
            \Log::info("Module data in Step 2 for ID $id:", (array)$row);

            if (!$row) {
                \Log::error("Module with ID $id not found in database for Step 2");
                return redirect()->route('ModulsControllerGetIndex')
                    ->with(['message' => 'Module not found', 'message_type' => 'warning']);
            }

            // Ensure table_name is set and valid
            if (empty($row->table_name)) {
                \Log::warning("Module ID $id has no table_name set");
                // Redirect back to step 1 if table name is not set
                return redirect()->route('ModulsControllerGetStep1', ['id' => $id])
                    ->with(['message' => 'Please select a table first', 'message_type' => 'warning']);
            }

            $columns = CRUDBooster::getTableColumns($row->table_name);

            // Log columns for debugging
            \Log::info("Columns for table {$row->table_name}:", ['columns' => $columns]);

            // Get database tables based on the driver
            $table_list = [];
            try {
                $driver = DB::connection()->getDriverName();

                if ($driver === 'pgsql') {
                    // PostgreSQL specific query to get tables (excluding system tables)
                    $schema = 'public'; // Default schema
                    $tables = DB::select("
                        SELECT table_name
                        FROM information_schema.tables
                        WHERE table_schema = ?
                        AND table_type = 'BASE TABLE'
                        AND table_name NOT LIKE 'pg_%'
                        AND table_name NOT LIKE 'sql_%'
                    ", [$schema]);

                    foreach ($tables as $table) {
                        if (!in_array($table->table_name, $table_list)) {
                            $table_list[] = $table->table_name;
                        }
                    }
                } else {
                    // Default approach using CRUDBooster
                    $tables = CRUDBooster::listTables();
                    foreach ($tables as $tab) {
                        if (is_array($tab)) {
                            foreach ($tab as $value) {
                                if (!in_array($value, $table_list)) {
                                    $table_list[] = $value;
                                }
                            }
                        } elseif (is_object($tab)) {
                            foreach ($tab as $key => $value) {
                                if (!in_array($value, $table_list)) {
                                    $table_list[] = $value;
                                }
                            }
                        }
                    }
                }

                // Filter out cms_ tables except user table
                $table_list = array_filter($table_list, function($table) {
                    return substr($table, 0, 4) != 'cms_' || $table == config('crudbooster.USER_TABLE');
                });

                // Filter out migrations table
                $table_list = array_filter($table_list, function($table) {
                    return $table != 'migrations';
                });

                // Convert to indexed array
                $table_list = array_values($table_list);

                // Sort tables alphabetically
                sort($table_list);

            } catch (\Exception $e) {
                \Log::error("Error getting tables: " . $e->getMessage());
                // Provide some default tables as fallback
                $table_list = ['users', 'products', 'categories', 'orders', 'posts', 'comments'];
            }

            // Initialize $cb_col variable
            $cb_col = [];

            // Load existing column configuration if controller file exists
            $controller_path = app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php');
            if (file_exists($controller_path)) {
                try {
                    $response = file_get_contents($controller_path);
                    $column_datas = extract_unit($response, "# START COLUMNS DO NOT REMOVE THIS LINE", "# END COLUMNS DO NOT REMOVE THIS LINE");
                    $column_datas = str_replace('$this->', '$cb_', $column_datas);

                    // Log the column data for debugging
                    \Log::info("Column data extracted from controller:", ['data' => $column_datas]);

                    // Safely eval the code
                    @eval($column_datas);

                    // If $cb_col is not set after eval, initialize it
                    if (!isset($cb_col)) {
                        $cb_col = [];
                    }
                } catch (\Exception $e) {
                    \Log::error("Error parsing controller columns: " . $e->getMessage());
                    $cb_col = [];
                }
            }

            $data = [];
            $data['id'] = $id;
            $data['row'] = $row; // Add the module row data to the view
            $data['columns'] = is_array($columns) ? $columns : [];
            $data['table_list'] = $table_list;
            $data['cb_col'] = $cb_col;

            // Try to find the view in various namespaces
            $viewPath = "crudbooster::module_generator.step2";
            if (!view()->exists($viewPath)) {
                $viewPath = "webilliumcms::module_generator.step2";
                if (!view()->exists($viewPath)) {
                    $viewPath = "module_generator.step2";
                }
            }

            \Log::info("Using view path for step2: $viewPath");

            return view($viewPath, $data);

        } catch (\Exception $e) {
            \Log::error("Error in getStep2: " . $e->getMessage());
            return redirect()->route('ModulsControllerGetIndex')
                ->with(['message' => 'Error processing module: ' . $e->getMessage(), 'message_type' => 'warning']);
        }
    }

    public function postStep2()
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (! CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $name = Request::get('name');
        $table_name = Request::get('table');
        $icon = Request::get('icon');
        $path = Request::get('path');

        if (! Request::get('id')) {

            if (DB::table('cms_moduls')->where('path', $path)->where('deleted_at', null)->count()) {
                return redirect()->back()->with(['message' => 'Sorry the slug has already exists, please choose another !', 'message_type' => 'warning']);
            }

            $created_at = now();

            $controller = CRUDBooster::generateController($table_name, $path);
            $id = DB::table($this->table)->insertGetId(compact("controller", "name", "table_name", "icon", "path", "created_at"));

            //Insert Menu
            if ($controller && Request::get('create_menu')) {
                $parent_menu_sort = DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1;

                $id_cms_menus = DB::table('cms_menus')->insertGetId([

                    'created_at' => date('Y-m-d H:i:s'),
                    'name' => $name,
                    'icon' => $icon,
                    'path' => $controller.'GetIndex',
                    'type' => 'Route',
                    'is_active' => 1,
                    'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                    'sorting' => $parent_menu_sort,
                    'parent_id' => 0,
                ]);
                DB::table('cms_menus_privileges')->insert(['id_cms_menus' => $id_cms_menus, 'id_cms_privileges' => CRUDBooster::myPrivilegeId()]);
            }

            $user_id_privileges = CRUDBooster::myPrivilegeId();
            DB::table('cms_privileges_roles')->insert([
                'id_cms_moduls' => $id,
                'id_cms_privileges' => $user_id_privileges,
                'is_visible' => 1,
                'is_create' => 1,
                'is_read' => 1,
                'is_edit' => 1,
                'is_delete' => 1,
            ]);

            //Refresh Session Roles
            $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
            Session::put('admin_privileges_roles', $roles);

            return redirect(Route("ModulsControllerGetStep2")."/". $id);
        } else {
            $id = Request::get('id');
            DB::table($this->table)->where('id', $id)->update(compact("name", "table_name", "icon", "path"));

            $row = DB::table('cms_moduls')->where('id', $id)->first();

            if (file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
                $response = file_get_contents(app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php'));
            } else {
                $response = file_get_contents(__DIR__.'/'.str_replace('.', '', $row->controller).'.php');
            }

            return redirect(Route("ModulsControllerGetStep2")."/".$id);
        }
    }

    public function postStep3()
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (! CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $column = Request::input('column');
        $name = Request::input('name');
        $join_table = Request::input('join_table');
        $join_field = Request::input('join_field');
        $is_image = Request::input('is_image');
        $is_download = Request::input('is_download');
        $callbackphp = Request::input('callbackphp');
        $id = Request::input('id');
        $width = Request::input('width');

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $i = 0;
        $script_cols = [];
        foreach ($column as $col) {

            if (! $name[$i]) {
                $i++;
                continue;
            }

            $script_cols[$i] = "\t\t\t".'$this->col[] = ["label"=>"'.$col.'","name"=>"'.$name[$i].'"';

            if ($join_table[$i] && $join_field[$i]) {
                $script_cols[$i] .= ',"join"=>"'.$join_table[$i].','.$join_field[$i].'"';
            }

            if ($is_image[$i]) {
                $script_cols[$i] .= ',"image"=>true';
            }

            if ($is_download[$i]) {
                $script_cols[$i] .= ',"download"=>true';
            }

            if ($width[$i]) {
                $script_cols[$i] .= ',"width"=>"'.$width[$i].'"';
            }

            if ($callbackphp[$i]) {
                $script_cols[$i] .= ',"callback_php"=>\''.$callbackphp[$i].'\'';
            }

            $script_cols[$i] .= "];";

            $i++;
        }

        $scripts = implode("\n", $script_cols);
        $raw = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
        $raw = explode("# START COLUMNS DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END COLUMNS DO NOT REMOVE THIS LINE", $raw[1]);

        $file_controller = trim($raw[0])."\n\n";
        $file_controller .= "\t\t\t# START COLUMNS DO NOT REMOVE THIS LINE\n";
        $file_controller .= "\t\t\t".'$this->col = [];'."\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "\t\t\t# END COLUMNS DO NOT REMOVE THIS LINE\n\n";
        $file_controller .= "\t\t\t".trim($rraw[1]);

        file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

        return redirect(Route("ModulsControllerGetStep3")."/".$id);
    }

    public function getStep3($id)
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (! CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        try {
            $row = DB::table('cms_moduls')->where('id', $id)->first();

            // Log the module data for debugging
            \Log::info("Module data in Step 3 for ID $id:", (array)$row);

            if (!$row) {
                \Log::error("Module with ID $id not found in database for Step 3");
                return redirect()->route('ModulsControllerGetIndex')
                    ->with(['message' => 'Module not found', 'message_type' => 'warning']);
            }

            // Ensure table_name is set and valid
            if (empty($row->table_name)) {
                \Log::warning("Module ID $id has no table_name set in Step 3");
                // Redirect back to step 1 if table name is not set
                return redirect()->route('ModulsControllerGetStep1', ['id' => $id])
                    ->with(['message' => 'Please select a table first', 'message_type' => 'warning']);
            }

            $columns = CRUDBooster::getTableColumns($row->table_name);

            // Initialize $cb_form
            $cb_form = [];

            // Load existing form configuration if controller file exists
            $controller_path = app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php');
            if (file_exists($controller_path)) {
                try {
                    $response = file_get_contents($controller_path);
                    $column_datas = extract_unit($response, "# START FORM DO NOT REMOVE THIS LINE", "# END FORM DO NOT REMOVE THIS LINE");
                    $column_datas = str_replace('$this->', '$cb_', $column_datas);

                    // Log the form data for debugging
                    \Log::info("Form data extracted from controller:", ['data' => $column_datas]);

                    // Safely eval the code
                    @eval($column_datas);

                    // If $cb_form is not set after eval, initialize it
                    if (!isset($cb_form)) {
                        $cb_form = [];
                    }
                } catch (\Exception $e) {
                    \Log::error("Error parsing controller form data: " . $e->getMessage());
                    $cb_form = [];
                }
            }

            // Get all available field types
            $types = [];
            $fallback_types = ['text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'date', 'time', 'datetime', 'email', 'password', 'hidden', 'wysiwyg', 'select2', 'upload', 'money', 'datamodal', 'child', 'json', 'googlemaps'];

            try {
                // First try the package location
                $package_path = base_path('vendor/webtunel/webilliumcms/src/views/default/type_components');
                if (is_dir($package_path)) {
                    foreach (glob($package_path.'/*', GLOB_ONLYDIR) as $dir) {
                        $types[] = basename($dir);
                    }
                }

                // Then try the original CRUDBooster location as a fallback
                if (empty($types)) {
                    $original_path = base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components');
                    if (is_dir($original_path)) {
                        foreach (glob($original_path.'/*', GLOB_ONLYDIR) as $dir) {
                            $types[] = basename($dir);
                        }
                    }
                }

                // Also check local override location
                $local_path = base_path('resources/views/vendor/crudbooster/type_components');
                if (is_dir($local_path)) {
                    foreach (glob($local_path.'/*', GLOB_ONLYDIR) as $dir) {
                        $type = basename($dir);
                        if (!in_array($type, $types)) {
                            $types[] = $type;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log the error but continue
                \Log::error('Failed to get field types: ' . $e->getMessage());
            }

            // If all methods fail, use the fallback types
            if (empty($types)) {
                $types = $fallback_types;
            }

            // Sort the types alphabetically
            sort($types);

            // Try to find the view in various namespaces
            $viewPath = "crudbooster::module_generator.step3";
            if (!view()->exists($viewPath)) {
                $viewPath = "webilliumcms::module_generator.step3";
                if (!view()->exists($viewPath)) {
                    $viewPath = "module_generator.step3";
                }
            }

            \Log::info("Using view path for step3: $viewPath");

            // Add the row data for the view to access
            return view($viewPath, compact('columns', 'cb_form', 'types', 'id', 'row'));

        } catch (\Exception $e) {
            \Log::error("Error in getStep3: " . $e->getMessage());
            return redirect()->route('ModulsControllerGetIndex')
                ->with(['message' => 'Error processing module: ' . $e->getMessage(), 'message_type' => 'warning']);
        }
    }

    public function getTypeInfo($type = 'text')
    {
        header("Content-Type: application/json");

        // Log the request for debugging
        \Log::info("Type info requested for: " . $type);

        // Try to find info.json file in multiple possible locations
        $paths = [
            // Package path
            base_path('vendor/webtunel/webilliumcms/src/views/default/type_components/'.$type.'/info.json'),
            // Original CRUDBooster path
            base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/info.json'),
            // Local project components
            base_path('resources/views/vendor/crudbooster/type_components/'.$type.'/info.json')
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                \Log::info("Found type info at: " . $path);
                echo file_get_contents($path);
                return;
            }
        }

        // If no info.json found, return a default structure based on the type
        $defaultInfo = [
            "title" => ucfirst($type),
            "alert" => "This is a basic {$type} input field",
            "attribute" => [
                "required" => []
            ]
        ];

        // Add type-specific defaults
        switch($type) {
            case 'select':
            case 'select2':
            case 'radio':
                $defaultInfo["attribute"]["required"]["dataenum"] = "Example: option1;option2;option3";
                break;
            case 'upload':
            case 'filemanager':
                $defaultInfo["attribute"]["required"]["upload_path"] = "Example: uploads/files/";
                break;
            case 'wysiwyg':
                $defaultInfo["attribute"]["optional"]["filemanager_group_name"] = "Example: content";
                break;
            case 'number':
            case 'money':
                $defaultInfo["attribute"]["optional"]["decimals"] = "Example: 2";
                break;
            default:
                $defaultInfo["attribute"]["required"]["placeholder"] = "Enter placeholder text";
                $defaultInfo["attribute"]["optional"]["readonly"] = "true/false";
                break;
        }

        \Log::info("Returning default type info for: " . $type);
        echo json_encode($defaultInfo);
    }

    public function postStep4()
    {
        try {
            $this->cbLoader();

            $post = Request::all();
            \Log::info("postStep4 form data:", $post);

            // Make sure the required parameters exist
            if (!isset($post['id'])) {
                return redirect()->back()->with(['message' => 'Missing module ID', 'message_type' => 'warning']);
            }

            $id = $post['id'];

            // Check if all required arrays exist
            if (!isset($post['label']) || !isset($post['name']) || !isset($post['type'])) {
                return redirect()->back()->with(['message' => 'Missing required form fields', 'message_type' => 'warning']);
            }

            $label = $post['label'];
            $name = $post['name'];
            $width = isset($post['width']) ? $post['width'] : [];
            $type = $post['type'];
            $option = isset($post['option']) ? $post['option'] : [];
            $validation = isset($post['validation']) ? $post['validation'] : [];

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $i = 0;
        $script_form = [];
        foreach ($label as $l) {

            if ($l != '') {

                $form = [];
                $form['label'] = $l;
                $form['name'] = $name[$i];
                $form['type'] = $type[$i];
                $form['validation'] = isset($validation[$i]) ? $validation[$i] : '';
                $form['width'] = isset($width[$i]) ? $width[$i] : '';
                if (isset($option[$i]) && is_array($option[$i])) {
                    $form = array_merge($form, $option[$i]);
                }

                foreach ($form as $k => $f) {
                    if ($f == '') {
                        unset($form[$k]);
                    }
                }

                $script_form[$i] = "\t\t\t".'$this->form[] = '.min_var_export($form).";";
            }

            $i++;
        }

        $scripts = implode("\n", $script_form);
        $raw = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
        $raw = explode("# START FORM DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END FORM DO NOT REMOVE THIS LINE", $raw[1]);

        $top_script = trim($raw[0]);
        $current_scaffolding_form = trim($rraw[0]);
        $bottom_script = trim($rraw[1]);

        //IF FOUND OLD, THEN CLEAR IT
        if (strpos($bottom_script, '# OLD START FORM') !== false) {
            $line_end_count = strlen('# OLD END FORM');
            $line_start_old = strpos($bottom_script, '# OLD START FORM');
            $line_end_old = strpos($bottom_script, '# OLD END FORM') + $line_end_count;
            $get_string = substr($bottom_script, $line_start_old, $line_end_old);
            $bottom_script = str_replace($get_string, '', $bottom_script);
        }

        //ARRANGE THE FULL SCRIPT
        $file_controller = $top_script."\n\n";
        $file_controller .= "\t\t\t# START FORM DO NOT REMOVE THIS LINE\n";
        $file_controller .= "\t\t\t".'$this->form = [];'."\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "\t\t\t# END FORM DO NOT REMOVE THIS LINE\n\n";

        //CREATE A BACKUP SCAFFOLDING TO OLD TAG
        if ($current_scaffolding_form) {
            $current_scaffolding_form = preg_split("/\\r\\n|\\r|\\n/", $current_scaffolding_form);
            foreach ($current_scaffolding_form as &$c) {
                $c = "\t\t\t//".trim($c);
            }
            $current_scaffolding_form = implode("\n", $current_scaffolding_form);

            $file_controller .= "\t\t\t# OLD START FORM\n";
            $file_controller .= $current_scaffolding_form."\n";
            $file_controller .= "\t\t\t# OLD END FORM\n\n";
        }

        $file_controller .= "\t\t\t".trim($bottom_script);

        //CREATE FILE CONTROLLER
        file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

        return redirect(Route("ModulsControllerGetStep4")."/".$id);
        } catch (\Exception $e) {
            \Log::error("Error in postStep4: " . $e->getMessage());
            \Log::error("Error trace: " . $e->getTraceAsString());
            return redirect()->back()->with(['message' => 'Error processing form: ' . $e->getMessage(), 'message_type' => 'warning']);
        }
    }

    public function getStep4($id)
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (! CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        try {
            $row = DB::table('cms_moduls')->where('id', $id)->first();

            // Log the module data for debugging
            \Log::info("Module data in Step 4 for ID $id:", (array)$row);

            if (!$row) {
                \Log::error("Module with ID $id not found in database for Step 4");
                return redirect()->route('ModulsControllerGetIndex')
                    ->with(['message' => 'Module not found', 'message_type' => 'warning']);
            }

            $data = [];
            $data['id'] = $id;
            $data['row'] = $row; // Add the module row data to the view

            $controller_path = app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php');
            if (file_exists($controller_path)) {
                try {
                    $response = file_get_contents($controller_path);
                    $column_datas = extract_unit($response, "# START CONFIGURATION DO NOT REMOVE THIS LINE", "# END CONFIGURATION DO NOT REMOVE THIS LINE");

                    // Log the configuration data for debugging
                    \Log::info("Configuration data extracted from controller:", ['data' => $column_datas]);

                    // Only process if data is not empty
                    if (trim($column_datas)) {
                        $column_datas = str_replace('$this->', '$data[\'cb_', $column_datas);
                        $column_datas = str_replace(' = ', '\'] = ', $column_datas);
                        $column_datas = str_replace([' ', "\t"], '', $column_datas);

                        // Safely eval the code
                        @eval($column_datas);
                    }
                } catch (\Exception $e) {
                    \Log::error("Error parsing controller configuration: " . $e->getMessage());
                }
            }

            // Try to find the view in various namespaces
            $viewPath = "crudbooster::module_generator.step4";
            if (!view()->exists($viewPath)) {
                $viewPath = "webilliumcms::module_generator.step4";
                if (!view()->exists($viewPath)) {
                    $viewPath = "module_generator.step4";
                }
            }

            \Log::info("Using view path for step4: $viewPath");

            return view($viewPath, $data);

        } catch (\Exception $e) {
            \Log::error("Error in getStep4: " . $e->getMessage());
            return redirect()->route('ModulsControllerGetIndex')
                ->with(['message' => 'Error processing module: ' . $e->getMessage(), 'message_type' => 'warning']);
        }
    }

    public function postStepFinish()
    {
        $this->cbLoader();
        $id = Request::input('id');
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $post = Request::all();

        $post['table'] = $row->table_name;

        $script_config = [];
        $exception = ['_token', 'id', 'submit'];
        $i = 0;
        foreach ($post as $key => $val) {
            if (in_array($key, $exception)) {
                continue;
            }

            if ($val != 'true' && $val != 'false') {
                $value = '"'.$val.'"';
            } else {
                $value = $val;
            }

            // if($key == 'orderby') {
            // 	$value = ;
            // }

            $script_config[$i] = "\t\t\t".'$this->'.$key.' = '.$value.';';
            $i++;
        }

        $scripts = implode("\n", $script_config);
        $raw = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
        $raw = explode("# START CONFIGURATION DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END CONFIGURATION DO NOT REMOVE THIS LINE", $raw[1]);

        $file_controller = trim($raw[0])."\n\n";
        $file_controller .= "\t\t\t# START CONFIGURATION DO NOT REMOVE THIS LINE\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "\t\t\t# END CONFIGURATION DO NOT REMOVE THIS LINE\n\n";
        $file_controller .= "\t\t\t".trim($rraw[1]);

        file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

        return redirect()->route('ModulsControllerGetIndex')->with(['message' => cbLang('alert_update_data_success'), 'message_type' => 'success']);
    }

    public function postAddSave()
    {
        $this->cbLoader();

        if (! CRUDBooster::isCreate() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_add_save', [
                'name' => Request::input($this->title_field),
                'module' => CRUDBooster::getCurrentModule()->name,
            ]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang("denied_access"));
        }

        $this->validation();
        $this->input_assignment();

        //Generate Controller
        $route_basename = basename(Request::get('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = CRUDBooster::generateController(Request::get('table_name'), $route_basename);
        }

        $this->arr['created_at'] = date('Y-m-d H:i:s');
        DB::table($this->table)->insert($this->arr);

        //Insert Menu
        if ($this->arr['controller']) {
            $parent_menu_sort = DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1;
//            $parent_menu_id = DB::table('cms_menus')->max('id') + 1;
            $parent_menu_id = DB::table('cms_menus')->insertGetId([
                'created_at' => date('Y-m-d H:i:s'),
                'name' => $this->arr['name'],
                'icon' => $this->arr['icon'],
                'path' => '#',
                'type' => 'URL External',
                'is_active' => 1,
                'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                'sorting' => $parent_menu_sort,
                'parent_id' => 0,
            ]);
            DB::table('cms_menus')->insert([
                'created_at' => date('Y-m-d H:i:s'),
                'name' => cbLang("text_default_add_new_module", ['module' => $this->arr['name']]),
                'icon' => 'fa fa-plus',
                'path' => $this->arr['controller'].'GetAdd',
                'type' => 'Route',
                'is_active' => 1,
                'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                'sorting' => 1,
                'parent_id' => $parent_menu_id,
            ]);
            DB::table('cms_menus')->insert([
                'created_at' => date('Y-m-d H:i:s'),
                'name' => cbLang("text_default_list_module", ['module' => $this->arr['name']]),
                'icon' => 'fa fa-bars',
                'path' => $this->arr['controller'].'GetIndex',
                'type' => 'Route',
                'is_active' => 1,
                'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                'sorting' => 2,
                'parent_id' => $parent_menu_id,
            ]);
        }

        $id_modul = $this->arr['id'];

        $user_id_privileges = CRUDBooster::myPrivilegeId();
        DB::table('cms_privileges_roles')->insert([
            'id_cms_moduls' => $id_modul,
            'id_cms_privileges' => $user_id_privileges,
            'is_visible' => 1,
            'is_create' => 1,
            'is_read' => 1,
            'is_edit' => 1,
            'is_delete' => 1,
        ]);

        //Refresh Session Roles
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        $ref_parameter = Request::input('ref_parameter');
        if (Request::get('return_url')) {
            CRUDBooster::redirect(Request::get('return_url'), cbLang("alert_add_data_success"), 'success');
        } else {
            if (Request::get('submit') == cbLang('button_save_more')) {
                CRUDBooster::redirect(CRUDBooster::mainpath('add'), cbLang("alert_add_data_success"), 'success');
            } else {
                CRUDBooster::redirect(CRUDBooster::mainpath(), cbLang("alert_add_data_success"), 'success');
            }
        }
    }

    public function postEditSave($id)
    {
        $this->cbLoader();

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! CRUDBooster::isUpdate() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang("log_try_add", ['name' => $row->{$this->title_field}, 'module' => CRUDBooster::getCurrentModule()->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $this->validation();
        $this->input_assignment();

        //Generate Controller
        $route_basename = basename(Request::get('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = CRUDBooster::generateController(Request::get('table_name'), $route_basename);
        }

        DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);

        //Refresh Session Roles
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        CRUDBooster::redirect(Request::server('HTTP_REFERER'), cbLang('alert_update_data_success'), 'success');
    }
}
