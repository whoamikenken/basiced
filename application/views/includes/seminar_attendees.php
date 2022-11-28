<?php 
$seminarInfo = $this->db->query("SELECT * FROM inhouse_seminar GROUP by title")->result_array();
 ?>
<style>
    #announce_part{
        float:left;
        background-color:#337ab7;
        width:30%;
    }
    #details_part{
        float:left;
        width:70%;
    }
    .modal-body{
        padding: 0px !important;
        margin-bottom: 0px !important;
    }

    .announce_text{
        font-size: 300%;
        line-height: 252%;
        color:white;
        font-weight: bold;
        font-family: 'Poppins', sans-serif;
        text-align: center;
    }

</style>

<input type="hidden" name="category" value="<?=$category?>">
<input type="hidden" name="id" value="<?=$id?>">
<div class="modal-dialog">

     <div class="modal-content" style="width: 150%;height: auto;">
       
        <div class="modal-body">
            <div style="display: flex;">
                <div id="announce_part">
                    <div style="margin-top: 10%;">
                        <table>
                            <tr><td class="announce_text"><?=$month?></td></tr>
                            <tr><td class="announce_text"><?=$day?></td></tr>
                            <tr><td class="announce_text"><?=$year?></td></tr>
                        </table>
                    </div>
                </div>
                <div id="details_part">
                <div class="media" style="margin-left: 5%; margin-top: 3%;">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:70px">
                    </div>
                    <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                        <h5 class="media-heading" style="font-size: 20px" ><b>Pinnacle Technologies Inc.</b></h5>
                        <p style="font-family:Avenir; margin-top: -1%; font-size: 17px;">D`Great</p>
                    </div>
                </div>
                    <hr>
                    <center><h5 style="font-size: 120%;font-weight: bold;margin-left: 5%;">Invitation for Upcoming Seminar</h5></center>
                    <table cellpadding="10">
                        
                        <tr>
                            <td style="font-weight: bold; margin-left: 5%">Seminar Category </td>
                            <td style="font-weight: 600;font-size:90%;"> &nbsp; : &nbsp;<?=$seminarList[$category]?></td>
                        </tr>
                        <tr style="border-top: 1px solid;">
                            <td style="font-weight: bold;">Seminar Title </td>
                            <td style="font-weight: 600;font-size:90%"> &nbsp; : &nbsp;<?= $this->extensions->getSeminarTitle($workshop); ?></td>
                        </tr>
                        <tr style="border-top: 1px solid;">
                            <td style="font-weight: bold;">Time </td>
                            <td style="font-weight: 600;font-size:90%"> &nbsp; : &nbsp;<?=$time_from?> to <?=$time_to?></td>
                        </tr>
                        <tr style="border-top: 1px solid;">
                            <td style="font-weight: bold;">Venue </td>
                            <td style="font-weight: 600;font-size:90%"> &nbsp; : &nbsp;<?=$venue?></td>
                        </tr>

                    </table>

                    <div style="text-align: right;margin-top: 5%;margin-bottom: 5%;margin-right: 5%;">
                        <button type="button" class="btn btn-success" id="going">Going</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn btn-default" id="notgoing" style="background-color:red;color:white;">Not Available</button>
                        <div class="form_row" id="reason_div" style="margin-top:10%;display: none">
                            <label class="field_name">Reason:</label>
                            <div class="field">
                                <input type="text" class="form-control" name="user_reason">
                            </div>
                            <div style="float: right;margin-top: 5%;">
                                <button class="btn btn-primary" id="save_reason"> Enter </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>

    $("#notgoing").click(function(){
        $("#reason_div").fadeIn();
    });

    $("#save_reason").click(function(){
        var id = $("input[name='id']").val();
        var reason = $("input[name='user_reason']").val();
        var category = $("input[name='category']").val();
        validateSeminarPoll(0, id, reason,category) 
    });

    $("#going").click(function(){
        $("input[name='user_reason']").val("");
        $("#reason_div").fadeOut();
        var id = $("input[name='id']").val();
        var reason = $("input[name='user_reason']").val();
        var category = $("input[name='category']").val();
        validateSeminarPoll(1, id, reason, category)
    });

    function validateSeminarPoll(isgoing, id, reason, category){
        $.ajax({
            url : "<?=site_url('seminar_/validateSeminarPoll')?>",
            type : "POST",
            data: {isgoing:isgoing, base_id:id, reason:reason, category:category},
            success:function(response){
                $("#seminar_attendees").modal("toggle");
                setTimeout(function(){ seminarAttendees(); }, 500);
            }
        });
    }

</script>