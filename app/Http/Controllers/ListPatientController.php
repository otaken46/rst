<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ViewerMst;
use App\Http\Models\FacilityManagerMst;
use App\Http\Models\FacilityMst;
use App\Http\Models\PatientMst;
use Illuminate\Support\Facades\Log;

class ListPatientController extends Controller
{
    public function index (Request $request) 
    {
        $user = NULL;
        $list_patient = array();

        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL){
            Log::debug("hoge");
            $viewer_mst = new ViewerMst();
            $viewer_mst_data = $viewer_mst->leftjoin('facility_mst','facility_mst.id','=','viewer_mst.facility_id')
            ->where('viewer_id',$request->session()->get('id'))->where('password', $request->session()->get('pass'))->get();
            if(isset($viewer_mst_data[0]['id'])){
                $facility_name = $viewer_mst_data[0]['facility_name'];
                $patient_mst_data = DB::select('SELECT DISTINCT
                    db_rst.patient_mst.patient_name
                    , db_rst.patient_mst.patient_id
                    , tbl3.tbl3_id
                    , tbl3.tbl3_patient_id
                    , tbl3.tbl3_doc_date
                    , tbl3.tbl3_mean_respr
                    , tbl3.tbl3_mean_cvr
                    , tbl3.tbl3_mean_rsi
                    , tbl3.tbl3_max_xhr2
                    , tbl3.tbl3_mean_hr
                    , tbl3.tbl3_total_taido_pc
                    , tbl3.tbl3_note
                FROM
                    db_rst.patient_mst 
                    LEFT JOIN ( 
                        SELECT
                            tbl1.id as tbl3_id
                            , tbl1.patient_id as tbl3_patient_id
                            , tbl1.doc_date as tbl3_doc_date
                            , tbl1.mean_respr as tbl3_mean_respr
                            , tbl1.mean_cvr as tbl3_mean_cvr
                            , tbl1.mean_rsi as tbl3_mean_rsi
                            , tbl1.max_xhr2 as tbl3_max_xhr2
                            , tbl1.mean_hr as tbl3_mean_hr
                            , tbl1.total_taido_pc as tbl3_total_taido_pc
                            , tbl1.note as tbl3_note
                        FROM
                            db_rst.final_output7 AS tbl1 
                            LEFT JOIN db_rst.final_output7 AS tbl2 
                                ON ( 
                                    tbl1.patient_id = tbl2.patient_id 
                                    AND tbl1.doc_date < tbl2.doc_date
                                ) 
                        WHERE
                            tbl2.id IS NULL
                    ) AS tbl3 
                        ON tbl3.tbl3_patient_id = db_rst.patient_mst.patient_id 
                WHERE
                    db_rst.patient_mst.facility_id = ? 
                    AND tbl3.tbl3_doc_date IS NOT NULL', [$viewer_mst_data[0]['id']]);
                $cnt = 0;
                $date ="";
                Log::debug("kkkk");
                $array = json_decode(json_encode($patient_mst_data), true);
                //Log::debug($array);
                
                foreach($array as $val){
                    $list_patient[$cnt]['患者ID'] = $val['patient_id'];
                    $list_patient[$cnt]['患者名'] = $val['patient_name'];
                    $list_patient[$cnt]['最終更新'] = date('yy/m/d',  strtotime($val['tbl3_doc_date']));
                    $list_patient[$cnt]['RST'] = sprintf('%.1F', floatval($val['tbl3_mean_respr']));
                    $list_patient[$cnt]['心拍数'] = sprintf('%.2F', floatval($val['tbl3_mean_cvr']));
                    $list_patient[$cnt]['呼吸数'] = sprintf('%.1F', floatval($val['tbl3_max_xhr2']));
                    $list_patient[$cnt]['CSRグレード'] = sprintf('%.1F', floatval($val['tbl3_total_taido_pc']));
                    $list_patient[$cnt]['臥床時間'] = sprintf('%.1F', floatval($val['tbl3_note']));
                    $cnt++;
                }
            }else{
                $errors = '';
                $id = '';
                $pass = '';
                return view('login_viewer', compact('id','pass','errors'));
            }
        }else{
            $errors = '';
            $id = '';
            $pass = '';
            return view('login_viewer', compact('id','pass','errors'));
        }
        $request->session()->put('facility_name', $facility_name);
        return view('list_patient', compact('facility_name','list_patient'));
    }
}
