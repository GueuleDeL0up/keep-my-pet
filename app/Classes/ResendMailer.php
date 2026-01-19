<?php

class ResendMailer
{
  private $api_key;
  private $api_url = 'https://api.resend.com/emails';

  public function __construct($api_key)
  {
    $this->api_key = $api_key;
  }

  public function send($to, $subject, $html, $from = 'noreply@resend.dev')
  {
    $payload = [
      'from' => $from,
      'to' => $to,
      'subject' => $subject,
      'html' => $html
    ];

    // Try cURL first
    if (function_exists('curl_init')) {
      return $this->sendWithCurl($payload);
    }

    // Fallback to streams
    return $this->sendWithStreams($payload);
  }

  private function sendWithCurl($payload)
  {
    $ch = curl_init($this->api_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Authorization: Bearer ' . $this->api_key,
      'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
      return [
        'success' => false,
        'error' => 'cURL Error: ' . $error,
        'http_code' => $http_code
      ];
    }

    $decoded = json_decode($response, true);

    if ($http_code >= 200 && $http_code < 300) {
      return [
        'success' => true,
        'message' => 'Email sent successfully via Resend',
        'id' => $decoded['id'] ?? null,
        'http_code' => $http_code
      ];
    } else {
      return [
        'success' => false,
        'error' => $decoded['message'] ?? 'Unknown error',
        'http_code' => $http_code
      ];
    }
  }

  private function sendWithStreams($payload)
  {
    $context = stream_context_create([
      'http' => [
        'method' => 'POST',
        'header' => [
          'Authorization: Bearer ' . $this->api_key,
          'Content-Type: application/json'
        ],
        'content' => json_encode($payload),
        'timeout' => 10,
        'verify_peer' => false,
        'verify_peer_name' => false
      ],
      'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
      ]
    ]);

    $response = @file_get_contents($this->api_url, false, $context);

    if ($response === false) {
      return [
        'success' => false,
        'error' => 'Stream Error: Could not connect to Resend API',
        'http_code' => 0
      ];
    }

    $decoded = json_decode($response, true);

    if (isset($decoded['id'])) {
      return [
        'success' => true,
        'message' => 'Email sent successfully via Resend',
        'id' => $decoded['id'],
        'http_code' => 200
      ];
    } else {
      return [
        'success' => false,
        'error' => $decoded['message'] ?? 'Unknown error',
        'http_code' => 400
      ];
    }
  }
}
