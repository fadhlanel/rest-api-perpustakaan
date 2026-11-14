<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    private ?string $serverKey;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
    }

    public function sendToAll(string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::pluck('fcm_token')->toArray();
        if (empty($tokens)) {
            return ['success' => 0, 'failure' => 0];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToUser(int $userId, string $title, string $body, array $data = []): array
    {
        $tokens = DeviceToken::where('user_id', $userId)->pluck('fcm_token')->toArray();
        if (empty($tokens)) {
            return ['success' => 0, 'failure' => 0];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        if (!$this->serverKey) {
            Log::error('FCM Server Key not configured');
            return ['success' => 0, 'failure' => count($tokens), 'error' => 'FCM not configured'];
        }

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => array_merge($data, [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]),
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                $result = $response->json();
                $this->cleanupInvalidTokens($tokens, $result['results'] ?? []);
                return [
                    'success' => $result['success'] ?? 0,
                    'failure' => $result['failure'] ?? 0,
                ];
            }

            Log::error('FCM send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => 0, 'failure' => count($tokens), 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('FCM exception', ['message' => $e->getMessage()]);
            return ['success' => 0, 'failure' => count($tokens), 'error' => $e->getMessage()];
        }
    }

    private function cleanupInvalidTokens(array $tokens, array $results): void
    {
        foreach ($results as $index => $result) {
            if (isset($result['error']) && in_array($result['error'], ['NotRegistered', 'InvalidRegistration'])) {
                DeviceToken::where('fcm_token', $tokens[$index])->delete();
            }
        }
    }
}
