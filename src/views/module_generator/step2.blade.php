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
            <i class="fa fa-database"></i> Module Generator (Table Display)

            <ul class="nav nav-tabs" style="margin-top: 20px;">
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep1')."/".$id}}"><i class='fa fa-info'></i> Step 1 - Module Information</a></li>
                <li role="presentation"><a class="btn btn-primary" href="{{Route('ModulsControllerGetStep2')."/".$id}}"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep3')."/".$id}}"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
                <li role="presentation"><a class="btn btn-secondary" href="{{Route('ModulsControllerGetStep4')."/".$id}}"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
            </ul>
        </h1>
    </div>
    @push('head')
        <style>
            .table-display tbody tr td {
                position: relative;
            }

            .sub {
                position: absolute;
                top: 35px; /* Position below the input field */
                left: 15px;
                padding: 0;
                margin: 0;
                list-style-type: none;
                max-height: 300px;
                overflow-y: auto;
                z-index: 100;
                background: white;
                border: 1px solid #ccc;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                width: 220px;
                display: block;
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

            /* Ensure proper z-index for dropdown menus */
            .table-display {
                position: relative;
                z-index: 1;
            }

            .btn-drag {
                cursor: move;
            }
        </style>
    @endpush

    @push('bottom')
        <script>
            var columns = {!! json_encode($columns) !!};
            var tables = {!! json_encode($table_list) !!};

            function ucwords(str) {
                return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                    return $1.toUpperCase();
                });
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

            function showTable(t) {
                t = $(t);
                t.next("ul").remove();
                var list = '';

                if (tables && tables.length > 0) {
                    $.each(tables, function (i, obj) {
                        if (typeof obj === 'string') {
                            list += "<li>" + obj + "</li>";
                        }
                    });
                } else {
                    // Default tables if none available
                    list = "<li>users</li><li>products</li><li>categories</li>";
                }

                if (list === '') {
                    list = "<li>No tables available</li>";
                }

                t.after("<ul class='sub'>" + list + "</ul>");
                // Force display the dropdown
                t.next("ul").css('display', 'block');
            }

            function showTableLike(t) {
                t = $(t);
                var v = t.val().toLowerCase();

                t.next("ul").remove();
                if (!v) {
                    // Show all tables if search is empty
                    showTable(t);
                    return;
                }

                var list = '';
                if (tables && tables.length > 0) {
                    $.each(tables, function (i, obj) {
                        if (typeof obj === 'string' && obj.toLowerCase().includes(v)) {
                            list += "<li>" + obj + "</li>";
                        }
                    });
                }

                if (list === '') {
                    list = "<li>No matching tables found</li>";
                }

                t.after("<ul class='sub'>" + list + "</ul>");
                // Force display the dropdown
                t.next("ul").css('display', 'block');
            }

            function showTableFieldLike(t) {
                t = $(t);
                var table = t.parent().parent().find('.join_table').val();
                var v = t.val().toLowerCase();

                t.next("ul").remove();

                if (!table) {
                    t.after("<ul class='sub'><li>Please select a table first</li></ul>");
                    t.next("ul").css('display', 'block');
                    return false;
                }

                if (!v) {
                    // If empty search, show all fields
                    showTableField(t);
                    return;
                }

                t.after("<ul class='sub'><li><i class='fa fa-spin fa-spinner'></i> Searching columns in " + table + "...</li></ul>");
                t.next("ul").css('display', 'block');

                $.get("{{CRUDBooster::mainpath('table-columns')}}/" + table, function (response) {
                    t.next("ul").remove();
                    var list = '';

                    // Handle different response formats
                    if (response) {
                        if (typeof response === 'object' && response.error) {
                            // Show error message
                            list += "<li>Error: " + response.error + "</li>";
                        } else {
                            $.each(response, function (i, obj) {
                                // Handle both string and object formats
                                var columnName = (typeof obj === 'string') ? obj : (obj.column_name || obj);

                                // Ensure columnName is a string
                                columnName = String(columnName);

                                // Check if the column name contains the search value
                                if (columnName.toLowerCase().includes(v)) {
                                    list += "<li>" + columnName + "</li>";
                                }
                            });
                        }
                    }

                    if (list === '') {
                        list = "<li>No matching columns found</li>";
                    }

                    t.after("<ul class='sub'>" + list + "</ul>");
                    t.next("ul").css('display', 'block');
                }).fail(function(xhr, status, error) {
                    t.next("ul").remove();
                    t.after("<ul class='sub'><li>Error loading columns: " + error + "</li></ul>");
                    t.next("ul").css('display', 'block');
                });
            }

            function showTableField(t) {
                t = $(t);
                var table = t.parent().parent().find('.join_table').val();

                t.next("ul").remove();

                if (!table) {
                    t.after("<ul class='sub'><li>Please select a table first</li></ul>");
                    t.next("ul").css('display', 'block');
                    return false;
                }

                t.after("<ul class='sub'><li><i class='fa fa-spin fa-spinner'></i> Loading columns from " + table + "...</li></ul>");
                t.next("ul").css('display', 'block');

                $.get("{{CRUDBooster::mainpath('table-columns')}}/" + table, function (response) {
                    t.next("ul").remove();
                    var list = '';

                    // Handle different response formats
                    if (response) {
                        if (typeof response === 'object' && response.error) {
                            // Show error message
                            list += "<li>Error: " + response.error + "</li>";
                        } else {
                            $.each(response, function (i, obj) {
                                // Handle both string and object formats
                                var columnName = (typeof obj === 'string') ? obj : (obj.column_name || obj);
                                // Ensure columnName is a string
                                columnName = String(columnName);
                                list += "<li>" + columnName + "</li>";
                            });
                        }
                    }

                    if (list === '') {
                        list = "<li>No columns found in table '" + table + "'</li>";
                    }

                    t.after("<ul class='sub'>" + list + "</ul>");
                    t.next("ul").css('display', 'block');

                    // Log debug info
                    console.log("Retrieved columns for table:", table, response);
                }).fail(function(xhr, status, error) {
                    t.next("ul").remove();
                    t.after("<ul class='sub'><li>Error loading columns: " + error + "</li></ul>");
                    t.next("ul").css('display', 'block');
                    console.error("Error fetching columns for table:", table, error);
                });
            }

            $(function () {


                $(document).on('click', '.btn-plus', function () {
                    var tr_parent = $(this).parent().parent('tr');
                    var clone = $('#tr-sample').clone();
                    clone.removeAttr('id');
                    tr_parent.after(clone);
                    $('.table-display tr').not('#tr-sample').show();
                })

                //init row
                $('.btn-plus').last().click();

                $(document).mouseup(function (e) {
                    var container = $(".sub");
                    // Don't hide if clicking on an input that should show the dropdown
                    var isInputClick = $(e.target).is('input[name="join_table[]"]') ||
                                    $(e.target).is('input[name="join_field[]"]');

                    if (!container.is(e.target) &&
                        container.has(e.target).length === 0 &&
                        !isInputClick) {
                        container.hide();
                    }
                });

                $(document).on('click', '.sub li', function () {
                    var v = $(this).text();
                    $(this).parent('ul').prev('input[type=text]').val(v);
                    $(this).parent('ul').remove();
                })

                $(document).on('click', '.table-display .btn-delete', function () {
                    $(this).parent().parent().remove();
                })

                $(document).on('click', '.table-display .btn-up', function () {
                    var tr = $(this).parent().parent();
                    var trPrev = tr.prev('tr');
                    if (trPrev.length != 0) {

                        tr.prev('tr').before(tr.clone());
                        tr.remove();
                    }
                })

                $(document).on('click', '.table-display .btn-down', function () {
                    var tr = $(this).parent().parent();
                    var trPrev = tr.next('tr');
                    if (trPrev.length != 0) {

                        tr.next('tr').after(tr.clone());
                        tr.remove();
                    }
                })

                $(document).on('change', '.is_image', function () {
                    var tr = $(this).parent().parent();
                    if ($(this).val() == 1) {
                        tr.find('.is_download').val(0);
                    }
                })

                $(document).on('change', '.is_download', function () {
                    var tr = $(this).parent().parent();
                    if ($(this).val() == 1) {
                        tr.find('.is_image').val(0);
                    }
                })

            })
        </script>
    @endpush

    <div class="box box-default">

        <div class="box-body">

            <div class="alert alert-info">
                <strong>Warning</strong>. Make sure that your column format are normally, unless using this Tool maybe make your current configuration broken,
                because this Tool will replace your configuration.
            </div>

            <form method="post" action="{{Route('ModulsControllerPostStep3')}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$id}}">

                <table class="table-display table table-striped">
                    <thead>
                    <tr>
                        <th>Column</th>
                        <th>Name</th>
                        <th colspan='2'>Join (Optional)</th>
                        <th>CallbackPHP</th>
                        <th width="90px">Width (px)</th>
                        <th width='80px'>Image</th>
                        <th width='80px'>Download</th>
                        <th width="180px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($cb_col) && $cb_col)
                        @foreach($cb_col as $c)
                            <tr>
                                <td><input value='{{$c["label"] ?? ""}}' type='text' name='column[]' onclick='showColumnSuggest(this)'
                                           onKeyUp='showColumnSuggestLike(this)' placeholder='Column Name' class='column form-control notfocus' value=''/></td>
                                <td><input value='{{$c["name"] ?? ""}}' type='text' name='name[]' onclick='showNameSuggest(this)' onKeyUp='showNameSuggestLike(this)'
                                           placeholder='Field Name' class='name form-control notfocus' value=''/></td>
                                <td><input value='{{ @explode(",",($c["join"] ?? ""))[0] }}' type='text' name='join_table[]' onclick='showTable(this)'
                                           onKeyUp='showTableLike(this)' placeholder='Table Name' class='join_table form-control notfocus' value=''/></td>
                                <td><input value='{{ @explode(",",($c["join"] ?? ""))[1] }}' type='text' name='join_field[]' onclick='showTableField(this)'
                                           onKeyUp='showTableFieldLike(this)' placeholder='Field Name Shown' class='join_field form-control notfocus' value=''/>
                                </td>
                                <td><input type='text' name='callbackphp[]' class='form-control callbackphp notfocus' value='{{$c["callback_php"] ?? ""}}'
                                           placeholder="Optional"/></td>
                                <td><input style="width:70px;" value='{{($c["width"] ?? 0) ? : 0}}' type='number' name='width[]' class='form-control'/></td>
                                <td>
                                    <select class='form-control is_image' name='is_image[]' style="width:70px;">
                                        <option {{ (!(isset($c['image']) && $c['image']))?"selected":""}} value='0'>N</option>
                                        <option {{ (isset($c['image']) && $c['image'])?"selected":""}} value='1'>Y</option>
                                    </select>
                                </td>
                                <td>
                                    <select class='form-control is_download' name='is_download[]'>
                                        <option {{ (!(isset($c['download']) && $c['download']))?"selected":""}} value='0'>N</option>
                                        <option {{ (isset($c['download']) && $c['download'])?"selected":""}} value='1'>Y</option>
                                    </select>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-info btn-plus"><i class='fa fa-plus'></i></a>
                                    <a href="javascript:void(0)" class="btn btn-danger btn-delete"><i class='fa fa-trash'></i></a><br>
                                    <a href="javascript:void(0)" class="btn btn-success btn-up"><i class='fa fa-arrow-up'></i></a>
                                    <a href="javascript:void(0)" class="btn btn-success btn-down"><i class='fa fa-arrow-down'></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <tr id="tr-sample" style="display:none">
                        <td><input type='text' name='column[]' onclick='showColumnSuggest(this)' onKeyUp='showColumnSuggestLike(this)' placeholder='Column Name'
                                   class='column form-control notfocus' value=''/></td>
                        <td><input type='text' name='name[]' onclick='showNameSuggest(this)' onKeyUp='showNameSuggestLike(this)' placeholder='Field Name'
                                   class='name form-control notfocus' value=''/></td>
                        <td><input type='text' name='join_table[]' onclick='showTable(this)' onKeyUp='showTableLike(this)' placeholder='Table Name'
                                   class='join_table form-control notfocus' value=''/></td>
                        <td><input type='text' name='join_field[]' onclick='showTableField(this)' onKeyUp='showTableFieldLike(this)'
                                   placeholder='Field Name Shown' class='join_field form-control notfocus' value=''/></td>
                        <td><input type='text' name='callbackphp[]' class='form-control callbackphp notfocus' value='' placeholder="Optional"/></td>
                        <td><input style="width:70px;" type='number' name='width[]' value='0' class='form-control'/></td>
                        <td>
                            <select class='form-control is_image' name='is_image[]' style="width:70px;">
                                <option value='0'>N</option>
                                <option value='1'>Y</option>
                            </select>
                        </td>
                        <td>
                            <select class='form-control is_download' name='is_download[]'>
                                <option value='0'>N</option>
                                <option value='1'>Y</option>
                            </select>
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
                <button type="button" onclick="location.href='{{CRUDBooster::mainpath('step1').'/'.$id}}'" class="btn btn-default">&laquo; Back</button>
                <input type="submit" class="btn btn-primary" value="Step 3 &raquo;">
            </div>
        </div>
        </form>
    </div>


@endsection
