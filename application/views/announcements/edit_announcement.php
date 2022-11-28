    <?php
        $datetoday = date("Y-m-d");
        $timetoday = date("h:i A");
        $departments = $this->extras->showdepartment();

    $cdisable = false;
    $leavetype = "";
    $leavecredit = "";
    $daterange = "";
    $dept = "";
    $array = array();

    // echo "<pre>";
    // print_r($deptid);


     if($codes){
    
    //      $sql = $this->db->query("SELECT * FROM `announcement` a
    // LEFT JOIN announcement_dept b ON (a.`id` = b.`id`) WHERE a.id='{$code}'");
      $sql = $this->db->query("SELECT 
      id,
      datefrom,
      dateto,
      timefrom,
      timeto,
      `event`,
      venue,
      posted_until,
      `user`,
      date_created 
    FROM
      announcement 
    WHERE id='{$codes}'
    GROUP BY datefrom,
      dateto,
      timefrom,
      timeto,
      `event`,
      venue,
      posted_until,
      `user`,
      date_created ");
// echo "<pre>"; print_r($sql); die;
         if($sql->num_rows()>0){
            $dfrom = $sql->row(0)->datefrom;
            $dto = $sql->row(0)->dateto;
            $tfrom=$sql->row(0)->timefrom;
            $tto =$sql->row(0)->timeto;
            $announcements=$sql->row(0)->event;
            $venue=$sql->row(0)->venue;
            $dateposted=$sql->row(0)->posted_until;

         }
         $cdisable = true;



     }

     $array = explode(',',$dept);
     
    ?>

    <style type="text/css">
        .col-md-5{
            padding: 0px;
        }

    </style>
    <form id="formAnnouncements" autocomplete="off">
        <div class="modal-dialog modal-lg">
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
                    <center><b><h3 tag="title" class="modal-title">Edit Annoucement</h3></b></center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-row">
                            <label class="col-md-3 align_right"><b>Concerned&nbsp;Department</b></label>
                            <div class="col-md-8">
                                <select class="chosen" style="width: 500px"  id="department" name="department"  multiple>
                                    <?php foreach ($departments as $k => $v): ?>
                                    <option value="<?=$k?>" <?= (in_array($k, $deptid)) ? "selected" : "" ?> ><?= $v ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 align_right">&emsp;</div>
                            <div class="col-md-8">
                            &emsp;
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 align_right"></div>
                            <div class="col-md-8">
                                <input type="checkbox"  size="16" name="alldept" value="alldept">&emsp;<b>All Departments</b>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 align_right">&emsp;</div>
                            <div class="col-md-8">
                            &emsp;
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-md-3 align_right"><b>Date From</b></label>
                            <div class="col-md-8">
                                <div class="col-md-5">
                                    <div class='input-group date' id="datesetfrom" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="datesetfrom" id="dfrom" value="<?=$dfrom?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <label class="col-md-2" style="width: 14.066667%; margin-left: 10px;"><b>To</b></label>
                                <div class="col-md-5">
                                    <div class='input-group date' id="datesetto" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="datesetto" id="dto" value="<?=$dto?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                    <div class="input-append" id="dayscon"></div>
                                </div>
                            </div>           
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 align_right">&emsp;</div>
                            <div class="col-md-8">
                            &emsp;
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="field_name col-md-3 align_right">Time</label>
                            <div class="field col-md-8">
                                <div class="col-md-5">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=$tfrom?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <label class="col-md-2" style="width: 14.066667%; margin-left: 10px;"></label>
                                <div class="col-md-5">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control"  name="tto" id="tto" value="<?=$tto?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 align_right">&emsp;</div>
                            <div class="col-md-8">
                            &emsp;
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-md-3 align_right"><b>Announcements&nbsp;/&nbsp;Events</b></label>
                            <div class="col-md-8 no-search">
                                <textarea rows="4" style="resize: none;" class="form-control isreq" name="event" id="event" placeholder="" value=""><?=$announcements ?></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 align_right">&emsp;</div>
                            <div class="col-md-8">
                            &emsp;
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-md-3 align_right"><b>Venue</b></label>
                            <div class="col-md-8 no-search">
                                <input class="form-control isreq" type="text" name="venue" id="venue"  value="<?=$venue?>" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 align_right">&emsp;</div>
                            <div class="col-md-8">
                            &emsp;
                            </div>
                        </div>
                        <div class="form-row">
                            <label class="col-md-3 align_right"><b>Posted Until</b></label>
                            <div class="col-md-8">
                                <div class="col-md-5">
                                    <div class='input-group date' id="posted_until" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="posted_until" value="<?=$dateposted?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" id="update_announcement" class="btn btn-success">Save</button>  
                </div>
            </div>
        </div>
    </form>
<script>

    $('formAnnouncements').find('#department').css('width', $('#formAnnouncements').find('#event').css('width'));

    $("#update_announcement").unbind().click(function(){
        var formdata   =  '';
        $('#formAnnouncements input, #formAnnouncements select, #formAnnouncements textarea').each(function(){
          if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
          else formdata = $(this).attr('name')+'='+$(this).val();
       })
        formdata += "&ids="+"<?=$codes?>";
        if($("input[name=alldept]").is(':checked')){

        }else{
           formdata   += "&deptids="+$("#departments").val();
        }



        if ($("select[name='department']").val() == "" && $("input[name=alldept]").not(":checked") ) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Concerned Departments is required",
                showConfirmButton: true,
                timer: 2000
            });
        }
        else
        {
         $.ajax({
            url  : "<?=site_url("announcements_/saveAnnouncement")?>",
            type : "POST",
            dataType:"json",
            data : {formdata:GibberishAES.enc( formdata, toks), toks:toks},
            success : function(msg)
            {
               if (msg.err_log == 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    });
                    loadAnnouncementHistory();
                    $("#close").click();
               }
               else
               {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    });
                    loadAnnouncementHistory();
                     $("#close").click();
               }
                 $(".inner_navigation .main li .active a").click();
            } 
         });
        }
        
    });

    $("input[name='datesetfrom'],input[name='datesetto']").blur(function(){
      var d1 = new Date($("#formAnnouncements").find("input[name='datesetfrom']").val());
      var d2 = new Date($("#formAnnouncements").find("input[name='datesetto']").val());
      if(d1 > d2){
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: "Please fill-up a valid date.",
              showConfirmButton: true,
              timer: 2000
          })
          $(this).val("");
          return;
      }
    });

    $("#event, #venue").on('input',function(){
        $(this).css("border-color","#AAAAAA").attr("placeholder", "");
    });
    $("input[name='datesetfrom']").change(function(){
        $("#err").remove();
       var  start = new Date($(this).val()),
            end   = new Date($("#dto").val()),
            diff  = new Date(end - start),
            days  = diff/1000/60/60/24;
            if(days < 0)    $("#dayscon").append("<input type='text' style='color: red;border-color:#FFFFFF;' id='err' value='Invalid date range!.'>");
            else            $("#err").remove();
    });
    $("input[name='datesetto']").change(function(){
        $("#err").remove();
       var  end = new Date($(this).val()),
            start   = new Date($("#dfrom").val()),
            diff  = new Date(end - start),
            days  = diff/1000/60/60/24;
            if(days < 0)    $("#dayscon").append("<input type='text' style='color: red;border-color:#FFFFFF;' id='err' value='Invalid date range!.'>");
            else            $("#err").remove();
    });

    $("#datesetfrom,#datesetto,#posted_until").datetimepicker({
        format: "YYYY-MM-DD"
    });
    $("input[name='tfrom'],input[name='tto']").datetimepicker({
        format: 'LT'
    });

    $("input[name='alldept']").click(function()
    {
        if($(this).is(':checked')){

           $('#departments').prop('disabled', true).trigger("chosen:updated");
        
        }
        else{
           $('#departments').prop('disabled', false).trigger("chosen:updated");
            
        }
    });
    $(".chosen").chosen();
</script>