<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FacilityMst;
use App\Http\Models\FacilityManagerMst;
use Illuminate\Support\Facades\Log;

class FacilityMngController extends Controller
{
    public function index () 
    {
        $facility = FacilityMst::get();
        $facility_mng = FacilityManagerMst::get();

        return view('facility_mng', compact('facility_mng','facility'));
    }
    public function regist (Request $request) 
    {
        Log::debug($request);
        if($request['regist_type'] != ""){
            Log::debug($request);
            DB::beginTransaction();
            try {
                $sql_result = 0;
                if($request['regist_type'] == "new"){
                    $facility_mng_mst = new FacilityManagerMst();
                    $sql_result = $facility_mng_mst->insert([
                        'facility_id' => $request['facility_id'],
                        'facility_manager_name' => $request['facility_manager_name'],
                        'facility_manager_id' => $request['facility_manager_id'],
                        'password' => $request['password'],
                        'contact' => $request['contact'],
                        'mail_address' => $request['mail_address'],
                        'create_date' => now(),
                    ]);
                    $res = ['result'=>'OK'];
                }
                if($request['regist_type'] == "update"){
                    $facility_mng_mst = new FacilityManagerMst();
                    Log::debug("hogehoge2");
                    $sql_result = $facility_mng_mst
                    ->where('id', $request['target_id'])
                    ->where('facility_id', $request['facility_id'])
                    ->update([
                        'facility_manager_name' => $request['facility_manager_name'],
                        'facility_manager_id' => $request['facility_manager_id'],
                        'password' => $request['password'],
                        'contact' => $request['contact'],
                        'mail_address' => $request['mail_address'],
                        'update_date' => now(),
                    ]);
                    $res = ['result'=>'OK'];
                }
                if($request['regist_type'] == "delete"){
                    $facility_mng_mst = new FacilityManagerMst();
                    Log::debug("hogehoge3");
                    $sql_result = $facility_mng_mst
                    ->where('id', $request['target_id'])
                    ->save([
                        'delete_date' => now(),
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
