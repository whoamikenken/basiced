<?php

$CI =& get_instance();
$CI->load->model('leave');

$q_other_leave = $CI->leave->getApplicableLeave($employeeid);
 
$lt = $lc = $df = $dt = $ld = $readonly = $readonly2 = $isdisabled = $empStat = "";
$datetoday = date("d-m-Y");

?>

<style type="text/css">
  .form_row{
    padding-bottom: 15px;
  }

  #form_leave_app{
    margin-top: 20px;
  }

</style>

<form id="form_leave_app" style="margin: 40px;">
    <div class="form_row">
        <label class="field_name align_right">Leave Type</label>
        <div class="col-md-9">
            <select class="chosen" name="mh_leavetype" id="mh_leavetype" <?=$readonly?>>
              <!-- <?=$this->employeemod->othLeave($lt,false);?>   -->
              <?php foreach ($q_other_leave as $row): ?>
              <option value="<?=$row->code_request?>"><?=$row->description?></option>  
              <?php endforeach; ?>
            </select>
        </div>
    </div>
	<div class="form_row">
		<label class="field_name align_right">Leave&nbsp;Credits</label>
		<div class="col-md-9">
			<input class="form-control required" id="mh_credits" name="mh_credits" type="number" value="<?=$lc?>" <?=$readonly2?>/>
		</div>
	</div>
    <div class="form_row">
        <label class="field_name align_right">Date Range</label>
        <div class="col-md-9">
            <select class="chosen" name="mh_cutoff">
                <!-- <?=
                    $CI->leave->getLeaveCreditDates();
                ?> -->
            </select>
        </div>
    </div>
</form>
<script>
  $('.chosen').chosen();
$("#loading").hide();
$("#button_save_modal").unbind("click").click(function(){
	
    var form_data = $("#form_leave_app").serialize();
        form_data += "&employeeid=<?=$employeeid?>";
    console.log(form_data);
    if($("#mh_leavetype").val() == ""){
        $("#mh_leavetype").focus();
        // alert("Leave Type is required!.");
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Leave Type is required!',
            showConfirmButton: true,
            timer: 1000
        })
        return false;
    /*}else if($("#mh_credits").val() == ""){
        $("#mh_credits").focus();
        alert("Leave Credits is required!.");
        return false;*/
    }else if($("select[name='mh_cutoff']") == ""){
        $("select[name='mh_cutoff']").focus();
        // alert("Leave Type is required!.");
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Date is required',
            showConfirmButton: true,
            timer: 1000
        })
        return false;
    /*}else if($("#mh_credits").val() == ""){
        $("#mh_credits").focus();
        alert("Leave Credits is required!.");
        return false;*/
    }else{
        $(".grey,#button_save_modal").hide();
        // $(".modal-footer").append("<div id='loading'><img class='pull-right' src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..</div>");
        $(".modal-footer").append();
        $.ajax({
           url      :   "<?=site_url("leave_/addIndividualLeaveCredit")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg,
            showConfirmButton: true,
            timer: 1000
        })
            $("#modalclose").click();
            $('#loading').remove();
            $('#pinfotab li.active').click();
           }
        });
    }
	
});

function numbersonly(myfield, e, dec)
{
    var key;
    var keychar;
    
    if (window.event)
       key = window.event.keyCode;
    else if (e)
       key = e.which;
    else
       return true;
    keychar = String.fromCharCode(key);
    if ((key==null) || (key==0) || (key==8) || 
        (key==9) || (key==13) || (key==27) )
       return true;
    else if ((("0123456789").indexOf(keychar) > -1))
       return true;
    else if (dec && (keychar == "."))
       {
       myfield.form.elements[dec].focus();
       return false;
       }
    else
       return false;
}

$("#mh_leavetype").change(function(){
  getLeaveDateRange();
})

function getLeaveDateRange(){
  var leavetype = $("#mh_leavetype").val();
  $.ajax({
     url      :   "<?=site_url("leave_/getLeaveDateRange")?>",
     type     :   "POST",
     data     :   {leavetype:leavetype, employeeid: "<?=$employeeid?>"},
     success  :   function(msg){
      $("select[name='mh_cutoff']").html(msg).trigger('chosen:updated');
     }
  })
}
</script>