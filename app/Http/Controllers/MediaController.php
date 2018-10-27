<?php

namespace App\Http\Controllers;

use App\Media;
use App\MediaLike;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

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
                'ok'          => true,
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

    public function likeUserMedia(Request $request)
    {
        $user = $request->get('user_object');
        $result = User::checkUserViewPermission($user);
        if (!empty($result)) return $result;


        // check media ownership
        try {
            $media = Media::findOrFail((int) $request->route('id'));
        } catch (\Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => 'Wrong media id.'
            ], 400);
        }

        if ($media->user_id !== $user->id) return response([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'Ownership of this media is not for the requested user.'
        ], 403);

        try {
            MediaLike::firstOrNew([
                'user_id'  => Auth::user()->id,
                'media_id' => $media->id
            ]);
            return response([
                'ok'          => true,
                'status_code' => 200,
                'description' => 'Post liked.'
            ], 200);
        } catch (Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 500,
                'description' => 'Internal error occurs when liking the media.'
            ], 500);
        }
    }
}
