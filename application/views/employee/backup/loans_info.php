<?php

/**
 * @author Justin 
 * @copyright 2015      
 */

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];    
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
}

$loan = "";
?>
<a id="addloan" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add Loan</a>
<br />

<?
if($empdetails){
$this->db->_reset_select(); /** reset active select */    
$q = $this->db->query("select a.id,a.employeeid,a.code_loan,a.startingamount,a.currentamount,a.amount,a.famount,a.nocutoff,a.datefrom,a.dateto,a.loan_base,a.schedule,a.cutoff_period from employee_loan a where a.employeeid='{$empdetails}'");
if($q->num_rows()>0){
?>
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="well blue">
            <div class="well-content" style="border: transparent !important;">
                    <table class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>
                            <th class="col-md-1">#</th>
                            <th class="sorting_asc">Type</th>
                            <th>Start Date</th>
                            <th>Starting Balance</th>
                            <th>Remaining Balance</th>
                            <th>Amount</th>
                            <th>Final Amount</th>
                            <th>No. Of Cut-Off</th>
                            <th>Schedule</th>
                            <th>Period</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="loanlist">
<?    
$t = 0;
$remaining = 0;

foreach($q->result() as $row){
    $paidloan = 0;
      $query = $this->db->query("SELECT * FROM employee_loan_history WHERE startBalance='$row->startingamount' AND amount='$row->amount' AND code_loan='$row->code_loan' AND mode='CUTOFF'");
      if ($query->num_rows() > 0) {
        $paidloan ++;
      }
$t++;
$remaining = (($row->amount*($row->nocutoff-1))+$row->famount);
?>
<tr>
    <!-- <td><?=$paidloan;?></td> -->
  <td><?=$t?></td>
  <td><?=$this->payrolloptions->loandesc($row->code_loan)?></td>
  <td class="align_right"><?=($row->datefrom != "0000-00-00" ? date('F d, Y',strtotime($row->datefrom)) : "")?></td>
  <td class="align_right"><?=$row->startingamount?></td>
  <td class="align_right"><?=($row->nocutoff == 0 ? 0 : (($row->amount*($row->nocutoff )))) ?></td>
  <td class="align_right"><?=$row->amount?></td>
  <td class="align_right"><?=$row->famount?></td>
  <td class="align_right"><?=$row->nocutoff?></td>
  <td class="align_center"><?=$this->payrolloptions->payscheduledesc($row->schedule)?></td>
  <td class="align_center"><?=$this->payrolloptions->quarterdesc($row->cutoff_period,FALSE,$row->schedule)?></td>
  <td class="col-md-1"><div class="btn-group" remaining='<?=$remaining?>' employeeid='<?=$row->employeeid?>' codeloan='<?=$row->code_loan?>' loanbase='<?=$row->loan_base?>' amount='<?=$row->amount?>' startingamount='<?=$row->startingamount?>' currentamount='<?=$row->currentamount?>' famount='<?=$row->famount?>' nocutoff='<?=$row->nocutoff?>' datefrom='<?=$row->datefrom?>' dateto='<?=$row->dateto?>' schedule='<?=$row->schedule?>' basedon='<?=$row->loan_base?>' period='<?=$row->cutoff_period?>' id='<?=$row->id?>'>
        <?php if ($paidloan <= 0): ?>
        <a class="btn" href="#modal-view" tag='edit_d' data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>
        <?php endif ?>
        <a class="btn" href="#" tag='delete_d'><i class="glyphicon glyphicon-trash"></i></a>
    </div>
</td>
</tr>
<?
}    
?>
            </tbody>
            </table>
            </div>
            </div>
           
        <div class="well blue">
            <div class="well-content" style="border: transparent !important;">
               <div class="form_row" >
                <h3>Loan History</h3>
               <label class="field_name align_right">Filter By:</label>
                  <div class="field">
                           <select class="chosen col-md-4 filter" name="filter" id='filter' >
                                 <option value='transaction'>TRANSACTION</option>
                                 <option value='action'>ACTION</option>  
                           </select>
                   </div>
                </div>
				<table class="table table-striped table-bordered table-hover datatable" id="history">
                    <thead>
                        <tr>
                            <th class="sorting_asc">Type</th>
                            <th>Cut-Off Date</th>
                            <th>Starting Balance</th>
			                <th>Amount</th>
                            <th>Remaining Balance</th>
                            <th>Mode</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class='loanhistory'>
                   
                    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>    
</div>           
<?
}else{
?>
<i>No Loan attached.</i>
<?    
}
}else{
?>
<i>No Loan attached.</i>
<?      
}
?>
<script>

$(document).ready(function(){
    $('#history').DataTable();
    loadhistorytransaction();
});

function loadhistorytransaction()
{
     $.ajax({
        url: "<?=site_url('employee_/loadLoanHistory')?>",
        type: "POST",
        data: {filter:"",empdetails:'<?=$empdetails?>'},
        success: function(msg){
        // alert(msg);
           $('.loanhistory').html(msg);
        }
    });
}
$("#filter").on('change',function()
{
     $('.loanhistory').html("<td colspan='7'>Loading..... Please wait!</td>");   
     $.ajax({
        url: "<?=site_url('employee_/loadLoanHistory')?>",
        type: "POST",
        data: {filter:$(this).val(),empdetails:'<?=$empdetails?>'},
        success: function(msg){
          
           $('.loanhistory').html(msg);
        }
    });

})

$("a[tag='edit_d']").click(function(){
    var md = $(this).parent();
    $("#modal-view").find("h3[tag='title']").text("Edit Loan");  
    var form_data = {
        employeeid: $(md).attr("employeeid"),
        loan: $(md).attr("codeloan"),
        memberid: $(md).attr("memberid"),
        amount: $(md).attr("amount"),
        startingamount: $(md).attr("startingamount"),
        currentamount: $(md).attr("currentamount"),
        famount: $(md).attr("famount"),
        nocutoff: $(md).attr("nocutoff"),
        datefrom: $(md).attr("datefrom"),
        dateto: $(md).attr("dateto"),
        schedule: $(md).attr("schedule"),
        period: $(md).attr("period"),
        basedon : $(md).attr('basedon'),
        id    :$(md).attr("id"),
        view: "employee/addloan"
    };
   
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});
$("a[tag='delete_d']").click(function(){
    var ans = confirm("Are you sure you want to continue?");
    if(ans){
    var md = $(this).parent();
    // alert($(md).attr("remaining"));
     
    var form_data = {
        job: "delete",
        employeeid: $(md).attr("employeeid"),
        startingamount: $(md).attr("startingamount"),
        remaining : $(md).attr("remaining"),
        loan: $(md).attr("codeloan"),
        amount: $(md).attr("amount"),
        famount: $(md).attr("famount"),
        nocutoff: $(md).attr("nocutoff"),
        datefrom: $(md).attr("datefrom"),
        dateto: $(md).attr("dateto"),
        period: $(md).attr("period"),
        id    :$(md).attr("id"),
        view: "employee/addloan"
    };
    // console.log(form_data);return;
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            
           refreshtab("#116");
           alert("Done deleting...");
           
        }
    });
    }
});
$("#addloan").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add Loan");  
    var form_data = {
        view: "employee/addloan"
    };
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});
function refreshtab(tabn){
    var form_data = { 
      view : $(tabn).attr("ld")
    }
    console.log(form_data);
    
    $.ajax({
            url: "<?=site_url("main/siteportion")?>",
            data: form_data,
            type:"POST",
            success: function(msg){
                $(tabn).html(msg);
            }
        });
}
</script>