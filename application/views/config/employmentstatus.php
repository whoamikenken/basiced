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
    $infotype = 'status';
    $pinfo = $empModel->getPersonnelInfoConfigList($infotype);
    
?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>

<div id="content">

    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
               <div class="panel  animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employment Status</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="employmentStatTable">
                            <thead>
                                <tr>
                                    <th>
                                        <a class="btn btn-primary addbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
                                    </th>
                                </tr>                            
                                <tr style="background-color: #0072c6;">
                                    <th width='10%' align="center"><b>Actions</b></th>
                                    <th><b>Sequence No.</b></th>
                                    <th><b>Code</b></th>
                                    <th><b>Description</b></th>
                                    <th><b>Duration</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach( $pinfo as $each ): ?>
                                <tr>
                                    <td class="align_center">
                                        <a id="<?=$each->code;?>" class="btn btn-info editbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp&nbsp<a id="<?=$each->code;?>" class="btn btn-danger delbtn" data-toggle="modal"><i class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                    <td><?=$each->seqno;?></td>
                                    <td><?=$each->code;?></td>
                                    <td><?=$each->description;?></td>
                                    <td><?=($each->duration ? ($each->duration==1 ? $each->duration." Month" : $each->duration." Months") : "");?></td>                            
                                </tr>
                                <? endforeach; ?>
                            </tbody>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div id="delete-alert" class="hide">
    <div><h5>&nbsp;&nbsp;Are You sure you want to delete <span id="chosen-row" class="text-error"></span> ?</h5></div>
</div>
<div id="delete-alert-footer">
    <input type="hidden" class="hiddenid" />
    <a href="#" class="btn btn-danger del-close" data-dismiss="modal">No</a>
    <a href="#" class="btn btn-success" id="del-submit">Yes</a>
</div>

<script>
    var toks = hex_sha512(" ");
    $(document).ready(function(){
    var table = $('#employmentStatTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
    
    $(".addbtn,.editbtn").click(function(){
        var infotype = "<?=$infotype;?>";
        var id = 0;
        if($(this).attr("id")) id = $(this).attr("id");
        
        $("#modal-view").find("h3[tag='title']").text(id>0 ? "Edit Employment Status" : "Add Employment Status");
        $("#button_save_modal").text("Save");
        var form_data = {
            info_type:  GibberishAES.enc(infotype, toks), 
            action:  GibberishAES.enc(id, toks),
            toks:toks
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
        var infotype = "<?=$infotype;?>";
        const swalWithBootstrapButtons = Swal.mixin({
         customClass: {
           confirmButton: 'btn btn-success',
           cancelButton: 'btn btn-danger'
         },
         buttonsStyling: false
       });

       swalWithBootstrapButtons.fire({
         title: 'Are you sure?',
         text: "You won't be able to revert this!",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: 'Yes, proceed!',
         cancelButtonText: 'No, cancel!',
         reverseButtons: true
       }).then((result) => {
         if (result.value) {
            $.ajax({
                url: "<?=site_url('configuration_/deleteRow')?>",
                type: "POST",
                data: {id:GibberishAES.enc(id, toks), infotype:GibberishAES.enc(infotype, toks), toks:toks},
                success: function(msg){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Employment Status has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 2000
                    });

                    setTimeout(function(){ location.reload(); }, 2000);
                }
            });
         } else if (
           result.dismiss === Swal.DismissReason.cancel
         ) {
           swalWithBootstrapButtons.fire(
             'Cancelled',
             'Data is safe.',
             'error'
           )
         }
       });
    });
    
</script>