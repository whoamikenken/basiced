var toks = hex_sha512(" ");
$('#submit').click(function(){
	var user = $("#username").val();
	var codeincome = $('select[name="code_type"]').val();
	var form_data  = new FormData();
	var file_data = $("#userfile").prop("files")[0]
	form_data.append('file',file_data);
	form_data.append('code_type', codeincome);
	form_data.append('user', user);
	var url = $("#site_url").val() + "be_income_upload/uploadData";
	if( document.getElementById("userfile").files.length == 0 ){
		$('#loadingbar').html('');
	    Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please upload file first.',
            showConfirmButton: true,
            timer: 2000
        });
	}else{ 
        $.ajax({
            url : url,  // Controller URL
            type : 'POST',
            data: form_data,
            dataType: "json",
            contentType : false,
            processData : false,
            success : function(response) {
	            $('#loadingbar').html('');
	            if(response != ""){
					var result = "<br><p style='color:green'><b>There are </b>" +response.noEmp+ " data that is not inserted.</p><p style='color:red'>Employee ID: " +response.empId+ "<p><b>Reason:</b> Employee ID is not existing.";    
					$('#be_modal').find('div[tag="display"]').html(result);
					$('#be_modal').modal('show');
					$('.modal-backdrop').css('z-index','90');  
	            }else{
	            	var result = "All data in inserted and updated";
	            	$('#be_modal').find('div[tag="display"]').html(result);
					$('#be_modal').modal('show');
					$('.modal-backdrop').css('z-index','90');  
	            }  
	        }
       	});
	}
});

$("#be_deduction").find("input, select").change(function(){
	var tr_id = $(this).closest('tr').attr('id');
	var checkamount = $("tr[id='"+ tr_id +"']").find(".amount").val();
	var checkdatefrom = $("tr[id='"+ tr_id +"']").find(".datete").val();
	var checknocutoff = $("tr[id='"+ tr_id +"']").find(".nocutoff").val();
	var checkcutoff_period = $("tr[id='"+ tr_id +"']").find(".cutoff_period").val();
	if((checkamount == "" || checkdatefrom == "" || checknocutoff == "" || checkcutoff_period == "")){
			 $("tr[id='"+ tr_id +"']").css({'background-color':'#ff6666'});
		}
	if((checkamount != "" && checkdatefrom != "" && checknocutoff != "" && checkcutoff_period != "")){
			 $("tr[id='"+ tr_id +"']").css({'background-color':'#99ff99'});
			 saveBEDeduction(checkcutoff_period);
			 
	}
});

$('#be_deduction tbody').on('change', '.amount', function () {
	var amount = $(this).val();
	// amount = parseInt(amount);
	var new_amount = 0;
	if(amount % 1 != 0) new_amount = amount;
	else new_amount = parseInt(amount).toFixed(2);
	var tr_id = $(this).closest('tr').attr('id');
	$("tr[id='"+ tr_id +"']").find(".amount").val(new_amount);
});

$("#ClrDeduction").click(function(){
	const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to clear all 0 cutoff deduction?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
        var formdata = { toks:toks, tblname:GibberishAES.enc("employee_deduction", toks) };
		$.ajax({
			url : $("#site_url").val() + "/payroll_/clearZeros",
			type : "POST",
			data : formdata,
			success : function(respond){
				Swal.fire({
		            icon: 'success',
		            title: 'Success!',
		            text: 'Successfully clear 0 cutoff.',
		            showConfirmButton: true,
		            timer: 2000
		        });
				loadBatchEncodeEmployee();
			}
		});
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Deduction data is safe.',
         'error'
       )
     }
   });
});

$("#be_deduction").dataTable({
    "pagination": "number",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    // "scrollY": 1000,
    "scrollX": true
});

$('#be_deduction').on('draw.dt', function () { 
    $(".date").datetimepicker({
    	format: "YYYY-MM-DD"
	});

	$(".clearRow").unbind().click(function(){
		var tr_id = $(this).closest('tr').attr('id');
		var codeDeduction = $("#codededuc").val();
		$("tr[id='"+ tr_id +"']").find("#datefrom").val("");
		$("tr[id='"+ tr_id +"']").find(".amount").val("");
		$("tr[id='"+ tr_id +"']").find(".datete").val("");
		$("tr[id='"+ tr_id +"']").find(".nocutoff").val("");
		$("tr[id='"+ tr_id +"']").find(".cutoff_period").val("");
		deleteBEDeduction(tr_id, codeDeduction);
	});
});

$(".clearRow").unbind().click(function(){

	const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to clear this row?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
        var tr_id = $(this).closest('tr').attr('id');
		var codeDeduction = $("#codededuc").val();
		$("tr[id='"+ tr_id +"']").find("#datefrom").val("");
		$("tr[id='"+ tr_id +"']").find(".amount").val("");
		$("tr[id='"+ tr_id +"']").find(".datete").val("");
		$("tr[id='"+ tr_id +"']").find(".nocutoff").val("");
		$("tr[id='"+ tr_id +"']").find(".cutoff_period").val("");
		deleteBEDeduction(tr_id, codeDeduction);
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Income data is safe.',
         'error'
       )
     }
   })

});
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();

function deleteBEDeduction(empid = '', code = ''){
	var formdata = {
		toks: toks,
		empid : GibberishAES.enc(empid, toks),
		code  : GibberishAES.enc(code, toks)
	};

	$.ajax({
		url : $("#site_url").val() + "/payroll_/BEdeleteDeduction",
		type : "POST",
		data : formdata,
		success : function(respond){

		}
	});

	Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Successfully clear this row.',
        showConfirmButton: true,
        timer: 2000
    });
}


function saveBEDeduction(checkcutoff_period){
	var emp_list  = {};
	var error_emp = {};
	var sched = "semimonthly";
	/*if(sched == 1) sched = "weekly";
	else if(sched == 2) sched = "semimonthly";
	else sched = "monthly";*/
	// get data here
	$("#be_deduction").find("tr").each(function(){
		var isContinue = true;
		var empid = $(this).attr('id');
		var empInfo = {};
		$(this).find("td[tag='new']").each(function(){
			var field_id = $(this).find("input, select").attr("id");
			var value = $(this).find("#"+ field_id).val();
			if(value) empInfo[field_id] = GibberishAES.enc(value, toks);
			else isContinue = false;

			if(isContinue) emp_list[empid] = empInfo;
		});
	});
	

	var formdata = {
		emp_list : (Object.keys(emp_list).length > 0) ? emp_list : 0,
		error_emp : (Object.keys(error_emp).length > 0) ? error_emp : 0,
		code_deduc : GibberishAES.enc($("select[name='code_type']").val(), toks),
		sched:GibberishAES.enc(sched, toks),
		toks: toks
	};

	// do save here..
	$.ajax({
		url : $("#site_url").val() + "/payroll_/saveBEDeduction",
		type : "POST",
		data : formdata,
		success : function(respond){

		}
	});
}

