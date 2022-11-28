<?php if($records) foreach($records as $value):?>
	<tr id="<?= $value->id ?>">
		<td class="align_center" style="padding: 7px;">
			<a class="btn btn-primary refresh_forms refresh_forms_<?= $value->id?>" id="<?= $value->id?>" style="display: none;"><i class="icon-refresh"></i></a>
			<a class="btn btn-primary edit_forms edit_forms_<?= $value->id?>" id="<?= $value->id?>"><i class="icon-edit"></i></a>
			<a class="btn btn-danger delete_forms delete_forms_<?= $value->id?>" id="<?= $value->id?>"><i class="icon-trash"></i></a>
		</td>
		<td class="align_center exist-id exist-id_<?= $value->id?>"><?= $value->filename?></td>
		<td class="align_center exist-desc exist-desc_<?= $value->id?>"><?= $value->description?></td>
	</tr>
<?php endforeach ?>