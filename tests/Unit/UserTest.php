<?php

namespace Tests\Unit;

use App\User;
use App\UserRelationship;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class UserTest extends TestCase
{
    private static $user;

    public function testSearch()
    {
        self::$user = User::first();
        Passport::actingAs(self::$user);
        $response = $this->call('get', Route('user.search', ['query' => self::$user->username]));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'profile_status',
                    'followers_count',
                    'followings_count',
                    'medias_count',
                    'profile' => [
                        'file_path',
                        'thumb_path',
                        'width',
                        'height'
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

        $response = $this->call('get', Route('user.search', ['query' => 'a']));
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'ok',
            'status_code',
            'description' => [
                'query' => []
            ]
        ]);
    }

    public function testUserProfile()
    {
        Passport::actingAs(self::$user);
        $response = $this->call('get', Route('user.profile', self::$user->username));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'username',
            'first_name',
            'last_name',
            'profile_status',
            'followers_count',
            'followings_count',
            'medias_count',
            'profile' => [
                'file_path',
                'thumb_path',
                'width',
                'height'
            ]
        ]);
    }

    public function testFollowers()
    {
        UserRelationship::truncate();
        UserRelationship::insert([
            ['follower_id' => 1, 'following_id' => 2, 'is_accepted' => 1],
            ['follower_id' => 2, 'following_id' => 1, 'is_accepted' => 1]
        ]);
        Passport::actingAs(self::$user);
        $response = $this->call('get', Route('user.followers', User::first()->username));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                [
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

    public function testFollowings()
    {
        UserRelationship::truncate();
        UserRelationship::insert([
            ['follower_id' => 1, 'following_id' => 2, 'is_accepted' => 1],
            ['follower_id' => 2, 'following_id' => 1, 'is_accepted' => 1]
        ]);
        Passport::actingAs(self::$user);
        $response = $this->call('get', Route('user.followings', User::first()->username));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                [
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

    public function testFollow()
    {
        // delete all relationships
        UserRelationship::truncate();

        Passport::actingAs(self::$user);

        // change profile status to private
        $request_to_follow_user = User::findOrFail(2);
        $request_to_follow_user->profile_status = "PRIVATE";
        $request_to_follow_user->save();

        // send the request
        $response = $this->call('patch', Route('user.follow', $request_to_follow_user->username));
        $response->assertStatus(201);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 201,
            'description' => 'The follow request has been sent.'
        ]);

        // resend the request
        $response = $this->call('patch', Route('user.follow', $request_to_follow_user->username));
        $response->assertStatus(202);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 202,
            'description' => 'There is an ongoing follow request that need to be accepted.'
        ]);

        // accept the follow request
        $relationship = UserRelationship::where('follower_id', '=', self::$user->id)
            ->where('following_id', '=', $request_to_follow_user->id)
            ->firstOrFail();
        $relationship->is_accepted = true;
        $relationship->save();

        // resend the request
        $response = $this->call('patch', Route('user.follow', $request_to_follow_user->username));
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'User is already followed.'
        ]);

        // delete all relationships
        UserRelationship::truncate();
        $request_to_follow_user->profile_status = "PUBLIC";
        $request_to_follow_user->save();

        // resend the request
        $response = $this->call('patch', Route('user.follow', $request_to_follow_user->username));
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'User successfully followed.'
        ]);
    }
}
