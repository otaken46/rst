var IEflag = false;
//IE判定
var userAgent = window.navigator.userAgent.toLowerCase();
if(userAgent.indexOf('msie') != -1 || userAgent.indexOf('trident') != -1) {
  IEflag = true;
}

if(IEflag) {
  $(".forIE").removeClass("d-none"); //IEのalertを表示
  //Polyfill CDN
  $.getScript("https://polyfill.io/v3/polyfill.min.js");
  $.getScript("https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"); 
  $.getScript("https://cdn.jsdelivr.net/npm/eligrey-classlist-js-polyfill");
}

//Polyfill for isInteger
Number.isInteger = Number.isInteger || function(value) {
  return typeof value === 'number' &&
    isFinite(value) &&
    Math.floor(value) === value;
};

$(document).ready(function(){
  //IEのみ、読み込み完了後にグラフを再描画する
  if(IEflag && $("body").hasClass("p_chart")){
    //数値リストのダミーのヘッダーを設置する
    $("#list_table").append("<ul id='dummy_tableHead_foIE' class='d-none'></ul>");
    chart.updateOptions({ //グラフのデータを更新して描画する
      chart: { width: document.getElementById('chart').clientWidth, events: {updated: function() {setDataToTable(dataForTable); createMemoLabel();}}},
      series: [{data: arryY_rst[1]}, {data: arryY_heart[1]}, {data: arryY_breath[1]}, {data: arryY_csr[1]}, {data: arryY_sleep[1]}],
      xaxis: {categories: arryX_moto},
      yaxis: y_axis_setting
    },true);
  }
});
// グラフページの数値リストで、ダミーのヘッダーをスクロールに追従させる
if(IEflag && $("body").hasClass("p_chart")) {
  $(window).scroll(function() {
    $("#dummy_tableHead_foIE").addClass("d-none");
    var timer_w;
    clearTimeout(timer_w);
    timer_w = setTimeout(function(){
      var current_Y = $("#list_table table").offset().top - $(window).scrollTop();
      $("#dummy_tableHead_foIE").css("top", current_Y + "px");
      $("#dummy_tableHead_foIE").removeClass("d-none")
    },300);
  })
  $(".forIE button").on("click", function(){
    var timer_b;
    clearTimeout(timer_b);
    timer_b = setTimeout(function(){
      var current_Y = $("#list_table table").offset().top - $(window).scrollTop();
      $("#dummy_tableHead_foIE").css("top", current_Y + "px");
    },300);
  })
}