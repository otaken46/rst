@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('css/facility.css') }}">
<script>
$(document).ready(function(){
    var facility_name = "";
    var facility_id = "";
    $('#facility_btn').on('click', function() {
        if('{{$facility_count}}' == '{{config('const.max_facility')}}'){
            regist_flg = false;
            $('#result').text('{{$facility_count}}{{config('const.msg.err_005')}}');
            resultmodal.style.display = 'block';
        }else{
            modal.style.display = 'block';
        }
    });
    $('#edit_btn').on('click', function() {
        ids = {'regist_facility_name':facility_name, 'regist_facility_id':facility_id};
        words = ['{{config('const.btn.update')}}', '{{config('const.text.circle')}}'];
        var type = edit_btn_click(click_flg,ids,words);
        if(type){
            regist_type = "update";
        }
    });
    $('#delete_btn').on('click', function() {
        if(click_flg){
            $('#faclity_name').text(facility_name +'{{config('const.text.delete')}}');
            delmodal.style.display = 'block';
            regist_type = "delete";
        }else{
            $("#edit_btn").css('outline','none');
        }
    });
    $('#cancel_btn').on('click', function() {
        modal.style.display = 'none';
        var error_message = document.getElementById("error_message");
        $('#regist_facility_name').val('');
        $('#regist_facility_id').val('');
        $("#regist_btn").text('{{config('const.btn.regist')}}');
        error_message.style.display = "none";
    });
    $('#delete_cancel_btn').on('click', function() {
        delmodal.style.display = 'none';
    })
    $('#facility_management_btn').on('click', function() {
        window.location.href = "{{ url('/facility_mng')}}"; 
    });
    $('#regist_btn,#delete_exe_btn').on('click', function() {
        // 連打対策
        if(regist_flg){
            regist_flg = false;
            var err = true;
            if(regist_type != "delete"){
                facility_name = $('#regist_facility_name').val();
                facility_id = $('#regist_facility_id').val();
                err = data_check("facility_name", facility_name, '{{config('const.label.facility_name')}}{{config('const.text.input')}}{{config('const.msg.symbol')}}');
                if(err){err = data_check("id_pass", facility_id, '{{config('const.label.facility_id')}}{{config('const.msg.err_004')}}');}
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
    $("#data tr").click(function() {
        ids = {'target_id':'val', 'facility_id':'txt', 'facility_name':'txt'};
        var arr_select = select_data(this,ids);
        target_id = arr_select['target_id'];
        facility_id = arr_select['facility_id'];
        facility_name = arr_select['facility_name'];
        click_flg = arr_select['click_flg'];
    });
});
</script>
    <div align="center">
    <div class="btn-area">
        <button class="btn1" id="facility_btn">{{config('const.btn.facility')}}</button>
        <button class="btn1" id="facility_management_btn">{{config('const.btn.facility_mng')}}</button>
        <button class="btn2" id="edit_btn">{{config('const.btn.edit')}}</button>
        <button class="btn3" id="delete_btn">{{config('const.btn.delete')}}</button>
    </div>
    <table border="1" id="data" class="sorttbl">
        <tr>
            <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(1)')" class="width320 tbl-heder" rowspan="2">{{config('const.label.facility_name')}}<i class="fa fa-sort"></i></th>
            <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(2)')" class="width160 tbl-heder" rowspan="2">{{config('const.label.facility_id')}}<i class="fa fa-sort"></i></th>
            <td class="width120 tbl-heder" rowspan="2">{{config('const.label.facility_manager')}}</td>
            <td class="tbl-heder" colspan="4">{{config('const.label.patient_count')}}</td>
        </tr>
        </tr> 
            <td class="width100 paddingleft5 tbl-heder">{{config('const.label.regist_status')}}</td>
            <td class="width100 paddingleft5 tbl-heder">{{config('const.label.setting_status')}}</td>
            <td class="width100 paddingleft5 tbl-heder">{{config('const.label.monitor_status')}}</td>
            <td class="width100 paddingleft5 tbl-heder">{{config('const.label.treatment_status')}}</td>
        </tr>
            @php
                $cnt = 0;
                foreach ($facility as $val){
                    echo "<tr class='item'>";
                        echo "<td class = 'paddingleft10' id='facility_name'>" . $val->facility_name ."</td>";
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
                        echo "<input type='hidden' id='target_id' value=". $val->id . "></input>";
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
    </table>
    </div>
    <div id="modal" class="modal">
        <div class="modal-content">
            <div align="center" class="paddingleft10 paddingtop10">
                <p>{{config('const.label.facility_name')}}<br>
                <input class="paddingleft10" type="text" id="regist_facility_name" maxlength='20' placeholder='{{config('const.label.facility_name')}}{{config('const.text.input')}}'><br><br>
                {{config('const.label.facility_id')}}<br>
                <input class="paddingleft10" type="text" id="regist_facility_id" maxlength='20' placeholder='{{config('const.label.facility_id')}}{{config('const.text.input')}}'><br>
                <span id="error_message"></span><br>
                </p>
                <button class="btn1" id="regist_btn">{{config('const.btn.regist')}}</button>
                <button class="btn1" id="cancel_btn">{{config('const.btn.cancel')}}</button>
            </div>
        </div>
    </div>
    <div id="delmodal" class="delmodal">
        <div class="delmodal-content paddingtop10">
             <div align="center" class="paddingleft10">
                <p>
                <span id="faclity_name"></span><br>
                </p>
                <button class="btn1" id="delete_exe_btn">{{config('const.btn.delete')}}</button>
                <button class="btn1" id="delete_cancel_btn">{{config('const.btn.cancel')}}</button>
            </div>
        </div>
    </div>
    <div id="resultmodal" class="resultmodal">
        <div class="resultmodal-content paddingtop10">
             <div align="center" class="paddingleft10">
                <p>
                <span id="result"></span><br>
                </p>
                <button class="btn1" id="result_btn">OK</button>
            </div>
        </div>
    </div>
@endsection
