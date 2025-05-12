@extends('crudbooster::admin_template')

@section('content')

    @push('head')
        <link href="{{ asset('vendor/crudbooster/assets/stisla/summernote-bs4.css') }}" rel="stylesheet">
    @endpush
    @push('bottom')
        <script src="{{ asset('vendor/crudbooster/assets/stisla/summernote-bs4.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.wysiwyg').summernote();
            })
        </script>
    @endpush
    @php
        $mainColor = '#6777ef';

        if(Session::get('theme_color') == 'skin-blue'){
            $mainColor = '#48cae4';
        }else if(Session::get('theme_color') == 'skin-yellow'){
            $mainColor = '#fcbf49';
        }else if(Session::get('theme_color') == 'skin-green'){
            $mainColor = '#80ed99';
        }else if(Session::get('theme_color') == 'skin-red'){
            $mainColor = '#d62828';
        }else if(Session::get('theme_color') == 'skin-yellow'){
            $mainColor = '#003049';
        }
    @endphp
    <div class="col-12 section-header section-header-primary" style="padding: 0px;">
        <div class="card mb-0">
            <div class="card-body">
                <ul class="nav nav-pills">
                    <li class="btn btn-outline-primary nav-item"><a style="color:{{$mainColor}};" href="{{ CRUDBooster::mainpath('documentation') }}"><i class='fa fa-file'></i> API Documentation </a></li>
                    <li class="btn btn-outline-primary nav-item"><a style="color:{{$mainColor}};"href="{{ CRUDBooster::mainpath('screet-key') }}"><i class='fa fa-key'></i> API Screet Key </a></li>
                    <li class="btn btn-outline-primary nav-item"><a style="color:{{$mainColor}};" href="{{ CRUDBooster::mainpath('generator') }}"><i class='fa fa-cog'></i> API Generator </a></li>
                    <li class="btn btn-outline-primary nav-item"><a style="color:{{$mainColor}};" href="{{ CRUDBooster::mainpath('jwt-auth') }}"><i class='fa fa-lock'></i> JWT Authentication </a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class='box'>
        <div class='box-header'>
            <h3 class='box-title'>JWT Authentication</h3>
        </div>
        <div class='box-body'>
            <div class="alert alert-info">
                <strong>Note:</strong> JWT (JSON Web Token) authentication provides a secure way to authenticate API users. 
                The system automatically registers the following endpoints:
            </div>

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
                            <td><code class="selected_text">/api/jwt/login</code></td>
                            <td>POST</td>
                            <td>Login with email and password to get a JWT token</td>
                            <td>
                                <ul>
                                    <li><code>email</code> (required): User's email</li>
                                    <li><code>password</code> (required): User's password</li>
                                </ul>
                            </td>
                            <td>
                                <pre class="selected_text">{
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
                            <td><code class="selected_text">/api/jwt/refresh</code></td>
                            <td>POST</td>
                            <td>Refresh an existing token before it expires</td>
                            <td>
                                <ul>
                                    <li>Requires Authorization header with Bearer token</li>
                                </ul>
                            </td>
                            <td>
                                <pre class="selected_text">{
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
                            <td><code class="selected_text">/api/jwt/me</code></td>
                            <td>GET</td>
                            <td>Get the current user's profile details</td>
                            <td>
                                <ul>
                                    <li>Requires Authorization header with Bearer token</li>
                                </ul>
                            </td>
                            <td>
                                <pre class="selected_text">{
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
                            <td><code class="selected_text">/api/jwt/logout</code></td>
                            <td>POST</td>
                            <td>Invalidate the current token</td>
                            <td>
                                <ul>
                                    <li>Requires Authorization header with Bearer token</li>
                                </ul>
                            </td>
                            <td>
                                <pre class="selected_text">{
  "api_status": 1,
  "api_message": "Successfully logged out"
}</pre>
                            </td>
                            <td>Yes</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>JWT Authentication Configuration</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ CRUDBooster::mainpath('save-jwt-config') }}">
                                @csrf
                                <div class="form-group">
                                    <label>JWT Secret Key</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="jwt_secret" value="{{ config('crudbooster.JWT_SECRET', env('JWT_SECRET', 'webillium_jwt_secret_key')) }}" placeholder="Enter JWT secret key">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="generate-secret">Generate</button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Secret key used to sign JWT tokens. Keep this secure!</small>
                                </div>
                                <div class="form-group">
                                    <label>Token TTL (minutes)</label>
                                    <input type="number" class="form-control" name="jwt_ttl" value="{{ config('crudbooster.JWT_TTL', env('JWT_TTL', 1440)) }}" min="5" placeholder="Token lifetime in minutes">
                                    <small class="form-text text-muted">How long tokens are valid (default: 1440 minutes or 24 hours)</small>
                                </div>
                                <div class="form-group">
                                    <label>Refresh TTL (minutes)</label>
                                    <input type="number" class="form-control" name="jwt_refresh_ttl" value="{{ config('crudbooster.JWT_REFRESH_TTL', env('JWT_REFRESH_TTL', 20160)) }}" min="5" placeholder="Refresh token lifetime in minutes">
                                    <small class="form-text text-muted">How long refresh tokens are valid (default: 20160 minutes or 14 days)</small>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="verify_ip" name="jwt_verify_ip" value="1" {{ config('crudbooster.JWT_VERIFY_IP', env('JWT_VERIFY_IP', true)) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="verify_ip">Verify IP Address</label>
                                    </div>
                                    <small class="form-text text-muted">If enabled, token will only be valid from the same IP address it was created from</small>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="verify_user_agent" name="jwt_verify_user_agent" value="1" {{ config('crudbooster.JWT_VERIFY_USER_AGENT', env('JWT_VERIFY_USER_AGENT', true)) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="verify_user_agent">Verify User Agent</label>
                                    </div>
                                    <small class="form-text text-muted">If enabled, token will only be valid from the same browser/device it was created from</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Configuration</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Example Usage</h4>
                        </div>
                        <div class="card-body">
                            <h5>Authentication Flow</h5>
                            <ol>
                                <li>Call <code>/api/jwt/login</code> with email and password to get a token</li>
                                <li>Include the token in subsequent API requests in the Authorization header</li>
                                <li>Use <code>/api/jwt/refresh</code> before the token expires to get a new token</li>
                                <li>Call <code>/api/jwt/logout</code> to invalidate the token when done</li>
                            </ol>

                            <h5>Example: Login Request</h5>
                            <pre class="selected_text">POST /api/jwt/login HTTP/1.1
Host: your-domain.com
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "your_password"
}</pre>

                            <h5>Example: Making an authenticated request</h5>
                            <pre class="selected_text">GET /api/some-endpoint HTTP/1.1
Host: your-domain.com
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
Content-Type: application/json</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('bottom')
<script>
    $(function() {
        $('#generate-secret').click(function() {
            // Generate a random string for JWT secret
            var randomString = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+';
            var length = 32;
            for (var i = 0; i < length; i++) {
                randomString += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            $('input[name="jwt_secret"]').val(randomString);
        });
    });
</script>
@endpush