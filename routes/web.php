<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\ConnectController;
use App\Http\Controllers\LinkedIn\PostController as LinkedInPostController;
use App\Http\Controllers\Facebook\PostController as FacebookPostController;
use App\Http\Controllers\User\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::name('user.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/connect', [ConnectController::class, 'index'])->name('connect');

        Route::get('/connect/facebook', [ConnectController::class, 'facebook'])->name('connect.facebook');
        Route::get('/connect/facebook/callback', [ConnectController::class, 'facebookCallback'])->name('connect.facebook.callback');
        Route::get('/connect/linkedin', [ConnectController::class, 'linkedin'])->name('connect.linkedin');
        Route::get('/connect/linkedin/callback', [ConnectController::class, 'linkedinCallback'])->name('connect.linkedin.callback');
        Route::get('/logout', [ConnectController::class, 'logout'])->name('logout');


        // Post
        Route::get('/post', [PostController::class, 'create'])->name('post.create');
        Route::post('/post', [PostController::class, 'store'])->name('post.store');


        Route::prefix('/facebook')
            ->name('facebook.')
            ->group(function () {
                Route::get('/post/image/create', [FacebookPostController::class, 'imageCreate'])->name('post.image.create');
                Route::post('/post/image/store', [FacebookPostController::class, 'imageStore'])->name('post.image.store');

                Route::get('/post/text/create', [FacebookPostController::class, 'textCreate'])->name('post.text.create');
                Route::post('/post/text/store', [FacebookPostController::class, 'textStore'])->name('post.text.store');

                Route::get('/post/video/create', [FacebookPostController::class, 'videoCreate'])->name('post.video.create');
                Route::post('/post/video/store', [FacebookPostController::class, 'videoStore'])->name('post.video.store');
            });

        Route::prefix('/linkedin')
            ->name('linkedin.')
            ->group(function () {
                Route::get('/post/text/create', [LinkedInPostController::class, 'textCreate'])->name('post.text.create');
                Route::post('/post/text/store', [LinkedInPostController::class, 'textStore'])->name('post.text.store');

                Route::get('/post/video/create', [LinkedInPostController::class, 'videoCreate'])->name('post.video.create');
                Route::post('/post/video/store', [LinkedInPostController::class, 'videoStore'])->name('post.video.store');
            });
    });




Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard.index');
        })->name('dashboard');
    });
