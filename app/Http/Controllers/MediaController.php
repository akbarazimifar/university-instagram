<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function getUserMedias(Request $request)
    {
        return $request->get('user_object')
            ->with('medias')
            ->withCount('medias')
            ->paginate(config('instagram.paginate_per_page.medias'));
    }
}
