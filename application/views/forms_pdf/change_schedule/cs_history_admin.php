<?php
/**
 * @author justin (with e)
 * @copyright 2017
 *  
 * para sa change schedule history for admin.. 
 * ica-hyperion 21194
 */
?>

<style>
.dataTables_paginate {
    margin-top: 6px;
}
#seminarh tr td,#seminarh tr th{
    text-align: center;
}
#seminarh tr th{
    background-color: #8b9dc3;
    color: #FFFFFF;
}
</style>
<table class="table table-hover table-bordered datatable" id="seminarh">
    <thead>
        <tr>
            <!-- <th>Select All <br><br> <input type='checkbox' name='multiplecheckbox'></th> -->
            <th>Approval</th>
            <th>Employee Number</th>
            <th>Employee Name</th>
            <th>Date Applied</th>
            <th>Effectivity Date</th>                        
            <th>Approving Authority</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Mark as read</th>
        </tr>
    </thead>
    <tbody id="manageot">                                                               
            <?
              # displayed list here
              foreach ($cs_list as $list) {
                extract($list); # gawin variable yung mga list sa cs_list


                /*status*/
            ?>
                <tr>
                  <!-- approval -->
                  <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" base_id="<?=$base_id?>" idkey="<?=$csid?>"><i class="icon-large glyphicon glyphicon-edit"></i></a></td>
                  
                  <!-- employee number -->
                  <td><?=$empId?></td>
                  
                  <!-- employee name -->
                  <td><?=$fullname?></td>
                  
                  <!-- date applied -->
                  <td><?=date('F d, Y',strtotime($timestamp))?></td>

                  <!-- effective date -->
                  <td><?=$date_effective?></td>

                  <!-- approving authority -->
                  <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" base_id="<?=$base_id?>" idkey="<?=$csid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>

                  <!-- reason -->
                  <td><?=$reason?></td>

                  <!-- status -->
                  <td><?=$status?></td>

                  <!-- mark as read -->
                  <td></td>
                </tr>
            <?} # end of foreach for $cs_list
            ?>
    </tbody>

    
</table>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");

    var colhead = $(this).attr('colhead'),
        colstatus = $(this).attr('colstatus'),
        isLastApprover = $(this).attr('isLastApprover');

    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        colhead         : colhead,
                        isLastApprover  : isLastApprover,
                        job             : "edit",
                        view            : "cs_details_manage"
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
$("input[name='mar']").click(function(){
   var cval  = $(this).val();
   var idkey = $(this).attr("idkey");
   $(this).attr("disabled",true);
   $(this).closest("tr").removeAttr("style");
   $.ajax({
           url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "change_sched_app_emplist"},
           success  : function(msg){
            location.reload();
           }
        });
});
$(function(){
   $(".par").each(function(){
    if($(this).text() == "")    $("#newrequest").prop("disabled",true);
   }); 
});

$("#seminarh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
/*$("#seminarh_length").append("<span style='margin-left: 45px;'>Status : <select id='changesched' style='margin-bottom: 2px;'><?=$this->extras->showCategoryopt(($this->employeemod->seminarnotif()->num_rows() ? "APPROVED" : ""))?></select></div>");*/
$("#changesched").change(function(){

    // $("#seminarh_length").append("<a href='#' style='margin-left: 45px;' class='btn blue' id='search'>Save</a>");
   // changesched($(this).val());

   });
$(".no-sort").removeClass("sorting");
</script>