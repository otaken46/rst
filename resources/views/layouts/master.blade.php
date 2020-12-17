<!doctype html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/master.css') }}">
    <title>RSTモニタシステム</title>
</head>
<body>
  <div align="center">
    <div class="site-header">
      <?php
        $uri = rtrim(url()->current(),'/');
        $uri = substr($uri, strrpos($uri, '/') + 1);
        if($uri =="facility"){
          echo "<p class='page-name'>RSTモニタシステム(施設登録)</p>";
        }
        if($uri == "facility_mng"){
          echo "<p class='page-name'>RSTモニタシステム(施設管理者登録)</p>";
        }
      ?>
    </div>
  </div>
  @yield('content')
</body>
</html>