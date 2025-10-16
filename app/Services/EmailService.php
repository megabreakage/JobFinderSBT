<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService extends BaseService
{
    /**
     * Send welcome email to new user.
     *
     * @param User $user
     * @return bool
     */
    public function sendWelcomeEmail(User $user): bool
    {
        try {
            $subject = 'Welcome to ' . config('app.name');
            $template = 'welcome';
            
            $data = [
                'user' => $user,
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
            ];

            return $this->sendEmail(
                $user->email,
                $subject,
                $template,
                $data,
                $user->id
            );
        } catch (Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send email verification email.
     *
     * @param User $user
     * @param string $token
     * @return bool
     */
    public function sendVerificationEmail(User $user, string $token): bool
    {
        try {
            $subject = 'Verify Your Email Address';
            $template = 'email-verification';
            
            $verificationUrl = config('app.url') . '/verify-email?token=' . $token;
            
            $data = [
                'user' => $user,
                'verification_url' => $verificationUrl,
                'token' => $token,
                'app_name' => config('app.name'),
            ];

            return $this->sendEmail(
                $user->email,
                $subject,
                $template,
                $data,
                $user->id
            );
        } catch (Exception $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send password reset email.
     *
     * @param User $user
     * @param string $token
     * @return bool
     */
    public function sendPasswordResetEmail(User $user, string $token): bool
    {
        try {
            $subject = 'Reset Your Password';
            $template = 'password-reset';
            
            $resetUrl = config('app.url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
            
            $data = [
                'user' => $user,
                'reset_url' => $resetUrl,
                'token' => $token,
                'app_name' => config('app.name'),
                'expires_in' => '60 minutes',
            ];

            return $this->sendEmail(
                $user->email,
                $subject,
                $template,
                $data,
                $user->id
            );
        } catch (Exception $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send email and log to database.
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $template Email template name
     * @param array $data Data to pass to template
     * @param int|null $userId User ID if applicable
     * @return bool
     */
    protected function sendEmail(
        string $to,
        string $subject,
        string $template,
        array $data = [],
        ?int $userId = null
    ): bool {
        $emailLog = null;

        try {
            // Create initial log entry
            $emailLog = $this->logEmail($to, $subject, $template, 'queued', $userId);

            // Send email
            Mail::send("emails.{$template}", $data, function ($message) use ($to, $subject) {
                $message->to($to)
                    ->subject($subject);
            });

            // Update log to sent
            $this->updateEmailLog($emailLog, 'sent');

            return true;
        } catch (Exception $e) {
            Log::error('Email sending failed', [
                'to' => $to,
                'subject' => $subject,
                'template' => $template,
                'error' => $e->getMessage(),
            ]);

            // Update log to failed
            if ($emailLog) {
                $this->updateEmailLog($emailLog, 'failed', $e->getMessage());
            }

            return false;
        }
    }

    /**
     * Log email to database.
     *
     * @param string $to
     * @param string $subject
     * @param string $template
     * @param string $status
     * @param int|null $userId
     * @return EmailLog
     */
    protected function logEmail(
        string $to,
        string $subject,
        string $template,
        string $status,
        ?int $userId = null
    ): EmailLog {
        return EmailLog::create([
            'user_id' => $userId,
            'to_email' => $to,
            'subject' => $subject,
            'template' => $template,
            'status' => $status,
        ]);
    }

    /**
     * Update email log.
     *
     * @param EmailLog $emailLog
     * @param string $status
     * @param string|null $errorMessage
     * @return void
     */
    protected function updateEmailLog(EmailLog $emailLog, string $status, ?string $errorMessage = null): void
    {
        $updateData = ['status' => $status];

        if ($status === 'sent') {
            $updateData['sent_at'] = now();
        } elseif ($status === 'failed') {
            $updateData['failed_at'] = now();
            if ($errorMessage) {
                $updateData['error_message'] = $errorMessage;
            }
        }

        $emailLog->update($updateData);
    }

    /**
     * Send a generic notification email.
     *
     * @param User $user
     * @param string $subject
     * @param string $message
     * @param array $additionalData
     * @return bool
     */
    public function sendNotificationEmail(User $user, string $subject, string $message, array $additionalData = []): bool
    {
        try {
            $template = 'notification';
            
            $data = array_merge([
                'user' => $user,
                'subject' => $subject,
                'message' => $message,
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
            ], $additionalData);

            return $this->sendEmail(
                $user->email,
                $subject,
                $template,
                $data,
                $user->id
            );
        } catch (Exception $e) {
            Log::error('Failed to send notification email', [
                'user_id' => $user->id,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send bulk emails (queued).
     *
     * @param array $recipients Array of ['email' => string, 'data' => array]
     * @param string $subject
     * @param string $template
     * @return int Number of emails queued
     */
    public function sendBulkEmails(array $recipients, string $subject, string $template): int
    {
        $queued = 0;

        foreach ($recipients as $recipient) {
            try {
                $email = $recipient['email'];
                $data = $recipient['data'] ?? [];
                $userId = $recipient['user_id'] ?? null;

                // Queue the email (in a real implementation, this would use Laravel queues)
                $this->sendEmail($email, $subject, $template, $data, $userId);
                $queued++;
            } catch (Exception $e) {
                Log::error('Failed to queue bulk email', [
                    'recipient' => $recipient['email'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $queued;
    }
}
