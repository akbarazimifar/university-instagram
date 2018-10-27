<?php

namespace Tests\Unit;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MediaTest extends TestCase
{
    private static $user;

    public function testUserMedias()
    {
        self::$user = User::findOrFail(2);
        Passport::actingAs(User::first());
        $response = $this->call('get', Route('user.medias', self::$user->username));
        switch ($response->getStatusCode()) {
            case 403:
                $response->assertStatus(403);
                $response->assertJsonStructure(['ok', 'description']);
                break;
            case 200:
                $response->assertStatus(200);
                $response->assertJsonStructure([
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total'
                ]);
                break;
            default:
                $this->fail('Unknown header status code returned (' . $response->getStatusCode() . ').');
        }
    }
}
