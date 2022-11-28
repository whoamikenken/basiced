<style>
	.dataTables_scrollBody{
		overflow: visible !important;
	}
</style>
<input type="text" id="codededuc" value="<?= $code_deduc ?>" hidden>
<div class="panel">
    <div class="panel-heading"><h4><b>Employee List</b></h4></div>
    <div class="panel-body emplist">
        <table class="table table-hover table-bordered datatable" id="be_deduction">
			<thead style="background-color: #0072c6">
				<tr>
					<th>Employee ID</th>
	    			<th>Fullname</th>
	    			<th>Deduction Date</th>
	    			<th>Amount</th>
	    			<th>Number of cut off</th>
	    			<th>Payroll Cut-off</th>
	    			<!-- <th>Status</th> -->
	    			<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($emplist as $employeeid => $emp_data): ?>
					<tr id="<?= $employeeid ?>">
						<td><?= $employeeid ?></td>
						<td><?= $emp_data['fullname'] ?></td>
						<td tag="new">
							<div class='input-group date' data-date="<?= $emp_data['datefrom'] ?>" data-date-format="yyyy-mm-dd" style="position: relative;">
								<input type='text' class="form-control" id="datefrom" value="<?= $emp_data['datefrom'] ?>" />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</td>

						<td tag="new">
							<input type="text" class="amount form-control" name="amount" id="amount" value="<?= number_format((double) $emp_data['amount'], 2) ?>">
						</td>
						<td tag="new">
							<input type="text" class="nocutoff form-control" name="nocutoff" id="nocutoff" value="<?= $emp_data['nocutoff'] ?>">
						</td>

						<td tag="new">
							<select class="cutoff_period form-control" name="cutoff_period" id="cutoff_period">
								<?=$this->payrolloptions->quarter($emp_data['cutoff_period'],FALSE,$emp_data['schedule'],TRUE);?>
							</select>
						</td>
						<td class="align_center">
							<button type="button" class="btn btn-danger clearRow">Clear</button>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	<br>
</div>
  
<div id="be_modal" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
    <div class="modal-header">
        <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="row-fluid span12" tag='display'></div>
        </div></div>
    <div class="modal-footer">
        <a href="#" data-dismiss="modal" aria-hidden="true" class="btn grey">Close</a>
    </div>
</div>
<script src="<?=base_url()?>js/batch_encode/be_deduction.js"></script>