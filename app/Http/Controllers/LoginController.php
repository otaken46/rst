<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function facility_index () 
    {
        Log::debug('デバッグメッセージ1');
        $errors = '';
        $id = '';
        $pass = '';
        return view('login_facility', compact('id','pass','errors'));
    }
    public function admin_user_check (Request $request) 
    {
        Log::debug('デバッグメッセージ2');
        $errors = '';
        $id = $request->id;
        $pass = $request->pass;
        if($id == config('const.admin_id')){
            $request->session()->put('id', $request->id);
        }else{
            $errors ="err_001";
            return view('login_facility', compact('id','pass','errors'));
        }
        if($pass == config('const.admin_pass')){
            return redirect('/facility');
        }else{
            $errors ="err_002";
            return view('login_facility', compact('id','pass','errors'));
        }
    }
}
