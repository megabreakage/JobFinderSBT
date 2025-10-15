<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\JobSeeker;
use App\Models\Company;
use App\Models\UserCompanyRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyEmail', 'verifyPhone', 'forgotPassword', 'resetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        // Check if user exists and is active
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['error' => 'Account is deactivated'], 401);
        }

        // Check if account is locked
        if ($user->locked_until && $user->locked_until > now()) {
            return response()->json(['error' => 'Account is temporarily locked'], 401);
        }

        if (!$token = auth()->attempt($credentials)) {
            // Increment login attempts
            $user->increment('login_attempts');

            // Lock account after 5 failed attempts
            if ($user->login_attempts >= 5) {
                $user->update(['locked_until' => now()->addMinutes(15)]);
                return response()->json(['error' => 'Account locked due to too many failed attempts'], 401);
            }

            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Reset login attempts on successful login
        $user->update([
            'login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        return $this->respondWithToken($token);
    }

    /**
     * Register a new user.
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            \DB::beginTransaction();

            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'uuid' => Str::uuid(),
            ]);

            // Assign role
            $user->assignRole($request->role);

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

            \DB::commit();

            $token = auth()->login($user);

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user->load('roles'),
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['error' => 'Registration failed: ' . $e->getMessage()], 500);
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
     * Get the token array structure.
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()->load('roles'),
        ]);
    }
}
