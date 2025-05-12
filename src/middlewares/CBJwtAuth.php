<?php

namespace webtunel\webilliumcms\middlewares;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;

class CBJwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get authorization header
        $auth_header = $request->header('Authorization');
        if (!$auth_header) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Authorization header not found!'
            ], 401);
        }

        // Parse token
        if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Token not found in Authorization header!'
            ], 401);
        }

        $token = $matches[1];
        if (!$token) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Token not provided!'
            ], 401);
        }

        try {
            // Decode token
            $jwt_secret = config('crudbooster.JWT_SECRET', env('JWT_SECRET', 'webillium_jwt_secret_key'));
            $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
            
            // Get user ID
            $user_id = $decoded->sub;
            
            // Check if token is in cache
            $cached_token = Cache::get("jwt_token_" . $user_id);
            if (!$cached_token || $cached_token['token'] !== $token) {
                return response()->json([
                    'api_status' => 0,
                    'api_message' => 'Invalid token!'
                ], 401);
            }
            
            // Optional: Verify IP and User Agent for extra security
            $verify_ip = config('crudbooster.JWT_VERIFY_IP', true);
            $verify_user_agent = config('crudbooster.JWT_VERIFY_USER_AGENT', true);
            
            if ($verify_ip && $cached_token['ip'] !== $request->ip()) {
                return response()->json([
                    'api_status' => 0,
                    'api_message' => 'Invalid token for this IP address!'
                ], 401);
            }
            
            if ($verify_user_agent && $cached_token['user_agent'] !== $request->header('User-Agent')) {
                return response()->json([
                    'api_status' => 0,
                    'api_message' => 'Invalid token for this browser!'
                ], 401);
            }
            
            // Set user_id in session for CRUDBooster::myId() to work
            Session::put('admin_id', $user_id);
            
            // Continue with request
            return $next($request);
            
        } catch (ExpiredException $e) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Token has expired!',
                'error' => 'expired'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'api_status' => 0,
                'api_message' => 'Invalid token!',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}