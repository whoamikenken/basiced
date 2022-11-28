<?php

/**
 * @author Justin
 * @copyright 2016
 */

$CI =& get_instance();
$CI->load->model('utils');
?>
<div id="content"> <!-- Content start -->
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="well blue">
            <div class="well-header">
                <h5>Batch Encoding</h5>
            </div>
            <div class="well-content">
            <div class="form_row" id="edept">
                <label class="field_name align_right">Department</label>
                <div class="field">
                    <div class="col-md-12">
                        <select class="chosen col-md-6" name="deptid">
                          <option value="">All Department</option>
                        <?
                          $opt_department = $this->extras->showdepartment();
                          foreach($opt_department as $c=>$val){
                          ?><option value="<?=$c?>"><?=$val?></option><?
                          }
                        ?>
                        </select>
                    </div>
                </div>
            </div>
			<div class="form_row">
				<label class="field_name align_right">Employee Status</label>
				<div class="field">
					<select class="chosen col-md-6" name="employmentstat">
					<?
					$opt_status = $this->extras->showemployeestatus("All Status");
					foreach($opt_status as $c=>$val){
					?><option value="<?=$c?>"><?=$val?></option><?    
					}
					?>
				  </select>
				</div>
			</div>
            <div class="form_row" hidden>
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <div class="col-md-12">
                        <select class="chosen col-md-6" name="employeeid" multiple="">
                            <option value="">All Employee</option>
                        <?
                          // $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",false,'teaching');
                          $opt_type = $CI->utils->getEmplist();
                            foreach($opt_type as $key => $val){
                        ?>      <option value="<?=$key?>"><?=$val?></option><?    
                            }
                        ?>
                        </select>
                        <a href="#" class="btn btn-primary" id="multipleencode">Encode</a> (Click here for multiple employee encode)
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Category</label>
                <div class="field">
                    <div class="col-md-12 no-search">
                        <select class="chosen col-md-6" name="category" id="category">
                        <?
                          // $type = array(""=>"Choose Category","1"=>"Salary","2"=>"Dependents","3"=>"Payment Schedule","4"=>"Tax","5"=>"Absent and Balance","6"=>"Basic Deduction","7"=>"Income","8"=>"Loans","9"=>"Other Income");
                        $type = array(""=>"Choose Category","1"=>"Salary","6"=>"Deduction","7"=>"Income","8"=>"Loans","9"=>"Other Income","10"=>"Income Adjustment");
                          foreach($type as $c=>$val){
                          ?><option value="<?=$c?>"><?=$val?></option><?
                          }
                        ?>
                        </select>
                    </div>
                </div>
            </div>  
        </div>
    </div>
    <div class="well-blue">
        <div class="well-header">
            <h5>Employee List</h5>
        </div>
        <div class="well-content">
            <table class="table table-striped table-bordered table-hover datatable">
                <thead>
                    <tr>
                        <th class="col-md-1">#</th>
                        <th class="sorting_asc">Employee</th>
                        <th>Fullname</th>
                        <th>Type</th>
                        <th>Department</th>
                    </tr>
                </thead>
                <tbody id="employeelist">
                <?
                    $employee = $this->employee->loadallemployee();
                    if(count($employee)>0){
                        $o=1;
                        foreach($employee as $row){
                ?>
                        <tr employeeid='<?=$row['employeeid']?>' style="cursor: pointer;">
                            <td><?=$o?></td>
                            <td><?=$row['employeeid']?></td>
                            <td><?=($row['lname'] . ", " . $row['fname'] . ($row['mname']!="" ? " " . substr($row['mname'],0,1) . "." : ""))?></td>
                            <td><?=$this->extras->getemployeetype($row['emptype'])?></td>
                            <td><?= $this->extras->getemployeedepartment($row['deptid']) ?></td>
                        </tr>
                <?  
                        $o++;  
                        }
                    }
                ?>                                        
                </tbody>
            </table>
        </div>
    </div>
    <a id="showsetup" href="#" data-toggle="modal" data-target="#myModal" hidden=""></a>
    <div class="modal fade" id="myModal" data-backdrop="static"></div>
</div>
</div>
</div>
<script>
$("#multipleencode").click(function(){
    var category = $("select[name='category']").val(),
         view     = '';
    if(category=='6' || category=='7' || category=='8' || category=='9' || category=='10') view = "setup/salary_individual.php";
	else					                                              view = "setup/salary.php";
	
	if($("select[name='employeeid'] option:selected").length > 0)
	{
		if(category != ""){  
			$("#showsetup").click();
			$.ajax({
				url      : "<?=site_url('payroll_/payrollconfig')?>",
				type     : "POST",
				data     : {
							view    	:   view,
							dept   		:   $("select[name='deptid']").val(),
							eid     	:   $("select[name='employeeid']").val(),
							estat     	:   $("select[name='employmentstat']").val(),
							cat     	:   category
						   },
				success: function(msg){
					$("#myModal").html(msg);
				}
			});
		}else    alert("Please choose a category first..");
	}
	else	alert("Please choose employee first..");
});
$("#employeelist tr").click(function(){
    var category = $("select[name='category']").val(),
        view     = '';
    ///< different view for income/loan/other_income
    if(category=='6' || category=='7' || category=='8' || category=='9' || category=='10') view = "setup/salary_individual.php";
    else                                                view = "setup/salary.php";

   if($(this).attr("employeeid")){
       if(category != ""){  
           var eid  = [$(this).attr("employeeid")] ;
           $("#showsetup").click();
           $.ajax({
                url      : "<?=site_url('payroll_/payrollconfig')?>",
                type     : "POST",
                data     : {
                            view    :   view,
                            dept    :   $("select[name='deptid']").val(),
                            eid     :   eid,
                            cat     :   category
                           },
                success: function(msg){
                    $("#myModal").html(msg);
                }
           });
       }else    alert("Please choose a category first..");
   }
});  
$("select[name='deptid']").change(function(){
	var form_data = "&deptid="+$(this).val();
	if($("select[name='employmentstat']").val() != "")
	{
		form_data = form_data + "&estatus="+$("select[name='employmentstat']").val();
	}
	$.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: form_data,
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('liszt:updated');
        }
    });
});
$("select[name='employmentstat']").change(function(){
	var form_data = "&estatus="+$(this).val();
	if($("select[name='deptid']").val() != "")
	{
		form_data = form_data + "&deptid="+$("select[name='deptid']").val();
	}
	$.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: form_data,
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('liszt:updated');
        }
    });
});
function loadempopt(etype = ""){
    $.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: {
           etype : etype
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('liszt:updated');
        }
    });   
}
/*
$("#category").change(function(){
  $("#showsetup").click();
   $.ajax({
        url      : "<?=site_url('payroll_/payrollconfig')?>",
        type     : "POST",
        data     : {
                    view    :   "setup/salary.php",
                    dept    :   $("select[name='deptid']").val(),
                    eid     :   $("select[name='employeeid']").val(),
                    cat     :   $("select[name='category']").val()
                   },
        success: function(msg){
            $("#myModal").html(msg);
        }
    });
    $(this).val("").trigger('liszt:updated');
});
*/
$('.chosen').chosen();
$("#datesetfrom,#datesetto").datepicker({
    autoclose: true,
    todayBtn : true
});

</script>