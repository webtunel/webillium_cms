<?php namespace webtunel\webilliumcms\controllers;

use CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class PrivilegesController extends CBController
{

    public function cbInit()
    {
        $this->module_name = "Privilege";
        $this->table = 'cms_privileges';
        $this->primary_key = 'id';
        $this->title_field = "name";
        $this->button_import = false;
        $this->button_export = false;
        $this->button_action_style = 'button_icon';
        $this->button_detail = false;
        $this->button_bulk_action = false;

        $this->col = [];
        $this->col[] = ["label" => "ID", "name" => "id"];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = [
            "label" => "Superadmin",
            "name" => "is_superadmin",
            'callback_php' => '($row->is_superadmin)?"<span class=\"label label-success\">Superadmin</span>":"<span class=\"label label-default\">Standard</span>"',
        ];

        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", 'required' => true];
        $this->form[] = ["label" => "Is Superadmin", "name" => "is_superadmin", 'required' => true];
        $this->form[] = ["label" => "Theme Color", "name" => "theme_color", 'required' => true];

        $this->alert[] = [
            'message' => "You can use the helper <code>CRUDBooster::getMyPrivilegeId()</code> to get current user login privilege id, or <code>CRUDBooster::getMyPrivilegeName()</code> to get current user login privilege name",
            'type' => 'info',
        ];
    }

    public function getIndex()
    {
        $this->cbLoader();

        $module = CRUDBooster::getCurrentModule();

        if (!CRUDBooster::isView() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_view', ['module' => $module->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        // Use parent's getIndex method to get the standard data
        $result = parent::getIndex();

        // If it's a PostgreSQL database, we need to modify the privileges data to avoid PDO::prepare error
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Override moduls data for each privilege to use proper SQL query format
            foreach ($result['result'] as $privilege) {
                $id = $privilege->id;

                // Get modules with properly cast DB::raw expressions
                $moduls = DB::table("cms_moduls")
                    ->where('is_protected', 0)
                    ->whereNull('deleted_at')
                    ->select([
                        "cms_moduls.*",
                        // Cast subqueries results to proper types to avoid PDO errors
                        DB::raw("COALESCE((select is_visible::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_visible"),
                        DB::raw("COALESCE((select is_create::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_create"),
                        DB::raw("COALESCE((select is_read::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_read"),
                        DB::raw("COALESCE((select is_edit::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_edit"),
                        DB::raw("COALESCE((select is_delete::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_delete")
                    ])
                    ->orderby("name", "asc")
                    ->get();

                // Store modules data per privilege
                $privilege->moduls = $moduls;
            }
        } else {
            // For MySQL and other drivers, get modules data normally
            foreach ($result['result'] as $privilege) {
                $id = $privilege->id;

                $moduls = DB::table("cms_moduls")
                    ->where('is_protected', 0)
                    ->whereNull('deleted_at')
                    ->select([
                        "cms_moduls.*",
                        DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_visible"),
                        DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_create"),
                        DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_read"),
                        DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_edit"),
                        DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_delete")
                    ])
                    ->orderby("name", "asc")
                    ->get();

                $privilege->moduls = $moduls;
            }
        }

        return $result;
    }

    public function getAdd()
    {
        $this->cbLoader();

        if (! CRUDBooster::isCreate() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang('log_try_add', ['module' => CRUDBooster::getCurrentModule()->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang("denied_access"));
        }

        $id = 0;
        $data['page_title'] = "Add Data";

        // Handle PostgreSQL differently
        if (DB::connection()->getDriverName() === 'pgsql') {
            $data['moduls'] = DB::table("cms_moduls")
                ->where('is_protected', 0)
                ->whereNull('deleted_at')
                ->select([
                    "cms_moduls.*",
                    // Cast subqueries results to proper types to avoid PDO errors
                    DB::raw("COALESCE((select is_visible::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_visible"),
                    DB::raw("COALESCE((select is_create::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_create"),
                    DB::raw("COALESCE((select is_read::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_read"),
                    DB::raw("COALESCE((select is_edit::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_edit"),
                    DB::raw("COALESCE((select is_delete::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_delete")
                ])
                ->orderby("name", "asc")
                ->get();
        } else {
            // For MySQL and other drivers
            $data['moduls'] = DB::table("cms_moduls")
                ->where('is_protected', 0)
                ->whereNull('deleted_at')
                ->select([
                    "cms_moduls.*",
                    DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_visible"),
                    DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_create"),
                    DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_read"),
                    DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_edit"),
                    DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_delete")
                ])
                ->orderby("name", "asc")
                ->get();
        }

        $data['page_menu'] = Route::getCurrentRoute()->getActionName();

        return view('crudbooster::privileges', $data);
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
        $id = DB::table($this->table)->insertGetId($this->arr);

        //set theme
        Session::put('theme_color', $this->arr['theme_color']);

        $priv = Request::input("privileges");
        if ($priv) {
            foreach ($priv as $id_modul => $data) {
                $arrs = [];
                $arrs['is_visible'] = @$data['is_visible'] ?: 0;
                $arrs['is_create'] = @$data['is_create'] ?: 0;
                $arrs['is_read'] = @$data['is_read'] ?: 0;
                $arrs['is_edit'] = @$data['is_edit'] ?: 0;
                $arrs['is_delete'] = @$data['is_delete'] ?: 0;
                $arrs['id_cms_privileges'] = $id;
                $arrs['id_cms_moduls'] = $id_modul;
                DB::table("cms_privileges_roles")->insert($arrs);

                $module = DB::table('cms_moduls')->where('id', $id_modul)->first();
            }
        }

        //Refresh Session Roles
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        CRUDBooster::redirect(CRUDBooster::mainpath(), cbLang("alert_add_data_success"), 'success');
    }

    public function getEdit($id)
    {
        $this->cbLoader();

        $row = DB::table($this->table)->where("id", $id)->first();

        if (! CRUDBooster::isRead() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang("log_try_edit", [
                'name' => $row->{$this->title_field},
                'module' => CRUDBooster::getCurrentModule()->name,
            ]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $page_title = cbLang('edit_data_page_title', ['module' => 'Privilege', 'name' => $row->name]);

        // Handle PostgreSQL differently
        if (DB::connection()->getDriverName() === 'pgsql') {
            $moduls = DB::table("cms_moduls")
                ->where('is_protected', 0)
                ->whereNull('deleted_at')
                ->select([
                    "cms_moduls.*",
                    // Cast subqueries results to proper types to avoid PDO errors
                    DB::raw("COALESCE((select is_visible::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_visible"),
                    DB::raw("COALESCE((select is_create::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_create"),
                    DB::raw("COALESCE((select is_read::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_read"),
                    DB::raw("COALESCE((select is_edit::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_edit"),
                    DB::raw("COALESCE((select is_delete::integer from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id'), 0) as is_delete")
                ])
                ->orderby("name", "asc")
                ->get();
        } else {
            // For MySQL and other drivers
            $moduls = DB::table("cms_moduls")
                ->where('is_protected', 0)
                ->whereNull('deleted_at')
                ->select([
                    "cms_moduls.*",
                    DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_visible"),
                    DB::raw("(select is_create from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_create"),
                    DB::raw("(select is_read from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_read"),
                    DB::raw("(select is_edit from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_edit"),
                    DB::raw("(select is_delete from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_delete")
                ])
                ->orderby("name", "asc")
                ->get();
        }

        $page_menu = Route::getCurrentRoute()->getActionName();

        return view('crudbooster::privileges', compact('row', 'page_title', 'moduls', 'page_menu'));
    }

    public function postEditSave($id)
    {
        $this->cbLoader();

        $row = CRUDBooster::first($this->table, $id);

        if (! CRUDBooster::isUpdate() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang("log_try_add", ['name' => $row->{$this->title_field}, 'module' => CRUDBooster::getCurrentModule()->name]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        $this->validation($id);
        $this->input_assignment($id);

        DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);

        $priv = Request::input("privileges");

        // This solves issue #1074
        DB::table("cms_privileges_roles")->where("id_cms_privileges", $id)->delete();

        if ($priv) {

            foreach ($priv as $id_modul => $data) {
                //Check Menu
                $module = DB::table('cms_moduls')->where('id', $id_modul)->first();
                $currentPermission = DB::table('cms_privileges_roles')->where('id_cms_moduls', $id_modul)->where('id_cms_privileges', $id)->first();

                if ($currentPermission) {
                    $arrs = [];
                    $arrs['is_visible'] = @$data['is_visible'] ?: 0;
                    $arrs['is_create'] = @$data['is_create'] ?: 0;
                    $arrs['is_read'] = @$data['is_read'] ?: 0;
                    $arrs['is_edit'] = @$data['is_edit'] ?: 0;
                    $arrs['is_delete'] = @$data['is_delete'] ?: 0;
                    DB::table('cms_privileges_roles')->where('id', $currentPermission->id)->update($arrs);
                } else {
                    $arrs = [];
                    $arrs['is_visible'] = @$data['is_visible'] ?: 0;
                    $arrs['is_create'] = @$data['is_create'] ?: 0;
                    $arrs['is_read'] = @$data['is_read'] ?: 0;
                    $arrs['is_edit'] = @$data['is_edit'] ?: 0;
                    $arrs['is_delete'] = @$data['is_delete'] ?: 0;
                    $arrs['id_cms_privileges'] = $id;
                    $arrs['id_cms_moduls'] = $id_modul;
                    DB::table("cms_privileges_roles")->insert($arrs);
                }
            }
        }

        //Refresh Session Roles
        if ($id == CRUDBooster::myPrivilegeId()) {
            $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
            Session::put('admin_privileges_roles', $roles);

            Session::put('theme_color', $this->arr['theme_color']);
        }

        CRUDBooster::redirect(CRUDBooster::mainpath(), cbLang("alert_update_data_success", [
            'module' => "Privilege",
            'title' => $row->name,
        ]), 'success');
    }

    public function getDelete($id)
    {
        $this->cbLoader();

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! CRUDBooster::isDelete() && $this->global_privilege == false) {
            CRUDBooster::insertLog(cbLang("log_try_delete", [
                'name' => $row->{$this->title_field},
                'module' => CRUDBooster::getCurrentModule()->name,
            ]));
            CRUDBooster::redirect(CRUDBooster::adminPath(), cbLang('denied_access'));
        }

        DB::table($this->table)->where($this->primary_key, $id)->delete();
        DB::table("cms_privileges_roles")->where("id_cms_privileges", $row->id)->delete();

        CRUDBooster::redirect(CRUDBooster::mainpath(), cbLang("alert_delete_data_success"), 'success');
    }
}
