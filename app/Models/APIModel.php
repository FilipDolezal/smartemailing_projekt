<?php

declare(strict_types=1);

namespace App\Models;

use GuzzleHttp\Client;

class APIModel
{
    public Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getPointsOfSale(): array
    {
        $response = $this->client->get("https://data.pid.cz/pointsOfSale/json/pointsOfSale.json");
        return json_decode($response->getBody()->getContents());
    }
}
