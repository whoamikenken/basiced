<div class="col-md-12">
	<div tag="display">
		<table class="table table-hover table-striped table-bordered app_base_list">
			<thead>	
				<tr>	
					<th class="align_center">Code</th>
					<th class="align_center">Sequence</th>
					<th class="align_center">Description</th>
				</tr>
			</thead>
			<tbody>
				<td class="align_center" rowspan="<?php echo  count($app_seq) + 1 ?>" style="vertical-align: middle;" ><?php echo  $code ?></td>
				<?php 
					if($app_seq){
						foreach($app_seq as $seq => $desc){ 

							?>
							<tr>
								<td class="align_center"><?php echo  $seq ?></td>
								<td class="align_center"><?php echo  $desc ?></td>
							</tr>
					<?php
						}
					} 
				?>
			</tbody>
		</table>
	</div>
</div>