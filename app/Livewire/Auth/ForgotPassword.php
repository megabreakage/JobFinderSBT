<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';
    public bool $success = false;
    public string $message = '';
    public string $errorMessage = '';

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'We could not find an account with this email address.',
        ];
    }

    public function sendResetLink()
    {
        $this->validate();

        try {
            $user = User::where('email', $this->email)->first();

            if (!$user) {
                $this->errorMessage = 'We could not find an account with this email address.';
                return;
            }

            // Generate password reset token
            $token = Str::random(64);
            
            // Store token in password_resets table
            \DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $this->email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now()
                ]
            );

            // Send password reset email
            $emailService = app(EmailService::class);
            $emailService->sendPasswordResetEmail($user, $token);

            $this->success = true;
            $this->message = 'We have sent a password reset link to your email address. Please check your inbox.';
            $this->errorMessage = '';
            $this->email = '';

        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to send password reset link. Please try again.';
            logger()->error('Forgot password error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
