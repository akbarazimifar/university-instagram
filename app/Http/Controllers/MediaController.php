<?php

namespace App\Http\Controllers;

use App\Media;
use App\MediaLike;
use App\User;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function getUserMedias(Request $request)
    {
        $user = $request->get('user_object');
        $result = User::checkUserViewPermission($user);
        if (!empty($result)) return $result;
        return $user->medias()
            ->paginate(config('instagram.paginate_per_page.medias'));
    }

    public function getUserMedia(Request $request)
    {
        $user = $request->get('user_object');
        $result = User::checkUserViewPermission($user);
        if (!empty($result)) return $result;
        try {
            return Media::whereId((int)$request->route('id'))
                ->whereUserId($user->id)
                ->withCount('likes')
                ->with(['likes'])
                ->firstOrFail();
        } catch (\Exception $e) {
            return response(['ok' => false, 'description' => 'Unknown media id.'], 400);
        }
    }

    public function getUserMediaLikes(Request $request)
    {
        $user = $request->get('user_object');
        $result = User::checkUserViewPermission($user);
        if (!empty($result)) return $result;
        try {
            $result = MediaLike::whereMediaId((int)$request->route('id'))
                ->whereUserId($user->id)
                ->with(['user', 'user.profile'])
                ->get();
            if (empty($result->toArray())) return response([
                'ok' => true,
                'status_code' => 200,
                'description' => 'This media does not have any likes'
            ], 200);
            return $result;
        } catch (\Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => 'Wrong media or user id.'
            ], 400);
        }
    }
}
