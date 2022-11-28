<?php

/**
 * @author Justin
 * @copyright 2016
 */
?>
    <div class="panel">
           <div class="panel-heading"><h4><b><?=$category?> LIST</b></h4></div>
           <div class="panel-body">
               <table class="table table-striped table-bordered table-hover" id="oth">
        <thead>
            <tr>
                <th>Approval</th>
                <th>Employee Number</th>
                <th>Employee Name</th>
                <th>Date Applied</th>
                <th>Effectivity Date</th>                        
                <th>Approving Authority</th>
                <th>Status</th>
            </tr>
        </thead>
        <?
            if($cs_list->num_rows() > 0){
        ?>
        <tbody>
            <?
                foreach($cs_list->result() as $row){
            ?>
                <tr>
                    <td style='text-align:center'><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->csid?>" base_id="<?=$row->base_id?>"><i class="icon-large glyphicon glyphicon-edit"></i></a></td>
                    <td><?=$row->employeeid?></td>
                    <td><?=$this->employee->getfullname($row->employeeid)?></td>
                    <td><?=date("F d, Y",strtotime($row->date_applied))?></td>
                    <td><?=date("F d, Y",strtotime($row->date_effective))?></td>
                    <td style='text-align:center'><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->csid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                    <td><?=$row->status?></td>
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
<div class="modal fade" id="myModalot" data-backdrop="static"></div>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>
    
$(document).ready(function(){
    var table = $('#oth').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});


$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        job             : "view",
                        view            : "cs_details_emp"
                    };
    $.ajax({
       url      :   "<?=site_url("schedule_/getSchedDetails")?>",
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
                        view     : "changesched_approval_list"
                    };
    $.ajax({
       url      :   "<?=site_url("schedule_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});

</script>