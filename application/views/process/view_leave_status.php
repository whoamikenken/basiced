<?php

/**
 * @author Justin
 * @copyright 2015
 */

if(empty($category)) $category = "";
// $employeeleave = $this->employee->empleavelist($category,$ltype,$dfrom,$dto,$deptid,$othtype,$noDA);
// $employeeleave1 = $this->employee->empleavelist($category,$ltype,$dfrom,$dto,$deptid,$othtype);
$employeeleave = $this->leave_application->getEmpLeaveListByAdmin($category,$ltype,$dfrom,$dto,$deptid,$othtype);
// echo '<pre>'; print_r($employeeleave); die;
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
table tr td,#leaveh tr th{
    text-align: center;
}
#leave_list_table {
  table-layout: fixed;
  width: 100% !important;
}
#leave_list_table td,
#leave_list_table th{
  width: auto !important;
}
</style>
<div class="panel">
   <div class="panel-heading" style="background-color: #0072c6;"><h4><b><?=$category?> LIST</b></h4></div>
     <div class="panel-body">
    <table class="table table-striped table-bordered table-hover" id="leave_list_table" >                                                              
        <thead>
            <tr style="background-color: #0072c6;">
                <th class="align_center">Action</th>
                <th class="sorting_asc">Employee ID</th>
                <th>Full Name</th>
                <th>Leave Type</th>
                <th>Credit/s to be Deduct</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Status</th>
                <th>Date Applied</th>
                <th>Details</th>
                <th>Approving Authority</th>
				<th>Liquidated?</th>
            </tr>
        </thead>
        <tbody id="manageleave">     
    <?
        
        #echo "<pre>"; print_r($employeeleave); echo "</pre>";
        # for ica-hyperion 21194   
        # by justin (with e)
        foreach ($employeeleave as $list) {
            $list = Globals::_array_XHEP($list);
            # change list to variable
            extract($list);
           
    ?>
        <tr employeeid='<?=$id?>' style="cursor: pointer;">
            <td style="text-align: center;" width='10%'>
                <?
                  if($status == "PENDING"){
                    # kapag approved na.. Delete button ang lilitaw...
                ?>
                    <a id="<?=$id?>" onclick="editRequest(this.id)" class="btn btn-info" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-edit"></i></a>
                <?} # end of if condition..
                ?>
                <?php if($status != "CANCELLED"): ?>
                    <a id="<?=$id?>" onclick="deleteRequest(this.id)" class="btn btn-danger"><i class="glyphicon glyphicon-trash"></i></a>
                <?php endif ?>
            </td>

            <td><?=$empID?></td>
            <td><?=$fullname?></td>
            <td><?=$lDesc?></td>
            <td><?=$nodays?></td>
            <td><?=date("F d, Y", strtotime($dfrom))?></td>
            <td><?=date("F d, Y", strtotime($dto)) ?></td>
            <td><?=$status?></td>
            <td><?=date("F d, Y", strtotime($date_applied))?></td>
            <td>
                <a href="#" tag='view_d' data-toggle="modal" data-target="#mymodalleave" code="<?=$empID?>" idkey="<?=$id?>" ><i class="icon-large icon-eye-open"></i></a>
            </td>
            <td style='text-align:center'>
                <!-- <a href="#" tag='view_d' data-toggle="modal" data-target="#mymodalleave" code="<?=$empID?>" idkey="<?=$id?>" ><i class="icon-large icon-eye-open"></i></a> -->

                <a href="#" tag='view_app' data-toggle="modal" data-target="#myModal" idkey="<?=$id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a>
            </td>
            <td><select class="form-control liquidated" idkey="<?=$baseID?>" <?=(($status == "PENDING") || $status == "APPROVED" && $liquidated == "YES") ? " disabled" : ""?>  style="<?=$lDesc == 'Professional' ? '' : 'display: none;'?>">
                <option value="YES" <?=($liquidated == "YES") ? " selected" : ""?> >YES</option>
                <option value="NO" <?=($liquidated == "NO") ? " selected" : ""?>>NO</option>
            </select></td>
        </tr>
    <?
        } # end of foreach loop
        # end for ica-hyperion 21194
    ?>
    </tbody>
    </table>
    </div>
</div>
<div class="modal fade" id="mymodalleave" data-backdrop="static"></div>
<script>
var toks = hex_sha512(" ");
$("a[tag='view_d']").click(function(){
    var base_id = "";  
    var idkey = "";  
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        toks            : toks,
                        idkey           : GibberishAES.enc(idkey, toks),
                        baseid          : GibberishAES.enc(base_id, toks),
                        job             : GibberishAES.enc("view", toks),
                        view            : GibberishAES.enc("leave_details_emp", toks)
                    };
    $.ajax({
       url      :   "<?=site_url("leave_application_/getLeaveDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
       }
    }); 
});

function editRequest(id){   
    var base_id = '';
    var form_data = {
                        toks            : toks,
                        idkey           : GibberishAES.enc(id, toks),
                        baseid          : GibberishAES.enc(base_id, toks),
                        job             : GibberishAES.enc("view", toks),
                        view            : GibberishAES.enc("leave_apply", toks)
                    };
    $.ajax({
       url      :   "<?=site_url("leave_application_/getLeaveDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModal").html(msg);
       }
    }); 
}
function deleteRequest(id){
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   });

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to delete this request?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
       $.ajax({
            // url : "<?=site_url("process_/doDeleteLeaveRequest")?>",
            url : "<?=site_url("leave_application_/deleteLeaveAppByAdmin")?>",
            type : "POST",
            data : {toks:toks,aid : GibberishAES.enc(id, toks)},
            dataType: "json",
            success : function(msg){
                if(msg.err == 0){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 2000
                    });
                    // console.log(msg);
                    view_leave_status();
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 2000
                    });

                    return;
                }
            }
        });
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Leave Application is safe.',
         'error'
       )
     }
   });
}

$(document).ready(function(){
    var table = $('#leave_list_table').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
$('#leave_list_table tbody').on('click', 'a[tag="view_app"]', function () {
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        toks     : toks,
                        idkey    : GibberishAES.enc(idkey, toks),
                        folder   : GibberishAES.enc("employeemod", toks), 
                        view     : GibberishAES.enc("approval_list_overtime", toks)
                    };
    $.ajax({
       url      :   "<?=site_url("leave_application_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModal").html(msg);
       }
    });
});

$('#leave_list_table tbody').on('change', '.liquidated', function () {
    if($(this).val() == "YES"){
        $(this).attr("disabled", "disabled");
    }
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        toks     : toks,
                        idkey    : GibberishAES.enc(idkey, toks),
                        status    : GibberishAES.enc($(this).val(), toks)
                    };
    $.ajax({
       url      :   "<?=site_url("leave_application_/updateLiquidated")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModal").html(msg);
       }
    });
});

</script>