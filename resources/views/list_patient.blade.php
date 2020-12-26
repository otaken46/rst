<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>患者リスト｜RST結果ビューア</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- ライブラリ-->
  <link rel="stylesheet" href="{{ asset('_vendors/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('_vendors/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('_vendors/DataTables/css/dataTables.bootstrap4.min.css') }}">
  <!-- 独自 -->
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="bg_color p_list">
  <nav class="navbar fixed-top navbar-expand-sm navbar-light bg-light justify-content-between">
    <a id="title_name" href="#">RST解析結果表示システム</a>
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

  <div class="wrapper">
    <table id="list_table" class="table table-hover">
      <thead>
        <tr>
          <th scope="col" class="list_col_s">患者ID</th>
          <th scope="col" class="list_col_l">患者名</th>
          <th scope="col" class="list_col_m">最終更新</th>
          <th scope="col" class="list_col_s">RST</th>
          <th scope="col" class="list_col_s">心拍数</th>
          <th scope="col" class="list_col_s">呼吸数</th>
          <th scope="col" class="list_col_m">CSRグレード</th>
          <th scope="col" class="list_col_s">臥床時間</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
  
  <!--
  ーーーーーーーーーーーーーーーーーーーー
      ※ ↓リストを表示するためのダミーのデータです
      ※ バックエンドとの連携後、削除してください
  
  <script type="text/javascript" src="{{asset('/js/flont/_sampleUserDataList.js')}}"></script>-->
    <!--
  ーーーーーーーーーーーーーーーーーーーー
-->
  <script type="text/javascript">
  var data = @json($list_patient);
  var flag_pink = [5, 9, 14, 18, 22];
  var flag_yellow = [7, 15, 20];
  </script>
  <!-- ライブラリ：ベース -->
  <script type="text/javascript" src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('/_vendors/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- 独自：IE対応 -->
  <script type="text/javascript" src="{{asset('/js/flont/common_forIE.js')}}"></script>

  <!-- テーブル作成関連 -->
  <!-- ライブラリ -->
  <script type="text/javascript" src="{{asset('/_vendors/DataTables/js/jquery.dataTables.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('/_vendors/DataTables/js/dataTables.bootstrap4.min.js')}}"></script>
  <!-- 独自(テーブル作成) -->
  <script type="text/javascript" src="{{asset('/js/flont/list_datatables_create.js')}}"></script>
  
  <!--
  ーーーーーーーーーーーーーーーーーーーー
      ※ ↓画面遷移をさせるダミーの機能です
      ※ バックエンドとの連携後、削除してください
  -->
  <script type="text/javascript">
     $(document).ready(function(){
       $("#list_userName").text('{{$facility_name}}');
     });
     $(document).on("click", ".list_row", function(){
       location.href = "chart.html"
     });
     $(document).on("click", "#logout", function(){
       window.location.href = "{{ url('/logout_viewer')}}";
     });
  </script>
  <!--
  ーーーーーーーーーーーーーーーーーーーー
  -->
  
</body>
</html>