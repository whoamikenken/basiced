<?php

/**
 * @author Carlos Pacheco
 * @date 6-19-2014 1:15pm
 * 
 */
/**
 * @Kennedy Hipolitp
 * @2019
 * @Updated UI
 */
 
    if(isset($empinfo)){
       $empdetails = $empinfo;    
     }else{
       $empinfo = $this->session->userdata("personalinfo"); 
       $empdetails = $empinfo;
     }
    
    // the unique identifier/employee id of "logger".
    $employeeid = $empdetails[0]['employeeid'];
    
    // instance of the employee model
    // Note: Employee() Model/Class Should contain most of the 
    //          necessary data needed regarding employee information'.
    $empModel = new Employee();
    $infotype = 'rank';
    $pinfo = $empModel->getPersonnelInfoConfigList($infotype);
    
?>
<div id="content">
    <div class="widgets_area">
        <div class="panel  animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Manage Rank</b></h4></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-hover table-condensed" id="civilTable">
                    <thead>
                        <tr>
                            <th>
                                <a class="btn btn-primary addbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
                            </th>
                        </tr>                            
                        <tr style="background-color: #0072c6;">
                            <th width='10%' align="center"><b>Actions</b></th>
                            <th><b>Code</b></th>
                            <th><b>Description</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach( $pinfo as $each ): ?>
                        <tr>
                            <td class="align_center">
                                <a id="<?=$each->rankid;?>" class="btn btn-info editbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                <a id="<?=$each->rankid;?>" class="btn btn-danger delbtn" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-trash"></i></a>
                            </td>
                            <td><?=$each->rankid;?></td>
                            <td><?=$each->description;?></td>
                        </tr>
                        <? endforeach; ?>
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>

<div id="delete-alert" class="hide">
    <div><h5>&nbsp;&nbsp;&nbsp;Are You sure you want to delete <span id="chosen-row" class="text-error"></span> ?</h5></div>
</div>
<div id="delete-alert-footer">
    <input type="hidden" class="hiddenid" />
    <a href="#" class="btn btn-danger" data-dismiss="modal">No</a>
    <a href="#" class="btn btn-success" id="del-submit">Yes</a>
</div>

<script>

$(document).ready(function(){
    var table = $('#civilTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader(table);
});


    $(".addbtn,.editbtn").click(function(){
        var infotype = "<?=$infotype;?>";
        var id = 0;
        if($(this).attr("id")) id = $(this).attr("id");
        
        $("#modal-view").find("h3[tag='title']").text(id>0 ? "Edit Rank" : "Add Rank");
        $("#button_save_modal").text("Save");  
        var form_data = {
            info_type: infotype,
            action: id
        };
        $.ajax({
            url: "<?=site_url('configuration_/viewForm')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });  
    });
    
    $(".delbtn").click(function(){
        var id = $(this).attr("id");
        var delalert = $('#delete-alert').clone();
        var delalert_footer = $('#delete-alert-footer').clone();
        delalert.find('#chosen-row').html(id);
        delalert.find('#del-submit').attr('tagkey',id);
        delalert.removeClass('hide');
        
        $("#modal-view").find("h3[tag='title']").text("Delete Civil Status");
        $("#modal-view").find("div[tag='display']").html( delalert );
        $("#modal-view").find(".modal-footer").html( delalert_footer );
    });
    
    $(document).on("click", '#del-submit', function(){
        var infotype = "<?=$infotype;?>";
        var id = $(this).attr('tagkey');
        $("#modal-view").find("div[tag='display']").html("<h3>Deleting...</h3>");
        $.ajax({
            url: "<?=site_url('configuration_/deleteRow')?>",
            type: "POST",
            data: {id:id, infotype:infotype},
            success: function(msg){
                console.log(msg);
                $("#modal-view").find("div[tag='display']").html(msg);
                $(".inner_navigation .main li .active a").click();
            }
        });
    });
    
</script>