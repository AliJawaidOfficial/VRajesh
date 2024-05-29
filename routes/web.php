<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\DashboardController;

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ConnectController;
use App\Http\Controllers\User\PostController;

use App\Http\Controllers\User\LinkedIn\PipelineController;
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


        Route::middleware('guest')->group(function () {
            Route::get('login', [AuthController::class, 'login'])->name('login');
            Route::post('login', [AuthController::class, 'loginStore']);

            Route::get('register', [AuthController::class, 'register'])->name('register');
            Route::post('register', [AuthController::class, 'registerStore'])->name('register.store');
            Route::get('register/{token}/{email}', [AuthController::class, 'registerVerify'])->name('register.verify');

            Route::get('forget-password', [AuthController::class, 'forgetPassword'])->name('password.forget');
            Route::post('forget-password', [AuthController::class, 'forgetPasswordStore'])->name('password.forget.email');
            Route::get('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
            Route::post('reset-password', [AuthController::class, 'resetPasswordStore'])->name('password.update');
        });

        Route::middleware('auth')->group(function () {

            Route::get('logout', [AuthController::class, 'logout'])->name('logout');

            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');


            /**
             * Connect
             */
            Route::get('/connect', [ConnectController::class, 'index'])->name('connect');
            Route::get('/connect/facebook', [ConnectController::class, 'facebook'])->name('connect.facebook');
            Route::get('/connect/facebook/callback', [ConnectController::class, 'facebookCallback'])->name('connect.facebook.callback');
            Route::get('/connect/facebook/disconnect', [ConnectController::class, 'facebookDisconnect'])->name('connect.facebook.disconnect');
            Route::get('/connect/linkedin', [ConnectController::class, 'linkedin'])->name('connect.linkedin');
            Route::get('/connect/linkedin/callback', [ConnectController::class, 'linkedinCallback'])->name('connect.linkedin.callback');
            Route::get('/connect/linkedin/disconnect', [ConnectController::class, 'linkedinDisconnect'])->name('connect.linkedin.disconnect');


            /**
             * Post
             */
            Route::prefix('/post')
                ->controller(PostController::class)
                ->name('post.')
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/details/{id}', 'show')->name('show');
                    Route::put('/update/{id}', 'update')->name('update');

                    Route::get('/scheduled', 'scheduled')->name('scheduled');
                    Route::get('/scheduled/response', 'scheduledPosts')->name('scheduled.all');

                    Route::get('/draft', 'draft')->name('draft');
                    Route::post('/draft', 'draftStore')->name('draft.store');
                    Route::post('/draft/new', 'draftNewStore')->name('draft.new.store');

                    Route::get('/create', 'create')->name('create');
                    Route::post('/', 'store')->name('store');
                });

            /**
             * Facebook
             */
            Route::prefix('/facebook')
                ->name('facebook.')
                ->group(function () {


                    // Route::get('/post/image/create', [FacebookPostController::class, 'imageCreate'])->name('post.image.create');
                    // Route::post('/post/image/store', [FacebookPostController::class, 'imageStore'])->name('post.image.store');

                    // Route::get('/post/text/create', [FacebookPostController::class, 'textCreate'])->name('post.text.create');
                    // Route::post('/post/text/store', [FacebookPostController::class, 'textStore'])->name('post.text.store');

                    // Route::get('/post/video/create', [FacebookPostController::class, 'videoCreate'])->name('post.video.create');
                    // Route::post('/post/video/store', [FacebookPostController::class, 'videoStore'])->name('post.video.store');
                });

            /**
             * LinkedIn
             */
            Route::prefix('/linkedin')
                ->name('linkedin.')
                ->group(function () {
                    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');

                    // Route::get('/post/text/create', [LinkedInPostController::class, 'textCreate'])->name('post.text.create');
                    // Route::post('/post/text/store', [LinkedInPostController::class, 'textStore'])->name('post.text.store');

                    // Route::get('/post/video/create', [LinkedInPostController::class, 'videoCreate'])->name('post.video.create');
                    // Route::post('/post/video/store', [LinkedInPostController::class, 'videoStore'])->name('post.video.store');
                });
        });
    });





Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin.dashboard.index');
        })->name('dashboard');
    });
