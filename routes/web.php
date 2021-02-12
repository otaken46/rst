<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
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
Log::debug('デバッグメッセージ2999');
Route::get('/', function () {
    return view('not_access');
});
Route::get('login_facility','LoginController@facility_index');
Route::post('login_facility','LoginController@user_check');
Route::get('facility','FacilityController@index');
Route::post('facility','FacilityController@regist');
Route::get('facility_mng','FacilityMngController@index');
Route::post('facility_mng','FacilityMngController@regist');
Route::get('viewer','ViewerController@index');
Route::post('viewer','ViewerController@regist');
Route::get('patient','PatientController@index');
Route::post('patient','PatientController@regist');
Route::get('logout','LoginController@logout');
Route::get('logout_viewer','LoginController@logout_viewer');
Route::get('login_viewer','LoginController@viewer_index');
Route::post('login_viewer','LoginController@viewer_check');
Route::get('list_patient','ListPatientController@index');
Route::post('list_patient','ListPatientController@regist');
Route::get('chart_patient','ChartPatientController@index');
Route::post('chart_patient','ChartPatientController@regist');
Route::post('chart_data','ChartPatientController@chart_data');