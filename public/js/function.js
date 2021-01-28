var text = "";
var val = "";
function select_data(e,ids){
  var select_data = {};
  var selected = $(e).hasClass("highlight");
  val = $(e).closest('tr').find('#target_id').val();
  $("#data tr").removeClass("highlight");
  if(!selected && (val != undefined)){
          $(e).addClass("highlight");
          $("#edit_btn").css('background-color', '#4672c4');
          $("#delete_btn").css('background-color', '#4672c4');
          $.each(ids, function(index, value){
            text = '#' + index;
            if(value == "val"){
              select_data[index] = $(e).closest('tr').find(text).val();
            }else{
              select_data[index] = $(e).closest('tr').find(text).text();
            }
          });
          select_data['click_flg'] = true;
  }else{
      $("#edit_btn").css('background-color', '#a7a7a7');
      $("#delete_btn").css('background-color', '#a7a7a7');
      select_data['click_flg'] = false;
  }
  return select_data;
}
function edit_btn_click(click_flg, ids, words,circles){
  if(click_flg){
    $.each(ids, function(index, value){
      text = '#' + index;
      $(text).val(value);
    });
    $.each(circles, function(index, value){
      text = '#' + index;
      if(value == words[1]){
          $(text).val('1');
      }else{
          $(text).val('0');
      }
    });
    $("#regist_btn").text(words[0]);
    modal.style.display = 'block';
    return true;
  }else{
      $("#edit_btn").css('outline','none');
      return false;
  }
}
function data_check(type, input_data, text){
  var result = true;
  var error_message = document.getElementById("error_message");
  var kigo = new RegExp(/[!"#$%&'()\*\+\-\.,\/:;<=>?@\[\\\]^_`{|}~]/g);
  if(type == "facility_name"){
    if(input_data.trim().length == 0 || (input_data.match(/^[ 　\r\n\t]*$/)) || (kigo.test(input_data))){
      result = false;
    }
  }
  if(type == "id_pass"){
    if(input_data.trim().length < 4 || (/\s/.test(input_data)) || !(input_data.match(/^[0-9a-zA-Z_\-]*$/))){
      result = false;
    }
  }
  if(type == "pass"){
    if(input_data.trim().length == 0 || !(input_data.match(/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}$/)) || (kigo.test(input_data))){
      result = false;
    }
  }
  if(type == "mail"){
    if(input_data.trim().length == 0 || !(input_data.match(/^([a-z0-9_\.\-])+@([a-z0-9_\.\-])+[^.]$/i))){
      result = false;
    }
  }
  if(type == "name"){
    if(input_data.trim().length == 0 || !(input_data.match(/^[ァ-ヶー　]+$/))){
      result = false;
    }
  }
  if(!result){
    $("#error_message").text(text);
    error_message.style.display = "inline";
  }
  return result;
}
// 1桁の数字を0埋めで2桁にする
var toDoubleDigits = function(num) {
  num += "";
  if (num.length === 1) {
    num = "0" + num;
  }
 return num;     
};

function update_date_set(mysql_string)
{ 
   var t,d, result = null;

   if( typeof mysql_string === 'string' )
   {
      t = mysql_string.split(/[- :]/);

      //when t[3], t[4] and t[5] are missing they defaults to zero
      d = new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
      var YYYY = d.getFullYear();
      var MM = toDoubleDigits(d.getMonth()+1);
      var DD = toDoubleDigits(d.getDate());
      var hh = toDoubleDigits(d.getHours());
      var mm = toDoubleDigits(d.getMinutes());
      var ss = toDoubleDigits(d.getSeconds());
      result = YYYY + "-" + MM + "-" + DD + hh + ":" + mm + ":" + ss;
   }

   return result;   
}