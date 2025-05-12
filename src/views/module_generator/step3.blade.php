@extends("crudbooster::admin_template")
@section("content")
    @push('head')
        <link rel="stylesheet" href="{{ asset('vendor/crudbooster/assets/stisla/select2.min.css') }}">
    @endpush
    @push('bottom')
        <script src="{{ asset('vendor/crudbooster/assets/stisla/select2.full.min.js') }}"></script>
        <script>
            $(function () {
                $('.select2').select2();
            })
            $('.section-header').hide();
            $('.module-generator-header').show();
        </script>
    @endpush

    <div class="section-header module-generator-header">
        <h1>
            <!--Now you can define $page_icon alongside $page_tite for custom forms to follow CRUDBooster theme style -->
            <i class="fa fa-database"></i> Module Generator (Form Display)

            <ul class="nav nav-tabs" style="margin-top: 20px;">
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep1')."/".$id}}"><i class='fa fa-info'></i> Step 1 - Module Information</a></li>
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep2')."/".$id}}"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
                <li role="presentation"><a class="btn btn-primary" href="{{Route('ModulsControllerGetStep3')."/".$id}}"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep4')."/".$id}}"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
            </ul>
        </h1>
    </div>
    @push('head')
        <style>
            .table-form tbody tr td {
                position: relative;
            }

            .sub {
                position: absolute;
                top: 35px; /* Position below the input field */
                left: 15px;
                padding: 0;
                margin: 0;
                list-style-type: none;
                height: 180px;
                overflow-y: auto;
                z-index: 100;
                background: white;
                border: 1px solid #ccc;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                max-height: 300px;
                width: 220px;
            }

            .sub li {
                padding: 8px 12px;
                background: #ffffff;
                cursor: pointer;
                display: block;
                border-bottom: 1px solid #f0f0f0;
                width: 100%;
                font-size: 14px;
            }

            .sub li:hover {
                background: #f7f7f7;
                color: #3c8dbc;
            }

            /* Make sure the dropdown is on top of other elements */
            .form-control:focus + .sub,
            .sub:hover {
                display: block !important;
            }

            /* Ensure proper z-index for dropdown menus */
            .table-form {
                position: relative;
                z-index: 1;
            }

            /* Modal styling */
            #myModal .modal-body {
                padding: 20px;
                max-height: 70vh;
                overflow-y: auto;
            }

            #myModal .form-group {
                margin-bottom: 15px;
            }

            #myModal .form-group label {
                display: block;
                font-weight: 600;
                margin-bottom: 5px;
            }

            #myModal .alert {
                margin-bottom: 20px;
            }

            #myModal .form-control {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #d2d6de;
                border-radius: 3px;
            }

            /* Option area styling for hidden options in the table */
            .option_area {
                padding: 10px;
                background-color: #f9f9f9;
                border: 1px solid #eee;
                border-radius: 3px;
                margin-top: 10px;
            }
        </style>
    @endpush

    @push('bottom')
        <script type="text/javascript">
            var columns = {!! json_encode($columns) !!};
            var types = {!! json_encode($types) !!};
            var validation_rules = ['required', 'string', 'integer', 'double', 'image', 'date', 'numeric', 'alpha_spaces'];

            function ucwords(str) {
                return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                    return $1.toUpperCase();
                });
            }

            function showTypeSuggest(t) {
                t = $(t);

                t.next("ul").remove();
                var list = '';

                if (types && types.length > 0) {
                    $.each(types, function (i, obj) {
                        if (typeof obj === 'string') {
                            list += "<li>" + obj + "</li>";
                        }
                    });
                } else {
                    // Fallback options if types array is empty
                    var defaultTypes = ['text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'date', 'time', 'datetime', 'email', 'password', 'hidden'];
                    $.each(defaultTypes, function(i, type) {
                        list += "<li>" + type + "</li>";
                    });
                }

                if (list === '') {
                    list = "<li>text</li><li>textarea</li><li>select</li><li>checkbox</li><li>radio</li><li>number</li>";
                }

                t.after("<ul class='sub'>" + list + "</ul>");
                t.next("ul").show(); // Make sure the dropdown is visible
            }

            function showTypeSuggestLike(t) {
                t = $(t);

                var v = t.val().toLowerCase();
                t.next("ul").remove();
                if (!v) {
                    // If empty, show all types
                    showTypeSuggest(t);
                    return;
                }

                var list = '';

                if (types && types.length > 0) {
                    $.each(types, function (i, obj) {
                        if (typeof obj === 'string' && obj.toLowerCase().includes(v)) {
                            list += "<li>" + obj + "</li>";
                        }
                    });
                } else {
                    // Fallback options if types array is empty
                    var defaultTypes = ['text', 'textarea', 'select', 'checkbox', 'radio', 'number', 'date', 'time', 'datetime', 'email', 'password', 'hidden'];
                    $.each(defaultTypes, function(i, type) {
                        if (type.includes(v)) {
                            list += "<li>" + type + "</li>";
                        }
                    });
                }

                if (list === '') {
                    list = "<li>No matching types found</li>";
                }

                t.after("<ul class='sub'>" + list + "</ul>");
                t.next("ul").show(); // Make sure the dropdown is visible
            }

            function showNameSuggest(t) {
                t = $(t);

                t.next("ul").remove();
                var list = '';
                $.each(columns, function (i, obj) {
                    list += "<li>" + obj + "</li>";
                });

                t.after("<ul class='sub'>" + list + "</ul>");
            }

            function showNameSuggestLike(t) {
                t = $(t);

                var v = t.val();
                t.next("ul").remove();
                if (!v) return false;

                var list = '';
                $.each(columns, function (i, obj) {
                    if (obj.includes(v.toLowerCase())) {
                        list += "<li>" + obj + "</li>";
                    }
                });

                t.after("<ul class='sub'>" + list + "</ul>");
            }

            function showColumnSuggest(t) {
                t = $(t);
                t.next("ul").remove();

                var list = '';
                $.each(columns, function (i, obj) {
                    obj = obj.replace('id_', '');
                    obj = ucwords(obj.replace('_', ' '));
                    list += "<li>" + obj + "</li>";
                });

                t.after("<ul class='sub'>" + list + "</ul>");
            }

            function showColumnSuggestLike(t) {
                t = $(t);
                var v = t.val();

                t.next("ul").remove();
                if (!v) return false;

                var list = '';
                $.each(columns, function (i, obj) {

                    if (obj.includes(v.toLowerCase())) {
                        obj = obj.replace('id_', '');
                        obj = ucwords(obj.replace('_', ' '));

                        list += "<li>" + obj + "</li>";
                    }
                });

                t.after("<ul class='sub'>" + list + "</ul>");
            }

            function showValidationSuggest(t) {
                t = $(t);
                t.next("ul").remove();

                var list = '';
                $.each(validation_rules, function (i, obj) {
                    list += "<li>" + obj + "</li>";
                });

                t.after("<ul class='sub'>" + list + "</ul>");
            }

            function showValidationSuggestLike(t) {
                t = $(t);
                var v = t.val();

                t.next("ul").remove();
                if (!v) return false;

                var list = '';
                $.each(validation_rules, function (i, obj) {
                    if (obj.includes(v.toLowerCase())) {
                        list += "<li>" + obj + "</li>";
                    }
                });

                t.after("<ul class='sub'>" + list + "</ul>");
            }

            $(function () {


                $(document).on('click', '.btn-plus', function () {
                    var tr_parent = $(this).parent().parent('tr');
                    var clone = $('#tr-sample').clone();
                    clone.removeAttr('id');
                    tr_parent.after(clone);
                    $('.table-form tr').not('#tr-sample').show();
                })

                //init row
                $('.btn-plus').last().click();

                $(document).mouseup(function (e) {
                    var container = $(".sub");
                    // Don't hide if clicking on an input that should show the dropdown
                    var isInputClick = $(e.target).is('input[name="type[]"]') ||
                                     $(e.target).is('input[name="name[]"]') ||
                                     $(e.target).is('input[name="validation[]"]');

                    if (!container.is(e.target) &&
                        container.has(e.target).length === 0 &&
                        !isInputClick) {
                        container.hide();
                    }
                });

                $(document).on('click', '.sub li', function () {
                    var v = $(this).text();
                    var t = $(this).parent('ul').parent('td');
                    var tr_index = parseInt(t.parent().index());

                    var input_name = $(this).parent().parent('td').find('input[type=text]').attr('name');

                    if (input_name == 'type[]') {
                        $(this).parent('ul').prev('input[type=text]').val(v);
                        $(this).parent('ul').remove();

                        t.parent('tr').find('.option_area').empty();

                        $.getJSON("{{CRUDBooster::mainpath('type-info')}}/" + v, function (data) {

                            if (data.alert) {
                                t.parent('tr').find('.option_area').prepend("<div class='alert alert-warning'><strong>IMPORTANT</strong><br/>" + data.alert + "</div>");
                            }

                            if (data.attribute.required) {
                                $.each(data.attribute.required, function (key, val) {

                                    var form_group_html = '';

                                    if (val instanceof Object) {
                                        form_group_html += "<div class='form-group'><label>" + key + "</label>";

                                        if (val.type) {
                                            if (val.type == 'radio') {
                                                $.each(val.enum, function (i, o) {
                                                    form_group_html += "<input type='radio' name='option[" + tr_index + "][" + key + "]' value='" + o + "'/> " + o + " &nbsp;&nbsp;";
                                                })
                                            } else {
                                                if (val.type == 'array') {

                                                    form_group_html += "<input class='form-control required' name='option[" + tr_index + "][" + key + "]' placeholder='" + val.placeholder + "' type='text'/>";
                                                    form_group_html += "<input name='option[" + tr_index + "][" + key + "_type]' value='array' type='hidden'/>";

                                                } else {

                                                    form_group_html += "<input class='form-control required' name='option[" + tr_index + "][" + key + "]' placeholder='" + val.placeholder + "' type='text'/>";
                                                }
                                            }
                                        } else {
                                            form_group_html += "<input class='form-control required' name='option[" + tr_index + "][" + key + "]' placeholder='" + val + "' type='text'/>";
                                        }

                                        form_group_html += "</div>";
                                    } else {
                                        form_group_html +=
                                            "<div class='form-group'>" +
                                            "<label>" + key + "</label>" +
                                            "<input class='form-control required' name='option[" + tr_index + "][" + key + "]' placeholder='" + val + "' type='text'/>" +
                                            "</div>"
                                        ;
                                    }

                                    t.parent('tr').find('.option_area').append(form_group_html);

                                });
                            }

                            if (data.attribute.requiredOne) {
                                $.each(data.attribute.requiredOne, function (key, val) {
                                    t.parent('tr').find('.option_area').append(
                                        "<div class='form-group'>" +
                                        "<label>" + key + "</label>" +
                                        "<input class='form-control required-one'  name='option[" + tr_index + "][" + key + "]' placeholder='" + val + "' type='text'/>" +
                                        "</div>"
                                    );
                                });
                            }

                            if (data.attribute.optional) {
                                $.each(data.attribute.optional, function (key, val) {
                                    if (typeof(val) == "object") {
                                        if (val.type == 'textarea') {
                                            t.parent('tr').find('.option_area').append(
                                                "<div class='form-group'>" +
                                                "<label>" + key + "</label>" +
                                                "<textarea class='form-control' name='option[" + tr_index + "][" + key + "]' placeholder='" + val.placeholder + "' ></textarea>" +
                                                "</div>"
                                            );
                                        }
                                    } else {
                                        t.parent('tr').find('.option_area').append(
                                            "<div class='form-group'>" +
                                            "<label>" + key + "</label>" +
                                            "<input class='form-control' name='option[" + tr_index + "][" + key + "]' placeholder='" + val + "' type='text'/>" +
                                            "</div>"
                                        );
                                    }
                                });
                            }
                        })

                    } else if (input_name == 'validation[]') {
                        var currentVal = $(this).parent('ul').prev('input[type=text]').val();
                        if (currentVal != '') {
                            v = currentVal + '|' + v;
                        }
                        $(this).parent('ul').prev('input[type=text]').val(v);
                        $(this).parent('ul').remove();
                    } else {
                        $(this).parent('ul').prev('input[type=text]').val(v);
                        $(this).parent('ul').remove();
                    }
                })

                $(document).on('click', '.table-form .btn-delete', function () {
                    $(this).parent().parent().remove();
                })

                $(document).on('click', '.table-form .btn-up', function () {
                    var tr = $(this).parent().parent();
                    var trPrev = tr.prev('tr');
                    if (trPrev.length != 0) {

                        tr.prev('tr').before(tr.clone());
                        tr.remove();
                    }
                })

                $(document).on('click', '.table-form .btn-down', function () {
                    var tr = $(this).parent().parent();
                    var trPrev = tr.next('tr');
                    if (trPrev.length != 0) {

                        tr.next('tr').after(tr.clone());
                        tr.remove();
                    }
                })

                var current_option_area = null;
                var current_field_type = '';

                $(document).on('click', '.btn-options', function () {
                    // Clear previous content
                    $('#myModal .modal-body').empty();

                    // Get the option area and field type
                    current_option_area = $(this).next('.option_area');
                    current_field_type = $(this).closest('tr').find('input[name="type[]"]').val() || 'text';

                    // Update modal title to include field type
                    $('#myModal .modal-title').html('<i class="fa fa-cog"></i> Options for <strong>' + current_field_type + '</strong> Field');

                    // If the option area is empty, show a message
                    if (current_option_area.children().length === 0) {
                        $('#myModal .modal-body').html(
                            '<div class="alert alert-info">' +
                            '<i class="fa fa-info-circle"></i> ' +
                            'No options available for this field type or you need to select a field type first.' +
                            '</div>'
                        );
                    } else {
                        // Clone the option area content and add it to the modal
                        var clone = current_option_area.clone();
                        clone.removeAttr('style');
                        clone.css('display', 'block');
                        clone.appendTo('#myModal .modal-body');
                    }

                    // Show the modal
                    $('#myModal').modal('show');
                })

                $('#myModal .btn-save-option').click(function () {
                    // Check if modal body is empty or has error message
                    if ($('#myModal .modal-body').children().length === 0 ||
                        $('#myModal .modal-body').find('.alert-info').length > 0) {
                        $('#myModal').modal('hide');
                        return;
                    }

                    // Validation for required fields
                    var i_required = [];
                    $('#myModal .modal-body .required').each(function () {
                        var value = $(this).val();
                        var name = $(this).attr('name');
                        if (value === '') {
                            i_required.push(name);
                            // Highlight the field
                            $(this).addClass('is-invalid').css('border-color', '#f44336');
                        } else {
                            $(this).removeClass('is-invalid').css('border-color', '');
                        }
                    });

                    if (i_required.length > 0) {
                        // Show validation message in the modal instead of an alert
                        if ($('#myModal .modal-body .validation-error').length === 0) {
                            $('#myModal .modal-body').prepend(
                                '<div class="alert alert-danger validation-error">' +
                                '<strong>Error:</strong> The following fields are required: ' + i_required.join(", ") +
                                '</div>'
                            );
                        }
                        return false;
                    }

                    // Validation for required-one fields
                    var i_required_one = [];
                    $('#myModal .modal-body .required-one').each(function () {
                        var value = $(this).val();
                        var name = $(this).attr('name');
                        if (value === '') {
                            i_required_one.push(name);
                        }
                    });

                    if (i_required_one.length > 0 && i_required_one.length === $('#myModal .modal-body .required-one').length) {
                        // Show validation message in the modal instead of an alert
                        if ($('#myModal .modal-body .validation-error').length === 0) {
                            $('#myModal .modal-body').prepend(
                                '<div class="alert alert-danger validation-error">' +
                                '<strong>Error:</strong> At least one of these fields is required: ' + i_required_one.join(", ") +
                                '</div>'
                            );
                        }
                        return false;
                    }

                    // Remove any validation error messages
                    $('#myModal .modal-body .validation-error').remove();

                    // Copy the options back to the original option area
                    current_option_area.empty();
                    var clone = $('#myModal .modal-body > .option_area').children().clone();
                    current_option_area.html(clone);

                    // Close the modal
                    $('#myModal').modal('hide');
                })

            })
        </script>
    @endpush

    <div id="myModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog  modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class='fa fa-cog'></i> Field Options</h4>
                </div>
                <div class="modal-body">
                    <!-- Options will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn-save-option btn btn-primary">Save Options</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="box box-default">

        <div class="box-body">
            <form method="post" autocomplete="off" action="{{Route('ModulsControllerPostStep4')}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$id}}">

                <table class='table-form table table-striped'>
                    <thead>
                    <tr>
                        <th>Label</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Validation</th>
                        <th width="90px">Width</th>
                        <th width="100px">Options</th>
                        <th width="180px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $index = 0;?>
                    @foreach($cb_form ?? [] as $form)
                        <tr>
                            <td><input type='text' value='{{$form["label"] ?? ""}}' placeholder="Input field label" onclick='showColumnSuggest(this)'
                                       onkeyup="showColumnSuggestLike(this)" class='form-control labels' name='label[]'/></td>
                            <td><input type='text' value='{{$form["name"] ?? ""}}' placeholder="Input field name" onclick='showNameSuggest(this)'
                                       onkeyup="showNameSuggestLike(this)" class='form-control name' name='name[]'/></td>
                            <td><input type='text' value='{{$form["type"] ?? "text"}}' placeholder="Input field type" onclick='showTypeSuggest(this)'
                                       onkeyup="showTypeSuggestLike(this)" class='form-control type' name='type[]'/></td>
                            <td><input type='text' value='{{$form["validation"] ?? ""}}' class='form-control validation' onclick="showValidationSuggest(this)"
                                       onkeyup="showValidationSuggestLike(this)" name='validation[]' value='required' placeholder='Enter Laravel Validation'/>
                            </td>
                            <td>
                                <select class='form-control width' name='width[]' style="width: 70px;">
                                    @for($i=10;$i>=1;$i--)
                                        <option {{ (isset($form['width']) && $form['width'] == "col-sm-$i")?"selected":"" }} value='col-sm-{{$i}}'>{{$i}}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <a class='btn btn-primary btn-options' href='javascript:;'><i class='fa fa-cog'></i> Options</a>
                                <div class='option_area' style="display: none">
                                    <?php
                                    // Get the field type with fallback to text
                                    $type = isset($form["type"]) ? ($form["type"] ?: "text") : "text";

                                    // Try multiple potential paths for type components
                                    $paths = [
                                        // First try webillium package path
                                        base_path('vendor/webtunel/webilliumcms/src/views/default/type_components/'.$type.'/info.json'),
                                        // Then try original crudbooster path
                                        base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/info.json'),
                                        // Then try local path
                                        base_path('resources/views/vendor/crudbooster/type_components/'.$type.'/info.json')
                                    ];

                                    $types = null;
                                    foreach ($paths as $types_path) {
                                        if(file_exists($types_path)) {
                                            $types = file_get_contents($types_path);
                                            $types = json_decode($types);
                                            break;
                                        }
                                    }

                                    // If no info file found, create a basic structure
                                    if (!$types) {
                                        $types = (object)[
                                            'title' => ucfirst($type),
                                            'alert' => 'This is a basic '.$type.' input field'
                                        ];
                                    }

                                    if($types):
                                    ?>

                                    @if(isset($type_info) && isset($type_info->alert))
                                        <div class="alert alert-warning">
                                            {!! $type_info->alert !!}
                                        </div>
                                    @endif

                                    <?php
                                    if(isset($type_info) && isset($type_info->attribute) && isset($type_info->attribute->required)):
                                    foreach($type_info->attribute->required as $key=>$val):
                                    @$value = isset($form[$key]) ? $form[$key] : '';
                                    if(is_object($val)):

                                    if(isset($val->type) && $val->type == 'radio'):
                                    ?>
                                    <div class="form-group">
                                        <label>{{$key}}</label>
                                        @foreach($val->enum as $enum)
                                            <input type="radio" name="option[{{$index}}][{{$key}}]"
                                                   {{ ($enum == $value)?"checked":"" }} value="{{$enum}}"> {{$enum}}
                                        @endforeach

                                    </div>

                                    <?php else:?>

                                    <div class="form-group">
                                        <label>{{$key}}</label>
                                        <input type="text" name="option[{{$index}}][{{$key}}]" placeholder="{{$val->placeholder}}" value="{{$value}}"
                                               class="form-control">
                                    </div>
                                    <?php endif;?>
                                    <?php else:?>

                                    <div class="form-group">
                                        <label>{{$key}}</label>
                                        <input type="text" name="option[{{$index}}][{{$key}}]" placeholder="{{$val}}" value="{{$value}}" class="form-control">
                                    </div>

                                    <?php endif;?>
                                    <?php endforeach; endif;?>



                                    <?php
                                    if(isset($type_info) && isset($type_info->attribute) && isset($type_info->attribute->requiredOne)):
                                    foreach($type_info->attribute->requiredOne as $key=>$val):
                                    @$value = isset($form[$key]) ? $form[$key] : '';
                                    ?>
                                    <div class="form-group">
                                        <label>{{$key}}</label>
                                        <input type="text" name="option[{{$index}}][{{$key}}]" placeholder="{{$val}}" value="{{$value}}" class="form-control">
                                    </div>
                                    <?php endforeach; endif;?>

                                    <?php
                                    if(isset($type_info) && isset($type_info->attribute) && isset($type_info->attribute->optional)):
                                    foreach($type_info->attribute->optional as $key=>$val):
                                    @$value = isset($form[$key]) ? $form[$key] : '';

                                    ?>
                                    <div class="form-group">
                                        <label>{{$key}}</label>
                                        @if(is_object($val) && property_exists($val, 'type') && $val->type == 'textarea')
                                            <textarea type="text" name="option[{{$index}}][{{$key}}]" placeholder="{{$val->placeholder}}"
                                                      class="form-control">{{$value}}</textarea>
                                        @else
                                            <input type="text" name="option[{{$index}}][{{$key}}]" placeholder="{{$val}}" value="{{$value}}"
                                                   class="form-control">
                                        @endif
                                    </div>
                                    <?php endforeach; endif;?>


                                    <?php endif;?>
                                </div>
                            </td>
                            <td>
                                <a href="javascript:void(0)" class="btn btn-info btn-plus"><i class='fa fa-plus'></i></a>
                                <a href="javascript:void(0)" class="btn btn-danger btn-delete"><i class='fa fa-trash'></i></a><br>
                                <a href="javascript:void(0)" class="btn btn-success btn-up"><i class='fa fa-arrow-up'></i></a>
                                <a href="javascript:void(0)" class="btn btn-success btn-down"><i class='fa fa-arrow-down'></i></a>
                            </td>
                        </tr>
                        <?php $index++;?>
                    @endforeach

                    <tr id='tr-sample' style="display: none">
                        <td><input type='text' placeholder="Input field label" onclick='showColumnSuggest(this)' onkeyup="showColumnSuggestLike(this)"
                                   class='form-control labels' name='label[]'/></td>
                        <td><input type='text' placeholder="Input field name" onclick='showNameSuggest(this)' onkeyup="showNameSuggestLike(this)"
                                   class='form-control name' name='name[]'/></td>
                        <td><input type='text' placeholder="Input field type" onclick='showTypeSuggest(this)' onkeyup="showTypeSuggestLike(this)"
                                   class='form-control type' name='type[]'/></td>
                        <td><input type='text' class='form-control validation' onclick="showValidationSuggest(this)" onkeyup="showValidationSuggestLike(this)"
                                   name='validation[]' value='required' placeholder='Enter Laravel Validation'/></td>
                        <td>
                            <select class='form-control width' name='width[]' style="width: 70px;">
                                @for($i=10;$i>=1;$i--)
                                    <option {{ ($i==9)?"selected":"" }} value='col-sm-{{$i}}'>{{$i}}</option>
                                @endfor
                            </select>
                        </td>
                        <td>
                            <a class='btn btn-primary btn-options' href='#'><i class='fa fa-cog'></i> Options</a>
                            <div class='option_area' style="display: none">

                            </div>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="btn btn-info btn-plus"><i class='fa fa-plus'></i></a>
                            <a href="javascript:void(0)" class="btn btn-danger btn-delete"><i class='fa fa-trash'></i></a><br>
                            <a href="javascript:void(0)" class="btn btn-success btn-up"><i class='fa fa-arrow-up'></i></a>
                            <a href="javascript:void(0)" class="btn btn-success btn-down"><i class='fa fa-arrow-down'></i></a>
                        </td>
                    </tr>


                    </tbody>
                </table>

        </div>
        <div class="box-footer">
            <div align="right" style="float: right;">
                <button type="button" onclick="location.href='{{CRUDBooster::mainpath('step2').'/'.$id}}'" class="btn btn-default">&laquo; Back</button>
                <input type="submit" class="btn btn-primary" value="Step 4 &raquo;">
            </div>
        </div>
        </form>
    </div>


@endsection
