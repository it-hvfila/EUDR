<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class LineMessagingService
{
    protected $accessToken;

    public function __construct()
    {
        $this->accessToken = env('LINE_MESSAGING_ACCESS_TOKEN');
    }

    public function LINE_MESSAGING_ACCESS_TOKEN($groupId, $message)
    {
        $messages = [['type' => 'text', 'text' => $message]];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])->post('https://api.line.me/v2/bot/message/push', [
            'to' => $groupId,
            'messages' => $messages
        ]);

        return $response->json();
    }
}
