<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\SavedJob;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobController extends Controller
{
    /**
     * Display a listing of jobs (public).
     */
    public function index(Request $request)
    {
        $query = JobPosting::with(['company', 'industry'])
            ->active()
            ->orderByDesc('is_featured')
            ->orderByDesc('is_urgent')
            ->orderByDesc('created_at');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($company) use ($search) {
                        $company->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->input('experience_level'));
        }

        if ($request->filled('industry_id')) {
            $query->where('industry_id', $request->input('industry_id'));
        }

        if ($request->filled('is_remote')) {
            $query->where('is_remote', $request->boolean('is_remote'));
        }

        if ($request->filled('salary_min')) {
            $query->where('salary_max', '>=', $request->input('salary_min'));
        }

        if ($request->filled('salary_max')) {
            $query->where('salary_min', '<=', $request->input('salary_max'));
        }

        $jobs = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'jobs' => $jobs->items(),
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
            ],
            'filters' => [
                'industries' => Industry::active()->orderBy('name')->get(['id', 'name']),
                'job_types' => ['full-time', 'part-time', 'contract', 'freelance', 'internship'],
                'experience_levels' => ['entry', 'mid', 'senior', 'executive'],
            ]
        ]);
    }

    /**
     * Display the specified job (public).
     */
    public function show(JobPosting $job)
    {
        $job->load(['company.industry', 'industry']);

        // Increment view count
        $job->increment('views_count');

        return response()->json(['job' => $job]);
    }

    /**
     * Get similar jobs.
     */
    public function similar(JobPosting $job, Request $request)
    {
        $similarJobs = JobPosting::with(['company', 'industry'])
            ->active()
            ->where('id', '!=', $job->id)
            ->where(function ($query) use ($job) {
                $query->where('industry_id', $job->industry_id)
                    ->orWhere('location', 'like', '%' . $job->location . '%')
                    ->orWhere('type', $job->type);
            })
            ->limit($request->input('limit', 5))
            ->get();

        return response()->json(['jobs' => $similarJobs]);
    }

    /**
     * Store a newly created job (employer).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'benefits' => 'nullable|string',
            'type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'experience_level' => 'required|in:entry,mid,senior,executive',
            'location' => 'required|string|max:255',
            'is_remote' => 'boolean',
            'industry_id' => 'nullable|exists:industries,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'string|size:3',
            'salary_period' => 'in:hourly,daily,weekly,monthly,yearly',
            'salary_negotiable' => 'boolean',
            'positions_available' => 'integer|min:1',
            'application_deadline' => 'nullable|date|after:today',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find(Auth::id());
        $company = $user->companies()->first();

        if (!$company) {
            return response()->json(['error' => 'No company associated with user'], 403);
        }

        $job = JobPosting::create([
            'company_id' => $company->id,
            'posted_by_user_id' => $user->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'requirements' => $request->requirements,
            'responsibilities' => $request->responsibilities,
            'benefits' => $request->benefits,
            'type' => $request->type,
            'experience_level' => $request->experience_level,
            'location' => $request->location,
            'is_remote' => $request->boolean('is_remote'),
            'industry_id' => $request->industry_id,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'salary_currency' => $request->input('salary_currency', 'KES'),
            'salary_period' => $request->input('salary_period', 'monthly'),
            'salary_negotiable' => $request->boolean('salary_negotiable'),
            'positions_available' => $request->input('positions_available', 1),
            'application_deadline' => $request->application_deadline,
            'expires_at' => $request->expires_at,
        ]);

        return response()->json([
            'message' => 'Job created successfully',
            'job' => $job->load(['company', 'industry']),
        ], 201);
    }

    /**
     * Get jobs for the authenticated employer.
     */
    public function employerJobs(Request $request)
    {
        $user = User::find(Auth::id());
        $company = $user->companies()->first();

        if (!$company) {
            return response()->json(['error' => 'No company associated with user'], 403);
        }

        $query = JobPosting::with(['industry'])
            ->where('company_id', $company->id)
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $jobs = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'jobs' => $jobs->items(),
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
            ]
        ]);
    }

    /**
     * Save a job (job seeker).
     */
    public function saveJob(Request $request, JobPosting $job)
    {
        $user = Auth::user();
        $jobSeeker = $user->jobSeeker;

        if (!$jobSeeker) {
            return response()->json(['error' => 'Job seeker profile not found'], 404);
        }

        $existingSave = SavedJob::where('job_seeker_id', $jobSeeker->id)
            ->where('job_posting_id', $job->id)
            ->first();

        if ($existingSave) {
            return response()->json(['message' => 'Job already saved'], 200);
        }

        SavedJob::create([
            'job_seeker_id' => $jobSeeker->id,
            'job_posting_id' => $job->id,
            'notes' => $request->input('notes'),
        ]);

        return response()->json(['message' => 'Job saved successfully'], 201);
    }

    /**
     * Unsave a job (job seeker).
     */
    public function unsaveJob(JobPosting $job)
    {
        $user = Auth::user();
        $jobSeeker = $user->jobSeeker;

        if (!$jobSeeker) {
            return response()->json(['error' => 'Job seeker profile not found'], 404);
        }

        SavedJob::where('job_seeker_id', $jobSeeker->id)
            ->where('job_posting_id', $job->id)
            ->delete();

        return response()->json(['message' => 'Job unsaved successfully']);
    }

    /**
     * Get saved jobs for job seeker.
     */
    public function savedJobs(Request $request)
    {
        $user = Auth::user();
        $jobSeeker = $user->jobSeeker;

        if (!$jobSeeker) {
            return response()->json(['error' => 'Job seeker profile not found'], 404);
        }

        $savedJobs = SavedJob::with(['jobPosting.company', 'jobPosting.industry'])
            ->where('job_seeker_id', $jobSeeker->id)
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'saved_jobs' => $savedJobs->items(),
            'pagination' => [
                'current_page' => $savedJobs->currentPage(),
                'last_page' => $savedJobs->lastPage(),
                'per_page' => $savedJobs->perPage(),
                'total' => $savedJobs->total(),
            ]
        ]);
    }

    /**
     * Update the specified job (employer).
     */
    public function update(Request $request, JobPosting $job)
    {
        $user = Auth::user();

        if ($job->posted_by_user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'benefits' => 'nullable|string',
            'type' => 'in:full-time,part-time,contract,freelance,internship',
            'experience_level' => 'in:entry,mid,senior,executive',
            'location' => 'string|max:255',
            'is_remote' => 'boolean',
            'industry_id' => 'nullable|exists:industries,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'string|size:3',
            'salary_period' => 'in:hourly,daily,weekly,monthly,yearly',
            'salary_negotiable' => 'boolean',
            'positions_available' => 'integer|min:1',
            'application_deadline' => 'nullable|date|after:today',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $job->update($request->validated());

        return response()->json([
            'message' => 'Job updated successfully',
            'job' => $job->load(['company', 'industry']),
        ]);
    }

    /**
     * Remove the specified job (employer).
     */
    public function destroy(JobPosting $job)
    {
        $user = Auth::user();

        if ($job->posted_by_user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully']);
    }
}
