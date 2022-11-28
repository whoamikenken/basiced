<?php

/**
 * @author Justin
 * @copyright 2016
 */

//$employeeleave = $this->employeemod->loadseminarstatus($category,$dfrom,$dto,$cnoti);
$seminar_query_list = $this->employeemod->loadseminarstatusnew($category,$dfrom,$dto,$cnoti);
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#seminartbl tr td,#seminartbl tr th{
    text-align: center;
}
#seminartbl tr th{
    background-color: #510051;
    color: #ADAD0E;
}
</style>
<div id="seminartblistory" class="well-content" style="padding-bottom: 31px;">
    <table class="table table-hover table-bordered datatable" id="seminartbl">                                                     
        <thead>
            <tr>
                <th rowspan="2"></th>
                <th rowspan="2">Employee ID</th>
                <th rowspan="2">Full Name</th>
                <th rowspan="2">Course Title</th>
                <th rowspan="2">Date Applied</th>
                <th colspan="2">Inclusive Dates</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Approving Authority</th>
                <th colspan="3">Post Activity Report</th>
            </tr>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Report</th>
                <th>Date Submitted</th>
                <th>Status</th>
            </tr>
        </thead>
    <?
    # new condition for ica-hyperion 21128
    #echo "<pre>". var_dump($seminar_query_list);
    foreach ($seminar_query_list as $key => $val) {
       $employeeleave = $this->db->query($val);
      if($employeeleave->num_rows() > 0){
        ?>
            <tbody id="manageleave">                                                               
        <?
        
        foreach($employeeleave->result() as $row){
        ?>
          <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">
            <td class="align_center col-md-1">
              <div class="btn-group">
                <a class="btn" tag='edit_d' href="#" data-toggle="modal" data-target="#myModalseminar" idkey="<?=$key."-".$row->id?>" ><i class="glyphicon glyphicon-edit"></i></a>
                <!--<a class="btn" tag='delete_d' code="<?=$row->id?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>-->
              </div>
            </td>
            <td><?=$row->employeeid?></td>
            <td><?=$row->fullname?></td>
            <td><?=$row->course?></td>
            <td><?=date('F d, Y',strtotime($row->dateapplied))?></td>
            <td><?=date('F d, Y',strtotime($row->dfrom))?></td>
            <td><?=date('F d, Y',strtotime($row->dto))?></td>
            <td><?=$row->status?></td>
            <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalseminar" idkey="<?=$row->aid?>" title="View" ><i class="icon-large icon-eye-open"></i></a></td>
            <!--<td class="align_center col-md-1"><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalseminar" idkey="<?=$row->id?>" ><i class="icon-large icon-eye-open"></i></a></td>-->
            <td><a href="#" tag='att_report' data-toggle="modal" data-target="#myModalseminar" idkey="<?=$row->aid?>" ><i class="icon-large icon-briefcase"></i></a></td>
            <td><?=($row->dateattached ? date("F d, Y",strtotime($row->dateattached)) : "")?></td>
            <td><?=$row->attstat?></td>
          </tr>
        <?
        }
        ?>
        </tbody>
        <?
      } // end of if employeeleave
    } // end of foreach seminar_query_list
    ?>
    </table>
    <div class="modal fade" id="myModalseminar" data-backdrop="static"></div>
</div>
<script>
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey: idkey,
                        job : "view",
                        folder   : "employeemod", 
                        view     : "mailseminarapp_view",
                        manage   : "1"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalseminar").html(msg);
       }
    });
});

$("a[tag='edit_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey: idkey,
                        job : "edit",
                        mod : "",
                        folder   : "employeemod", 
                        view     : "mailseminarapp_view",
                        manage   : "1"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalseminar").html(msg);
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
        $("#myModalseminar").html(msg);
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
        $("#myModalseminar").html(msg);
       }
    });
});

$("#seminartbl").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});

</script>