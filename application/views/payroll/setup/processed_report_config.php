<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

$CI =& get_instance();
$CI->load->model('utils');
?>
<style>
.two-col {
    -webkit-column-count: 2; /* Chrome, Safari, Opera */
    -moz-column-count: 2; /* Firefox */
    column-count: 2;
}


div.options > label > input {
    visibility: hidden;
}

div.options > label {
    /*display: block;*/
    margin: 0 0 0 -10px;
    padding: 0 0 20px 0;  
    height: 10px;
    /*width: 150px;*/
    
}

div.options > label > img {
    display: inline-block;
    padding: 0px;
    height:20px;
    width:20px;
    background: none;
}

div.options > label > input:checked +img {  
    background: url("<?=base_url()?>images/greencheck.png?>");
    background-repeat: no-repeat;
    background-position:center center;
    background-size:20px 20px;
}




</style>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Setup</h4>
        </div>
        <div class="modal-body">
            <div>

        		<form id="configform">
        			<input type="hidden" name="reportname" value="<?=$reportname?>">

	            	<?if($reportname == 'payrollsummary'){?>
	            		<div class="options">
	            		    <label title="PDF">
	            		        <input type="radio"  name="reportformat" value="pdf" checked="" /> 
	            		        <img />
	            		      	PDF
	            		   </label>
	            		    <label title="XLS">
	            		        <input type="radio" name="reportformat" value="xls" />
	            		        <img />
	            		        XLS
	            		    </label>   
	            		</div>

		            	<div class="two-col">
		            		<div>
		            			<strong>Deminimiss</strong><br>
		            			<?
		            				foreach ($deminimiss as $key => $row) {?>
		            					<input type="checkbox" name="deminimiss[]" value="<?=$key?>"><?=$row['description']?><br>
		            				<?}
		            			?>

		            		</div>

		            		<div>
		            			<strong>Others</strong><br>
		            			<?
		            				foreach ($others as $key => $row) {?>
		            					<input type="checkbox" name="other[]" value="<?=$key?>"><?=$row['description']?><br>
		            				<?}
		            			?>
		            		</div>
		            	</div>
	            	<?}elseif($reportname == 'atmpayrolllist'){?>
	            		<div class="form_row">
	            		    <label class="field_name align_right">Bank</label>
	            		    <div class="field">
		            			<select class="form-control" name="emp_bank">
		            				 <?
	                                  $opt_type = $CI->utils->getBankList("Select bank");
	                                  foreach($opt_type as $c=>$val){
	                                  ?><option value="<?=$c?>"><?=$val?></option><?    
	                                  }
	                                ?>
		            			</select>
	            			</div>
	            		</div>
	            		<div class="form_row">
	            		    <label class="field_name align_right">Status</label>
	            		    <div class="field">
			            		<select class="form-control" name="reportformat">
			            			<option value="">Select an option</option>
		            				<option value="PDF">PDF</option>
		            				<option value="XLS">EXCEL</option>
		            			</select>
		            		</div>
		            	</div>
	            		<div class="form_row" style="display: none;">
	            		    <label class="field_name align_right">Report Type</label>
	            		    <div class="field">
			            		<select class="form-control" name="emp_status">
		            				<option value="saved">SAVED</option>
		            				<option value="processed">PROCESSED</option>
		            			</select>
		            		</div>
		            	</div>
	            	<?}?>
	            </form>



			</div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="gen" class="btn btn-primary">Generate</button>
            </div>
        </div>
    </div>
</div>


<script>
	$('#gen').on('click',function(){

		if(!$("select[name='emp_bank']").val()){
			Swal.fire({
	            icon: 'warning',
	            title: 'Warning!',
	            text: 'Please Select a bank.',
	            showConfirmButton: true,
	            timer: 2000
	        });

	        return;
		}

		var form_data = $('#configform').serialize();
		form_data = '?'+form_data;
		form_data += "&deptid=<?=$deptid?>";
		form_data += "&office=";
		form_data += "&employeeid=<?=$employeeid?>";
		form_data += "&payrollcutoff=<?=$payrollcutoff?>";
		form_data += "&schedule=<?=$schedule?>";
		form_data += "&quarter=<?=$quarter?>";
		
		console.log(form_data);
		window.open("<?=site_url("payroll_/loadPayrollReport")?>"+form_data);
		return;
		$.ajax({
			url 	: "<?=site_url("payroll_/loadPayrollReport")?>",
			type 	: "POST",
			data 	: form_data,
			success : function(msg){

			}
		});

	});

</script>