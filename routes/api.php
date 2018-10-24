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
    Route::get('{username}', 'UserController@show')->name('user.profile');
    Route::get('{username}/medias', 'MediaController@getUserMedias')->name('user.medias');
});