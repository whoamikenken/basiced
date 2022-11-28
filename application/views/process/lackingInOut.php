<?
///< @modified Angelica - Hyperion21532
// $datetoday = date("Y-m-t");
$date_filter_to = isset($date_filter_to) ? $date_filter_to : date("Y-m-t");
$date_filter_from = isset($date_filter_from) ? $date_filter_from : date("Y-m-01");
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#lackingInOut{
	padding-bottom: 50px;
}
</style>
<div class="panel animated fadeIn">
   <div class="panel-heading"><h4><b>Lacking of In/Out</b></h4></div>
   <div id="lackingInOut" class="panel-body">
   	<div class="col-md-12">
   		<div class="col-md-4">
   			<div class="form_row" >
				<label>Type :</label>
				<select id="reportType" class="form-control" style="width: 60%;display: unset;">
					<option value="ABSENT" <?=($reportType=="ABSENT")?"selected":""?>>Absent</option>
					<option value="FAILURETOLOG" <?=($reportType=="FAILURETOLOG")?"selected":""?>>Failure to Log</option>
				</select>
			</div>
   		</div>
   		<div class="col-md-6">
   			<div class="form-group">
                <label class="col-md-1 align_right">Date :</label>
                <div class="col-md-11">
                	<div class="col-md-5">
	                    <div class="input-group date" data-date="<?= isset($date_filter_from) ? $date_filter_from : '' ?>" data-date-format="yyyy-mm-dd" placeholder="Date" style="width: 86%;">
	                        <input type="text" name="date_filter_from" class="form-control" value="<?= isset($date_filter_from) ? $date_filter_from : '' ?>"/>
	                        <span class="input-group-addon">
	                            <span class="glyphicon glyphicon-calendar"></span>
	                        </span>
	                    </div>
	                </div>
	                <div class="col-md-5">
	                    <div class="input-group date" data-date="<?= isset($datetoday) ? $datetoday : '' ?>" data-date-format="yyyy-mm-dd" placeholder="Date" style="width: 86%;">
	                        <input type="text" name="date_filter_to" class="form-control" value="<?= isset($date_filter_to) ? $date_filter_to : '' ?>"/>
	                        <span class="input-group-addon">
	                            <span class="glyphicon glyphicon-calendar"></span>
	                        </span>
	                    </div>
	                </div>
                </div>
            </div>
   		</div>
   		<div class="col-md-2">
   			
   		</div>
   	</div>

	<table class="table table-striped table-bordered table-hover" id='tbl'>
		<thead>
			<tr style="background-color: #0072c6;">
				<th>Employee ID</th>
				<th>Employee Name</th>
				<th>Date</th>
				<th>Time In</th>
				<th>Time Out</th>
			</tr>
		</thead>
		<tbody>
		<?
			foreach($attendance_list as $deptid => $emplist){
				foreach ($emplist as $employeeid => $datelist) {
					foreach ($datelist as $date => $perday) {
						if($date_filter_to >= $date && $date_filter_from <= $date){
							$isAbsent = false;
							foreach ($perday['detail'] as $key => $persched_info) {

								if($persched_info['isAbsent']) $isAbsent = true;
								if($isAbsent){
									$login = isset($persched_info['login']) ? $persched_info['login'] : '';
									$logout = isset($persched_info['logout']) ? $persched_info['logout'] : '';

									if( ($reportType == 'ABSENT' && !$login && !$logout) || ($reportType == 'FAILURETOLOG' && ((!$login && $logout) || ($login && !$logout))) ){
										?>
										<tr>
											<td class='align_center'><?=$employeeid?></td>
											<td class='align_center'><?=Globals::_e($emplist_detail[$employeeid]['fullname'])?></td>
											<td class='align_center'><?=date('M d, Y',strtotime($date))?></td>
											<td class='align_center'><?=$login?date('h:i A',strtotime($login)):'-- : --'?></td>
											<td class='align_center'><?=$logout?date('h:i A',strtotime($logout)):'-- : --'?></td>
										</tr>
										<?

										break;
									}
								}
							}
						}
							
					}
				}
			}
		?>
		</tbody>
	</table>
</div>
<script>
	$("#reportType").change(function(){
		var reportType = $(this).val();
		var datefrom = $("input[name='date_filter_from']").val();
		var dateto = $("input[name='date_filter_to']").val();
		loadLackInOut(reportType, datefrom, dateto);
	});

	$("input[name='date_filter_from'], input[name='date_filter_to']").blur(function(){
		var datefrom = $("input[name='date_filter_from']").val();
		var dateto = $("input[name='date_filter_to']").val();
		var reportType = $("#reportType").val();
		loadLackInOut(reportType, datefrom, dateto);
});
	
	///OLD
	function loadTbl(reportType){
		$("#displaylogs").html("Loading, please wait...");
		 $.ajax({
			url: "<?=site_url("process_/showLackInOut")?>",
			type: "POST",
			data: {reportType : reportType},
			success: function(msg) {
				$("#displaylogs").html(msg);
				$("#cutofflist").html(msg);
			}
		});   
		return false;  
	}
	
	$(document).ready(function() {
		var ulist = $("#tbl").dataTable({
			"sPaginationType": "full_numbers",
			order: [[2, 'asc']],
			"oLanguage": {
							 "sEmptyTable":     "No Data Available.."
						 },
			"aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
		});
	});

	// $(".date").datetimepicker({
	//     format: "YYYY-MM-DD"
	// });

	 $(".date").datetimepicker({
        format: "YYYY-MM-DD",
        useCurrent: true, 
        minDate: '<?=$dfrom?>',
        maxDate: '<?=$dto?>'
    });


</script>