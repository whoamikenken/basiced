<?php
 // $data = ($this->input->post())?$this->input->post():"";
 $cdate = "";
 $starttime = "";
 $endtime = "";
 $dayofweek = "";
 $remarks = "";
 $datetoday = "";

// $webSetup = '';

// $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$uid' AND STATUS = 'active'");
// if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;

 ?>
<style>
.modal{
    width:700px;
    left: 0;
    right: 0;
    margin: auto;

}

.swal2-cancel{
    margin-right: 20px;
}

@media (min-width: 992px){
  .modal-lg {
      width: 600px;
  }
}
</style>
<form id="form_adjustment" method="POST" action="#" style="width: 96%">
	<!-- Date Section -->
	<div class="form_row">
    <label class="field_name align_right">Dates</label>
    <div class="col-sm-7">
      <div class='input-group date' name="date" data-date="<?=date("Y-m-d",strtotime($cdate))?>" data-date-format="YYYY-MM-DD">
          <input type='text' class="form-control isrequired" size="16" id="cdate" name="cdate" value="<?=$datetoday?>" />
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
          <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
      </div>
    </div><br />
</div>
 
	<!-- <div class="form_row">
	    <label class="field_name align_right">Date</label>
	    <div class="field">
	        <div class="input-group date" id="dp2" data-date="<?=$cdate?>" data-date-format="yyyy-mm-dd">
	            <input class="align_center cdate" id="cdate" name="cdate" size="16" type="text" value="<?=$cdate?>">
	            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
	        </div>
	    </div>
	</div> -->
	<!-- End Date Section -->

	<!-- Table Section -->
	<div class="form_row">
		<label class="field_name align_right">Time Record</label>
		<div class="field">
		<table class="table table-hover table-bordered" id="tblTimeRecord" style="width: 100%">
            <thead>
                <tr>
                   	<th class="input-small align_center" style="background-color: #0072c6;">TIME IN</th>
                    <th class="input-small align_center" style="background-color: #0072c6;">TIME OUT</th>
                    <th class="input-small align_center" style="background-color: #0072c6;">ACTIONS</th>
                </tr>
                <tr>
                    <th class="input-small align_center" colspan="3" id="no_result">No Result Found..</th>
                </tr>
            </thead>
            <tbody id="displayedTimeInOut">
                                <!-- displayed the time in, out and Edit -->
            </tbody>
        </table>
		</div>
	</div>
	<!-- End Table Section -->

	<!-- Time Section -->
	<div class="form_row">
	    <label class="field_name align_right">Time In</label>
		<div class="field">
			<!-- Time in -->
			<div class="col-md-5" style="padding-left: 0px;">
				<div class='input-group time'>
                    <input type='text' class="form-control" id="u_timein" name="u_timein" value="<?=$starttime?>"/>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
			</div>
			<!-- Time out -->
			<div class="col-md-1" style="padding: 2px;">
				<label>Out</label>
			</div>
			<div class="col-md-5">
				<div class='input-group time'>
                    <input type='text' class="form-control" id="u_timeout" name="u_timeout" value="<?=$endtime?>" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
			</div>
			
			<div class="col-md-1" style="padding-left: 0px;">
	        	<div id="showDoneEditBtn" class="input-group">
		            <a class="btn btn-primary" id="btnDoneEditTITO" value="0" onclick="doneEditTITO(this.value)"><i class="icon-save"></i></a>
		        </div>
			</div>
		</div><br/>
	</div><br>
	<!-- End Time Section -->

	<!-- Remarks Section -->
	<div class="form_row">
    <label class="field_name align_right">Remarks</label>
	    <div class="field no-search" style="width: 64%;">
	        <select id="u_remarks" name="u_remarks" class="form-control">
	        	<option value="">All Remarks</option><?=$this->extras->showrequesttype($remarks)?></select>
	    </div>
	<!-- <a class="btn blue pull-right" href="#modal-view" data-toggle='modal' href="#" id="addremarks"><i class="glyphicon glyphicon-plus-sign"></i> New Remarks</a> -->
	</div>
	<!-- End Remarks Section -->
	

</form>
<script>
var toks = hex_sha512(" ");
$(document).ready(function () {
	var today = new Date();
	$(".date").datetimepicker({
	    format: "YYYY-MM-DD",
	    maxDate: today
	});

    $('[name="date"]').keyup(function () {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9^-]/g, '');
        }
    });
});

// $('#dp2').datetimepicker({
//     autoclose: true
// });

$('.time').datetimepicker({
	format: 'LT'
});
function removeAllrow(){
    $( "#tblTimeRecord tbody tr" ).each( function(){
        this.parentNode.removeChild( this ); 
    });
}
$("#cdate").blur(function(){
	$("#no_result").show();
	$("#displayedTimeInOut").hide();
	$("#no_result").html("<img src='<?=base_url()?>images/loading.gif' />  Finding records, Please Wait..");
	// find here time records
	$.ajax({
            url     :   "<?=site_url("process_/findTimeRecord")?>",
            type    :   "POST",
            data    :   {
                            eid     :  GibberishAES.enc("<?=$uid?>", toks),
                            cdate   : GibberishAES.enc($(this).val(), toks),
                            toks:toks
                        },
            success : function(msg){
            	// console.log(msg);
                removeAllrow();

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
          });
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
	/*if($("#u_timein").val() == "" || $("#u_timeout").val() == "" || $("#u_timein").val() == undefined || $("#u_timeout").val() == undefined){
		alert("Invalid Time in - Time out!.");
		return;
	}*/
	var cancontinue = true;
	var tin = tout = tID = extin = extout = 0;
	tID = $("#tblTimeRecord tbody").find("tr").length;
	if($("#tblTimeRecord tbody").find("tr").find("td").length > 1) tID += 1;
	if($("#u_timein").val() != "" && $("#u_timeout").val() != ""){
			tin = convertTimeToNumber($("#u_timein").val());
			tout = convertTimeToNumber($("#u_timeout").val());
			if(tin >= tout){
				Swal.fire({
	                icon: 'warning',
	                title: 'Warning!',
	                text: 'Invalid Time In or Time Out',
	                showConfirmButton: true,
	                timer: 1000
	            });
				return;
			}
	}else{
		Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Invalid Time In or Time Out',
            showConfirmButton: true,
            timer: 1000
        });
		return;
	}
	
	if(getID == '0' || $("#tblTimeRecord tbody").find("tr").find("td").length <= 1 || getID == undefined){
		//newly added by glenmark
		$("#tblTimeRecord tbody").find("tr").each(function()
		{
			extin = convertTimeToNumber($(this).find("td:eq(1)").text());
			extout = convertTimeToNumber($(this).find("td:eq(2)").text());
			// alert($("#u_timein").val());
			// if ($("#u_timein").val() < $(this).find("td:eq(1)").text() && $("#u_timeout").val() > $(this).find("td:eq(2)").text() ) {
			// 	alert("Time you have inputted was already on the table...");
			// 	cancontinue = false;
			// 	return;
			// }

			if((tin > extin && tin < extout) || (tout > extin && tout < extout) || (tin == extin && tout == extout)){
				Swal.fire({
	                icon: 'warning',
	                title: 'Warning!',
	                text: 'Time in and out already exists.',
	                showConfirmButton: true,
	                timer: 1000
	            });
	            cancontinue = false;
				return;
			}
			
				
		})
		if (cancontinue === true) {
			$("#no_result").hide();
			$("#displayedTimeInOut").show();
			$("#displayedTimeInOut").html($("#displayedTimeInOut").html() +"<tr id=\"row-"+ tID +"a\"><td class=\"input-small align_center\" hidden>"+ tID +"a</td><td class=\"input-small align_center\" id=\"timein-"+ tID +"a\">"+ $("#u_timein").val() +"</td><td class=\"input-small align_center\" id=\"timeout-"+ tID +"a\">"+ $("#u_timeout").val() +"</td><td class=\"input-small align_center\"><a class=\"btn btn-info\" id=\""+ tID +"a\" onclick=\"clickEdit(this.id)\"><i class=\"icon glyphicon glyphicon-edit\"></i></a><a class=\"btn btn-danger\" id=\""+ tID +"a\" onclick=\"clickRemove(this.id)\"><i class=\"icon glyphicon glyphicon-remove-sign\"></i></a></td></tr>");

			Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Time In and Time Out has been saved successfully',
                showConfirmButton: true,
                timer: 1000
            });
			
		}
			
        // alert("1");
        

	}
	else{
		if(getID == '0' || $("#tblTimeRecord tbody").find("tr").find("td").length <= 1){
        	$("#no_result").hide();
        	$("#displayedTimeInOut").html($("#displayedTimeInOut").html() +"<tr id=\"row-"+ tID +"a\"><td class=\"input-small align_center\" hidden>"+ tID +"a</td><td class=\"input-small align_center\" id=\"timein-"+ tID +"a\">"+ $("#u_timein").val() +"</td><td class=\"input-small align_center\" id=\"timeout-"+ tID +"a\">"+ $("#u_timeout").val() +"</td><td class=\"input-small align_center\"><a class=\"btn blue\" id=\""+ tID +"a\" onclick=\"clickEdit(this.id)\"><i class=\"icon glyphicon glyphicon-edit\"></i></a><a class=\"btn blue\" id=\""+ tID +"a\" onclick=\"clickRemove(this.id)\"><i class=\"icon glyphicon glyphicon-remove-sign\"></i></a></td></tr>");
        }else{

			$("#timein-"+getID).html($("#u_timein").val());
			$("#timeout-"+getID).html($("#u_timeout").val());
        }

        Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Time In and Time Out has been updated successfully',
                showConfirmButton: true,
                timer: 1000
            });
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
	const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
      },
      buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, proceed!',
      cancelButtonText: 'No, cancel!',
      reverseButtons: true
    }).then((result) => {
      if (result.value) {
        $("#row-"+getID).remove();
		if($("#tblTimeRecord tbody").find("tr").length == 0){
			$("#no_result").show();
			$("#no_result").html("No Result Found..");
		}
		Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Time In and Time Out has been deleted successfully',
                showConfirmButton: true,
                timer: 1000
            });
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire(
          'Cancelled',
          'Data is safe.',
          'error'
        )
      }
    })
}

// save here
$(function(){
	/*$("#button_save_modal").unbind("click").click(function(){
	    $("#form_adjustment").submit();
	});*/
	$("#button_save_modal").unbind('click').click(function(){
	
		// $("#employeelist tr").each(function()
		// 			 		{
		// 			 			if ($(this).attr('employeeid') == "<?=$uid?>") {
		// 			 				alert($("#employeelist .<?=$uid?>");
						 			
		// 			 			}
		// 			 		});
		// return;
		var cancontinue = true;
		if ($("input[name='u_timein']").val() != "" || $("input[name='u_timeout']").val() != ""  ) {
			Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: "Please save the Time In and Time Out first",
                  showConfirmButton: true,
                  timer: 1000
              })
			return;
			cancontinue = false;
		}
		if($("#cdate").val() == ""){
			Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: "Date is required",
                  showConfirmButton: true,
                  timer: 1000
              })
			return;
			cancontinue = false;
		}
		// if($("#u_timein").val() != "" || $("#u_timeout").val() != ""){
		// 	alert("You must saved the Time In/Out First!.");
		// 	return;
		// 	cancontinue = false;
		// }
		if($("#tblTimeRecord tbody").find("tr").find("td").length <= 1){
			Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: "Time Record is required",
                  showConfirmButton: true,
                  timer: 1000
              })
			cancontinue = false;
			return;
		}
		if($("#u_remarks option:selected").val()==""){
			Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: "Remarks is required",
                  showConfirmButton: true,
                  timer: 1000
              })
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
                    if($(this).find("td:eq(1)").text() != '') timeRecord += GibberishAES.enc($("#cdate").val() +" "+ $(this).find("td:eq(1)").text(), toks); // time in
                    else timeRecord += GibberishAES.enc('0000-00-00 00:00:00', toks);
                    timeRecord += "~u~";
                    if($(this).find("td:eq(2)").text() != '') timeRecord += GibberishAES.enc($("#cdate").val() +" "+ $(this).find("td:eq(2)").text(), toks);// time out
                    else timeRecord += GibberishAES.enc('0000-00-00 00:00:00', toks);
                }
             });

			$.ajax({
				 url 	 : "<?=site_url("process_/saveManageDTR")?>",
				 type 	 : "POST",
				 data    : {
				 				eid 		: GibberishAES.enc("<?=$uid?>", toks), 
				 				cdate 		: GibberishAES.enc($("#cdate").val(), toks),
				 				time_record : timeRecord,
				 				remarks 	: GibberishAES.enc($("#u_remarks option:selected").val(), toks),
				 				toks		: toks
				 		   },
				 success : function(msg){
				 	$("#employeelist tr").each(function()
				 		{
				 			if ($(this).attr('employeeid') == "<?=$uid?>") {
					 			$("#employeelist .<?=$uid?>").click();
				 			}
				 		});
				 		Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Time In and Time Out has been saved successfully',
                          showConfirmButton: true,
                          timer: 1000
                      })
				 		ulist.fnDraw();
				 		loadAddjustment();
            			$("#modalclose").click(); 		
				}
			});
		}
	});
});

$(".chosen").chosen();
</script>