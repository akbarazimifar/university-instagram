<?php

namespace Tests\Unit;

use App\User;
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
}
