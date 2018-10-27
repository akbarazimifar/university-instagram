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

    public function testUserProfile()
    {
        self::$user = User::first();
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
            'ok' => true,
            'status_code' => 201,
            'description' => 'The follow request has been sent.'
        ]);

        // resend the request
        $response = $this->call('patch', Route('user.follow', $request_to_follow_user->username));
        $response->assertStatus(202);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok' => true,
            'status_code' => 202,
            'description' => 'There is an ongoing follow request that need to be accepted.'
        ]);

        // accept the follow request
        $relationship = UserRelationship::where('follower_id','=',self::$user->id)
            ->where('following_id','=',$request_to_follow_user->id)
            ->firstOrFail();
        $relationship->is_accepted = true;
        $relationship->save();

        // resend the request
        $response = $this->call('patch', Route('user.follow', $request_to_follow_user->username));
        $response->assertStatus(200);
        $response->assertJsonStructure(['ok', 'status_code', 'description']);
        $response->assertJson([
            'ok' => true,
            'status_code' => 200,
            'description' => 'User is already followed.'
        ]);
    }
}
