/*--------------------
変数の定義
--------------------*/
//日付のデフォルトの期間
var dayRange = 30;
//開始〜終了までの日付データを格納するための変数・グラフの描画でも使用
var arryX_moto = [];
//数値リストで表示するための日付の配列
var arryX_table = [""];
//グラフと数値リストで表示するための各指標の値の配列
var arryY_rst = ["RST"], arryY_heart = ["心拍数"], arryY_breath = ["呼吸数"], arryY_csr = ["CSR G"], arryY_sleep = ["臥床時間"], arryY_memo = ["メモ"];
//数値リストに、データをまとめて渡すための配列
var dataForTable = [];
//--------------------

createArryX_moto();
createData(data_rst_30, data_heart_30, data_breath_30, data_csr_30, data_sleep_30, data_memo_30);

/*--------------------
関数
--------------------*/
//開始〜終了までの日付データの配列を作成する関数
function createArryX_moto(){ //yyyy-mm-dd
  arryX_moto.length = 0; //配列を初期化
  var startDate = $("#chart_earliestDate")[0].dataset.date; //開始日の日付 yyyy-mm-dd
  dayRange = Number($("#chart_dayRange")[0].dataset.value); //日付の期間
  for(var i=0; i<dayRange; i++){
    var startObj = new Date(startDate);
    startObj.setDate(startObj.getDate() + i);
    var current = startObj.getFullYear() + "-" + ("0" + (startObj.getMonth() + 1)).slice(-2)+ "-" + ("0" + startObj.getDate()).slice(-2);
    arryX_moto.push(current);
  }
  //console.log(arryX_moto);
}

//描画で使用するデータを引数から整形して、グローバル変数に渡す
function createData(arr_rst, arr_heart, arr_breath, arr_csr, arr_sleep, arr_memo){
  //配列の最初の値以外を削除 (配列の初期化)
  arryY_rst.splice(1, arryY_rst.length - 1);
  arryY_heart.splice(1, arryY_heart.length - 1);
  arryY_breath.splice(1, arryY_breath.length - 1);
  arryY_csr.splice(1, arryY_csr.length - 1);
  arryY_sleep.splice(1, arryY_sleep.length - 1);
  arryY_memo.splice(1, arryY_memo.length - 1);
  //配列にデータの配列を格納
  arryY_rst.push(arr_rst);
  arryY_heart.push(arr_heart);
  arryY_breath.push(arr_breath);
  arryY_csr.push(arr_csr);
  arryY_sleep.push(arr_sleep);
  arryY_memo.push(arr_memo);
  //数値リストで使用する日付データを作成
  //配列を初期化
  arryX_table.splice(1, arryX_table.length -1);
  //日付データの配列から数値リスト用の日付の配列を作成
  var arr_t = [];
  for(var i=0; i<arryX_moto.length; i++){
    var currentDateObj = new Date(arryX_moto[i]);
    var date_table = currentDateObj.getDate();
    if(date_table === 1){
      date_table = (currentDateObj.getMonth() + 1) + "/" + date_table;
    } else{
      date_table = date_table.toString();
    }
    arr_t.push(date_table);
  }
  arryX_table.push(arr_t);
  //リストに渡すためのデータを作成
  //配列を初期化
  dataForTable.length = 0;
  //配列を作成
  dataForTable.push(arryX_table, arryY_rst, arryY_heart, arryY_breath, arryY_csr, arryY_sleep, arryY_memo);
  //console.log(dataForTable);
}