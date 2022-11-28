<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
?>	
<div class="col-md-10 col-md-offset-1">
	<form class="form-horizontal" id="web-setup-manage">
		<input type="hidden" name="action" id="action" value="<?= $tag ?>">
		<input type="hidden" id="employeeid" value="<?= $employee ?>">
		<input type="hidden" id="ident" name="id" value="<?= $id ?>">
		<div class="form-group">
		    <label class="col-sm-3 control-label">Status</label>
		    <div class="col-sm-9">
		    	<select class="chosen form-control" name="status" <?= ($tag == "add")? "":"disabled" ?>>
                    <option value="active" <?= ($status == "active")? "selected":"" ?>>Active</option>
                    <option value="in-active" <?= ($status == "inactive")? "selected":"" ?>>In-Active</option>
                </select>
		    </div>
		</div>
		<div class="form-group">
		    <label class="col-sm-3 control-label">Department</label>
		    <div class="col-sm-9">
		    	<select class="chosenManage" id="deptidManage" name="deptid" <?= ($tag == "add")? "":"disabled" ?>><?=$this->extras->getDeptpartment($deptid)?></select>
		    </div>
		</div>
		<div class="form-group">
		    <label class="col-sm-3 control-label">Office</label>
		    <div class="col-sm-9">
		    	<select class="chosenManage" id="officeManage" name="office" <?= ($tag == "add")? "":"disabled" ?>><?=$this->extras->getOffice($office)?></select>
		    </div>
		</div>
		<div class="form-group">
		    <label class="col-sm-3 control-label">Employee</label>
		    <div class="col-sm-9">
		      <select class="chosen form-control" name="employee" id="setupEmployeeSelect" <?= ($tag == "add" || $tag == "batch")? "multiple":"disabled" ?> >
	          </select>
		    </div>
		</div>
		<?php 
		if ($tag != 'batch'):
		?>
		<div class="form-group">
		    <label class="col-sm-3 control-label">Date From</label>
		    <div class="col-sm-9">
		      	<div class='input-group date' data-date-format="yyyy-mm-dd">
	                <input type='text' class="form-control" size="16" id="from" name="date_from" type="text" value="<?= $date_from ?>" <?= ($tag == "add")? "":"disabled" ?>/>
	                <span class="input-group-addon">
	                      <span class="glyphicon glyphicon-calendar"></span>
	                </span>
	            </div>
		    </div>
		</div>
		<?php 
		endif 
		?>
		<div class="form-group">
		    <label class="col-sm-3 control-label">Date To</label>
		    <div class="col-sm-9">
		      	<div class='input-group date' data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" size="16" name="date_to" id="to"  type="text" value="<?= $date_to ?>"/>
                    <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
		    </div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	    employeeListManage("","<?= $tag ?>");
	    $(".date").datetimepicker({
		    format: "YYYY-MM-DD"
		});

		$(".chosen").chosen();
		$(".chosenManage").chosen();
	});

	$('#button_save_modal').unbind('click').bind('click', function (e) {
		
		if ($("input[name='employee']").val() == "" && $("#action").val() != 'batch') {
			alert("Please Select Employee");
			return false;
		}
		if ($("#from").val() == "" && $("#action").val() != 'batch') {
			alert("Please Select Date From");
			return false;
		}
		if ($("#to").val() == "") {
			alert("Please Select Date To");
			return false;
		}
			var formdata = $("#web-setup-manage").serialize()+ "&employeeList="+ $("#setupEmployeeSelect").val()+"&ldfrom="+$("#ldfroms").val()+"&ldto="+$("#ldto").val();

	        $.ajax({
	        type: "POST",
	        url: $("#site_url").val() + "/webcheckin_/saveSetup",
	        data: {formdata:GibberishAES.enc(formdata, toks), type:GibberishAES.enc(type, toks), toks:toks},
	        success:function(response){
	        	response = $.trim(response);
				if(response == "transaction"){
	            	Swal.fire({
				        icon: 'warning',
				        title: 'Warning!',
				        text: 'Date selected already has transactions on weblogin.',
				        showConfirmButton: true,
				        timer: 1500
				    })
	            }else if(response == "date"){
	            	Swal.fire({
				        icon: 'warning',
				        title: 'Warning!',
				        text: 'Date selected already has existing setup.',
				        showConfirmButton: true,
				        timer: 1500
				    })
	            }else if(response == "added." || response == "updated."){
	            	Swal.fire({
				        icon: 'success',
				        title: 'Success!',
				        text: 'Setup is successfully '+ response,
				        showConfirmButton: true,
				        timer: 1500
				    })
				    setTimeout(function() {
				    	webSetup();
	            		$('#modal-view').modal('toggle');
				    }, 1600);
	            }else{
	            	Swal.fire({
				        icon: 'warning',
				        title: 'Warning!',
				        text: response+' already have an existing setup.',
				        showConfirmButton: true,
				        timer: 10000
				    })
	            }
	            
	        }
	        });
	  
	});

	$(".chosenManage").change(function(){
   		employeeListManage("", "select");
	});

	function employeeListManage(type, selected){
	    $.ajax({
	        url: $("#site_url").val() + "/webcheckin_/loadEmployeeListDropdownm",
	        type: "POST",
	        data:{type:type, office:$("#officeManage").val(), deptid:$("#deptidManage").val(), action:$("#action").val(), id:$("#employeeid").val(), selected:selected},
	        success:function(response){
	            $("#setupEmployeeSelect").html(response);
	            $("#setupEmployeeSelect").trigger("chosen:updated");
	        }
	    });
	}

</script>