<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function getUserMedias(Request $request)
    {
        try{
            return $request->get('user_object')->medias()->paginate(config('instagram.paginate_per_page.medias'));
        }catch (\Exception $e){
            dd($e->getMessage());
        }
    }
}
