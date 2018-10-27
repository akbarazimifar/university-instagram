<?php

namespace App\Http\Controllers;

use App\Media;
use App\User;
use App\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return Media::whereId((int) $request->route('id'))
                ->whereUserId($user->id)
                ->withCount('likes')
                ->with(['likes'])
                ->firstOrFail();
        } catch (\Exception $e) {
            return response(['ok' => false, 'description' => 'Unknown media id.'], 400);
        }
    }
}
