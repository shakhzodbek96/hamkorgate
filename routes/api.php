<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/switch-theme',function (Request $request){
    $user = \App\Models\User::find($request->user_id);
    return $user->switchTheme();
})->name('switchTheme');

Route::any('healthcheck',[\App\Http\Controllers\DevOps\HealthCheckController::class,'check']);

Route::post('login',[\App\Http\Controllers\Api\Login::class,'login']);

Route::group(['middleware' => ['authPartners','throttle:partners']],function (){
    Route::post('/{version}/partners',[\App\Http\Controllers\Api\MainController::class,'index']);
});

