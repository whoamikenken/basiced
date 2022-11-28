<?php

/**
 * @author Justin
 * @copyright 2015
 */

/**
 * @edit Angelica
 * For new ob process and tables
 */

if(empty($category)) $category = "";
$employeeleave1 = $this->employee->empoblist($category,$dfrom,$dto,$deptid);

# for ica-hyperion 21194
# by justin (with e)
$employeeleave = $this->employee->empObListForAdmin($category,$dfrom,$dto,$deptid,$otherType);
// echo $this->db->last_query();
# echo "<pre>". var_dump($employeeleave);
# end for ica-hyperion 21194

?>
<input type="hidden" id="otherType" value="<?= $otherType ?>">
<div class="panel">
   <div class="panel-heading" style="background-color: #0072c6;"><h4><b><?=($category) ? ucfirst(strtolower($category))."  Application" : "All Application"?> List</b></h4></div>
   <div class="panel-body">
       <table class="table table-striped table-bordered table-hover" id="table">                                                          
        <thead style="background-color: #0072c6;">
            <tr>
                <th class="align_center">Action</th>
                <th class="sorting_asc">Employee ID</th>
                <th>Full Name</th>
                <!-- <th>Leave Type</th> -->
                <th>No. of Days</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Status</th>
                <th>Date Applied</th>
                <th>Details</th>
				<th>Approving Authority</th>
            </tr>
        </thead>
        <tbody id="manageleave">                                                               
    <?
        # for ica-hyperion 21194
        # by justin (with e)
        # > idisplay yung list ng ob
        #echo "<pre>"; print_r($employeeleave); echo "</pre>";
        foreach ($employeeleave as $data) {
            $data = Globals::_array_XHEP($data);
            # convert into var
            extract($data);
    ?>  
        <tr>
            <td style='text-align:center' width="10%">
                <?
                   # displayed edit button if allowable to edit
                   if($isEdit){
                ?>
                    <a href="#" class="btn btn-info" tag='' data-toggle="modal" data-target="#myModal"  id="<?=$id?>" onclick="editRequest(this.id)" title="View Approval Status" ><i class="glyphicon glyphicon-edit"></i></a>
                <? } #end of condition
                ?>

                <a href="#" class="btn btn-danger" tag='' data-toggle="modal" data-target=""  id= "<?=$id?>" onclick="delRequest(this.id)" title="View Approval Status" ><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td><?=$empID?></td>
            <td><?=$fullname?></td>
            <td><?=$nodays?></td>
            <td><?=date("F d, Y", strtotime($dfrom))?></td>
            <td><?=date("F d, Y", strtotime($dto))?></td>
            <td><?=$status?></td>
            <td><?=date("F d, Y", strtotime($cdate))?></td>
            <td style='text-align:center'>
                <a href="#" tag='view_details' data-toggle="modal" data-target="#myModalleave" idkey="<?=$id?>" code="<?=$empID?>" title="View Approval Status" onclick='viewDetails(this)'>
                    <i class="icon-large icon-eye-open"></i>
                </a>
            </td>
            <td style='text-align:center'>
                <a href="#" tag='view_app' data-toggle="modal" data-target="#myModalleave" idkey="<?=$id?>" title="View Approval Status" >
                    <i class="icon-large icon-eye-open"></i>
                </a>
            </td>
        </tr>
    <?  } # end if condition
        # end for ica-hyperion 21194
    ?>
    </tbody>
    </table>
    </div>
</div>
<div class="modal fade" id="myModalleave" data-backdrop="static"></div>
<script>
var toks = hex_sha512(": ");
$(document).ready(function(){
    var table = $('#table').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

// for ica-hyperion 21194
// by justin (with e)
// > new script function added

// > delete request
function delRequest(id){
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
                url : "<?=site_url("ob_application_/delRequest")?>",
                type : "POST",
                data : {id:id},
                success : function(msg){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    })
                    view_leave_status();
                }
            });
        } else if (
        result.dismiss === Swal.DismissReason.cancel
        ) {
            var msg = "Official Business application is safe.";
            if ($("#otherType").val() == "CORRECTION") msg = "Correction for Time In/Out Application is safe.";
            swalWithBootstrapButtons.fire(
                'Cancelled',
                msg,
                'error'
            )
        }
    });
}

function editRequest(id){
    //alert(id);

    var form_data = {
                        idkey           : id,
                        // baseid          : base_id,
                        job             : "view",
                        view            : "<?=($otherType == "DA") ? "ob_apply" : "correction_apply" ?>",
                        target          : "<?=$otherType?>"
                    };

    $.ajax({
        url      :   "<?=site_url("ob_application_/getLeaveDetails")?>",
        type     : "POST",
        data     : form_data,
        success: function(msg){
            $("#myModal").html(msg);
        }
    }); 
}
// end for ica-hyperion 21194

// $('#table tbody').on('click', 'a[tag="view_app"]', function () {
$( "#table" ).delegate( "a[tag='view_app']", "click", function() {    
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list_overtime"
                    };
    $.ajax({
       url      :   "<?=site_url("ob_application_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalleave").html(msg);
       }
    });
});


function viewDetails(a_this){
    $.ajax({
        url : "<?=site_url("ob_application_/getLeaveDetails")?>",
        type : "POST",
        data : {
            idkey : $(a_this).attr("idkey"),
            code  : $(a_this).attr("code"),
            job   : "view",
            view  : "ob_details_emp"
        },
        success : function(content){
            $("#myModalleave").html(content);
        }
    });
}

</script>