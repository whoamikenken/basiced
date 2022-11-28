<?
$datetoday = date("Y-m-d");

?>

<style type="text/css">
	
	.form_row{
		padding-bottom: 15px;
	}

	.loan_form{
		margin-top: 25px;
	}
div.modal-content{

	width: 100%;
}

</style>

<form id="loan_frm" class="loan_form" style="margin-right: 30px;">
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">Based on</label>
	    <div class="col-md-7">
			<select id="based_on" name="based_on" class="form-control">
			<?=$based_on?>
			</select>
	    </div>
	</div>
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">Loan</label>
	    <div class="col-md-7">
			<select id="code_loan" name="code_loan" tag="Loan" class="form-control" <?=($is_modify) ? "disabled" : ""?>>
				<?=$code_loan?>
			</select>
	    </div>
	</div>
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">Deduction Date</label>
	    <div class="col-md-7">
			<div class='input-group date' data-date="<?=($deduction_date)? $deduction_date:$datetoday ?>" data-date-format="yyyy-mm-dd">
                <input type='text' class="form-control" size="16" name="deduction_date" value="<?=($deduction_date)? $deduction_date:$datetoday ?>" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
	    </div>
	</div>
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">Starting Balance</label>
	    <div class="col-md-7">
			<input class="form-control col-md-8" type="text" id="start_balance" name="start_balance" value="<?=$start_balance?>" <?=($is_modify) ? "readonly" : ""?> />
	    </div>
	</div>
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">No. of Cut-Off</label>
	    <div class="col-md-7">
			<input class="form-control col-md-8" tag="No. Cut-Off" type="text" id="no_cutoff" name="no_cutoff" value="<?=$no_cutoff?>" />
	    </div>
	</div>
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">Amount</label>
	    <div class="col-md-7">
			<input class="form-control col-md-8" type="text" id="amount" name="amount" value="<?=$amount?>" readonly />
	    </div>
	</div>
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">Schedule</label>
	    <div class="col-md-7">
			<select id="schedule" name="schedule" class="form-control" disabled>
				<?=$schedule?>
			</select>
	    </div>
	</div>
	<div class="form_row">
	    <label class="col-sm-3 control-label text-right">Quarter</label>
	    <div class="col-md-7">
			<select id="quarter" name="quarter" class="form-control">
				<?=$quarter?>
			</select>
	    </div>
	</div>
</form>

<script type="text/javascript">
var toks = hex_sha512(" ");
function basedOnFunction(based_on, is_first_load = false){
	if(!is_first_load){
		$("#amount").val('');
		$("#no_cutoff").val('');
	}

	switch(based_on){
		case '0':
			$("#amount").attr("readonly","true");
			$("#no_cutoff").removeAttr("readonly");
			$("#amount").attr("style","background-color:#E8E8E8");
			$("#no_cutoff").attr("style","background-color:#FFFFFF");
			break;

		case '1':
			$("#no_cutoff").attr("readonly","true");
			$("#amount").removeAttr("readonly");
			$("#no_cutoff").attr("style","background-color:#E8E8E8");
			$("#amount").attr("style","background-color:#FFFFFF");
			break;

		default: break;
	}
}

$("#button_save_modal").click(function(){
    if ($(this).change("click") == true) {
         $("#amount")

    }
});

basedOnFunction($("#based_on").val(), true);
$("#based_on").change(function(){
	basedOnFunction(this.value);
});

function getFormData(){
	var formdata = "";

	$("#loan_frm").find("input, select").each(function(){
		if($(this).attr("name")){
			formdata += (formdata) ? "&" : "";
			formdata += $(this).attr("name") +"="+ this.value;
		}
	});

	return formdata;
}

function validateForm(formdata){
	var is_continue = true;
	var id = value = "";

	form_list = formdata.split("&");
	for(key in form_list){
		[id, value] = form_list[key].split("=");

		if(!value){
			is_continue = false;
			error_msg = $("#"+ id).attr("tag") +" is Required..";
			$("#"+ id).css("border", "1px solid red");
			break;
		}else{
			$("#"+ id).css("border", "1px solid #ccc");
		}
	}

	if(!is_continue){
		Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: error_msg,
              showConfirmButton: true,
              timer: 1000
          })
	} 
	return is_continue;
}

$("#button_save_modal").unbind("click").click(function(){
	var formdata = getFormData();
	var is_continue = validateForm(formdata);

	if(is_continue){
		formdata +="&employeeid=<?=$employeeid?>";
		formdata +="&id=<?=$id?>";

		$.ajax({
			url : "<?=site_url("loan_/saveEmployeeLoan")?>",
			type : "POST",
			data : {formdata:GibberishAES.enc(formdata , toks), toks:toks},
			success : function(result){
				Swal.fire({
		              icon: 'success',
		              title: 'success!',
		              text: result,
		              showConfirmButton: true,
		              timer: 1000
		          })
				loadLoanPage();
				$("#modalclose").click();
				$(".nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover").click();
			}
		});
	}
});

$("#start_balance, #no_cutoff, #amount").bind("change keyup input", function () {
	if($(this).attr("name") == "userfile") return;
	var position = this.selectionStart - 1;
	//remove all but number and .
	var fixed = this.value.replace(/[^0-9\.]/g, '');
	if (fixed.charAt(0) === '.')                  //can't start with .
	    fixed = fixed.slice(1);

	var pos = fixed.indexOf(".") + 1;
	if (pos >= 0)               //avoid more than one .
	    fixed = fixed.substr(0, pos) + fixed.slice(pos).replace('.', '');

	if (this.value !== fixed) {
	    this.value = fixed;
	    this.selectionStart = position;
	    this.selectionEnd = position;
	}
});



$("#start_balance, #no_cutoff, #amount").bind("change keyup input", function () {
	var amount_cutoff = 0;
	var start_balance = ($("#start_balance").val()) ? $("#start_balance").val() : 0;
	var no_cutoff 	  = ($("#no_cutoff").val()) ? $("#no_cutoff").val() : 0;
	var amount 		  = ($("#amount").val()) ? $("#amount").val() : 0;

	var display_tag = "";
	if($("#based_on").val() == '0'){
		amount_cutoff = start_balance / no_cutoff;
		display_tag = "#amount";
	}

	if($("#based_on").val() == '1'){
		amount_cutoff = start_balance / amount;
		display_tag = "#no_cutoff";
	}

	amount_cutoff = (amount_cutoff == "Infinity" || amount_cutoff == "NaN") ? 0 : amount_cutoff;

	if($("#based_on").val() == '1' && !!(amount_cutoff % 1)) amount_cutoff = parseInt(amount_cutoff) + 1;
	$(display_tag).val(amount_cutoff);

	/*var amount = 0;
	var start_balance = $("#start_balance").val();
	var no_cutoff = ($("#no_cutoff").val()) ? $("#no_cutoff").val() : 0;

	if(start_balance && no_cutoff) amount = start_balance / no_cutoff;

	$("#amount").val(amount);*/
});

$(".date").datetimepicker({
format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script>
