<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Attempt to authenticate the user via JWT token
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'USER_NOT_FOUND',
                        'message' => 'User not found',
                    ],
                    'timestamp' => now()->toIso8601String()
                ], 404);
            }
            
            // Check if user account is active
            if (!$user->is_active) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'ACCOUNT_INACTIVE',
                        'message' => 'Your account has been deactivated',
                    ],
                    'timestamp' => now()->toIso8601String()
                ], 403);
            }
            
        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TOKEN_EXPIRED',
                    'message' => 'Token has expired. Please refresh your token or login again.',
                ],
                'timestamp' => now()->toIso8601String()
            ], 401);
            
        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TOKEN_INVALID',
                    'message' => 'Token is invalid',
                ],
                'timestamp' => now()->toIso8601String()
            ], 401);
            
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TOKEN_ABSENT',
                    'message' => 'Authorization token not provided',
                ],
                'timestamp' => now()->toIso8601String()
            ], 401);
        }
        
        return $next($request);
    }
}
