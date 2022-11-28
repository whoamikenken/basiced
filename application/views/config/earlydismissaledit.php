<?php
	$datetoday = date("Y-m-d");
	$employeeid = $this->session->userdata("username");
$tsplit=$thr=$tmin=$ttotal=$ahr=$amin=$atotal= $ehr=$emin=$etotal=$asplit=$esplit=$dfrom = $dto = $tardy = $absent = $early=$years="";
if ($id) {
     $query = $this->db->query("SELECT rangefrom,rangeto,tardy,absent,early,year,sequence FROM earlydismissal  WHERE id='{$id}'");
     if ($query->num_rows()>0) {
         $dfrom = $query->row(0)->rangefrom;
         $dto = $query->row(0)->rangeto;
         $tardy = $query->row(0)->tardy;
         $absent = $query->row(0)->absent;
         $early = $query->row(0)->early;
         $years = $query->row(0)->year;
         $sequence = $query->row(0)->sequence;
     }
     $tsplit = explode(':',$tardy);
     $thr = $tsplit[0] * 60;
     $tmin = $tsplit[1];
     $ttotal = $thr + $tmin;

     $asplit = explode(':',$absent);
     $ahr = $asplit[0] * 60;
     $amin = $asplit[1];
     $atotal = $ahr + $amin;

     $esplit = explode(':',$early);
     $ehr = $esplit[0] * 60;
     $emin = $esplit[1];
     $etotal = $ehr + $emin;

}
    

    
?>

<style>
.form_row .field {
    position: relative;
    margin-left: 28%;
}

.form_row label.field_name, .form_row span.field_name {
    display: inline-block;
    float: left;
    padding-top: 5px;
    margin-bottom: 5px;
    width: 26%;
    font-weight: bolder;
}

</style>
<form id="frmsc">
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<input name="job" value="update" hidden="" />
        <div class="modal-body">
            <div class="content">
                    <div class="form_row">
                        <label class="field_name align_right">No. Sequences</label>
                        <div class="field">
                            <input class="isreq fees  form-control sequences isrequired" type="text" name="sequences" id="sequences" value="<?=$sequence?$sequence:""?>" maxlength="4" onkeypress="return numbersonly(event,this)" />
                            <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                        </div>
                    </div>
                    <div class="form_row">
                        <label class="field_name align_right">Range From</label>
                        <div class="field">
                                <input class="isreq fees  form-control from isrequired" type="text" name="from" id="from" value="<?=$dfrom?$dfrom:""?>" maxlength="4" onkeypress="return numbersonly(event,this)" />
                                <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                        </div>
                    </div>
                    <div class="form_row">
                        <label class="field_name align_right">Range To</label>
                        <div class="field">
                                <input class="isreq fees  form-control to isrequired" type="text" name="to" id="to" value="<?=$dto?$dto:""?>" maxlength="4" onkeypress="return numbersonly(event,this)" />
                                <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                        </div>
                    </div>

                    <div class="form_row">
                        <label class="field_name align_right">Tardy Start</label>
                        <div class="field">
                                <input class="isreq fees  form-control tardy_e isrequired" type="text" name="tardy_e" id="tardy_e" value="<?=$ttotal?$ttotal:""?>" maxlength="2" onkeypress="return numbersonly(event,this)" />
                                <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                        </div>
                    </div>

                    <div class="form_row">
                        <label class="field_name align_right">Absent Start</label>
                        <div class="field">
                                <input class="isreq fees  form-control absent_e isrequired" type="text" name="absent_e" id="absent_e" value="<?=$atotal?$atotal:""?>" maxlength="2" onkeypress="return numbersonly(event,this)" />
                                <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                        </div>
                    </div>

                    <div class="form_row">
                        <label class="field_name align_right">Early Dismissal Start</label>
                        <div class="field">
                                <input class="isreq fees form-control early_d isrequired" type="text" name="early_d" id="early_d" value="<?=$etotal?$etotal:""?>" maxlength="2" onkeypress="return numbersonly(event,this)" />
                                <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                        </div>
                    </div>

                    <div class="form_row">
                        <label class="field_name align_right">Year</label>
                        <div class="field">
                            <div class='input-group date' id='date_active1' data-date="" data-date-format="yyyy">
                                <input type='text' class="form-control isrequired" size="16" name="year" value="<?=$years?$years:""?>" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <span id="errormsg" hidden="" style="color: red; margin-right: 20px;"></span>
                <span id="loading" hidden=""></span>
                <span id="saving">
                    <div id="msgloads" hidden="" style="color: red;"></div>
                    <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" id="save" class="btn btn-success">Save</button>
                </span>
            </div> -->
</form>
<script>
function numbersonly(evt, myfield, e, dec, id)
{ ///< edited for cross-browser compatibility
    var key;
    var keychar;
    var e = evt || window.event;
    if (e)         key = e.which || e.keyCode;
    // else if (window.event)   key = window.event.keyCode;
    // else                return true;
    keychar = String.fromCharCode(key);
        
    // control keys
    if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;
        
    // numbers
    else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;
        
    // decimal point jump
    else if (dec && (keychar == "."))
    {
        myfield.form.elements[dec].focus();
        return false;
    }
    else    return false;
}

$(".date").datetimepicker({
    format: "YYYY"
});


$(".save-dtr-setup").unbind("click").bind("click",function(){
    // $("#save").hide();
    // $("#close").hide();
    console.log($("#frmsc").serialize());
    var iscontinue = validateForm($("#frmsc"));
    if(iscontinue){
        $.ajax({
            url: "<?=site_url("process_/earlydismissalsActions")?>",
            type: "POST",
            dataType:"json",
            data: $("#frmsc").serialize(),
            success: function(msg) {
                if (msg.err_code== 0)
                {
                     alert(msg.msg);
                     $(".modalclose").click();
                     loadlogs();
                }
                 else
                {
                    alert(msg.msg);
                    

                }
            }
        }); 
    }
});

$("#dayMode").change(function(){
	var dayMode = $(this).val();
	if(dayMode == 'whole')
	{
		$("#sc").val(1);
	}
	else
	{
		$("#sc").val(0.5);
	}
});

$(".chosen").chosen();
</script>