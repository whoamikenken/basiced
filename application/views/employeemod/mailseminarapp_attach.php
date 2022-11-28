<?php

/**
 * @author Justin
 * @copyright 2016
 */
$id  = $this->input->post("idkey");
$user = $this->session->userdata("username");
if($id){
    $query = $this->employeemod->loadseminarapp_attachment($id);
    if($query->num_rows() > 0){
        foreach($query->result() as $row){
            $purpose    = $row->purpose;
            $course     = $row->course;
            $dfrom      = $row->dfrom;
            $dto        = $row->dto;
            $tstart     = date('h:i A',strtotime($row->tstart));;
            $tend       = date('h:i A',strtotime($row->tend));
            $days       = $row->nodays;
            $paid       = $row->paiddays;
            $cfee       = $row->coursefee;
            $meal       = $row->meal;
            $transpo    = $row->transportation;
            $hotel      = $row->hotel;
            $totalcost  = $row->totalcost;
            $venue      = $row->venue;
            $statement  = $row->statement;
            $speaker    = $row->speaker;
            $miscellaneous = $row->miscellaneous;
            $attach     = $row->points;
            $status     = $row->status;
            $dattach    = $row->dateattached;
            $ishr       = $row->hrdir;
        }
    }
}
if($job != "add")   $readonly = " readonly";
else                $readonly = "";
?>
<style>
.modal{
    width: 50%;
    left: 0;
    right: 0;
    margin: auto;
}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>ATTENDANCE TO PROFESSIONAL DEVELOPMENT PROGRAMS</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            
            <div class="form_row">
                <label class="field_name align_right"><strong>Title:</strong></label>
                <div class="field" style="word-wrap: break-word;">
                    <label class="field_name" style="width: 100%;"><?=$course?></label>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Date:</strong></label>
                <div class="field" style="word-wrap: break-word;">
                    <label class="field_name" style="width: 100%;"><?=date("F d, Y",strtotime($dfrom))." - ".date("F d, Y",strtotime($dto))?></label>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Time:</strong></label>
                <div class="field" style="word-wrap: break-word;">
                    <label class="field_name" style="width: 100%;"><?=date("h:i A",strtotime($tstart))." - ".date("h:i A",strtotime($tend))?></label>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Venue:</strong></label>
                <div class="field" style="word-wrap: break-word;">
                    <label class="field_name" style="width: 100%;"><?=$venue?></label>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Speakers:</strong></label>
                <div class="field" style="word-wrap: break-word;">
                    <label class="field_name" style="width: 100%;"><?=$speaker?></label>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Objective:</strong></label>
                <div class="field" style="word-wrap: break-word;">
                    <label class="field_name" style="width: 100%;"><?=$purpose?></label>
                </div>
            </div>
            <?if($job != "add"){?>
            <div class="form_row">
                <label class="field_name align_right"><strong>Key Learning Points:</strong></label>
                <div class="field" style="word-wrap: break-word;">
                    <label class="field_name" style="width: 100%;"><?=$attach?></label>
                </div>
            </div>            
            <?}else{?>
            <div class="form_row">
                <label class="field_name align_right"><strong>Key Learning Points</strong></label>
                <div class="field">
                    <textarea rows="3" style="width: 100%;resize: none;" name="points" id="points" placeholder="Key Learning Points" <?=$readonly?>><?=$attach?></textarea>
                </div>
            </div>
            <?}?>
            <?if($user == $ishr){?>
            <div class="form_row">
                <label class="field_name align_right"><strong>Status</strong></label>
                <div class="field no-search">
                    <select class="form-control" name="status" id="status" <?= (in_array($status,array("APPROVED","DISAPPROVED")) ? " disabled" : "")?>>
                        <?
                            $opt_status = $this->extras->showLeaveStatus();
                            foreach($opt_status as $c=>$val){
                            if($c != "PENDING"){
                        ?><option<?=($c==$status ? " selected" : "")?> value="<?=$c?>" ><?=$val?></option><?    
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?}?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
            <button type="button" id="saveatt" class="btn btn-danger">Save</button>
        <button type="button" id="closeatt" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>
<script>
if("<?=$job?>" != "add" && "<?=$user?>" != "<?=$ishr?>"){
    $("#saveatt").hide();
}
$("#saveatt").click(function(){
    $.ajax({
        url:"<?=site_url("employeemod_/loadmodelfunc")?>",
        type:"POST",
        data:{
            job: "<?=$job?>",
            id: "<?=$id?>",
            points: $("#points").val(),
            status: $("#status").val(),
            model: "seminar_attach"
        },
        success: function(msg){
            $("#closeatt").click();
            alert(msg);
            location.reload();
        }
    });
});

$('.chosen').chosen();
</script>