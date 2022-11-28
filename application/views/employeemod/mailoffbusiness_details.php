<?php

/**
 * @author Justin
 * @copyright 2016
 */

$empoffbusiness = $this->employeemod->loadoffbusstatus($category,$dfrom,$dto,$dept,$cnoti);
$otherType = array(
                    "ABSENT"          => "ABSENT",
                    "DIRECT"          => "OFFICIAL BUSINESS",
                    "NO PUNCH IN/OUT" => "CORRECTION OF TIME IN/OUT "
                  );
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#offbush tr td,#offbush tr th{
    text-align: center;
}
#offbush tr th{
    background-color: #510051;
    color: #ADAD0E;
}
</style>
<div id="offbushistory" style="padding-bottom: 31px;">
    <table class="table table-hover table-bordered datatable" id="offbush">                                                     
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
    if($empoffbusiness->num_rows() > 0){
    ?>
        <tbody id="manageleave">                                                               
    <?
    
    foreach($empoffbusiness->result() as $row){
    ?>
	
      <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">
        <td class="align_center col-md-1">
          <div class="btn-group">
            <a class="btn" href="#modal-view" tag='edit_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->id?>" ><i class="glyphicon glyphicon-edit"></i></a>
            <!--<a class="btn" tag='delete_d' code="<?=$row->id?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>-->
          </div>
        </td>
        <td><?=$row->employeeid?></td>
        <td><?=$row->fullname?></td>
        <td><?=$row->deptid?></td>
        <td><?=$otherType[$row->othertype]?></td>
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
    $("#modal-view").find("h3[tag='title']").text(code ? "Official Business" : "");
    $("#button_save_modal").text("Save");  
    var form_data = {
                    code: code,
                    idnum: idnum,
                    category: "<?=$category?>",
                    job : "edit",
                    folder   : "employeemod", 
                    view     : "leaveapplydaily",
                    dept     : "<?=$dept?>"   
                    };
					
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
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
            view_offbus_status();
        }
    }); 
    }
});
$("#offbush").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
</script>