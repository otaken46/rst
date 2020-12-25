<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>詳細グラフ｜RST結果ビューア</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- ライブラリ -->
  <link rel="stylesheet" href="{{ asset('_vendors/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('_vendors/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('_vendors/flatpickr/css/flatpickr.min.css') }}">
  <!-- 独自 -->
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="p_chart">
  <nav class="navbar fixed-top navbar-expand-sm navbar-light bg-light justify-content-between">
    <a id="title_name" href="javascript:void(0)"><i class="fas fa-arrow-left"></i><span class="pr-2">患者ID：<span id="chart_idName">00001</span></span><span class="font-weight-normal chart_updateText d-none d-sm-inline">(<span id="chart_upDate">2021/12/19 7:02</span> 更新)</span></a>
    <ul class="navbar-nav flex-row">
      <li class="nav-item mr-3 d-none d-sm-block">
        <span class="nav-link disabled">医療機関：<span id="list_userName">〇〇〇〇病院</span></span>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">メニュー</a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
          <a id="logout" class="dropdown-item" href="javascript:void(0)">ログアウト</a>
        </div>
      </li>
    </ul>
  </nav>
  
  <!-- IE対応 -->
  <p class="alert alert-warning alert-dismissible fade show forIE d-none" role="alert" style="margin-top: 3rem; z-index: 1000;">
    このブラウザ (Internet Explorer) は本システムの推奨ブラウザではないため、一部機能が制限される可能性があります。<br>
    <a target="_blank"  style="color:white; text-decoration: underline;" href="https://www.google.com/intl/ja/chrome/">Google Chrome</a> または <a target="_blank"  style="color:white; text-decoration: underline;" href="https://www.microsoft.com/ja-jp/edge">Microsoft Edge</a> のご利用をお勧めいたします。
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true"><i class="fas fa-times"></i></span>
    </button>
  </p>
  
  <div class="wrapper" id="chart_area_main">
    <div class="chart_tools">
      <div class="chart_setting">
        <span class="pr-2"><span id="chart_earliestDate" data-date=""></span>〜</span><div class="d-inline-block mr-2 mark_triangle_down"><input id="chart_latestDate" class="flatpickr btn btn-outline-primary" data-date="" type="text" placeholder="Select Date.." readonly="readonly"></div>
        <div class="dropdown d-inline-block">
          <button type="button" id="chart_dayRange" class="btn btn-outline-primary dropdown-toggle" data-value="30" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>直近30日間</span></button>
          <ul class="dropdown-menu" id="chart_dayRange_choices" aria-labelledby="chart_dayRange">
            <li class="dropdown-item" data-value="30">直近30日間</li>
            <li class="dropdown-item" data-value="60">直近60日間</li>
            <li class="dropdown-item" data-value="90">直近90日間</li>
          </ul>
        </div>
        <div id="chart_legend">
          <label id="legend_rst"><input type="checkbox" name="RST" checked><span class="legend_text">RST</span></label>
          <label id="legend_heart"><input type="checkbox" name="心拍数" checked><span class="legend_text">心拍数</span></label>
          <label id="legend_breath"><input type="checkbox" name="呼吸数" checked><span class="legend_text">呼吸数</span></label>
          <label id="legend_csr"><input type="checkbox" name="CSR G" checked><span class="legend_text">CSR G</span></label>
          <label id="legend_sleep"><input type="checkbox" name="臥床時間" checked><span class="legend_text">臥床時間</span></label>
        </div>
      </div>
      <div class="chart_memo">
        <button type="button" id="memo_add" class="btn btn-outline-primary"><i class="fas fa-plus pr-2"></i>メモ追加</button>
      </div>
    </div>
    <!-- グラフ描画エリア -->
    <div id="chart"></div>
    <!-- 数値リストエリア -->
    <div id="list_table">
      <table>
        <thead></thead>
        <tbody></tbody>
      </table>
      <div id="scroll_bar" class="mt-2 d-none">
        <div id="scroll_box"></div>
      </div>
    </div>
  </div>
  
<!-- Modal メモ追加/編集 -->
<div class="modal fade" id="memo_set" tabindex="-1" role="dialog" aria-labelledby="memoSetModalTitle" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="memoSetModalTitle">メモ</h5>
      </div>
      <div class="modal-body">
        <div class="memo_date mb-4 position-relative">
          <span class="d-inline-block mr-2">日付：</span><div class="d-inline-block mark_triangle_down"><input id="memo_setDate" class="flatpickr btn btn-outline-secondary text-left" type="text" placeholder="Select Date.." readonly="readonly"></div>
        </div>
        <div class="memo_label">
          <span class="d-inline-block mr-2">内容：</span><button type="button" id="memo_setLabel" class="btn btn-outline-secondary text-left mark_triangle_down" data-value="ペアリング失敗" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>ペアリング失敗</span></button>
          <ul class="dropdown-menu" id="memo_setLabel_choices" aria-labelledby="memo_setLabel">
            <li class="dropdown-item" data-value="ペアリング失敗">ペアリング失敗</li>
            <li class="dropdown-item" data-value="センサ電源OFF">センサ電源OFF</li>
            <li class="dropdown-item" data-value="スマホバッテリー切れ">スマホバッテリー切れ</li>
            <li class="dropdown-item" data-value="入院中、または、外泊">入院中、または、外泊</li>
            <li class="dropdown-item" data-value="意識消失（有害事象）">意識消失（有害事象）</li>
            <li class="dropdown-item" data-value="憎悪">憎悪</li>
            <li class="dropdown-item" data-value="緩徐低下">緩徐低下</li>
            <li class="dropdown-item" data-value="薬を変えた">薬を変えた</li>
            <li class="dropdown-item" data-value="その他特別な出来事">その他特別な出来事</li>
          </ul>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <div><button type="button" id="memo_delete" class="btn btn-danger">削除</button></div>
        <div>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">キャンセル</button>
          <button type="button" class="btn btn-primary"><span id="m_add">追加</span><span id="m_change">変更</span></button>
        </div>
      </div>
    </div>
  </div>
</div>
  
  <!--
  ーーーーーーーーーーーーーーーーーーーー
      ※ ↓グラフと数値リストを表示するためのダミーのデータです
      ※ バックエンドとの連携後、削除してください
  -->
  <script type="text/javascript" src="{{asset('/js/flont/_sampleChartDataY.js')}}"></script>
  <!--
  ーーーーーーーーーーーーーーーーーーーー
  -->

  <!-- ライブラリ：ベース -->
  <script type="text/javascript" src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('/_vendors/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- 独自：IE対応 -->
  <script type="text/javascript" src="{{asset('/js/flont/common_forIE.js')}}"></script>
  
  <!-- 日付指定ピッカー関連 -->
  <!-- ライブラリ -->
  <script type="text/javascript" src="{{asset('/_vendors/flatpickr/js/flatpickr.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('/_vendors/flatpickr/js/l10ns/ja.js')}}"></script>
  <!-- 独自(ピッカーの描画) -->
  <script type="text/javascript" src="{{asset('/js/flont/chart_flatpickr_create.js')}}"></script>

  <!-- 独自(描画用ににデータを整形) -->
  <script type="text/javascript" src="{{asset('/js/flont/chart_allData_create.js')}}"></script>

  <!-- グラフ関連 -->
  <!-- ライブラリ -->
  <script type="text/javascript" src="{{asset('/_vendors/apexcharts/js/apexcharts.min.js')}}"></script>
  <!-- 独自(グラフの設定値) -->
  <script type="text/javascript" src="{{asset('/js/flont/chart_apexcharts_setting.js')}}"></script>
  <!-- 独自(グラフの描画設定) -->
  <script type="text/javascript" src="{{asset('/js/flont/chart_apexcharts_create.js')}}"></script>
  
  <!-- 数値リスト関連 -->
  <!-- 独自(数値リストの描画) -->
  <script type="text/javascript" src="{{asset('/js/flont/chart_tableList_create.js')}}"></script>

  <!-- 独自(ページ内のボタンなどの制御) -->
  <script type="text/javascript" src="{{asset('/js/flont/chart__custom.js')}}"></script>
  
  <!--
  ーーーーーーーーーーーーーーーーーーーー
      ※ ↓画面遷移をさせるダミーの機能です
      ※ バックエンドとの連携後、削除してください
  -->
  <script type="text/javascript">
    $(document).on("click", "#title_name", function(){
       location.href = "list.html"
    });
    $(document).on("click", "#logout", function(){
      location.href = "login.html"
    });
  </script>
  <!--
  ーーーーーーーーーーーーーーーーーーーー
  -->

</body>
</html>
