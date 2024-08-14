<?php

namespace App\Repositories;

/**
 * Interface GameLookupRepositoryInterface
 *
 * @package App\Repositories
 */
interface GameLookupRepositoryInterface
{
    /**
     * @param string $username
     * @return array
     */
    public function lookupByUsername(string $username): array;

    /**
     * @param string $id
     * @return array
     */
    public function lookupById(string $id): array;
}
