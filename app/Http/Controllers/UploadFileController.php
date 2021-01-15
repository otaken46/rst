<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ViewerMst;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\FacilityMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class UploadFileController extends Controller
{
    public function getfile(Request $request) {
Log::debug('message111');
Log::debug($request->header('id'));
Log::debug($request->header('pass'));
        $id = $request->header('id');
        $pass =  $request->header('pass');
        if($id != NULL && $pass != NULL){
            $patient_mst = new PatientMst();
            $sql_result = $patient_mst->where('patient_id',$id)->where('password',$pass)->where('delete_date', NULL)->count();
            if($sql_result == 1){
                $err = true;
                // ファイル情報取得
                $file = $request->file('file');
                $file_name = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $file_name_flont = str_replace("." . $extension,'',$file_name);

                if($extension != "json"){
                    $log_id = $this::operation_log($id,"RST016", "RST_003");
                    $err = false;
                    $result = "RST_003";
                }
                if($file_name_flont == "dummy" && ($err)){
                    $log_id = $this::operation_log($id,"RST016", "RST_004");
                    $err = false;
                    $result = "RST_004";
                }
                if($err){
                    $file_name = $id . "_" . $pass . ".json";
                    //ファイル保存
                    $request->file('file')->storeAs('public/upload_file',$file_name);
                    $log_id = $this::operation_log($id,"RST016", "RST_001");
                    $result = "RST_001";
                }
            }else{
                $log_id = $this::operation_log($id,"RST016", "RST_002");
                $result = "RST_002";
            }
        }else{
            $log_id = $this::operation_log($id,"RST016", "RST_002");
            $result = "RST_002";
        }
        return response()->json($result);
    }
}
