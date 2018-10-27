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

Route::middleware(['auth:api', 'username'])->group(function () {
    Route::prefix('{username}')->name('user.')->group(function () {
        Route::get('.', 'UserController@show')->name('profile');
        Route::get('medias', 'MediaController@getUserMedias')->name('medias');
        Route::get('media/{id}', 'MediaController@getUserMedia')->name('media')->where('id', '[0-9]+');

        Route::patch('follow','UserController@follow')->name('follow');
        Route::patch('unfollow','UserController@unFollow')->name('unfollow');
    });
});