<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register-token',[ApiController::class,'registerToken']);    //Register Token
Route::get('get-city',[ApiController::class,'getCity']);  //Get Cities
Route::get('get-category',[ApiController::class,'getCategory']);   //Get Category
Route::get('get-slider',[ApiController::class,'getSlider']);   // Get Slider
Route::get('get-radio-station',[ApiController::class,'getRadioStation']);    //Get Radio Station
Route::get('get-settings',[ApiController::class,'getSettings']);   // Get Settings
Route::post('report-radio-station',[ApiController::class,'reportRadioStation']);   // Submit Radio Station Reports
