<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- "{{config('const.version')}}" -->
        <meta charset="utf-8">
        <title>COVID-19患者用RSTモニタリング（登録）</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}"> 
        <!-- ライブラリ-->
        <link rel="stylesheet" href="{{ asset('_vendors/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('_vendors/fontawesome/css/all.min.css') }}">
        <!-- 独自 -->
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    </head>
    <body>
    <!-- IE対応 -->
    <p class="alert alert-warning alert-dismissible fade show forIE d-none" role="alert" style="z-index: 1000;">
        このブラウザ (Internet Explorer) は本システムの推奨ブラウザではないため、一部機能が制限される可能性があります。<br>
        <a target="_blank"  style="color:white; text-decoration: underline;" href="https://www.google.com/intl/ja/chrome/">Google Chrome</a> または <a target="_blank"  style="color:white; text-decoration: underline;" href="https://www.microsoft.com/ja-jp/edge">Microsoft Edge</a> のご利用をお勧めいたします。
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>
    </p>
    <form action="{{ url('/login_facility')}}" method="POST">
    {{ csrf_field() }}
        <div  align="center">
            <div class="width1028">
                <h1><span>COVID-19患者用RSTモニタリング（登録）</span></h1>
                <table border="0" class="layout-fixed">
                <tr>
                    <th class="textleft">ID</th>
                </tr>
                <tr>
                    <td><input name="id" placeholder="ID" type="text"@if($id<>"") value={{$id}} @endif /></td>
                </tr>
                <tr>
                    <th class="textleft">パスワード</th>
                </tr>
                <tr>
                    <td><input name="pass" placeholder="パスワード" type="password" @if($pass<>"") value={{$pass}} @endif/></td>
                </tr>
                <tr>
                    <td>
                        @if($errors<>"")
                            {{$errors}}
                        @endif
                    <td><br>
                </tr>
                </table><br>
                <table border="0" class="layout-fixed">
                <tr>
                    <th class="textleft">・パスワードを忘れた場合は、施設管理者にお問い合わせください</th>
                </tr>
                <tr>
                    <th class="textleft">・ログインに連続５回失敗するとログインできなくなります</th>
                </tr>
                </table><br>
            </div>
                <button class="btn1">ログイン</button>
        </div>
    </form>
     <!-- ライブラリ：ベース -->
    <script type="text/javascript" src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/_vendors/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <!-- 独自：IE対応 -->
    <script type="text/javascript" src="{{asset('/js/flont/common_forIE.js')}}"></script>
    </body>
</html>