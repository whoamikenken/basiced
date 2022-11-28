<?php

 $cdisable = false;
 $description = "";
 $type = "";
 $code="";
 $campus="";
 $fr="";
$payment_type = "";
$teaching_type = "";
 $active = true;
 if($holiday_id){
     $sql = $this->db->query("select code, hdescription, holiday_type, is_active, freq_id, campus, payment_type, teaching_type from code_holidays WHERE holiday_id='{$holiday_id}'");
     if($sql->num_rows()>0){
        $description = Globals::_e($sql->row(0)->hdescription);
        $code = Globals::_e($sql->row(0)->code);
        $type = Globals::_e($sql->row(0)->holiday_type);
        $active = Globals::_e($sql->row(0)->is_active) == "YES";
        $fr = Globals::_e($sql->row(0)->freq_id);
        $campus = Globals::_e($sql->row(0)->campus);
        $payment_type = Globals::_e($sql->row(0)->payment_type);
        $teaching_type = Globals::_e($sql->row(0)->teaching_type);

     }
     $cdisable = true;
 }

?>
<style>
  .modal-dialog.modal-lg {
    width: min-content!important;
}
</style>
<input type="hidden" id="job" value="<?=$job?>">
<table class="table">
  <tr>
    <td class="align_right" style="width:10%;">Code</td>
    <td><input class="form-control required isrequired" id="mh_code" name="mh_code" type="text" value="<?=$code?>" maxlength="3"/>
    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span></td>
  </tr>
  <tr>
    <td class="align_right">Description</td>
    <td><input class="form-control required isrequired" id="mh_description" name="mh_description" type="text" value="<?=$description?>"/>
    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span></td>
  </tr>
  <tr>
    <td class="align_right">Type</td>
    <td>
            <select class="chosen isrequired" style="width:80%;" name="mh_type" id="mh_type">
              <option value="">-Select Type-</option> 
            <?
              $opt_type = $this->db->query("select DISTINCT holiday_type,description from code_holiday_type ")->result();
              foreach($opt_type as $c){
              ?><option<?=($c->holiday_type==$type ? " selected" : "")?> value="<?=$c->holiday_type?>"><?=$c->description?></option><?    
              }
            ?>
            </select>
            <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <div class="col-md-12">
        <label><b>Applicable To:</b></label><br><br>
        <div class="form-group">
            <div class="col-md-12" style="padding-left: 0px;">
                <label  for="employeeid" class="col-sm-3 align_right">Campus</label>
                <div class="col-sm-6">
                    <select class="chosen" name="campus" id="campus" disabled>
                        <?= $this->extras->getCampus($campus);?>
                    </select>
                </div> 
            </div>
        </div>
        <br><br>
        <div class="form-group">
            <div class="col-md-12" style="padding-left: 0px;">
                <label  for="employeeid" class="col-sm-3 align_right">Teaching Type</label>
                <div class="col-sm-9">
                    <div class="col-md-12 no-search" style="padding-left: 0.6%;">
                      <div class="col-sm-3" style="padding-left: 0px;">
                          <input type="checkbox" class="holidaycheckbox" id="teaching" <?=($teaching_type == "teaching" || $teaching_type == "all" ? "checked" : "")?> style="-webkit-transform: scale(1.5);"/>&nbsp;&nbsp;Teaching
                      </div>
                      <div class="col-sm-6">
                          <input type="checkbox" class="holidaycheckbox" id="nonteaching" <?=($teaching_type == "nonteaching" || $teaching_type == "all" ? "checked" : "")?>  style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;Non-Teaching
                      </div>
                    </div>
                </div> 
            </div>
        </div>
        <br><br>
        <div class="form-group">
            <div class="col-md-12" style="padding-left: 0px; display: none">
                <label  for="employeeid" class="col-sm-3 align_right">Payment Type</label>
                <div class="col-sm-9">
                    <!-- <div class="col-md-12 no-search" style="padding-left: 0.6%;">
                      <div class="col-sm-3" style="padding-left: 0px;">
                          <input type="checkbox" class="holidaycheckbox" id="Monthly" <?=($payment_type == "1" || $payment_type == "all" ? "checked" : "")?>  style="-webkit-transform: scale(1.5);"/>&nbsp;&nbsp;Monthly
                      </div>
                      <div class="col-sm-6">                   
                          <input type="checkbox" class="holidaycheckbox" id="Daily" <?=($payment_type == "0" || $payment_type == "all" ? "checked" : "")?> style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;Daily
                      </div>
                    </div> -->
                    <div class="col-md-12 no-search" style="padding-left: 0.6%;">
                      <div class="col-sm-3" style="padding-left: 0px;">
                          <input type="checkbox" class="holidaycheckbox" id="Monthly" checked  style="-webkit-transform: scale(1.5);"/>&nbsp;&nbsp;Monthly
                      </div>
                      <div class="col-sm-6">                   
                          <input type="checkbox" class="holidaycheckbox" id="Daily" checked  style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;Daily
                      </div>
                    </div>
                </div> 
            </div>
        </div>
      </div>
    </td>
  </tr>
      <tr>
        <td class="align_right">Frequency</td>
        <td>
          <select class="chosen" style="width:80%;" name="hol_freq" id="hol_freq">
            <?php
              $freq = $this->extras->listHolidayFreqs();
              foreach ($freq as $key => $value) {
                  print("<option value=\"".$key."\" ".(($key == $fr) ? "selected" : "").">".$value."</option> ");
              }// end foreach
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="align_right" valign="top">Affected Office:</td>
        <?php
          $deptsaffect = $this->extras->listDepartmentsAffectedByHoliday($holiday_id);
          // echo "<pre>";print_r($this->db->last_query());die;
        ?>
        <td>
        <form id="form_holidays">
          <table class="table table-bordered">
            <tr>
              <td><input type="checkbox" id="selectall" />&nbsp;SELECT ALL</td>
              <!-- <td>Permanent</td>
              <td>Pro-B</td>
              <td>Contractual</td> -->
              <script>
                var cs_list = [];
              </script>
              <?
                $i = 0;
                $getCodeStatus = $this->extras->getCodeStatus();
                foreach ($getCodeStatus->result() as $gcs) {
                    echo "<td class='deptkey' deptcode='".$gcs->code."'><input type='checkbox' value='".$gcs->code."' id='".$gcs->code."' onclick='selectAll(this.value)' />".$gcs->description."</td>";
                    echo "<script> cs_list[".$i."] = '".$gcs->code."'; </script>";
                    $i +=1;
                }
              ?>
            </tr>
          <?php
            $depts = $this->extras->showdepartmentholiday($holiday_id);
            foreach ($depts as $key => $detpItem) {
              $desc = explode("|",$detpItem);
              $permanent = explode("~",$desc[1]);
              $prob      = explode("~",$desc[2]);
              $contract  = explode("~",$desc[3]);
              echo "<tr><td><input class=\"deptsaffected chk\" type=\"checkbox\" name=\"deptsaffected[]\" value=\"{$key}\"";
              if ($holiday_id) {
                if (!empty($deptsaffect)) {
                  echo in_array($key, $deptsaffect) ? " checked=\"checked\"" : "";
                }
              }else{
              echo " checked=\"checked\"";
              }
              echo ">&nbsp;{$desc[0]}</td>";
              $displayed_cs = "";
              
              foreach ($getCodeStatus->result() as $gcs){
                  $displayed_checked = "";
                  $value = $key."~".$gcs->code;
                  
                  // validate if status are included.
                  $query = $this->extras->findStatusIncluded($holiday_id,$key);
                  // if($key == "QA"){
                  //   echo "<pre>"; print_r($holiday_id);
                  //   echo "<pre>"; print_r(explode(", ",$query->row()->status_included)); die;
                  // }
                  if($query->num_rows() > 0){
                      foreach ((explode(", ",$query->row()->status_included)) as $sc) {
                          //$displayed_checked = "";
                          if($sc == $value) $displayed_checked = "checked";
                          //$displayed_checked = $sc == $value? "checked":"";
                      }
                  }
                  // end of validation

                  $displayed_cs = $displayed_cs. "<td><center><input type='checkbox' class='empstatus ".$gcs->code."' name='".$gcs->code."[]' value='".$value."' ".$displayed_checked." style='-webkit-transform: scale(1.5);padding: 10px;'></center></td>";
              }
              
              echo $displayed_cs."</tr>";
              // <td><input type='checkbox' name='permanent[]' value='$key~permanent' ".($key == $permanent[0] ? " checked" : "")." style='-webkit-transform: scale(1.5);padding: 10px;'/></td>
              // <td><input type='checkbox' name='prob[]' value='$key~prob' ".($key == $prob[0] ? " checked" : "")." style='-webkit-transform: scale(1.5);padding: 10px;'/></td>
              // <td><input type='checkbox' name='contractual[]' value='$key~contract' ".($key == $contract[0] ? " checked" : "")." style='-webkit-transform: scale(1.5);padding: 10px;'/></td>
              // </tr>");
            }
          ?>   
          </table>
        </form>
        </td>
      </tr>
  <tr>
    <td class="align_right">Active</td>
    <td>
      <input class="col-md-1" id="mh_active" name="mh_active" type="checkbox" value="1"<?=($active ? " checked='true'" : "")?> style="-webkit-transform: scale(1.5);"/>
    </td>
  </tr>

</table>
<script>
var toks = hex_sha512(" "); 
selectAllChecked();
deptSelectAll();
$(".chosen").chosen();

// $("#mh_code").keypress(function (e) {
//     var keyCode = e.keyCode || e.which;
//     var regex = /^[A-Za-z0-9]+$/;
//     var isValid = regex.test(String.fromCharCode(keyCode));
//     return isValid;
// });

$("#save-dtr-setup").unbind("click").click(function(){
  
  var TType = "all";
  var PType = "all";
  var Campus = $("#campus").val();
  var cancontinue = false;
 

  if($("#mh_code").val() == ''){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Code is required!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  } 
  if($("#mh_description").val() == ''){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Description is required!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  } 
  if($("#mh_type").val() == ''){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Holiday type is required!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }
   if ($("#teaching").prop("checked") == false && $("#nonteaching").prop("checked") == false) {
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Please select a teaching type',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }else if($("#teaching").prop("checked") == true && $("#nonteaching").prop("checked") == false){
    TType = "teaching";
  }else if($("#nonteaching").prop("checked") == true && $("#teaching").prop("checked") == false){
    TType = "nonteaching";
  }

  if ($("#Monthly").prop("checked") == false && $("#Daily").prop("checked") == false) {
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Please select a payment type',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }else if($("#Monthly").prop("checked") == true && $("#Daily").prop("checked") == false){
    PType = "1";
  }else if($("#Daily").prop("checked") == true && $("#Monthly").prop("checked") == false){
    PType = "0";
  }

  var empstatus = false;
  $(".empstatus").each(function(){
    if($(this).is(":checked")) empstatus = true;
  });

  var deptsaffected = false;
  $(".deptsaffected").each(function(){
    if($(this).is(":checked")) deptsaffected = true;
  });

  if(!deptsaffected || !empstatus){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Affected status and departments are required!',
        showConfirmButton: true,
        timer: 1000
    });

    return;
  }

   if($("#form_holidays").valid()){   
                $("#saving").hide();
            $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait.."); 
      var active = $("input[name='mh_active']").is(":checked") ? "YES" : "NO"        
   var form_datas = $("#form_holidays").serialize();
        form_datas+="&toks="+toks;
  var  form_data = {
        mh_code : GibberishAES.enc($("input[name='mh_code']").val(), toks),
        mh_description : GibberishAES.enc($("input[name='mh_description']").val(), toks),
        mh_type : GibberishAES.enc($("select[name='mh_type']").val(), toks),
        hol_freq : GibberishAES.enc($("select[name='hol_freq']").val(), toks),
        holiday_id : GibberishAES.enc("<?=$holiday_id?>", toks),
        active : GibberishAES.enc(active, toks),
        code_status : GibberishAES.enc(cs_list, toks),
        teaching : GibberishAES.enc(TType, toks),
        payment : GibberishAES.enc(PType, toks),
        campus: GibberishAES.enc($("select[name='campus']").val(), toks),
        toks: toks
      }
       // form_data+="&mh_code="+GibberishAES.enc($("input[name='mh_code']").val(), toks);
       // form_data+="&mh_description="+GibberishAES.enc($("input[name='mh_description']").val(), toks);
       // form_data+="&mh_type="+GibberishAES.enc($("select[name='mh_type']").val(), toks);
       // form_data+="&hol_freq="+GibberishAES.enc($("select[name='hol_freq']").val(), toks);
       // form_data+="&holiday_id="+GibberishAES.enc("<?=$holiday_id?>", toks); 
       // form_data+="&active="+GibberishAES.enc(active, toks);
       // form_data+="&code_status="+GibberishAES.enc(cs_list, toks);
       // form_data+="&teaching="+GibberishAES.enc(TType, toks);
       // form_data+="&payment="+GibberishAES.enc(PType, toks);
       // form_data+="&campus="+GibberishAES.enc($("select[name='campus']").val(), toks);
       // form_data+="&toks="+toks;
    $.ajax({
        url: "<?=site_url("maintenance_/checkifCodeExist")?>",
        data : {code:GibberishAES.enc($("input[name='mh_code']").val(), toks), table:GibberishAES.enc('code_holidays', toks), holiday_type:GibberishAES.enc("<?=$holiday_id?>", toks), toks:toks},
        type : "POST",
        success:function(msg){
          msg = $.trim(msg);
          if(msg == '1'){

              save_holiday(form_data, form_datas); 
            
          }else{
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Code already exist!',
                showConfirmButton: true,
                timer: 1000
            })
            $("#saving").show();
          $("#loading").hide(); 
          }
        }
     });
   }else {
       $validator.focusInvalid();
       return false;
   }     
});

// select all function
// author : justin (with e)
function selectAll(val){
  //alert(val);
  $("."+val+"").prop('checked', $("#"+val+"").prop("checked"));
}

function deptSelectAll(){
  var ischecked = true;
  $(".chk").each(function(){
    if(!$(this).prop("checked")) ischecked = false;
  });
  if(!ischecked) $("#selectall").attr("checked", false);
  else $("#selectall").attr("checked", true);
}
// end of select all

$("#selectall").change(function(){
    $(".chk").prop('checked', $(this).prop("checked"));
});

function selectAllChecked(){
  var ischecked = true;
  $(".deptkey").each(function(){
    var deptcode = $(this).attr("deptcode");
    $("."+deptcode+"").each(function(){
      if(!$(this).prop("checked")) ischecked = false;
    });

    if(ischecked) $("#"+deptcode+"").prop("checked", true);
    ischecked = true;
  });
}

function save_holiday(form_data, form_datas){
  $.ajax({
      url: "<?=site_url("maintenance_/save_holidays")?>",
      data : form_data,
      type : "POST",
      complete:function(data){
        save_deptaffected(form_datas);
        // Swal.fire({
        //     icon: 'success',
        //     title: 'Success!',
        //     text: 'Holiday Name has been '+msg+' succesfully!',
        //     showConfirmButton: true,
        //     timer: 1500
        // })
        // setTimeout(function(){
        //   $(".modalclose").click();
        //   cancontinue = true;
        // }, 1500)
        
      }
   });
}

function save_deptaffected(data){
  var msg = ($("#job").val() == "edit") ? "updated" : "saved";
  $.ajax({
      url: "<?=site_url("maintenance_/saveAffectedDepartment")?>",
      data : data,
      type : "POST",
      success:function(data){
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Holiday Name has been '+msg+' succesfully!',
            showConfirmButton: true,
            timer: 1500
        })
        setTimeout(function(){
          $(".modalclose").click();
          $("#saving").show();
          $("#loading").hide(); 
          cancontinue = true;
        }, 1500)
        
      }
  })
}
  

</script>
