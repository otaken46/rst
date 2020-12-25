//日本語化
/*$.extend( $.fn.dataTable.defaults, { 
  language: {
    url: "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Japanese.json"
  }
});*/ 
var dataTable = $("#list_table").DataTable({
  lengthChange: false, //件数切替
  searching: false, //検索
  //ordering: false, // ソート
  info: false, //情報表示
  paging: false, //ページング
  autoWidth: false, //自動の幅設定
  columnDefs : [ //列設定
    { targets: [3, 4, 5, 6, 7], sortable: false},
  ],
  data: data,
  columns: [
    { data: "患者ID" },
    { data: "患者名" },
    { data: "最終更新" },
    { data: "RST" },
    { data: "心拍数" },
    { data: "呼吸数" },
    { data: "CSRグレード" },
    { data: "臥床時間" }
    ],
  "scrollY": returnScrollHeight(),
  "scrollCollapse": true,
  "createdRow": function( row, data, dataIndex ) {
    $(row).addClass( 'list_row' );
    for(var i = 0; i < flag_pink.length; i++){
      if(flag_pink[i] === dataIndex){
        $(row).addClass('pink');
      }
    }
    for(var i = 0; i < flag_yellow.length; i++){
      if(flag_yellow[i] === dataIndex){
        $(row).addClass('yellow');
      }
    }
  },
});

//IEのみ、DataTablesの機能でヘッダーを固定する
function returnScrollHeight(){
  var h_text;
  if(IEflag){
    var h = window.innerHeight - $("#list_table thead th").innerHeight() - 64;
    h_text = h + "px";
  } else {
    h_text = "";
  }
  return h_text;
}

//table要素のwrapperの高さを指定する
var table_wrapper_h = window.innerHeight - 64;
$("#list_table_wrapper").css("height",table_wrapper_h + "px");
var timerResized;
window.addEventListener( "resize", function () {
  clearTimeout(timerResized);
  timerResized = setTimeout(function(){ 
    table_wrapper_h = window.innerHeight - 64;
    $("#list_table_wrapper").css("height",table_wrapper_h + "px");
    if(IEflag){
      dataTable.columns.adjust();
    }
  },300);
});

