<div id="removeAni" class="panel">
   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Overload Employee List</b></h4></div>
   <div class="panel-body">
       <table class="table table-striped table-bordered table-hover" id="emplistTable">
			<thead style="background-color: #0072c6;">
				<tr>
					<th style="text-align:center">Employee ID</th>
					<th style="text-align:center">Fullname</th>
					<th style="text-align:center">Monthly</th>
					<th style="text-align:center">Daily</th>
					<th style="text-align:center">Hourly</th>
					<th style="text-align:center">Total Hours</th>
					<th style="text-align:center">Effectivity Date</th>
				</tr>
			</thead>
			<tbody>
				<?php if($records){ ?>
					<?php foreach($records as $row): ?>
						<tr>
							<td class="align_center"><?=$row["employeeid"]?></td>
							<td class="align_center"><?=$this->extensions->getIncomeDesc($row["other_income"])?></td>
							<td class="align_center"><?=$this->extensions->getEmployeeName($row["monthly"])?></td>
							<td class="align_center"><?=$row["daily"]?></td>
							<td class="align_center"><?=$row["hourly"]?></td>
							<td class="align_center"><?=$row["overload_hours"]?></td>
							<td class="align_center"><?=$row["dateEffective"]?></td>
						</tr>
					<?php endforeach ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<script>
	$(document).ready(function(){
	    var table = $('#emplistTable').DataTable({
	    });
	    new $.fn.dataTable.FixedHeader( table );
	});
</script>