<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\ViewerMst;
use App\Http\Models\PatientMst;
use App\Http\Models\OperationLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function dupe_id_check($id){
        $sql_result = FacilityManagerMst::where('facility_manager_id', $id)->where('delete_date', NULL)->count();
        if($sql_result == 0 && $id != config('const.admin_id')){
            $sql_result = ViewerMst::where('viewer_id', $id)->where('delete_date', NULL)->count();
            if($sql_result == 0){
                $sql_result = PatientMst::where('patient_id', $id)->where('delete_date', NULL)->count();
                if($sql_result == 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public static function operation_log($userid, $operation_code, $result = NULL){
        $operation_log = new OperationLog();
        $operation_log->insert([
            'user_id' => $userid,
            'operation_code' => $operation_code,
            'result' => $result,
            'operation_date' => now(),
        ]);
        $id = DB::getPdo()->lastInsertId();
        return $id;
    }
    public static function operation_result($log_id, $result){
        $operation_log = new OperationLog();
        $operation_log->where('id', $log_id)
        ->update([
            'result' => $result,
        ]);
    }
}
