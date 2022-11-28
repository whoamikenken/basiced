<?
	$otherIncome = $this->input->post("otherIncome");
?>

<div class="well blue">
	<div class="well-header">
		<h5>Other Income History</h5>
	</div>
	<div class="well-content"> 
		<table class="table table-striped table-bordered table-hover datatable" id="table">
			<thead>
				<tr style="background-color:#343434;color:white">
					<th style="text-align:center" hidden="">Timestamp</th>
					<th style="text-align:center">Fullname</th>
					<th style="text-align:center">Employee</th>
					<th style="text-align:center">Monthly</th>
					<th style="text-align:center">Daily</th>
					<th style="text-align:center">Hourly</th>
					<th style="text-align:center">Effectivity Date</th>
					<th style="text-align:center">End Date</th>
					<th style="text-align:center" >Timestamp</th>

					<th style="text-align:center">Status</th>
					<th style="text-align:center">Edited By</th>
				</tr>
			</thead>
			<tbody>
				<?
					$emplist = $this->employee->emplistWithOtherIncomeHistory($otherIncome);

					if($emplist->num_rows() >0)
					{
						foreach($emplist->result() as $row)
						{
							#echo '<pre>';print_r($row);
							?>
							<tr id='information'>
								<td style="text-align:center" hidden=""><?=$row->timestamp  == "1970-01-01"?"":$row->timestamp?></td>
								<td style="text-align:center"><?=$row->employeeid?></td>
								<td style="text-align:center"><?=$this->employee->getfullname($row->employeeid)?></td>
								<td style="text-align:center"><?=$row->monthly?></td>
								<td style="text-align:center"><?=$row->daily?></td>
								<td style="text-align:center"><?=$row->hourly?></td>
								<td style="text-align:center"><?=$row->dateEffective == "1970-01-01"?"":date("F d, Y",strtotime($row->dateEffective))?></td>
								<td style="text-align:center"><?=$row->dateEnd == "1970-01-01" || $row->dateEnd == "" || $row->dateEnd == NULL?"":date("F d, Y",strtotime($row->dateEnd))?></td>
								<td style="text-align:center" ><?=$row->timestamp  == "1970-01-01"?"":$row->timestamp?></td>
								
								<td style="text-align:center"><?=$row->status?></td>
								<td style="text-align:center"><?=$row->appliedby?></td>
							<!-- 	<td style="text-align:center">
									<a href="#" employeeid="<?=$row->employeeid?>" othIncome="<?=$otherIncome?>" data-target='#myModalatts' class="btn btn-info editbtn" href="#" data-toggle="modal"><span class="glyphicon glyphicon-edit"></span></a>
									<a href="#" employeeid="<?=$row->employeeid?>" othIncome="<?=$otherIncome?>" class="btn btn-danger delbtn" href="#modal-view" data-toggle="modal"><span class="glyphicon glyphicon-trash"></span></a>
								</td> -->
							</tr>
						<?
						}
					}
					else
					{
						?>
							<tr>
								<td colspan="10" style="text-align:center">No data exist !</td>
							</tr>
						<?
					}
				?>
			</tbody>
		</table>
		
	</div>
</div>


<!-- <div class="modal fade" id="myModalatts" data-backdrop="static"></div> -->
<script>
	$(".delbtn").click(function(){
		$(".greyHistory").click();
		$(".grey").click();
		$(".greyedit").click();
		var deleteConfirm = confirm("Do you want to delete this?");
		if(deleteConfirm)
		{
			var employeeid = $(this).attr("employeeid");
			var othIncome = $(this).attr("othIncome");
			
			$.ajax({
				type : "POST",
				url : "<?=site_url("process_/deleteOtherIncome")?>",
				data : {
					employeeid : employeeid,
					othIncome : othIncome
				},
				success : function(msg){
					alert(msg);
					$("select[name='othincome_drop']").trigger("change");
				}
			});
		}
    });

    $(".editbtn").unbind('click').click(function()
    {	
    	$(".greyHistory").click();
		$(".grey").click();
		$(".greyedit").click();
    	var employeeid = $(this).attr("employeeid");
    	var othIncome = $(this).attr("othIncome");
    	var formdata = {
    		employeeid: employeeid,
    		otherIncome : othIncome
    	}
    	$.ajax({
			url: "<?=site_url("process_/editOtherIncome")?>",
			type:"POST",
			data : formdata,
			success :function(msg)
			{
				// alert(msg);
				$("#myModalatts").find('.modal-body').html(msg);
				$("#myModalatts").show();	
			}
    	});
    });

    $(function()
    {
    	 $('#table').dataTable(
    	 	{
    	 		"aoColumns": [
    	 						{ "bSortable": true },
    	 						{ "bSortable": false },
    	 						{ "bSortable": false },
    	 						{ "bSortable": false },
    	 						{ "bSortable": false },
    	 						{ "bSortable": false },
    	 						{ "bSortable": false },
    	 						{ "bSortable": false },
    	 						{ "bSortable": true },
    	 						{ "bSortable": false },
    	 						{ "bSortable": false },
    	 					],
    	 		"sPaginationType": "full_numbers",
 		        "oLanguage": {
 		                       "sEmptyTable":     "No Data Available.."
 		                   },
 		        "aLengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]]
    	 	});
    });





</script>