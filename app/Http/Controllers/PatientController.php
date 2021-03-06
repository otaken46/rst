<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    public function index (Request $request) 
    {
        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL && $request->session()->get('user') == "manager"){
            $facility = FacilityManagerMst::select('facility_mst.id','facility_mst.facility_id','facility_mst.facility_name')
            ->leftjoin('facility_mst','facility_mst.id','=','facility_manager_mst.facility_id')
            ->where('facility_manager_id', $request->session()->get('id'))
            ->where('password', $request->session()->get('pass'))->get();
            $patient = PatientMst::where('facility_id',$facility[0]['id'])->where('delete_date', NULL)->get();
            $patient_count = PatientMst::where('facility_id',$facility[0]['id'])->where('delete_date', NULL)->count();
            $statuscount['regist_status'] = 0;
            $statuscount['setting_status'] = 0;
            $statuscount['monitor_status'] = 0;
            $statuscount['treatment_status'] = 0;
            $regist_status = 0;
            foreach($patient as $val){
                $statuscount['regist_status'] = $regist_status = $regist_status + 1;
                if($val['setting_status'] == 1){$statuscount['setting_status'] =  $statuscount['setting_status'] + 1;}
                if($val['monitor_status'] == 1){$statuscount['monitor_status'] =  $statuscount['monitor_status'] + 1;}
                if($val['treatment_status'] == 1){$statuscount['treatment_status'] =  $statuscount['treatment_status'] + 1;}
            }
        }else{
            $errors = '';
            $id = '';
            $pass = '';
            return view('login_facility', compact('id','pass','errors'));
        }
        return view('patient', compact('patient','facility','statuscount','patient_count'));
    }
    public function regist (Request $request) 
    {
        if($request['patient_name'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                $message = "";
                if($request['regist_type'] == "new"){
                    if($request->session()->get('id') != NULL){
                        $log_id = $this::operation_log($request->session()->get('id'),"RST011");
                    }else{
                        $log_id = "";
                    }
                    $message = config('const.btn.regist');
                    $dupe = $this::dupe_id_check($request['patient_id']);
                    $pass = $this::ng_password_check($request['password']);
                    if($dupe && $pass){
                        $sql_result = PatientMst::insert([
                            'facility_id' => $request['facility_id'],
                            'patient_name' => $request['patient_name'],
                            'patient_id' => $request['patient_id'],
                            'password' => $request['password'],
                            'setting_status' => $request['setting_status'],
                            'monitor_status' => $request['monitor_status'],
                            'treatment_status' => $request['treatment_status'],
                            'doctor' => $request['doctor'],
                            'create_date' => now(),
                        ]);
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.SUCCESS'));
                        }
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                    }else{
                        $sql_result = 1;
                        if($dupe){
                            if($log_id != ""){
                                $this::operation_result($log_id,config('const.operation.NG_PASS'));
                            }
                            $res = ['result'=>'NG','message'=>config('const.result.NG＿PASS')];
                        }else{
                            if($log_id != ""){
                                $this::operation_result($log_id,config('const.operation.DUPE_ID'));
                            }
                            $res = ['result'=>'NG','message'=>config('const.label.patient_id') . config('const.result.DUPE_ID')];
                        }
                    }
                }
                if($request['regist_type'] == "update"){
                    if($request->session()->get('id') != NULL){
                        $log_id = $this::operation_log($request->session()->get('id'),"RST012");
                    }else{
                        $log_id = "";
                    }
                    $pass = $this::ng_password_check($request['password']);
                    if($pass){
                        $target_record = PatientMst::where('id', $request['target_id'])->get();
                        if($request['update_date'] != "no_update"){
                            $update_date = substr_replace($request['update_date'], ' ', 10, 0);
                        }else{
                            $update_date = null;
                        }
                        //排他制御
                        if($target_record[0]['update_date'] == $update_date){
                            $message = config('const.btn.update');
                            $sql_result = PatientMst::where('id', $request['target_id'])
                            ->update([
                                'facility_id' => $request['facility_id'],
                                'patient_name' => $request['patient_name'],
                                'password' => $request['password'],
                                'setting_status' => $request['setting_status'],
                                'monitor_status' => $request['monitor_status'],
                                'treatment_status' => $request['treatment_status'],
                                'doctor' => $request['doctor'],
                                'update_date' => now(),
                            ]);
                            if($log_id != ""){
                                $this::operation_result($log_id,config('const.operation.SUCCESS'));
                            }
                            $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                        }else{
                            // 変更されている場合
                            $message = config('const.result.used_others');
                            if($log_id != ""){
                                $this::operation_result($log_id,config('const.operation.EXCLUSIVE'));
                            }
                            $sql_result = 1;
                            $res = ['result'=>'OK','message'=>$message];
                        }
                    }else{
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.NG_PASS'));
                        }
                        $sql_result = 1;
                        $res = ['result'=>'NG','message'=>config('const.result.NG＿PASS')];
                    }
                }
                if($request['regist_type'] == "delete"){
                    if($request->session()->get('id') != NULL){
                        $log_id = $this::operation_log($request->session()->get('id'),"RST013");
                    }else{
                        $log_id = "";
                    }
                    $target_record = PatientMst::where('id', $request['target_id'])->get();
                    if($request['update_date'] != "no_update"){
                        $update_date = substr_replace($request['update_date'], ' ', 10, 0);
                    }else{
                        $update_date = null;
                    }
                    //排他制御
                    if($target_record[0]['update_date'] == $update_date){
                        $message = config('const.btn.delete');
                        $sql_result = PatientMst::where('id', $request['target_id'])
                        ->update([
                            'update_date' => now(),
                            'delete_date' => now(),
                        ]);
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.SUCCESS'));
                        }
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                    }else{
                        // 変更されている場合
                        $message = config('const.result.used_others');
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.EXCLUSIVE'));
                        }
                        $sql_result = 1;
                        $res = ['result'=>'OK','message'=>$message];
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $res = ['result'=>'NG','message'=>$message . config('const.result.NG')];
                $result = json_encode($res);
                return $result;
            }
            if($sql_result != 0){
                $result = json_encode($res);
                return $result;
            }else{
                $res = ['result'=>'NG','message'=>config('const.result.DB_NG')];
                $result = json_encode($res);
                return $result;
            }
        }else{
            $res = ['result'=>'NG','message'=>config('const.result.NAME_NG')];
            $result = json_encode($res);
            return $result;
        }
    }
}
