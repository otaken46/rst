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
                foreach($files as $val){
                    $sql_result = 0;
                    $json = file_get_contents($val);
                    $data = json_decode($json, true);
                    // DB::beginTransaction();
                    // try {
                        $manage = new Manage();
                        Log::debug("1111");
                        $sql_result = $manage->insert([
                            'patient_id' => $data['Record']['Manage']['ID'],
                            'doc_date' => $data['Record']['Manage']['DocDate'],
                            'kind' => $data['Record']['Manage']['Kind'],
                            'unique_key' => $data['Record']['Manage']['UniqueKey'],
                            'upload_datetime' => $data['Record']['Manage']['UploadDatetime'],
                            'create_date' => now(),
                        ]);
                        Log::debug("2222");
                        $upload_data = new UploadData();
                        $sql_result = $upload_data->insert([
                            'patient_id' => $data['Record']['Data']['ID'],
                            'doc_date' => $data['Record']['Data']['DocDate'],
                            'analyze_timezone' => $data['Record']['Data']['AnalyzeTimezone'],
                            'analyze_datetime' => $data['Record']['Data']['AnalyzeDatetime'],
                            'first_sample_datetime' => $data['Record']['Data']['FirstSampleDatetime'],
                            'last_sample_datetime' => $data['Record']['Data']['LastSampleDatetime'],
                            'timeIn_bed' => $data['Record']['Data']['TimeInBed'],
                            'input_file_num' => $data['Record']['Data']['InputFileNum'],
                            'input_sample_num' => $data['Record']['Data']['InputSampleNum'],
                            'create_date' => now(),
                        ]);
                        Log::debug("3333");
                        $deviceInfo = new DeviceInfo();
                        Log::debug($data['Record']['Data']['ID']);
                        Log::debug($data['Record']['Data']['DocDate']);
                        Log::debug($data['Record']['Data']['DeviceInfo']['SensorID']);
                        Log::debug($data['Record']['Data']['DeviceInfo']['FwVersion']);
                        Log::debug($data['Record']['Data']['DeviceInfo']['AppID']);
                        Log::debug($data['Record']['Data']['DeviceInfo']['AppVersion']);
                        Log::debug($data['Record']['Data']['DeviceInfo']['ConnectionErrorCount']);
                        Log::debug($data['Record']['Data']['DeviceInfo']['SensorErrorCount']);
                        Log::debug($data['Record']['Data']['DeviceInfo']['ModuleErrorCount']);
                        $sql_result = $deviceInfo->insert([
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
                        Log::debug("4444");
                        $final_output = new FinalOutput();
                        $sql_result = $final_output->insert([
                            'patient_id' => $data['Record']['Data']['ID'],
                            'doc_date' => $data['Record']['Data']['DocDate'],
                            'mean_respr' => $data['Record']['Data']['FinalOutput7']['meanRespR'],
                            'mean_cvr' => $data['Record']['Data']['FinalOutput7']['meanCVR'],
                            'mean_rsi' => $data['Record']['Data']['FinalOutput7']['meanRSI'],
                            'max_xhr2' => $data['Record']['Data']['FinalOutput7']['maxXhr2'],
                            'mean_hr' => $data['Record']['Data']['FinalOutput7']['meanHR'],
                            'total_taido_pc' => $data['Record']['Data']['FinalOutput7']['TotalTaidoPC'],
                            'exl_noise_pc' => $data['Record']['Data']['FinalOutput7']['ExlNoisePC'],
                            'note' => NULL,
                            'create_date' => now(),
                            'update_date' => NULL,
                        ]);
                        Log::debug("5555");
                        $hr_final = new HrFinal();
                        $sql_result = $hr_final->insert([
                            'patient_id' => $data['Record']['Data']['ID'],
                            'doc_date' => $data['Record']['Data']['DocDate'],
                            'mean_hr' => $data['Record']['Data']['HRfinal']['meanHR'],
                            'hr_max_rsi' => $data['Record']['Data']['HRfinal']['HR_maxRSI'],
                            'xco_in_max_rsi' => $data['Record']['Data']['HRfinal']['XcolN_maxRSI'],
                            'xmin2_max_rsi' => $data['Record']['Data']['HRfinal']['Xmin2_maxRSI'],
                            'create_date' => now(),
                        ]);
                        Log::debug("6666");
                        $sel_pwmtx_series = new SePwmtxSeriesOne();
                        $hf = "";
                        $resp_hz = "";
                        $resp_rate = "";
                        $resp_hzsd = "";
                        $var_index = "";
                        $regularity = "";
                        $respmx = "";
                        $hf = $this->data_set($data['Record']['Data']['selPWmtxSeries']['HF']);
                        $resp_hz = $this->data_set($data['Record']['Data']['selPWmtxSeries']['RespHz']);
                        $resp_rate = $this->data_set($data['Record']['Data']['selPWmtxSeries']['RespRate']);
                        $resp_hzsd = $this->data_set($data['Record']['Data']['selPWmtxSeries']['RespHzSD']);
                        $var_index = $this->data_set($data['Record']['Data']['selPWmtxSeries']['VarIndex']);
                        $regularity = $this->data_set($data['Record']['Data']['selPWmtxSeries']['Regularity']);
                        $respmx = $this->data_set($data['Record']['Data']['selPWmtxSeries']['Respmx']);
                        $sql_result = $sel_pwmtx_series->insert([
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
                        Log::debug("7777");
                        $sel_pwmtx_series = new SePwmtxSeriesTwo();
                        $max_n = "";
                        $csr = "";
                        $csr_max = "";
                        $csr_hz = "";
                        $csr_pq = "";
                        $rsi = "";
                        $csr_max_respmax = "";
                        $max_n = $this->data_set($data['Record']['Data']['selPWmtxSeries']['maxN']);
                        $csr = $this->data_set($data['Record']['Data']['selPWmtxSeries']['CSR']);
                        $csr_max = $this->data_set($data['Record']['Data']['selPWmtxSeries']['CSRmax']);
                        $csr_hz = $this->data_set($data['Record']['Data']['selPWmtxSeries']['CSRHz']);
                        $csr_pq = $this->data_set($data['Record']['Data']['selPWmtxSeries']['CSRpq']);
                        $rsi = $this->data_set($data['Record']['Data']['selPWmtxSeries']['RSI']);
                        $csr_max_respmax = $this->data_set($data['Record']['Data']['selPWmtxSeries']['CSRmax/Respmax']);
                        $sql_result = $sel_pwmtx_series->insert([
                            'patient_id' => $data['Record']['Data']['ID'],
                            'doc_date' => $data['Record']['Data']['DocDate'],
                            'max_n' => $max_n,
                            'csr' => $csr,
                            'csr_max' => $csr_max,
                            'csr_hz' => $csr_hz,
                            'csr_pq' => $csr_pq,
                            'rsi' => $rsi,
                            'csr_max_respmax' => $csr_max_respmax,
                            'create_date' => now(),
                        ]);
                        Log::debug("8888");
                        $sel_pwmtx_series = new SePwmtxSeriesThree();
                        $rsi1 = "";
                        $lng_pwsel2 = "";
                        $nz4sel2 = "";
                        $nz_lng2 = "";
                        $xn2 = "";
                        $xhr2 = "";
                        $xmin2 = "";
                        $rsi1 = $this->data_set($data['Record']['Data']['selPWmtxSeries']['RSI1']);
                        $lng_pwsel2 = $this->data_set($data['Record']['Data']['selPWmtxSeries']['LngPWSel2']);
                        $nz4sel2 = $this->data_set($data['Record']['Data']['selPWmtxSeries']['Nz4Sel2']);
                        $nz_lng2 = $this->data_set($data['Record']['Data']['selPWmtxSeries']['Nz_Lng2']);
                        $xn2 = $this->data_set($data['Record']['Data']['selPWmtxSeries']['Xn2']);
                        $xhr2 = $this->data_set($data['Record']['Data']['selPWmtxSeries']['Xhr2']);
                        $v = $this->data_set($data['Record']['Data']['selPWmtxSeries']['Xmin2']);
                        $sql_result = $sel_pwmtx_series->insert([
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
                        // DB::commit();
                        File::delete($val);
                    // } catch (\Exception $e) {
                    //     DB::rollback();
                    // }
                }
                Log::debug($files);
                Log::debug('ファイルは存在します。');
            }
        })->dailyAt('09:40');
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
