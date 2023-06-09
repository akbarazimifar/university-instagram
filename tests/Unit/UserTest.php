<?php

namespace Tests\Unit;

use App\User;
use App\UserRelationship;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class UserTest extends TestCase
{
    private static $user;

    public function testRegister()
    {
        $response = $this->call('post', route('user.register'), [
            'username'         => 'test____test_' . rand(0, 20),
            'first_name'       => 'test',
            'last_name'        => 'test',
            'password'         => '123456',
            'password_confirm' => '123456',
            'email'            => 'a@a' . rand(0, 20) . '.com'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'username',
                'first_name',
                'last_name',
            ],
            'token'
        ]);
        User::findOrFail(json_decode($response->getContent())->user->id)->delete();
    }

    public function testLogout()
    {
        self::$user = User::first();
        Passport::actingAs(self::$user);
        $response = $this->call('post', route('user.logout'));
        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
    }

    public function testUserSelf()
    {
        Passport::actingAs(self::$user);
        $response = $this->call('get', route('user.self', self::$user->username));
        dd($response->getContent());
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

    public function testSearch()
    {
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
        $response = $this->call('get', route('user.profile', self::$user->username));
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
                    'profile'
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
                    'profile'
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

    public function testUnFollow()
    {
        // delete all relationships
        UserRelationship::truncate();
        Passport::actingAs(self::$user);

        // change profile status to private
        $request_to_unfollow_user = User::findOrFail(2);

        $response = $this->call('patch', Route('user.unfollow', $request_to_unfollow_user->username));
        $response->assertStatus(406);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => false,
            'status_code' => 406,
            'description' => 'User it is not followed.'
        ]);

        UserRelationship::insert([
            ['follower_id' => self::$user->id, 'following_id' => $request_to_unfollow_user->id, 'is_accepted' => 1]
        ]);

        $response = $this->call('patch', Route('user.unfollow', $request_to_unfollow_user->username));
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'User successfully unfollowed.'
        ]);
    }

    public function testEdit()
    {
        Passport::actingAs(self::$user);
        $response = $this->json('patch', Route('user.profile.edit', self::$user->username), [
            'first_name'            => 'test',
            'last_name'             => 'test',
            'password'              => 123456789,
            'password_confirmation' => 123456789,
            'profile_status'        => 'PUBLIC',
            'profile_photo'         => UploadedFile::fake()->image('avatar.jpg', 500, 500)
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'Profile updated.'
        ]);
    }
}
