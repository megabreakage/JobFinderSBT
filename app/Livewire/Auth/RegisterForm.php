<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuthService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterForm extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'job-seeker';
    public bool $terms_accepted = false;
    
    public bool $success = false;
    public string $successMessage = '';
    public string $errorMessage = '';

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:job-seeker,employer'],
            'terms_accepted' => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required' => 'Please select a role.',
            'role.in' => 'Invalid role selected.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
        ];
    }

    public function submit()
    {
        $this->validate();

        try {
            $authService = app(AuthService::class);
            $emailService = app(EmailService::class);

            // Create user with role assignment
            $user = $authService->createUser([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
            ], $this->role);

            // Send verification email
            $token = $authService->generateEmailVerificationToken();
            $user->email_verification_token = $token;
            $user->save();

            $emailService->sendVerificationEmail($user, $token);

            $this->success = true;
            $this->successMessage = 'Registration successful! Please check your email to verify your account.';
            
            // Reset form
            $this->reset(['first_name', 'last_name', 'email', 'phone', 'password', 'password_confirmation', 'role', 'terms_accepted']);

        } catch (\Exception $e) {
            $this->errorMessage = 'Registration failed. Please try again.';
            logger()->error('Registration error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register-form');
    }
}
