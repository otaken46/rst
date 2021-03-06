<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\ViewerMst;
use App\Http\Models\SettingMst;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function facility_index () 
    {
        $errors = '';
        $id = '';
        $pass = '';
        return view('login_facility', compact('id','pass','errors'));
    }
    public function viewer_index () 
    {
        $errors = '';
        $id = '';
        $pass = '';
        return view('login_viewer', compact('id','pass','errors'));
    }
    public function user_check (Request $request)
    {
        Log::debug('message1111');
        $errors = '';
        $sql_result = 0;
        $sql = 0;
        $id = $request->id;
        $pass = $request->pass;
        $setting = SettingMst::get();
        // システム管理者
        if($id == $setting[0]['admin_id'] && $pass == $setting[0]['admin_pass']){
            $request->session()->put('id', $request->id);
            $request->session()->put('pass', $request->pass);
            $request->session()->put('user', "admin");
            $log_id = $this::operation_log($id,"RST001",config('const.operation.SUCCESS'));
            return redirect('/facility');
        }
        $facility_mng_mst = new FacilityManagerMst();
        $sql_result = $facility_mng_mst->select('id','fail_count','account_rock')->where('facility_manager_id', $id)->where('delete_date', NULL)->get();
        //　施設管理者
        if(isset($sql_result[0]['id']) && $id != $setting[0]['admin_id']){
            $sql = FacilityManagerMst::where('facility_manager_id', 'like binary', $id)->where('password', 'like binary', $pass)->where('delete_date', NULL)->get();
            //　ユーザーidとパスワードが正しいかつアカウントロックされていない
            if(isset($sql[0]['id']) && $sql_result[0]['account_rock'] == 0){
                $request->session()->put('id', $request->id);
                $request->session()->put('pass', $request->pass);
                $request->session()->put('user', "manager");
                $sql_result = $facility_mng_mst
                    ->where('facility_manager_id', $id)
                    ->update([
                        'fail_count' => 0,
                    ]);
                $log_id = $this::operation_log($id,"RST007",config('const.operation.SUCCESS'));
                return redirect('/viewer');
            }else{
                //　ユーザーidとパスワードが正しくない
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
                    $log_id = $this::operation_log($id,"RST007",config('const.operation.FAIL'));
                    $errors = config('const.msg.err_001');
                    return view('login_facility', compact('id','pass','errors'));
                }else{
                    //　アカウントロック状態
                    $log_id = $this::operation_log($id,"RST007","account rock");
                    $errors = config('const.msg.err_006');
                    return view('login_facility', compact('id','pass','errors'));
                }
            }
        }else{
            if($id != "" && $id == $setting[0]['admin_id']){
                $log_id = $this::operation_log($id,"RST001",config('const.operation.FAIL'));
            }else{
                if($id != ""){
                    $log_id = $this::operation_log($id,"RST007","not find id");
                }
            }
            $errors = config('const.msg.err_001');
            return view('login_facility', compact('id','pass','errors'));
        }
    }
    public function viewer_check (Request $request) 
    {
        $errors = '';
        $sql_result = 0;
        $sql = 0;
        $id = $request->inputID;
        $pass = $request->inputPass;
        $viewer_mst = new ViewerMst();
        $sql_result = $viewer_mst->where('viewer_id', $id)->where('account_rock', 0)->get();
        //　閲覧者
        if(isset($sql_result[0]['id'])){
            $sql = $viewer_mst->where('viewer_id', 'like binary', $id)->where('password', 'like binary', $pass)->get();
            if(isset($sql[0]['id'])){
                $request->session()->put('id', $id);
                $request->session()->put('pass', $pass);
                $request->session()->put('user', "viewer");
                $sql_result = $viewer_mst
                ->where('viewer_id', $id)
                ->update([
                    'fail_count' => 0,
                ]);
                $log_id = $this::operation_log($id,"RST014",config('const.operation.SUCCESS'));
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
                $log_id = $this::operation_log($id,"RST014",config('const.operation.FAIL'));
                $errors = "pass";
                return view('login_viewer', compact('id','pass','errors'));
            }
        }else{
            $log_id = $this::operation_log($id,"RST014","not find id");
            $errors = "id";
            return view('login_viewer', compact('id','pass','errors'));
        }
    }
    public function logout (Request $request) 
    {
        if($request->session()->get('id') != NULL){
            $log_id = $this::operation_log($request->session()->get('id'),"RST018",config('const.operation.SUCCESS'));
        }
        session()->flush();
        return redirect('login_facility');
    }
    public function logout_viewer (Request $request) 
    {
        if($request->session()->get('id') != NULL){
            $log_id = $this::operation_log($request->session()->get('id'),"RST018",config('const.operation.SUCCESS'));
        }
        session()->flush();
        return redirect('login_viewer');
    }
}
