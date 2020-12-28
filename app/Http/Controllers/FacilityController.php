<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FacilityMst;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\ViewerMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class FacilityController extends Controller
{
    public function index (Request $request) 
    {
        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL){
            $facility = FacilityMst::where('delete_date', NULL)->get();
            $facility_count = FacilityMst::where('delete_date', NULL)->count();
            if(isset($facility[0]->id)){
                $data = $facility;
                $cnt = 0;
                foreach($data as $val){
                    $facility_id = FacilityManagerMst::where('facility_id', $val->id)->get()->count();
                    $facility[$cnt]['mng_count'] = $facility_id;
                    $facility[$cnt]['regist_status'] = 0;
                    $facility[$cnt]['setting_status'] = 0;
                    $facility[$cnt]['monitor_status'] = 0;
                    $facility[$cnt]['treatment_status'] = 0;
                    if($facility_id != 0){
                        $patient_mst = PatientMst::where('facility_id', $val->id)->get();
                        foreach($patient_mst as $value){
                            if($value->regist_status == 1)$facility[$cnt]['regist_status'] += 1;
                            if($value->setting_status == 1)$facility[$cnt]['setting_status'] += 1;
                            if($value->monitor_status == 1)$facility[$cnt]['monitor_status'] += 1;
                            if($value->treatment_status == 1)$facility[$cnt]['treatment_status'] += 1;
                        }
                    }
                    $cnt++;
                }
            }
        }else{
            $errors = '';
            $id = '';
            $pass = '';
            return view('login_facility', compact('id','pass','errors'));
        }
        return view('facility', compact('facility','facility_count'));
    }
    public function regist (Request $request) 
    {
        Log::debug($request);
        if($request['facility_name'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                $message = "";
                if($request['regist_type'] == "new"){
                    Log::debug("1111");
                    $message = config('const.btn.regist');
                    $facility_mst = new FacilityMst();
                    $sql_result = $facility_mst->where('facility_id', $request['facility_id'])->where('delete_date', NULL)->count();
                    if($sql_result == 0){
                        $sql_result = $facility_mst->insert([
                            'facility_name' => $request['facility_name'],
                            'facility_id' => $request['facility_id'],
                            'create_date' => now(),
                        ]);
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                    }else{
                        $res = ['result'=>'NG','message'=>config('const.label.facility_id') . config('const.result.DUPE_ID')];
                    }
                }
                if($request['regist_type'] == "delete"){
                    Log::debug("333");
                    $message = config('const.btn.delete');
                    $facility_mst = new FacilityMst();
                    $sql_result = $facility_mst
                    ->where('id', $request['target_id'])
                    ->update([
                        'delete_date' => now(),
                    ]);
                    $facility_count = FacilityManagerMst::where('facility_id', $request['target_id'])->count();
                    if($facility_count != 0){
                        $sql_result = FacilityManagerMst::
                        where('facility_id', $request['target_id'])
                        ->update([
                            'delete_date' => now(),
                        ]);
                    }
                    $facility_count = ViewerMst::where('facility_id', $request['target_id'])->count();
                    if($facility_count != 0){
                        $sql_result = ViewerMst::
                        where('facility_id', $request['target_id'])
                        ->update([
                            'delete_date' => now(),
                        ]);
                    }
                    $facility_count = PatientMst::where('facility_id', $request['target_id'])->count();
                    if($facility_count != 0){
                        $sql_result = PatientMst::
                        where('facility_id', $request['target_id'])
                        ->update([
                            'delete_date' => now(),
                        ]);
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
