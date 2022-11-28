<?php

 /**
 * @author Max Consul
 * @copyright 2018
 */

?>

<!-- <div id="content"> -->

    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                   <div class="panel-heading"><h4><b>Document Submission</b></h4></div>
                   <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="DocumentSubmission">
                            <thead>
                                <tr>
                                    <th>
                                        <a class="btn btn-primary addbtn-approval-document"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
                                    </th>
                                </tr>                            
                                <tr>
                                    <th class='align_center' width='10%'><b>Actions</b></th>
                                    <th class='align_center'><b>Code</b></th>
                                    <th class='align_center'><b>Description</b></th>
                                    <th class='align_center'><b>Required</b></th>
                                </tr>
                            </thead>

                            <tbody> 
                            <?php if($data){ ?>
                                  <?php foreach($data as $value): ?>
                                    <tr>
                                       <td class="align_center">
                                            <a id="<?=$value['code'] ?>" class="btn btn-info editbtn-document"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                                            <a id="<?=$value['code'] ?>" class="btn btn-danger delbtn-document"><i class="glyphicon glyphicon-trash"></i></a>
                                        </td>
                                        <td class='align_center'><?= $value['code']?></td>
                                        <td class='align_center'><?= $value['description']?></td>
                                        <td class='align_center'><?= $value['isRequired']?></td>
                                    </tr>
                                  <?php endforeach ?>
                            <?php } ?>
                            </tbody>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<div id="datamodal_docs" class="modal fade" role="dialog"></div>

<div id="deletemodal_doc" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                        <p>D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">Delete Document Setup</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <h5>&nbsp;&nbsp;&nbsp;Are You sure you want to delete <strong id="document_code"></strong> from Document Setup?</h5>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger del-close" data-dismiss="modal">No</button>
                <button type="button" id="delete_docs" class="btn btn-success" data-dismiss="modal">Yes</button>
            </div>
        </div>
        
    </div>
</div>

<script>
$(document).ready(function(){
    var table = $('#DocumentSubmission').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

    $(".addbtn-approval-document").click(function(){
        $.ajax({
            type: "POST",
            url:"<?= site_url('applicant/loadApplicantDocumentDetail') ?>",
            data: {tag:"add", title: "Add"},
            success:function(response){
                $("#datamodal_docs").html(response);
                $("#datamodal_docs").modal('toggle');
            }
        });
    });

    $(".editbtn-document").click(function(){
        var code = $(this).attr('id');
        if(code){
            $.ajax({
                type:"POST",
                url: "<?= site_url('applicant/manageApplicantDocument') ?>",
                data: {code:code, tag:"edit"},
                success:function(response){
                    $("#datamodal_docs").html(response);
                    $("#datamodal_docs").modal('toggle');
                }
            });
        }
    });

    $(".delbtn-document").click(function(){
        var code = $(this).attr("id");
        $("#deletemodal_doc").find("#document_code").text(code);
        $("#deletemodal_doc").find("#delete_docs").attr("code", code);
        $("#deletemodal_doc").modal("toggle");
    });

    $("#delete_docs").click(function(){
        $.ajax({
            url: "<?= site_url('applicant/deleteApplicantDocs') ?>",
            type: "POST",
            data: {code: $(this).attr("code")},
            success:function(response){
                loadApplicantStatus();
                if(response) alert("Successfully deleted approval status.");
                else alert("Failed to delete approval status.");
                location.reload();
            }
        });
    });

</script>