<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\ViewerMst;
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
    public function viewer_index () 
    {
        Log::debug('デバッグメッセージ1');
        $errors = '';
        $id = '';
        $pass = '';
        return view('login_viewer', compact('id','pass','errors'));
    }
    public function user_check (Request $request) 
    {
        Log::debug('デバッグメッセージ2');
        $errors = '';
        $sql_result = 0;
        $id = $request->id;
        $pass = $request->pass;
        if($id == config('const.admin_id')){
            $request->session()->put('id', $request->id);
        }
        if($pass == config('const.admin_pass')){
            $request->session()->put('id', $request->id);
            $request->session()->put('pass', $request->pass);
            return redirect('/facility');
        }
        $facility_mng_mst = new FacilityManagerMst();
        $sql_result = $facility_mng_mst->select('id')->where('facility_manager_id', $id)->get();
        if(isset($sql_result[0]['id'])){
            $sql_result = 0;
            $sql_result = FacilityManagerMst::where('facility_manager_id', $id)->where('password', $pass)->get();
            if(isset($sql_result[0]['id'])){
                $request->session()->put('id', $request->id);
                $request->session()->put('pass', $request->pass);
                return redirect('/viewer');
            }else{
                $errors = config('const.msg.err_001');
                return view('login_facility', compact('id','pass','errors'));
            }
        }else{
            $errors = config('const.msg.err_001');
            return view('login_facility', compact('id','pass','errors'));
        }
    }
    public function viewer_check (Request $request) 
    {
        Log::debug('デバッグメッセージ3');
        $errors = '';
        $sql_result = 0;
        $id = $request->inputID;
        $pass = $request->inputPass;
        Log::debug($request);
        $viewer_mst = new ViewerMst();
        $sql_result = $viewer_mst->where('viewer_id', $id)->get();

        if(isset($sql_result[0]['id'])){
            Log::debug('111');
            $sql_result = 0;
            $sql_result = $viewer_mst->where('viewer_id', $id)->where('password', $pass)->get();
            if(isset($sql_result[0]['id'])){
                Log::debug('444');
                $request->session()->put('id', $id);
                $request->session()->put('pass', $pass);
                return redirect('/list_patient');
            }else{
                $errors = "pass";
                Log::debug($errors);
                return view('login_viewer', compact('id','pass','errors'));
            }
        }else{
            Log::debug('222');
            $errors = "id";
            Log::debug($errors);
            return view('login_viewer', compact('id','pass','errors'));
        }
    }
    public function logout () 
    {
        session()->flush();
        return redirect('login_facility');
    }
    public function logout_viewer () 
    {
        session()->flush();
        return redirect('login_viewer');
    }
}
