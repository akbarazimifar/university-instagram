<?php

namespace Tests\Unit;

use App\Media;
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
        self::$user = User::first();
        Passport::actingAs(User::findOrFail(2));
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

    public function testUserMedia()
    {
        Passport::actingAs(User::findOrFail(3));
        $media = Media::where('user_id', '=', self::$user->id)->firstOrFail();
        $parameters = [
            'username' => self::$user->username,
            'id'       => $media->id
        ];
        $response = $this->call('get', Route('user.media', $parameters));
        switch ($response->getStatusCode()) {
            case 403:
                $response->assertStatus(403);
                $response->assertJsonStructure(['ok', 'description']);
                break;
            case 200:
                $response->assertStatus(200);
                $response->assertJsonStructure([
                    'id',
                    'width',
                    'height',
                    'file_path',
                    'thumb_path',
                    'caption',
                    'likes_count',
                    'likes' => []
                ]);
                break;
            default:
                $this->fail('Unknown header status code returned (' . $response->getStatusCode() . ').');
        }
    }
}
