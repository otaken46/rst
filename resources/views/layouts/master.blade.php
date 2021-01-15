<!doctype html>
<html lang="ja">
<head>
  <!-- "{{config('const.version')}}" -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/master.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sort.css') }}">
　<link rel="stylesheet" href="{{ asset('_vendors/fontawesome/css/all.min.css') }}">
  <title>{{config('const.title')}}</title>
</head>
<body>
<script src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
<script src="{{ asset('js/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/function.js') }}"></script>
<script src="{{ asset('js/sort.js') }}"></script>
<script>
var ids = {};
var words = [];
var circles ={};
var click_flg = false;
var regist_flg = true;
var regist_type = "new";
var target_id = "";
$(document).ready(function(){
  $('#logout').on('click', function() {
        window.location.href = "{{ url('/logout')}}";
  });
});
</script>
  <div align="center">
    <div class="site-header page-name">
      <?php
        $uri = rtrim(url()->current(),'/');
        $uri = substr($uri, strrpos($uri, '/') + 1);
        if($uri =="facility"){
          echo config('const.title') . config('const.page_title.facility') . "<button class='btn_logout' id='logout'>" . config('const.btn.logout') . "</button>";
        }
        if($uri == "facility_mng"){
          echo config('const.title') . config('const.page_title.facility_mng') . "<button class='btn_logout' id='logout'>" . config('const.btn.logout') . "</button>";
        }
        if($uri == "viewer"){
          echo config('const.title') . config('const.page_title.viewer') . "<button class='btn_logout' id='logout'>" . config('const.btn.logout') . "</button>";
        }
        if($uri == "patient"){
          echo config('const.title') . config('const.page_title.patient') . "<button class='btn_logout' id='logout'>" . config('const.btn.logout') . "</button>";
        }
      ?>
    </div>
  </div>
  @yield('content')
</body>
</html>