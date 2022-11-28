<?php  
/**
* @author justin (with e)
* @copyright 2018
* 
* >  for mcu-hyperion 21479
* @Angelica - copied for ICA-Hyperion21533
*/
/** COPIED BY PAULO HYPERION21534 03-21-2018 FROM ICADTR**/

$total_amount = 0;
foreach ($emplist as $empid => $info) {

	$total_amount += $info["amount"];
?>

	<tr id="row-<?=$empid?>">
		<td style="text-align: center;width:  166.01px;"  ><?=$empid?></td>
		<td style="text-align: left;"><?=$info['fullname']?></td>
		<td style="text-align: center;" tag="new">
			<div class="input-append date datefrom" data-date="" data-date-format="yyyy-mm-dd">
            	<input size="16" class="align_center datete required" type="text" name="datefrom" id="datefrom" value="<?=$info['datefrom']?>" readonly>
                <span class="add-on"><i class="icon-calendar"></i></span>
            </div>
		</td>
		<td style="text-align: center;" tag="new">
			<input class='numbersonly amount' type="text" name="amount" id="amount" value="<?=$info["amount"]?>" style="text-align: right;">
		</td>
		<td style="text-align: center;" tag="new">
			<input class='numbersonly nocutoff' type="text" name="nocutoff" id="nocutoff" value="<?=$info["nocutoff"]?>" style="text-align: right;">
		</td>
		<td style="text-align: center;" tag="new">
			<select class="cutoff_period" name="cutoff_period" id="cutoff_period">
				<?=$this->payrolloptions->quarter($info['cutoff_period'],FALSE,$info['schedule'],TRUE);?>
			</select>
		</td>
		<td tag="new_status" style="text-align: center;">
			<div id="status"><?=$info['status']?></div>
		</td>
	

		<!-- old data ni employee.. -->
		<td style="text-align: center;" tag="old" hidden>
			<div class="input-append date datefrom" data-date="" data-date-format="yyyy-mm-dd">
            	<input size="16" class="align_center required" type="text" name="datefrom" id="datefrom" value="<?=$info['datefrom']?>" readonly>
                <span class="add-on"><i class="icon-calendar"></i></span>
            </div>
		</td>
		<td style="text-align: center;" tag="old" hidden>
			<input type="text" name="amount" id="amount" value="<?=$info["amount"]?>" style="text-align: right;">
		</td>
		<td style="text-align: center;" tag="old" hidden>
			<input type="text" name="nocutoff" id="nocutoff" value="<?=$info["nocutoff"]?>" style="text-align: right;">
		</td>
		<td style="text-align: center;" tag="old" hidden>
			<select name="cutoff_period" id="cutoff_period">
				<?=$this->payrolloptions->quarter($info['cutoff_period'],FALSE,$info['schedule'],TRUE);?>
			</select>
		</td>
		<td tag="old_status" hidden>
			<div id="status"><?=$info['status']?></div>
		</td>
	</tr>
<?	
} # end of foreach..
?>

<script src="<?=base_url()?>jsbstrap/jquery-1.12.4.js"></script>
<script type="text/javascript">
	var $j = jQuery.noConflict();
	$j(document).ready(function(){
		    var payroll_table;

		    if ( $.fn.DataTable.isDataTable('#dble') ) {
		      $('#dble').DataTable().destroy();
              oTable.fnAdjustColumnSizing();
		    }

			setTimeout(function(){
			          payroll_table = $("#dble").dataTable({
			        "sPaginationType": "full_numbers",
			        "oLanguage": {
			                         "sEmptyTable":     "No Data Available.."
			                     },
			        "aLengthMenu": [[10], [10]],
			        "aoColumnDefs": [ 
			                { "bSortable": false, "aTargets": [ 'noSort' ] }
			                ],
			        scrollY:        "400px",
			        scrollX:        true,
			        scrollCollapse: true,
			        paging:         true,
			        fixedHeader: true,
			        fixedColumns:   {
			            leftColumns: 2
			        }
			    });
			    $j(".DTFC_LeftBodyLiner").css({"overflow-y":"hidden","overflow-x":"hidden"});
			    $j(".DTFC_RightBodyWrapper").hide();
			},0);

			///< for hovering Table Row(tr)
			$("#dble").on("mouseleave mouseover","tr.even, tr.odd",function(e){
			    // console.log(e);
			    var i = $(this).index();
			    var type = e.type=="mouseover";

			    $(this).toggleClass("active",type);
			    //left Table or fixed columns
			    $(".DTFC_Cloned > tbody").find("tr").eq(i).toggleClass("active",type);
			    //right Table
			    $("#dble > tbody").find("tr").eq(i).toggleClass("active",type);
			 });

			///< select employees to include

	});



	$("#td_total_amount").html("<strong><?=number_format($total_amount,2)?></strong>");

	$("#tbl_content").find("input, select").change(function(){
		var tr_id = $(this).closest('tr').attr('id');
		checkRowFields(tr_id);
		var checkamount = $("tr[id='"+ tr_id +"']").find(".amount").val();
		var checkdatefrom = $("tr[id='"+ tr_id +"']").find(".datete").val();
		var checknocutoff = $("tr[id='"+ tr_id +"']").find(".nocutoff").val();
		var checkcutoff_period = $("tr[id='"+ tr_id +"']").find(".cutoff_period").val();
		if((checkamount == "" || checkdatefrom == "" || checknocutoff == "" || checkcutoff_period == "")){
				 $("tr[id='"+ tr_id +"']").css({'background-color':'#ff6666'});
			}
		if((checkamount != "" && checkdatefrom != "" && checknocutoff != "" && checkcutoff_period != "")){
				 $("tr[id='"+ tr_id +"']").css({'background-color':'#99ff99'});
				 setTimeout("saveBEDeduction()", 2000);
				 
		}
	});

	// pang check ng row..
	function checkRowFields(tr_id){
		var field_id = old_val = new_val = "";
		var isChanged = false;

		$("tr[id='"+ tr_id +"']").find("td[tag='new']").each(function(){
			field_id = $(this).find("input, select").attr("id");

			new_val = $(this).find("#"+ field_id).val();
			old_val = $("tr[id='"+ tr_id +"']").find("td[tag='old']").find("#"+ field_id).val();

			if(old_val != new_val) isChanged = true;			
		});

		if(isChanged){
			$("tr[id='"+ tr_id +"']").find("td[tag='new_status']").find("#status").html("UPDATED");
		}else{
			var status = "";
			status = $("tr[id='"+ tr_id +"']").find("td[tag='old_status']").find("#status").html();
			$("tr[id='"+ tr_id +"']").find("td[tag='new_status']").find("#status").html(status);
		}
	}


	// number only..
	$(".numbersonly").bind("change keyup input", function () {
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

    function saveBEDeduction(){ 
		$("#div_save").hide();
		// $("#div_loading").show();

		var emp_list  = {};
		var error_emp = {};

		// get data here
		$("#tbl_content").find("tr").each(function(){
			// $(this).removeAttr('style');

			var status = $(this).find("td[tag='new_status']").find("#status").html();
			if(status == "UPDATED"){
				var split_tr = $(this).attr('id').split("-");
				var isContinue = true;
				var empid = split_tr[1];

				var empInfo = {};
				$(this).find("td[tag='new']").each(function(){
					var field_id = $(this).find("input, select").attr("id");
					var value = $(this).find("#"+ field_id).val();

					if(value && isContinue) empInfo[field_id] = value;
					else{
						isContinue = false;
						//alert(field_id +" = "+ value);
					} 					
				});

				// $(this).removeAttr('style');
				if(isContinue) emp_list[empid] = empInfo; // good data
				else{
					error_emp[empid] = "* "+ empid +" - Incomplete information.."; // bad data
					$("#tbl_content").find("#"+ $(this).attr("id")).attr("style","background-color: #AC191994;");
				}
			} 	
		});
		
		// check if has error
		if(Object.keys(error_emp).length > 0){
			$("#div_save").show();
			$("#div_loading").hide();
			return;
		}

		var formdata = {
			emp_list : (Object.keys(emp_list).length > 0) ? emp_list : 0,
			error_emp : (Object.keys(error_emp).length > 0) ? error_emp : 0,
			code_deduc : $("select[name='code_deduc']").val()
		};

		// do save here..
		$.ajax({
			url : "<?=site_url("payroll_/saveBEDeduction")?>",
			type : "POST",
			data : formdata,
			success : function(respond){
				// $('#be_modal').find('div[tag="display"]').html(respond);

				// $j('#be_modal').modal('show');
				// $j('.modal-backdrop').css('z-index','90');

				// findEmployee({
				// 	deptid : "<?=$deptid?>",
				// 	employmentstat : "<?=$employmentstat?>",
				// 	code_deduc : $("select[name='code_deduc']").val(),
				// 	schedule : $("select[name='schedule']").val()
				// });
			}

		});
	}

	$('.date').datepicker({
	 autoclose: true,
	 todayBtn : true
	});
	
</script>