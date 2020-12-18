<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ViewerMst;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class ViewerController extends Controller
{
    public function index (Request $request) 
    {
        $user_id = $request->session()->get('id');
        Log::debug('hoge');
        Log::debug($user_id);
        $facility = ViewerMst::get();
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
        return view('facility', compact('facility'));
    }
    public function regist (Request $request) 
    {
        if($request['facility_name'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                if($request['regist_type'] == "new"){
                    Log::debug("1111");
                    $facility_mst = new FacilityMst();
                    $sql_result = $facility_mst->insert([
                        'facility_name' => $request['facility_name'],
                        'facility_id' => $request['facility_id'],
                        'create_date' => now(),
                    ]);
                    $res = ['result'=>'OK'];
                }
                if($request['regist_type'] == "update"){
                    Log::debug("2222");
                    $facility_mst = new FacilityMst();
                    $sql_result = $facility_mst
                    ->where('id', $request['target_id'])
                    ->update([
                        'facility_name' => $request['facility_name'],
                        'facility_id' => $request['facility_id'],
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
