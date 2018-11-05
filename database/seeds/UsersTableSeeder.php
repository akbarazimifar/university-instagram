<?php

use Illuminate\Database\Seeder;
use App\UserRelationship;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 50)->create()->each(function ($user) {
            for ($i = 0; $i < 5; $i++) {
                $user->medias()->save(factory(App\Media::class)->make());
            }
            $user->profile()->save(factory(App\UserProfile::class)->make());
        });
        UserRelationship::create(['follower_id' => 1, 'following_id' => 2, 'is_accepted' => 1]);
    }
}
