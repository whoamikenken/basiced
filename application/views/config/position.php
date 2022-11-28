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
    $infotype = 'position';
    $pinfo = $empModel->getPersonnelInfoConfigList($infotype);
    $types = $empModel->getPersonnelInfoConfigList('position_type');
    $arr_types = array();
    if(sizeof($types)>0){
        foreach ($types as $row) {
            $arr_types[$row->id] = $row->description;
        }
    }
    
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
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Position</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="positionTable">
                            <thead>
                                <tr>
                                    <th>
                                        <a class="btn btn-primary addbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
                                    </th>
                                </tr>                            
                                <tr style="background-color: #0072c6;">
                                    <th width='10%' align="center" style="text-align: center;"><b>Actions</b></th>
                                    <th style="text-align: center;"><b>Code</b></th>
                                    <th style="text-align: center;"><b>Description</b></th>
                                    <th style="text-align: center;"><b>Teaching Type</b></th>
                                    <!-- <th><b>Employment Status</b></th> -->
                                    <th style="text-align: center;"><b>Hiring</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach( $pinfo as $each ): ?>
                                <tr>
                                    <td class="align_center">
                                        <a id="<?=$each->positionid;?>" class="btn btn-info editbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp&nbsp<a id="<?=$each->positionid;?>" desc="<?=$each->description;?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                    <td><?=$each->positionid;?></td>
                                    <td><?=$each->description;?></td>
                                    <td><?php echo ($each->isteaching == 'YES' ? 'Teaching' : 'Non-teaching')?></td>
                                    <!-- <td><?= isset($arr_types[$each->type]) ? $arr_types[$each->type] : '';  ?></td> -->
                                    <td><?= ($each->hiring == "YES")? "YES":"NO";?></td>
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

<!-- <div id="delete-alert" class="hide">
    <div><h5>&nbsp;&nbsp;Are You sure you want to delete <span id="chosen-row" class="text-error"></span> ?</h5></div>
</div>
<div style="display: none;">
    <div id="delete-alert-footer">
        <input type="hidden" class="hiddenid" />
        <a href="#" class="btn btn-danger del-close" data-dismiss="modal">No</a>
        <a href="#" class="btn btn-success" id="del-submit">Yes</a>
    </div>
</div> -->
<div id="deletemodal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                        <h5>D`Great</h5>
                    </div>
                </div>
                <center><h4 class="modal-title">Delete Position Setup</h4></center>
            </div>
            <div class="modal-body">
                <h5 class="align_center" id="deleteDisplay">Are you sure you want to Remove <span id="positionDesc"></span> from Position Setup?</h5>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete" class="btn btn-success" data-dismiss="modal">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
        
    </div>
</div>
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    var table = $('#positionTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

    $(".addbtn,.editbtn").click(function(){
        var infotype = "<?=$infotype;?>";
        var id = 0;
        if($(this).attr("id")) id = $(this).attr("id");
        
        $("#modal-view").find("h3[tag='title']").text(id>0 ? "Edit Position Setup" : "Add Position Setup");
        // $("#button_save_modal").text("Save");
        var form_data = {
            info_type:  GibberishAES.enc(infotype, toks),
            action:  GibberishAES.enc(id , toks),
            toks:toks
        };
        $.ajax({
            url: "<?=site_url('configuration_/viewFormPosition')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });  
    });
    
    // $(".delbtn").click(function(){
    //     var id = $(this).attr("id");
    //     var delalert = $('#delete-alert').clone();
    //     var delalert_footer = $('#delete-alert-footer').clone();
    //     delalert.find('#chosen-row').html(id);
    //     delalert.find('#del-submit').attr('tagkey',id);
    //     delalert.removeClass('hide');
        
    //     $("#modal-view").find("h3[tag='title']").text("Delete Position");
    //     $("#modal-view").find("div[tag='display']").html( delalert );
    //     $("#modal-view").find(".modal-footer").html( delalert_footer );
    // });

    $("#positionTable").on("click", ".delbtn", function(){
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

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
            var infotype = "<?=$infotype;?>";
            var id = $(this).attr('id');
            var form_data = {
                infotype:  GibberishAES.enc(infotype, toks),
                id:  GibberishAES.enc(id , toks),
                toks:toks
            };
            $.ajax({
                url: "<?=site_url('configuration_/deleteRow')?>",
                type: "POST",
                data: form_data,
                success: function(msg){
                    $("#deleteDisplay").html(msg);
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Position has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    setTimeout(function() {
                        location.reload();
                    }, 1500); 
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
        })
    });

    
    // $(document).on("click", '#del-submit', function(){
    //     var infotype = "<?=$infotype;?>";
    //     var id = $(this).attr('tagkey');
    //     $("#modal-view").find("div[tag='display']").html("<h3>Deleting...</h3>");
    //     $.ajax({
    //         url: "<?=site_url('configuration_/deleteRow')?>",
    //         type: "POST",
    //         data: {id:id, infotype:infotype},
    //         success: function(msg){
    //             console.log(msg);
    //             $("#modal-view").find("div[tag='display']").html(msg);
    //             $(".inner_navigation .main li .active a").click();
    //         }
    //     });
    // });
    
    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");
    
</script>