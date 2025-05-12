<?php

namespace webtunel\webilliumcms\controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use webtunel\webilliumcms\helpers\CRUDBooster;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiJwtAuthController extends Controller
{
    private $ttl = 1440; // Token lifetime in minutes (24 hours)
    
    /**
     * Login with JWT
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLogin()
    {
        // Validate inputs
        $validator = Validator::make(Request::all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Validation error!',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user by email
        $user = DB::table('cms_users')
            ->where('email', Request::input('email'))
            ->where('status', 'Active')
            ->first();

        if (!$user) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Email or password is invalid!'
            ], 401);
        }

        // Verify password
        if (!Hash::check(Request::input('password'), $user->password)) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Email or password is invalid!'
            ], 401);
        }

        // Create JWT token
        $payload = [
            'iss' => url('/'), // Issuer
            'sub' => $user->id, // Subject (user ID)
            'iat' => time(), // Issued at
            'exp' => time() + ($this->ttl * 60), // Expiration time
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'privileges' => $this->getUserPrivileges($user->id),
                'photo' => $user->photo ? asset($user->photo) : null
            ]
        ];

        try {
            $jwt_secret = config('crudbooster.JWT_SECRET', env('JWT_SECRET', 'webillium_jwt_secret_key'));
            $token = JWT::encode($payload, $jwt_secret, 'HS256');

            // Store token in cache for validation
            Cache::put("jwt_token_" . $user->id, [
                'token' => $token,
                'ip' => Request::ip(),
                'user_agent' => Request::header('User-Agent')
            ], $this->ttl);

            return response()->json([
                'api_status' => 1,
                'api_message' => 'Login successful',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => $this->ttl * 60,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'photo' => $user->photo ? asset($user->photo) : null
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Failed to generate token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh JWT token
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRefreshToken()
    {
        $auth_header = Request::header('Authorization');
        if (!$auth_header) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Authorization header not found!'
            ], 401);
        }

        list($token_type, $token) = explode(' ', $auth_header, 2);
        if (strtoupper($token_type) != 'BEARER') {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Token type must be Bearer!'
            ], 401);
        }

        try {
            $jwt_secret = config('crudbooster.JWT_SECRET', env('JWT_SECRET', 'webillium_jwt_secret_key'));
            $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
            
            // Get user data
            $user_id = $decoded->sub;
            $user = DB::table('cms_users')->where('id', $user_id)->first();
            
            if (!$user) {
                return response()->json([
                    'api_status' => 0,
                    'api_message' => 'User not found!'
                ], 404);
            }
            
            // Create new token
            $payload = [
                'iss' => url('/'),
                'sub' => $user->id,
                'iat' => time(),
                'exp' => time() + ($this->ttl * 60),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'privileges' => $this->getUserPrivileges($user->id),
                    'photo' => $user->photo ? asset($user->photo) : null
                ]
            ];
            
            $new_token = JWT::encode($payload, $jwt_secret, 'HS256');
            
            // Update token in cache
            Cache::put("jwt_token_" . $user->id, [
                'token' => $new_token,
                'ip' => Request::ip(),
                'user_agent' => Request::header('User-Agent')
            ], $this->ttl);
            
            return response()->json([
                'api_status' => 1,
                'api_message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $new_token,
                    'token_type' => 'Bearer',
                    'expires_in' => $this->ttl * 60
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Invalid token!',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    /**
     * Get user details from token
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMe()
    {
        try {
            $user_id = CRUDBooster::myId();
            if (!$user_id) {
                return response()->json([
                    'api_status' => 0,
                    'api_message' => 'Unauthorized access!'
                ], 401);
            }
            
            $user = DB::table('cms_users')->where('id', $user_id)->first();
            if (!$user) {
                return response()->json([
                    'api_status' => 0,
                    'api_message' => 'User not found!'
                ], 404);
            }
            
            return response()->json([
                'api_status' => 1,
                'api_message' => 'Success',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'photo' => $user->photo ? asset($user->photo) : null,
                        'privileges' => $this->getUserPrivileges($user->id)
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Failed to get user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout and invalidate token
     * @return \Illuminate\Http\JsonResponse
     */
    public function postLogout()
    {
        try {
            $user_id = CRUDBooster::myId();
            if ($user_id) {
                Cache::forget("jwt_token_" . $user_id);
            }
            
            return response()->json([
                'api_status' => 1,
                'api_message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Failed to logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user privileges
     * @param int $user_id
     * @return array
     */
    private function getUserPrivileges($user_id)
    {
        $user = DB::table('cms_users')->find($user_id);
        $privileges = [];
        
        if ($user->id_cms_privileges) {
            // Get privilege name
            $privilege = DB::table('cms_privileges')->find($user->id_cms_privileges);
            if ($privilege) {
                $privileges['role'] = $privilege->name;
            }
            
            // Get privileges list
            $roles = DB::table('cms_privileges_roles')
                ->join('cms_moduls', 'cms_moduls.id', '=', 'cms_privileges_roles.id_cms_moduls')
                ->where('cms_privileges_roles.id_cms_privileges', $user->id_cms_privileges)
                ->select('cms_moduls.name', 'cms_privileges_roles.is_create', 'cms_privileges_roles.is_read', 
                         'cms_privileges_roles.is_edit', 'cms_privileges_roles.is_delete')
                ->get();
            
            $priv_list = [];
            foreach ($roles as $role) {
                $priv_list[$role->name] = [
                    'create' => (bool) $role->is_create,
                    'read' => (bool) $role->is_read,
                    'edit' => (bool) $role->is_edit,
                    'delete' => (bool) $role->is_delete
                ];
            }
            
            $privileges['modules'] = $priv_list;
        }
        
        return $privileges;
    }
}