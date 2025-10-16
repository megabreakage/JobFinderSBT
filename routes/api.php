<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\HrPackageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'timestamp' => now()]);
});

// Example search endpoint for search-select component
Route::get('/search', function (Request $request) {
    $query = $request->input('q', '');
    
    // Example data - replace with actual database queries
    $items = [
        ['id' => 1, 'name' => 'Software Engineer', 'category' => 'Technology'],
        ['id' => 2, 'name' => 'Product Manager', 'category' => 'Management'],
        ['id' => 3, 'name' => 'Data Analyst', 'category' => 'Analytics'],
        ['id' => 4, 'name' => 'UX Designer', 'category' => 'Design'],
        ['id' => 5, 'name' => 'Marketing Manager', 'category' => 'Marketing'],
    ];
    
    $results = collect($items)->filter(function ($item) use ($query) {
        return stripos($item['name'], $query) !== false;
    })->values();
    
    return response()->json(['results' => $results]);
});

// Authentication routes with rate limiting
Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    // Public routes
    Route::post('login', [AuthController::class, 'login'])->middleware('account.lockout');
    Route::post('register', [AuthController::class, 'register']);
    
    // Email verification
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    
    // Phone verification
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-phone', [AuthController::class, 'verifyPhone']);
    
    // Password reset
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    
    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Public job routes
Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::get('/{job}', [JobController::class, 'show']);
    Route::get('/{job}/similar', [JobController::class, 'similar']);
});

// Public company routes
Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);
    Route::get('/{company}', [CompanyController::class, 'show']);
    Route::get('/{company}/jobs', [CompanyController::class, 'jobs']);
});

// Public HR packages
Route::get('hr-packages', [HrPackageController::class, 'index']);

// Protected routes with general API rate limiting
Route::middleware(['auth:api', 'throttle:api'])->group(function () {

    // Job Seeker routes
    Route::middleware('role:job-seeker')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::post('/upload-resume', [ProfileController::class, 'uploadResume']);
            Route::delete('/resume', [ProfileController::class, 'deleteResume']);
        });

        Route::prefix('applications')->group(function () {
            Route::get('/', [ApplicationController::class, 'index']);
            Route::post('/', [ApplicationController::class, 'store']);
            Route::get('/{application}', [ApplicationController::class, 'show']);
            Route::put('/{application}', [ApplicationController::class, 'update']);
            Route::delete('/{application}', [ApplicationController::class, 'destroy']);
        });

        Route::prefix('saved-jobs')->group(function () {
            Route::get('/', [JobController::class, 'savedJobs']);
            Route::post('/{job}/save', [JobController::class, 'saveJob']);
            Route::delete('/{job}/unsave', [JobController::class, 'unsaveJob']);
        });
    });

    // Employer routes
    Route::middleware('role:employer')->group(function () {
        Route::prefix('employer')->group(function () {
            Route::prefix('jobs')->group(function () {
                Route::get('/', [JobController::class, 'employerJobs']);
                Route::post('/', [JobController::class, 'store']);
                Route::get('/{job}', [JobController::class, 'employerShow']);
                Route::put('/{job}', [JobController::class, 'update']);
                Route::delete('/{job}', [JobController::class, 'destroy']);
                Route::post('/{job}/publish', [JobController::class, 'publish']);
                Route::post('/{job}/pause', [JobController::class, 'pause']);
            });

            Route::prefix('applications')->group(function () {
                Route::get('/', [ApplicationController::class, 'employerIndex']);
                Route::get('/{application}', [ApplicationController::class, 'employerShow']);
                Route::put('/{application}/status', [ApplicationController::class, 'updateStatus']);
                Route::post('/{application}/notes', [ApplicationController::class, 'addNotes']);
                Route::get('/{application}/resume', [ApplicationController::class, 'downloadResume']);
            });

            Route::prefix('company')->group(function () {
                Route::get('/', [CompanyController::class, 'employerShow']);
                Route::put('/', [CompanyController::class, 'update']);
                Route::post('/logo', [CompanyController::class, 'uploadLogo']);
                Route::delete('/logo', [CompanyController::class, 'deleteLogo']);
            });

            Route::prefix('subscription')->group(function () {
                Route::get('/', [HrPackageController::class, 'currentSubscription']);
                Route::post('/subscribe', [HrPackageController::class, 'subscribe']);
                Route::post('/cancel', [HrPackageController::class, 'cancel']);
                Route::get('/usage', [HrPackageController::class, 'usage']);
            });
        });
    });

    // Admin routes
    Route::middleware('role:admin|super-admin')->prefix('admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\Api\Admin\UserController::class);
        Route::resource('companies', \App\Http\Controllers\Api\Admin\CompanyController::class);
        Route::resource('jobs', \App\Http\Controllers\Api\Admin\JobController::class);
        Route::resource('applications', \App\Http\Controllers\Api\Admin\ApplicationController::class);
        Route::resource('hr-packages', \App\Http\Controllers\Api\Admin\HrPackageController::class);
        Route::resource('industries', \App\Http\Controllers\Api\Admin\IndustryController::class);

        Route::prefix('analytics')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Api\Admin\AnalyticsController::class, 'dashboard']);
            Route::get('/users', [\App\Http\Controllers\Api\Admin\AnalyticsController::class, 'users']);
            Route::get('/jobs', [\App\Http\Controllers\Api\Admin\AnalyticsController::class, 'jobs']);
            Route::get('/companies', [\App\Http\Controllers\Api\Admin\AnalyticsController::class, 'companies']);
        });
    });
});

// Fallback route
Route::fallback(function () {
    return response()->json(['error' => 'Route not found'], 404);
});
