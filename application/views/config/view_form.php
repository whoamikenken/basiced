<?
 
/**
 * @author Carlos Pacheco
 * @date 6-19-2014 2:30pm
 * 
 */

    $datetoday = date('Y-m-d');
    $empModel = new Employee();
    $pinfo = $empModel->getPersonnelInfoConfigList($info_type, $id);
    $datesetfrom = $this->input->post("datesetfrom")=="0000-00-00" ? date("Y-m-d") : $this->input->post("datesetfrom");
    $action = ($pinfo) ? "edit" : "add";
    $tbl = $empModel->getInfoTypeTable($info_type);
    $fields = $empModel->getTableFields($tbl);
    if($tbl == "code_relationship") unset($fields[0]); 
    if($tbl == "code_gender") unset($fields[0]); 
    if($tbl == "code_type") unset($fields[0]); 

?>

<style type="text/css">
  .form_row{
    padding-top: 10px;
  }

  #info_form{
    margin-top: 30px;
  }

</style>
<form id="info_form">

    <input type="hidden" name="id" value="<?=$id;?>" />
    
    <? foreach( $fields as $each ): ?>
        <? $strshow = ucwords($each);
        if($each=="schedid") {
           $strshow = "Schedule List";
        }else if( strpos($each, 'id')){
           $idremoved = str_replace('id',"",$each);
           $strshow = ucfirst($idremoved)." ID" ;
        }
    if($each=="campus_principal"){
       $strshow = "Campus Principal";
    }
    if(($strshow == "Code" && ($tbl != "code_type" && $tbl != "code_status"))  || $strshow == "Nationality ID" || $strshow == "Religion ID" || $strshow == "Citizen ID" || $strshow == "Management ID" || $strshow == "Rank ID"){
    ?> 
        <div class="form_row" hidden="">
            <label class="field_name align_right"><?=$strshow?></label>
            <div class="field">
    <?
    }
    else{
      ?>

      <div class="form_row" >
            <label class="field_name align_right"><?=$strshow?></label>
            <div class="field">
      <?
    }
        if($each=="schedid"){
            $rv = isset($pinfo[0]->$each)?$pinfo[0]->$each:"";
?>
              <div class="col-md-12">
                <select class="form-control chosen isrequired" id="mh_<?=$each;?>" name="<?=$each;?>">
                    <?
                      $opt_shift = $this->extras->showshiftschedule();
                      foreach($opt_shift as $c=>$val){
                      ?><option<?=($c==$rv ? " selected" : "")?> value="<?=Globals::_e($c)?>"><?=Globals::_e($val)?></option><?    
                      }
                    ?>
                </select>
                <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span></div>
              </div>
<?            
        }
    else if($each=="duration"){
            $rv = isset($pinfo[0]->$each)?$pinfo[0]->$each:"";
?>
                <div class="col-md-12">
                    <select class="chosen" id="mh_<?=$each;?>" name="<?=$each;?>">
                    <?
                      for($x = 0; $x <= 12; $x++){
                       ?><option<?=($x==$rv ? " selected" : "")?> value="<?=$x?>"><?=$x==0 ? "N/A" :($x==1 ? "$x Month" : "$x Months")?></option><?    
                      }
                    ?>
                    </select>
                </div>
               


 <?            
        }elseif($each=="date_active"){?>
          <div class="col-md-12">
            <div class='input-group date' id="datesetfrom" data-date="<?=($datesetfrom) ? $datesetfrom:$datetoday ?>"  data-date-format="yyyy-mm-dd">
              <input type='text' class="form-control isrequired" size="16"  name="<?=$each;?>" id="mh_<?=$each;?>" type="text" value="<?=$datetoday ?>" />
              <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
              </span>
              
            </div>             
          </div>
<?            
        }elseif($each=="campus_principal"){?>
          <div class="col-md-12">
            <select class="chosen"  id="mh_<?=$each;?>" name="<?=$each;?>" width='100%'>
                      <option value="">Select Employee</option>
                      <?
                $rv = isset($pinfo[0]->$each)?$pinfo[0]->$each:"";
                          $opt_type = $empModel->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true);
                          foreach($opt_type as $val){
                  ?><option value="<?=$val['employeeid']?>" <?=$rv==$val['employeeid']?"selected":""?>><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                }
                      ?>
            </select>
          </div>
    <?}else{
      if(($each == "code" && ($tbl != "code_type" && $tbl != "code_status"))  || $each == "nationalityid" || $each == "religionid" || $each == "Citizenid" || $each == "managementid" || $each == "rankid" ){
?>            
                <!-- <div class="col-md-12"  hidden=""><input class="col-md-8  form-control" id="mh_<?=$each;?>" name="<?=$each;?>" type="text" value="<?=(($pinfo) ? $pinfo[0]->$each : '')?>"/></div>  -->

<?
      }
      else{
        ?>
          <div class="col-md-12" ><input class="col-md-8 form-control isrequired" id="mh_<?=$each;?>" name="<?=$each;?>" type="text" value="<?=(($pinfo) ? $pinfo[0]->$each : '')?>" <?=($each == 'code' && $pinfo && $tbl == 'code_status' ? 'disabled' : '')?>/>
          <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span></div> 
        <?
        }
      }
?>                
            </div>
        </div>
    <? endforeach; ?>

    <div class="form_row">
    <div class="field">
        <span id="errmsg"></span>
    </div>
</div>
</form>

<script>
  var toks = hex_sha512(" "); 
  var reload;
  var code = $("#mh_code").val();
  $("#mh_code").keypress(function (e) {
    var keyCode = e.keyCode || e.which;
    var regex = /^[A-Za-z0-9]+$/;
    var isValid = regex.test(String.fromCharCode(keyCode));
    return isValid;
}); 
    $(".save-dtr-setup, #button_save_modal").unbind("click").click(function(){
  var cancontinue = false;

        // $(this).attr("disabled", "true");
        if($("#mh_code").val() == ''){
          Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Code is required!',
            showConfirmButton: true,
            timer: 1000
          })
          return;
        }else if($("#mh_description").val() == ''){
          Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Description is required!',
            showConfirmButton: true,
            timer: 1000
          })
          return;
        }
        else if($("#mh_schedid").val() == ''){
          Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Schedule List is required!',
            showConfirmButton: true,
            timer: 1000
          })
          return;
        }

   if($("#info_form").valid()){  
   var formdata = "";  
   // var form_data = $('#info_form').serializeArray();
   $('#info_form input, #info_form select').each(function(){
      if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
      else formdata = $(this).attr('name')+'='+$(this).val();
   })

   // console.log(code); return;
   // var form_data = $("#info_form").serialize();
    if("<?= $tbl ?>" == 'code_type' || "<?= $tbl ?>" == 'code_status'){
      $.ajax({
          url: "<?=site_url("maintenance_/checkifCodeExist")?>",
          data : {code: GibberishAES.enc($("#mh_code").val(), toks), table:GibberishAES.enc("<?= $tbl ?>", toks), toks:toks},
          type : "POST",
          success:function(msg){
            msg = msg.replace(/(\r\n|\n|\r)/gm, "");
            if ("<?=$action?>"== "add") {
            if(msg == '1'){
               save_batch(formdata); 
            }else{
              Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: 'Code already exist!',
                  showConfirmButton: true,
                  timer: 1000
              })
            }
          }
          else{
            if(msg === '0' || code == $("#mh_code").val()){
               save_batch(formdata); 
            }else{
              Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: 'Code already exist!',
                  showConfirmButton: true,
                  timer: 1000
              })
            }
          }
          }
       });
    }else{
      save_batch(formdata); 
    }  
      
   }else {
       $validator.focusInvalid();
       return false;
   }  
       
    });
function save_batch(form_data){
  var msgs = ("<?=$action?>"== "edit") ? "updated" : "saved";
   if("<?=$info_type?>" == "type"){
     $("#saving").html('<img src="<?=base_url()?>images/loading.gif">&emsp;<b id="savingText">Saving, please wait.</b>&nbsp;&nbsp;');
        reload = setInterval(function(){ 
            loadCodeTypeProgress(GibberishAES.enc($("#mh_code").val(), toks));
        }, 3000);
   }else {
    $("#errmsg").html("<h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This may take a while, please wait...</h6>");
  }
        var iscontinue = validateForm($('#info_form'));
        if(iscontinue){
          $(this).attr("disabled", "true");
           $.ajax({
              url:"<?=site_url("configuration_/saveForm")?>",
              type:"POST",
              data:{
                 id:  GibberishAES.enc($("input[name='id']").val(), toks),
                 info_type:  GibberishAES.enc('<?=$info_type;?>', toks),
                 formdata: GibberishAES.enc(form_data , toks),
                 action: GibberishAES.enc('<?=$action;?>' , toks),
                 toks:toks
              },
              dataType: 'JSON',
              success: function(msg){
                if("<?=$info_type?>" == "status"){
                    if("<?=$action?>" == "edit") msgs = "Employment Status has been updated successfully.";
                    else msgs = "Employment Status has been saved successfully. ";
                    Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: msgs,
                      showConfirmButton: true,
                      timer: 1500
                   });
                    setTimeout(function(){
                      location.reload();
                      $(".modalclose").click();
                      cancontinue = true;
                    }, 1500)
                }else if("<?=$info_type?>" == "type"){
                  if(msg.err_code == 4){
                    Swal.fire({
                        icon: 'warning',
                        title: 'In progress!',
                        text: "Saving is currently in progress..",
                        showConfirmButton: true,
                        timer: 1000
                    })
                    $("#saving").html('<img src="<?=base_url()?>images/loading.gif">&emsp;<b id="savingText">Recovering status, please wait..</b>&nbsp;&nbsp;');
                  }else{
                     Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "Batch Scheduling has been "+ msgs +" successfully.",
                        showConfirmButton: true,
                        timer: 1500
                    })
                     clearInterval(reload);
                    $("#saving").html('<button type="button" data-dismiss="modal" class="btn btn-danger modalclose">Close</button><button type="button" class="btn btn-success save-dtr-setup" id="save-dtr-setup">Save</button>');
                    $(".modalclose").click();
                    setTimeout(function(){
                      location.reload();
                      $(".modalclose").click();
                      cancontinue = true;
                    }, 1500)
                  }
                }else{
                  Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: 'Successfully '+msgs,
                      showConfirmButton: true,
                      timer: 1500
                  })
                  setTimeout(function(){
                  location.reload();
                  $(".modalclose").click();
                  cancontinue = true;
                }, 1500)
                }
                
        
                  // if(msg.err_code == 0){
                  //   Swal.fire({
                  //       icon: 'success',
                  //       title: 'Success!',
                  //       text: 'Successfully saved data!',
                  //       showConfirmButton: true,
                  //       timer: 1000
                  //   });
                  //     location.reload();
                  //     $("#modalclose").click();
                  //     $(".inner_navigation .main li .active a").click();
                  // } else {
                  //     $(this).attr("disabled", "false");
                  //     $('#errmsg').html(msg.msg);
                  // }
              }
           });
        }
}

function loadCodeTypeProgress(code){
      $.ajax({
            url:"<?=site_url("setup_/loadCodeTypeProgress")?>",
            type:"POST",
            data:{
               code:  code,
               toks:toks,
            },
            success: function(msg){
              if(msg == 0){
                Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: "Saving has been successfully completed!",
                  showConfirmButton: true,
                  timer: 1000
              })
              clearInterval(reload);
              $("#saving").html('<button type="button" data-dismiss="modal" class="btn btn-danger modalclose">Close</button><button type="button" class="btn btn-success save-dtr-setup" id="save-dtr-setup">Save</button>');
              $(".modalclose").click();
              setTimeout(function(){
                  location.reload();
                  $(".modalclose").click();
                  cancontinue = true;
                }, 1500)
              }else{
                $("#saving").html('<img src="<?=base_url()?>images/loading.gif">&emsp;<b id="savingText">Saving, please wait.. '+msg+'</b>');
              }
            }
         });
    }

$(".modalclose").click(function(){
  $(".save-dtr-setup").prop("disabled", "");
});

$(".chosen").chosen();    
$("#datesetfrom,#datesetto").datetimepicker({
    format: "YYYY-MM-DD"
}); 
</script>