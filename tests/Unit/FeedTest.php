<?php

namespace Tests\Unit;

use App\User;
use App\UserRelationship;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class FeedTest extends TestCase
{
    private static $user;

    public function testHomePage()
    {
        self::$user = User::first();
        Passport::actingAs(self::$user);
        UserRelationship::truncate();
        UserRelationship::insert([
            ['follower_id' => 1, 'following_id' => 2, 'is_accepted' => 1],
            ['follower_id' => 2, 'following_id' => 1, 'is_accepted' => 1]
        ]);
        $response = $this->call('get', Route('user.feeds'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                [
                    'id',
                    'width',
                    'height',
                    'file_path',
                    'thumb_path',
                    'caption',
                    'created_at',
                    'likes_count',
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
                            'height'
                        ]
                    ]
                ]
            ],
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
    }
}
