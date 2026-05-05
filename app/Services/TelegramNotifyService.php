<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramNotifyService
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = config('services.telegram_notify.token');
        $this->chatId = config('services.telegram_notify.chat_id');
        $this->chatIdAdmin = config('services.telegram_notify.chat_id_admin');
    }

    public function sendTelegramNotification($message)
    {
        if (empty($message)) {
            return ['success' => false, 'error' => ['message' => 'Message cannot be empty']];
        }

        $apiUrl = "https://api.telegram.org/bot{$this->accessToken}/sendMessage";

        $response = Http::post($apiUrl, [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML' // รองรับข้อความ HTML
        ]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        } else {
        // dd($message);
            return ['success' => false, 'error' => $response->json()];
        }
    }

    public function sendTelegramAdminNotification($message)
    {
        if (empty($message)) {
            return ['success' => false, 'error' => ['message' => 'Message cannot be empty']];
        }

        $apiUrl = "https://api.telegram.org/bot{$this->accessToken}/sendMessage";

        $response = Http::post($apiUrl, [
            'chat_id' => $this->chatIdAdmin,
            'text' => $message,
            'parse_mode' => 'HTML' // รองรับข้อความ HTML
        ]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        } else {
            return ['success' => false, 'error' => $response->json()];
        }
    }

}
