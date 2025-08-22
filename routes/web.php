<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PartnersController;

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

Auth::routes();
Route::group(['middleware' => "auth"], function () {

    // Admin routes
    Route::group(['prefix' => 'cpadmin', 'middleware' => 'admin'], function () {
        Route::resource('partners', PartnersController::class);
        Route::post('partner/filter-partner-show-stats/{id}', [PartnersController::class, 'filterStats'])->name('filter.partner.stats');
        Route::get('partner/stats', [PartnersController::class, 'stats'])->name('partners.stats');
        Route::post('partner/add-user/{id}', [PartnersController::class, 'addUser'])->name('partners.add-user');
    });
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/download-data', [App\Http\Controllers\HomeController::class, 'downloadData'])->name('home.download-data');
    Route::get('/update-data', [App\Http\Controllers\HomeController::class, 'updateData'])->name('home.update-data');

    // User management
    Route::resource('users', App\Http\Controllers\Blade\UserController::class);
    Route::resource('roles', App\Http\Controllers\Blade\RolesController::class);
    Route::get('permissions', [App\Http\Controllers\Blade\PermissionsController::class, 'index'])->name('permissions.index');
});
