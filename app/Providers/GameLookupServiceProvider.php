<?php

namespace App\Providers;

use App\Services\GameLookupService;
use App\Services\GameLookupServiceInterface;
use App\Repositories\MinecraftLookupRepository;
use App\Repositories\SteamLookupRepository;
use App\Repositories\XblLookupRepository;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class GameLookupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(GameLookupServiceInterface::class, function ($app) {
            $config = config('game_lookup');
            $repositories = [
                'minecraft' => new MinecraftLookupRepository(new Client(), $config['minecraft']),
                'steam' => new SteamLookupRepository(new Client(), $config['steam']),
                'xbl' => new XblLookupRepository(new Client(), $config['xbl']),
            ];
            return new GameLookupService($repositories, $config['cache_ttl']);
        });
    }
}
