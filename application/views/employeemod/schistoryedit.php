<?php
	$datetoday = date("d-m-Y");
	$employeeid = $this->session->userdata("username");
  $usertype = $this->db->query("SELECT * FROM user_info WHERE username = '$employeeid' ")->row()->type;
     $cdisable = false;
     $dateservice = "";
      $servicecredit = "";
      $dayMode = "";
      $dateexplode =$dateexplode1 =$dateexplode2 =$date = "";
      $date;
      $id = "";
     if($code){
         $sql = $this->db->query("SELECT a.id,a.date,a.service_credit,a.dayMode,a.reason
                                    FROM sc_app a
                                    LEFT JOIN sc_app_emplist b ON a.id = b.base_id
                                    LEFT JOIN employee_service_credit c ON a.date = c.date
                                    WHERE a.id = '$code' ");
         if($sql->num_rows()>0){
            $id = $sql->row(0)->id;
            $servicecredit = $sql->row(0)->service_credit;
            $dayMode = $sql->row(0)->dayMode;
            $reason = $sql->row(0)->reason;
            if (str_word_count($sql->row(0)->date) == 2) {
              $date = $sql->row(0)->date;
            }
            else{
              $dateexplode = explode('/',$sql->row(0)->date);
              // $dateexplode1 = $dateexplode[0];
              // $dateexplode2 = $dateexplode[1];
            }
         }
         $cdisable = true;
     }

     $verify = $this->db->query("SELECT * FROM sc_app_emplist WHERE employeeid ='$employeeid'");
     if($verify->num_rows() > 0) $verify = $this->db->query("SELECT * FROM sc_app_emplist WHERE employeeid ='$employeeid'")->row()->dstatus;
     else $verify = '';
?>

<style>
.modal{
    width: 50%;
    left: 0;
    right: 0;
    margin: auto;
}
</style>
<p id="verified" style="display:none;"><?=$verify?></p>
<form id="frmsc">
<input name="model" value="applySCWithSequence" hidden=""/>
<input name="id" id="id" value="<?=$id?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="7%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Service Credit Edit History</strong></td>
                </tr>

            </table>
        </div>
        <div class="modal-body">
            <div class="content">
               <!-- Approve by approver section -->
                <div class="form_row" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?>>
                    <label class="field_name align_right">Will be approve by approver?</label>
                    <div class="field no-search">
                        <select class="form-control" name="allowApprover" id="allowApprover">
                            <option value="1">YES</option> <!-- kapag yes, dadaan sa sequence approver -->
                            <option value="0">NO</option> <!-- kapag no, deretso approved na -->
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Date of Service Credit </label>
                    <div class="field no-search">
                      <?php if (str_word_count($date) == 2): ?>
                        
                         <div class="input-group date" id='datePicker' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            
                            <input class="align_center" size="16" name="date" id="date" type="text" value="<?=$date;?>" readonly":"">
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                      <?php else: ?>
                        <div class="input-group date" id='datePicker' data-date="<?=$dateexplode[0]?>" data-date-format="yyyy-mm-dd">
                            
                            <input class="align_center" size="16" name="date" id="date" type="text" value="<?=$dateexplode[0]?>" readonly":"">
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <div class="input-group date" id='datePickers' data-date="<?=$dateexplode[1]?>" data-date-format="yyyy-mm-dd">
                            
                            <input class="align_center" size="16" name="date1" id="date1" type="text" value="<?=$dateexplode[1]?>" readonly":"">
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                      <?php endif ?>
                        
						<span id='message' style='color:red'></span>
                    </div>
                </div>
				
				<div class="form_row">
                    <label class="field_name align_right">Day Mode</label>
                    <div class="field no-search">
                        <select name='dayMode' id='dayMode' class='chosen' ">
							<option value='whole' <?=$dayMode =="whole"? "selected":""?> >Whole Day</option>
							<option value='half' <?=$dayMode=="half"? "selected":""?> >Half Day</option>
						</select>
                    </div>
                </div>
				<div class="form_row">
                    <label class="field_name align_right">Service Credit</label>
                    <div class="field no-search">
                        <input type='text' tag="<?=$servicecredit?>" name="sc" id="sc" value="<?=$servicecredit?>" readonly":"" disabled/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" value="" placeholder="Reason"><?=$reason;?></textarea>
                    </div>
                </div>
            </div>
        </div>
       <!--  <div class="modal-footer">
            <span id="errormsg" hidden="" style="color: red; margin-right: 20px;"></span>
            <span id="loading" hidden=""></span>
            <span id="saving">
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </span>
        </div> -->
    </div>
</div>
</form>
<script>

$(document).ready(function(){
    $("#datePicker,#datePickers").datepicker({
        autoclose: true,
        todayBtn : true
    });
});

$("#button_save_modal").unbind("click").click(function(){
 var validate = $("#verified").text();
  if(validate == "APPROVED"){
      alert("This date already APPROVED! Not able to edit");
      location.reload();
      return;
  }
 var $validator = $("#frmsc").validate({
        rules: {
            id: {
              required: true,
              minlength: 1
            },
            date: {
              required: true,
              minlength: 2
            },
            dayMode: {
              valueNotEquals: "" 
            }
        }
    });
  
   if($("#frmsc").valid()){   
     $.ajax({
            url:"<?=site_url("service_credit_/SCactions")?>",
            type:"POST",
            data:{
                id:$("#id").val(),
                sc:$("#sc").val(),
                date:$("#date").val(),
                date1:$("#date1").val(),
                day:$("#dayMode").val(),
                reason:$("#reason").val(),
                allowApprover:$("#allowApprover").val(),
                job:'update'
            },
            success: function(msg){
                $("#modalclose").click();
                $(".inner_navigation .main li .active a").click();
                alert(msg); 
            }
         });
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$(".chosen").chosen();


$("#dayMode").change(function(){
    var dayMode = $(this).val();
    if(dayMode == 'whole')
    {
        $("#sc").val(1);
        $("#datePickers").show();
         $("#date").val('');
        $("#date1").val('');
    }
    else
    {
        $("#sc").val(0.5);
       $("#datePickers").hide();
        $("#date").val('');
        $("#date1").val('');
    }
});

</script>