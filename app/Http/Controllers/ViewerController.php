<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ViewerMst;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\FacilityMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class ViewerController extends Controller
{
    public function index (Request $request) 
    {
        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL){
            $facility = FacilityManagerMst::select('facility_mst.id','facility_mst.facility_id','facility_mst.facility_name')
            ->leftjoin('facility_mst','facility_mst.id','=','facility_manager_mst.facility_id')
            ->where('facility_manager_id', $request->session()->get('id'))->get();
            $viewer = ViewerMst::where('delete_date', NULL)->where('facility_id',$facility[0]['id'])->get();
        }else{
            $errors = '';
            $id = '';
            $pass = '';
            return view('login_facility', compact('id','pass','errors'));
        }
        return view('viewer', compact('viewer','facility'));
    }
    public function regist (Request $request) 
    {
        if($request['viewer_name'] != ""){
            DB::beginTransaction();
            try {
                $sql_result = 0;
                $message = "";
                if($request['regist_type'] == "new"){
                    if($request->session()->get('id') != NULL){
                        $log_id = $this::operation_log($request->session()->get('id'),"RST008");
                    }else{
                        $log_id = "";
                    }
                    $message = config('const.btn.regist');
                    $dupe = $this::dupe_id_check($request['viewer_id']);
                    $pass = $this::ng_password_check($request['password']);
                    if($dupe && $pass){
                        $viewer_mst = new ViewerMst();
                        $sql_result = $viewer_mst->insert([
                            'facility_id' => $request['facility_id'],
                            'viewer_name' => $request['viewer_name'],
                            'viewer_id' => $request['viewer_id'],
                            'mail_address' => $request['mail_address'],
                            'password' => $request['password'],
                            'create_date' => now(),
                        ]);
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.SUCCESS'));
                        }
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
                    }else{
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.DUPE_ID'));
                        }
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
                            $res = ['result'=>'NG','message'=>config('const.label.viewer_id') . config('const.result.DUPE_ID')];
                        }
                    }
                }
                if($request['regist_type'] == "update"){
                    if($request->session()->get('id') != NULL){
                        $log_id = $this::operation_log($request->session()->get('id'),"RST009");
                    }else{
                        $log_id = "";
                    }
                    $pass = $this::ng_password_check($request['password']);
                    if($pass){
                        $message = config('const.btn.update');
                        $viewer_mst = new ViewerMst();
                        $sql_result = $viewer_mst
                        ->where('id', $request['target_id'])
                        ->update([
                            'facility_id' => $request['facility_id'],
                            'viewer_name' => $request['viewer_name'],
                            'password' => $request['password'],
                            'mail_address' => $request['mail_address'],
                            'update_date' => now(),
                        ]);
                        if($log_id != ""){
                            $this::operation_result($log_id,config('const.operation.SUCCESS'));
                        }
                        $res = ['result'=>'OK','message'=>$message . config('const.result.OK')];
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
                        $log_id = $this::operation_log($request->session()->get('id'),"RST010");
                    }else{
                        $log_id = "";
                    }
                    $message = config('const.btn.delete');
                    $viewer_mst = new ViewerMst();
                    $sql_result = $viewer_mst
                    ->where('id', $request['target_id'])
                    ->update([
                        'delete_date' => now(),
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
