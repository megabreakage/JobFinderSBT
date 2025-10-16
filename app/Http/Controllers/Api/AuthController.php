<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JobSeeker;
use App\Models\Company;
use App\Models\UserCompanyRole;
use App\Services\AuthService;
use App\Services\EmailService;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected AuthService $authService;
    protected EmailService $emailService;
    protected SmsService $smsService;

    /**
     * Create a new AuthController instance.
     */
    public function __construct(
        AuthService $authService,
        EmailService $emailService,
        SmsService $smsService
    ) {
        $this->authService = $authService;
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        
        $this->middleware('auth:api', ['except' => [
            'login',
            'register',
            'verifyEmail',
            'sendOtp',
            'verifyPhone',
            'forgotPassword',
            'resetPassword'
        ]]);
    }

    /**
     * Get a JWT via given credentials.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Log failed attempt without user
            $this->logAuthentication(null, $request, 'login', false, 'User not found');
            
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials'
            ], 401);
        }

        // Check if account is active
        if (!$user->is_active) {
            $this->logAuthentication($user, $request, 'login', false, 'Account deactivated');
            
            return response()->json([
                'success' => false,
                'error' => 'Account is deactivated. Please contact support.'
            ], 401);
        }

        // Check if account is locked
        if ($this->authService->isAccountLocked($user)) {
            $this->logAuthentication($user, $request, 'login', false, 'Account locked');
            
            $lockedUntil = $user->locked_until->diffForHumans();
            return response()->json([
                'success' => false,
                'error' => "Account is temporarily locked. Please try again {$lockedUntil}."
            ], 401);
        }

        // Attempt authentication
        $credentials = $request->only('email', 'password');
        
        if (!$token = auth()->attempt($credentials)) {
            // Increment failed attempts
            $this->authService->incrementFailedAttempts($user);
            
            // Log failed attempt
            $this->logAuthentication($user, $request, 'login', false, 'Invalid password');
            
            // Check if account is now locked
            if ($this->authService->isAccountLocked($user)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Account locked due to too many failed login attempts. Please try again in 15 minutes.'
                ], 401);
            }
            
            $remainingAttempts = 5 - ($user->fresh()->failed_login_attempts ?? 0);
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials',
                'remaining_attempts' => $remainingAttempts
            ], 401);
        }

        // Reset failed attempts on successful login
        $this->authService->resetFailedAttempts($user);
        
        // Log successful authentication
        $this->logAuthentication($user, $request, 'login', true);

        return $this->respondWithToken($token);
    }

    /**
     * Register a new user.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:job-seeker,employer',

            // Company fields (required if role is employer)
            'company_name' => 'required_if:role,employer|string|max:255',
            'company_website' => 'nullable|url',
            'company_description' => 'nullable|string',
            'industry_id' => 'nullable|exists:industries,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create user using AuthService
            $user = $this->authService->createUser([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
            ], $request->role);

            // Generate email verification token
            $verificationToken = $this->authService->generateEmailVerificationToken();
            $user->update(['email_verification_token' => $verificationToken]);

            // Create job seeker profile if role is job-seeker
            if ($request->role === 'job-seeker') {
                JobSeeker::create([
                    'user_id' => $user->id,
                    'profile_completion_percentage' => 20, // Basic info completed
                ]);
            }

            // Create company and employer relationship if role is employer
            if ($request->role === 'employer') {
                $company = Company::create([
                    'name' => $request->company_name,
                    'slug' => Str::slug($request->company_name),
                    'website' => $request->company_website,
                    'description' => $request->company_description,
                    'industry_id' => $request->industry_id,
                    'uuid' => Str::uuid(),
                ]);

                UserCompanyRole::create([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                    'role_type' => 'owner',
                    'is_primary_contact' => true,
                    'can_post_jobs' => true,
                    'can_manage_applications' => true,
                    'can_manage_subscriptions' => true,
                    'joined_at' => now(),
                ]);
            }

            // Send verification email
            $this->emailService->sendVerificationEmail($user, $verificationToken);

            DB::commit();

            // Generate JWT token
            $token = auth()->login($user);

            // Log authentication
            $this->logAuthentication($user, $request, 'register', true);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully. Please check your email to verify your account.',
                'user' => $user->load('roles'),
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Registration failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Registration failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     */
    public function me()
    {
        $user = auth()->user()->load(['roles', 'jobSeeker', 'companyRoles.company']);

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Verify email address with token.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user by verification token
        $user = User::where('email_verification_token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or expired verification token'
            ], 400);
        }

        // Check if already verified
        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified'
            ], 200);
        }

        // Verify email
        $this->authService->verifyEmail($user);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ], 200);
    }

    /**
     * Send OTP to phone number.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user by phone
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Phone number not found'
            ], 404);
        }

        // Check if already verified
        if ($user->phone_verified_at) {
            return response()->json([
                'success' => false,
                'error' => 'Phone number already verified'
            ], 400);
        }

        try {
            // Generate OTP
            $otp = $this->authService->generateOtp();
            
            // Store OTP
            $this->authService->storeOtp($user, $otp);
            
            // Send OTP via SMS
            $this->smsService->sendOtp($request->phone, $otp);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your phone',
                'expires_in' => 600 // 10 minutes in seconds
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify phone number with OTP.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user by phone
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Phone number not found'
            ], 404);
        }

        // Check if already verified
        if ($user->phone_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Phone number already verified'
            ], 200);
        }

        // Verify OTP
        if (!$this->authService->verifyOtp($user, $request->otp)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid or expired OTP'
            ], 400);
        }

        // Mark phone as verified
        $this->authService->verifyPhone($user);

        return response()->json([
            'success' => true,
            'message' => 'Phone number verified successfully'
        ], 200);
    }

    /**
     * Send password reset link.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Don't reveal if email exists or not for security
            return response()->json([
                'success' => true,
                'message' => 'If your email is registered, you will receive a password reset link shortly.'
            ], 200);
        }

        try {
            // Generate password reset token
            $token = Password::createToken($user);
            
            // Send password reset email
            $this->emailService->sendPasswordResetEmail($user, $token);

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to send password reset link. Please try again.'
            ], 500);
        }
    }

    /**
     * Reset password with token.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Attempt to reset password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'error' => 'Invalid or expired reset token'
        ], 400);
    }

    /**
     * Get the token array structure.
     * 
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()->load('roles'),
        ]);
    }

    /**
     * Log authentication attempt.
     * 
     * @param User|null $user
     * @param Request $request
     * @param string $action
     * @param bool $success
     * @param string|null $failureReason
     * @return void
     */
    protected function logAuthentication(
        ?User $user,
        Request $request,
        string $action,
        bool $success,
        ?string $failureReason = null
    ): void {
        try {
            DB::table('authentication_logs')->insert([
                'user_id' => $user?->id,
                'email' => $request->email ?? $user?->email,
                'action' => $action,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'success' => $success,
                'failure_reason' => $failureReason,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log authentication', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
