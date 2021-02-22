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
            $chart_data =  FinalOutput::where('patient_id', 'like binary', $patient_id)->get();
            $chart_patient_data = array();
            $old_date = "";
            $new_date = "";
            $create_old_date = "";
            $create_new_date = "";
            foreach($chart_data as $val){
                $date = date('Y-m-d',  strtotime($val['doc_date']));
                $create_date = date('Y-m-d',  strtotime($val['create_date']));
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
                $chart_patient_data[$date]['mean_rsi'] = sprintf('%d', floatval($val['mean_rsi']));
                $chart_patient_data[$date]['mean_hr'] = sprintf('%d', floatval($val['mean_hr']));
                $chart_patient_data[$date]['mean_respr'] = sprintf('%d', floatval($val['mean_respr']));
                $chart_patient_data[$date]['mean_csr'] = sprintf('%.2F', floatval($val['mean_csr']));
                $chart_patient_data[$date]['time_in_bed'] = sprintf('%.1F', floatval($val['time_in_bed']));
                $chart_patient_data[$date]['note'] = $val['note'];
                if($create_old_date == ""){
                    $create_old_date = $create_date;
                    $create_new_date = $val['create_date'];
                }else{
                    if($create_old_date > $create_date){
                        $create_old_date = $create_date;
                    }
                }
                if($val['create_date'] > $create_new_date){
                    $create_new_date = $val['create_date'];
                }
            }
            if($create_new_date != ""){
                $create_new_date = date('Y/m/d h:i',  strtotime($create_new_date));
            }
            $setting = SettingMst::get();
            $memo_list = explode(",",$setting[0]['memo_list']);
            $chart_patient = $this->chartDataSet($chart_patient_data, "today");

            return view('chart_patient', compact('patient_id','old_date','new_date','chart_patient','memo_list','create_new_date'));
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
    public static function chartDataSet($chart_data, $date){
        $result_data = [];
        if($date == "today"){
            $today = date('Y-m-d H:i:s');
        }else{
            $today = $date . " 00:00:00";
        }
        for($cnt = 90; $cnt > 0 ;$cnt--){
            if($cnt == 90){
                $gensan = $cnt - 1;
                $gensan_day = "-" . $gensan . " day";
                $target_day = strtotime($gensan_day . $today);
            }else{
                $gensan = $gensan - 1;
                $gensan_day = "-" . $gensan . " day";
                $target_day = strtotime($gensan_day . $today);
            }
            if(!empty($chart_data[date("Y-m-d", $target_day)])){
                $result_data['data_rst_90'][] = $chart_data[date("Y-m-d", $target_day)]['mean_rsi'];
                $result_data['data_heart_90'][] = $chart_data[date("Y-m-d", $target_day)]['mean_hr'];
                $result_data['data_breath_90'][] = $chart_data[date("Y-m-d", $target_day)]['mean_respr'];
                $result_data['data_csr_90'][] = $chart_data[date("Y-m-d", $target_day)]['mean_csr'];
                $result_data['data_sleep_90'][] = $chart_data[date("Y-m-d", $target_day)]['time_in_bed'];
                $result_data['data_memo_90'][] = $chart_data[date("Y-m-d", $target_day)]['note'];
            }else{
                $result_data['data_rst_90'][] = null;
                $result_data['data_heart_90'][] = null;
                $result_data['data_breath_90'][] = null;
                $result_data['data_csr_90'][] = null;
                $result_data['data_sleep_90'][] = null;
                $result_data['data_memo_90'][] = null;
            }
            if($cnt < 61){
                if(!empty($chart_data[date("Y-m-d", $target_day)])){
                    $result_data['data_rst_60'][] = $chart_data[date("Y-m-d", $target_day)]['mean_rsi'];
                    $result_data['data_heart_60'][] = $chart_data[date("Y-m-d", $target_day)]['mean_hr'];
                    $result_data['data_breath_60'][] = $chart_data[date("Y-m-d", $target_day)]['mean_respr'];
                    $result_data['data_csr_60'][] = $chart_data[date("Y-m-d", $target_day)]['mean_csr'];
                    $result_data['data_sleep_60'][] = $chart_data[date("Y-m-d", $target_day)]['time_in_bed'];
                    $result_data['data_memo_60'][] = $chart_data[date("Y-m-d", $target_day)]['note'];
                }else{
                    $result_data['data_rst_60'][] = null;
                    $result_data['data_heart_60'][] = null;
                    $result_data['data_breath_60'][] = null;
                    $result_data['data_csr_60'][] = null;
                    $result_data['data_sleep_60'][] = null;
                    $result_data['data_memo_60'][] = null;
                }
            }
            if($cnt < 31){
                if(!empty($chart_data[date("Y-m-d", $target_day)])){
                    $result_data['data_rst_30'][] = $chart_data[date("Y-m-d", $target_day)]['mean_rsi'];
                    $result_data['data_heart_30'][] = $chart_data[date("Y-m-d", $target_day)]['mean_hr'];
                    $result_data['data_breath_30'][] = $chart_data[date("Y-m-d", $target_day)]['mean_respr'];
                    $result_data['data_csr_30'][] = $chart_data[date("Y-m-d", $target_day)]['mean_csr'];
                    $result_data['data_sleep_30'][] = $chart_data[date("Y-m-d", $target_day)]['time_in_bed'];
                    $result_data['data_memo_30'][] = $chart_data[date("Y-m-d", $target_day)]['note'];
                }else{
                    $result_data['data_rst_30'][] = null;
                    $result_data['data_heart_30'][] = null;
                    $result_data['data_breath_30'][] = null;
                    $result_data['data_csr_30'][] = null;
                    $result_data['data_sleep_30'][] = null;
                    $result_data['data_memo_30'][] = null;
                }
            }
        }
        return $result_data;
    }
    public function chart_data (Request $request) 
    {
        $sql_result = 0;
        $message = "";
        
        $message = config('const.btn.delete');
        $chart_data =  FinalOutput::where('patient_id',$request['target_id'])->get();
        $chart_patient_data = array();
        $old_date = "";
        $new_date = "";
        foreach($chart_data as $val){
            $date = date('Y-m-d',  strtotime($val['doc_date']));
            if($old_date == ""){
                $old_date = $date;
                $new_date = $val['create_date'];
            }else{
                if($old_date > $date){
                    $old_date = $date;
                }
            }
            if($val['create_date'] > $new_date){
                $new_date = $val['create_date'];
            }
            $chart_patient_data[$date]['mean_rsi'] = sprintf('%d', floatval($val['mean_rsi']));
            $chart_patient_data[$date]['mean_hr'] = sprintf('%d', floatval($val['mean_hr']));
            $chart_patient_data[$date]['mean_respr'] = sprintf('%d', floatval($val['mean_respr']));
            $chart_patient_data[$date]['mean_csr'] = sprintf('%.2F', floatval($val['mean_csr']));
            $chart_patient_data[$date]['time_in_bed'] = sprintf('%.1F', floatval($val['time_in_bed']));
            $chart_patient_data[$date]['note'] = $val['note'];
        }

        $chart_patient = $this->chartDataSet($chart_patient_data, $request['doc_date']);
        $res = ['result'=>'OK','chart_data'=>$chart_patient];

        if($sql_result != 0){
            $result = json_encode($res);
            return $result;
        }else{
            $result = json_encode($res);
            return $result;
        }
    }
}
