<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use InvalidArgumentException;

/**
 * Class SteamLookupRepository
 *
 * @package App\Repositories
 */
class SteamLookupRepository implements GameLookupRepositoryInterface
{
    private Client $client;
    private array $config;

    /**
     * @param Client $client
     * @param array $config
     */
    public function __construct(Client $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param string $username
     * @return array
     */
    public function lookupByUsername(string $username): array
    {
        throw new InvalidArgumentException("Steam does not support username lookups");
    }

    /**
     * @param string $id
     * @return array
     */
    public function lookupById(string $id): array
    {
        $url = strtr($this->config['id_url'], ['{id}' => $id]);
        return $this->makeRequest($url);
    }

    /**
     * @param string $url
     * @return array
     */
    private function makeRequest(string $url): array
    {
        try {
            $response = $this->client->get($url, ['timeout' => 5]);
            $data = json_decode($response->getBody(), true);

            return [
                'username' => $data['username'],
                'id' => $data['id'],
                'avatar' => $data['meta']['avatar'],
            ];
        } catch (GuzzleException $e) {
            throw new RuntimeException("Failed to fetch Steam data: " . $e->getMessage());
        }
    }
}
