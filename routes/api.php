<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('feeds', 'FeedController@homepage')->middleware('auth:api')->name('user.feeds');
Route::get('search', 'UserController@search')->middleware('auth:api')->name('user.search');
Route::middleware(['auth:api', 'username'])->group(function () {
    Route::prefix('{username}')->name('user.')->group(function () {
        Route::get('.', 'UserController@show')->name('profile');
        Route::get('medias', 'MediaController@getUserMedias')->name('medias');
        Route::get('media/{id}', 'MediaController@getUserMedia')->name('media')->where('id', '[0-9]+');

        Route::get('media/{id}/likes', 'MediaController@getUserMediaLikes')->name('media.likes')->where('id', '[0-9]+');
        Route::patch('media/{id}/like', 'MediaController@likeUserMedia')->name('media.like')->where('id', '[0-9]+');

        Route::patch('follow', 'UserController@follow')->name('follow');
        Route::patch('unfollow', 'UserController@unFollow')->name('unfollow');

        Route::get('followers', 'UserController@followers')->name('followers');
        Route::get('followings', 'UserController@followings')->name('followings');
    });
});