<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ViewerMst;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\FacilityMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    public function index (Request $request) 
    {
        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL){
            $facility = FacilityManagerMst::select('facility_mst.id','facility_mst.facility_id','facility_mst.facility_name')
            ->leftjoin('facility_mst','facility_mst.id','=','facility_manager_mst.facility_id')
            ->where('facility_manager_id', $request->session()->get('id'))->get();
            $patient = PatientMst::where('facility_id',$facility[0]['id'])->where('delete_date', NULL)->get();
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
        return view('patient', compact('patient','facility','statuscount'));
    }
    public function regist (Request $request) 
    {
        if($request['patient_name'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                $message = "";
                if($request['regist_type'] == "new"){
                    $log_id = $this::operation_log($request->session()->get('id'),"RST011");
                    $message = config('const.btn.regist');
                    $dupe = $this::dupe_id_check($request['patient_id']);
                    if($dupe){
                        $patient_mst = new PatientMst();
                        $sql_result = $patient_mst->insert([
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
                        $this::operation_result($log_id,"success");
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                    }else{
                        $this::operation_result($log_id,"fail dupe id");
                        $sql_result = 1;
                        $res = ['result'=>'NG','message'=>config('const.label.patient_id') . config('const.result.DUPE_ID')];
                    }
                }
                if($request['regist_type'] == "update"){
                    $log_id = $this::operation_log($request->session()->get('id'),"RST012");
                    $message = config('const.btn.update');
                    $patient_mst = new PatientMst();
                    $sql_result = $patient_mst
                    ->where('id', $request['target_id'])
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
                    $this::operation_result($log_id,"success");
                    $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                }
                if($request['regist_type'] == "delete"){
                    $log_id = $this::operation_log($request->session()->get('id'),"RST013");
                    $message = config('const.btn.delete');
                    $patient_mst = new PatientMst();
                    $sql_result = $patient_mst
                    ->where('id', $request['target_id'])
                    ->update([
                        'delete_date' => now(),
                    ]);
                    $this::operation_result($log_id,"success");
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
