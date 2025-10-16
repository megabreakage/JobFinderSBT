<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuthService;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class VerifyPhone extends Component
{
    public string $otp = '';
    public bool $verified = false;
    public bool $otpSent = false;
    public string $message = '';
    public string $errorMessage = '';
    public int $resendCooldown = 0;

    protected function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ];
    }

    protected function messages(): array
    {
        return [
            'otp.required' => 'Please enter the OTP code.',
            'otp.size' => 'OTP must be 6 digits.',
            'otp.regex' => 'OTP must contain only numbers.',
        ];
    }

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->phone_verified_at) {
            $this->verified = true;
            $this->message = 'Your phone number is already verified.';
        }

        $this->checkResendCooldown();
    }

    protected function checkResendCooldown()
    {
        $key = 'otp_cooldown_' . Auth::id();
        $this->resendCooldown = Cache::get($key, 0);
    }

    public function sendOtp()
    {
        if (!Auth::check()) {
            $this->errorMessage = 'Please log in to verify your phone.';
            return;
        }

        $this->checkResendCooldown();

        if ($this->resendCooldown > 0) {
            $this->errorMessage = "Please wait {$this->resendCooldown} seconds before requesting a new OTP.";
            return;
        }

        $user = Auth::user();

        if ($user->phone_verified_at) {
            $this->errorMessage = 'Your phone number is already verified.';
            return;
        }

        try {
            $authService = app(AuthService::class);
            $smsService = app(SmsService::class);

            // Generate OTP
            $otp = $authService->generateOtp();

            // Store OTP in cache for 10 minutes
            $cacheKey = 'phone_otp_' . $user->id;
            Cache::put($cacheKey, $otp, now()->addMinutes(10));

            // Send OTP via SMS
            $smsService->sendOtp($user->phone, $otp);

            $this->otpSent = true;
            $this->message = 'OTP has been sent to your phone number.';
            $this->errorMessage = '';

            // Set cooldown for 60 seconds
            $cooldownKey = 'otp_cooldown_' . $user->id;
            Cache::put($cooldownKey, 60, now()->addSeconds(60));
            $this->resendCooldown = 60;

        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to send OTP. Please try again.';
            logger()->error('Send OTP error: ' . $e->getMessage());
        }
    }

    public function verifyOtp()
    {
        $this->validate();

        if (!Auth::check()) {
            $this->errorMessage = 'Please log in to verify your phone.';
            return;
        }

        $user = Auth::user();
        $cacheKey = 'phone_otp_' . $user->id;
        $storedOtp = Cache::get($cacheKey);

        if (!$storedOtp) {
            $this->errorMessage = 'OTP has expired. Please request a new one.';
            return;
        }

        if ($this->otp !== $storedOtp) {
            $this->errorMessage = 'Invalid OTP. Please check and try again.';
            return;
        }

        try {
            // Verify phone
            $user->phone_verified_at = now();
            $user->save();

            // Clear OTP from cache
            Cache::forget($cacheKey);

            $this->verified = true;
            $this->message = 'Your phone number has been successfully verified!';
            $this->errorMessage = '';
            $this->otp = '';

        } catch (\Exception $e) {
            $this->errorMessage = 'Verification failed. Please try again.';
            logger()->error('Verify OTP error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.verify-phone');
    }
}
