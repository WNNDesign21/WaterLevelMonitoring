<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
    }

    /**
     * Send WhatsApp Message via Fonnte
     */
    public function sendMessage($target, $message)
    {
        if (!$this->token) {
            Log::error("[WHATSAPP] Fonnte Token is not set in .env");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == true) {
                Log::info("[WHATSAPP] Message sent to $target: " . $message);
                return true;
            }

            Log::error("[WHATSAPP] Failed to send message to $target. Reason: " . json_encode($result));
            return false;

        } catch (\Exception $e) {
            Log::error("[WHATSAPP] Exception while sending message: " . $e->getMessage());
            return false;
        }
    }
}
