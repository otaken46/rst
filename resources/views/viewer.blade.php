@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('css/viewer.css') }}">
<script>
$(document).ready(function(){
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
    $('#edit_btn').on('click', function() {
        ids = {'regist_viewer_name':viewer_name,
            'regist_viewer_id':viewer_id,
            'regist_password':password,
            'regist_mail_address':mail_address};
        words = ['{{config('const.btn.update')}}'];
        var type = edit_btn_click(click_flg, ids, words,"");
        if(type){
            $("#regist_viewer_id").prop('disabled', true);
            regist_type = "update";
        }
    });
    $('#delete_btn').on('click', function() {
        if(click_flg){
            $('#faclity_mng_name').text(viewer_name +'{{config('const.text.delete')}}');
            delmodal.style.display = 'block';
            regist_type = "delete";
        }else{
            $("#edit_btn").css('outline','none');
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
        $("#regist_viewer_id").prop('disabled', false);
        error_message.style.display = "none";
    });
    $('#delete_cancel_btn').on('click', function() {
        delmodal.style.display = 'none';
    });
    $('#patient_btn').on('click', function() {
        window.location.href = "{{ url('/patient')}}";
    });
    $('#regist_btn,#delete_exe_btn').on('click', function() {
        // 連打対策
        if(regist_flg){
            regist_flg = false;
            var err = true;
            if(regist_type != "delete"){
                viewer_name = $('#regist_viewer_name').val();
                viewer_id = $('#regist_viewer_id').val();
                password = $('#regist_password').val();
                mail_address = $('#regist_mail_address').val();
                err = data_check("name", viewer_name, '{{config('const.label.viewer_name')}}{{config('const.msg.err_003')}}');
                if(err){err = data_check("id_pass", viewer_id, '{{config('const.label.viewer_id')}}{{config('const.msg.err_004')}}');}
                if(err){err = data_check("pass", password, '{{config('const.label.password')}}{{config('const.msg.err_007')}}');}
                if(err){err = data_check("mail", mail_address, '{{config('const.label.mail_address')}}{{config('const.msg.err_008')}}');}
            }
            if(err){
                error_message.style.display = "none";
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
                        regist_flg = true;
                    }
                    $('#result').text(data.message);
                    resultmodal.style.display = 'block';
                })
                // Ajaxリクエストが失敗した場合
                .fail(function(data) {
                    regist_flg = false;
                    $('#result').text('{{config('const.result.ACCESS_NG')}}');
                    resultmodal.style.display = 'block';
                });
            }else{
                regist_flg = true;
            }
        }
    });
    $("#result_btn").click(function() {
        if(regist_flg){
            location.reload();
        }else{
            regist_flg = true;
            resultmodal.style.display = 'none';
        }
    });
    $(document).on('click','#data tr', function() {
        ids = {'target_id':'val', 'viewer_name':'txt', 'viewer_id':'txt', 'password':'val', 'contact':'txt', 'mail_address':'txt'};
        var arr_select = select_data(this,ids);
        target_id = arr_select['target_id'];
        viewer_name = arr_select['viewer_name'];
        viewer_id = arr_select['viewer_id'];
        password = arr_select['password'];
        contact = arr_select['contact'];
        mail_address = arr_select['mail_address'];
        click_flg = arr_select['click_flg'];
    });
});
</script>
    <div align="center">
    <div class="btn-area">
        <button class="btn1" id="viewer_btn">閲覧者登録</button>
        <button class="btn1" id="patient_btn">患者登録</button>
        <button class="btn2" id="edit_btn">編集</button>
        <button class="btn3" id="delete_btn">{{config('const.btn.delete')}}</button>
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
    <table border="1" id="data" class="margintop30 width1024 sorttbl">
            <tr>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(1)')" class="width200 tbl-heder">閲覧者名<i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(1)')" class="width150 tbl-heder">閲覧者ID<i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(1)')" class="width180 tbl-heder">eメールアドレス<i class="fa fa-sort"></i></th>
            </tr>
            @php
                $cnt = 0;
                foreach ($viewer as $val){
                    echo "<tr class='item'>";
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
                </p><br>
                <button class="btn1" id="regist_btn">登録</button>
                <button class="btn1" id="cancel_btn">キャンセル</button>
            </div><br>
        </div>
    </div>
    <div id="delmodal" class="delmodal">
        <div class="delmodal-content paddingtop10">
             <div align="center" class="paddingleft10">
                <p class="paddingtop10">
                <span id="faclity_mng_name"></span>
                </p><br>
                <button class="btn1" id="delete_exe_btn">{{config('const.btn.delete')}}</button>
                <button class="btn1" id="delete_cancel_btn">{{config('const.btn.cancel')}}</button>
            </div>
        </div>
    </div>
    <div id="resultmodal" class="resultmodal">
        <div class="resultmodal-content paddingtop10">
             <div align="center" class="paddingleft10">
                <p class="paddingtop10">
                <span id="result"></span>
                </p><br>
                <button class="btn1" id="result_btn">OK</button>
            </div>
        </div>
    </div>
@endsection
