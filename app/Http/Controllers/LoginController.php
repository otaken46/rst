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
        $sql = 0;
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
        $sql_result = $facility_mng_mst->select('id','fail_count','account_rock')->where('facility_manager_id', $id)->get();
        if(isset($sql_result[0]['id'])){
            $sql = FacilityManagerMst::where('facility_manager_id', $id)->where('password', $pass)->get();
            if(isset($sql[0]['id']) && $sql_result[0]['account_rock'] == 0){
                $request->session()->put('id', $request->id);
                $request->session()->put('pass', $request->pass);
                $sql_result = $facility_mng_mst
                    ->where('facility_manager_id', $id)
                    ->update([
                        'fail_count' => 0,
                    ]);
                return redirect('/viewer');
            }else{
                $fail_count = $sql_result[0]['fail_count'] + 1;
                $account_rock = $sql_result[0]['account_rock'];
                if($account_rock == 0){
                    if($fail_count > 4){
                        $account_rock = 1;
                    }
                    $sql_result = $facility_mng_mst
                    ->where('facility_manager_id', $id)
                    ->update([
                        'fail_count' => $fail_count,
                        'account_rock' => $account_rock,
                    ]);
                    $errors = config('const.msg.err_001');
                    return view('login_facility', compact('id','pass','errors'));
                }else{
                    $errors = config('const.msg.err_006');
                    return view('login_facility', compact('id','pass','errors'));
                }
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
        $sql = 0;
        $id = $request->inputID;
        $pass = $request->inputPass;
        Log::debug($request);
        $viewer_mst = new ViewerMst();
        $sql_result = $viewer_mst->where('viewer_id', $id)->where('account_rock', 0)->get();

        if(isset($sql_result[0]['id'])){
            $sql = $viewer_mst->where('viewer_id', $id)->where('password', $pass)->get();
            if(isset($sql[0]['id'])){
                Log::debug('444');
                $request->session()->put('id', $id);
                $request->session()->put('pass', $pass);
                $sql_result = $viewer_mst
                ->where('viewer_id', $id)
                ->update([
                    'fail_count' => 0,
                ]);
                return redirect('/list_patient');
            }else{
                $fail_count = $sql_result[0]['fail_count'] + 1;
                $account_rock = $sql_result[0]['account_rock'];
                if($fail_count > 4){
                    $account_rock = 1;
                }
                $sql_result = $viewer_mst
                ->where('viewer_id', $id)
                ->update([
                    'fail_count' => $fail_count,
                    'account_rock' => $account_rock,
                ]);
                $errors = "pass";
                return view('login_viewer', compact('id','pass','errors'));
            }
        }else{
            $errors = "id";
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
