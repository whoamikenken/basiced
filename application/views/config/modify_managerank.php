 
<?php

 /**
 * @author Max Consul
 * @copyright 2019
 */
?>
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
            <center><b><h3 tag="title" class="modal-title" id="tag" action="<?= $tag ?>"><?= $title ?></h3></b></center>
        </div>
        <div class="modal-body" style="width: 80%; margin-left: 10%;">
            <form id="frmsetup"><br>
                <div class="form-row">
                    <label class="field_name align_right">Type</label>
                    <div class="field">
                        <input type="hidden" name="tag" value="<?= $tag ?>">
                        <input type="hidden" name="id" value="<?= isset($id) ? $id : ""?>">
                        <select class="chosen" name="type">
                            <option>- Select Type -</option>
                            <?php foreach($type_config as $value): ?>
                                <option value="<?= $value['id'] ?>" <?= ($value['id'] == $type) ? "selected" : "" ?> ><?= $value['description'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label class="field_name align_right">Rank</label>
                    <div class="field">
                        <select class="chosen" name="rank">
                            <option>- Select Rank -</option>
                            <?php foreach($rank_config as $value): ?>
                                <option value="<?= $value['id'] ?>" <?= ($value['id'] == $rank) ? "selected" : "" ?> ><?= $value['description'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label class="field_name align_right">Set</label>
                    <div class="field">
                        <select class="chosen" name="set">
                            <option>- Select Set -</option>
                            <?php foreach($set_config as $value): ?>
                                <option value="<?= $value['id'] ?>" <?= ($value['id'] == $set) ? "selected" : "" ?> ><?= $value['description'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label class="field_name align_right">Basic Rate</label>
                    <div class="field">
                        <input class="form-control" id="basic_rate" name="basic_rate" type="text" value="<?= isset($basic_rate) ? $basic_rate : ""?>"/>
                    </div>
                </div>
            </form>
        </div>
        <div id="alert_message" style="height: 30px;width: 100%;margin:auto;font-size:15px;font-style:cursive;display: none;"></div><br>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="save">Save</button>
            
        </div>
    </div>

</div>

  <script>
    var toks = hex_sha512(" ");
    $("#save").click(function(){
        var form_data = GibberishAES.enc($("#frmsetup").serialize(), toks);
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/saveManageRank')?>",
            data: {form_data:form_data,toks:toks},
            success:function(response){
                if(response == "add"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Rank has been saved successfully',
                        showConfirmButton: true,
                        timer: 1000
                    })
                }else if(response == "edit"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Rank has been updated successfully',
                        showConfirmButton: true,
                        timer: 1000
                    })
                }
                else{
                    $("#alert_message").fadeIn().fadeIn("slow").fadeIn(3000).fadeOut(3000);
                    $("#alert_message").css({"background-color": "#d16f6a","color": "white"});
                    alert('Entry Failed');
                }
                managerank_setup();
                $('#myModal').modal('toggle');
            }
        });
    });

    $(".chosen").chosen();
  </script>