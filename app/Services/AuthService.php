<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService extends BaseService
{
    /**
     * Create a new user with role assignment.
     *
     * @param array $data User data
     * @param string $role Role to assign (job-seeker, employer, admin, super-admin)
     * @return User
     */
    public function createUser(array $data, string $role): User
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Generate UUID if not provided
        if (!isset($data['uuid'])) {
            $data['uuid'] = Str::uuid()->toString();
        }

        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;
        $data['email_notifications'] = $data['email_notifications'] ?? true;
        $data['sms_notifications'] = $data['sms_notifications'] ?? true;

        // Create the user
        $user = User::create($data);

        // Assign role
        $user->assignRole($role);

        return $user;
    }

    /**
     * Generate a secure email verification token.
     *
     * @return string
     */
    public function generateEmailVerificationToken(): string
    {
        return Str::random(64);
    }

    /**
     * Generate a 6-digit OTP.
     *
     * @return string
     */
    public function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Lock a user account after failed login attempts.
     *
     * @param User $user
     * @param int $lockoutMinutes Duration of lockout in minutes (default: 15)
     * @return void
     */
    public function lockAccount(User $user, int $lockoutMinutes = 15): void
    {
        $user->update([
            'locked_until' => now()->addMinutes($lockoutMinutes),
            'failed_login_attempts' => 0, // Reset counter after locking
        ]);
    }

    /**
     * Unlock a user account.
     *
     * @param User $user
     * @return void
     */
    public function unlockAccount(User $user): void
    {
        $user->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Check if account is currently locked.
     *
     * @param User $user
     * @return bool
     */
    public function isAccountLocked(User $user): bool
    {
        if (!$user->locked_until) {
            return false;
        }

        // Check if lockout period has expired
        if (now()->greaterThan($user->locked_until)) {
            // Auto-unlock if lockout period has passed
            $this->unlockAccount($user);
            return false;
        }

        return true;
    }

    /**
     * Increment failed login attempts and lock if threshold reached.
     *
     * @param User $user
     * @param int $maxAttempts Maximum attempts before locking (default: 5)
     * @return void
     */
    public function incrementFailedAttempts(User $user, int $maxAttempts = 5): void
    {
        $attempts = ($user->failed_login_attempts ?? 0) + 1;

        if ($attempts >= $maxAttempts) {
            $this->lockAccount($user);
        } else {
            $user->update([
                'failed_login_attempts' => $attempts,
            ]);
        }
    }

    /**
     * Reset failed login attempts on successful login.
     *
     * @param User $user
     * @return void
     */
    public function resetFailedAttempts(User $user): void
    {
        $user->update([
            'failed_login_attempts' => 0,
            'last_login_at' => now(),
        ]);
    }

    /**
     * Verify email with token.
     *
     * @param User $user
     * @return void
     */
    public function verifyEmail(User $user): void
    {
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);
    }

    /**
     * Verify phone number.
     *
     * @param User $user
     * @return void
     */
    public function verifyPhone(User $user): void
    {
        $user->update([
            'phone_verified_at' => now(),
            'phone_verification_code' => null,
            'phone_verification_expires_at' => null,
        ]);
    }

    /**
     * Store OTP for phone verification.
     *
     * @param User $user
     * @param string $otp
     * @param int $expirationMinutes
     * @return void
     */
    public function storeOtp(User $user, string $otp, int $expirationMinutes = 10): void
    {
        $user->update([
            'phone_verification_code' => $otp,
            'phone_verification_expires_at' => now()->addMinutes($expirationMinutes),
        ]);
    }

    /**
     * Verify OTP for phone verification.
     *
     * @param User $user
     * @param string $otp
     * @return bool
     */
    public function verifyOtp(User $user, string $otp): bool
    {
        // Check if OTP matches
        if ($user->phone_verification_code !== $otp) {
            return false;
        }

        // Check if OTP has expired
        if ($user->phone_verification_expires_at && now()->greaterThan($user->phone_verification_expires_at)) {
            return false;
        }

        return true;
    }
}
