
var sep_categ = "salary, regdeduc, regpayment";
var toks = hex_sha512(" ");
$(document).ready(function(){
	// loadDepartmentSelection();
	loadEmpStatusSelection();
	loadCategorySelection();
    loadCategorySelection2();
});
$("#be_search").on('click',function(){
    $('#schedule').val( $('#schedule').prop('defaultSelected')).trigger("chosen:updated");
    if($("#category").val() == "income"){
            $("#income_categ").show();
            $("#deduc_categ").hide();
            $("#DeducClr").hide();
            $("#LoanClr").hide();
            $("#IncomeClr").show();
            $("#loan_categ").hide();
            $(".categorytype").text("Income Category:");
        }else if($("#category").val() == "deduction"){
            $("#deduc_categ").show();
            $("#income_categ").hide();
            $("#DeducClr").show();
            $("#IncomeClr").hide();
            $("#LoanClr").hide();
            $("#loan_categ").hide();
            $(".categorytype").text("Deduction Category:");
        }else{
            $("#deduc_categ").hide();
            $("#income_categ").hide();
            $("#loan_categ").show();
            $("#DeducClr").hide();
            $("#LoanClr").show();
             $("#IncomeClr").hide();
            $(".categorytype").text("Loan Category:");
        }
});
$("#be_search").on('click',function(){
	$("#encode_body").hide();
	$("#wrapListEncode").html('');
    var categ = $("#category").val();
    if(sep_categ.includes(categ)){
    	loadBatchEncodeEmployee();
    	$("#type_div,#sched_div").hide();
    }else{
    	loadTypeSelection(categ);
    	$("#type_div,#sched_div").show();
    }
});
$("#batch_process").click(function(){
    var category = $("#category").val();
    var deptid = $("select[name=deptid]").val();
    var office = $("select[name=office]").val();
    var campus = $("select[name=campus]").val();
    var teachingtype = $("select[name=teachingtype]").val();
    var employmentstat = $("select[name=employmentstat]").val();
    var status = $("select[name=status]").val();
    /*if(!category){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please select a category!",
            showConfirmButton: true,
            timer: 2000
        });
        return;
    }*/
    $.ajax({
        url:    $("#site_url").val() + "/setup_/loadBatchEncodeModal",
        type:   "POST",
        data:   {toks:toks,category: GibberishAES.enc(category, toks),deptid: GibberishAES.enc(deptid, toks),office: GibberishAES.enc(office, toks),campus: GibberishAES.enc(campus, toks), status: GibberishAES.enc(status, toks), employmentstat: GibberishAES.enc(employmentstat, toks), teachingtype: GibberishAES.enc(teachingtype, toks)},
        success:function(response){
            $("#encode_process").html(response);
            $("#encode_process").modal('toggle');
        }
    });
});
$("select[name=deptid], select[name=employmentstat], select[name=code_type], select[name=schedule], #office, #teachingtype, #status, #campus").on('change',function(){
   	loadBatchEncodeEmployee();
});

$("#income-modal").click(function(){
    var category = $("#category").val();
    var deptid = $("select[name=deptid]").val();
    var office = $("select[name=office]").val();
    var campus = $("select[name=campus]").val();
    var teachingtype = $("select[name=teachingtype]").val();
    var employmentstat = $("select[name=employmentstat]").val();
    var status = $("select[name=status]").val();
    
    $.ajax({
        url:    $("#site_url").val() + "/setup_/laodYearEndIncomeModal",
        type:   "POST",
        data:   {toks:toks,category: GibberishAES.enc(category, toks),deptid: GibberishAES.enc(deptid, toks),office: GibberishAES.enc(office, toks),campus: GibberishAES.enc(campus, toks), status: GibberishAES.enc(status, toks), employmentstat: GibberishAES.enc(employmentstat, toks), teachingtype: GibberishAES.enc(teachingtype, toks)},
        success:function(response){
            $("#encode_process").html(response);
            $("#encode_process").modal('toggle');
        }
    });
});

$('.chosen').chosen();

function loadDepartmentSelection(){
	$.ajax({
		url: $("#site_url").val() + "/batch_encode_/loadDepartmentList",
		success:function(response){
			$("select[name='deptid']").html(response).trigger("chosen:updated");
		}
	})
}

function loadEmpStatusSelection(){
	$.ajax({
		url: $("#site_url").val() + "/batch_encode_/loadEmploymentStatusList",
		success:function(response){
			$("select[name='employmentstat[]']").html(response).trigger("chosen:updated");
		}
	})
}

function loadCategorySelection(){
	$.ajax({
		url: $("#site_url").val() + "/batch_encode_/loadBatchEncodeCategory",
		success:function(response){
			// $("select[name='process_category']").html(response).trigger("chosen:updated");
            $("select[name='category']").html(response).trigger("chosen:updated");
		}
	})
}

function loadCategorySelection2(){
    $.ajax({
        url: $("#site_url").val() + "/batch_encode_/loadBatchEncodeCategory2",
        success:function(response){
            $("select[name='process_category']").html(response).trigger("chosen:updated");
            // $("select[name='category']").html(response).trigger("chosen:updated");
        }
    })
}

function loadTypeSelection(code){
	$("#encode_body").show();
	$.ajax({
		url: $("#site_url").val() + "/batch_encode_/loadBatchEncodeTypeSelection",
		type: "POST",
		data: {toks:toks,code:GibberishAES.enc(code, toks)},
		success:function(response){
			$("select[name='code_type']").html(response).trigger("chosen:updated");
            loadBatchEncodeEmployee()
		}
	});
}

function loadBatchEncodeEmployee(){
	var loading = $("#loading").html();
	var categ = $("#category").val();
	var deptid = $("select[name=deptid]").val();
    var office = $("#office").val();
    var tnt = $("#teachingtype").val();
    var campus = $("#campus").val();
    var status = $("#status").val();
	var employmentstat = $("select[name=employmentstat]").val();
	var code_type = $("select[name=code_type]").val();
    var schedule = $("select[name=schedule]").val();
    var reglamentory = $("select[name=reglamentory]").val();
	var cutoff = $("select[name=cutoff]").val();
	// if(!code_type && !sep_categ.includes(categ)) return;
	// $('#wrapListEncode').html(loading);

	$.ajax({
        url : $("#site_url").val() + "/batch_encode_/loadPayrollBatchEncode",
        type : "POST",
        data : {
            toks : toks,
            category : GibberishAES.enc(categ, toks),
            deptid : GibberishAES.enc(deptid, toks),
            employmentstat : GibberishAES.enc(employmentstat, toks),
            code_type : GibberishAES.enc(code_type, toks), 
            schedule : GibberishAES.enc(schedule, toks),
            campus:GibberishAES.enc(campus, toks),
            status:GibberishAES.enc(status, toks),
            tnt:GibberishAES.enc(tnt, toks),
            reglamentory:GibberishAES.enc(reglamentory, toks),
            cutoff:GibberishAES.enc(cutoff, toks),
            office:GibberishAES.enc(office, toks)
        },
        success : function(msg){
            $('#wrapListEncode').html(msg);
            if($("#canwrite").val() == 0) $("#wrapListEncode").find("input, .btn, select").css("pointer-events", "none");
            else $("#wrapListEncode").find("input, .btn, select").css("pointer-events", "");
        }
    });


}

    $("#department").change(function(){
        $.ajax({
            url : $("#site_url").val() + "/setup_/getOffice",
            type: "POST",
            data: {toks:toks, department:GibberishAES.enc($(this).val(), toks)},
            success: function(msg){
                $("#office").html(msg).trigger("chosen:updated");
            }
        });
    })