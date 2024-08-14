<?php

namespace App\Services;

/**
 * Interface GameLookupServiceInterface
 *
 * @package App\Services
 */
interface GameLookupServiceInterface
{
    /**
     * @param string $type
     * @param string $identifier
     * @param bool $isUsername
     * @return array
     */
    public function lookup(string $type, string $identifier, bool $isUsername): array;
}
