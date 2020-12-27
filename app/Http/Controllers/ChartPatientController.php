<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FinalOutput;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class ChartPatientController extends Controller
{
    public function index (Request $request) 
    {
        Log::debug("hoge");
        
        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL && $request->input('patient_id') != NULL){
            $patient_id = $request->input('patient_id');
            $final_output = new FinalOutput();
            $chart_data =  FinalOutput::where('patient_id',$patient_id)->get();
            $chart_patient = array();
            $old_date = "";
            
            foreach($chart_data as $val){
                $date = date('Y-m-d',  strtotime($val['doc_date']));
                if($old_date == ""){
                    $old_date = $date;
                }else{
                    if($old_date > $date){
                        $old_date = $date;
                    }
                }
                $chart_patient[$date]['mean_respr'] = round($val['mean_respr'],0);
                $chart_patient[$date]['mean_cvr'] = round($val['mean_cvr'],0);
                $chart_patient[$date]['mean_rsi'] = round($val['mean_rsi'],0);
                $chart_patient[$date]['max_xhr2'] = round($val['max_xhr2'],1);
                $chart_patient[$date]['mean_hr'] = round($val['mean_hr'],1);
                $chart_patient[$date]['total_taido_pc'] = round($val['total_taido_pc'],1);
                $chart_patient[$date]['exl_noise_pc'] = round($val['exl_noise_pc'],1);
                $chart_patient[$date]['note'] = $val['note'];
            }
            Log::debug("222");
            Log::debug($old_date);
            return view('chart_patient', compact('patient_id','old_date','chart_patient'));
        }else{
            $errors = '';
            $id = '';
            $pass = '';
            return view('login_viewer', compact('id','pass','errors'));
        }
    }
    public function regist (Request $request) 
    {
        if($request['patient_name'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                if($request['regist_type'] == "new"){
                    Log::debug("1111");
                    Log::debug($request);
                    $patient_mst = new PatientMst();
                    $sql_result = $patient_mst->insert([
                        'facility_id' => $request['facility_id'],
                        'patient_name' => $request['patient_name'],
                        'patient_id' => $request['patient_id'],
                        'password' => $request['password'],
                        'regist_status' => $request['regist_status'],
                        'setting_status' => $request['setting_status'],
                        'monitor_status' => $request['monitor_status'],
                        'treatment_status' => $request['treatment_status'],
                        'doctor' => $request['doctor'],
                        'create_date' => now(),
                    ]);
                    $res = ['result'=>'OK'];
                }
                if($request['regist_type'] == "update"){
                    Log::debug("2222");
                    $patient_mst = new PatientMst();
                    $sql_result = $patient_mst
                    ->where('id', $request['target_id'])
                    ->update([
                        'facility_id' => $request['facility_id'],
                        'patient_name' => $request['patient_name'],
                        'patient_id' => $request['patient_id'],
                        'password' => $request['password'],
                        'regist_status' => $request['regist_status'],
                        'setting_status' => $request['setting_status'],
                        'monitor_status' => $request['monitor_status'],
                        'treatment_status' => $request['treatment_status'],
                        'doctor' => $request['doctor'],
                        'update_date' => now(),
                    ]);
                    $res = ['result'=>'OK'];
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $res = ['result'=>'NG'];
                $result = json_encode($res);
                return $result;
            }
            if($sql_result != 0){
                $result = json_encode($res);
                return $result;
            }else{
                $res = ['result'=>'NG'];
                $result = json_encode($res);
                return $result;
            }
        }else{

        }
    }
}
