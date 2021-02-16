<?php

declare(strict_types=1);

namespace Arendach\VodafoneName\Services;

use Throwable;

class CurlRequest
{
    private $logger;

    public function __construct()
    {
        $this->logger = resolve(Logger::class);
    }

    public function post(string $uri, array $headers = [], array $data = [])
    {
        return $this->curl($uri, $headers, $data);
    }

    public function get(string $uri, array $headers = [], array $data = [])
    {
        return $this->curl($uri, $headers, $data, 'GET');
    }

    public function getToken(): ?string
    {
        try {

            $login = config('vodafone-name.middleware-login');
            $password = config('vodafone-name.middleware-password');

            $auth = "Basic " . base64_encode("{$login}:{$password}");

            $result = $this->curl('/uaa/oauth/token?grant_type=client_credentials', [
                "Authorization: {$auth}"
            ]);


            return $result['access_token'] ?? null;

        } catch (Throwable $exception) {

            $this->logger->save($exception);

            return null;

        }
    }

    private function curl($uri, $headers = [], $data = [], $method = 'POST')
    {
        $host = config('vodafone-name.middleware-host');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $host . $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $data
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        try {

            return json_decode($response, true);

        } catch (Throwable $exception) {

            return [];

        }
    }
}