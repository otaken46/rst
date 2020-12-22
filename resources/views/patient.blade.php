@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('css/patient.css') }}">
<link rel="stylesheet" href="{{ asset('css/sort.css') }}">
<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css">
<script src="{{ asset('js/sort.js') }}"></script>
<script>
$(document).ready(function(){
    var click_flg = false;
    var regist_type = "new";
    var target_id = "";
    var patient_name = "";
    var patient_id = "";
    var password = "";
    var regist_status = "";
    var setting_status = "";
    var monitor_status = "";
    var treatment_status = "";
    var doctor = "";
    var viewer = @json($patient);
    var facility = @json($facility);
    var facility_id = facility[0]['id'];
    $('#viewer_btn').on('click', function() {
        window.location.href = "{{ url('/viewer')}}";
    });
    $('#facility_delete_btn').on('click', function() {
        if(click_flg){
            $('#faclity_mng_name').text(patient_name +'{{config('const.text.delete')}}');
            delmodal.style.display = 'block';
            regist_type = "delete";
        }else{
            $("#facility_edit_btn").css('outline','none');
        }
    });
    $('#facility_edit_btn').on('click', function() {
        var val = $("#facility_edit_btn").css('background-color');
        if(click_flg){
            $("#regist_patient_name").val(patient_name);
            $("#regist_patient_id").val(patient_id);
            $("#regist_password").val(password);
            if(regist_status == "{{config('const.text.circle')}}"){
                $("#regist_regist_status").val('1');
            }else{
                $("#regist_regist_status").val('0');
            }
            if(setting_status == "{{config('const.text.circle')}}"){
                $("#regist_setting_status").val('1');

            }else{
                $("#regist_setting_status").val('0');
            }
            if(monitor_status == "{{config('const.text.circle')}}"){
                $("#regist_monitor_status").val('1');

            }else{
                $("#regist_monitor_status").val('0');
            }
            if(treatment_status == "{{config('const.text.circle')}}"){
                $("#regist_treatment_status").val('1');

            }else{
                $("#regist_treatment_status").val('0');
            }
            $("#regist_doctor").val(doctor);
            $("#regist_btn").text('更新');
            modal.style.display = 'block';
            regist_type = "update";
        }else{
            $("#facility_edit_btn").css('outline','none');
        }
    });
    $('#cancel_btn').on('click', function() {
        $("#regist_patient_name").val('');
        $("#regist_patient_id").val('');
        $("#regist_password").val('');
        $("#regist_regist_status").val('');
        $("#regist_setting_status").val('');
        $("#regist_monitor_status").val('');
        $("#regist_treatment_status").val('');
        $("#regist_doctor").val('');
        $("#regist_btn").text('登録');
        modal.style.display = 'none';
        var error_message = document.getElementById("error_message");
        error_message.style.display = "none";
    });
     $('#delete_cancel_btn').on('click', function() {
        delmodal.style.display = 'none';
    });
    $('#patient_btn').on('click', function() {
        modal.style.display = 'block';
    });
    $('#regist_btn').on('click', function() {
        var err = true;
        patient_name = $('#regist_patient_name').val();
        patient_id = $('#regist_patient_id').val();
        password = $('#regist_password').val();
        regist_status = $('#regist_regist_status').val();
        setting_status = $('#regist_setting_status').val();
        monitor_status = $('#regist_monitor_status').val();
        treatment_status = $('#regist_treatment_status').val();
        doctor = $('#regist_doctor').val();
        var reg = new RegExp(/[!"#$%&'()\*\+\-\.,\/:;<=>?@\[\\\]^_`{|}~]/g);
        if(patient_name == "" || (patient_name.match(/^[ 　\r\n\t]*$/)) || (reg.test(patient_name))){
            $("#error_message").text('患者名を入力してください。※記号入力不可');
            error_message.style.display = "inline";
            err = false;
        }
        if(patient_id == ""　&& (err)){
            $("#error_message").text('患者IDを入力してください。');
            error_message.style.display = "inline";
            err = false;
        }
        if(password == ""　&& (err)){
            $("#error_message").text('パスワードを入力してください。');
            error_message.style.display = "inline";
            err = false;
        }
        if(doctor == ""　&& (err)){
            $("#error_message").text('担当医を入力してください。');
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
                url: "{{ action('PatientController@regist') }}",
                type: 'POST',
                data:{
                    'patient_name':patient_name,
                    'patient_id':patient_id,
                    'password':password,
                    'regist_status':regist_status,
                    'setting_status':setting_status,
                    'monitor_status':monitor_status,
                    'treatment_status':treatment_status,
                    'doctor':doctor,
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
        patient_name = $(this).closest('tr').find('#patient_name').text();
        $("#data tr").removeClass("highlight");
        if(!selected && (patient_name != "")){
                $(this).addClass("highlight");
                $("#facility_edit_btn").css('background-color', '#4672c4');
                $("#facility_delete_btn").css('background-color', '#4672c4');
                target_id = $(this).closest('tr').find('#target_id').val();
                patient_id = $(this).closest('tr').find('#patient_id').val();
                password = $(this).closest('tr').find('#password').val();
                regist_status = $(this).closest('tr').find('#regist_status').text();
                setting_status = $(this).closest('tr').find('#setting_status').text();
                monitor_status = $(this).closest('tr').find('#monitor_status').text();
                treatment_status = $(this).closest('tr').find('#treatment_status').text();
                doctor = $(this).closest('tr').find('#doctor').text();
                click_flg = true;
        }else{
            $("#facility_edit_btn").css('background-color', '#a7a7a7');
            $("#facility_delete_btn").css('background-color', '#a7a7a7');
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
        <button class="btn3" id="facility_delete_btn">{{config('const.btn.delete')}}</button>
    </div>
    <div class="btn-area">
        <table class="width1024 ex_table">
        <td class="width400"><span class = "paddingleft10">施設名</span></td>
        <td class="width200"><span class = "paddingleft10">施設ID</span></td>
        <th class="width400"></th>
        <td class="width120"><span class = "paddingleft10">登録済み</span></td>
        <td class="width120"><span class = "paddingleft10">設置済み</span></td>
        <td class="width120"><span class = "paddingleft10">モニタ中</span></td>
        <td class="width120"><span class = "paddingleft10">治療済み</span></td>
        <tr>
            <td>
                <span class = "paddingleft10">{{$facility[0]['facility_name']}}</span>
            </td>
            <td>
                <span class = "paddingleft10">{{$facility[0]['facility_id']}}</span>
            </td>
            <td class="ex_border_e"></td>
            <td class = "textright">
                <span>{{$statuscount['regist_status']}}</span>
            </td>
            <td class = "textright">
                <span>{{$statuscount['setting_status']}}</span>
            </td>
            <td class = "textright">
                <span>{{$statuscount['monitor_status']}}</span>
            </td>
            <td class = "textright">
                <span>{{$statuscount['treatment_status']}}</span>
            </td>
        </tr>
        </table>
    </div>
    <table border="1" id="data" class="width1024 margintop30 sorttbl">
            <tr>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(1)')" class="width400">患者名 <i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(2)')" class="width250">登録年月日 <i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(3)')" class="width150">登録済み <i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(4)')" class="width150">設置済み <i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(5)')" class="width150">モニタ中 <i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(6)')" class="width150">治療済み <i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(7)')" class="width400">担当医 <i class="fa fa-sort"></i></th>
            </tr>
            @php
                $cnt = 0;
                foreach ($patient as $val){
                    echo "<tr class='item'>";
                        echo "<td class = 'paddingleft10' id='patient_name'>" . $val->patient_name . "</td>";
                        echo "<td class = 'paddingleft10' id='create_date'>" .  date('Y年m月d日',  strtotime($val->create_date)) . "</td>";
                        if($val->regist_status != 0){
                            echo "<td class = 'textcenter' id='regist_status' value=" . $val->regist_status . ">〇</td>";
                        }else{
                            echo "<td></td>";
                        }
                        if($val->setting_status != 0){
                            echo "<td class = 'textcenter' id='setting_status' value=" . $val->setting_status . ">〇</td>";
                        }else{
                            echo "<td></td>";
                        }
                        if($val->monitor_status != 0){
                            echo "<td class = 'textcenter' id='monitor_status' value=" . $val->monitor_status . ">〇</td>";
                        }else{
                            echo "<td></td>";
                        }
                        if($val->treatment_status != 0){
                            echo "<td class = 'textcenter' id='treatment_status' value=" . $val->treatment_status . ">〇</td>";
                        }else{
                            echo "<td></td>";
                        }
                        echo "<td class = 'paddingleft10' id='doctor'>" . $val->doctor . "</td>";
                        echo   "<input type='hidden' id='target_id' value=" . $val->id . ">
                             <input type='hidden' id='patient_id' value=" . $val->patient_id . ">
                             <input type='hidden' id='password' value=" . $val->password . ">";
                    echo "</tr>";
                    $cnt++;
                }
                while ($cnt < 15){
                    echo "<tr>";
                        $count = 0;
                        while ($count < 7){
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
                <table border=0 class="width490">
                    <tr>
                        <td class="paddingtop15">患者名</td>
                        <td class="paddingtop15">
                            <input class="paddingleft10" type="text" id="regist_patient_name" maxlength='20' placeholder='患者名を入力してください'>
                        </td>
                    </tr>
                    <tr>
                        <td class="paddingtop15">患者ID</td>
                        <td class="paddingtop15">
                            <input class="paddingleft10" type="text" id="regist_patient_id" maxlength='20' placeholder='患者者IDを入力してください'>
                        </td>
                    </tr>
                    <tr>
                        <td class="paddingtop15">パスワード</td>
                        <td class="paddingtop15">
                            <input class="paddingleft10" type="text" id="regist_password" maxlength='20' placeholder='パスワードを入力してください'>
                        </td>
                    </tr>
                    <tr>
                        <td class="paddingtop15">登録済み</td>
                        <td class="paddingtop15">
                            <select id="regist_regist_status">
                            <option value="0" selected>なし</option>
                            <option value="1" >〇</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="paddingtop15">設置済み</td>
                        <td class="paddingtop15">
                            <select id="regist_setting_status">
                            <option value="0" selected>なし</option>
                            <option value="1" >〇</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="paddingtop15">モニタ中</td>
                        <td class="paddingtop15">
                            <select id="regist_monitor_status">
                            <option value="0" selected>なし</option>
                            <option value="1" >〇</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="paddingtop15">治療済み</td>
                        <td class="paddingtop15">
                            <select id="regist_treatment_status">
                                    <option value="0" selected>なし</option>
                                    <option value="1" >〇</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="paddingtop15">担当医</td>
                        <td class="paddingtop15">
                            <input class="paddingleft10" type="text" id="regist_doctor" maxlength='20' placeholder='担当医を入力してください'>
                        </td>
                    </tr>
                </table><br>
            <span id="error_message"></span><br>
            <button class="btn1" id="regist_btn">登録</button>
            <button class="btn1" id="cancel_btn">キャンセル</button>
            </div>
        </div>
    </div>
    <div id="delmodal" class="delmodal">
        <div class="delmodal-content paddingtop10">
             <div align="center" class="paddingleft10">
                <p>
                <span id="faclity_mng_name"></span><br>
                </p>
                <button class="btn1" id="delete_btn">{{config('const.btn.delete')}}</button>
                <button class="btn1" id="delete_cancel_btn">{{config('const.btn.cancel')}}</button>
            </div>
        </div>
    </div>
@endsection
