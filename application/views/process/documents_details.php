	<?php if($records) foreach($records as $value):?>
		<tr id="<?= $value['id'] ?>">
			<td class="align_center" style="padding: 7px;">
				<b><a class="btn btn-primary refresh_rec refresh_rec_<?= $value['id'] ?>" code="<?= $value['code']?>" style="display: none;font-size: 15px;"><i class="icon-refresh"></i></a></b>
				<b><a class="btn btn-info edit_rec  edit_rec_<?= $value['id'] ?>" code="<?= $value['code']?>"><i class="glyphicon glyphicon-edit"></i></a></b>
				<b><a class="btn btn-danger delete_rec  delete_rec_<?= $value['id'] ?>" code="<?= $value['code']?>"><i class="glyphicon glyphicon-trash"></i></a></b>
			</td>
			<td class="align_center exist-code exist-code_<?= $value['id'] ?>"><?= $value['code']?></td>
			<td class="align_center exist-desc exist-desc_<?= $value['id'] ?>"><?= $value['description']?></td>
		</tr>
	<?php endforeach ?>