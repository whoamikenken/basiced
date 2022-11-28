<?php

/**
 * @author Justin
 * @copyright 2016
 */

$employeeleave = $this->employeemod->loadleavestatus($category,$dfrom,$dto,$dept,$cnoti);

?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#leaveh tr td,#leaveh tr th{
    text-align: center;
}
#leaveh tr th{
    background-color: #3b5998;
    color: #ADAD0E;
}
</style>
<div id="leavehistory" style="padding-bottom: 31px;">
    <table class="table table-hover table-bordered datatable" id="leaveh">                                                     
        <thead>
            <tr>
                <th rowspan="2"></th>
                <th rowspan="2" class="sorting_asc">Employee ID</th>
                <th rowspan="2">Full Name</th>
                <th rowspan="2">Department</th>
                <th rowspan="2">Leave Type</th>
                <th rowspan="2">Date Applied</th>
                <th colspan="2">Inclusive Dates</th>
                <th rowspan="2">Status</th>                
            </tr>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
    <?
    if($employeeleave->num_rows() > 0){
    ?>
        <tbody id="manageleave">                                                               
    <?
    
    foreach($employeeleave->result() as $row){
    ?>
      <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">
        <td class="align_center col-md-1">
          <div class="btn-group">
			<?if($row->status != "CANCELED"){?>
            <a class="btn" href="#modal-view" tag='edit_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->id?>" ltype="<?=$row->other?>" ><i class="glyphicon glyphicon-edit"></i></a>
			<?}else{?>
			<a class="btn" href="#modal-view" tag='view_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->id?>" ltype="<?=$row->other?>" ><i class="icon-zoom-in"></i></a>
			<?}?>
			<?if($row->status == "APPROVED" && $row->deptid == "HR"){?>
            <a class="btn" tag='cancel_d' code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" ltype="<?=($row->type == "other" ? $row->other : $row->type)?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>
			
			<?}?>
            <!--<a class="btn" tag='delete_d' code="<?=$row->id?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>-->
          </div>
        </td>
        <td><?=$row->employeeid?></td>
        <td><?=$row->fullname?></td>
        <td><?=$row->deptid?></td>
        <td><?=$this->employeemod->othLeaveDesc(($row->type == "other" ? $row->other : $row->type))?></td>
        <td><?=date('F d, Y',strtotime($row->timestamp))?></td>
        <td><?=date('F d, Y',strtotime($row->datefrom))?></td>
        <td><?=date('F d, Y',strtotime($row->dateto))?></td>
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
<script>
$("a[tag='edit_d']").click(function(){
    var code = "";  
    var idnum = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    if($(this).attr("idnum")) idnum = $(this).attr("idnum");
    
    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Leave Status" : "");
    $("#button_save_modal").text("Save");  
    
                        
    if($(this).attr("ltype") == "DA"){
        var form_data = {
                            code: code,
                            idnum: idnum,
                            category: "<?=$category?>",
                            job : "edit",
                            folder   : "employeemod", 
                            view     : "leaveapplydaily",
                            dept     : "<?=$dept?>"   

                        }
						
        $.ajax({
            url      : "<?=site_url("employeemod_/fileconfig")?>",
            type     : "POST",
            data     : form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });
    }else{
        var form_data = {
                            code: code,
                            idnum: idnum,
                            category: "<?=$category?>",
                            job : "edit",
                            folder   : "employeemod", 
                            view     : "mailleaveapp_manage",
                            dept     : "<?=$dept?>"   
                        }
        $.ajax({
           url      :   "<?=site_url("employeemod_/fileconfig")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
           }
        }); 
    }
});
$("a[tag='delete_d']").click(function(){
    var id = $(this).attr("code");
    var confirmdel = confirm("Are you sure you want to delete this?");
    if(confirmdel == true){
        var form_data = {
                            code: id,
                            job : "delete",
                            folder   : "employeemod", 
                            view     : "mailleaveapp_manage"
                        }
        $.ajax({
        url      :   "<?=site_url("employeemod_/fileconfig")?>",
        type     :   "POST",
        data     :   form_data,
        success: function(msg){
            var message = $(msg).find("message").text();
            alert(message);
            view_leave_status();
        }
    }); 
    }
});

$("a[tag='cancel_d']").click(function(){
    var employeeid = $(this).attr("code");
    var idnum = $(this).attr("idnum");
    var ltype = $(this).attr("ltype");
    var confirmcancel = confirm("Are you sure you want to cancel this?");
    if(confirmcancel == true){
        var form_data = {
                            employeeid: employeeid,
                            idnum: idnum,
                            ltype: ltype,
                            job : "cancel",
                            folder   : "employeemod", 
                            view     : "mailleaveapp_manage"
                        }
        $.ajax({
        url      :   "<?=site_url("employeemod_/fileconfig")?>",
        type     :   "POST",
        data     :   form_data,
        success: function(msg){
            var message = $(msg).find("message").text();
            alert(message);
            view_leave_status();
        }
    }); 
    }
});

$("a[tag='view_d']").click(function(){
    var code = "";  
    var idnum = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    if($(this).attr("idnum")) idnum = $(this).attr("idnum");
    var form_data = {
                        code: code,
                        idnum: idnum,
                        job : "lview",
                        folder   : "employeemod", 
                        view     : "mailleaveapp_manage"
                    }
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#modal-view").find("div[tag='display']").html(msg);
       }
    }); 
});

$("#leaveh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
</script>