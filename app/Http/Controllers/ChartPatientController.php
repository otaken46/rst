<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FinalOutput;
use App\Http\Models\SettingMst;
use Illuminate\Support\Facades\Log;

class ChartPatientController extends Controller
{
    public function index (Request $request) 
    {
        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL && $request->input('patient_id') != NULL  && $request->session()->get('user') == "viewer"){
            $patient_id = $request->input('patient_id');
            $final_output = new FinalOutput();
            $chart_data =  FinalOutput::where('patient_id',$patient_id)->get();
            $chart_patient = array();
            $old_date = "";
            $new_date = "";
            foreach($chart_data as $val){
                $date = date('Y-m-d',  strtotime($val['doc_date']));
                if($old_date == ""){
                    $old_date = $date;
                    $new_date = $val['doc_date'];
                }else{
                    if($old_date > $date){
                        $old_date = $date;
                    }
                }
                if($val['doc_date'] > $new_date){
                    $new_date = $val['doc_date'];
                }
                $chart_patient[$date]['mean_rsi'] = sprintf('%d', floatval($val['mean_rsi']));
                $chart_patient[$date]['mean_hr'] = sprintf('%d', floatval($val['mean_hr']));
                $chart_patient[$date]['mean_respr'] = sprintf('%d', floatval($val['mean_respr']));
                $chart_patient[$date]['mean_cvr'] = sprintf('%.2F', floatval($val['mean_cvr']));
                $chart_patient[$date]['time_in_bed'] = sprintf('%.1F', floatval($val['time_in_bed']));
                $chart_patient[$date]['note'] = $val['note'];
            }
            $new_date = date('Y/m/d h:i',  strtotime($new_date));
            $setting = SettingMst::get();
            $memo_list = explode(",",$setting[0]['memo_list']);
            return view('chart_patient', compact('patient_id','old_date','new_date','chart_patient','memo_list'));
        }else{
            $errors = '';
            $id = '';
            $pass = '';
            return view('login_viewer', compact('id','pass','errors'));
        }
    }
    public function regist (Request $request) 
    {
        if($request['type'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                $message = "";
                if($request['type'] == "update"){
                    if($request->session()->get('id') != NULL){
                        $log_id = $this::operation_log($request->session()->get('id'),"RST015");
                    }else{
                        $log_id = "";
                    }
                    $message = config('const.btn.regist');
                    $final_output = new FinalOutput();
                    $sql_result = $final_output
                    ->where('patient_id', $request['target_id'])
                    ->where('doc_date', $request['doc_date'])
                    ->update([
                        'note' => $request['note'],
                        'update_date' => now(),
                    ]);
                    if($sql_result != 0){
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.SUCCESS'));
                        }
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                    }else{
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.FAIL'));
                        }
                        $res = ['result'=>'NG','message'=>config('const.result.NOT_REGIST')];
                    }
                }
                if($request['type'] == "delete"){
                    if($request->session()->get('id') != NULL){
                        $log_id = $this::operation_log($request->session()->get('id'),"RST019");
                    }else{
                        $log_id = "";
                    }
                    $message = config('const.btn.delete');
                    $final_output = new FinalOutput();
                    $sql_result = $final_output
                    ->where('patient_id', $request['target_id'])
                    ->where('doc_date', $request['doc_date'])
                    ->update([
                        'note' => NULL,
                        'update_date' => now(),
                    ]);
                    if($log_id != ""){
                        $this::operation_result($log_id,config('const.operation.SUCCESS'));
                    }
                    $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
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
