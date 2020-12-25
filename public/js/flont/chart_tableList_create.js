/*--------------------
変数の定義
--------------------*/
//tableの幅を定義するための値を計算
var chart_graphic_w = $(".apexcharts-graphical")[0].getBoundingClientRect().width; //グラフの描画部分の幅
var table_th_w = $(".apexcharts-graphical")[0].getBoundingClientRect().left - 16; //グラフの描画部分のX座標からpaddingの分を引く
var table_cell_w = chart_graphic_w / 30; //リストのセルの幅の初期値
var tableWidth = chart_graphic_w + table_th_w; //リストの全体の幅の初期値
var tableWrapper_w = chart_graphic_w + table_th_w; //リストのラッパーの幅の初期値

setDataToTable(dataForTable);

/*--------------------
関数
--------------------*/
function createTrNum(dataArry, index){ //データが数値(メモ以外)の場合のリスト作成
  if(IEflag){ //IEの場合はダミーのヘッダーを作成する
    $("#dummy_tableHead_foIE").append("<li>" + dataArry[0] + "</li>");
  }
  $("#list_table table tbody").append("<tr id='tableListNum_row" + index + "'></tr>");
  var trID = "#list_table table tbody tr#tableListNum_row" + index;
  $(trID).append("<th class='dataTitle' title='" + dataArry[0] + "' style='width:" + table_th_w + "px;'>" + dataArry[0] + "</th>");
  for(var i=0; i<dataArry[1].length; i++){
    var elem_num = dataArry[1][i];
    if(elem_num){
      if(typeof elem_num === "number" && Number.isInteger(elem_num) === false){ //データが数値かつ整数ではない場合
        elem_num = Math.round(elem_num*10)/10; //小数点第一位までになるように四捨五入
      }
      var elem = "<td>" + elem_num + "</td>";
      $(trID).append(elem);
    } else {
      var elem = "<td></td>";
      $(trID).append(elem);
    }
  }
}

function createTrDate(dataArry){ //データが日付の場合のリスト作成
  if(IEflag){ //IEの場合はダミーのヘッダーを作成する
    $("#dummy_tableHead_foIE").append("<li style='color: white;'>日付</li>");
  }
  $("#list_table table thead").append("<tr id='tableListDate_row'></tr>");
  var trID = "#list_table table thead tr#tableListDate_row";
  $(trID).append("<th class='dataTitle' title='" + dataArry[0] + "' style='width:" + table_th_w + "px;'>" + dataArry[0] + "</th>");
  for(var i=0; i<dataArry[1].length; i++){
    var elem_num = dataArry[1][i];
    var elem = "<td style='width:" + table_cell_w + "px;'>" + elem_num + "</td>";
    $(trID).append(elem);
  }
}

function createTrMemo(dataArry){ //データがメモの場合のリスト作成
  if(IEflag){ //IEの場合はダミーのヘッダーを作成する
    $("#dummy_tableHead_foIE").append("<li>" + dataArry[0] + "</li>");
  }
  $("#list_table table tbody").append("<tr id='tableListMemo_row'></tr>");
  var trID = "#list_table table tbody tr#tableListMemo_row";
  $(trID).append("<th class='dataTitle' title='" + dataArry[0] + "' style='width:" + table_th_w + "px;'>" + dataArry[0] + "</th>");
  for(var i=0; i<dataArry[1].length; i++){
    if(dataArry[1][i]) {
      var memoTextClip = dataArry[1][i].slice(0,2); //先導2文字だけの文字列
      var elem = "<td class='memoClickable' title='" + dataArry[1][i] + "'>" + memoTextClip + "</td>";
    } else {
      elem = "<td></td>";
    }
    $(trID).append(elem);
  }
}

function setDataToTable(multiDataArry){ //引数の配列からリストを作成
  //スクロールバーのクラスを削除
  $("#list_table").removeClass("scroll");
  //tableの幅を定義するための値を計算
  chart_graphic_w = $(".apexcharts-graphical")[0].getBoundingClientRect().width; //グラフの描画部分の幅
  table_th_w = $(".apexcharts-graphical")[0].getBoundingClientRect().left - 16; //グラフの描画部分の左端座標からpaddingの分を引く
  table_cell_w = chart_graphic_w / multiDataArry[0][1].length;
  tableWrapper_w = chart_graphic_w + table_th_w;
  if(multiDataArry[0][1].length>30){ //30日を超えた場合は、テーブルの幅はセルの数で換算
    tableWidth = (chart_graphic_w / 30)*multiDataArry[0][1].length + table_th_w;
    table_cell_w = chart_graphic_w / 30;
    if(table_cell_w < 28){
      table_cell_w = 28;
      tableWidth = table_cell_w*multiDataArry[0][1].length + table_th_w;
    }
    $("#list_table").addClass("scroll");
  } else {
    tableWidth = chart_graphic_w + table_th_w;
    if(table_cell_w < 20){ //スマホサイズの画面で見た場合はテーブルの幅を大きくする
      table_cell_w = 20;
      tableWidth = table_cell_w*multiDataArry[0][1].length + table_th_w;
    }
  }
  $("#list_table").css({"width":tableWrapper_w});
  $("#list_table table").css({"width":tableWidth});
  $("#list_table table thead").empty(); //要素を空にする
  $("#list_table table tbody").empty(); //要素を空にする
  if(IEflag){ //IEの場合はダミーのヘッダーを空にする
    $("#dummy_tableHead_foIE").empty();
    $("#dummy_tableHead_foIE").css({
      width: table_th_w + "px",
      top: $("#list_table table").offset().top + "px",
      left: $("#list_table table").offset().left + "px"
    });
    $("#dummy_tableHead_foIE").removeClass("d-none");
  }
  for(var i=0; i<multiDataArry.length; i++){
    if(i === 0){
      createTrDate(multiDataArry[i]);
    }
    else if(i === multiDataArry.length - 1){
      createTrMemo(multiDataArry[i]);
    } else {
      createTrNum(multiDataArry[i], i);
    }
  }
}
