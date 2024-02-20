<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinanceService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchLatestRates(): array
    {
        $response = $this->client->request('GET', 'https://api.binance.com/api/v3/ticker/bookTicker?symbol=BTCUSDT');
        $data = $response->toArray();

        return [
            'bid' => $data['bidPrice'],
            'ask' => $data['askPrice'],
            'timestamp' => new \DateTime(),
        ];
    }
}
