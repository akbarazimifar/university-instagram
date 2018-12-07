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
Route::post('register', [\App\Http\Controllers\UserController::class, 'register'])->name('user.register');
Route::post('logout', [\App\Http\Controllers\UserController::class, 'logout'])->name('user.logout');

Route::middleware(['auth:api'])->group(function () {
    Route::get('user/self',[\App\Http\Controllers\UserController::class, 'self'])->name('user.self');
    Route::get('feeds', [\App\Http\Controllers\FeedController::class, 'homepage'])->name('user.feeds');
    Route::get('search', [\App\Http\Controllers\UserController::class, 'search'])->name('user.search');
    Route::post('upload', [\App\Http\Controllers\MediaController::class, 'upload'])->name('user.media.upload');
});
Route::middleware(['auth:api', 'username'])->group(function () {
    Route::prefix('{username}')->name('user.')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'show'])->name('profile');
        Route::post('edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('profile.edit');

        Route::get('medias', [\App\Http\Controllers\MediaController::class, 'getUserMedias'])->name('medias');
        Route::get('media/{id}', [\App\Http\Controllers\MediaController::class, 'getUserMedia'])->name('media')->where('id', '[0-9]+');

        Route::get('media/{id}/likes', [\App\Http\Controllers\MediaController::class, 'getUserMediaLikes'])->name('media.likes')->where('id', '[0-9]+');
        Route::patch('media/{id}/like', [\App\Http\Controllers\MediaController::class, 'likeUserMedia'])->name('media.like')->where('id', '[0-9]+');
        Route::patch('media/{id}/disslike', [\App\Http\Controllers\MediaController::class, 'disslikeUserMedia'])->name('media.disslike')->where('id', '[0-9]+');
        Route::delete('media/{id}/delete', [\App\Http\Controllers\MediaController::class, 'delete'])->name('media.delete')->where('id', '[0-9]+');
        Route::patch('media/{id}/edit', [\App\Http\Controllers\MediaController::class, 'edit'])->name('media.edit')->where('id', '[0-9]+');

        Route::patch('follow', [\App\Http\Controllers\UserController::class, 'follow'])->name('follow');
        Route::patch('unfollow', [\App\Http\Controllers\UserController::class, 'unFollow'])->name('unfollow');

        Route::get('followers', [\App\Http\Controllers\UserController::class, 'followers'])->name('followers');
        Route::get('followings', [\App\Http\Controllers\UserController::class, 'followings'])->name('followings');
    });
});