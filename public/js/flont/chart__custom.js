//iOSの慣性スクロールに関するCSSの属性が指定されている場合は、フラグを立てて、属性値を変更する
var touchFlag = false;
if($("#list_table").css("-webkit-overflow-scrolling") === "auto"){
  //var ua = window.navigator.userAgent.toLowerCase();
  //var iOSflag = ua.indexOf('iphone') > -1 || ua.indexOf('ipad') > -1 || ua.indexOf('macintosh') > -1 && 'ontouchend' in document;
  touchFlag = true;
  $("#list_table").css("-webkit-overflow-scrolling", "touch"); //古いiOSの慣性スクロールを有効にする
}
//タッチ系のデバイスの時だけ、ダミーのスクロールバーを作って表示させる
createScrollBar();

//モーダル内のメモ内容ボタンの選択肢を作成
createMemoChoices(memoChoices);

/*--------------------
モーダルの制御
--------------------*/
//メモ追加ボタンをクリックした時のモーダルの制御
$(document).on("click", "#memo_add", function(){
  $("#memo_set").removeClass("memo_edit");
  $("#memo_set").addClass("memo_create");
  memoSetPicker.setDate("today"); //ピッカーの日付を今日にする
  $("#memo_setLabel span").text($("#memo_setLabel_choices li:first-of-type").text()); //内容の初期選択肢を設定
  $("#memo_setLabel")[0].dataset.value = $("#memo_setLabel_choices li:first-of-type")[0].dataset.value; //内容の初期選択肢のvalue値を設定
  $("#memo_set").modal("show");
});
//数値リスト内のメモをクリックした時のモーダルの制御
$(document).on("click", "#list_table .memoClickable", function(){
  $("#memo_set").removeClass("memo_create");
  $("#memo_set").addClass("memo_edit");
  memoSetPicker.setDate($(this)[0].dataset.date); //ピッカーの日付を指定の日にする
  $("#memo_setDate").prop('disabled', true);
  $("#memo_setLabel span").text($(this)[0].dataset.value); //内容の初期選択肢を設定
  $("#memo_setLabel")[0].dataset.value = $("#memo_setLabel_choices li:first-of-type")[0].dataset.value; //内容の初期選択肢のvalue値を設定
  $("#memo_set").modal("show");
});
//メモのモーダル内の「内容」の選択肢をクリックした時の制御
$(document).on("click", "#memo_setLabel_choices li", function(){
  exchangeDropdownText($(this));
});

/*--------------------
グラフ上部のボタン類の制御
--------------------*/
//グラフ範囲を指定する日付指定ピッカー内のボタン押下時の制御
//キャンセルボタン
$(document).on("click", "#calendar_close", function(){
  latestDatePicker.setDate(currentDate,false);
  setStartEndDates(currentDate); //表示する日付と属性の値を変更する
  latestDatePicker.close();
});
//変更ボタン
$(document).on("click", "#calendar_change", function(){
　reDraw();
});

//グラフの期間選択ボタン押下時の制御
$(document).on("click", "#chart_dayRange_choices li" ,function(){
  var clickedVal = $(this)[0].dataset.value;
  var currentVal = $(this).parent().parent().find(".btn")[0].dataset.value;
  exchangeDropdownText($(this));
  if(clickedVal != currentVal) {
    reDraw();
  }
});
    
//グラフの凡例押下時の制御
$(document).on("click", "#chart_legend input[type='checkbox']", function(){
  var targetName = $(this).attr("name");
  targetName = targetName.replace(/\s/g, "x"); //半角スペースがある場合は、xに置き換え
  $("g[seriesName=" + targetName + "]").toggle(250);
});

/*--------------------
ウィンドウサイズが変更されたときの制御
--------------------*/
var timerResized;
var currentRatio = window.devicePixelRatio; 
window.addEventListener( "resize", function () {
  if(currentRatio === window.devicePixelRatio){ //ズームした直後でない時のみ実行
    clearTimeout(timerResized);
    timerResized = setTimeout(function(){ reDraw(); },300)
    //console.log("resized");
    currentRatio = window.devicePixelRatio;
  }
});


/*--------------------
関数
--------------------*/
//タッチ系デバイスの時だけダミーのスクロールバーを作って表示させる関数
function createScrollBar() {
  if(touchFlag){
    var table_w = Math.round($("#list_table table").width());
    var visible_w = Math.round($("#list_table").width());
    if(table_w - visible_w > 2){
      $("#scroll_bar").removeClass("d-none");
      var scrollBoxRatio = (visible_w / table_w) * 100
      $("#scroll_box").css("width", scrollBoxRatio + "%");
    } else {
      $("#scroll_bar").addClass("d-none");
    }
    var timerScroll;
    $("#list_table").scroll(function() {
      clearTimeout(timerScroll);
      timerScroll = setTimeout(function(){ 
        var scrollLeft = $("#list_table").scrollLeft() * (visible_w / table_w);
        $("#scroll_box").css("transform", "translateX(" + scrollLeft + "px");
      },200);
    })
  }
}

//dropdown形式の値を選択したらテキストを入れ替える関数
function exchangeDropdownText(jQueryElm){
  var selectedText = jQueryElm.text();
  var parentBtn = jQueryElm.parent().parent().find(".btn");
  parentBtn.children("span").text(selectedText);
  if(jQueryElm.is("[data-value]") && parentBtn.is("[data-value]")){ //data-value属性を持っていたら書き換える
    var selectedValue = jQueryElm[0].dataset.value;
    parentBtn[0].dataset.value = selectedValue;
  }
}

//グラフを再描画して、数値リストを作成する関数
function reDraw() {
  currentDate = flatpickr.formatDate(latestDatePicker.selectedDates[0], "Y-m-d");
  $.when(
    setStartEndDates(currentDate) //表示する日付と属性の値を変更する
  ).done(function() {
    latestDatePicker.close();
    createArryX_moto(); //表示期間の日付の配列を作る
    /*--------------------
    (テスト用)サンプルデータを切り替える
    ※バックエンド連携時に不要になるはずなので、適宜削除してください
    --------------------*/
    switch(dayRange){ //グラフ＆リスト表示用にデータを整形する
      case 30: createData(data_rst_30, data_heart_30, data_breath_30, data_csr_30, data_sleep_30, data_memo_30); break;
      case 60: createData(data_rst_60, data_heart_60, data_breath_60, data_csr_60, data_sleep_60, data_memo_60); break;
      case 90: createData(data_rst_90, data_heart_90, data_breath_90, data_csr_90, data_sleep_90, data_memo_90); break;
      default: createData(data_rst_30, data_heart_30, data_breath_30, data_csr_30, data_sleep_30, data_memo_30);
    }
    //--------------------
    chart.clearAnnotations(); //今のメモラベルを削除する
    chart.updateOptions({ //グラフのデータを更新して描画する
      chart: { width: document.getElementById('chart').clientWidth, height: chart_h(), events: {updated: function() {setDataToTable(dataForTable); createMemoLabel(); createScrollBar();}}},
      series: [{data: arryY_rst[1]}, {data: arryY_heart[1]}, {data: arryY_breath[1]}, {data: arryY_csr[1]}, {data: arryY_sleep[1]}],
      xaxis: {categories: arryX_moto},
      yaxis: y_axis_setting
    },true);
  });
}

//引数からモーダル内のメモ内容の選択肢を作成
function createMemoChoices(arry){
  //選択肢を作成
  for(var i=0; i<arry.length; i++){
    $("#memo_setLabel_choices").append("<li class='dropdown-item' data-value='" + arry[i] + "'>" + arry[i] + "</li>");
  }
  //初期状態では選択肢の最初の項目を設定
  $("#memo_setLabel")[0].dataset.value = arry[0];
   $("#memo_setLabel span").text(arry[0]);
}
