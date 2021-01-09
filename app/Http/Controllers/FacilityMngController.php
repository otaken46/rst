<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\FacilityMst;
use App\Http\Models\FacilityManagerMst;
use Illuminate\Support\Facades\Log;

class FacilityMngController extends Controller
{
    public function index (Request $request) 
    {
        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL){
            $default_facility_id = "";
            if($request->input('facility_id') != NULL){
                $default_facility_id = $request->input('facility_id');
            }
            $facility = FacilityMst::where('delete_date', NULL)->orderBy('create_date', 'asc')->get();
            $facility_mng = FacilityManagerMst::where('delete_date', NULL)->orderBy('create_date', 'asc')->get();
        }else{
            $errors = '';
            $id = '';
            $pass = '';
            return view('login_facility', compact('id','pass','errors'));
        }
        return view('facility_mng', compact('facility_mng', 'facility', 'default_facility_id'));
    }
    public function regist (Request $request) 
    {
        if($request['regist_type'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                $message = "";
                if($request['regist_type'] == "new"){
                    $log_id = $this::operation_log($request->session()->get('id'),"RST004");
                    $message = config('const.btn.regist');
                    $dupe = $this::dupe_id_check($request['facility_manager_id']);
                    if($dupe){
                        $sql_result = FacilityManagerMst::insert([
                            'facility_id' => $request['facility_id'],
                            'facility_manager_name' => $request['facility_manager_name'],
                            'facility_manager_id' => $request['facility_manager_id'],
                            'password' => $request['password'],
                            'contact' => $request['contact'],
                            'mail_address' => $request['mail_address'],
                            'create_date' => now(),
                        ]);
                        $this::operation_result($log_id,"success");
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                    }else{
                        $this::operation_result($log_id,"fail dupe id");
                        $sql_result = 1;
                        $res = ['result'=>'NG','message'=>config('const.label.facility_manager_id') . config('const.result.DUPE_ID')];                        
                    }
                }
                if($request['regist_type'] == "update"){
                    $log_id = $this::operation_log($request->session()->get('id'),"RST005");
                    $message = config('const.btn.update');
                    $facility_mng_mst = new FacilityManagerMst();
                    $sql_result = $facility_mng_mst
                    ->where('id', $request['target_id'])
                    ->where('facility_id', $request['facility_id'])
                    ->update([
                        'facility_manager_name' => $request['facility_manager_name'],
                        'password' => $request['password'],
                        'contact' => $request['contact'],
                        'mail_address' => $request['mail_address'],
                        'update_date' => now(),
                    ]);
                    $this::operation_result($log_id,"success");
                    $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                }
                if($request['regist_type'] == "delete"){
                    $log_id = $this::operation_log($request->session()->get('id'),"RST006");
                    $message = config('const.btn.delete');
                    $facility_mng_mst = new FacilityManagerMst();
                    $sql_result = $facility_mng_mst
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
