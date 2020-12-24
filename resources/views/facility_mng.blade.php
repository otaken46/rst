@extends('layouts.master')
@section('content')
<link rel="stylesheet" href="{{ asset('css/facility_mng.css') }}">
<script>
$(document).ready(function(){
    var facility_id = "";
    var facility_manager_name = "";
    var facility_manager_id = "";
    var password = "";
    var contact = "";
    var mail_address = "";
    var facility_mng = @json($facility_mng);
    $('#facility_btn').on('click', function() {
        window.location.href = "{{ url('/facility')}}";
    });
    $('#facility_name').change(function() {
        var count = 0;
        var contact_str = "";
        var str = "";
        facility_id = $(this).val();
        target_id  = $(this).val();
        $('table#data tr').remove();
        str = '<tr><th class="width300">{{config('const.label.facility_manager_name')}}</th>';
        str += '<th class="width230">{{config('const.label.facility_manager_id')}}</th>';
        str += '<th class="width94">{{config('const.label.contact')}}</th>';
        str += '<th class="width400">{{config('const.label.mail_address')}}</th></tr>';
        $("#data").append(str);
        $.each(facility_mng, function(key, value) {            
            if(facility_id == value['facility_id']){
                str = "";
                str += "<tr>";
                str += "   <td class = 'paddingleft10' id='facility_manager_name'>" + value['facility_manager_name'] +"<input type='hidden' id='target_id' value=" +value['id'] +"><input type='hidden' id='password' value=" +value['password'] +"></td>";
                str += "   <td class = 'paddingleft10' id='facility_manager_id'>" + value['facility_manager_id'] +"</td>";
                if(value['contact'] == 1){contact_str = "〇";}
                str += "   <td class = 'textcenter' id='contact'>" + contact_str +"</td>";
                str += "   <td class = 'paddingleft10' id='mail_address'>" + value['mail_address'] +"</td>";
                str += "</tr>";
                $("#data").append(str);
                count += 1;
            }
        });
        for( ; count < 15; count++){
            str = "<tr><td></td><td></td><td></td><td></td></tr>";
            $("#data").append(str);
        }
        $("#edit_btn").css('background-color', '#a7a7a7');
    });
    $('#edit_btn').on('click', function() {
        ids = {'regist_facility_manager_name':facility_manager_name,
            'regist_facility_manager_id':facility_manager_id,
            'regist_password':password,
            'regist_mail_address':mail_address};
        circles = {'regist_contact':contact}
        words = ['{{config('const.btn.update')}}', '{{config('const.text.circle')}}'];
        var type = edit_btn_click(click_flg, ids, words, circles);
        if(type){
            facility_name = $('#facility_name option:selected').text();
            $('#faclity_name_val').text(facility_name);
            regist_type = "update";
        }
    });
    $('#delete_btn').on('click', function() {
        if(click_flg){
            $('#faclity_mng_name').text(facility_manager_name +'{{config('const.text.delete')}}');
            delmodal.style.display = 'block';
            regist_type = "delete";
        }else{
            $("#edit_btn").css('outline','none');
        }
    });
    $('#cancel_btn').on('click', function() {
        modal.style.display = 'none';
        var error_message = document.getElementById("error_message");
        $("#regist_facility_manager_name").val('');
        $("#regist_facility_manager_id").val('');
        $("#regist_password").val('');
        $("#regist_contact").val('0');
        $("#regist_mail_address").val('');
        $("#regist_btn").text('{{config('const.btn.regist')}}');
        error_message.style.display = "none";
    });
    $('#delete_cancel_btn').on('click', function() {
        delmodal.style.display = 'none';
    });
    $('#facility_management_btn').on('click', function() {
        faclity_name = "{{config('const.label.facility_name_id')}}" + $('#facility_name option:selected').text();
        $('#faclity_name_val').text(faclity_name);
        facility_id = $('#facility_name option:selected').val();
        modal.style.display = 'block';
    });
    $('#regist_btn').on('click', function() {
        if(regist_flg){
            regist_flg = false;
            var err = true;
            facility_id = $('#facility_name option:selected').val();
            facility_manager_name = $('#regist_facility_manager_name').val();
            facility_manager_id = $('#regist_facility_manager_id').val();
            password = $('#regist_password').val();
            contact = $('#regist_contact').val();
            mail_address = $('#regist_mail_address').val();
            err = data_check("name", facility_manager_name, '{{config('const.label.facility_manager_name')}}{{config('const.msg.err_003')}}');
            if(err){err = data_check("id_pass", facility_manager_id, '{{config('const.label.facility_manager_id')}}{{config('const.msg.err_004')}}');}
            if(err){err = data_check("id_pass", password, '{{config('const.label.password')}}{{config('const.text.input')}}');}
            if(err){err = data_check("mail", mail_address, '{{config('const.label.mail_address')}}{{config('const.text.input')}}');}
            if(err){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ action('FacilityMngController@regist') }}",
                    type: 'POST',
                    data:{
                        'facility_manager_name':facility_manager_name,
                        'facility_manager_id':facility_manager_id,
                        'password':password,'contact':contact,
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
                        location.reload();
                    }
                })
                // Ajaxリクエストが失敗した場合
                .fail(function(data) {
                    alert("接続失敗");
                    regist_flg = true;
                });
            }else{
                regist_flg = true;
            }
        }
    });
    $('#delete_btn').on('click', function() {
        if(regist_flg){
            regist_flg = false;
            if(target_id !=""){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ action('FacilityMngController@regist') }}",
                    type: 'POST',
                    data:{
                        'facility_manager_name':"",
                        'facility_manager_id':"",
                        'password':"",
                        'contact':"",
                        'mail_address':"",
                        'regist_type':regist_type,
                        'facility_id':"",
                        'target_id':target_id},
                    dataType:'json'
                })
                // Ajaxリクエストが成功した場合
                .done(function(data) {
                    if (data.result == "OK") {
                        regist_flg = true;
                        location.reload();
                    }
                })
                // Ajaxリクエストが失敗した場合
                .fail(function(data) {
                    alert("接続失敗");
                    regist_flg = true;
                });
            }else{
                regist_flg = true;
            }
        }
    });
    $(document).on('click','#data tr', function() {
        ids = {'target_id':'val', 'facility_manager_name':'txt', 'facility_manager_id':'txt', 'password':'val', 'contact':'txt', 'mail_address':'txt'};
        var arr_select = select_data(this,ids);
        target_id = arr_select['target_id'];
        facility_manager_name = arr_select['facility_manager_name'];
        facility_manager_id = arr_select['facility_manager_id'];
        password = arr_select['password'];
        contact = arr_select['contact'];
        mail_address = arr_select['mail_address'];
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
    <div>
        <span>{{config('const.label.facility_name')}}</span>
        @if(isset($facility[0]->id))
            <select id="facility_name">
                @foreach ($facility as $val)
                    <option value="{{$val->id}}">{{$val->facility_name}}:{{$val->facility_id}}</option>
                @endforeach
            </select>
        @else
            <select>
                <option>{{config('const.msg.facility_regist')}}</option>
            </select>
        @endif
    </div>
    <table border="1" id="data" class="layout-fixed">
            <tr>
                <th class="width300">{{config('const.label.facility_manager_name')}}</th>
                <th class="width230">{{config('const.label.facility_manager_id')}}</th>
                <th class="width94">{{config('const.label.contact')}}</th>
                <th class="width400">{{config('const.label.mail_address')}}</th>
            </tr>
            @php
                $cnt = 0;
                foreach ($facility_mng as $val){
                    if($facility[0]->id == $val->facility_id){
                        echo "<tr>";
                            echo "<td class = 'paddingleft10' id='facility_manager_name'>" . $val->facility_manager_name . "<input type='hidden' id='target_id' value=" . $val->id . "><input type='hidden' id='password' value=" . $val->password . "></td>";
                            echo "<td class = 'paddingleft10' id='facility_manager_id'>" . $val->facility_manager_id . "</td>";
                            if($val->contact != 0){
                                echo "<td class = 'textcenter' id='contact'>" . config('const.text.circle') . "</td>";
                            }else{
                                echo "<td></td>";
                            }
                            echo "<td class = 'paddingleft10' id='mail_address'>" . $val->mail_address . "</td>";
                        echo "</tr>";
                        $cnt++;
                    }
                }
                while ($cnt < 15){
                    echo "<tr>";
                        $count = 0;
                        while ($count < 4){
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
            <span class="paddingleft10 " id="faclity_name_val">aaa</span><br>
             <div align="center" class="paddingleft10">
                <p>{{config('const.label.facility_manager_name')}}<br>
                <input class="paddingleft10" type="text" id="regist_facility_manager_name" maxlength='20' placeholder='施設管理者名を入力してください'><br><br>
                {{config('const.label.facility_manager_id')}}<br>
                <input class="paddingleft10" type="text" id="regist_facility_manager_id" maxlength='20' placeholder='施設管理者IDを入力してください'><br><br>
                {{config('const.label.password')}}<br>
                <input class="paddingleft10" type="text" id="regist_password" maxlength='20' placeholder='パスワードを入力してください'><br><br>
                {{config('const.label.contact')}}<br>
                <select id="regist_contact">
                        <option value="0" selected>なし</option>
                        <option value="1" >{{config('const.text.circle')}}</option>
                </select><br><br>
                {{config('const.label.mail_address')}}<br>
                <input class="paddingleft10" type="text" id="regist_mail_address" maxlength='256' placeholder='eメールアドレスを入力してください'><br>
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
                <span id="faclity_mng_name"></span><br>
                </p>
                <button class="btn1" id="delete_btn">{{config('const.btn.delete')}}</button>
                <button class="btn1" id="delete_cancel_btn">{{config('const.btn.cancel')}}</button>
            </div>
        </div>
    </div>
@endsection
