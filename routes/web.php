<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('login_facility','LoginController@facility_index');
Route::post('login_facility','LoginController@user_check');
Route::get('facility','FacilityController@index');
Route::post('facility','FacilityController@regist');
Route::get('facility_mng','FacilityMngController@index');
Route::post('facility_mng','FacilityMngController@regist');
Route::get('viewer','ViewerController@index');