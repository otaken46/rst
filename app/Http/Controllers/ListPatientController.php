<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ViewerMst;
use App\Http\Models\PatientMst;
use App\Http\Models\SettingMst;
use Illuminate\Support\Facades\Log;

class ListPatientController extends Controller
{
    public function index (Request $request) 
    {
        $user = NULL;
        $list_patient = array();

        if($request->session()->get('id') != NULL && $request->session()->get('pass') != NULL && $request->session()->get('user') == "viewer"){
            $viewer_mst = new ViewerMst();
            $viewer_mst_data = $viewer_mst->leftjoin('facility_mst','facility_mst.id','=','viewer_mst.facility_id')
            ->where('viewer_id',$request->session()->get('id'))->where('password', $request->session()->get('pass'))->get();
            if(isset($viewer_mst_data[0]['id'])){
                $facility_name = $viewer_mst_data[0]['facility_name'];
                $final_output = DB::select('SELECT
                tbl1.id as tbl3_id
                , tbl1.patient_id as tbl3_patient_id
                , tbl1.doc_date as tbl3_doc_date
                , tbl1.mean_respr as tbl3_mean_respr
                , tbl1.mean_csr as tbl3_mean_csr
                , tbl1.mean_rsi as tbl3_mean_rsi
                , tbl1.max_xhr2 as tbl3_max_xhr2
                , tbl1.mean_hr as tbl3_mean_hr
                , tbl1.total_taido_pc as tbl3_total_taido_pc
                , tbl1.note as tbl3_note
                , tbl1.time_in_bed as tbl3_time_in_bed
                ,(select max(tbl4.create_date) from db_rst.final_output7 as tbl4 where tbl4.patient_id = tbl1.patient_id) as maxcreate_date
            FROM
                db_rst.final_output7 AS tbl1 
                LEFT JOIN db_rst.final_output7 AS tbl2 
                    ON ( 
                        tbl1.patient_id = tbl2.patient_id 
                        AND tbl1.doc_date < tbl2.doc_date
                    ) 
            WHERE
                tbl2.id IS NULL');
                $patient_mst_data = PatientMst::where('facility_id', 'like binary',$viewer_mst_data[0]['id'])->where('delete_date', NULL)->get();
                $cnt = 0;
                $date ="";

                $array = json_decode(json_encode($patient_mst_data), true);
                $final_output_data = json_decode(json_encode($final_output), true);
                $flag_pink = array();
                $flag_yellow = array();
                foreach($array as $val){
                    $list_patient[$cnt]['患者ID'] = $val['patient_id'];
                    $list_patient[$cnt]['患者名'] = $val['patient_name'];
                    $list_patient[$cnt]['最終更新'] = "";
                    $list_patient[$cnt]['RST'] = "";
                    $list_patient[$cnt]['心拍数'] = "";
                    $list_patient[$cnt]['呼吸数'] = "";
                    $list_patient[$cnt]['CSRグレード'] = "";
                    $list_patient[$cnt]['臥床時間'] = "";
                    foreach($final_output_data as $value){
                        if($val['patient_id'] == $value['tbl3_patient_id']){
                            $list_patient[$cnt]['最終更新'] = date('Y/m/d',  strtotime($value['maxcreate_date']));
                            $list_patient[$cnt]['RST'] = sprintf('%d', floatval($value['tbl3_mean_rsi']));
                            $list_patient[$cnt]['心拍数'] = sprintf('%d', floatval($value['tbl3_mean_hr']));
                            $list_patient[$cnt]['呼吸数'] = sprintf('%d', floatval($value['tbl3_mean_respr']));
                            $list_patient[$cnt]['CSRグレード'] = sprintf('%.2F', floatval($value['tbl3_mean_csr']));
                            $list_patient[$cnt]['臥床時間'] = sprintf('%.1F', floatval($value['tbl3_time_in_bed']));
                        }
                    }
                    $cnt++;
                }
                //患者一覧の色付き
                $cnt = 0;
                $setting = SettingMst::get();
                foreach($list_patient as $key=>$val){
                    if($val['RST'] !="" && $val['RST'] < $setting[0]['flag_pink']){
                        array_push($flag_pink,$cnt);
                    }else{
                        if($val['RST'] !="" && $val['RST'] < $setting[0]['flag_yellow']){
                            array_push($flag_yellow,$cnt);
                        }
                    }
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
        return view('list_patient', compact('facility_name','list_patient','flag_pink','flag_yellow'));
    }
}
