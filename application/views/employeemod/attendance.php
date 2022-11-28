<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
 $employeeid = $this->session->userdata('username');
 $tnt = $this->extensions->getEmployeeTeachingType($employeeid);

?>
<style type="text/css">
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b> </b></h4></div>
                   <div class="panel-body">
                        <input type="hidden" id="employeeid"  value="<?= $employeeid ?>">
                        <input type="hidden" id="tnt"  value="<?= $tnt ?>">
                        <div id="confirmation" class="well-content" style="padding-bottom: 12px;"></div>
                        
                        <!-- <div id="attendanceview" class="well-content" style="padding-bottom: 32px;" hidden="">
                            <a id="confirmbtn" class="btn blue pull-right" style="margin-right: 15px;background: #0066ff;color: white;">Confirm</a><br>
                        </div> -->
                    
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                  </div>
                </div>
                <div id="attendanceview" class="well-content" style="padding-bottom: 32px;" hidden="">
                            <a id="confirmbtn" class="btn blue pull-right" style="margin-right: 15px;background: #0066ff;color: white;">Confirm</a><br>
                        </div>
            </div>
        </div>        
    </div>        
</div>
<script>
  var toks = hex_sha512(" ");
$(document).ready(function(){
    loadconfirmation();        
});

/*
 *  FUNCTIONS
 */
 // leave
function loadconfirmation(){
   $("#confirmation").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("employeemod_/fileconfig")?>",
      type     :   "POST",
      data     :   {folder: GibberishAES.enc("employeemod"  , toks), view:  GibberishAES.enc("attendance_confirm" , toks), toks:toks},
      success  :   function(msg){
       $("#confirmation").html(msg);
      }
   });
}
 
function loadattcutoff(dfrom,dto){
   $("#attendanceview").prepend("<div id='loading'><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</div>");
   att_exist(dfrom,dto);
   $.ajax({
       url: "<?=site_url("process_/showallindividual")?>",
       type: "POST",
       data: {
          datesetfrom:  GibberishAES.enc(dfrom , toks), 
          datesetto:  GibberishAES.enc(dto , toks),
          fv :  GibberishAES.enc( $("#employeeid").val(), toks),
          edata :  GibberishAES.enc("NEW" , toks),
          toks:toks
       },
       success: function(msg) {
           $("#loading,#attcontent").remove();
           $("#attendanceview").show().prepend('<div id="attcontent">'+msg+'</div>');
       }
   }); 
}
    // confirm
$("#confirmbtn").click(function(){
    var cutoff  = $("#cutoff").val();
    var csure = confirm("Are you sure you want to confirm this attendance?");
    if(csure){
        var form_data = {
                            cutoff       :  GibberishAES.enc(cutoff , toks),
                            employeeid   :  GibberishAES.enc($("#employeeid").val() , toks),
                            tnt          :  GibberishAES.enc( $("#tnt").val(), toks),
                            recompute    :  GibberishAES.enc(true , toks),
                            toks:toks
                        }
                $.ajax({
                        url      :   "<?=site_url("process_/saveEmployeeAttendanceSummaryDept")?>",
                        type     :   "POST",
                        data     :   form_data,
                        dataType :   "JSON",
                        success  :   function(msg){

                            var data_failed = msg.data_failed;
                            var failed = '';
                            for (var key in data_failed) {
                                failed += data_failed[key] + ", ";
                            }
                            if(failed) failed = failed.substring(0, failed.length-2);
                            else failed = 'NONE';

                            if(msg.err_code == 0){

                            if(failed == 'NONE') $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'green','font-size':'15px','font-weight':'bold'});
                            else{
                                $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
                                $('#modal-view').find('.modal-body').append('<span style="color:red;">Data insert failed: '+failed+'</span>');
                            }                  
                            }else{
                                $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'red','font-size':'15px','font-weight':'bold'});
                            }

                            $('#modal-view').find('.modal-header, #button_save_modal').hide();
                            $('#modal-view').modal('show');
                            var cdate = $("#cutoff").val().split(",");
                            att_exist(cdate[0], cdate[1]);
                        }
                    });
    }
});

function att_exist(dfrom,dto){
    var form_data = {
                        model :  GibberishAES.enc("checkconfirmed_att", toks),
                        dfrom :  GibberishAES.enc(dfrom , toks), 
                        dto   :  GibberishAES.enc( dto, toks),
                        toks:toks
                    }
        $.ajax({
                url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
                type     :   "POST",
                data     :   form_data,
                success : function(msg){
                  // console.log(msg);
                    if(msg == 1){
                        $("#confirmbtn").hide();
                        $("#ctxt").remove();
                        $("#attendanceview").append('<div id="ctxt" class="pull-right" style="margin-right: 15px;font-size: 14px;">Status : <span style="color: red;font-size: 18px;"><b>Confirmed</b></span></div>');
                    }else{
                        $("#ctxt").remove();
                    }                        
                }
            });
}

$(".chosen").chosen();
</script>