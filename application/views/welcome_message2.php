<?php
	
	$old_resource_pk = "";
	$th = $td = "";
	$th.="<th>Accession Number</th><th>Shelf</th><th>Supplier</th>";
	// foreach($accessions as $accession){
	// 	if($accession['resource_pk'] != $old_resource_pk){
	// 		if($old_resource_pk) $td .= "</tr>";
	// 		$td .= "<tr>";
	// 		$td .= "<td>".$accession['resource_pk']."</td>";
	// 	}

	// 	$td .= "<td>".$accession['accession_number']."</td>";
	// 	$td .= "<td>".$accession['shelf']."</td>";
	// 	$td .= "<td>".$accession['supplier']."</td>";

	// 	$old_resource_pk = $accession['resource_pk'];
	// }

?>

<table border="1" cellspacing="5" cellpadding="5">
	<thead>
		<tr>
				<?= $th ?>
		</tr>
	</thead>
	<tbody>
				<?= $td ?>
	</tbody>
</table>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script>

</script>