<?php
	//Added 6-3-2017
	$CI =& get_instance();
    $CI->load->model('my_payroll');
	$cutoff = $CI->my_payroll->getCutoffListWithLoan($this->session->userdata('username'));
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
                            <label class="field_name align_right">Select Cutoff</label>
                            <div class="field">
                                <select class="form-control" id="cutoff">
									<?
										foreach($cutoff as $c)
										{
											foreach($c as $c1)
											{
											?>
										
											<option value="<?=$c1->startdate?>,<?=$c1->enddate?>"><?=date("F d",strtotime($c1->startdate))?> - <?=date("d, Y",strtotime($c1->enddate))?></option>
										
										<?}
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
	$("#search").click(function(){
		cutoff = $("#cutoff").val();
		if(cutoff)
		{
			$("#loanHistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
			
			$.ajax({
				url      :   "<?=site_url("employeemod_/fileconfig")?>",
				type     :   "POST",
				data     :   {folder: "employeemod/myPayroll", view: "loanHistory",cutoff: cutoff},
				success  :   function(msg){
					$("#loanHistory").html(msg);
				}
			});
		}
	});
 
	$(".chosen").chosen();
</script>