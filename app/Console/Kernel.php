<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Manage;
use App\Http\Models\UploadData;
use App\Http\Models\DeviceInfo;
use App\Http\Models\FinalOutput;
use App\Http\Models\HrFinal;
use App\Http\Models\SePwmtxSeriesOne;
use App\Http\Models\SePwmtxSeriesTwo;
use App\Http\Models\SePwmtxSeriesThree;
use App\Http\Models\OperationLog;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $path = storage_path('app/public/upload_file/*.json');
            // 拡張子.jsonが付いたファイルを配列化し変数に格納
            $files = glob($path); 
            // 配列に値が入っているかチェック
            if (empty($files)) {
                Log::debug('ファイルが見つかりません！');
            } else {
                ini_set('memory_limit', '-1');
                foreach($files as $val){
                    $sql_result = 0;
                    $json = file_get_contents($val);
                    $data = json_decode($json, true);
                    try {
                        $log_id = $this::operation_log($data['Record']['Manage']['ID'],"RST017");
                        
                        $update_check = Manage::where('patient_id',$data['Record']['Manage']['ID'])->where('doc_date',$data['Record']['Manage']['DocDate'])->count();
                        if($update_check == 0){
                            $sql_result = Manage::insert([
                                'patient_id' => $data['Record']['Manage']['ID'],
                                'doc_date' => $data['Record']['Manage']['DocDate'],
                                'kind' => $data['Record']['Manage']['Kind'],
                                'unique_key' => $data['Record']['Manage']['UniqueKey'],
                                'upload_datetime' => $data['Record']['Manage']['UploadDatetime'],
                                'create_date' => now(),
                            ]);
                            $sql_result = UploadData::insert([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'analyze_timezone' => $data['Record']['Data']['AnalyzeTimezone'],
                                'analyze_datetime' => $data['Record']['Data']['AnalyzeDatetime'],
                                'first_sample_datetime' => $data['Record']['Data']['FirstSampleDatetime'],
                                'last_sample_datetime' => $data['Record']['Data']['LastSampleDatetime'],
                                'time_in_bed' => $data['Record']['Data']['TimeInBed'],
                                'input_file_num' => $data['Record']['Data']['InputFileNum'],
                                'input_sample_num' => $data['Record']['Data']['InputSampleNum'],
                                'create_date' => now(),
                            ]);
                            $sql_result = DeviceInfo::insert([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'sensor_id' => $data['Record']['Data']['DeviceInfo']['SensorID'],
                                'fw_version' => $data['Record']['Data']['DeviceInfo']['FwVersion'],
                                'app_id' => $data['Record']['Data']['DeviceInfo']['AppID'],
                                'app_version' => $data['Record']['Data']['DeviceInfo']['AppVersion'],
                                'connection_error_count' => $data['Record']['Data']['DeviceInfo']['ConnectionErrorCount'],
                                'sensor_error_count' => $data['Record']['Data']['DeviceInfo']['SensorErrorCount'],
                                'module_error_count' => $data['Record']['Data']['DeviceInfo']['ModuleErrorCount'],
                                'create_date' => now(),
                            ]);
                            $sql_result = FinalOutput::insert([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'mean_respr' => $data['Record']['Data']['FinalOutput7']['meanRespR'],
                                'mean_csr' => $data['Record']['Data']['FinalOutput7']['meanCSR'],
                                'mean_rsi' => $data['Record']['Data']['FinalOutput7']['meanRSI'],
                                'max_xhr2' => $data['Record']['Data']['FinalOutput7']['maxXhr2'],
                                'mean_hr' => $data['Record']['Data']['FinalOutput7']['meanHR'],
                                'total_taido_pc' => $data['Record']['Data']['FinalOutput7']['TotalTaidoPC'],
                                'exl_noise_pc' => $data['Record']['Data']['FinalOutput7']['ExlNoisePC'],
                                'note' => NULL,
                                'time_in_bed' => $data['Record']['Data']['TimeInBed'],
                                'create_date' => now(),
                                'update_date' => NULL,
                            ]);
                            $sql_result = HrFinal::insert([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'mean_hr' => $data['Record']['Data']['HRfinal']['meanHR'],
                                'hr_max_rsi' => $data['Record']['Data']['HRfinal']['HR_maxRSI'],
                                'xco_in_max_rsi' => $data['Record']['Data']['HRfinal']['XcolN_maxRSI'],
                                'xmin2_max_rsi' => $data['Record']['Data']['HRfinal']['Xmin2_maxRSI'],
                                'create_date' => now(),
                            ]);
                            $hf = "";
                            $resp_hz = "";
                            $resp_rate = "";
                            $resp_hzsd = "";
                            $var_index = "";
                            $regularity = "";
                            $respmx = "";
                            $hf = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['HF']);
                            $resp_hz = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RespHz']);
                            $resp_rate = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RespRate']);
                            $resp_hzsd = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RespHzSD']);
                            $var_index = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['VarIndex']);
                            $regularity = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Regularity']);
                            $respmx = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Respmx']);
                            $sql_result = SePwmtxSeriesOne::insert([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'hf' => $hf,
                                'resp_hz' => $resp_hz,
                                'resp_rate' => $resp_rate,
                                'resp_hzsd' => $resp_hzsd,
                                'var_index' => $var_index,
                                'regularity' => $regularity,
                                'respmx' => $respmx,
                                'create_date' => now(),
                            ]);
                            $max_n = "";
                            $csr = "";
                            $csr_max = "";
                            $csr_hz = "";
                            $csrpw = "";
                            $rsi = "";
                            $csr_max_respmax = "";
                            $max_n = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['maxN']);
                            $csr = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSR']);
                            $csr_max = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRmax']);
                            $csr_hz = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRHz']);
                            $csrpw = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRpw']);
                            $rsi = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RSI']);
                            $csr_max_respmax = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRmax/Respmax']);
                            $sql_result = SePwmtxSeriesTwo::insert([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'max_n' => $max_n,
                                'csr' => $csr,
                                'csr_max' => $csr_max,
                                'csr_hz' => $csr_hz,
                                'csrpw' => $csrpw,
                                'rsi' => $rsi,
                                'csr_max_respmax' => $csr_max_respmax,
                                'create_date' => now(),
                            ]);
                            $rsi1 = "";
                            $lng_pwsel2 = "";
                            $nz4sel2 = "";
                            $nz_lng2 = "";
                            $xn2 = "";
                            $xhr2 = "";
                            $xmin2 = "";
                            $rsi1 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RSI1']);
                            $lng_pwsel2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['LngPWSel2']);
                            $nz4sel2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Nz4Sel2']);
                            $nz_lng2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Nz_Lng2']);
                            $xn2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Xn2']);
                            $xhr2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Xhr2']);
                            $xmin2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Xmin2']);
                            $sql_result = SePwmtxSeriesThree::insert([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'rsi1' => $rsi1,
                                'lng_pwsel2' => $lng_pwsel2,
                                'nz4sel2' => $nz4sel2,
                                'nz_lng2' => $nz_lng2,
                                'xn2' => $xn2,
                                'xhr2' => $xhr2,
                                'xmin2' => $xmin2,
                                'create_date' => now(),
                            ]);
                        }else{
                            $sql_result = Manage::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Manage']['ID'],
                                'doc_date' => $data['Record']['Manage']['DocDate'],
                                'kind' => $data['Record']['Manage']['Kind'],
                                'unique_key' => $data['Record']['Manage']['UniqueKey'],
                                'upload_datetime' => $data['Record']['Manage']['UploadDatetime'],
                                'create_date' => now(),
                            ]);
                            $sql_result = UploadData::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'analyze_timezone' => $data['Record']['Data']['AnalyzeTimezone'],
                                'analyze_datetime' => $data['Record']['Data']['AnalyzeDatetime'],
                                'first_sample_datetime' => $data['Record']['Data']['FirstSampleDatetime'],
                                'last_sample_datetime' => $data['Record']['Data']['LastSampleDatetime'],
                                'time_in_bed' => $data['Record']['Data']['TimeInBed'],
                                'input_file_num' => $data['Record']['Data']['InputFileNum'],
                                'input_sample_num' => $data['Record']['Data']['InputSampleNum'],
                                'create_date' => now(),
                            ]);
                            $sql_result = DeviceInfo::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'sensor_id' => $data['Record']['Data']['DeviceInfo']['SensorID'],
                                'fw_version' => $data['Record']['Data']['DeviceInfo']['FwVersion'],
                                'app_id' => $data['Record']['Data']['DeviceInfo']['AppID'],
                                'app_version' => $data['Record']['Data']['DeviceInfo']['AppVersion'],
                                'connection_error_count' => $data['Record']['Data']['DeviceInfo']['ConnectionErrorCount'],
                                'sensor_error_count' => $data['Record']['Data']['DeviceInfo']['SensorErrorCount'],
                                'module_error_count' => $data['Record']['Data']['DeviceInfo']['ModuleErrorCount'],
                                'create_date' => now(),
                            ]);
                            $sql_result = FinalOutput::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'mean_respr' => $data['Record']['Data']['FinalOutput7']['meanRespR'],
                                'mean_csr' => $data['Record']['Data']['FinalOutput7']['meanCSR'],
                                'mean_rsi' => $data['Record']['Data']['FinalOutput7']['meanRSI'],
                                'max_xhr2' => $data['Record']['Data']['FinalOutput7']['maxXhr2'],
                                'mean_hr' => $data['Record']['Data']['FinalOutput7']['meanHR'],
                                'total_taido_pc' => $data['Record']['Data']['FinalOutput7']['TotalTaidoPC'],
                                'exl_noise_pc' => $data['Record']['Data']['FinalOutput7']['ExlNoisePC'],
                                'note' => NULL,
                                'time_in_bed' => $data['Record']['Data']['TimeInBed'],
                                'create_date' => now(),
                                'update_date' => NULL,
                            ]);
                            $hr_final = new HrFinal();
                            $sql_result = HrFinal::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'mean_hr' => $data['Record']['Data']['HRfinal']['meanHR'],
                                'hr_max_rsi' => $data['Record']['Data']['HRfinal']['HR_maxRSI'],
                                'xco_in_max_rsi' => $data['Record']['Data']['HRfinal']['XcolN_maxRSI'],
                                'xmin2_max_rsi' => $data['Record']['Data']['HRfinal']['Xmin2_maxRSI'],
                                'create_date' => now(),
                            ]);
                            $hf = "";
                            $resp_hz = "";
                            $resp_rate = "";
                            $resp_hzsd = "";
                            $var_index = "";
                            $regularity = "";
                            $respmx = "";
                            $hf = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['HF']);
                            $resp_hz = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RespHz']);
                            $resp_rate = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RespRate']);
                            $resp_hzsd = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RespHzSD']);
                            $var_index = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['VarIndex']);
                            $regularity = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Regularity']);
                            $respmx = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Respmx']);
                            $sql_result = SePwmtxSeriesOne::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'hf' => $hf,
                                'resp_hz' => $resp_hz,
                                'resp_rate' => $resp_rate,
                                'resp_hzsd' => $resp_hzsd,
                                'var_index' => $var_index,
                                'regularity' => $regularity,
                                'respmx' => $respmx,
                                'create_date' => now(),
                            ]);
                            $max_n = "";
                            $csr = "";
                            $csr_max = "";
                            $csr_hz = "";
                            $csrpw = "";
                            $rsi = "";
                            $csr_max_respmax = "";
                            $max_n = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['maxN']);
                            $csr = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSR']);
                            $csr_max = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRmax']);
                            $csr_hz = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRHz']);
                            $csrpw = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRpw']);
                            $rsi = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RSI']);
                            $csr_max_respmax = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['CSRmax/Respmax']);
                            $sql_result = SePwmtxSeriesTwo::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'max_n' => $max_n,
                                'csr' => $csr,
                                'csr_max' => $csr_max,
                                'csr_hz' => $csr_hz,
                                'csrpw' => $csrpw,
                                'rsi' => $rsi,
                                'csr_max_respmax' => $csr_max_respmax,
                                'create_date' => now(),
                            ]);
                            $rsi1 = "";
                            $lng_pwsel2 = "";
                            $nz4sel2 = "";
                            $nz_lng2 = "";
                            $xn2 = "";
                            $xhr2 = "";
                            $xmin2 = "";
                            $rsi1 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['RSI1']);
                            $lng_pwsel2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['LngPWSel2']);
                            $nz4sel2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Nz4Sel2']);
                            $nz_lng2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Nz_Lng2']);
                            $xn2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Xn2']);
                            $xhr2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Xhr2']);
                            $xmin2 = $this->data_set($data['Record']['Data']['SelPWmtxSeries']['Xmin2']);
                            $sql_result = SePwmtxSeriesThree::where('patient_id', $data['Record']['Manage']['ID'])
                            ->where('doc_date', $data['Record']['Manage']['DocDate'])
                            ->update([
                                'patient_id' => $data['Record']['Data']['ID'],
                                'doc_date' => $data['Record']['Data']['DocDate'],
                                'rsi1' => $rsi1,
                                'lng_pwsel2' => $lng_pwsel2,
                                'nz4sel2' => $nz4sel2,
                                'nz_lng2' => $nz_lng2,
                                'xn2' => $xn2,
                                'xhr2' => $xhr2,
                                'xmin2' => $xmin2,
                                'create_date' => now(),
                            ]);
                        }
                        $this::operation_result($log_id,"success");
                        File::delete($val);
                        unset($data);
                    } catch (\Exception $e) {
                        report($e);
                        File::delete($val);
                        unset($data);
                    }
                }
                ini_set('memory_limit', '256M');
            }
        })->dailyAt('06:00');
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
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    public function data_set($arr){
        $result = "";
        $str = "";
        foreach($arr as $key=>$value){
            $str .= $value . ",";
        }
        $result = rtrim($str, ',');
        return $result;
    }
}
