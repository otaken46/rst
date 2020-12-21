@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('css/viewer.css') }}">
<script src="{{asset('/js/jquery-3.5.0.min.js')}}"></script>
<script src="{{ asset('js/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
<script>
$(document).ready(function(){
    var click_flg = false;
    var regist_type = "new";
    var target_id = "";
    var viewer_name = "";
    var viewer_id = "";
    var password = "";
    var contact = "";
    var mail_address = "";
    var viewer = @json($viewer);
    var facility = @json($facility);
    var facility_id = facility[0]['id'];
    $('#viewer_btn').on('click', function() {
        modal.style.display = 'block';
    });
    $('#viewer_name').change(function() {
        var count = 0;
        var contact_str = "";
        var str = "";
        facility_id = $(this).val();
        target_id  = $(this).val();
        $('table#data tr').remove();
        str = '<tr><th class="width200">施設管理者名</th>';
        str += '<th class="width150">施設管理者ID</th>';
        str += '<th class="width150">パスワード</th>';
        str += '<th class="width120">連絡担当</th>';
        str += '<th class="width180">eメールアドレス</th></tr>';
        $("#data").append(str);
        $.each(viewer, function(key, value) {            
            if(facility_id == value['facility_id']){
                str = "";
                str += "<tr>";
                str += "   <td class = 'paddingleft10' id='viewer_name'>" + value['viewer_name'] +"<input type='hidden' id='target_id' value=" +value['id'] +"></td>";
                str += "   <td class = 'paddingleft10' id='viewer_id'>" + value['viewer_id'] +"</td>";
                str += "   <td class = 'paddingleft10' id='password'>" + value['password'] +"</td>";
                if(value['contact'] == 1){contact_str = "〇";}
                str += "   <td class = 'paddingleft10' id='contact'>" + contact_str +"</td>";
                str += "   <td class = 'paddingleft10' id='mail_address'>" + value['mail_address'] +"</td>";
                str += "</tr>";
                $("#data").append(str);
                count += 1;
            }
        });
        for( ; count < 15; count++){
            str = "<tr><td></td><td></td><td></td><td></td><td></td></tr>";
            $("#data").append(str);
        }
        $("#facility_edit_btn").css('background-color', '#a7a7a7');
    });
    $('#facility_edit_btn').on('click', function() {
        var val = $("#facility_edit_btn").css('background-color');
        if(click_flg){
            $("#regist_viewer_name").val(viewer_name);
            $("#regist_viewer_id").val(viewer_id);
            $("#regist_password").val(password);
            $("#regist_mail_address").val(mail_address);
            $("#regist_btn").text('更新');
            modal.style.display = 'block';
            regist_type = "update";
        }else{
            $("#facility_edit_btn").css('outline','none');
        }
    });
    $('#cancel_btn').on('click', function() {
        $("#regist_viewer_name").val('');
        $("#regist_viewer_id").val('');
        $("#regist_password").val('');
        $("#regist_mail_address").val('');
        $("#regist_btn").text('登録');
        modal.style.display = 'none';
        var error_message = document.getElementById("error_message");
        error_message.style.display = "none";
    });
    $('#patient_btn').on('click', function() {
        window.location.href = "{{ url('/patient')}}";
    });
    $('#regist_btn').on('click', function() {
        var err = true;
        viewer_name = $('#regist_viewer_name').val();
        viewer_id = $('#regist_viewer_id').val();
        password = $('#regist_password').val();
        mail_address = $('#regist_mail_address').val();
        var reg = new RegExp(/[!"#$%&'()\*\+\-\.,\/:;<=>?@\[\\\]^_`{|}~]/g);
        if(viewer_name == "" || (viewer_name.match(/^[ 　\r\n\t]*$/)) || (reg.test(viewer_name))){
            $("#error_message").text('閲覧者名を入力してください。※記号入力不可');
            error_message.style.display = "inline";
            err = false;
        }
        if(viewer_id == ""　&& (err)){
            $("#error_message").text('閲覧者IDを入力してください。');
            error_message.style.display = "inline";
            err = false;
        }
        if(password == ""　&& (err)){
            $("#error_message").text('パスワードを入力してください。');
            error_message.style.display = "inline";
            err = false;
        }
        if(mail_address == ""　&& (err)){
            $("#error_message").text('eメールアドレスを入力してください。');
            error_message.style.display = "inline";
            err = false;
        }
        if(err){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ action('ViewerController@regist') }}",
                type: 'POST',
                data:{
                    'viewer_name':viewer_name,
                    'viewer_id':viewer_id,
                    'password':password,
                    'mail_address':mail_address,
                    'regist_type':regist_type,
                    'facility_id':facility_id,
                    'target_id':target_id},
                dataType:'json'
            })
            // Ajaxリクエストが成功した場合
            .done(function(data) {
                if (data.result == "OK") {
                    location.reload();
                }
            })
            // Ajaxリクエストが失敗した場合
            .fail(function(data) {
                alert("接続失敗");
            });
        }
    });
    $(document).on('click','#data tr', function() {
        var selected = $(this).hasClass("highlight");
        viewer_id = $(this).closest('tr').find('#viewer_id').text();
        $("#data tr").removeClass("highlight");
        if(!selected && (viewer_id != "")){
                $(this).addClass("highlight");
                $("#facility_edit_btn").css('background-color', '#4672c4');
                target_id = $(this).closest('tr').find('#target_id').val();
                viewer_name = $(this).closest('tr').find('#viewer_name').text();
                password = $(this).closest('tr').find('#password').val();
                contact = $(this).closest('tr').find('#contact').text();
                mail_address = $(this).closest('tr').find('#mail_address').text();
                click_flg = true;
        }else{
            $("#facility_edit_btn").css('background-color', '#a7a7a7');
            click_flg = false;
        }
    });
});
</script>
    <div align="center">
    <div class="btn-area">
        <button class="btn1" id="viewer_btn">閲覧者登録</button>
        <button class="btn1" id="patient_btn">患者登録</button>
        <button class="btn2" id="facility_edit_btn">編集</button>
    </div>
    <div class="btn-area">
        <table border="1" class="width400">
        <th class="width200 paddingleft10">施設名</th>
        <th class="width200 paddingleft10">施設ID</th>
        <tr>
            <td>
                <span class = "paddingleft10">{{$facility[0]['facility_name']}}</span>
            </td>
            <td>
                <span class = "paddingleft10">{{$facility[0]['facility_id']}}</span>
            </td>
        </tr>
        </table>
    </div>
    <table border="1" id="data" class="margintop30 width1024">
            <tr>
                <th class="width200">閲覧者名</th>
                <th class="width150">閲覧者ID</th>
                <th class="width180">eメールアドレス</th>
            </tr>
            @php
                $cnt = 0;
                foreach ($viewer as $val){
                    echo "<tr>";
                        echo "<td class = 'paddingleft10' id='viewer_name'>" . $val->viewer_name . "<input type='hidden' id='target_id' value=" . $val->id . "><input type='hidden' id='password' value=" . $val->password . "></td>";
                        echo "<td class = 'paddingleft10' id='viewer_id'>" . $val->viewer_id . "</td>";
                        echo "<td class = 'paddingleft10' id='mail_address'>" . $val->mail_address . "</td>";
                    echo "</tr>";
                    $cnt++;
                }
                while ($cnt < 15){
                    echo "<tr>";
                        $count = 0;
                        while ($count < 3){
                            echo "<td></td>";
                            $count++;
                        }
                    echo "</tr>";
                    $cnt++;
                }
            @endphp
    </table>
    </div>
    <div id="modal" class="modal">
        <div class="modal-content paddingtop10">
             <div align="center" class="paddingleft10">
                <p>閲覧者名<br>
                <input class="paddingleft10" type="text" id="regist_viewer_name" maxlength='20' placeholder='閲覧者名を入力してください'><br><br>
                閲覧者ID<br>
                <input class="paddingleft10" type="text" id="regist_viewer_id" maxlength='20' placeholder='閲覧者IDを入力してください'><br><br>
                パスワード<br>
                <input class="paddingleft10" type="text" id="regist_password" maxlength='20' placeholder='パスワードを入力してください'><br><br>
                eメールアドレス<br>
                <input class="paddingleft10" type="text" id="regist_mail_address" maxlength='256' placeholder='eメールアドレスを入力してください'><br>
                <span id="error_message"></span><br>
                </p>
                <button class="btn1" id="regist_btn">登録</button>
                <button class="btn1" id="cancel_btn">キャンセル</button>
            </div>
        </div>
    </div>
@endsection
