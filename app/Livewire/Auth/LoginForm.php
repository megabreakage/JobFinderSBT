<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class LoginForm extends Component
{
    public string $login = '';
    public string $password = '';
    public bool $remember = false;
    
    public string $errorMessage = '';
    public bool $isLocked = false;
    public int $lockoutSeconds = 0;

    protected function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'login.required' => 'Email or phone number is required.',
            'password.required' => 'Password is required.',
        ];
    }

    public function mount()
    {
        $this->checkLockout();
    }

    protected function checkLockout()
    {
        $key = $this->throttleKey();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->isLocked = true;
            $this->lockoutSeconds = RateLimiter::availableIn($key);
            $this->errorMessage = "Too many login attempts. Please try again in {$this->lockoutSeconds} seconds.";
        }
    }

    protected function throttleKey(): string
    {
        return 'login-' . request()->ip();
    }

    public function submit()
    {
        $this->validate();

        // Check if account is locked
        $this->checkLockout();
        
        if ($this->isLocked) {
            return;
        }

        $key = $this->throttleKey();

        try {
            // Determine if login is email or phone
            $field = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
            
            // Find user
            $user = User::where($field, $this->login)->first();

            if (!$user || !Hash::check($this->password, $user->password)) {
                // Increment failed attempts
                RateLimiter::hit($key, 900); // 15 minutes lockout
                
                $attemptsLeft = 5 - RateLimiter::attempts($key);
                
                if ($attemptsLeft <= 0) {
                    $this->isLocked = true;
                    $this->lockoutSeconds = 900;
                    $this->errorMessage = "Too many failed login attempts. Your account has been locked for 15 minutes.";
                } else {
                    $this->errorMessage = "Invalid credentials. You have {$attemptsLeft} attempts remaining.";
                }
                
                return;
            }

            // Check if account is active
            if (!$user->is_active) {
                $this->errorMessage = 'Your account has been deactivated. Please contact support.';
                return;
            }

            // Check if account is locked
            if ($user->locked_until && $user->locked_until->isFuture()) {
                $minutes = now()->diffInMinutes($user->locked_until);
                $this->errorMessage = "Your account is locked. Please try again in {$minutes} minutes.";
                return;
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            // Reset failed attempts
            $user->failed_login_attempts = 0;
            $user->locked_until = null;
            $user->last_login_at = now();
            $user->last_login_ip = request()->ip();
            $user->save();

            // Log the user in
            Auth::login($user, $this->remember);

            // Redirect based on role
            $this->redirectBasedOnRole($user);

        } catch (\Exception $e) {
            $this->errorMessage = 'Login failed. Please try again.';
            logger()->error('Login error: ' . $e->getMessage());
        }
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->hasRole('employer')) {
            return redirect()->intended('/employer/dashboard');
        } else {
            return redirect()->intended('/dashboard');
        }
    }

    public function render()
    {
        return view('livewire.auth.login-form');
    }
}
