<?php
 $data = ($this->input->post())?$this->input->post():"";
 $cdate = "";
 $starttime = "";
 $endtime = "";
 $dayofweek = "";
 $remarks = "";
 if($data){
 	$toks = $this->input->post("toks");
 	$id = $this->gibberish->decrypt( $this->input->post("bID"), $toks );
 	$eid = $this->gibberish->decrypt( $this->input->post("eid"), $toks );
 	$remarkss = "";
 	$sql = $this->db->query("SELECT * FROM employee_schedule_adjustment WHERE id={$id}");
 	$cdate = $sql->row()->cdate;
 	$remarks = $sql->row()->remarks;
 	$utype = $this->db->query("SELECT id,request_code,description FROM code_request_type WHERE request_code={$remarks}");
 	if ($utype->num_rows() > 0) {
 	$remarkss = $utype->row()->description?$utype->row()->description:"";
 	// $remarkss = $this->extras->findRemarks($sql->row()->remarks;);
 	}
 }
 ?>
<style>
.modal{
    width:700px;
    left: 0;
    right: 0;
    margin: auto;

}
</style>
<form id="form_adjustment" method="POST" action="#" style="width: 96%">
	<!-- Date Section -->
	<div class="form_row">
	    <label class="field_name align_right">Dates</label>
	    <div class="field" style="width: 54%;">
	    	<div class='input-group date' id='datetimepicker1' id="dp2" data-date="<?=$cdate?>" data-date-format="yyyy-mm-dd" >
                <input type='text' class="form-control"  id="u_date" name="u_date" size="16" type="text" value="<?=$cdate?>" readonly/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
	    </div><br />
	</div>
	<!-- End Date Section -->

	<!-- Table Section -->
	<div class="form_row">
		<label class="field_name align_right">Time Record</label>
		<div class="field">
		<table class="table table-hover table-bordered" id="tblTimeRecord" style="width: 100%">
            <thead>
                <tr>
                   	<th class="input-small align_center" style="background-color: #0072c6;">Actual Time</th>
                    <th class="input-small align_center" style="background-color: #0072c6;">Request Time</th>
                    <th class="input-small align_center" style="background-color: #0072c6;">Status</th>
                </tr>
                <!-- <tr>
                    <th class="input-small align_center" colspan="3" id="no_result">No Result Found..</th>
                </tr> -->
            </thead>
            <tbody id="displayedTimeInOut">
            <?
            	$findAdjustment = $this->db->query("SELECT a.*,b.* FROM employee_schedule_adjustment_ext a INNER JOIN employee_schedule_adjustment b ON (a.baseID = b.id) WHERE a.baseID='$id'")->result();
            	foreach ($findAdjustment as $fa) {

            		$stats  = $fa->status;
            		if($fa->actual_time){
            			$at = explode(" - ", $fa->actual_time);
            			$ain = strtoupper(date("h:i a",strtotime($at[0])));
            			$aout = strtoupper(date("h:i a",strtotime($at[1])));
            		}
            		if($fa->final_time){
            			$at = explode(" - ", $fa->final_time);

            			$fin = $at[0] != '0000-00-00 00:00:00' ? strtoupper(date("h:i a",strtotime($at[0]))) : '(--:-- xx)';
            			$fout = $at[1] != '0000-00-00 00:00:00' ? strtoupper(date("h:i a",strtotime($at[1]))) : '(--:-- xx)';
            		}
            ?>
              	<tr style='background:<?=$fa->actual_time && $stats == "UPDATED"?"rgba(255, 99, 71, 0.4)":"" ?>'>
              		<td class="align_center"><?=$fa->actual_time  ? ($ain ." - ". $aout) :"(--:-- xx) - (--:-- xx)"?></td>
              		<td class="align_center"><?=$fa->final_time  ? ($fin ." - ". $fout) :"(--:-- xx) - (--:-- xx)"?></td>
              		<td class="align_center" ><?=$fa->actual_time && $stats == "UPDATED"?"UPDATED":"NEW" ?></td>
              	</tr>
            <?	}// end of foreach?>
            </tbody>
        </table>
		</div><br />
	</div>
	<!-- End Table Section -->

	<!-- Time Section -->
	<div class="form_row" hidden="">
	    <label class="field_name align_right">Time</label>
		<div class="field">
			<!-- Time in -->
			<div class="input-group bootstrap-timepicker">
	            <input id="u_timein" name="u_timein" class="col-md-8 input-small align_center" type="text" value="<?=$starttime?>" readonly/>
	            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
	        </div>
	        <!-- Time out -->
	        <div class="input-group bootstrap-timepicker">
	            <input id="u_timeout" name="u_timeout" class="col-md-8 input-small align_center" type="text" value="<?=$endtime?>" readonly/>
	            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
	        </div>
	        <div id="showDoneEditBtn" class="input-group">
                <a class="btn btn-primary" id="btnDoneEditTITO" value="0" onclick="" readonly><i class="icon-save"></i></a>
            </div>
		</div><br />
	</div>
	<!-- End Time Section -->

	<!-- Remarks Section -->
	<div class="form_row">
    <label class="field_name align_right">Remarks</label>
	    <div class="field" style="width: 54%;">
	        <!-- <select id="u_remarks" name="u_remarks" class="form-control"><?=$this->extras->showrequesttype($remarks)?></select> -->
	        <input id="i_remarks" class="form-control input-small align_center" type="text" value="<?=$remarkss?>" readonly/>
	    </div>
	</div>
	<!-- End Remarks Section -->

</form>
<script>
	$(document).ready(function()
	{
		// $("#no_result").show();
		// $("#displayedTimeInOut").hide();
		// $("#no_result").html("<img src='<?=base_url()?>images/loading.gif' />  Finding records, Please Wait..");
		// find here time records
/*		$.ajax({
	            url     :   "<?=site_url("process_/findTimeRecord")?>",
	            type    :   "POST",
	            data    :   {
	                            eid     : "<?=$eid?>",
	                            cdate   : $("#u_date").val()
	                        },
	            success : function(msg){

	                //alert(msg);
	                if(msg == "No result found!."){
	                	$("#no_result").show();
						$("#displayedTimeInOut").hide();
						$("#no_result").html("No Result Found..");
	                }else{
	                	$("#no_result").hide();
						$("#displayedTimeInOut").show();
						$("#displayedTimeInOut").html(msg);
	                }
	            }
	          });*/

		/*$.ajax({
		           url      :   "<?=site_url("employeemod_/displayedTITO")?>",
		           type     :   "POST",
		           data     :   {
		                            ldate : $("#u_date").val(),
		                            empID : "<?=$eid?>"
		                        },
		           success  :   function(msg){
		                if(msg == "No result found!."){
    	                	$("#no_result").show();
    						$("#displayedTimeInOut").hide();
    						$("#no_result").html("No Result Found..");
    	                }else{
    	                	$("#no_result").hide();
    						$("#displayedTimeInOut").show();
    						$("#displayedTimeInOut").html(msg);
    	                }
		           }
		        });*/

	});

/*$("#u_remarks").hide();
$("#i_remarks").val($("#u_remarks option:selected").val()"asdas");*/

$('#dp2').datepicker({
    autoclose: true
});


$('.date').datetimepicker({
	format: "YYYY-MM-DD"
}); 

$("#u_date").change(function(){
	/*$("#no_result").show();
	$("#displayedTimeInOut").hide();
	$("#no_result").html("<img src='<?=base_url()?>images/loading.gif' />  Finding records, Please Wait..");
	// find here time records
	$.ajax({
            url     :   "<?=site_url("process_/findTimeRecord")?>",
            type    :   "POST",
            data    :   {
                            eid     : "<?=$eid?>",
                            cdate   : $(this).val()
                        },
            success : function(msg){

                //alert(msg);
                if(msg == "No result found!."){
                	$("#no_result").show();
					$("#displayedTimeInOut").hide();
					$("#no_result").html("No Result Found..");
                }else{
                	$("#no_result").hide();
					$("#displayedTimeInOut").show();
					$("#displayedTimeInOut").html(msg);
                }
            }
          });*/
});


function convertTimeToNumber(time_val){
  const [time, modifier] = time_val.split(' ');

  let [hours, minutes] = time_val.split(':');

  if (hours === '12') {
    hours = '00';
  }

  if (modifier === 'PM') {
    hours = parseInt(hours, 10) + 12;
  }

  hours = parseInt(hours);
  minutes = parseInt(minutes) / 60;
  return hours + minutes;
}

// back to table
function doneEditTITO(getID){
	if($("#u_timein").val() == "" || $("#u_timeout").val() == "" || $("#u_timein").val() == undefined || $("#u_timeout").val() == undefined){
		alert("Invalid Time in - Time out!.");
		return;
	}
	var tin = tout = tID = 0;
	tID = $("#tblTimeRecord tbody").find("tr").length;
	if($("#tblTimeRecord tbody").find("tr").find("td").length > 1) tID += 1;
	tin = convertTimeToNumber($("#u_timein").val());
	tout = convertTimeToNumber($("#u_timeout").val());
	if(tin > tout){
		alert("Invalid Time in");
		return;
	}
	if(getID == '0' || $("#tblTimeRecord tbody").find("tr").find("td").length <= 1 || getID == undefined){
        //alert("1");
        $("#no_result").hide();
        $("#displayedTimeInOut").show();
        $("#displayedTimeInOut").html($("#displayedTimeInOut").html() +"<tr id=\"row-"+ tID +"a\"><td class=\"input-small align_center\" hidden>"+ tID +"a</td><td class=\"input-small align_center\" id=\"timein-"+ tID +"a\">"+ $("#u_timein").val() +"</td><td class=\"input-small align_center\" id=\"timeout-"+ tID +"a\">"+ $("#u_timeout").val() +"</td><td class=\"input-small align_center\"><a class=\"btn blue\" id=\""+ tID +"a\" onclick=\"clickEdit(this.id)\"><i class=\"icon glyphicon glyphicon-edit\"></i></a><a class=\"btn blue\" id=\""+ tID +"a\" onclick=\"clickRemove(this.id)\"><i class=\"icon glyphicon glyphicon-remove-sign\"></i></a></td></tr>");

	}else{
		if(getID == '0' || $("#tblTimeRecord tbody").find("tr").find("td").length <= 1){
        	$("#no_result").hide();
        	$("#displayedTimeInOut").html($("#displayedTimeInOut").html() +"<tr id=\"row-"+ tID +"a\"><td class=\"input-small align_center\" hidden>"+ tID +"a</td><td class=\"input-small align_center\" id=\"timein-"+ tID +"a\">"+ $("#u_timein").val() +"</td><td class=\"input-small align_center\" id=\"timeout-"+ tID +"a\">"+ $("#u_timeout").val() +"</td><td class=\"input-small align_center\"><a class=\"btn blue\" id=\""+ tID +"a\" onclick=\"clickEdit(this.id)\"><i class=\"icon glyphicon glyphicon-edit\"></i></a><a class=\"btn blue\" id=\""+ tID +"a\" onclick=\"clickRemove(this.id)\"><i class=\"icon glyphicon glyphicon-remove-sign\"></i></a></td></tr>");

        }else{

			$("#timein-"+getID).html($("#u_timein").val());
			$("#timeout-"+getID).html($("#u_timeout").val());
        }
	}
	$("#u_timein").val('');
	$("#u_timeout").val('');
	$("#btnDoneEditTITO").val('0');
}
// edit time record
function clickEdit(getID){
	//alert(getID);
	$("#u_timein").val($("#timein-"+getID).html());
	$("#u_timeout").val($("#timeout-"+getID).html());
	$("#btnDoneEditTITO").val(getID);
}

// remove time record
function clickRemove(getID){
	var res = confirm("Are you sure, you want to remove this row?");
	if(res === false) return;
	$("#row-"+getID).remove();
	if($("#tblTimeRecord tbody").find("tr").length == 0){
		$("#no_result").show();
		$("#no_result").html("No Result Found..");
	}
}

// save here
$(function(){
	/*$("#button_save_modal").unbind("click").click(function(){
	    $("#form_adjustment").submit();
	});*/
	$("#button_save_modal").click(function(){
		var cancontinue = true;
		if($("#u_date").val() == ""){
			alert("Date is required");
			return
			cancontinue = false;
		}
		if($("#tblTimeRecord tbody").find("tr").find("td").length <= 1){
			alert("Time Record is required");
			cancontinue = false;
			return;
		}
		if($("#u_remarks option:selected").val()==""){
			alert("Remarks is required");
			cancontinue = false;
			return;
		}
		if(cancontinue === true){
			var timeRecord = "";
			var tblTR = $("#tblTimeRecord").find("tbody tr");
			tblTR.each(function(){
	                     			if($(this).find("td").length>1){
	                                    timeRecord += (timeRecord?"|":"");
	                                    timeRecord += GibberishAES.enc($(this).find("td:eq(0)").text(), toks); // timesheet id
	                                    timeRecord += "~u~";
	                                    if($(this).find("td:eq(1)").text() != '') timeRecord += GibberishAES.enc($("#u_date").val() +" "+ $(this).find("td:eq(1)").text(), toks); // time in
                   								 else timeRecord += GibberishAES.enc('0000-00-00 00:00:00', toks);
	                                    timeRecord += "~u~";
	                                    if($(this).find("td:eq(2)").text() != '') timeRecord += GibberishAES.enc($("#u_date").val() +" "+ $(this).find("td:eq(2)").text(), toks);// time out
                    					else timeRecord += GibberishAES.enc('0000-00-00 00:00:00', toks);
	                                }
	                             });
			$.ajax({
					 url 	 : "<?=site_url("process_/saveManageDTR")?>",
					 type 	 : "POST",
					 data    : {
					 				eid 		: GibberishAES.enc("<?=$id?>", toks),
					 				cdate 		: GibberishAES.enc($("#u_date").val(), toks),
					 				time_record : timeRecord,
					 				remarks 	: GibberishAES.enc($("#u_remarks option:selected").val(), toks),
					 				toks: toks
					 		   },
					 success : function(msg){
					 		alert(msg);
					 		ulist.fnDraw();
                			$("#modalclose").click();
					 }
			});
		}
	});
});


</script>