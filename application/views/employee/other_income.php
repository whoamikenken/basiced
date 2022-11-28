<?php

/**
 * @author Justin
 * @copyright 2016
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
<p>( Note: Other Income will only reflect in Employee History Report )</p>
<a id="addincomeoth" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add Income</a>
<br />

<?
if($empdetails){
$this->db->_reset_select(); /** reset active select */    
$q = $this->db->query("select a.employeeid,a.code_income,a.amount,a.pos from employee_income_oth a where a.employeeid='{$empdetails}'");
if($q->num_rows()>0){
?>
<div class="widgets_area animated fadeIn delay-1s">
<div class="row">
    <div class="col-md-12">
        <div class="well blue">
            <div class="well-content" style="border: transparent !important;">
                    <table class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>
                            <th class="col-md-1">#</th>
                            <th class="sorting_asc">Type</th>
                            <th>Amount</th>
                            <th>Position</th>
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
  <td><?=$this->payrolloptions->incomedescoth($row->code_income)?></td>
  <td class="align_right"><?=$row->amount?></td>
  <td><?=ucfirst($row->pos)?></td>
  <td class="col-md-1"><div class="btn-group" employeeid='<?=$row->employeeid?>' codeincome='<?=$row->code_income?>' amount='<?=$row->amount?>' pos='<?=$row->pos?>' ishidden='1'>
        <a class="btn" href="#modal-view" tag='edit_d' data-toggle="modal"><i class="glyphicon glyphicon-edit"></i></a>
        <a class="btn" href="#" tag='delete_d'><i class="glyphicon glyphicon-trash"></i></a>
    </div></td>
</tr>
<?
}    
?>
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
<i>No Other Income attached.</i>
<?    
}
}else{
?>
<i>No Other Income attached.</i>
<?      
}
?>
<script>
$("a[tag='edit_d']").click(function(){
    var md = $(this).parent();
    $("#modal-view").find("h3[tag='title']").text("Edit Income");  
    var form_data = {
        employeeid: $(md).attr("employeeid"),
        income: $(md).attr("codeincome"),
        amount: $(md).attr("amount"),
        pos   : $(md).attr("pos"),
        view: "employee/addincomeoth"
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
                pos   : $(md).attr("pos"),
                view: "employee/addincomeoth"
            };
            $.ajax({
                url: "<?=site_url('main/siteportion')?>",
                type: "POST",
                data: form_data,
                success: function(msg){
                   refreshtab("#tab12");
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

$("#addincomeoth").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add Income");  
    var form_data = {
        view: "employee/addincomeoth"
    };
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
             loadotherincomeinfo();
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});
 setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn delay-1s");
  }, 2000);
</script>