@extends("crudbooster::admin_template")
@section("content")
    @push('bottom')
        <script>
            $('.section-header').hide();
            $('.module-generator-header').show();
        </script>
    @endpush

    <div class="section-header module-generator-header">
        <h1>
            <!--Now you can define $page_icon alongside $page_tite for custom forms to follow CRUDBooster theme style -->
            <i class="fa fa-database"></i> Module Generator (Configuration)

            <ul class="nav nav-tabs" style="margin-top: 20px;">
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep1')."/".$id}}"><i class='fa fa-info'></i> Step 1 - Module Information</a></li>
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep2')."/".$id}}"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep3')."/".$id}}"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
                <li role="presentation"><a class="btn btn-primary" href="{{Route('ModulsControllerGetStep4')."/".$id}}"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
            </ul>
        </h1>
    </div>
    <div class="box box-default">
        <form method='post' action='{{Route('ModulsControllerPostStepFinish')}}'>
            {{csrf_field()}}
            <input type="hidden" name="id" value='{{$id}}'>
            <div class="box-body">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Title Field Candidate</label>
                            <input type="text" name="title_field" value="{{$cb_title_field ?? ''}}" class='form-control'>
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="form-group">
                            <label>Limit Data</label>
                            <input type="number" name="limit" value="{{$cb_limit ?? ''}}" class='form-control'>
                        </div>
                    </div>

                    <div class="col-sm-7">
                        <div class="form-group">
                            <label>Order By</label>
                            <?php
                            if (isset($cb_orderby)) {
                                if (is_array($cb_orderby)) {
                                    $orderby = [];
                                    foreach ($cb_orderby as $k => $v) {
                                        $orderby[] = $k.','.$v;
                                    }
                                    $orderby = implode(";", $orderby);
                                } else {
                                    $orderby = $cb_orderby;
                                }
                            } else {
                                $orderby = '';
                            }
                            ?>
                            <input type="text" name="orderby" value="{{$orderby}}" class='form-control'>
                            <div class="help-block">E.g : column_name,desc</div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Global Privilege</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" class="custom-switch-input" name='global_privilege' {{(isset($cb_global_privilege) && $cb_global_privilege)?"checked":""}} value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_global_privilege) && !$cb_global_privilege)?"checked":"checked"}} name='global_privilege' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Button Table Action</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" class="custom-switch-input" name='button_table_action' {{(isset($cb_button_table_action) && $cb_button_table_action)?"checked":""}} value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_table_action) && !$cb_button_table_action)?"checked":"checked"}} name='button_table_action' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Bulk Action Button</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" class="custom-switch-input" name='button_bulk_action' {{(isset($cb_button_bulk_action) && $cb_button_bulk_action)?"checked":""}} value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_bulk_action) && !$cb_button_bulk_action)?"checked":"checked"}} name='button_bulk_action' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Button Action Style</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_action_style) && $cb_button_action_style=='button_icon')?"checked":""}} name='button_action_style' value='button_icon' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Icon</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_action_style) && $cb_button_action_style=='button_icon_text')?"checked":""}} name='button_action_style' value='button_icon_text' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Icon & Text</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_action_style) && $cb_button_action_style=='button_text')?"checked":""}} name='button_action_style' value='button_text' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Button Text</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_action_style) && $cb_button_action_style=='button_dropdown')?"checked":"checked"}} name='button_action_style' value='button_dropdown' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Dropdown</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Show Button Add</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" class="custom-switch-input" name='button_add' {{(isset($cb_button_add) && $cb_button_add)?"checked":""}} value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_add) && !$cb_button_add)?"checked":"checked"}} name='button_add' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Button Edit</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_edit) && $cb_button_edit)?"checked":""}} name='button_edit' class="custom-switch-input"  value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_edit) && !$cb_button_edit)?"checked":"checked"}} name='button_edit' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Button Delete</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_delete) && $cb_button_delete)?"checked":""}} name='button_delete' class="custom-switch-input"  value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_delete) && !$cb_button_delete)?"checked":"checked"}} name='button_delete' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Button Detail</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_detail) && $cb_button_detail)?"checked":""}} name='button_detail' class="custom-switch-input"  value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_detail) && !$cb_button_detail)?"checked":"checked"}} name='button_detail' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label>Show Button Show Data</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_show) && $cb_button_show)?"checked":""}} name='button_show' class="custom-switch-input"  value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_show) && !$cb_button_show)?"checked":"checked"}} name='button_show' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Button Filter & Sorting</label>

                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_filter) && $cb_button_filter)?"checked":""}} name='button_filter' class="custom-switch-input"  value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_filter) && !$cb_button_filter)?"checked":"checked"}} name='button_filter' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Button Import</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_import) && $cb_button_import)?"checked":""}} name='button_import' class="custom-switch-input"  value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_import) && !$cb_button_import)?"checked":"checked"}} name='button_import' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Show Button Export</label>
                                <div class="row">
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_export) && $cb_button_export)?"checked":""}} name='button_export' class="custom-switch-input"  value='true'>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">TRUE</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" {{(isset($cb_button_export) && !$cb_button_export)?"checked":"checked"}} name='button_export' value='false' class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">FALSE</span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div align="right" style="float: right;">
                    <button type="button" onclick="location.href='{{CRUDBooster::mainpath('step3').'/'.$id}}'" class="btn btn-default">&laquo; Back</button>
                    <input type="submit" name="submit" class='btn btn-primary' value='Save Module'>
                </div>
            </div>
        </form>
    </div>


@endsection
