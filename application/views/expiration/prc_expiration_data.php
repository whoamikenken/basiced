<div style="margin: 10px;">
	<div class="col-md-12">
		<table class="table table-striped table-bordered table-hover table-condensed" id="prc_expiration_data" width="100%">
			<thead>
				<tr style="background-color: #0072c6;">
					<td><b>Employee ID</b></td>
					<td><b>Employee Name</b></td>
					<td><b>PRC Number</b></td>
					<td><b>Expiration Date</b></td>
					<td><b>Remarks</b></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($prcExpiryData as $value):  
					$fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
					$prc_expiration = date("Y-m-d",strtotime($value['prc_expiration']));
					$remainingDays = $this->time->dateDiff($value['prc_expiration']);
					if($remainingDays > 1) $remainingDays = $remainingDays.' days remaining until expiration date';
					else if($remainingDays == 0) $remainingDays = "PRC # will expire today";
					else $remainingDays = 'PRC # will expire tommorow';
					($prc_expiration < $today) ? $spanStatus = "Expired" : $spanStatus = $remainingDays;
				?>
				<tr>
					<td><?= $value['employeeid'] ?></td>
					<td><?= $fullname ?></td>
					<td><?= $value['prc'] ?></td>
					<td><?= $prc_expiration ?></td>
					<td><?= $spanStatus ?></td>
				</tr>		
				<?php endforeach;  ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var table = $('#prc_expiration_data').DataTable({
        });
        new $.fn.dataTable.FixedHeader(table);
    });
</script>