<?php

namespace Tests\Feature;

use App\Services\GameLookupServiceInterface;
use Tests\TestCase;
use Mockery;

class LookupControllerTest extends TestCase
{
    public function testLookupWithValidData()
    {
        $mockService = Mockery::mock(GameLookupServiceInterface::class);
        $mockService->shouldReceive('lookup')
            ->once()
            ->with('minecraft', 'testuser', true)
            ->andReturn(['username' => 'testuser', 'id' => '12345', 'avatar' => 'http://example.com/avatar.png']);

        $this->app->instance(GameLookupServiceInterface::class, $mockService);

        $response = $this->get('/lookup?type=minecraft&username=testuser');

        $response->assertStatus(200)
            ->assertJson([
                'username' => 'testuser',
                'id' => '12345',
                'avatar' => 'http://example.com/avatar.png'
            ]);
    }

    public function testLookupWithMissingUsernameAndId()
    {
        $response = $this->get('/lookup?type=minecraft');

        $response->assertStatus(400)
            ->assertJson(['error' => 'Username or ID is required']);
    }

    public function testLookupWithInvalidGameType()
    {
        $mockService = Mockery::mock(GameLookupServiceInterface::class);
        $mockService->shouldReceive('lookup')
            ->once()
            ->andThrow(new \InvalidArgumentException("Unsupported game type: invalidgame"));

        $this->app->instance(GameLookupServiceInterface::class, $mockService);

        $response = $this->get('/lookup?type=invalidgame&username=testuser');

        $response->assertStatus(400)
            ->assertJson(['error' => 'Unsupported game type: invalidgame']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
