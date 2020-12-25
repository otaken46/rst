flatpickr.l10ns.ja.firstDayOfWeek = 0; //日曜始まりに変更
var currentDate = ""; //選択中の日付を定義

//ピッカーのオプション一式
var datepick_opt = {
  locale: "ja",
  defaultDate: "today", //初期表示の日付
  dateFormat: "Y/m/d",
  maxDate: "today",
  monthSelectorType: "static",
  closeOnSelect: false,
  disable: [
    {
      from: new Date().fp_incr(1) // 今から1日後
    }
  ],
  enable: [
    {
      from: oldestDate,
      to: "today"
    }
  ],
  nextArrow: "<i class='fas fa-angle-double-right'></i>",
  prevArrow: "<i class='fas fa-angle-double-left'></i>",
  clickOpens: false, //toggle()で手動切り替えするので、無効化
  ignoredFocusElements: [document.body], //外部要素をクリックしても閉じないようにする
};

/*--------------------
グラフの範囲を指定するピッカーを設置
--------------------*/
//グラフの日付指定ピッカーをセット
var latestDatePicker = flatpickr("#chart_latestDate", datepick_opt);
//グラフの日付指定ピッカーの下部にボタンを追加
latestDatePicker.set("onReady",setFooterButton(latestDatePicker));
//input要素のクリックでピッカーの開閉を制御できるようにする
$(document).on("click", "#chart_latestDate", function(){
 latestDatePicker.open();
});
   
/*----------
・メモ追加時([+メモ追加]ボタン押下時)
・メモ編集時(リスト内のメモ内容クリック時)
のピッカーを設置
----------*/
var memoSetPicker = flatpickr("#memo_setDate", datepick_opt);
memoSetPicker.set("closeOnSelect",true); //選択時にピッカーを閉じる仕様に変更
//input要素のクリックでピッカーの開閉を制御
$(document).on("click", "#memo_setDate", function(){
  memoSetPicker.toggle();
});


/*--------------------
関数
--------------------*/
//最新の日付から最古の日付を計算し、内容を入れ替える関数 (ピッカー操作時に使用)
function setStartEndDates(end_date) {
  var dayAdd = $("#chart_dayRange")[0].dataset.value - 1; //データの期間
  var startDateObj = new Date(end_date);
  startDateObj.setDate(startDateObj.getDate() - dayAdd);
  var e_year = startDateObj.getFullYear();
  var e_month = startDateObj.getMonth() + 1;
  e_month = ("0" + e_month).slice(-2); //0埋め
  var e_date = startDateObj.getDate();
  e_date = ("0" + e_date).slice(-2); //0埋め
  var startDateText = e_year + "/" + e_month + "/" + e_date;
  var startDate = e_year + "-" + e_month + "-" + e_date;
  $("#chart_earliestDate").text(startDateText); //最古日の日付を書き換え
  $("#chart_earliestDate")[0].dataset.date = startDate;  //最古日のdata属性の日付を書き換え
  $("#chart_latestDate")[0].dataset.date = end_date; //最新日のdata属性の日付を書き換え
}

//オプション onReady で下部に独自ボタンを追加するための関数
function setFooterButton(pick) {
  //選択中の日付を格納
  currentDate = flatpickr.formatDate(pick.selectedDates[0], "Y-m-d");
  setStartEndDates(currentDate);
  //独自ボタンを追加
  $(".flatpickr-calendar").append("<div id='calendar_btn'></div>");
  $("<span class='text-left'><button id='calendar_close' class='btn btn-outline-secondary d-inline-block'>キャンセル</button></span><span class='text-right'><button id='calendar_change'class='btn btn-primary d-inline-block'>変更</button></span>").appendTo("#calendar_btn");
}
