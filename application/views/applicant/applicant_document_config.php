<?php

 /**
 * @author Max Consul
 * @copyright 2018
 */

?>

<div class="modal-dialog modal-md">
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
            <center><b><h3 tag="title" class="modal-title">Document Submission</h3></b></center>
        </div>
        <div class="modal-body"><br>
            <form id="appdocs_form">
                <input type="hidden" name="action" value="<?= $code ? "edit" : "add" ?>">
                <div class="form_row">
                    <label class="field_name align_right">Code</label>
                    <div class="field">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
                            <input class="form-control" id="code" name="code" type="text" value="<?= $code ?>" <?= $code ? "readonly" : "" ?> >
                        </div>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">Description</label>
                    <div class="field">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
                            <input class="form-control" id="description" name="description" type="text" value="<?= $description ?>" >
                        </div>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">For Email</label>
                    <div class="field">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
                            <select class="form-control" name="isRequired"  >
                                <option value=""> - Select a option - </option>
                                <option value="1" <?= $isRequired ? "selected" : "" ?> > Yes </option>
                                <option value="0"> No </option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            <button type="button" class="btn btn-success" id='button_save_modal'>Save</button>
        </div>
    </div>
</div>

<script>
    $("#button_save_modal").click(function(){
        var error = 0;
        $("#appdocs_form input").each(function(){
            console.log($(this).val());
            if(!$(this).val()) error+=1;
        });
        if(error){
            alert("All fields are required!");
            return;
        }
        var formdata = $("#appdocs_form").serialize();
        $.ajax({
            url: "<?= site_url("applicant/validateDocumentSubmission") ?>",
            type: "POST",
            data: formdata,
            success:function(response){
                if(response){
                    alert("Successfully added new approval status.");
                    loadApplicantStatus();
                    $("#datamodal_docs").modal("toggle");
                    // location.reload();
                }
                else{
                    alert("Failed to add approval status. Check all fields and duplicate code.");
                    return;
                }
            }
        });

    });
</script>