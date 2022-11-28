<?
	//Added (6-2-2017)
    $model = new Disciplinary_Action();
	if($info_type == "code_disciplinary_action_offense_type")
	{
		$disciplinary_action_data = $model->getOffensesInfo($code);
    
	}
	else if($info_type == "code_disciplinary_action_sanction")
	{
		$disciplinary_action_data = $model->getSanctionsInfo($code);
	}

	$datacode = $desc = "";
    $action = ($disciplinary_action_data) ? "edit" : "add";
    $type = $desc = $message = $frequency = $nomonths = "";
    if($disciplinary_action_data){
      foreach ($disciplinary_action_data as $row) {
        $datacode = $row->code;
        $desc = $row->description;
        $message = $row->message;
        $frequency = (isset($row->frequency)) ? $row->frequency : '' ;
        $nomonths = (isset($row->nomonths)) ? $row->nomonths : '' ;
      }
    }
  if($action == "edit" && $info_type == "code_disciplinary_action_offense_type")
  {
    $disciplinary_action_data = json_decode(json_encode($disciplinary_action_data[0]), true);
    $sanctions = $this->extras->constructArrayListFromComputedTable($disciplinary_action_data["message"]); 
    
  }
  $disciplinary_setup = $this->extensions->getDisciplinaryActionSetup();

?>

<style type="text/css">
  .form-group{
    padding-bottom: 10px;
  }
  .error{
    color: red;
  }
</style>

<form id="info_form">
    <input type="hidden" id="site_url" value="<?= site_url() ?>">
    <input type="hidden" id="code_type" value="<?=$code;?>" />
    <input type="hidden" id="action_type" value="<?=$action;?>" />
    <input type="hidden" id="info_type" value="<?=$info_type;?>" />
    <input type="hidden" id="type_dis" value="<?=$type_dis;?>" />
      <div class="form-group">
        <div class="col-md-12">
            <label  for="employeeid" class="col-sm-3">Code</label>
            <div class="col-sm-9">
                <input class="form-control required isrequired" id="code" name="code" type="text" value="<?=$datacode?>" <?= ($datacode) ? "readonly" : "" ?>/>
            </div>
        </div>
      </div>
      <br>
      <div class="form-group">
        <div class="col-md-12">
            <label  for="employeeid" class="col-sm-3">Description</label>
            <div class="col-sm-9">
                <input class="form-control required" id="desc" name="desc" type="text" value="<?=$desc?>"/>
            </div>
        </div>
      </div>
      <br>
<!--       <div class="form-group" <?= ($func_type == "offense") ? "hidden" : "" ?> >
        <div class="col-md-12">
          <label  for="employeeid" class="col-sm-3">Message:</label>
          <div class="col-sm-9">
              <textarea class="form-control" rows="5" id="message" name="message" value="<?=$message?>" style="width: 65%;"><?=$message?></textarea>
            <div class="field" style="cursor: pointer;">
                <a id="refresh-var"><span><i class="icon-refresh"></i></span></a>&nbsp;&nbsp;&nbsp;
                <a id="add-name"><span>Add name</span></a>&nbsp;&nbsp;&nbsp;
                <a id="add-date"><span>Add date</span></a>
            </div>
          </div>
        </div>
      </div> -->
      <div id="nodays_div">
        <?php if($info_type == "code_disciplinary_action_offense_type"){ ?>
        <br>
        <div class="form-group">
          <div class="col-md-12">
              <label  for="employeeid" class="col-sm-3"><b>Sanctions Lists:</b></label>
              <div class="col-sm-9">
              </div>
          </div>
        </div>

        <?php if(empty($disciplinary_setup)){ ?>
          <h3 style="text-align: center;">Please Add Disciplinary Sanction</h3>
          <?php }else{ ?>
            <?php foreach($disciplinary_setup as $key => $value): ?>
            <div class="form-group">
              <div class="col-md-12">
                  <label  for="employeeid" class="col-sm-3"><?= $value['description']?></label>
                  <div class="col-sm-9">
                      <input class="form-control required" name="sanctions[<?=$value['code']?>]" type="number" value="<?= isset($sanctions[$value["code"]]) ? $sanctions[$value["code"]] : '' ?>" onkeypress="return isNumber(event)" /><span>&nbsp;&nbsp;(no of Days)</span>
                  </div>
              </div>
            </div>
            <br>
            <br>
            <?php endforeach ?>
          <?php } ?>
        <?php } ?>

      </div>
      <div id="notify_div">
        <div class="form-group">
          <div class="col-md-12">
              <label  for="employeeid" class="col-sm-3"><b>Notify on:</b></label>
              <div class="col-sm-9">
              </div>
          </div>
        </div>
        <br>
        <div class="form-group">
          <div class="col-md-12">
            <label for="employeeid" class="col-sm-3">Frequency</label>
            <div class="col-sm-9">
                <input class="form-control required" id="frequency" name="frequency" type="number" value="<?= $frequency?>"/>
            </div>
          </div>
        </div>
        <br>
        <div class="form-group">
          <div class="col-md-12">
            <label for="employeeid" class="col-sm-3">Within</label>
            <div class="col-sm-9">
                <select class="chosen required" id="month" name="month">
                    <option value="1" <?= ($nomonths==1) ? "selected" : "" ?> >1 Month</option>
                    <option value="2" <?= ($nomonths==2) ? "selected" : "" ?> >2 Month</option>
                    <option value="3" <?= ($nomonths==3) ? "selected" : "" ?> >3 Month</option>
                </select>
            </div>
        </div>
        </div>
      </div>
    <div class="field">
        <span id="errmsg"></span>
    </div>
</form>

<script>
  var toks = hex_sha512(" ");
  var off_type = $("#code").val();
  if(off_type == "ET" || off_type == "EA") $("#nodays_div").hide();
  else $("#notify_div").hide();

    $("#button_save_modal").unbind("click").click(function(){
        var form_data = "";  
        $('#info_form input, #info_form select, #info_form textarea').each(function(){
            if(form_data) form_data += '&'+$(this).attr('name')+'='+$(this).val();
            else form_data = $(this).attr('name')+'='+$(this).val();
        })
		
        form_data += "&action="+$("#action_type").val();
        form_data += "&info_type="+$("#info_type").val();
        var $validator = $("#info_form").validate({
            rules: {
                code: {
                  required: true,
                  minlength: 2
                },
                desc: {
                  required: true,
                  minlength: 2
                }
            }
        });

        if($("#info_form").valid()){
             $.ajax({
                url:"<?=site_url("disciplinary_action_/saveForm")?>",
                type:"POST",
                data:{formdata:GibberishAES.enc(form_data, toks), toks:toks},
                dataType: 'JSON',
                success: function(msg){
                    if(msg.err_code == 0){
                        $("#modalclose").click();
                        var notif = "saved.";
                        if ($("#code_type").val() != "") notif = "updated.";
                        var dis_type = "Offenses";
                        if ($("#type_dis").val() == "sanction" || $("#info_type").val() == "code_disciplinary_action_sanction") dis_type = "Sanction"

                         Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: dis_type +' has been '+ notif +' successfully.',
                            showConfirmButton: true,
                            timer: 1000
                          })
                         setTimeout(function() {
                          location.reload();
                         }, 2000);
                         return false;
                    } else {
                        $('#errmsg').html(msg.msg);
            						$('#info_form input').attr("disabled",false);
            						$('#info_form').find(".btn").attr("disabled",false);
                     
                    }
                }
             });
		}else {
           $validator.focusInvalid();
           return false;
       }
    });

    $("#add-name,da #add-date").on('click', function() {
    if($(this).ta('clicked', true)) $(this).css({"color":"red","pointer-events":"none"});
    var message = $("#message");
    var caretPos = message[0].selectionStart;
    var textAreamessage = message.val();
    var messageToAdd = "";
    if($(this).attr("id") == "add-name") messageToAdd = "name";
    else messageToAdd = "date";
    message.val(textAreamessage.substring(0, caretPos) + messageToAdd + textAreamessage.substring(caretPos) );
});
$(".chosen").chosen();    

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
</script>