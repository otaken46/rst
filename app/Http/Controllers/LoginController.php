<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FacilityManagerMst;
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
            return redirect('/facility');
        }
        $facility_mng_mst = new FacilityManagerMst();
        $sql_result = $facility_mng_mst->select('id')->where('facility_manager_id', $id)->get();
        if(isset($sql_result[0]['id'])){
            $sql_result = 0;
            $sql_result = FacilityManagerMst::where('facility_manager_id', $id)->where('password', $pass)->get();
            if(isset($sql_result[0]['id'])){
                $request->session()->put('id', $request->id);
                return redirect('/viewer');
            }else{
                $errors ="err_002";
                return view('login_facility', compact('id','pass','errors'));
            }
        }else{
            $errors ="err_001";
            return view('login_facility', compact('id','pass','errors'));
        }
    }
    public function logout () 
    {
        session()->flush();
        return redirect('login_facility');
    }
}
