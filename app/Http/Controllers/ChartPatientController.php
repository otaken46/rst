<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ViewerMst;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\FacilityMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class ChartPatientController extends Controller
{
    public function index (Request $request) 
    {
        $chart_patient = "";
        return view('chart_patient', compact('chart_patient'));
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
