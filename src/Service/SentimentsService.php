<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SentimentsService
{
    private HttpClientInterface $httpClient;

    public function __construct(
        HttpClientInterface $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

    public function analyzeSentiments(array $texts): array
    {
        $response = $this->httpClient->request('POST', 'http://localhost:5000/predict', [
            'json' => ['texts' => $texts],
            'headers' => ['Content-Type' => 'application/json']
        ]);

        if ($response->getStatusCode() === 200) {
            return $response->toArray();
        }

        return [];
    }
}