 $(document).ready(function(){
    var payroll_table;

	// $("#reglamentoryTable").dataTable({
	//     "pagination": "number",
	//     "oLanguage": {
	//                      "sEmptyTable":     "No Data Available.."
	//                  },
	//     "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
	//     "pagelength": 10,
	//     "scrollY": false,
	//     "scrollX": true
	// });

	///< for hovering Table Row(tr)
	$("#reglamentory_table").on("mouseleave mouseover","tr.even, tr.odd",function(e){
	    // console.log(e);
	    var i = $(this).index();
	    var type = e.type=="mouseover";

	    $(this).toggleClass("active",type);
	    //left Table or fixed columns
	    $(".DTFC_Cloned > tbody").find("tr").eq(i).toggleClass("active",type);
	    //right Table
	    $("#dble > tbody").find("tr").eq(i).toggleClass("active",type);
	 });

});


	$("#reglamentory, #schedule").change(function(){
		$.ajax({
		    url : $("#site_url").val() + "/batch_encode_/loadPayrollBatchEncodeFilter",
		    type : "POST",
		    data : {
		    	reglamentory : $(this).val(),
		        deptid : $("select[name=deptid]").val(),
		        schedule : $("select[name=schedule]").val(),
		        employmentstat : $("select[name=employmentstat]").val()
		    },
		    success : function(msg){
		    	console.log(msg);
		        $('#reglamentory_table').html(msg);
		    }
		    
		});
	});
	var arr_tdName = {
		amount   : "input",
		quarter : "select"
	};

	var arr_code_deduction = ["sss", "philhealth", "pagibig"];

	function checkIsUpdate(tr_id, p_id){
		var isUpdate = false;
		var new_value = old_value = "";

		for(key in arr_tdName){
			fields_id = p_id +''+ key;
			new_value = $("#"+ tr_id).find("td[tag='new']").find("#"+ fields_id).val();
			old_value = $("#"+ tr_id).find("td[tag='old']").find("#"+ fields_id).val();	 
			
			if(new_value != old_value){
				isUpdate = true;
				break;
			}
		}

		return isUpdate;
	}

	$(".clear").click(function(){
		var tr_id = $(this).closest('tr').attr('id');
		var tr_ = $(this).closest('tr');
	    var windowMessage = confirm("Do you want to cleared the selected payments? This action can't undo.");

	    if (windowMessage == true) {
	        $(tr_).find("td[tag='new']").find("#sssamount").val('0');
			$(tr_).find("td[tag='new']").find("#philhealthamount").val('0');
			$(tr_).find("td[tag='new']").find("#pagibigamount").val('0');
			$(tr_).find("td[tag='new']").find("#sssquarter").val('').trigger("liszt:updated");
			$(tr_).find("td[tag='new']").find("#philhealthquarter").val('').trigger("liszt:updated");
			$(tr_).find("td[tag='new']").find("#pagibigquarter").val('').trigger("liszt:updated");
			$(tr_).find("td[tag='new']").find("#sssstatus").text('CLEARED').css("color", "red");
			$(tr_).find("td[tag='new']").find("#philhealthstatus").text('CLEARED').css("color", "red");
			$(tr_).find("td[tag='new']").find("#pagibigstatus").text('CLEARED').css("color", "red");
			$(tr_).find("td[tag='new']").find("#regstatus").text('CLEARED').css("color", "red");
            setTimeout(saveBEReglamentoryDeduction(tr_id), 1000);
	    }
	    else {
	    	alert("Action Canceled");
	    }
	});

	function changeStatus(tr_id, fields_id, isUpdate){
		status = $("#"+ tr_id).find("td[tag='old']").find("#"+ fields_id).text();
		if(isUpdate) status = "UPDATED";

		$("#"+ tr_id).find("td[tag='new']").find("#"+ fields_id).text(status);
	}

	$("#reglamentory_table").find("select, input").change(function(){
		var tr_id = $(this).closest('tr').attr('id');
		var p_id = $(this).closest('p').attr('id');

		var checksss = $("#"+ tr_id).find(".sssamount").val();
		var checkphilhealth = $("#"+ tr_id).find(".philhealthamount").val();
		var checkpagibig = $("#"+ tr_id).find(".pagibigamount").val();

		checksss = parseInt(checksss);
		checkphilhealth = parseInt(checkphilhealth);
		checkpagibig = parseInt(checkpagibig);

		newsss = checksss.toFixed(2);
		newphilhealth = checkphilhealth.toFixed(2);
		newpagibig = checkpagibig.toFixed(2);

		$("#"+ tr_id).find(".sssamount").val(newsss);
		$("#"+ tr_id).find(".philhealthamount").val(newphilhealth);
		$("#"+ tr_id).find(".pagibigamount").val(newpagibig);

		if((checksss == "" || checkphilhealth == "" || checkpagibig == "")){
          $("#" + tr_id).css({'background-color':'#ff6666'});
        }
    	if((checksss != "" && checkphilhealth != "" && checkpagibig != "")){
       	  $("#" + tr_id).css({'background-color':'#99ff99'});
          setTimeout(saveBEReglamentoryDeduction(tr_id), 3000);
         
  		}

		var isUpdate = checkIsUpdate(tr_id, p_id);

		var status_id = p_id +'status';
		changeStatus(tr_id, status_id, isUpdate);
	});

	$("#reglamentory_table").find("input").change(function(){
		var amount = $(this).val();
		amount = parseInt(amount);
		$(this).val(amount.toFixed(2));
	});


	function showLoading(isShow = false){
		if(isShow){
			// $("#div_save").hide();
			// $("#div_loading").show();
		}else{
			// $("#div_save").show();
			// $("#div_loading").hide();
		}
	}

	function saveBEReglamentoryDeduction(tr_id=""){
		showLoading(true);
		var isContinue = true;
		var formdata = {};
		var emplist = {};
		var emplError = {};

		// loop here the all row in table..
		$("#reglamentory_table tbody").find("tr").each(function(){
			// remove color..
			// $(this).removeAttr('style');

			var isEmpError = false;
			var tr_split = $(this).attr("id").split("~");
			var empid = tr_split[1];
			emplist[empid] = {};
			if(tr_id == $(this).attr("id")){
				for(i in arr_code_deduction){
					status_id = arr_code_deduction[i] +"status";
					var status = $(this).find("td[tag='new']").find("#"+ status_id).text();
					var code_deduction = arr_code_deduction[i]; 

					if(!isEmpError){
						var empInfo = {};
						
						for(tdname in arr_tdName){
							var fields_id = arr_code_deduction[i] +''+ tdname;
							var value = $(this).find("td[tag='new']").find("#"+ fields_id).val();
							empInfo[fields_id] = value;
							
							if(!value){
								$(this).attr("style","background-color: #AC191994;");
							}else{
								$(this).attr("style","background-color: #99ff99;");
							}
						}

						if(!isEmpError){
							emplist[empid][code_deduction] = empInfo;
						}else{
							emplError[empid] = "Error";
						}
					}
				}	
			}
		});


		isContinue = false;
		for(empid in emplist){
			if(Object.keys(emplist[empid]).length > 0){
				isContinue = true;
				break;
			}
		}

		if(!isContinue){
			if(Object.keys(emplError).length == 0){
				alert("No employee updated..");
			}
		}
		
		formdata ={
			emplist : emplist
		}

		if(!isContinue)  showLoading(false);
		else 			 saveBEReglamentory(formdata);
	}

	function saveBEReglamentory(formdata){
		$.ajax({
			url : $("#site_url").val() + "/payroll_/saveBEReglamentory",
			type : "POST",
			data : formdata,
			success : function(result){
			}
		});
	}

	function showModal(content){
		$('#other_batch_encode_modal').find('div[tag="display"]').html(content);
		$('#other_batch_encode_modal').css('z-index','100000000').modal('show');
		$('.modal-backdrop').css('z-index','90');
	}

	// number only..
	$("input").bind("change keyup input", function () {
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
	$('.chosen').chosen();