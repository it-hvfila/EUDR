<?php
// app/Services/LineNotifyService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LineNotifyService
{
    protected $accessToken;
    protected $accessTokenITSTAFF;
    protected $accessTokenPart;

    public function __construct()
    {
        $this->accessToken = config('services.line_notify.token');
        $this->accessTokenITSTAFF = config('services.line_notify_it_staff.token');
    }

    public function sendNotification($message, $stickerPackageId = null, $stickerId = null)
    {
        // Check if message is empty
        if (empty($message)) {
            return ['success' => false, 'error' => ['message' => 'Message cannot be empty']];
        }

        // Encode message to UTF-8
        $encodedMessage = mb_convert_encoding($message, 'UTF-8', 'auto');

        $postData = ['message' => $message];

        // Include sticker parameters if provided
        if ($stickerPackageId && $stickerId) {
            $postData['stickerPackageId'] = $stickerPackageId;
            $postData['stickerId'] = $stickerId;
        }


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Content-Type'  => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://notify-api.line.me/api/notify', $postData);

        // dd($this->accessToken);
        // ตรวจสอบสถานะการตอบกลับ
        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        } else {
            return ['success' => false, 'error' => $response->json()];
        }
    }

    public function sendNotificationITSTAFF($message, $stickerPackageId = null, $stickerId = null)
    {
        // Check if message is empty
        if (empty($message)) {
            return ['success' => false, 'error' => ['message' => 'Message cannot be empty']];
        }

        // Encode message to UTF-8
        $encodedMessage = mb_convert_encoding($message, 'UTF-8', 'auto');

        $postData = ['message' => $message];

        // Include sticker parameters if provided
        if ($stickerPackageId && $stickerId) {
            $postData['stickerPackageId'] = $stickerPackageId;
            $postData['stickerId'] = $stickerId;
        }


        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessTokenITSTAFF,
            'Content-Type'  => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://notify-api.line.me/api/notify', $postData);

        // dd($this->accessToken);
        // ตรวจสอบสถานะการตอบกลับ
        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        } else {
            return ['success' => false, 'error' => $response->json()];
        }
    }

}
