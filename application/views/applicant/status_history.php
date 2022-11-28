<div class="col-md-12">
	<table class="table table-bordered table-hovered">
		<thead>
			<tr style="background-color: #ffC72c">
				<th>Applicant ID</th>
				<th>Description</th>
				<th>Status</th>
				<th>Date Changed</th>
				<th>Edited By:</th>
			</tr>
		</thead>
		<tbody>
			<?php if($records): ?>
				<?php foreach($records as $row): ?>
					<tr>
			 			<td><?= $row["applicantid"] ?></td>
			 			<td><?= $row["description"] ?></td>
			 			<td><?= $row["app_stat"] ?></td>
			 			<td><?= date("Y-m-d", strtotime($row["timestamp"])) ?></td>
			 			<td><?= $row["changedby"] ?></td>
					</tr>
				<?php endforeach ?>	
			<?php endif ?>	
		</tbody>
	</table>
</div>
