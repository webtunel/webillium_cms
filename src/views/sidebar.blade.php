<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{CRUDBooster::adminPath()}}">{{Session::get('appname')}}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{CRUDBooster::adminPath()}}">{{substr(Session::get('appname'), 0, 2)}}</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">{{cbLang("menu_navigation")}}</li>

            <li class="menu-header"> <?php $dashboard = CRUDBooster::sidebarDashboard();?></li>
            @if($dashboard && isset($dashboard->id))
                <li data-id='{{$dashboard->id}}'>
                    <a class="nav-link {{ (Request::is(config('crudbooster.ADMIN_PATH'))) ? 'active' : '' }} {{(isset($dashboard->color) && $dashboard->color)?"text-".$dashboard->color:""}}"
                       href="{{CRUDBooster::adminPath()}}">
                        <i class="fas fa-pencil-ruler"></i> <span>{{cbLang("text_dashboard")}}</span>
                    </a>
                </li>
            @else
                <li>
                    <a class="nav-link {{ (Request::is(config('crudbooster.ADMIN_PATH'))) ? 'active' : '' }}"
                       href="{{CRUDBooster::adminPath()}}">
                        <i class="fas fa-pencil-ruler"></i> <span>{{cbLang("text_dashboard")}}</span>
                    </a>
                </li>
            @endif

            @foreach(CRUDBooster::sidebarMenu() as $menu)
                <li class="dropdown">
                    <a class="nav-link @if(isset($menu->children) && !empty($menu->children))has-dropdown @endif" href='{{ ($menu->is_broken)?"javascript:alert('".cbLang('controller_route_404')."')":$menu->url }}'>
                        <i class='{{$menu->icon}} {{($menu->color)?"text-".$menu->color:""}}'></i>
                        <span>{{$menu->name}}</span>
                    </a>
                    @if(!empty($menu->children) && isset($menu->children))
                        <ul class="dropdown-menu">
                            @foreach($menu->children as $child)
                                <li data-id='{{$child->id}}'>
                                    <a  href='{{ ($child->is_broken)?"javascript:alert('".cbLang('controller_route_404')."')":$child->url}}'
                                        class='{{($child->color)?"text-".$child->color:""}} nav-link'>
                                        <i class='{{$child->icon}}'></i> <span>{{$child->name}}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach



            @if(CRUDBooster::isSuperadmin())
                <li class="menu-header"> {{ cbLang('SUPERADMIN') }}</li>
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="far fa-user"></i>
                        <span>{{ cbLang('Users_Management') }}</span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link"
                               href="{{Route("AdminCmsUsersControllerGetAdd")}}">{{ cbLang('add_user') }}</a></li>
                        <li><a class="nav-link"
                               href="{{Route("AdminCmsUsersControllerGetIndex")}}">{{ cbLang('List_users') }}</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="fas fa-grip-horizontal"></i>
                        <span>{{ cbLang('Privileges_Roles') }}</span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{Route("PrivilegesControllerGetAdd")}}">{{ cbLang('Add_New_Privilege') }}</a></li>
                        <li><a class="nav-link"
                               href="{{Route("PrivilegesControllerGetIndex")}}">{{ cbLang('List_Privilege') }}</a></li>
                    </ul>
                </li>


                <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/menu_management*')) ? 'active' : '' }}">
                    <a href='{{Route("MenusControllerGetIndex")}}'>
                        <i class="fas fa-bars"></i>
                        <span>{{ cbLang('Menu_Management') }}</span>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="fas fa-th"></i>
                        <span>{{ cbLang('settings') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{route("SettingsControllerGetAdd")}}">{{ cbLang('Add_New_Setting') }}</a></li>
                        <?php
                        $groupSetting = DB::table('cms_settings')->groupby('group_setting')->pluck('group_setting');
                        foreach($groupSetting as $gs):
                        ?>
                        <li><a class="nav-link" href="{{route("SettingsControllerGetShow")}}?group={{urlencode($gs)}}&m=0">{{$gs}}</a></li>
                        <?php endforeach;?>

                    </ul>
                </li>


                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="fas fa-plug"></i>
                        <span>{{ cbLang('Module_Generator') }}</span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{Route("ModulsControllerGetStep1")}}">{{ cbLang('Add_New_Module') }}</a></li>
                        <li><a class="nav-link" href="{{Route("ModulsControllerGetIndex")}}">{{ cbLang('List_Module') }}</a></li>
                    </ul>
                </li>

                <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/generate-models')) ? 'active' : '' }}">
                    <a href='{{'generate-models'}}'>
                        <i class="fas fa-book-reader"></i>
                        <span>Generate models</span>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown">
                        <i class="fas fa-ellipsis-h"></i>
                        <span>
                        {{ cbLang('API_Generator') }}
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{Route("ApiCustomControllerGetGenerator")}}">{{ cbLang('Add_New_API') }}</a></li>
                        <li><a class="nav-link" href="{{Route("ApiCustomControllerGetIndex")}}">{{ cbLang('list_API') }}</a></li>
                        <li><a class="nav-link" href="{{Route("ApiCustomControllerGetScreetKey")}}">{{ cbLang('Generate_Screet_Key') }}</a></li>
{{--                        <li><a class="nav-link" href="{{Route("ApiCustomControllerGetIndex")}}">{{ cbLang('list_API') }}</a></li>--}}
                    </ul>
                </li>


                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown">
                        <i class='fas fa-mail-bulk'></i>
                        <span>
                        {{ cbLang('Email_Templates') }}
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{Route("EmailTemplatesControllerGetAdd")}}">{{ cbLang('Add_New_Email') }}</a></li>
                        <li><a class="nav-link" href="{{Route("EmailTemplatesControllerGetIndex")}}">{{ cbLang('List_Email_Template') }}</a></li>
                    </ul>
                </li>

            @endif

        </ul><!-- /.sidebar-menu -->
        @if(CRUDBooster::isSuperadmin())
            <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
                <a href="{{Route("LogsControllerGetIndex")}}" class="btn btn-primary btn-lg btn-block btn-icon-split">
                    <i class="fas fa-rocket"></i> {{ cbLang('Log_User_Access') }}
                </a>
            </div>
        @endif


    </aside>
</div>
