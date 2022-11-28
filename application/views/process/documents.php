	<?php if($records) foreach($records as $value):?>
		<tr id="<?= $value['id'] ?>">
			<td class="align_center" style="padding: 7px;">
				<a class="btn blue refresh_rec" code="<?= $value['code']?>" style="display: none;"><i class="icon-refresh"></i></a>
				<a class="btn blue edit_rec" code="<?= $value['code']?>"><i class="icon-edit"></i></a>
				<a class="btn blue delete_rec" code="<?= $value['code']?>"><i class="icon-trash"></i></a>
			</td>
			<td class="align_center exist-code"><?= $value['code']?></td>
			<td class="align_center exist-desc"><?= $value['description']?></td>
		</tr>
	<?php endforeach ?>