<?php

$stat   = isset($stat) ? $stat : "";
$show   = false;

?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#leaveh tr td,#leaveh tr th{
    text-align: center;
}
#leaveh tr th{
    color: #000000;
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
<br>
<table class="table table-striped table-bordered table-hover" id="leaveh">
    <thead>
        <tr style="background-color: #0072c6;">
            <th rowspan="2" class="mh" >&nbsp;</th>
            <th rowspan="2">Date Applied</th>
            <th colspan="2">Inclusive Dates</th>
            <th rowspan="2">Seminar Category</th>
            <th rowspan="2">Details</th>
            <th rowspan="2">Approving Authority</th>
            <th rowspan="2">Status</th>
            <th rowspan="2">Mark as read</th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th>From</th>
            <th>To</th>
        </tr>
    </thead>
    <?
        if(sizeof($seminar_list) > 0){
    ?>
    <tbody>
        <?
           // echo "<pre>"; print_r($seminar_list); die;
            foreach($seminar_list as $key => $row)
            {
             
                $nomodif = false;                
                $bold = $row->isread ? "" : "style='font-weight: bold;'";
                if($row->dstatus == "APPROVED" || $row->cstatus == "APPROVED" || $row->hrstatus == "APPROVED" || $row->cpstatus == "APPROVED" || $row->fdstatus == "APPROVED" || $row->bostatus == "APPROVED" || $row->pstatus == "APPROVED" || $row->upstatus == "APPROVED" || $row->status == "DISAPPROVED" || $row->status == "APPROVED")
                    $nomodif = true;
                else   
                    $show   = true;
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : ($row->status == "CANCELED" ? " style='background: #ffcccc'" : ""))?>>
                <td class="mh" >
                    <?if(!$nomodif){?>                
                        <div class="btn-group">
                            <a class="btn btn-info" tag="editrequest" style="margin-right: 10px;" href="#" data-toggle="modal" data-target="#mymodalleave" idkey="<?=$row->seminarid?>"  base_id="<?=$row->base_id?>" ><i class="icon glyphicon glyphicon-edit"></i></a>
                            <a class="btn btn-danger" tag="delrequest" href="#" idnum="<?=$row->seminarid?>" ><i class="icon glyphicon glyphicon-trash"></i></a>
                        </div>
                    <?}?>                    
                </td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->date_applied))?></td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->datesetfrom))?></td>
                <td <?=$bold?> ><?=date('F d, Y',strtotime($row->datesetto))?></td>
                <td 
                  <?
                  $seminarList = Globals::seminarList();
                  foreach($seminarList as $c=>$val){
                    if($c == $row->category){
                      ?><?=$bold?>><?=$val?><?    
                    }
                  }
                  ?>
                </td>
                <td><a href="#" tag='view_d' data-toggle="modal" data-target="#mymodalleave" code="<?=$row->employeeid?>" idkey="<?=$row->seminarid?>" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><a href="#" tag='view_app' data-toggle="modal" data-target="#mymodalleave" idkey="<?=$row->seminarid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td <?=$bold?>><?=$row->status?></td>
                <td width="1%"><input type="checkbox" value="1" name="mar" idkey="<?=$row->seminarid?>"  <?=($row->isread ? " checked disabled" : "")?>  /></td>
            </tr>   
        <?
            }
        ?>
    </tbody>
    <?
        }
    ?>
    
</table>
<div class="modal fade" id="mymodalleave" data-backdrop="static"></div>
<?if($show){?><script>$(".mh").show();</script><?}?>
<script>
$("a[tag='view_d']").click(function(){
    var base_id = "";  
    var idkey = "";  
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        job             : "view",
                        view            : "seminar_details_emp"
                    };
    $.ajax({
       url      :   "<?=site_url("seminar_/getSeminarDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
       }
    }); 
});
$("a[tag='view_app']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey
                    };
    $.ajax({
       url      :   "<?=site_url("seminar_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "leave_app_emplist"},
           success  : function(msg){
            alert(msg);
            $(".inner_navigation .main li .active a").click();
           }
        }); 
});
$("a[tag='editrequest']").click(function(){
    var base_id = "";  
    var idkey = "";  
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        job             : "view",
                        view            : "seminar_app"
                    };
    $.ajax({
       url      :   "<?=site_url("seminar_/getSeminarDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
       }
    }); 
});
$("a[tag='delrequest']").click(function(){
   var pmpt = confirm("Do you really want to delete this request?");
   if(pmpt){
       $.ajax({
        url      :   "<?=site_url("seminar_/deleteSeminarApp")?>",
        type     :   "POST",
        data     :   {id: $(this).attr("idnum")},
        success  :   function(msg){
            alert(msg);
            location.reload();
        }
       }); 
   }
});
$("#leaveh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$("#leaveh_length").append("<span style='margin-left: 45px;'>Status : <select id='leavestatus' class='form-control' style='margin-bottom: 2px; width:220px;'><?=$this->extras->showCategoryopt($stat)?></div>");
$("#leavestatus").change(function(){ getEmpSeminarHistory($(this).val()); });
</script>