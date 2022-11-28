<?php

/**
 * @author Justin
 * @copyright 2016
 */

?>
<div class="well orange">
    <div class="well-header" style="background: #A548A2;">
        <h5><?=$category?> LIST</h5>
    </div>
    <table class="table table-hover table-bordered datatable" id="seminarh">
        <thead>
            <tr>
                <th rowspan="2">Employee ID</th>
                <th rowspan="2">Employee Name</th>
                <th rowspan="2">Date Applied</th>
                <th colspan="2">Inclusive Dates</th>
                <th rowspan="2">Details</th>
				<th rowspan="2">Approving Authority</th>
                <th rowspan="2" class="align_center">Status</th>
                <th colspan="3">Post Activity Report</th>
				
            </tr>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Report</th>
                <th>Date Submitted</th>
            </tr>
        </thead>
        <?
            $query = $this->employee->empseminarlist($dfrom,$dto,$category,$deptid);
            if($query->num_rows() > 0){
        ?>
        <tbody>
            <?
                foreach($query->result() as $row){
            ?>
                <tr>
                    <td><?=$row->employeeid?></td>
                    <td><?=$row->fullname?></td>
                    <td><?=date('F d, Y',strtotime($row->timestamp))?></td>
                    <td><?=date('F d, Y',strtotime($row->startdate))?></td>
                    <td><?=date('F d, Y',strtotime($row->enddate))?></td>
                    <td class="align_center"><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" ><i class="icon-large icon-eye-open"></i></a></td>
					<td style='text-align:center'><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                    <td><?=$row->status?></td>
                    <td class="align_center"><a href="#" tag='att_report' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" ><i class="icon-large icon-briefcase"></i></a></td>
                    <td><?=($row->dateattached ? date("F d, Y",strtotime($row->dateattached)) : "")?></td>
                </tr>  
            <?
                }
            ?>
        </tbody>
        <?
            }
        ?>
        
    </table>
</div>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey: idkey,
                        job : "view",
                        mod : "emph",
                        folder   : "employeemod", 
                        view     : "mailseminarapp_view"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$("a[tag='att_report']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        job      : "view",
                        folder   : "employeemod", 
                        view     : "mailseminarapp_attach"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$("a[tag='view_app']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list_seminar"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$('#seminarh').dataTable({
    bJQueryUI: true,
    "sPaginationType": "full_numbers",
    "responsive": true,
    "bDestroy": true,
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 }
});

</script>