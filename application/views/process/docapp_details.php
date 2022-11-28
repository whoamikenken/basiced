<style>
	.cbox{
	    -ms-transform: scale(1.5); /* IE */
	    -moz-transform: scale(1.5); /* FF */
	    -webkit-transform: scale(1.5); /* Safari and Chrome */
	    -o-transform: scale(1.5); /* Opera */
	}
</style>
<div class="modal fade" id="process_modal" role="dialog"> <!-- Apply employee doc --> </div>
<table class="table table-bordered table-hover datatable" id="doc_app">
	<thead>
		<tr style="background-color: #0072c6 ">
			<th class="align_center">Actions</th>
			<th class="align_center">Date Requested</th>
			<th class="align_center">Full Name</th>
			<th class="align_center">Document Requested</th>
			<th class="align_center">Status</th>
			<th class="align_center">To be claimed on</th>
			<?php if($this->session->userdata("usertype") != "ADMIN"): ?><th class="align_center">Mark as read&emsp;<input type="checkbox" class="cbox" id="mars" name="mars" /></th><?php endif ?>
		</tr>   
	</thead>
	<tbody>
		<?php foreach($records as $row):
			foreach ($row as $key => $value) $row[$key] = Globals::_e($value);
		 ?>
			<tr <?=(!$row['isread'] && ($row['status'] == "APPROVED" || $row['status'] == "DISAPPROVED") && $this->session->userdata("usertype") != "ADMIN" ? "style='background: #B4CDC6'" : ($row['status'] == "CANCELED" ? " style='background: #ffcccc'" : ""))?> <?= ($this->session->userdata("usertype") == "ADMIN" && $row['status'] == "PENDING") ? "style='background: #B4CDC6'" : " " ?> >
				<td class="align_center">
					<a class="btn btn-info edit_application" id="<?= $row['id']?>" <?= ($row['status'] != 'PENDING' && $this->session->userdata("usertype") != "ADMIN") ? ' style="display: none;"' : '' ; ?>><i class="glyphicon glyphicon-edit"></i></a>
					<?php if($row['status'] == "PENDING" || $row['status'] == ""){ ?><a class="btn btn-danger delete_application" id="<?= $row['id']?>" <?= ($row['status'] != 'PENDING' && $this->session->userdata("usertype") != "ADMIN") ? ' style="display: none;"' : '' ; ?> ><i class="glyphicon glyphicon-trash"></i></a><?php } ?>
				</td>
				<td class="align_center"><?= $row['dateapplied']?></td>
				<td class="align_center"><?= $row['fullname']?></td>
				<td class="align_center"><?= $this->extensions->getDocumentDescription($row['doc_requested'])?></td>
				<td class="align_center"><?= ($row['status'] == "PROCESS") ? "ON PROCESS" : $row['status'] ?></td>
				<td class="align_center">
					<?php 
						if($row['date_to_claim'] && $row['date_to_claim'] != "0000-00-00") echo $row['date_to_claim']; 
						else echo "Not yet approved";  
					?>
				</td>
				<?php if($this->session->userdata("usertype") != "ADMIN"): ?><td class="align_center"><input type="checkbox" class="cbox" value="1" name="mar" idkey="<?=$row['id']?>"  <?=($row['isread'] ? " checked disabled isread='yes'" : "isread='no'")?> /></td><?php endif ?>
			</tr>
		<?php endforeach ?>
	</tbody>                  
</table>
<script src="<?=base_url()?>js/hr_setup/documents_datatable.js"></script>