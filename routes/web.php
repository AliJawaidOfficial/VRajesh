<?php

use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| User Routes Import
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ConnectController;
use App\Http\Controllers\User\PostController;

use App\Http\Controllers\User\LinkedIn\PipelineController;

/*
|--------------------------------------------------------------------------
| Admin Routes Import
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\User\DraftPostController;
use App\Http\Controllers\User\IndivisualPostController;
use App\Http\Controllers\User\LinkedIn\SalesNavigatorController;
use App\Http\Controllers\User\PixelsController;
use App\Http\Controllers\User\ScheduledPostController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

// Jobs
Route::get('/scheduled/post/job', [ScheduledPostController::class, 'job'])->name('post.index');

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

            /**
             * Logout
             */
            Route::get('logout', [AuthController::class, 'logout'])->name('logout');

            /**
             * Dashboard
             */
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            /**
             * Connect
             */
            Route::controller(ConnectController::class)
                ->prefix('/connect')
                ->name('connect')
                ->group(function () {
                    Route::get('/', 'index');

                    // Facebook
                    Route::get('/facebook', 'facebook')->name('.facebook');
                    Route::get('/facebook/callback', 'facebookCallback')->name('.facebook.callback');
                    Route::get('/facebook/disconnect', 'facebookDisconnect')->name('.facebook.disconnect');

                    // Linkedin
                    Route::get('/linkedin', 'linkedin')->name('.linkedin');
                    Route::get('/linkedin/callback', 'linkedinCallback')->name('.linkedin.callback');
                    Route::get('/linkedin/callback/2', 'linkedinCallback2')->name('.linkedin.callback.2');
                    Route::get('/linkedin/disconnect', 'linkedinDisconnect')->name('.linkedin.disconnect');
                });


            /**
             * User Accounts
             */
            Route::get('/facebook-pages', [PostController::class, 'facebookPages'])->name('facebook.pages');
            Route::get('instagram-accounts', [PostController::class, 'instagramAccounts'])->name('instagram.accounts');
            Route::get('linkedin-accounts', [PostController::class, 'linkedinOrganizations'])->name('linkedin.organizations');


            /**
             * Post
             */
            Route::prefix('/post')
                ->controller(PostController::class)
                ->name('post.')
                ->group(function () {

                    Route::get('/', 'index')->name('index');
                    Route::get('/details/{id}', 'show')->name('show');

                    Route::get('/create', 'create')->name('create');
                    Route::post('/create', 'store')->name('store');
                    Route::post('/create/new', 'newStore')->name('new.store');
                    Route::post('/{id}/delete', 'destroy')->name('destroy');

                    /**
                     * Draft Post
                     */
                    Route::prefix('/draft')
                        ->controller(DraftPostController::class)
                        ->name('draft')
                        ->group(function () {
                            Route::get('/', 'index');
                            Route::post('/', 'store')->name('.store');
                            Route::post('/new', 'storeAsDraft')->name('.store.new');
                            Route::post('/update', 'update')->name('.update');
                        });

                    /**
                     * Scheduled Post
                     */
                    Route::prefix('/scheduled')
                        ->controller(ScheduledPostController::class)
                        ->name('scheduled')
                        ->group(function () {
                            Route::get('/', 'index');
                            Route::get('/response', 'all')->name('.all');
                        });
                });


            /**
             * Indivisual Post
             */
            Route::prefix('/linkedin-self')
                ->controller(IndivisualPostController::class)
                ->name('individual.post.')
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::get('/details/{id}', 'show')->name('show');
                    Route::get('/create', 'create')->name('create');
                    Route::post('/create', 'store')->name('store');
                    Route::post('/{id}/delete', 'destroy')->name('destroy');
                });


            /**
             * LinkedIn
             */
            Route::prefix('/linkedin')
                ->name('linkedin.')
                ->group(function () {

                    Route::prefix('/leads')
                        ->name('leads.')
                        ->group(function () {

                            Route::prefix('/sales-navigator')
                                ->name('sales-navigator.')
                                ->controller(SalesNavigatorController::class)
                                ->group(function () {
                                    Route::get('/', 'index')->name('index');
                                    Route::get('/all', 'all')->name('all');
                                });
                        });

                    Route::get('/pipeline', [PipelineController::class, 'index'])->name('pipeline.index');
                });

            /**
             * Pixels
             */
            Route::prefix('/pixels')
                ->name('pixels.')
                ->controller(PixelsController::class)
                ->group(function () {
                    Route::get('/{type}/{q}', 'search')->name('search');
                });
        });
    });





Route::prefix('/admin')
    ->name('admin.')
    ->group(function () {

        Route::controller(AdminAuthController::class)
            ->middleware('admin.guest')
            ->group(function () {
                Route::get('login', 'login')->name('login');
                Route::post('login', 'loginStore');

                Route::get('forget-password', 'forgetPassword')->name('password.forget');
                Route::post('forget-password', 'forgetPasswordStore')->name('password.forget.email');
                Route::get('reset-password/{token}', 'resetPassword')->name('password.reset');
                Route::post('reset-password', 'resetPasswordStore')->name('password.update');
            });


        Route::middleware('admin')
            ->group(function () {

                /**
                 * Logout
                 */
                Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');

                /**
                 * Dashboard
                 */
                Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

                /**
                 * Users
                 */
                Route::controller(AdminUserController::class)
                    ->prefix('/users')
                    ->name('user.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/create', 'create')->name('create');
                        Route::post('/create', 'store')->name('store');
                        Route::get('/{id}/edit', 'edit')->name('edit');
                        Route::post('/{id}/edit', 'update')->name('update');
                        Route::delete('/{id}/delete', 'destroy')->name('destroy');
                        Route::post('/{id}/login', 'login')->name('login');
                    });

                /**
                 * Packages
                 */
                Route::controller(AdminPackageController::class)
                    ->prefix('/packages')
                    ->name('package.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/create', 'create')->name('create');
                        Route::post('/create', 'store')->name('store');
                        Route::get('/{id}/edit', 'edit')->name('edit');
                        Route::post('/{id}/edit', 'update')->name('update');
                        Route::delete('/{id}/delete', 'destroy')->name('destroy');
                        Route::post('/{id}/visibility', 'visibility')->name('visibility');
                    });
            });
    });
