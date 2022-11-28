<?php

/**
 * @author Justin
 * @copyright 2015
 */
$deduction_array = array();

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];    
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
}

$deduction = "";
?>
<style type="text/css">
    .swal2-cancel{
    margin-right: 20px;
}

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
<?
if($empdetails){
$this->db->_reset_select(); /** reset active select */    
$q = $this->db->query("select a.employeeid,a.code_deduction,a.amount,a.datefrom,a.dateto,a.deduction_base,a.nocutoff,a.schedule,a.cutoff_period from employee_deduction a where a.employeeid='{$empdetails}'");
if($q->num_rows()>0){
?>
                  <div class="scrollbar">
                   <table class="table table-striped table-bordered table-hover" id="deductiondetail">
                    <thead style="background-color: #0072c6;">
                        <tr>
                            <th class="col-md-1">#</th>
                            <th class="sorting_asc">Type</th>
                            <!-- <th>Start Date</th> -->
                            <th>Amount</th>
                            <th>No. Of Cut-Off</th>
                            <th>Schedule</th>
                            <th>Period</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="deductiondetails">
<?    
$t = 0;
foreach($q->result() as $row){
$t++;
if($this->payrolloptions->deductiondesc($row->code_deduction)){
?>
<tr>
  <td><?=$t?></td>
  <td><?=$this->payrolloptions->deductiondesc($row->code_deduction)?></td>
  <!-- <td class="align_right"><?=($row->datefrom != "0000-00-00" ? date('F d, Y',strtotime($row->datefrom)) : "")?></td> -->
  <td class="align_right"><?= (is_numeric($row->amount) ? number_format($row->amount, 2) : $row->amount ) ?></td>
  <td class="align_right"><?=$row->nocutoff?></td>
  <td class="align_center"><?=$this->payrolloptions->payscheduledesc($row->schedule)?></td>
  <td class="align_center"><?=$this->payrolloptions->quarterdesc($row->cutoff_period,FALSE,$row->schedule)?></td>
  <td class="col-md-1"><div class="btn-group" employeeid='<?=$row->employeeid?>' codededuction='<?=$row->code_deduction?>' deductionbase='<?=$row->deduction_base?>' amount='<?=$row->amount?>' nocutoff='<?=$row->nocutoff?>' datefrom='<?=$row->datefrom?>' dateto='<?=$row->dateto?>' schedule='<?=$row->schedule?>' period='<?=$row->cutoff_period?>' ishidden='1'>
        <a class="btn btn-info" style="margin-right: 10px;" href="#modal-view" tag='edit_d' data-toggle="modal"><i class="glyphicon glyphicon-edit"></i></a>
        <a class="btn btn-danger" href="#" tag='delete_d'><i class="glyphicon glyphicon-trash"></i></a>
    </div></td>
</tr>
<?
}
}    
?>
                    </tbody>
                    </table>
                  </div>
                     
<?
}else{
?>
<i>No deduction attached.</i>
<?    
}
}else{
?>
<i>No deduction attached.</i>
<?      
}
?>
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    if($("#deductiondetail").html()){
        var table = $('#deductiondetail').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
    }
});

$("a[tag='edit_d']").click(function(){
    var md = $(this).parent();
    $("#modal-view").find("h3[tag='title']").text("Edit deduction");  
    var form_data = {
        employeeid:  GibberishAES.enc($(md).attr("employeeid") , toks),
        deduction: GibberishAES.enc($(md).attr("codededuction")  , toks),
        memberid:  GibberishAES.enc($(md).attr("memberid") , toks),
        amount:  GibberishAES.enc($(md).attr("amount") , toks),
        nocutoff:  GibberishAES.enc( $(md).attr("nocutoff"), toks),
        datefrom: GibberishAES.enc( $(md).attr("datefrom") , toks),
        dateto: GibberishAES.enc($(md).attr("dateto")  , toks),
        schedule:  GibberishAES.enc( $(md).attr("schedule"), toks),
        period:  GibberishAES.enc( $(md).attr("period"), toks),
        ishidden:  GibberishAES.enc($(md).attr("ishidden") , toks),
        view: GibberishAES.enc("employee/adddeduction"  , toks),
        toks:toks
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
                    job:  GibberishAES.enc("delete" , toks),
                    employeeid: GibberishAES.enc($(md).attr("employeeid")  , toks),
                    deduction:  GibberishAES.enc($(md).attr("codededuction") , toks),
                    amount:  GibberishAES.enc($(md).attr("amount") , toks),
                    datefrom: GibberishAES.enc($(md).attr("datefrom")  , toks),
                    dateto:  GibberishAES.enc($(md).attr("dateto") , toks),
                    period:  GibberishAES.enc($(md).attr("period") , toks),
                    view: GibberishAES.enc("employee/adddeduction"  , toks),
                    toks:toks
                };
                $.ajax({
                    url: "<?=site_url('main/siteportion')?>",
                    type: "POST",
                    data: form_data,
                    success: function(msg){
                       loaddeductions();
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
$("#adddeduction").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add deduction");  
    var form_data = {
        view:  GibberishAES.enc("employee/adddeduction" , toks),
        toks:toks
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