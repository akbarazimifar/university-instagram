<?php

namespace Tests\Unit;

use App\Media;
use App\MediaLike;
use App\User;
use App\UserRelationship;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class MediaTest extends TestCase
{
    private static $user;
    private static $media;

    public function testUserMedias()
    {
        self::$user = User::first();
        $second_user = User::findOrFail(2);
        Passport::actingAs($second_user);
        UserRelationship::truncate();
        UserRelationship::insert([
            ['follower_id' => 1, 'following_id' => 2, 'is_accepted' => 1],
            ['follower_id' => 2, 'following_id' => 1, 'is_accepted' => 1]
        ]);
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

    public function testUserMediaLikes()
    {
        // delete all likes
        MediaLike::truncate();
        Passport::actingAs(self::$user);

        $media = Media::where('user_id', '=', self::$user->id)->firstOrFail();
        $parameters = [
            'username' => self::$user->username,
            'id'       => $media->id
        ];
        $response = $this->call('get', Route('user.media.likes', $parameters));
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'This media does not have any likes'
        ]);

        // add a like
        try {
            MediaLike::create(['user_id' => self::$user->id, 'media_id' => $media->id]);
        } catch (\Exception $e) {
            $this->fail('Could not add new like to media');
            return false;
        }

        // resend the request
        $response = $this->call('get', Route('user.media.likes', $parameters));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                'user_id',
                'media_id',
                'user' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'profile_status',
                    'profile' => [
                        'file_path',
                        'thumb_path',
                        'width',
                        'height',
                    ]
                ],
            ]
        ]);
    }

    public function testLikeUserMedia()
    {
        // delete all likes
        MediaLike::truncate();
        Passport::actingAs(self::$user);
        $target_user = User::findOrFail(2);
        $target_user->profile_status = "PRIVATE";
        $target_user->save();
        $target_media = Media::whereUserId($target_user->id)->firstOrFail();

        $parameters = [
            'username' => $target_user->username,
            'id'       => 99999999
        ];
        $response = $this->call('patch', Route('user.media.like', $parameters));
        $response->assertStatus(400);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => false,
            'status_code' => 400,
            'description' => 'Wrong media id.'
        ]);

        $parameters = [
            'username' => self::$user->username,
            'id'       => $target_media->id
        ];
        $response = $this->call('patch', Route('user.media.like', $parameters));
        $response->assertStatus(403);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'Ownership of this media is not for the requested user.'
        ]);

        $parameters = [
            'username' => $target_user->username,
            'id'       => $target_media->id
        ];

        $response = $this->call('patch', Route('user.media.like', $parameters));
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'Post liked.'
        ]);
    }

    public function testUpload()
    {
        Passport::actingAs(self::$user);
        $response = $this->json('POST', Route('user.media.upload'), [
            'file'    => UploadedFile::fake()->image('avatar.jpg', 500, 500),
            'caption' => ''
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'file_path',
            'thumb_path',
            'width',
            'height',
            'caption',
            'created_at',
            'id'
        ]);
    }

    public function testEdit()
    {
        Passport::actingAs(self::$user);
        self::$media = Media::create([
            'user_id'    => self::$user->id,
            'width'      => 1,
            'height'     => 1,
            'file_path'  => 'a',
            'thumb_path' => 'a',
            'caption'    => 'a'
        ]);
        $response = $this->json('patch', Route('user.media.edit', [
            'username' => self::$user->username,
            'id'       => self::$media->id
        ]), ['caption' => 'lol']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'Media updated.'
        ]);
        $wrong_media = Media::where('user_id', '!=', self::$user->id)->firstOrFail();
        $response = $this->json('patch', Route('user.media.edit', [
            'username' => self::$user->username,
            'id'       => $wrong_media->id
        ]), ['caption' => 'lol']);
        $response->assertStatus(403);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'This media does not belongs to you.'
        ]);
    }

    public function testDelete()
    {
        Passport::actingAs(self::$user);
        $response = $this->json('patch', Route('user.media.edit', [
            'username' => self::$user->username,
            'id'       => self::$media->id
        ]), ['caption' => 'lol']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'Media updated.'
        ]);
    }
}
