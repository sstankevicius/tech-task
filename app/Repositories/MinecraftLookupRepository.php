<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

/**
 * Class MinecraftLookupRepository
 *
 * @package App\Repositories
 */
class MinecraftLookupRepository implements GameLookupRepositoryInterface
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
        $data = $this->makeRequest($url);
        return $this->formatResponse($data);
    }

    /**
     * @param string $id
     * @return array
     */
    public function lookupById(string $id): array
    {
        $url = strtr($this->config['id_url'], ['{id}' => $id]);
        $data = $this->makeRequest($url);
        return $this->formatResponse($data);
    }

    /**
     * @param string $url
     * @return array
     */
    private function makeRequest(string $url): array
    {
        try {
            $response = $this->client->get($url, ['timeout' => 5]);
            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            throw new RuntimeException("Failed to fetch Minecraft data: " . $e->getMessage());
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function formatResponse(array $data): array
    {
        return [
            'username' => $data['name'],
            'id' => $data['id'],
            'avatar' => strtr($this->config['avatar_url'], ['{id}' => $data['id']]),
        ];
    }
}
