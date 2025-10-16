<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    public bool $success = false;
    public bool $invalid = false;
    public string $message = '';
    public string $errorMessage = '';

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'token' => ['required', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.exists' => 'We could not find an account with this email address.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function mount(string $token, ?string $email = null)
    {
        $this->token = $token;
        $this->email = $email ?? '';

        // Verify token exists
        $this->verifyToken();
    }

    protected function verifyToken()
    {
        $resetRecord = \DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->first();

        if (!$resetRecord) {
            $this->invalid = true;
            $this->errorMessage = 'Invalid or expired password reset token.';
            return;
        }

        // Check if token matches
        if (!hash_equals($resetRecord->token, hash('sha256', $this->token))) {
            $this->invalid = true;
            $this->errorMessage = 'Invalid or expired password reset token.';
            return;
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            $this->invalid = true;
            $this->errorMessage = 'This password reset link has expired. Please request a new one.';
            return;
        }
    }

    public function resetPassword()
    {
        $this->validate();

        if ($this->invalid) {
            return;
        }

        try {
            // Verify token again before resetting
            $resetRecord = \DB::table('password_reset_tokens')
                ->where('email', $this->email)
                ->first();

            if (!$resetRecord || !hash_equals($resetRecord->token, hash('sha256', $this->token))) {
                $this->errorMessage = 'Invalid or expired password reset token.';
                return;
            }

            // Find user
            $user = User::where('email', $this->email)->first();

            if (!$user) {
                $this->errorMessage = 'User not found.';
                return;
            }

            // Update password
            $user->password = Hash::make($this->password);
            $user->save();

            // Delete password reset token
            \DB::table('password_reset_tokens')
                ->where('email', $this->email)
                ->delete();

            $this->success = true;
            $this->message = 'Your password has been successfully reset. You can now log in with your new password.';
            
            // Clear form
            $this->password = '';
            $this->password_confirmation = '';

        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to reset password. Please try again.';
            logger()->error('Reset password error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
