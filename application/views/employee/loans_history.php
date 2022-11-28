<?
  /**
  * @author justin (with e)
  * @copyright 2018
  */
?>
<style type="text/css">
	.scrollbar{
   overflow: auto;
   margin-bottom: 10px;
}

  .scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  /* Track */
  .scrollbar::-webkit-scrollbar-track {
    box-shadow: inset 0 0 0 grey; 
    border-radius: 10px;
  }
   
  /* Handle */
  .scrollbar::-webkit-scrollbar-thumb {
    background: #0072c6;
    border-radius: 10px;
  }

  /* Handle on hover */
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background: #fadd14; 
  }
</style>
<div class="form_row">
	<?if ($is_title_display):?>
	<?endif;?>
		<div class="scrollbar">
	<table class="table table-striped table-bordered table-hover" id="history">
		<thead style="background-color: #0072c6;">
			<tr>
				<th width="3%">#</th>
				<th width="15%">Type</th>
				<th width="12%">Payroll Cut-Off</th>
				<th width="10%">Loan Amount</th>
				<th width="10%">Remaining Balance</th>
				<th width="10%">Amount Payment</th>
				<th width="7%">No. of Cut-Off</th>
				<th width="7%">Remaining Cut-Off</th>
				<th width="10%">Hold</th>
				<th width="6%">Remarks</th>
				<th width="10%">Timestamp</th>
			</tr>
		</thead>
		<tbody>
		<?
		$idx = 0;
		foreach ($loan_history_list as $key => $info) {
			$idx += 1;
		?>

			<tr>
				<td class="align_right"><?=$idx?></td>
				<td><?=$info["type"]?></td>
				<td class="align_center"><?=$info["payroll_cutoff"]?></td>
				<td class="align_right"><?=$info["loan_amount"]?></td>
				<td class="align_right"><?=$info["remaining_balance"]?></td>
				<td class="align_right"><?=$info["amount_payment"]?></td>
				<td class="align_center"><?=$info["no_cutoff"]?></td>
				<td class="align_center"><?=$info["remaining_cutoff"]?></td>
				<td class="align_center"><?=$info["status"]?></td>
				<td class="align_center">
					<?if($info["status"] == "DELETED"):?>
					<button tag="view-remarks" loan-payment-id="<?=$info["lp_id"]?>"  class="btn btn-primary" style="background-color:  #00b3b3;">
						<span class="glyphicon glyphicon-eye-open"></span>
					</button>
					<?endif;?>
				</td>
				<td class="align_center"><?=date("F d, Y", strtotime($info["date"]))?></td>
			</tr>
		<?
		}
		?>
		</tbody>
	</table>
</div>
</div>
<script type="text/javascript">
var toks = hex_sha512(" ");
$(document).ready(function(){
    var table = $('#history').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$("button[tag='view-remarks']").unbind("click").click(function(){
	loan_payment_id = $(this).attr("loan-payment-id");

	$.ajax({
		url : "<?=site_url("loan_/showLoanPaymentRemarks")?>",
		type : "POST",
		data : {lp_id :  GibberishAES.enc(loan_payment_id, toks), toks:toks},
		success : function(content){
			viewLoanModal("REMARKS", "<center><div class='form_row'><div style='padding-left:20px;'><h5>"+ content + "</h5></div></div><center>");
			$("#button_save_modal").hide();
		}
	});

});
</script>
