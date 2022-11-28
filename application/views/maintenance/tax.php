<?php

/**
 * @author Robert Ram Bolista
 * @copyright ram_bolista@yahoo.com
 * @date 9-5-2013
 * @time 16:43
 * @modified Justin 2015
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
<!--<a id="addtax" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>-->
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="panel animated fadeIn delay-1s">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Tax</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="taxtable">
                            <thead>
                                <tr>
                                    <th colspan="2">
                                        <center><a id="addtax" href="#modal-view" class="btn btn-primary" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign" aria-hidden="true"></i><span class="" style="font-family: Tahoma;"> Add New Manage Tax </span></a></center>
                                    </th>
                                    <th colspan="3">
                                        <center><a id="adddependent" href="#modal-view" class="btn btn-primary" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New Dependent Setup</span></a></center>
                                    </th>
                                    <th colspan="3">
                                        <center><a id="updatedependent" href="#modal-view" class="btn btn-primary" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i><span class="" style="font-family: Tahoma;"> Update Dependent Setup </span></a></center>
                                    </th>
                                </tr>
                                <tr style="background-color: #0072c6;">
                                    <th class="align_center col-md-1"></th>
                                    <th class="sorting_asc">Payment Type</th>
                                    <th>Tax Range</th>
                                    <th>Base Tax</th>
                                    <th>Base Amount</th>
                                    <th>% in excess</th>
                                    <th>Tax Status</th>
                                    <!--<th>Exemption</th>-->
                                </tr>
                            </thead>   
                            <tbody>
                            <?
                            $sql = $this->db->query("select * from code_tax ORDER BY tax_type,status_")->result();
                            foreach($sql as $mrow){
                            ?>
                                <tr>
                                    <td class="align_center col-md-1">
                                        <a class="btn btn-info" href="#modal-view" tag='edit_d' data-toggle="modal" taxid='<?=$mrow->tax_id?>'><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;
                                        <a class="btn btn-danger" href="#" tag='delete_d' taxid='<?=$mrow->tax_id?>'><i class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                    <!--<td class="align_center"><?=$this->extras->getincomebase($mrow->tax_type)?></td>-->
                                    <td class="align_center"><?=$this->payrolloptions->payscheduledesc($mrow->tax_type)?></td>
                                    <td class="align_center"><?=number_format($mrow->tax_range,2,'.','')?></td>  
                                    <td class="align_center"><?=number_format($mrow->basic_tax,2,'.','')?></td>
                                    <td class="align_center"><?=number_format($mrow->basic_amount,2,'.','')?></td>
                                    <td class='align_right'><?=($mrow->percent ? "{$mrow->percent}%" : "NA")?></td>
                                    <td class="align_center"><?=$this->extras->gettaxstatuscode($mrow->status_)?></td>
                                    <!--<td class="align_right"><?=number_format($mrow->exemption,2,'.','')?></td>-->
                                </tr> 
                            <?    
                            }
                            ?>  
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
    var table = $('#taxtable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$("#addtax,a[tag='edit_d']").click(function(){
    $("#modal-view").find("div[tag='display']").html('');
    var code = "";  
    var tstatus = "";
    var basic = "";
    var basicamt = "";
    var percent = "";
    if($(this).attr("taxid")) code = $(this).attr("taxid");
    
    $(".modal-footer").html('');
    $(".modal-footer").html('<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">Close</a><a href="#" class="btn btn-success" id="button_save_modal">Save</a>');

    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Tax" : "Add Tax");  
    $("#modal-view").find("div[tag='display']").html("<img src='<?=base_url()?>images/loading.gif' />  Finding records, Please Wait..");
    $("#button_save_modal").text("Save");    
    var form_data = {
        toks:toks,
        code: GibberishAES.enc(code, toks)
    };
    $.ajax({
        url: "<?=site_url('maintenance_/manage_tax')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});

// add new dependent
// justin (with e) 8-25-2017
$("#adddependent").click(function(){
    $("#modal-view").find("div[tag='display']").html("<img src='<?=base_url()?>images/loading.gif' />  Finding records, Please Wait..");
    $("#modal-view").find("h3[tag='title']").text("New Dependent Setup");
    
    $(".modal-footer").html('');
    $(".modal-footer").html('<a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">Close</a><a href="#" class="btn btn-success" id="button_save_modal">Save</a>');
    
    
    $("#button_save_modal").text("Save");
    $.ajax({
        url : "<?=site_url('maintenance_/newDependent')?>",
        type : "POST",
        data : "",
        success : function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});
// end of new dependent 

// update dependent 
// justin (with e) 8-25-2017
$("#updatedependent").click(function(){
    $("#modal-view").find("div[tag='display']").html("<img src='<?=base_url()?>images/loading.gif' />  Finding records, Please Wait..");
    $("#modal-view").find("h3[tag='title']").text("Update Dependent Setup");

    $(".modal-footer").html('');
    $(".modal-footer").html('<a href="#" id="cancel_btn" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">Cancel</a><a href="#" class="btn btn-info" id="button_save_modal">Update</a><a href="#" class="btn btn-success" id="delete_btn">Delete</a>');

    $.ajax({
        url : "<?=site_url('maintenance_/updateDependent')?>",
        type : "POST",
        data : "",
        success : function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });

});
// end of update dependent

$("a[tag='delete_d']").click(function(){
 var ans = confirm("Are you sure you want to continue?");
 
 if(ans){
     $.ajax({
        url:"<?=site_url("maintenance_/save_tax")?>",
        type:"POST",
        data:{
           toks: toks,
           taxid: GibberishAES.enc($(this).attr("taxid"), toks),
           job: GibberishAES.enc("delete", toks)  
        },
        success: function(msg){
            $("#modalclose").click();
            $(".inner_navigation .main li .active a").click(); 
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