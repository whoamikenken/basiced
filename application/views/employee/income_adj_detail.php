<?php

/**
 * @author Justin
 * @copyright 2015
 */
$income_array = array();

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];    
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
}

$income = "";
?>
<!-- <a id="addincome" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add Income</a> -->

<?
if($empdetails){
$this->db->_reset_select(); /** reset active select */    
$q = $this->db->query("select a.employeeid,a.code_income,a.amount,a.datefrom,a.dateto,a.income_base,a.nocutoff,a.deduct,a.taxable,a.schedule,a.cutoff_period from employee_income_adj a where a.employeeid='{$empdetails}'");
if($q->num_rows()>0){
?>

                    <table class="table table-striped table-bordered table-hover" id="incomeAdjDetail">
                    <thead style="background-color: #0072c6;">
                        <tr>
                            <th class="col-md-1">#</th>
                            <th class="sorting_asc">Type</th>
                            <th>Deduction Date</th>
                            <th>Amount</th>
                            <th>No. Of Cut-Off</th>
                            <th>Deduct</th>
                            <th>Schedule</th>
                            <th>Period</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="incomelist">
<?    
$t = 0;
foreach($q->result() as $row){
$t++;
?>
<tr>
  <td><?=$t?></td>
  <td><?=$row->code_income == 'SALARY' ? $row->code_income : $this->payrolloptions->incomedesc($row->code_income)?></td>
  <td class="align_center"><?=($row->datefrom != "0000-00-00" ? date('F d, Y',strtotime($row->datefrom)) : "")?></td>
  <td class="align_center"><?=$row->amount?></td>
  <td class="align_center"><?=$row->nocutoff?></td>
  <td class="align_center"><?=$row->deduct?'YES':'NO'?></td>
  <td class="align_center"><?=$this->payrolloptions->payscheduledesc($row->schedule)?></td>
  <td class="align_center"><?=$this->payrolloptions->quarterdesc($row->cutoff_period,FALSE,$row->schedule)?></td>
  <td class="col-md-1"><div class="btn-group" employeeid='<?=$row->employeeid?>' codeincome='<?=$row->code_income?>' incomebase='<?=$row->income_base?>' amount='<?=$row->amount?>' nocutoff='<?=$row->nocutoff?>' datefrom='<?=$row->datefrom?>' dateto='<?=$row->dateto?>' schedule='<?=$row->schedule?>' period='<?=$row->cutoff_period?>' ishidden='1' deduct='<?=$row->deduct?>' taxable='<?=$row->taxable?>' >
        <a class="btn btn-info" style="margin-right: 10px;" href="#modal-view" tag='edit_d' data-toggle="modal"><i class="glyphicon glyphicon-edit"></i></a>
        <a class="btn btn-danger" href="#" tag='delete_d'><i class="glyphicon glyphicon-trash"></i></a>
    </div></td>
</tr>
<?
}    
?>
                    </tbody>
                    </table>
<?
}else{
?>
<i>No Income Adjustment attached.</i>
<?    
}
}else{
?>
<i>No Income Adjustment attached.</i>
<?      
}
?>
<script>

$(document).ready(function(){
    if($("#incomeAdjDetail").html()){
        var table = $('#incomeAdjDetail').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
    }
});

$("a[tag='edit_d']").click(function(){
    var md = $(this).parent();
    $("#modal-view").find("h3[tag='title']").text("Edit Income Adjustment");  
    var form_data = {
        employeeid: $(md).attr("employeeid"),
        income: $(md).attr("codeincome"),
        memberid: $(md).attr("memberid"),
        amount: $(md).attr("amount"),
        nocutoff: $(md).attr("nocutoff"),
        datefrom: $(md).attr("datefrom"),
        dateto: $(md).attr("dateto"),
        schedule: $(md).attr("schedule"),
        period: $(md).attr("period"),
        deduct: $(md).attr("deduct"),
        taxable: $(md).attr("taxable"),
        ishidden: $(md).attr("ishidden"),
        view: "employee/addincome_adj"
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
    const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var md = $(this).parent();
     
            var form_data = {
                job: "delete",
                employeeid: $(md).attr("employeeid"),
                income: $(md).attr("codeincome"),
                amount: $(md).attr("amount"),
                datefrom: $(md).attr("datefrom"),
                dateto: $(md).attr("dateto"),
                period: $(md).attr("period"),
                view: "employee/addincome_adj"
            };
            $.ajax({
                url: "<?=site_url('main/siteportion')?>",
                type: "POST",
                data: form_data,
                success: function(msg){
                   loadincome_adj();
                   Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: 'Successfully deleted data!',
                      showConfirmButton: true,
                      timer: 1000
                  })
                }
            });
                  } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe.',
              'error'
            )
          }
        })
});

$("#addincome").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add Income");  
    var form_data = {
        view: "employee/addincome"
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

 setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn");
  }, 2000);
</script>