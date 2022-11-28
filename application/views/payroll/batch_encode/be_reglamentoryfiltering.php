<style type="text/css">
	.table-template tr th {
    background-color: #0072c6 !important;
    color:black !important;
}
</style>

            	<table class="table table-bordered table-hover table-template" id="reglamentory_table_filtered">
            		<thead>
            			<tr valign="center">
            				<th style="text-align: center; width: 10%;">Employee ID</th>
	            			<th style="text-align: center; width: 20%;">Fullname</th>
	            			<th style="text-align: center; width: 30%; min-width: 160px">Amount</th>
	            			<th style="text-align: center; width: 20%;">Schedule</th>
	            			<th style="text-align: center; width: 10%;">Status</th>
	            			<th hidden=""></th>
	            			<th hidden=""></th>
	            			<th hidden=""></th>
            			</tr>
            		</thead>
            		<tbody id='tbl_content'>
            		<?
            			$total_amount = 0;
        				foreach ($emplist as $empid => $info) {
        					$arr_code_deduction = array("sss", "philhealth", "pagibig");
        			?>
        				<tr id="tr-<?=$empid?>">
        					<td class="align_center"><?=$empid?></td>
        					<td class="align_center"><?=$info["fullname"]?></td>
        					<?
        						$arr_tag = array("new", "old");
        						foreach ($arr_tag as $tag) {
        							$isHidden = ($tag == "old") ? "hidden" : "";

        					?>		
        						<td class="align_left" tag="<?=$tag?>" tdname="amount" <?=$isHidden?>>
		        					<?
		        						foreach ($arr_code_deduction as $code_deduction) {
		        							if ($code_deduction == $reglamentoryfilter) {
		        								
		        							$key = $code_deduction ."amount";
		        							$label = strtoupper($code_deduction);

		        							if(!$isHidden){
		        								$total_amount += $info[$key];
		        					?>
				        						<table>
				        							<td><label id="<?=$code_deduction?>" class="span4"><?=$label?></label></td>
				        							<td><input type="text" class="form-control" style="text-align: right;" name="<?=$key?>" id="<?=$key?>" value="<?=number_format((int)$info[$key], 2)?>"></td>
				        						</table>
		        					<?	
		        							}else{
		        					?>
		        								<input type="text" id="<?=$key?>" value="<?=number_format((int)$info[$key], 2)?>">
		        					<?		}
		        						 }# end of if condition for filtering
		        						 else if($reglamentoryfilter == "")
		        						 {
			         							$key = $code_deduction ."amount";
			         							$label = strtoupper($code_deduction);

			         							if(!$isHidden){
			         								$total_amount += $info[$key];
			         					?>
			 		        						<p id="<?=$code_deduction?>">
			 		        							<label style="width: 20%;"><?=$label?></label>
			 		        							<input type="text" class="form-control"  style="width: 65%;float: right;display: inline;" name="<?=$key?>" id="<?=$key?>" value="<?=number_format((int)$info[$key], 2)?>">
			 		        						</p>
			         					<?	
			         							}else{
			         					?>
			         								<input  type="text" id="<?=$key?>" value="<?=number_format((int)$info[$key], 2)?>">
			         					<?		}
		        						 ?>


		        						 <?} #end of else if condition (filter equal  to null)
		        						} # end of foreach for arr_code_deduction
		        					?>
	        					</td>
	        					<td class="align_center" tag="<?=$tag?>" tdname="schedule" <?=$isHidden?>>
		        					<?
		        						foreach ($arr_code_deduction as $code_deduction) {
		        							if ($code_deduction == $reglamentoryfilter) {
		        							$key = $code_deduction ."quarter";
		        							
		        							if(!$isHidden){
		        					?>
				        						<p id="<?=$code_deduction?>">
				        							<select class="form-control" name="<?=$key?>" id="<?=$key?>">
														<?=$this->payrolloptions->quarter($info[$key],FALSE,$info['schedule'],TRUE);?>
													</select>
				        						</p>
		        					<?		}else{
		        					?>
		        								<input type="text" id="<?=$key?>" value="<?=$info[$key]?>">
		        					<?		}
		        						  }# end of if condition for filtering
		        						  else if($reglamentoryfilter == "")
		        						  {
			  	        							$key = $code_deduction ."quarter";
			  	        							
			  	        							if(!$isHidden){
			  	        					?>
			  			        						<p id="<?=$code_deduction?>">
			  			        							<select class="form-control" name="<?=$key?>" id="<?=$key?>">
			  													<?=$this->payrolloptions->quarter($info[$key],FALSE,$info['schedule'],TRUE);?>
			  												</select>
			  			        						</p>
			  	        					<?		}else{
			  	        					?>
			  	        								<input type="text" id="<?=$key?>" value="<?=$info[$key]?>">
			  	        					<?		}

		        						  } #end of else if condition (filter equal  to null)
		        						} # end of foreach for arr_code_deduction
		        					
		        					?>
	        					</td>
	        					<td class="align_center" tag="<?=$tag?>" tdname="status" <?=$isHidden?>>
	        						<?
		        						foreach ($arr_code_deduction as $code_deduction) {
		        							if ($code_deduction == $reglamentoryfilter) {
		        							$key = $code_deduction ."status";
		        					?>
			        						<p id="<?=$key?>">
			        								<?=($info[$key]) ? $info[$key] : "&nbsp;&nbsp;"?>
			        						</p>
		        					<?		
		        							} # end of if condition for filtering
		        							else if($reglamentoryfilter == "")
		        							{
		        								$key = $code_deduction ."status";
					        					?>
					        						<p id="<?=$key?>">
					        								<?=($info[$key]) ? $info[$key] : "&nbsp;&nbsp;"?>
					        						</p>
					        					<?
		        							}#end of else if condition (filter equal  to null)
		        						} # end of foreach for arr_code_deduction
		        					?>
	        					</td>
        					<?	} # end of foreach for arr_tag
        					?>
        				</tr>
        			<?
        				} # end of foreach for emplist
            		?>
            		</tbody>
            		<tfoot>
            			<tr>
            				<td class="align_right" colspan="2"><strong>Total Amount : </strong></td>
            				<td class="align_right"><strong><?=number_format($total_amount,2)?>&nbsp;&nbsp;</strong></td>
            				<td>&nbsp;</td>
            				<td>&nbsp;</td>
            			</tr>
            		</tfoot>
            	</table>
      			<br>



<script type="text/javascript">

	// $j(document).ready(function(){
	// 	    var payroll_table;

	// 	    if ( $.fn.DataTable.isDataTable('#reglamentory_table_filtered') ) {
	// 	      $('#dble').DataTable().destroy();
	// 	    }

	// 		setTimeout(function(){
	// 		          payroll_table = $("#reglamentory_table_filtered").dataTable({
	// 		        "sPaginationType": "full_numbers",
	// 		        "oLanguage": {
	// 		                         "sEmptyTable":     "No Data Available.."
	// 		                     },
	// 		        "aLengthMenu": [[-1,10, 20], ["All", 10, 20]],
	// 		        "aoColumnDefs": [ 
	// 		                { "bSortable": false, "aTargets": [ 'noSort' ] }
	// 		                ],
	// 		        scrollY:        "400px",
	// 		        scrollX:        true,
	// 		        scrollCollapse: true,
	// 		        paging:         true,
	// 		        fixedHeader: true,
	// 		        fixedColumns:   {
	// 		            leftColumns: 2
	// 		        }
	// 		    });
	// 		    $j(".DTFC_LeftBodyLiner").css({"overflow-y":"hidden","overflow-x":"hidden"});
	// 		    $j(".DTFC_RightBodyWrapper").hide();
	// 		},0);

	// 		///< for hovering Table Row(tr)
	// 		$("#reglamentory_table_filtered").on("mouseleave mouseover","tr.even, tr.odd",function(e){
	// 		    // console.log(e);
	// 		    var i = $(this).index();
	// 		    var type = e.type=="mouseover";

	// 		    $(this).toggleClass("active",type);
	// 		    //left Table or fixed columns
	// 		    $(".DTFC_Cloned > tbody").find("tr").eq(i).toggleClass("active",type);
	// 		    //right Table
	// 		    $("#dble > tbody").find("tr").eq(i).toggleClass("active",type);
	// 		 });

	// });



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

	function changeStatus(tr_id, fields_id, isUpdate){
		status = $("#"+ tr_id).find("td[tag='old']").find("#"+ fields_id).text();
		if(isUpdate) status = "UPDATED";

		$("#"+ tr_id).find("td[tag='new']").find("#"+ fields_id).text(status);
	}

	$("#tbl_content").find("select, input").change(function(){
		var tr_id = $(this).closest('tr').attr('id');
		var p_id = $(this).closest('p').attr('id');

		var isUpdate = checkIsUpdate(tr_id, p_id);

		var status_id = p_id +'status';
		changeStatus(tr_id, status_id, isUpdate);
	});


	function showLoading(isShow = false){
		if(isShow){
			$("#div_save").hide();
			$("#div_loading").show();
		}else{
			$("#div_save").show();
			$("#div_loading").hide();
		}
	}

	$("#btn_be_save").unbind("click").click(function(){
		showLoading(true);
		var isContinue = true;
		var formdata = {};
		var emplist = {};

		// loop here the all row in table..
		$("#tbl_content").find("tr").each(function(){
			// remove color..
			$(this).removeAttr('style');

			var isEmpError = false;
			var tr_split = $(this).attr("id").split("-");
			var empid = tr_split[1];
			emplist[empid] = {};
			
			for(i in arr_code_deduction){
				status_id = arr_code_deduction[i] +"status";
				var status = $(this).find("td[tag='new']").find("#"+ status_id).text();
				var code_deduction = arr_code_deduction[i]; 

				if(status == "UPDATED" && !isEmpError){
					var empInfo = {};
					
					for(tdname in arr_tdName){
						var fields_id = arr_code_deduction[i] +''+ tdname;
						var value = $(this).find("td[tag='new']").find("#"+ fields_id).val();
						empInfo[fields_id] = value;
						
						if(!value){
							isContinue = false;
							isEmpError = true;

							$(this).attr("style","background-color: #AC191994;");
						}
					}

					if(!isEmpError){
						emplist[empid][code_deduction] = empInfo;
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
			alert("No Employee updated..");
		}

		formdata ={
			emplist : emplist
		}

		if(!isContinue)  showLoading(false);
		else 			 saveBEReglamentory(formdata);
	});

	function saveBEReglamentory(formdata){
		$.ajax({
			url : "<?=site_url("payroll_/saveBEReglamentory")?>",
			type : "POST",
			data : formdata,
			success : function(result){
				showLoading(false);
				showModal(result);
			}
		});
	}

	/*function showModal(content){
		$('#other_batch_encode_modal').find('div[tag="display"]').html(content);
		$('#other_batch_encode_modal').modal('show');
	}*/

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

</script>