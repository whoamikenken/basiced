<?php
	//Added 6-5-2017
	$CI =& get_instance();
    $CI->load->model('my_payroll');
	$cutoff = $CI->my_payroll->getCutoffList();

	// var_dump($cutoff);
	// die;
?>
<div id="content">
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                   <div class="panel-heading"><h4><b>My Other Income</b></h4></div>
                   <div class="panel-body" id="otherIncomecontent" style="padding-bottom: 32px;">
                   	<div class="form-group">
				            <div class="col-md-12">
				                <label  for="employeeid" class="col-sm-1" style="width: auto!important;">Select Cutoff</label>
					                <div class="col-sm-4">
                                        <select class="form-control" id="cutoff">
											<?
												foreach($cutoff as $c)
												{?>
													<option value="<?=$c->startdate?>,<?=$c->enddate?>"><?=date("F d",strtotime($c->startdate))?> - <?=date("d, Y",strtotime($c->enddate))?></option>
												<?}
											?>
		                                </select>			                
		                            </div>
				                <div class="col-sm-3">
									<a href="#" class="btn btn-primary" id="search">Search</a>
				                </div>
				            </div>
				        </div>
					</div>                    
                    <div id="otherIncomeHistory"></div>
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
$("#search").click(function(){
		cutoff = $("#cutoff").val();
		category = "income";
		if(cutoff)
		{
			$("#otherIncomeHistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
			$.ajax({
				url      :   "<?=site_url("employeemod_/verifyStatus")?>",
				type     :   "POST",
				data     :   {cutoff: cutoff, category: category},
				success  :   function(response){
					if(response == "PENDING"){
						alert("Your income is still pending.");
						$("#otherIncomeHistory").html('');
					}
					else{
						getIncomeHistory();
					}
				}
			});
		}

	});
	function getIncomeHistory(){
		cutoff = $("#cutoff").val();
		if(cutoff)
		{
			$("#otherIncomeHistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
			$.ajax({
				url      :   "<?=site_url("employeemod_/fileconfig")?>",
				type     :   "POST",
				data     :   {folder: "employeemod/myPayroll", view: "otherIncomeHistory",cutoff: cutoff},
				success  :   function(msg){
					$("#otherIncomeHistory").html(msg);
				}
			});
		}
	}
 
	$(".chosen").chosen();
</script>