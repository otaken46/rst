<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\ViewerMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function dupe_id_check($id){
        Log::debug('testda');
        Log::debug($id);
        $sql_result = FacilityManagerMst::where('facility_manager_id', $id)->where('delete_date', NULL)->count();
        if($sql_result == 0){
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
}
