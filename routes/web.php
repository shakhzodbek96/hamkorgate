<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Faker\DebitWebFakerPartnerController;
use App\Http\Controllers\Blade\TransferController;
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
        Route::get('toggle/auto', [PartnersController::class, 'toggleAuto'])->name('partners.toggleAuto');
        Route::get('toggle/status', [PartnersController::class, 'toggleStatus'])->name('partners.toggleStatus');
        Route::put('configuration/{id}/partner', [PartnersController::class, 'configurations'])->name('partners.configurations');

        Route::get('transfers/', [TransferController::class, 'index'])->name('transfers.index');
        Route::get('transfers/create', [TransferController::class, 'create'])->name('transfers.create');
        Route::get('transfers/{id}', [TransferController::class, 'show'])->name('transfers.show');
    });
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/download-data', [App\Http\Controllers\HomeController::class, 'downloadData'])->name('home.download-data');
    Route::get('/update-data', [App\Http\Controllers\HomeController::class, 'updateData'])->name('home.update-data');
    Route::post('partner/search', [PartnersController::class, 'search'])->name('partners.search');

    // User management
    Route::resource('users', App\Http\Controllers\Blade\UserController::class);
    Route::resource('roles', App\Http\Controllers\Blade\RolesController::class);
    Route::get('permissions', [App\Http\Controllers\Blade\PermissionsController::class, 'index'])->name('permissions.index');


});








// Payment form
Route::get ('/pay/{ext_id}',         [DebitWebFakerPartnerController::class, 'showForm'])->name('pay.form');
Route::post('/pay/{ext_id}',         [DebitWebFakerPartnerController::class, 'submitForm'])->name('pay.submit');

// 3DS mock
Route::get ('/3ds/{ext_id}',         [DebitWebFakerPartnerController::class, 'show3DS'])->name('pay.3ds');
Route::post('/3ds/{ext_id}',         [DebitWebFakerPartnerController::class, 'submit3DS'])->name('pay.3ds.submit');

// Success page
Route::get ('/pay/{ext_id}/success', [DebitWebFakerPartnerController::class, 'success'])->name('pay.success');
