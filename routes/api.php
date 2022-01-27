<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JwtController;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\CategoryController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::group(['middleware'=>'api'], function ($router) {
//     Route::post('logout',[JwtController::class,'logout']);
//     Route::post('refresh',[JwtController::class,'refresh']);
//     Route::post('profile',[JwtController::class,'profile']);
//     Route::post('login',[JwtController::class,'login']);
//     Route::post('register',[JwtController::class,'register']);
   
//     });

    Route::group(['middleware'=>['jwt']], function ($router) {
        Route::get('getuser',[JwtController::class,'get_user']);
        Route::post('logout',[JwtController::class,'logout']);
        Route::post('refresh',[JwtController::class,'refresh']);
        Route::get('profile',[JwtController::class,'profile']);
        Route::post('editprofile',[JwtController::class,'UpdateProfile']);
        Route::post('changePassword',[JwtController::class,'changePassword']);
   
    });
    Route::get('services',[JwtController::class,'CMSDetails']);

    Route::post('checkout',[JwtController::class,'checkout']);
    Route::post('login',[JwtController::class,'login']);
    Route::post('register',[JwtController::class,'register']);
    Route::get('banner',[JwtController::class,'banner']);
    Route::post('banner',[JwtController::class,'banner']);

    Route::post('contactus',[JwtController::class,'contactus']);
    // Route::get('categories',[CategoryController::class,'categories']);
    Route::get('category',[JwtController::class,'category']);
    Route::get('category/{id}',[JwtController::class,'show']);
    Route::get('product',[JwtController::class,'product']);
    Route::get('productt',[JwtController::class,'productt']);
    Route::get('myorders/{id}',[JwtController::class,'MyOrder']);
    Route::get('coupons',[JwtController::class,'Coupons']);
