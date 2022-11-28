<?php

 /**
 * @author Max Consul
 * @copyright 2019
 */

?>

<style>
  .cbox{
     -ms-transform: scale(1.5); /* IE */
     -moz-transform: scale(1.5); /* FF */
     -webkit-transform: scale(1.5); /* Safari and Chrome */
     -o-transform: scale(1.5); /* Opera */
  }
</style>

<div class="modal-dialog modal-md">
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
            <center><b><h3 tag="title" class="modal-title">Applicant Approval Status</h3></b></center>
        </div>
        <div class="modal-body"><br>
            <form id="appstat_form">
                <input type="hidden" name="action" value="<?= $id ? "edit" : "add" ?>">
                <?php
                    if($id == ''){
                        $id = $this->db->query("SELECT MAX(id) as maxid FROM code_applicant_status")->row();
                        $id = $id->maxid + 1;
                    } 
                ?>
                <input type="hidden" name="rowid" value="<?= $id ?>" >
                <div class="row">
                    <label class="col-md-2 align_left">Type: </label>
                    <div class="col-md-6 align_left" style="padding:0px;">
                        Teaching: &nbsp;&nbsp;<input type="checkbox" value="teaching" name="type" class="cbox type" <?= ($type == "teaching")  ? "checked" :  "" ?> >&nbsp;&nbsp;
                        Non Teaching: &nbsp;&nbsp;<input type="checkbox" value="nonteaching" name="type" class="cbox type" <?= ($type == "nonteaching")  ? "checked" :  "" ?>>
                    </div>
                </div><br>
                <div class="row">
                    <label class="col-md-4 align_left">Sequence Number</label>
                    <div class="col-md-6 align_left" style="width: 20%;padding:0px;">
                        <input class="form-control" id="seqno" name="seqno" type="number" value="<?= $seqno ?>" >
                    </div>
                </div><br>
                <div class="row">
                    <label class="col-md-2 align_left">Description</label>
                    <div class="col-md-10 align_left">
                        <input class="form-control" id="description" name="description" type="text" value="<?= $description ?>" >
                    </div>
                </div><br>
                <div class="row">
                    <label class="col-md-2 align_left">Message</label>
                    <div class="col-md-10 align_left">
                        <textarea id="message" name="message" rows="10" style="width: 100%;" value="<?= $message ?>"><?= $message ?></textarea>
                    </div>
                </div><br>
                <div class="row category_opt">
                    <label class="col-md-2 align_left">Category</label>
                    <div class="col-md-10 align_left">
                        <!-- <input class="form-control" id="category" name="category" type="text" style="width: 80%;display: inline;"> -->
                        <select class="form-control" id="category" name="category" style="width: 80%;display: inline;">
                            <option value="">SELECT CATEGORY</option>
                            <option value="REMARKS">REMARKS</option>
                            <option value="FILE">FILE</option>
                            <option value="DATE">DATE</option>
                            <option value="TIME">TIME</option>
                        </select>
                        <a class="btn btn-info" id="add_categ" style="border-radius:20px;display: inline;">
                            <span class="glyphicon glyphicon-plus"></span> 
                        </a>
                    </div>
                </div><br>

                <div class="row" id="inireq">
                    <label class="col-md-2 align_left">&nbsp;</label>
                    <div class="col-md-10 align_left">
                        <input type="checkbox" value="1" checking="isrequirements" class="cbox isrequirements" name="req" <?= ($isrequirements)  ? "checked" :  "" ?> > &emsp;Check this to display Initial Requirements.
                    </div>
                </div><br>
                <div class="row" id="prereq">
                    <label class="col-md-2 align_left">&nbsp;</label>
                    <div class="col-md-10 align_left">
                        <input type="checkbox" value="1" checking="isprerequirements" class="cbox isprerequirements" name="req" <?= ($isprerequirements)  ? "checked" :  "" ?> > &emsp;Check this to display Pre Employment Requirements.
                    </div>
                </div><br>

                 <div class="row" id="laststep">
                    <label class="col-md-2 align_left">&nbsp;</label>
                    <div class="col-md-10 align_left">
                        <input type="checkbox" value="1" checking="islaststep" class="cbox islaststep" name="islaststep" <?= ($islaststep)  ? "checked" :  "" ?> >&emsp;Check this if it is the last step of hiring process.
                    </div>
                </div><br>
                <div class="row category_opt">
                    <div class="col-md-12">
                        <div id="msg_header" style="display: none;">
                            <strong></strong><span></span>
                        </div>
                        <table class="table table-hover table-bordered" id="categ_table">
                            <thead style="background-color: #0072c6">
                                <tr>
                                    <td><b>Description</b></td>
                                    <td><b>Action</b></td>
                                </tr>
                            </thead>
                            <tbody id="categ_tbody">
                                <?php if($categ_desc): ?>
                                    <?php foreach($categ_desc as $value): 
                                        ?>
                                        <tr>
                                            <td class="align_center categ_desc"><?= $value['description'] ?></td>
                                            <td class='align_center'>
                                                <!-- <a type="button" class='btn btn-info editbtn-status' ><i class='glyphicon glyphicon-edit'></i></a>&nbsp; -->
                                                <a class='btn btn-danger delbtn-status' categ="<?=$value['id']?>" ><i class='glyphicon glyphicon-trash'></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row approver_opt">
                    <label class="col-md-2 align_left">Approver&nbsp;List</label>
                    <div class="col-md-10 align_left">
                        <select class="form-control chosen" id="approver_list" name="approver_list" multiple style="display: inline;">
                            <?=$this->employee->loadallofficeheadempid($approver_list)?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <label class="col-md-3 align_left">Send to email?</label>
                    <div class="col-md-5 align_left">
                        <select class="form-control" name="foremail" id="foremail">
                            <option value=""> - Select a option - </option>
                            <option value="1" <?= $foremail ? "selected" : "" ?> >YES</option>
                            <option value="0" <?= ($foremail == 0) ? "selected" : "" ?> >NO</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            <button type="button" class="btn btn-success save_app_stat">Save</button>
        </div>
    </div>
</div>

<script>
    var toks = hex_sha512(" ");
    var msg = "Category has been saved successfully.";
    $("input[name='type']").click(function(){
        $("input[name='type']").removeAttr('checked');
        this.checked = true;
        checkRequirements($(this).val());
    });

    checkCategory();

    $("input[type='checkbox']").click(function(){
        checkCategory();
    })

    function checkCategory(){
        var checked = 0
        $("input[type='checkbox']").each(function(){
            if($(this).is(':checked') && $(this).attr('name') != 'type') checked++;
        })

        if(checked > 0) $(".category_opt").hide();
        else $(".category_opt").show();
    }

    checkRequirements("<?=$type?>");

    function checkRequirements(tnt=''){
        var id = "<?=$id?>";
        if($("input[name='action']").val() == 'add') id = "";
        if(tnt){
            $.ajax({
                url: "<?= site_url('applicant/checkRequirements') ?>",
                type: "POST",
                dataType: "json",
                data: {tnt:  GibberishAES.enc(tnt, toks), id:  GibberishAES.enc(id, toks), toks:toks},
                success:function(response){
                    if(response.inireq > 0 && response.isrequirements == 0 && response.isrequirements == '') $("#inireq").hide();
                    else $("#inireq").show();

                    if(response.prereq > 0 && response.isprerequirements == 0  && response.isprerequirements == 'null') $("#prereq").hide();
                    else $("#prereq").show();

                    if(response.laststep > 0 && response.islaststep == 0  && response.islaststep == '') $("#laststep").hide();
                    else $("#laststep").show();
                }
            });
        }
    }

    $("input[name='req']").click(function(){
        if($(this).is(':checked')){
            var checking = $(this).attr("checking");
            $("input[name='req']").each(function(){
                if(checking != $(this).attr("checking")) $(this).removeAttr('checked');
            })
        }
            
        // $("input[name='req']").removeAttr('checked');
        // this.checked = true;
    });

    $("#add_categ").click(function(){
        var category = $("#category").val();
        if(!category){
            Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: "Select category first.",
                  showConfirmButton: true,
                  timer: 1000
              })
            return;
        }else{
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: msg,
              showConfirmButton: true,
              timer: 1000
          })
        }
        msg = "Category has been saved successfully.";
        var markup = "<tr><td class='align_center categ_desc'>" + category + "</td><td class='align_center'><a class='btn btn-danger del_row'><i class='glyphicon glyphicon-trash'></i></a></td></tr>";
        $("#categ_table #categ_tbody").append(markup);
        $("#category").val("");
    });

    $("#categ_table #categ_tbody").delegate(".del_row", "click", function() {
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
            $(this).closest("tr").remove(); 
            Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'Description has been deleted successfully.',
                  showConfirmButton: true,
                  timer: 1000
              })
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

    $("#categ_table #categ_tbody").delegate(".editbtn-status", "click", function() {
        var trid = $(this).closest("tr");
        msg = "Description has been updated successfully.";
        $(trid).find(".savebtn-status").show();
        var current_desc = $(trid).find(".categ_desc").text();
        $(trid).find(".categ_desc").html("<input class='form-control replace_categdesc' type='text' value='"+current_desc+"'>");
    });

    $("#categ_table #categ_tbody").delegate(".delbtn-status", "click", function() {
        var trid = $(this).closest("tr").remove();
    });

    $("#categ_table #categ_tbody").delegate(".replace_categdesc", "change", function() {
        var update_desc = $(this).val();
        var trid = $(this).closest("tr");
        $(trid).find(".categ_desc").text(update_desc);
    });

    $('.chosen').chosen();

</script>