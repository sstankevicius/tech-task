<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * Class XblLookupRepository
 *
 * @package App\Repositories
 */
class XblLookupRepository implements GameLookupRepositoryInterface
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
        $url = strtr($this->config['username_url'], ['{username}' => $username]);
        return $this->makeRequest($url);
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
            throw new RuntimeException("Failed to fetch XBL data: " . $e->getMessage());
        }
    }
}
