<?php
/**
 * @author Justin
 * @copyright 2016
 */
 
$lt = $lc = $df = $dt = $ld = $readonly = $readonly2 = $isdisabled = $empStat = "";
$datetoday = date("d-m-Y");
if($code){   
    // $readonly    = " style='pointer-events: none;'";
    $readonly2    = " readonly";
    $isdisabled  = " disabled";
    $qupdate     = $this->employeemod->load_leave_setup($code);
    if($qupdate->num_rows() > 0){
        $lt = $qupdate->row(0)->leavetype;
        $lc = $qupdate->row(0)->credit;
        $tnt = $qupdate->row(0)->teachingType;
        $empStat = $qupdate->row(0)->employmentStatus;
        $df = $qupdate->row(0)->dfrom;
        $dt = $qupdate->row(0)->dto;
        $ld = $qupdate->row(0)->id;
    }
}
?>
<div class="container" style="width: 80%;">
    <form id="form_leave_app" class="form-horizontal">
        <input type="hidden" name="code" value="<?=$code?>" />
        <input name="model" value="setupLeave" hidden=""/>
        <input type="hidden" name="lid" value="<?=$ld?>" hidden=""/>
        <div class="form-group">
            <label class="field_name align_right">Leave Type</label>
            <div class="field" style="width: 96%;">
                <select class="chosen" name="mh_leavetype" id="mh_leavetype" <?=$readonly?>><?=$this->employeemod->othLeave($lt,false);?></select>
            </div>
        </div>
    	<div class="form-group" hidden="">
            <label class="field_name align_right">Teaching</label>
            <div class="field no-search">
                <select class="form-control" name="tnt" id="tnt" <?=$readonly?>><?=$this->reports->employeetype($tnt)?></select>
            </div>
        </div>
    	
    	
    	<div class="form-group" id="status">
            <label class="field_name align_right">Employment Status</label>
            <div class="field no-search">
    			<? 	
    				$status = $this->employeemod->employeestatusupdatenotif();

    				if($status->num_rows() > 0)
    				{
    					$explode = explode(",",$empStat);
    					foreach($status->result() as $row){
    							if(in_array($row->code,$explode)) $checked = "checked";
    							else $checked = "";
    						?>
    							<input type="checkbox" name="empstatus" value="<?=$row->code?>" <?=$checked?> <?=$readonly?>/><?=$row->description?><br>
    						<?
    					}
    				}
    			?>
    		</div>
    	</div>
    	<div class="form-group" style="display: none;">
    		<label class="field_name align_right">Leave Credits</label>
    		<div class="field">
    			<input class="form-control" id="mh_credits" name="mh_credits" type="number" value="<?=$lc?>" <?=$readonly2?>/>
    		</div>
    	</div>
        <div class="form-group">
            <label class="field_name align_right">Date Range</label>
            <div class="field">
              <div class="col-md-6" style="padding-left: 0px;">
                  <div class='input-group date' id="datesetfrom" data-date="<?=$df?>" data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" size="16" name="datesetfrom" id="dfrom" value="<?=$df?>"/>
                    <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                  </div>
                </div>
              <div class="col-md-6" style="padding-left: 0px;">
                <div class='input-group date' id="datesetto" data-date="<?=$dt?>" data-date-format="yyyy-mm-dd">
                  <input type='text' class="form-control" size="16" name="datesetto" id="dto" value="<?=$dt?>"/>
                  <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </div>
            </div>
        </div>
    </form>
</div>

<script>
var toks = hex_sha512(" ");
$("#tnt").change(function(){
    if($(this).val() == "teaching")
	{
		$("#status").hide();
	}
	else
	{
		$("#status").show();
	}
});

$("#button_save_modal").unbind("click").click(function(){
    var checkbox_array = {};
    var count = 0;
    if($("#mh_leavetype").val() == ""){
        $("#mh_leavetype").focus();
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Leave Type is required!.',
            showConfirmButton: true,
            timer: 2000
        });
        return false;
    // }else if($("#mh_credits").val() == ""){
    //     $("#mh_credits").focus();
    //     alert("Leave Credits is required!.");
    //     return false;
    }else{
        $(".grey, #button_save_modal").hide();
        $("#leaveloading").show();
        $("input:checkbox[name=empstatus]:checked").each(function(){
            count++;
            checkbox_array[count] = GibberishAES.enc($(this).val(), toks);
        });
        $.ajax({
           // url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
           url      :   "<?=site_url("leave_/saveLeaveSetup")?>",
           dataType : 'json',
           type     :   "POST",
           data     :   {formdata: GibberishAES.enc($("#form_leave_app").serialize(), toks), toks:toks, empstatuses: checkbox_array},
           success  :   function(msg){
                var data_failed = msg.data_failed;
                var failed = '';
                for (var key in data_failed) {
                    // console.log(data_failed[key]);
                    failed += data_failed[key] + ", ";
                }
                if(failed) failed = failed.substring(0, failed.length-2);
                else failed = 'NONE';

                if(msg.err_code==1){
                    var toast = msg.msg+'\n'+'Success count: '+msg.success_count+'\n'+'Data insert failed: '+failed;
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: toast,
                        showConfirmButton: true,
                        timer: 2000
                    });
                    $("#tab2").html("");
                    $("#modalclose").click();
                    loaddata();
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 2000
                    });
                    $(".grey, #button_save_modal").show();
                    $("#leaveloading").hide();
                }

           }
        });
    }
	
});
$('#datesetfrom,#datesetto').datetimepicker({
    format: 'YYYY-MM-DD'
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

$(".chosen").chosen();
</script>