<?php

namespace Tests\Unit;

use App\Services\GameLookupService;
use App\Repositories\GameLookupRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class GameLookupServiceTest extends TestCase
{
    protected $mockMinecraftRepo;
    protected $mockSteamRepo;
    protected $mockXblRepo;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockMinecraftRepo = Mockery::mock(GameLookupRepositoryInterface::class);
        $this->mockSteamRepo = Mockery::mock(GameLookupRepositoryInterface::class);
        $this->mockXblRepo = Mockery::mock(GameLookupRepositoryInterface::class);

        $this->service = new GameLookupService([
            'minecraft' => $this->mockMinecraftRepo,
            'steam' => $this->mockSteamRepo,
            'xbl' => $this->mockXblRepo,
        ], 3600);
    }

    public function testLookupWithValidMinecraftUsername()
    {
        $expectedResult = ['username' => 'testuser', 'id' => '12345', 'avatar' => 'http://example.com/avatar.png'];

        $this->mockMinecraftRepo->shouldReceive('lookupByUsername')
            ->once()
            ->with('testuser')
            ->andReturn($expectedResult);

        Cache::shouldReceive('remember')
            ->once()
            ->andReturnUsing(function ($key, $ttl, $callback) use ($expectedResult) {
                return $callback();
            });

        $result = $this->service->lookup('minecraft', 'testuser', true);

        $this->assertEquals($expectedResult, $result);
    }

    public function testLookupWithInvalidGameType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->lookup('invalidgame', 'testuser', true);
    }

    public function testLookupWithSteamUsernameThrowsException()
    {
        $this->mockSteamRepo->shouldReceive('lookupByUsername')
            ->once()
            ->andThrow(new InvalidArgumentException("Steam does not support username lookups"));

        $this->expectException(RuntimeException::class);
        $this->service->lookup('steam', 'testuser', true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
