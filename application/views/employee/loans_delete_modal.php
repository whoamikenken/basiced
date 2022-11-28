<?
/**
* @author justin (with e)
* @copyright 2018
*/
?>

<?if(count($loan_payment_list) > 1):?>
<style type="text/css">
	.modal{
		width: 800px;
	}
</style>	
<?endif;?>

<div class="form_row" style="margin-left: 20px">
	<h4 style="color:red; font-weight: bold;"><?=$delete_msg?></h4>
</div>
<br>
<div class="form_row" style="margin-left: 20px">
	<h5><strong>Remarks:</strong></h5>
	<textarea name="remarks" class="form_control" style="width: 500px;"></textarea>
</div>
<?
if(count($loan_payment_list) > 1){
?>
<div class="form_row">&nbsp;</div>
<div class="form_row">
	<h5 style="color:red; font-weight: bold;">Affected Loan Payment : </h5>
	<table class="table table-striped table-bordered table-hover datatable">
		<thead>
			<tr>
				<th width="5%">#</th>
				<th width="22%">Payroll Cut-Off</th>
				<th width="13%">Credit</th>
				<th width="13%">Debit</th>
				<th width="13%">Balance</th>
				<th width="8%">Cut-Off Seq.</th>
				<th width="8%">Remaining Cut-off</th>
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
<?
}
?>

<script type="text/javascript">
var toks = hex_sha512(" ");
$("#button_save_modal").html('Delete');

$("#button_save_modal").unbind('click').click(function(){
	if(!$("textarea[name='remarks']").val()){
		Swal.fire({
	          icon: 'warning',
	          title: 'Warning!',
	          text: "Remarks is required..",
	          showConfirmButton: true,
	          timer: 1000
	      })
		return;
	}

	$.ajax({
		url : "<?=site_url("loan_/deleteEmployeeLoan")?>",
		type : "POST",
		data : {
				id :  GibberishAES.enc( "<?=$id?>", toks),
				employeeid :  GibberishAES.enc("<?=$employeeid?>" , toks),
				remarks : GibberishAES.enc( $("textarea[name='remarks']").val() , toks),
				toks:toks
			   },
		success : function(result){
			$("#modalclose").click();
			$(".nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover").click();
			Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: result,
              showConfirmButton: true,
              timer: 1000
          })
			<?if(isset($is_batch_encode)):?>
				loadLoanTableList();
			<?else:?>
				loadLoanPage();
			<?endif;?>
			$(".close").click();
		}
	});
});
</script>