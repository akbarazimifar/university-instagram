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
Route::middleware(['auth:api'])->group(function () {
    Route::get('feeds', 'FeedController@homepage')->name('user.feeds');
    Route::get('search', 'UserController@search')->name('user.search');
    Route::post('upload', 'MediaController@upload')->name('user.media.upload');
});
Route::middleware(['auth:api', 'username'])->group(function () {
    Route::prefix('{username}')->name('user.')->group(function () {
        Route::get('.', 'UserController@show')->name('profile');
        Route::patch('edit', 'UserController@edit')->name('profile.edit');

        Route::get('medias', 'MediaController@getUserMedias')->name('medias');
        Route::get('media/{id}', 'MediaController@getUserMedia')->name('media')->where('id', '[0-9]+');

        Route::get('media/{id}/likes', 'MediaController@getUserMediaLikes')->name('media.likes')->where('id', '[0-9]+');
        Route::patch('media/{id}/like', 'MediaController@likeUserMedia')->name('media.like')->where('id', '[0-9]+');
        Route::delete('media/{id}/delete', 'MediaController@delete')->name('media.delete')->where('id', '[0-9]+');
        Route::patch('media/{id}/edit', 'MediaController@edit')->name('media.edit')->where('id', '[0-9]+');

        Route::patch('follow', 'UserController@follow')->name('follow');
        Route::patch('unfollow', 'UserController@unFollow')->name('unfollow');

        Route::get('followers', 'UserController@followers')->name('followers');
        Route::get('followings', 'UserController@followings')->name('followings');
    });
});