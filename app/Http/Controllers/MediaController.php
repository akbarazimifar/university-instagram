<?php

namespace App\Http\Controllers;

use App\Media;
use App\MediaLike;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
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
            $media = Media::findOrFail((int)$request->route('id'));
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

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'    => 'required|file|image|dimensions:min_width=400,min_height=400',
            'caption' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => $validator->errors()
            ], 400);
        }
        $file_path = Storage::put('public/medias', $request->file('file'));
        try {
            $img = Image::make($request->file('file'));
            $width = $img->width();
            $height = $img->height();
            $img->fit(300, 300, function ($constraint) {
                $constraint->upsize();
            });
            $save_path = 'public/storage/medias/thumbs/';
            if (!file_exists($save_path)) {
                mkdir($save_path, 666, true);
            }
            $img->save('public/storage/medias/thumbs/' . basename($file_path));
        } catch (\Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 500,
                'description' => $e->getMessage()
            ], 500);
        }
        return Media::create([
            'user_id'    => Auth::user()->id,
            'file_path'  => basename($file_path),
            'thumb_path' => 'thumbs/' . basename($file_path),
            'width'      => $width,
            'height'     => $height,
            'caption'    => $request->get('caption')
        ]);
    }

    public function delete(Request $request)
    {
        $user = $request->get('user_object');
        if ($user->id !== Auth::user()->id) return response([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'This media does not belongs to you.'
        ], 403);

        try {
            $media = Media::findOrFail((int)$request->route('id'));
        } catch (\Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => 'Wrong media id.'
            ], 400);
        }

        if ($media->user()->id !== Auth::user()->id) return response([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'This media does not belongs to you.'
        ], 403);

        try {
            $media->delete();
            return response([
                'ok'          => true,
                'status_code' => 200,
                'description' => 'Media successfully deleted.'
            ], 200);
        } catch (\Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 500,
                'description' => 'Could not delete media.'
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caption' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => $validator->errors()
            ], 400);
        }

        $user = $request->get('user_object');
        if ($user->id !== Auth::user()->id) return response([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'This media does not belongs to you.'
        ], 403);

        try {
            $media = Media::findOrFail((int)$request->route('id'));
        } catch (\Exception $e) {
            return response([
                'ok'          => false,
                'status_code' => 400,
                'description' => 'Wrong media id.'
            ], 400);
        }

        if ($media->user->id !== Auth::user()->id) return response([
            'ok'          => false,
            'status_code' => 403,
            'description' => 'This media does not belongs to you.'
        ], 403);

        $media->update($request->only(['caption']));
        return response([
            'ok'          => true,
            'status_code' => 200,
            'description' => 'Media updated.'
        ], 200);
    }
}
