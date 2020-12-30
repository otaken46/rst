<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\File;
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
                    $json = file_get_contents($val);
                    $data = json_decode($json, true);
                    Log::debug($data);
                    File::delete($val);
                }
                Log::debug($files);
                Log::debug('ファイルは存在します。');
            }
        })->dailyAt('15:11');
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
}
