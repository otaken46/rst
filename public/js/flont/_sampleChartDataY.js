//flatpickr 用のデータ
//var oldestDate = "2020-08-12"; //最古のデータの日付を格納
var oldestDate = old_date;
var hoge = data;
var first_view = true;
var data_rst_30 = [];
var data_heart_30 = [];
var data_breath_30 = [];
var data_csr_30 = [];
var data_sleep_30 = [];
var data_memo_30 = [];
var data_rst_60 = [];
var data_heart_60 = [];
var data_breath_60 = [];
var data_csr_60 = [];
var data_sleep_60 = [];
var data_memo_60 = [];
var data_rst_90 = [];
var data_heart_90 = [];
var data_breath_90 = [];
var data_csr_90 = [];
var data_sleep_90 = [];
var data_memo_90 = [];
if(first_view){
    first_view = false;
    chartDataSet();
}
function chartDataSet(date = "today") {
    var YYYY = "";
    var MM = "";
    var DD = "";
    var toDoubleDigits = function(num) {
        num += "";
        if (num.length === 1) {
        num = "0" + num;
        }
    return num;     
    };
    data_rst_30 = [];
    data_heart_30 = [];
    data_breath_30 = [];
    data_csr_30 = [];
    data_sleep_30 = [];
    data_memo_30 = [];
    data_rst_60 = [];
    data_heart_60 = [];
    data_breath_60 = [];
    data_csr_60 = [];
    data_sleep_60 = [];
    data_memo_60 = [];
    data_rst_90 = [];
    data_heart_90 = [];
    data_breath_90 = [];
    data_csr_90 = [];
    data_sleep_90 = [];
    data_memo_90 = [];
    if(date == "today"){
        var today = new Date();
    }else{
        var today = new Date(date);
    }
    for(var cnt = 90; cnt > 0 ;cnt--){
        if(cnt == 90){
            today.setDate(today.getDate() - (cnt - 1));
        }else{
            today.setDate(today.getDate() + 1);
        }
        YYYY = today.getFullYear();
        MM = toDoubleDigits(today.getMonth() + 1);
        DD = toDoubleDigits(today.getDate());
        str = YYYY + "-" + MM + "-" + DD;
        if(data[str] != undefined){
            data_rst_90.push(data[str]['mean_respr']);
            data_heart_90.push(data[str]['mean_cvr']);
            data_breath_90.push(data[str]['mean_rsi']);
            data_csr_90.push(data[str]['max_xhr2']);
            data_sleep_90.push(data[str]['mean_hr']);
            data_memo_90.push(data[str]['note']);
        }else{
            data_rst_90.push("");
            data_heart_90.push("");
            data_breath_90.push("");
            data_csr_90.push("");
            data_sleep_90.push("");
            data_memo_90.push("");
        }
        if(cnt < 61){
            if(data[str] != undefined){
                data_rst_60.push(data[str]['mean_respr']);
                data_heart_60.push(data[str]['mean_cvr']);
                data_breath_60.push(data[str]['mean_rsi']);
                data_csr_60.push(data[str]['max_xhr2']);
                data_sleep_60.push(data[str]['mean_hr']);
                data_memo_60.push(data[str]['note']);
            }else{
                data_rst_60.push("");
                data_heart_60.push("");
                data_breath_60.push("");
                data_csr_60.push("");
                data_sleep_60.push("");
                data_memo_60.push("");
            }
        }
        if(cnt < 31){
            if(data[str] != undefined){
                data_rst_30.push(data[str]['mean_respr']);
                data_heart_30.push(data[str]['mean_cvr']);
                data_breath_30.push(data[str]['mean_rsi']);
                data_csr_30.push(data[str]['max_xhr2']);
                data_sleep_30.push(data[str]['mean_hr']);
                data_memo_30.push(data[str]['note']);
            }else{
                data_rst_30.push("");
                data_heart_30.push("");
                data_breath_30.push("");
                data_csr_30.push("");
                data_sleep_30.push("");
                data_memo_30.push("");
            }
        }
    }
}
//グラフ用のサンプルデータ
//30日間
//var data_rst_30 = [68,68,73,68,72,71,66,72,68,73,63,68,57,66,66,64,69,61,58,61,62,63,69,70,66,63,63,63,62,61]; //RST
//var data_heart_30 = [58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49]; //心拍数
//var data_breath_30 = [18,15,13,14,15,18,20,21,20,19,18,16,18,18,17,19,20,21,18,22,24,20,22,21,18,20,16,24,22,21]; //呼吸数
//var data_csr_30 = [0.32 ,0.12 ,0.47 ,0.69 ,0.47 ,0.17 ,0.28 ,0.30 ,0.32 ,0.27 ,0.69 ,0.17 ,0.80 ,0.64 ,0.52 ,0.76 ,0.29 ,0.74 ,0.49 ,0.11 ,0.70 ,0.45 ,0.50 ,1.41 ,1.41 ,0.85 ,1.40 ,0.92 ,0.64 ,1.36]; //CSR
//var data_sleep_30 = [7.5, 7.5, 8, 8, 7.5, 6, 6, 7.5, 7, 7, 8, 7, 7.5, 7, 6, 6, 5, 4.5, 4.5, 5, 5, 4.5, 4, 4, 7.5, 7, 7, 7.5, 8, 7.5]; //臥床時間
//var data_memo_30 = [null, null, null, null, null, 'センサOFF', 'センサOFF', null, null, null, 'ペア失敗', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null]; //メモ

//60日間
//var data_rst_60 = [68,68,73,68,72,null,66,72,68,73,63,68,57,66,66,64,69,61,58,61,62,63,69,70,66,63,63,63,62,61,68,68,73,68,72,71,66,72,68,73,63,68,57,66,66,64,69,61,58,61,62,63,69,70,66,63,63,63,62,61]; //RST
//var data_heart_60 = [58,55,53,50,50,null,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49]; //心拍数
//var data_breath_60 = [18,15,13,14,15,null,20,21,20,19,18,16,18,18,17,19,20,21,18,22,24,20,22,21,18,20,16,24,22,21,18,15,13,14,15,18,20,21,20,19,18,16,18,18,17,19,20,21,18,22,24,20,22,21,18,20,16,24,22,21]; //呼吸数
//var data_csr_60 = [0.32 ,0.12 ,0.47 ,0.69 ,0.47 ,null ,0.28 ,0.30 ,0.32 ,0.27 ,0.69 ,0.17 ,0.80 ,0.64 ,0.52 ,0.76 ,0.29 ,0.74 ,0.49 ,0.11 ,0.70 ,0.45 ,0.50 ,1.41 ,1.41 ,0.85 ,1.40 ,0.92 ,0.64 ,1.36 ,0.32 ,0.12 ,0.47 ,0.69 ,0.47 ,0.17 ,0.28 ,0.30 ,0.32 ,0.27 ,0.69 ,0.17 ,0.80 ,0.64 ,0.52 ,0.76 ,0.29 ,0.74 ,0.49 ,0.11 ,0.70 ,0.45 ,0.50 ,1.41 ,1.41 ,0.85 ,1.40 ,0.92 ,0.64 ,1.36]; //CSR
//var data_sleep_60 = [7.5, 7.5, 8, 8, 7.5, null, 6, 7.5, 7, 7, 8, 7, 7.5, 7, 6, 6, 5, 4.5, 4.5, 5, 5, 4.5, 4, 4, 7.5, 7, 7, 7.5, 8, 7.5, 7.5, 7.5, 8, 8, 7.5, 6, 6, 7.5, 7, 7, 8, 7, 7.5, 7, 6, 6, 5, 4.5, 4.5, 5, 5, 4.5, 4, 4, 7.5, 7, 7, 7.5, 8, 7.5]; //臥床時間
//var data_memo_60 = [null, null, null, null, null, 'センサOFF', 'センサOFF', null, null, null, 'ペア失敗', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 'センサOFF', 'センサOFF', null, null, null, 'ペア失敗', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null]; //メモ

//90日間
//var data_rst_90 = [68,68,73,68,72,71,66,72,68,73,63,68,57,66,66,64,69,61,58,61,62,63,69,70,66,63,63,63,62,61,68,68,73,68,72,71,66,72,68,73,63,68,57,66,66,64,69,61,58,61,62,63,69,70,66,63,63,63,62,61,68,68,73,68,72,71,66,72,68,73,63,68,57,66,66,64,69,61,58,61,62,63,69,70,66,63,63,63,62,61]; //RST
//var data_heart_90 = [58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49,48,46,58,55,53,50,50,49]; //心拍数
//var data_breath_90 = [18,15,13,14,15,18,20,21,20,19,18,16,18,18,17,19,20,21,18,22,24,20,22,21,18,20,16,24,22,21,18,15,13,14,15,18,20,21,20,19,18,16,18,18,17,19,20,21,18,22,24,20,22,21,18,20,16,24,22,21,18,15,13,14,15,18,20,21,20,19,18,16,18,18,17,19,20,21,18,22,24,20,22,21,18,20,16,24,22,21]; //呼吸数
//var data_csr_90 = [0.32 ,0.12 ,0.47 ,0.69 ,0.47 ,0.17 ,0.28 ,0.30 ,0.32 ,0.27 ,0.69 ,0.17 ,0.80 ,0.64 ,0.52 ,0.76 ,0.29 ,0.74 ,0.49 ,0.11 ,0.70 ,0.45 ,0.50 ,1.41 ,1.41 ,0.85 ,1.40 ,0.92 ,0.64 ,1.36, 0.32 ,0.12 ,0.47 ,0.69 ,0.47 ,0.17 ,0.28 ,0.30 ,0.32 ,0.27 ,0.69 ,0.17 ,0.80 ,0.64 ,0.52 ,0.76 ,0.29 ,0.74 ,0.49 ,0.11 ,0.70 ,0.45 ,0.50 ,1.41 ,1.41 ,0.85 ,1.40 ,0.92 ,0.64 ,1.36, 0.32 ,0.12 ,0.47 ,0.69 ,0.47 ,0.17 ,0.28 ,0.30 ,0.32 ,0.27 ,0.69 ,0.17 ,0.80 ,0.64 ,0.52 ,0.76 ,0.29 ,0.74 ,0.49 ,0.11 ,0.70 ,0.45 ,0.50 ,1.41 ,1.41 ,0.85 ,1.40 ,0.92 ,0.64 ,1.36]; //CSR
//var data_sleep_90 = [7.5, 7.5, 8, 8, 7.5, 6, 6, 7.5, 7, 7, 8, 7, 7.5, 7, 6, 6, 5, 4.5, 4.5, 5, 5, 4.5, 4, 4, 7.5, 7, 7, 7.5, 8, 7.5, 7.5, 7.5, 8, 8, 7.5, 6, 6, 7.5, 7, 7, 8, 7, 7.5, 7, 6, 6, 5, 4.5, 4.5, 5, 5, 4.5, 4, 4, 7.5, 7, 7, 7.5, 8, 7.5, 7.5, 7.5, 8, 8, 7.5, 6, 6, 7.5, 7, 7, 8, 7, 7.5, 7, 6, 6, 5, 4.5, 4.5, 5, 5, 4.5, 4, 4, 7.5, 7, 7, 7.5, 8, 7.5]; //臥床時間
//var data_memo_90 = [null, null, null, null, null, 'センサOFF', 'センサOFF', null, null, null, 'ペア失敗', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 'センサOFF', 'センサOFF', null, null, null, 'ペア失敗', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 'センサOFF', 'センサOFF', null, null, null, 'ペア失敗', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null]; //メモ
