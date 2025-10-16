<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\EmailService;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VerifyEmail extends Component
{
    public ?string $token = null;
    public bool $verified = false;
    public bool $invalid = false;
    public bool $alreadyVerified = false;
    public string $message = '';
    public bool $resendSuccess = false;
    public bool $resendError = false;

    public function mount(?string $token = null)
    {
        $this->token = $token;

        if ($this->token) {
            $this->verifyToken();
        } else {
            // Check if current user is already verified
            if (Auth::check() && Auth::user()->email_verified_at) {
                $this->alreadyVerified = true;
                $this->message = 'Your email is already verified.';
            }
        }
    }

    protected function verifyToken()
    {
        $user = User::where('email_verification_token', $this->token)->first();

        if (!$user) {
            $this->invalid = true;
            $this->message = 'Invalid or expired verification token.';
            return;
        }

        if ($user->email_verified_at) {
            $this->alreadyVerified = true;
            $this->message = 'Your email has already been verified.';
            return;
        }

        // Verify the email
        $user->email_verified_at = now();
        $user->email_verification_token = null;
        $user->is_active = true;
        $user->save();

        $this->verified = true;
        $this->message = 'Your email has been successfully verified! You can now log in.';
    }

    public function resendVerification()
    {
        $this->resendSuccess = false;
        $this->resendError = false;

        if (!Auth::check()) {
            $this->resendError = true;
            $this->message = 'Please log in to resend verification email.';
            return;
        }

        $user = Auth::user();

        if ($user->email_verified_at) {
            $this->resendError = true;
            $this->message = 'Your email is already verified.';
            return;
        }

        try {
            $authService = app(AuthService::class);
            $emailService = app(EmailService::class);

            // Generate new token
            $token = $authService->generateEmailVerificationToken();
            $user->email_verification_token = $token;
            $user->save();

            // Send verification email
            $emailService->sendVerificationEmail($user, $token);

            $this->resendSuccess = true;
            $this->message = 'Verification email has been resent. Please check your inbox.';

        } catch (\Exception $e) {
            $this->resendError = true;
            $this->message = 'Failed to resend verification email. Please try again.';
            logger()->error('Resend verification error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}
