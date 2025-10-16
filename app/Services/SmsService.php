<?php

namespace App\Services;

use App\Models\SmsLog;
use Exception;
use Illuminate\Support\Facades\Log;
use Vonage\Client as VonageClient;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class SmsService extends BaseService
{
    protected ?VonageClient $client = null;

    /**
     * Initialize Vonage client.
     */
    public function __construct()
    {
        $apiKey = config('services.vonage.key');
        $apiSecret = config('services.vonage.secret');

        if ($apiKey && $apiSecret) {
            $credentials = new Basic($apiKey, $apiSecret);
            $this->client = new VonageClient($credentials);
        }
    }

    /**
     * Send OTP via SMS using Vonage.
     *
     * @param string $phone Phone number in E.164 format
     * @param string $otp The OTP code to send
     * @return array Response with status and message_id
     * @throws Exception
     */
    public function sendOtp(string $phone, string $otp): array
    {
        $message = "Your verification code is: {$otp}. This code will expire in 10 minutes.";
        
        return $this->sendSms($phone, $message, 'otp');
    }

    /**
     * Send SMS via Vonage.
     *
     * @param string $phone Phone number in E.164 format
     * @param string $message Message content
     * @param string $type Type of SMS (otp, notification, alert, etc.)
     * @return array Response with status and message_id
     * @throws Exception
     */
    public function sendSms(string $phone, string $message, string $type = 'general'): array
    {
        $smsLog = null;
        
        try {
            // Create initial log entry
            $smsLog = $this->logSms($phone, $message, $type, 'queued');

            // Check if Vonage is configured
            if (!$this->client) {
                throw new Exception('Vonage SMS service is not configured. Please set VONAGE_KEY and VONAGE_SECRET in your .env file.');
            }

            // Get sender name from config
            $from = config('services.vonage.sms_from', config('app.name'));

            // Send SMS via Vonage
            $response = $this->client->sms()->send(
                new SMS($phone, $from, $message)
            );

            $vonageMessage = $response->current();
            $status = $vonageMessage->getStatus();

            // Update log with response
            if ($status == 0) {
                // Success
                $this->updateSmsLog($smsLog, 'sent', [
                    'message_id' => $vonageMessage->getMessageId(),
                    'status' => $status,
                ]);

                return [
                    'success' => true,
                    'message_id' => $vonageMessage->getMessageId(),
                    'status' => 'sent',
                ];
            } else {
                // Failed
                $errorMessage = $vonageMessage->getStatus() . ': ' . $vonageMessage->getBody();
                
                $this->updateSmsLog($smsLog, 'failed', [
                    'error' => $errorMessage,
                ]);

                throw new Exception("SMS sending failed: {$errorMessage}");
            }
        } catch (Exception $e) {
            // Log error
            Log::error('SMS sending failed', [
                'phone' => $phone,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            // Update log if exists
            if ($smsLog) {
                $this->updateSmsLog($smsLog, 'failed', [
                    'error' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    /**
     * Log SMS to database.
     *
     * @param string $phone
     * @param string $message
     * @param string $type
     * @param string $status
     * @return SmsLog
     */
    protected function logSms(string $phone, string $message, string $type, string $status): SmsLog
    {
        return SmsLog::create([
            'phone' => $phone,
            'message' => $message,
            'type' => $type,
            'status' => $status,
            'gateway' => 'vonage',
        ]);
    }

    /**
     * Update SMS log with response data.
     *
     * @param SmsLog $smsLog
     * @param string $status
     * @param array $data
     * @return void
     */
    protected function updateSmsLog(SmsLog $smsLog, string $status, array $data = []): void
    {
        $updateData = ['status' => $status];

        if (isset($data['message_id'])) {
            $updateData['message_id'] = $data['message_id'];
        }

        if (isset($data['error'])) {
            $updateData['error_message'] = $data['error'];
        }

        if ($status === 'sent') {
            $updateData['sent_at'] = now();
        } elseif ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        } elseif ($status === 'failed') {
            $updateData['failed_at'] = now();
        }

        if (!empty($data)) {
            $updateData['gateway_response'] = $data;
        }

        $smsLog->update($updateData);
    }

    /**
     * Handle Vonage webhook for delivery receipts.
     *
     * @param array $webhookData
     * @return void
     */
    public function handleDeliveryReceipt(array $webhookData): void
    {
        $messageId = $webhookData['messageId'] ?? null;
        $status = $webhookData['status'] ?? null;

        if (!$messageId) {
            return;
        }

        // Find SMS log by message_id
        $smsLog = SmsLog::where('message_id', $messageId)->first();

        if (!$smsLog) {
            return;
        }

        // Map Vonage status to our status
        $mappedStatus = match ($status) {
            'delivered' => 'delivered',
            'failed', 'rejected' => 'failed',
            'expired' => 'expired',
            default => $smsLog->status,
        };

        // Update log
        $this->updateSmsLog($smsLog, $mappedStatus, $webhookData);
    }

    /**
     * Check if SMS service is configured.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return $this->client !== null;
    }
}
