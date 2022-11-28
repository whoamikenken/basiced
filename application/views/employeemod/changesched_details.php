<?php

/**
 * @author Justin
 * @copyright 2016
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
    background-color: #505050 ;
    color: #ffffff;
}
input[name=mar]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}
</style>
<table class="table table-hover table-bordered datatable" id="seminarh">
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
        $query = $this->employeemod->displayschedrequesthistory($category,$this->input->post("indi"));
        if($query->num_rows() > 0){
    ?>
    <tbody>
        <?
            foreach($query->result() as $row){
            $bold = $row->isread ? "" : "style='font-weight: bold;'";
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>
                <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" indi="<?=$this->input->post("indi")?>" ><i class="icon-large glyphicon glyphicon-edit"></i></a></td>
                <td><?=$row->employeeid?></td>
                <td><?=$this->employee->getfullname($row->employeeid)?></td>
                <td><?=date("F d, Y",strtotime($row->timestamp))?></td>
                <td><?=date("F d, Y",strtotime($row->dateedit))?></td>
                <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
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
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        indi     : $(this).attr("indi"),
                        job      : "view",
                        mod      : "emph",
                        folder   : "employeemod", 
                        view     : "changesched_view"
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
                        view     : "changesched_approval_list"
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
$("input[name='mar']").click(function(){
   var cval  = $(this).val();
   var idkey = $(this).attr("idkey");
   $(this).attr("disabled",true);
   $(this).closest("tr").removeAttr("style");
   $.ajax({
           url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "seminar_app"},
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
$("#seminarh_length").append("<span style='margin-left: 45px;'>Status : <select id='seminarstatus' style='margin-bottom: 2px;'><?=$this->extras->showCategoryopt(($this->employeemod->seminarnotif()->num_rows() ? "APPROVED" : ""))?></select></div>");
$("#seminarstatus").change(function(){ changesched($(this).val()); });
$(".no-sort").removeClass("sorting");
</script>