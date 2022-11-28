<?php
	//Added 6-3-2017
	$CI =& get_instance();
    $CI->load->model('my_payroll');
	// $cutoff = $CI->my_payroll->getCutoffListWithLoan($this->session->userdata('username'));

	// var_dump($cutoff);
	// die;
?>
<div id="content">
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #A548A2;">
                        <h5>My Loan</h5>
                    </div>
                    <div id="loancontent" class="well-content" style="padding-bottom: 32px;">
						<div class="form_row no-search">
                            <label class="field_name align_right">Select Status</label>
                            <div class="field">
                                <select class="form-control" id="status">
									<?
										$option = array("On Going","Completed");
										foreach($option as $opt)
										{
										?>
											<option value="<?=$opt?>"><?=$opt?></option>
										
										<?
										}
									?>
                                </select>
                            </div>
                        </div>
						<div class="form_row">
							<div class="field">
                                <a href="#" class="btn btn-primary" id="search">Search</a>
                            </div>
						</div>
					</div>                    
                    <div id="loanHistory"></div>
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
	$(document).ready(function(){
		// loanHistory("On Going");
	});

	$("#search").click(function(){
		status = $("#status").val();
		if(status)
		{
			loanHistory(status);
		}
	});
	
	function loanHistory(status){
		$("#loanHistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
		
		$.ajax({
			url      :   "<?=site_url("employeemod_/fileconfig")?>",
			type     :   "POST",
			data     :   {folder: "employeemod/myPayroll", view: "loanHistory",status: status},
			success  :   function(msg){
				$("#loanHistory").html(msg);
			}
		});
	}
 
	$(".chosen").chosen();
</script>