<?php

namespace App\Service;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class VonageSmsService
{
    private Client $client;
    private string $from;

    public function __construct(string $apiKey, string $apiSecret, string $from)
    {
        $credentials = new Basic($apiKey, $apiSecret);
        $this->client = new Client($credentials);
        $this->from = $from;
    }

    public function sendSms(string $to, string $message): void
    {
        $sms = new SMS($to, $this->from, $message);
        $this->client->sms()->send($sms); // ✅
    }
}

