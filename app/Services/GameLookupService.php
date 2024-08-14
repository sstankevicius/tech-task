<?php

namespace App\Services;

use InvalidArgumentException;
use RuntimeException;
use Illuminate\Support\Facades\Cache;

/**
 * Class GameLookupService
 *
 * @package App\Services
 */
class GameLookupService implements GameLookupServiceInterface
{
    private array $repositories;
    private int $cacheTtl;

    /**
     * @param array $repositories
     * @param int $cacheTtl
     */
    public function __construct(array $repositories, int $cacheTtl)
    {
        $this->repositories = $repositories;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @param string $type
     * @param string $identifier
     * @param bool $isUsername
     * @return array
     */
    public function lookup(string $type, string $identifier, bool $isUsername): array
    {
        if (!isset($this->repositories[$type])) {
            throw new InvalidArgumentException("Unsupported game type: {$type}");
        }

        $repository = $this->repositories[$type];
        $cacheKey = "{$type}:" . ($isUsername ? 'username:' : 'id:') . $identifier;

        try {
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($repository, $identifier, $isUsername) {
                return $isUsername
                    ? $repository->lookupByUsername($identifier)
                    : $repository->lookupById($identifier);
            });
        } catch (\Exception $e) {
            throw new RuntimeException("Lookup failed: " . $e->getMessage());
        }
    }
}
