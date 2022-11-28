<?php 
/**
* @author justin (with e)
* @copyright 2018
*/

?>
<style type="text/css">

</style>
<div class="form_row">
	<table class="table table-striped table-bordered table-hover datatable">
		<thead style="background-color: #0072c6;">
			<tr>
				<th width="5%">#</th>
				<th width="22%">Payroll Cut-Off</th>
				<th width="13%">Credit</th>
				<th width="8%">Debit</th>
				<th width="13%">Balance</th>
				<th width="13%">Cut-Off Seq.</th>
				<th width="19%">Remaining Cut-off</th>
				<th width="8%">Timestamp</th>
			</tr>
		</thead>
		<tbody>
		<?
		foreach ($loan_payment_list as $idx => $info) {
		?>
			<tr>
				<td class="align_right"><?=($idx + 1)?></td>
				<td class="align_center"><?=$info["payroll_cutoff"]?></td>
				<td class="align_right"><?=$info["credit"]?></td>
				<td class="align_right"><?=$info["debit"]?></td>
				<td class="align_right"><?=$info["balance"]?></td>
				<td class="align_center"><?=$info["cutoff_seq"]?></td>
				<td class="align_center"><?=$info["remain_cutoff"]?></td>
				<td class="align_center"><?=$info["timestamp"]?></td>
			</tr>
		<?
		}
		?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	$("#button_save_modal").hide();
</script>