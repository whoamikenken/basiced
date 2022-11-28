<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
/**
 * @Kennedy Hipolitp
 * @2019
 * @Updated UI
 */

?> 
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
<!--<a id="addphilhealth" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>-->
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="panel animated fadeIn delay-1s">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Philhealth</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="philTable">
                            <thead>
                                <tr>
                                        <th>
                                            <a id="addphilhealth" href="#modal-view" data-toggle="modal" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
                                        </th>
                                    </tr> 
                                <tr style="background-color: #0072c6;">
                                    <th>Action</th>
                                    <th>Salary Bracket</th>
                                    <th>Salary Range</th>
                                    <th>Salary Base</th>
                                    <th>ER</th>
                                    <th>EE</th>
                                    <th>Total Contribution</th>
                                </tr>
                            </thead>   
                            <tbody>
                            <?
                            $sql = $this->db->query("SELECT * FROM philhealth_deduction ORDER BY id");
                            for($r=0;$r<$sql->num_rows();$r++){
                            $mrow = $sql->row($r);   
                            ?>                            
                                <tr>
                                    <td class="align_center col-md-1">
                                            <a class="btn btn-info" href="#modal-view" tag='edit_d' data-toggle="modal" philhealthid='<?=$mrow->id?>'><i class="glyphicon glyphicon-edit"></i></a>
                                            <a class="btn btn-danger" href="#" tag='delete_d' philhealthid='<?=$mrow->id?>'><i class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                    <td class="align_center"><?=$mrow->salary_range?></td>
                                    <td class='align_center'><?=(number_format($mrow->compensationfrom,2,'.','')).' - '.(number_format($mrow->compensationto,2,'.',''))?></td>
                                    <td class="align_center"><?=number_format($mrow->salary_base,2,'.','')?></td>
                                    <td class="align_center"><?=number_format($mrow->emp_er,2,'.','')?></td>
                                    <td class="align_center"><?=number_format($mrow->emp_ee,2,'.','')?></td>
                                    <td class="align_center"><?=number_format($mrow->total_contribution,2,'.','')?></td>
                                </tr>
                            <?}?>   
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
</div>    
</div> 
<script>
var toks = hex_sha512(" ");
validateCanWrite();
$(document).ready(function(){
    var table = $('#philTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
    
$("#addphilhealth,a[tag='edit_d']").click(function(){
    var id = "";  
    
    if($(this).attr("philhealthid")) id = $(this).attr("philhealthid");
    
    $("#modal-view").find("h3[tag='title']").text(id ? "Edit PhilHealth" : "Add PhilHealth");
    $("#button_save_modal").text("Save");     
    var form_data = {
        toks:toks,
        id: GibberishAES.enc(id, toks)
    };
    $.ajax({
        url: "<?=site_url('maintenance_/manage_philhealth')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});
$("a[tag='delete_d']").click(function(){
 var ans = confirm("Are you sure you want to continue?");
 
 if(ans){
     $.ajax({
        url:"<?=site_url("maintenance_/save_philhealth")?>",
        type:"POST",
        data:{
           toks:toks,
           philhealthid: GibberishAES.enc($(this).attr("philhealthid"), toks),
           job: GibberishAES.enc("delete", toks)  
        },
        success: function(msg){
            $("#modalclose").click();
            location.reload();
        }
     }); 
 }   
 return false;   
});

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");
}

</script>