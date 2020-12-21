<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
        <title>RTS</title>
    </head>
    <body>
    <form action="{{ url('/login_facility')}}" method="POST">
    {{ csrf_field() }}
        <div  align="center">
            <div class="width1028">
                <h1><span>RSTモニタシステム(登録)</span></h1>
                <table border="0">
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
                    <td class="textcenter"><button class="btn1">ログイン</button></td>
                </tr>
                </table>
            </div>
        </div>
        @if($errors<>"")
            {{$errors}}
        @endif
    </form>
    </body>
</html>