<?
/**
* @author justin (with e)
* @copyright 2018
*/
?>
<style type="text/css">
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Active Loan</b></h4></div>
                   <div class="panel-body" id="div-active-loan">
						<img src="<?=base_url()?>images/loading.gif"/> Loading, Please wait..
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Loan History</b></h4></div>
                   <div class="panel-body" id="div-loan-history">
						<img src="<?=base_url()?>images/loading.gif"/> Loading, Please wait..
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	var toks = hex_sha512(" ");
	function showActiveLoans(){
		$.ajax({
			url : "<?=site_url("loan_/showEmpLoanList")?>",
			type : "POST",
			data : {
				employeeid :  GibberishAES.enc("<?=$this->session->userdata('username')?>" , toks),
				edit_display :  GibberishAES.enc(0 , toks),
				toks:toks
			},
			success : function(response_content){
				$("#div-active-loan").html(response_content);
			}
		});
	}

	function showLoanHistory(){
		$.ajax({
			url : "<?=site_url("loan_/showLoanPaymentHistory")?>",
			type : "POST",
			data : { 
				employeeid : GibberishAES.enc("<?=$this->session->userdata('username')?>" , toks),
				is_title_display :  GibberishAES.enc(0 , toks),
				toks:toks
			},
			success : function(response_content){
				$("#div-loan-history").html(response_content);
			}
		});
	}

	showActiveLoans();
	showLoanHistory();
</script>