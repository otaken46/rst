@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('css/facility.css') }}">
<script src="{{asset('/js/jquery-3.5.0.min.js')}}"></script>
<script src="{{ asset('js/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
<script>
$(document).ready(function(){
    var click_flg = false;
    var regist_type = "new";
    var target_id = "";
    var facility_name = "";
    var facility_id = "";
    $('#facility_btn').on('click', function() {
        modal.style.display = 'block';
    });
    $('#facility_edit_btn').on('click', function() {
        var val = $("#facility_edit_btn").css('background-color');
        if(click_flg){
            var name = 
            $("#regist_facility_name").val(facility_name);
            $("#regist_facility_id").val(facility_id);
            $("#regist_btn").text('更新');
            modal.style.display = 'block';
            regist_type = "update";
        }else{
            $("#facility_edit_btn").css('outline','none');
        }
    });
    $('#cancel_btn').on('click', function() {
        modal.style.display = 'none';
        var error_message = document.getElementById("error_message");
        $('#facility_name').val('');
        $('#facility_id').val('');
        $("#regist_btn").text('登録');
        error_message.style.display = "none";
    });
    $('#facility_management_btn').on('click', function() {
        window.location.href = "{{ url('/facility_mng')}}"; 
    });
    $('#regist_btn').on('click', function() {
        var err = true;
        var reg = new RegExp(/[!"#$%&'()\*\+\-\.,\/:;<=>?@\[\\\]^_`{|}~]/g);
        var error_message = document.getElementById("error_message");
        facility_name = $('#regist_facility_name').val();
        facility_id = $('#regist_facility_id').val();
        if(facility_name == "" || (facility_name.match(/^[ 　\r\n\t]*$/)) || (reg.test(facility_name))){
            $("#error_message").text('入力してください。※記号入力不可');
            error_message.style.display = "inline";
            err = false;
        }
        if(facility_id == ""　&& (err)){
            $("#error_message").text('施設IDを入力してください。');
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
                url: "{{ action('FacilityController@regist') }}",
                type: 'POST',
                data:{'facility_name':facility_name,'facility_id':facility_id, 'regist_type':regist_type, 'target_id':target_id},
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
    $("#data tr").click(function() {
        var selected = $(this).hasClass("highlight");
        var val = $(this).closest('tr').find('input').val();
        $("#data tr").removeClass("highlight");
        if(!selected && (val != undefined)){
                $(this).addClass("highlight");
                $("#facility_edit_btn").css('background-color', '#4672c4');
                target_id = $(this).closest('tr').find('#target_id').val();
                facility_id = $(this).closest('tr').find('#facility_id').text();
                facility_name = $(this).closest('tr').find('#facility_name').text();
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
        <button class="btn1" id="facility_btn">施設登録</button>
        <button class="btn1" id="facility_management_btn">施設管理者登録</a>
        <button class="btn2 marginleft400" id="facility_edit_btn">編集</a>
    </div>
    <table border="1" id="data">
        <tr>
            <th class="width320" rowspan="2">施設名</th>
            <th class="width160" rowspan="2">施設ID</th>
            <th class="width120" rowspan="2">施設管理者</th>
            <th colspan="4">患者数</th>
        </tr>
        <tr>
            <td class="width100 paddingleft5">登録済み</td>
            <td class="width100 paddingleft5">設置済み</td>
            <td class="width100 paddingleft5">モニタ中</td>
            <td class="width100 paddingleft5">治療済み</td>
        </tr>
        <tbody>
            @php
                $cnt = 0;
                foreach ($facility as $val){
                    echo "<tr>";
                        echo "<td class = 'paddingleft10' id='facility_name'>" . $val->facility_name . "<input type='hidden' id='target_id' value=". $val->id . "></input></td>";
                        echo "<td class = 'paddingleft10' id='facility_id'>" . $val->facility_id . "</td>";
                        if(isset($val->mng_count)){
                            echo "<td class = 'textright'>" . $val->mng_count . "</td>";
                        }else{
                            echo "<td></td>";
                        }
                        if(isset($val->regist_status)){
                            echo "<td class = 'textright'>" . $val->regist_status . "</td>";
                        }else{
                            echo "<td></td>";
                        }
                        if(isset($val->setting_status)){
                            echo "<td class = 'textright'>" . $val->setting_status . "</td>";
                        }else{
                            echo "<td></td>";
                        }
                        if(isset($val->monitor_status)){
                            echo "<td class = 'textright'>" . $val->monitor_status . "</td>";
                        }else{
                            echo "<td></td>";
                        }
                        if(isset($val->treatment_status)){
                            echo "<td class = 'textright'>" . $val->treatment_status . "</td>";
                        }else{
                            echo "<td></td>";
                        }
                    echo "</tr>";
                    $cnt++;
                }
                while ($cnt < 15){
                    echo "<tr>";
                        $count = 0;
                        while ($count < 7){
                            echo "<td class = 'paddingleft10'></td>";
                            $count++;
                        }
                    echo "</tr>";
                    $cnt++;
                }
            @endphp
        </tbody>
    </table>
    </div>
    <div id="modal" class="modal">
        <div class="modal-content">
            <div align="center" class="paddingleft10 paddingtop10">
                <p>施設名<br>
                <input class="paddingleft10" type="text" id="regist_facility_name" maxlength='20' placeholder='施設名を入力してください'><br><br>
                施設ID<br>
                <input class="paddingleft10" type="text" id="regist_facility_id" maxlength='20' placeholder='施設IDを入力してください'><br>
                <span id="error_message"></span><br>
                </p>
                <button class="btn1" id="regist_btn">登録</button>
                <button class="btn1" id="cancel_btn">キャンセル</button>
            </div>
        </div>
    </div>
@endsection
