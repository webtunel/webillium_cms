<!DOCTYPE html>
<html>
<head>
    <title>API Documentation</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"
          integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>API Documentation {{ get_setting("appname") }}</h1>
    </div>

    <div class='box'>

        <div class='box-body'>

            <style>
                .table-api tbody tr td a {
                    color: #db0e00;
                    font-family: arial;
                }
                .nav-tabs-custom>.nav-tabs>li.active {
                    border-top-color: #3c8dbc;
                }
                .nav-tabs-custom>.nav-tabs>li.active>a {
                    border-top-color: transparent;
                    border-left-color: #f4f4f4;
                    border-right-color: #f4f4f4;
                }
                pre {
                    display: block;
                    padding: 9.5px;
                    margin: 0 0 10px;
                    font-size: 13px;
                    line-height: 1.42857143;
                    color: #333;
                    word-break: break-all;
                    word-wrap: break-word;
                    background-color: #f5f5f5;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }
            </style>

            <script>
                $(function () {
                    $(".link_name_api").click(function () {
                        $(".detail_api").slideUp();
                        $(this).parent("td").find(".detail_api").slideDown();
                    })
                    $(".selected_text").each(function () {
                        var n = $(this).text();
                        if (n.indexOf('api_') == 0) {
                            $(this).attr('class', 'selected_text text-danger');
                        }
                    })
                })
            </script>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#apilist" aria-controls="apilist" role="tab" data-toggle="tab">API List</a></li>
                <li role="presentation"><a href="#jwt" aria-controls="jwt" role="tab" data-toggle="tab">JWT Authentication</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="apilist">

            <div class='form-group'>
                <label>API BASE URL</label>
                <input type='text' readonly class='form-control' title='Hanya klik dan otomatis copy to clipboard (kecuali Safari)'
                       onClick="this.setSelectionRange(0, this.value.length); document.execCommand('copy');" value='{{url('api')}}'/>
            </div>
            <table class='table table-striped table-api table-bordered'>
                <thead>
                <tr class='info'>
                    <th width='2%'>No</th>
                    <th>API Name
                        <span class='pull-right'>
                      <a class='btn btn-xs btn-warning' target="_blank" href='{{url("download-documentation-postman")}}'>Export For POSTMAN <sup>Beta</sup></a>
                    </span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 0;?>
                <tr>
                    <td>{{ ++$no  }}</td>
                    <td>
                        <a href='javascript:void(0)' title='API Authentication' style='color:red !important;' class='link_name_api'>Authentication (Request Token)</a> &nbsp;
                        <div class='detail_api' style='display:none'>
                            <table class='table table-bordered'>
                                <tr>
                                    <td width='12%'><strong>URL</strong></td>
                                    <td><input title='Click and copied !' type='text' class='form-control' readonly
                                               onClick="this.setSelectionRange(0, this.value.length); document.execCommand('copy');"
                                               value="/get-token"/></td>
                                </tr>
                                <tr>
                                    <td><strong>METHOD</strong></td>
                                    <td>POST</td>
                                </tr>
                                <tr>
                                    <td><strong>PARAMETER</strong></td>
                                    <td>
                                        <table class='table table-bordered table-hover'>
                                            <thead>
                                            <tr class='active'>
                                                <th width="3%">No</th>
                                                <th width="5%">Type</th>
                                                <th>Parameter Names</th>
                                                <th>Description / Validate / Rule</th>
                                                <th>Mandatory</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td width="5%"><em>String</em></td>
                                                <td>secret</td>
                                                <td></td>
                                                <td>Yes</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>RESPONSE</strong></td>
                                    <td>
                                        <table class='table table-bordered table-hover'>
                                            <thead>
                                            <tr class='active'>
                                                <th width="3%">No</th>
                                                <th width="5%">Type</th>
                                                <th>Response Names</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i = 1;?>
                                            <tr>
                                                <td>1</td>
                                                <td><em>integer</em></td>
                                                <td>api_status</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td><em>string</em></td>
                                                <td>api_message</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td><em>object</em></td>
                                                <td>data</td>
                                            </tr>
                                            <tr>
                                                <td>3.1</td>
                                                <td><em>string</em></td>
                                                <td>access_token</td>
                                            </tr>
                                            <tr>
                                                <td>3.2</td>
                                                <td><em>integer</em></td>
                                                <td>expiry</td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
                @foreach($apis as $api)
                    <?php
                    $parameters = ($api->parameters) ? unserialize($api->parameters) : array();
                    $responses = ($api->responses) ? unserialize($api->responses) : array();
                    ?>
                    <tr>
                        <td><?= ++$no;?></td>
                        <td>
                            <a href='javascript:void(0)' title='API {{$ac->nama}}' style='color:#009fe3' class='link_name_api'><?=$api->nama;?></a> &nbsp;
                            <div class='detail_api' style='display:none'>
                                <table class='table table-bordered'>
                                    <tr>
                                        <td width='12%'><strong>URL</strong></td>
                                        <td><input title='Click and copied !' type='text' class='form-control' readonly
                                                   onClick="this.setSelectionRange(0, this.value.length); document.execCommand('copy');"
                                                   value="/{{$api->permalink}}"/></td>
                                    </tr>
                                    <tr>
                                        <td><strong>METHOD</strong></td>
                                        <td>{{strtoupper($api->method_type)}}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>HEADERS</strong></td>
                                        <td>
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                <tr class="active">
                                                    <th>Name</th>
                                                    <th>Value</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>Authorization</td>
                                                    <td>Bearer <span style="color:red">{access_token}</span></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>PARAMETER</strong></td>
                                        <td>
                                            <table class='table table-bordered table-hover'>
                                                <thead>
                                                <tr class='active'>
                                                    <th width="3%">No</th>
                                                    <th width="5%">Type</th>
                                                    <th>Parameter Names</th>
                                                    <th>Description / Validate / Rule</th>
                                                    <th>Mandatory</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i = 0;?>
                                                @foreach($parameters as $param)
                                                    @if($param['used'])
                                                        <?php
                                                        $param_exception = ['in', 'not_in', 'digits_between'];
                                                        if ($param['config'] && substr($param['config'], 0, 1) != '*' && ! in_array($param['type'], $param_exception)) continue;?>
                                                        <tr>
                                                            <td>{{++$i}}</td>
                                                            <td width="5%"><em>{{$param['type']}}</em></td>
                                                            <td>{{$param['name']}}</td>
                                                            <td>

                                                                @if(substr($param['config'],0,1) == '*')
                                                                    <span class='text-info'>{{substr($param['config'],1)}}</span>
                                                                @else
                                                                    {{$param['config']}}
                                                                @endif

                                                            </td>
                                                            <td>{!! ($param['required'])?"<span class='label label-primary'>REQUIRED</span>":"<span class='label label-default'>OPTIONAL</span>"!!}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                @if($i == 0)
                                                    <tr>
                                                        <td colspan='4' align="center"><i class='fa fa-search'></i> There is no parameter</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>RESPONSE</strong></td>
                                        <td>
                                            <table class='table table-bordered table-hover'>
                                                <thead>
                                                <tr class='active'>
                                                    <th width="3%">No</th>
                                                    <th width="5%">Type</th>
                                                    <th>Response Names</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $i = 0;?>
                                                <tr>
                                                    <td>{{ ++$i }}</td>
                                                    <td><em>integer</em></td>
                                                    <td>api_status</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ ++$i }}</td>
                                                    <td><em>string</em></td>
                                                    <td>api_message</td>
                                                </tr>

                                                @if($api->aksi == 'list')
                                                    <tr class='active'>
                                                        <td>{{ ++$i }}</td>
                                                        <td>Array</td>
                                                        <td><strong>data</strong></td>
                                                    </tr>
                                                @endif

                                                @if($api->aksi == 'detail')
                                                    <tr class='active'>
                                                        <td>{{ ++$i }}</td>
                                                        <td>Object</td>
                                                        <td><strong>data</strong></td>
                                                    </tr>
                                                @endif

                                                @php $e = 0; @endphp
                                                @if($api->aksi == 'list' || $api->aksi == 'detail')
                                                    @foreach($responses as $resp)
                                                        @if($resp['used'])
                                                            <tr>
                                                                <td>{{$i.".".(++$e)}}</td>
                                                                <td width="5%"><em>{{$resp['type']}}</em></td>
                                                                <td>{{ ($api->aksi=='list')?'- ':'' }} {{$resp['name']}}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif

                                                @if($api->aksi == 'save_add')
                                                    <tr>
                                                        <td width="5%">{{ ++$i }}</td>
                                                        <td><em>integer</em></td>
                                                        <td>id</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>DESCRIPTION</strong></td>
                                        <td><em>{!! $api->keterangan !!}</em></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>

                </div><!-- End API List tab panel -->

                <div role="tabpanel" class="tab-pane" id="jwt">
                    <div class="box-body">
                        <h3>JWT Authentication</h3>
                        <p>This API uses JWT (JSON Web Token) for authentication. JWT provides a secure way to authenticate API users without storing session data on the server.</p>

                        <h4>Authentication Endpoints</h4>
                        <div class="table-responsive">
                            <table class='table table-striped table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Endpoint</th>
                                        <th>Method</th>
                                        <th>Description</th>
                                        <th>Parameters</th>
                                        <th>Response</th>
                                        <th>Auth Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>/api/jwt/login</code></td>
                                        <td>POST</td>
                                        <td>Login with email and password to get a JWT token</td>
                                        <td>
                                            <ul>
                                                <li><code>email</code> (required): User's email</li>
                                                <li><code>password</code> (required): User's password</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <pre>{
  "api_status": 1,
  "api_message": "Login successful",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1...",
    "token_type": "Bearer",
    "expires_in": 86400,
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "photo": "..."
    }
  }
}</pre>
                                        </td>
                                        <td>No</td>
                                    </tr>
                                    <tr>
                                        <td><code>/api/jwt/refresh</code></td>
                                        <td>POST</td>
                                        <td>Refresh an existing token before it expires</td>
                                        <td>
                                            <ul>
                                                <li>Requires Authorization header with Bearer token</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <pre>{
  "api_status": 1,
  "api_message": "Token refreshed successfully",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1...",
    "token_type": "Bearer",
    "expires_in": 86400
  }
}</pre>
                                        </td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td><code>/api/jwt/me</code></td>
                                        <td>GET</td>
                                        <td>Get the current user's profile details</td>
                                        <td>
                                            <ul>
                                                <li>Requires Authorization header with Bearer token</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <pre>{
  "api_status": 1,
  "api_message": "Success",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@example.com",
      "photo": "...",
      "privileges": {
        "role": "Superadmin",
        "modules": {...}
      }
    }
  }
}</pre>
                                        </td>
                                        <td>Yes</td>
                                    </tr>
                                    <tr>
                                        <td><code>/api/jwt/logout</code></td>
                                        <td>POST</td>
                                        <td>Invalidate the current token</td>
                                        <td>
                                            <ul>
                                                <li>Requires Authorization header with Bearer token</li>
                                            </ul>
                                        </td>
                                        <td>
                                            <pre>{
  "api_status": 1,
  "api_message": "Successfully logged out"
}</pre>
                                        </td>
                                        <td>Yes</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h4>How to Use JWT Authentication</h4>
                        <ol>
                            <li>Call <code>/api/jwt/login</code> with email and password to get a token</li>
                            <li>Include the token in subsequent API requests in the Authorization header</li>
                            <li>Use <code>/api/jwt/refresh</code> before the token expires to get a new token</li>
                            <li>Call <code>/api/jwt/logout</code> to invalidate the token when done</li>
                        </ol>

                        <h4>Example: Login Request</h4>
                        <pre>POST /api/jwt/login HTTP/1.1
Host: {{request()->getHost()}}
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "your_password"
}</pre>

                        <h4>Example: Making an authenticated request</h4>
                        <pre>GET /api/some-endpoint HTTP/1.1
Host: {{request()->getHost()}}
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/json</pre>
                    </div>
                </div><!-- End JWT tab panel -->

            </div><!-- End tab content -->

        </div><!--END BODY-->
    </div><!--END BOX-->

    <hr>
    <div align="center">
        &copy; Copyright {{date('Y')}}. All Right Reserved. API Documentation
    </div>


</div>
</body>
</html>