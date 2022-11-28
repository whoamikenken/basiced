<?php

 /**
 * @author Max Consul
 * @copyright 2018
 */
 
?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}

.swal2-cancel{
    margin-right: 20px;
}

</style>
<div id="content">
	<div class="widgets_area">
	    <div class="row">
	        <div class="col-md-12">
	            <div class="panel">
	               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Applicant Status</b></h4></div>
	               <div class="panel-body" id="app_stat_body">
	               </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>

<div id="datamodal_stat" class="modal fade" role="dialog" data-backdrop="static"></div>

<div id="deletemodal_stat" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">Delete Document Setup</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row align_center">
                    <h5>Are You sure you want to delete this row from Applicant Status Setup?</h5>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete_status" tag="" class="btn btn-danger" data-dismiss="modal">Yes</button>
                <button type="button" class="btn btn-success del-close" data-dismiss="modal">No</button>
                
            </div>
        </div>

    </div>
</div>

<script>
    var toks = hex_sha512(" ");
	$(document).ready(function(){
		loadApplicantStatus();
	});

	function loadApplicantStatus(){
		$.ajax({
			url: "<?= site_url('utils_/loadApplicantStatus') ?>",
			success:function(response){
				$("#app_stat_body").html(response);
			}
		});
	}

    $("#datamodal_stat").delegate(".save_app_stat", "click", function(event ){
        var categ_desc = "";
        var action = $("input[name='action']").val();
        var rowid = $("input[name='rowid']").val();
        var type = $(".type:checkbox").filter(":checked").val();
        var isrequirements = "";
        var isprerequirements = "";
        var islaststep = "";
        if($('.isprerequirements').is(':checked')) isprerequirements = 1;
        else isprerequirements = 0;

        if($('.isrequirements').is(':checked')) isrequirements = 1;
        else isrequirements = 0;

        if($('.islaststep').is(':checked')) islaststep = 1;
        else islaststep = 0;
        var seqno = $("#seqno").val();
        var description = $("#description").val();
        var message = $("#message").val();
        var seqId = 0;
        $.ajax({
            url: "<?= site_url("applicant/deleteApplicantCategory") ?>",
            type: "POST",
            data: {base_id:  GibberishAES.enc(rowid , toks), toks:toks},
            complete:function(response){
                    $("#categ_table #categ_tbody tr").each(function(){
                    seqId++;
                    categ_desc += "/" + $(this).find(".categ_desc").text();
                    var formdata = {
                        base_id: GibberishAES.enc(rowid , toks),
                        idseq:  GibberishAES.enc(seqId , toks),
                        description: GibberishAES.enc($(this).find(".categ_desc").text()  , toks),
                        toks:toks
                    };
                    $.ajax({
                        url: "<?= site_url("applicant/validateApplicantCategory") ?>",
                        type: "POST",
                        data: formdata,
                        success:function(response){
                            
                        }
                    });
                });
            }
        });

        

        categ_desc = categ_desc.substr(1);
        var foremail = $("#foremail").val();

        var form_data = {
            id:  GibberishAES.enc(rowid , toks),
            type:  GibberishAES.enc(type , toks),
            seqno:  GibberishAES.enc(seqno , toks),
            description:  GibberishAES.enc(description , toks),
            message:  GibberishAES.enc(message , toks),
            foremail:  GibberishAES.enc(foremail , toks),
            isrequirements:  GibberishAES.enc(isrequirements , toks),
            isprerequirements:  GibberishAES.enc(isprerequirements , toks),
            islaststep:  GibberishAES.enc(islaststep , toks),
            action:  GibberishAES.enc(action , toks),
            approver_list:  $("select[name='approver_list']").val(),
            toks:toks
        };

        var isexist = validateSequenceNo(type, seqno, rowid);
        if(isexist > 0){
            Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: "Sequence on "+type+" is already exist",
                  showConfirmButton: true,
                  timer: 1000
            })
            return;
        }

        $.ajax({
            url: "<?= site_url("applicant/validateApplicantApprovalStatus") ?>",
            type: "POST",
            data: form_data,
            success:function(response){
                if(response){
                    if(!rowid){
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: "Approval Status has been saved successfully.",
                          showConfirmButton: true,
                          timer: 1000
                      })
                    } 
                    else{
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: "Approval Status has been updated successfully.",
                          showConfirmButton: true,
                          timer: 1000
                      })
                    } 
                    loadApplicantStatus();
                    $("#datamodal_stat").modal("toggle");
                    event.stopPropagation();
                }
                else{
                    Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: "Failed to add approval status. Check all fields and duplicate code.",
                          showConfirmButton: true,
                          timer: 1000
                    })
                    return;
                }
            }
        });
    });

    function validateSequenceNo(type, seqno, rowid){
        var isexist = true;
        $.ajax({
            url: "<?= site_url('applicant/checkIfSequenceExist') ?>",
            type: "POST",
            data: {type:  GibberishAES.enc(type , toks), seqno:  GibberishAES.enc(seqno , toks), rowid:  GibberishAES.enc(rowid , toks), toks:toks},
            async:false,
            success:function(response){
                isexist = response;
            }
        });
        
        return isexist;
    }
	
</script>