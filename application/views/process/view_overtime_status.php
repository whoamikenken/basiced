<?php

/**
 * @author Justin
 * @copyright 2016
 */

$CI =&get_instance();
$CI->load->model('overtime');

$approver_list = array("status", "dstatus", "cstatus", "hrstatus", "cpstatus", "fdstatus", "bostatus", "pstatus", "upstatus");
?>
<a id="open-modal" href="#" data-toggle="modal" data-target="#myModal" hidden></a>
    <table class="table table-striped table-bordered table-hover" id="oth">
        <thead style="background-color: #0072c6;">
            <tr>
                <th  rowspan="2" class="align_center">Action</th>
                <th rowspan="2" class="sorting_asc">Employee ID</th>
                <th rowspan="2">Full Name</th>
                <th rowspan="2">Date Applied</th>
                <th colspan="2">Inclusive Dates</th>
                <th colspan="2">Time</th>
                <th rowspan="2">Total Hour/s</th>
                <th rowspan="2">Details</th>
                <!--<th rowspan="2">Reason</th>-->
				<th rowspan="2">Approving Authority</th>
                <th rowspan="2">Status</th>
            </tr>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Start</th>
                <th>End</th>
            </tr>
        </thead>
        <?
            $query = $this->employee->empovertimelist2($dfrom,$dto,$category,$deptid);
            if($query)
			{
            if($query->num_rows() > 0){
        ?>
        <tbody>
            <?
                foreach($query->result() as $row){
                    $is_edit = true;
                    foreach ($approver_list as $column) if($row->{$column} != "PENDING") $is_edit = false;

                    # ica-hyperion 21535
                    # by justin (with e)
                                        
                    $isDisplayedDelete = true;
/*                    if($row->status == "APPROVED"){
                        $isDisplayedDelete = $CI->overtime->isAllowedToDeleteRequest($row->base_id, $row->employeeid, $row->dfrom, $row->dto);
                    }*/
                    # end of ica-hyperion 21535
            ?>
                <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">
                    <td class="align_center" width="10%"> 
                        <button class="btn btn-info" tag="edit_request" base_id="<?=$row->base_id?>" <?=($isDisplayedDelete && $row->status == "PENDING") ? "" : "style='display:none;' " ?> ><span class="glyphicon glyphicon-edit"></span></button>
                        <button class="btn btn-danger" tag="delete_request" base_id="<?=$row->base_id?>" <?=($isDisplayedDelete) ? "" : "hidden" ?> ><span class="glyphicon glyphicon-trash"></span></button>
                    </td>
                    <td><?=Globals::_e($row->employeeid)?></td>
                    <td><?=Globals::_e($row->fullname)?></td>
                    <td><?=date('F d, Y',strtotime($row->timestamp))?></td>
                    <td><?=date('F d, Y',strtotime($row->dfrom))?></td>
                    <td><?=date('F d, Y',strtotime($row->dto))?></td>
                    <td><?=date('h:i A',strtotime($row->tstart))?></td>
                    <td><?=date('h:i A',strtotime($row->tend))?></td>
                    <td><?=Globals::_e($row->total)?></td>
                    <td style='text-align:center'>
                        <a href="#" tag='view_d' data-toggle="modal" data-target="#mymodalleave" code="<?=$row->employeeid?>" idkey="<?=$row->base_id?>" ><i class="icon-large icon-eye-open"></i></a>
                    </td>
					<td style='text-align:center'><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->otid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                    <!--<td><?=$row->reason?></td>-->
                    <td><?=$row->status?></td>
                </tr>
            <?
                }
			}
            ?>
        </tbody>
        <?
            }
        ?>
        
    </table>
</div>
<div class="modal fade" id="myModalot" data-backdrop="static"></div>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>

$(document).ready(function(){
    var table = $('#oth').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$( "#oth" ).on( "click", "a[tag='view_app']", function() {
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list_overtime"
                    };
    $.ajax({
       url      :   "<?=site_url("overtime_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});

// for ica-hyperion 21535
// by justin (with e)
$( "#oth" ).on( "click", "button[tag='delete_request']", function() {
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

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
       var base_id = $(this).attr("base_id");

        $.ajax({
            url : "<?=site_url("overtime_/deleteOvertimeRequest")?>",
            type : "POST",
            data : {
                    base_id : base_id
                   },
            success : function(msg){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: msg,
                    showConfirmButton: true,
                    timer: 2000
                });
                view_overtime_status();
            }
        });
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Overtime application is safe.',
         'error'
       )
     }
   })
});
// end for ica-hyperion 21535

$( "#oth" ).on( "click", "button[tag='edit_request']", function() {
    $.ajax({
        url : "<?=site_url("overtime_/modifyOTManagementRequest")?>",
        type : "POST",
        data : {id : $(this).attr("base_id")},
        success : function(content){
            $("#myModal").html(content);
            $("#open-modal").click();
        }
    });
});

$( "#oth" ).on( "click", "a[tag='view_d']", function() {
    $.ajax({
        url : "<?=site_url("overtime_/viewOvertimeDetails")?>",
        type : "POST",
        data : {id : $(this).attr("idkey")},
        success : function(content){
            $("#myModal").html(content);
            $("#open-modal").click();
        }
    });
});

</script>