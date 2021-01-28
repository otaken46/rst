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
    var count = 0;
    var contact_str = "";
    var str = "";
    if('{{$default_facility_id}}' != ""){
        facility_id = '{{$default_facility_id}}';
        $('#facility_name').val("{{$default_facility_id}}");
        $('table#data #item').remove();
        $.each(facility_mng, function(key, value) {
            if(facility_id == value['facility_id']){
                if(value['update_date'] != null){
                    update_date = update_date_set(value['update_date']);
                }else{
                    update_date = "no_update";
                }
                str = "";
                contact_str = "";
                str += "<tr id='item' class='item'>";
                str += "   <td class = 'paddingleft10' id='facility_manager_name'>" + value['facility_manager_name'] +"<input type='hidden' id='target_id' value=" +value['id'] +"><input type='hidden' id='password' value=" +value['password'] +"><input type='hidden' id='update_date' value=" + update_date +"></td>";
                str += "   <td class = 'paddingleft10' id='facility_manager_id'>" + value['facility_manager_id'] +"</td>";
                if(value['contact'] == 1){contact_str = "〇";}
                str += "   <td class = 'textcenter' id='contact'>" + contact_str +"</td>";
                str += "   <td class = 'paddingleft10' id='mail_address'>" + value['mail_address'] +"</td>";
                str += "</tr>";
                $("#data").append(str);
                count += 1;
            }
        });
        for( ; count < 5; count++){
            str = "<tr id='item'><td></td><td></td><td></td><td></td></tr>";
            $("#data").append(str);
        }
    }
    $('#facility_btn').on('click', function() {
        window.location.href = "{{ url('/facility')}}";
    });
    $('#facility_name').change(function() {
        count = 0;
        facility_id = $(this).val();
        $('table#data #item').remove();
        $.each(facility_mng, function(key, value) {
            if(facility_id == value['facility_id']){
                if(value['update_date'] != null){
                    update_date = update_date_set(value['update_date']);
                }else{
                    update_date = "no_update";
                }
                str = "";
                str += "<tr id ='item' class='item'>";
                str += "   <td class = 'paddingleft10' id='facility_manager_name'>" + value['facility_manager_name'] +"<input type='hidden' id='target_id' value=" +value['id'] +"><input type='hidden' id='password' value=" +value['password'] +"><input type='hidden' id='update_date' value=" + update_date +"></td>";
                str += "   <td class = 'paddingleft10' id='facility_manager_id'>" + value['facility_manager_id'] +"</td>";
                contact_str = "";
                if(value['contact'] == 1){contact_str = "〇";}
                str += "   <td class = 'textcenter' id='contact'>" + contact_str +"</td>";
                str += "   <td class = 'paddingleft10' id='mail_address'>" + value['mail_address'] +"</td>";
                str += "</tr>";
                $("#data").append(str);
                count += 1;
            }
        });
        for( ; count < 5; count++){
            str = "<tr id ='item'><td></td><td></td><td></td><td></td></tr>";
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
            $("#regist_facility_manager_id").prop('disabled', true);
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
        $("#regist_facility_manager_id").prop('disabled', false);
        error_message.style.display = "none";
    });
    $('#delete_cancel_btn').on('click', function() {
        delmodal.style.display = 'none';
    });
    $('#facility_management_btn').on('click', function() {
        facility_id = $('#facility_name').val();
        count = 0;
        $.each(facility_mng, function(key, value) {
            if(facility_id == value['facility_id']){
                count += 1;
            }
        });
        if(count == '{{config('const.max_facility_mng')}}'){
            regist_flg = false;
            $('#result').text('{{config('const.max_facility_mng')}}{{config('const.msg.err_005')}}');
            resultmodal.style.display = 'block';
        }else{
            regist_type = "new";
            faclity_name = "{{config('const.label.facility_name_id')}}" + $('#facility_name option:selected').text();
            $('#faclity_name_val').text(faclity_name);
            facility_id = $('#facility_name option:selected').val();
            modal.style.display = 'block';
        }
    });
    $('#regist_btn,#delete_exe_btn').on('click', function() {
        if(regist_flg){
            regist_flg = false;
            var err = true;
            if(regist_type != "delete"){
                facility_id = $('#facility_name option:selected').val();
                facility_manager_name = $('#regist_facility_manager_name').val();
                facility_manager_id = $('#regist_facility_manager_id').val();
                password = $('#regist_password').val();
                contact = $('#regist_contact').val();
                mail_address = $('#regist_mail_address').val();
                err = data_check("name", facility_manager_name, '{{config('const.label.facility_manager_name')}}{{config('const.msg.err_003')}}');
                if(err){err = data_check("id_pass", facility_manager_id, '{{config('const.label.facility_manager_id')}}{{config('const.msg.err_004')}}');}
                if(err){err = data_check("pass", password, '{{config('const.label.password')}}{{config('const.msg.err_007')}}');}
                if((err) && facility_manager_id == password){
                    err = false; 
                    text = '{{config('const.msg.err_009')}}';
                    $("#error_message").text(text);
                    error_message.style.display = "inline";
                }
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
                    url: "{{ action('FacilityMngController@regist') }}",
                    type: 'POST',
                    data:{
                        'facility_manager_name':facility_manager_name,
                        'facility_manager_id':facility_manager_id,
                        'password':password,'contact':contact,
                        'mail_address':mail_address,
                        'regist_type':regist_type,
                        'facility_id':facility_id,
                        'target_id':target_id,
                        'update_date':update_date},
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
            url = "{{url('/facility_mng')}}" + "?facility_id=" + facility_id;
            window.location.href = url;
        }else{
            regist_flg = true;
            resultmodal.style.display = 'none';
        }
    });
    $(document).on('click','#data tr', function() {
        ids = {'target_id':'val', 'facility_manager_name':'txt', 'facility_manager_id':'txt', 'password':'val', 'contact':'txt', 'mail_address':'txt', 'update_date':'val'};
        var arr_select = select_data(this,ids);
        target_id = arr_select['target_id'];
        facility_manager_name = arr_select['facility_manager_name'];
        facility_manager_id = arr_select['facility_manager_id'];
        password = arr_select['password'];
        contact = arr_select['contact'];
        mail_address = arr_select['mail_address'];
        update_date = arr_select['update_date'];
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
    <table border="1" id="data" class="sorttbl">
            <tr>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(1)')" class="width250 tbl-heder">{{config('const.label.facility_manager_name')}}<i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(2)')" class="width225 tbl-heder">{{config('const.label.facility_manager_id')}}<i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(3)')" class="width94 tbl-heder">{{config('const.label.contact')}}<i class="fa fa-sort"></i></th>
                <th onclick="w3.sortHTML('#data','.item', 'td:nth-child(4)')" class="width350 tbl-heder">{{config('const.label.mail_address')}}<i class="fa fa-sort"></i></th>
            </tr>
            @php
                $cnt = 0;
                if(isset($facility_mng[0]->facility_id)){
                    foreach ($facility_mng as $val){
                        if($facility[0]->id == $val->facility_id){
                            if($val->update_date != ""){
                                $date = strtotime($val->update_date);
                                $date = date('Y-m-dH:i:s',$date);
                            }else{
                                $date = "no_update";
                            }
                            echo "<tr id ='item' class='item'>";
                                echo "<td class = 'paddingleft10' id='facility_manager_name'>" . $val->facility_manager_name . "<input type='hidden' id='target_id' value=" . $val->id . "><input type='hidden' id='password' value=" . $val->password . "><input type='hidden' id='update_date' value=" . $date . "></td>";
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
                }
                while ($cnt < 5){
                    echo "<tr id ='item'>";
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
            <span class="paddingleft10 " id="faclity_name_val">aaa</span><br><br>
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
