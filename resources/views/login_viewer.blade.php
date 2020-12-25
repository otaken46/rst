<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <title>ログイン｜RST結果ビューア</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- ライブラリ-->
  <link rel="stylesheet" href="{{ asset('_vendors/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('_vendors/fontawesome/css/all.min.css') }}">
  <!-- 独自 -->
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body class="bg_color p_login">
  <!-- IE対応 -->
  <p class="alert alert-warning alert-dismissible fade show forIE d-none" role="alert">
    このブラウザ (Internet Explorer) は本システムの推奨ブラウザではないため、一部機能が制限される可能性があります。<br>
    <a target="_blank"  style="color:white; text-decoration: underline;" href="https://www.google.com/intl/ja/chrome/">Google Chrome</a> または <a target="_blank"  style="color:white; text-decoration: underline;" href="https://www.microsoft.com/ja-jp/edge">Microsoft Edge</a> のご利用をお勧めいたします。
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true"><i class="fas fa-times"></i></span>
    </button>
  </p>
  
  <div class="wrapper text-center">
    <h1 class="d-block">RST解析結果表示システム</h1>
    <form method="post" name="login_viewer" class="mb-5" action="{{ url('/login_viewer')}}">
    {{ csrf_field() }}
      <div class="form-group text-left mb-4" id="login_id">
        <label for="inputID">ID / メールアドレス</label><span>※入力内容が正しくありません</span>
        <input type="text" id="inputID" name="inputID" class="form-control" placeholder="ID / メールアドレス" required>
      </div>
      <div class="form-group text-left" id="login_pass">
        <label for="inputPass">パスワード</label><span>※入力内容が正しくありません</span>
        <input type="password" id="inputPass" name="inputPass" class="form-control" placeholder="パスワード" required>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">ログイン</button>
    </form>
    <ul class="login_message text-left">
      <li class="mb-3">パスワードを忘れた場合は、院内のID管理者にお問い合わせください</li>
      <li>ログインに連続５回失敗するとログインできなくなります</li>
    </ul>
  </div>

  <!-- ライブラリ：ベース -->
  <script type="text/javascript" src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('/_vendors/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- 独自：IE対応 -->
  <script type="text/javascript" src="{{asset('/js/flont/common_forIE.js')}}"></script>

  <script type="text/javascript">
     $(document).ready(function(){
       $("#login_id, #login_pass").removeClass("login_error");
       $("#inputID").val('{{$id}}');
       $("#inputPass").val('{{$pass}}');
       if('{{$errors}}' == "id"){
         $("#login_id").addClass("login_error");
       }
       if('{{$errors}}' == "pass"){
         $("#login_pass").addClass("login_error");
       }
     });

  </script>
  
</body>
</html>