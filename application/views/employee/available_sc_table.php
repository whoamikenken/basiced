<?
/**
* @author justin (with e)
* @copyright 2018
*/
?>
<div class="align_right">
	<button type="button" class="btn btn-primary" id="btn-new-sc">
		<span class="glyphicon glyphicon-plus"></span>
		New Service Credit Date
	</button>
</div>
<br>
<div>
	<table class="table table-striped table-bordered table-hover" id="available-sc-tbl">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>Date</th>
				<th>Credit</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
		<? 
			foreach ($sc_list as $id => $info): 
				if($info["balance"] > 0):
		?>	
			<tr>
				<td>
					<button class="btn btn-danger" tag="delete" value="<?=$id?>" type="button">
						<span class="glyphicon glyphicon-trash"></span>
					</button>
				</td>
				<td><?=$info["caption"]?></td>
				<td><?=number_format($info["credit"], 2)?></td>
				<td><?=number_format($info["balance"], 2)?></td>
			</tr>
		<? 
				endif;
			endforeach; 
		?>
		</tbody>
	</table>
</div>

<a href="#" id="show-modal" data-toggle="modal" data-target="#sc-modal" hidden></a>
<div class="modal fade" id="sc-modal" data-backdrop="static"></div>

<script type="text/javascript">
	function showModal(site_url, formdata){
		$.ajax({
			url : site_url,
			type : "POST",
			data : formdata,
			success : function(content){
				$("#sc-modal").html(content);
				$("#show-modal").click();
			}
		});
	}

	$("#btn-new-sc").unbind("click").click(function(){
		showModal("<?=site_url("service_credit_/newSCDate")?>", { employeeid : "<?=$employeeid?>" });
	});

	$("button[tag='delete']").unbind("click").click(function(){
		var id = this.value;

		if(confirm("Are you sure, you want to delete this date?")){
			$.ajax({
				url : "<?=site_url("service_credit_/deleteSCDate")?>",
				type : "POST",
				data : { id : id },
				success : function(msg){
					
					alert(msg.trim());
					if(msg.trim() == "Successfully delete.") displayAvailableSC();
				}
			});
		}
	});

	$(document).ready(function(){
    var table = $('#available-sc-tbl').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
</script>