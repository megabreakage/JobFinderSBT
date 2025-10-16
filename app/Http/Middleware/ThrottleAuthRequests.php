<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleAuthRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Create a unique key based on IP address and email (if provided)
        $key = $this->resolveRequestSignature($request);
        
        // Auth endpoints: 5 attempts per minute
        $maxAttempts = 5;
        $decayMinutes = 1;
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TOO_MANY_REQUESTS',
                    'message' => 'Too many authentication attempts. Please try again in ' . $seconds . ' seconds.',
                    'details' => [
                        'retry_after' => $seconds,
                    ],
                ],
                'timestamp' => now()->toIso8601String()
            ], 429)->header('Retry-After', $seconds);
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
        ]);
        
        return $response;
    }
    
    /**
     * Resolve the request signature for rate limiting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function resolveRequestSignature(Request $request): string
    {
        // Use IP address and email/phone if provided for more granular rate limiting
        $identifier = $request->ip();
        
        if ($request->has('email')) {
            $identifier .= '|' . $request->input('email');
        } elseif ($request->has('phone')) {
            $identifier .= '|' . $request->input('phone');
        }
        
        return 'auth_throttle:' . sha1($identifier);
    }
}
