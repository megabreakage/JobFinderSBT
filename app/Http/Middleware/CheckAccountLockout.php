<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountLockout
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if a user account is locked before allowing login.
     * It can work in two modes:
     * 1. Pre-authentication: Check lockout status based on email/phone in request
     * 2. Post-authentication: Check lockout status for authenticated user
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;
        
        // Try to get authenticated user first
        if ($request->user()) {
            $user = $request->user();
        } 
        // For login attempts, check by email or phone
        elseif ($request->has('email')) {
            $user = User::where('email', $request->input('email'))->first();
        } elseif ($request->has('phone')) {
            $user = User::where('phone', $request->input('phone'))->first();
        }
        
        // If no user found, let the request continue (will fail at authentication)
        if (!$user) {
            return $next($request);
        }
        
        // Check if account is locked
        if ($user->locked_until && now()->lessThan($user->locked_until)) {
            $remainingSeconds = now()->diffInSeconds($user->locked_until);
            $remainingMinutes = ceil($remainingSeconds / 60);
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ACCOUNT_LOCKED',
                    'message' => 'Your account has been temporarily locked due to multiple failed login attempts.',
                    'details' => [
                        'locked_until' => $user->locked_until->toIso8601String(),
                        'remaining_seconds' => $remainingSeconds,
                        'remaining_minutes' => $remainingMinutes,
                    ],
                ],
                'timestamp' => now()->toIso8601String()
            ], 423); // 423 Locked status code
        }
        
        // If lock period has expired, clear the lock
        if ($user->locked_until && now()->greaterThanOrEqualTo($user->locked_until)) {
            $user->update([
                'locked_until' => null,
                'failed_login_attempts' => 0,
            ]);
        }
        
        return $next($request);
    }
}
