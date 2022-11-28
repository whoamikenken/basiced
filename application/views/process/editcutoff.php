<?php
/**
 * @author Justin
 * @copyright 2015
 */
$psched = $pquater = $pfrom = $pto = $tfrom = $tto = $nodtr = "";
$quarter  = $schedule = "";
// $sdate    = $edate    = date('Y-m-d');
$ishidden = " hidden=''";
$cdate = date('Y-m-d');
// echo $dkey;
$dkey = isset($toks) ? $this->gibberish->decrypt($dkey, $toks) : $dkey;
$data = $this->extras->editCutoff($dkey);
foreach($data as $row){
    // var_dump($row);
    $cutofffrom  =   $row->CutoffFrom;
    $cutoffto    =   $row->CutoffTo;
    $psched =   $row->schedule;
    $pquater   =  $row->quarter;
    $confirmfrom = $row->ConfirmFrom;
    $confirmto = $row->ConfirmTo;
    $tfrom = date("h:i A", strtotime($row->TimeFrom));
    $tto = date("h:i A", strtotime($row->TimeTo));
    $pfrom = $row->startdate;
    $pto=$row->enddate;
    $nodtr=$row->nodtr;
    $ID=$row->ID;

}
?>
<style type="text/css">
 table {
    width: 98%!important;
}

.c-title{
    color:red;
}

</style>

<form id="form_cutoff" method="POST" action="#">
<input name="dkey" type="hidden" value="<?php echo $ID?>" />
<div class="col-md-12">
    <table width="100%">
        <tr>
            <!-- <td><label class="align_center" ><b style="margin-right: 30px;" class="c-title">DTR Cut-Off Date </b><input type="checkbox" class="cbox nodtr" name="nodtr" <?=($nodtr == 1 ? 'checked' : '')?> > &nbsp;&nbsp; <b>No DTR</b></label></td> -->
            <td><label class="align_center" ><b style="margin-right: 30px;" class="c-title">DTR Cut-Off Date </b></label></td>
            <td><label class="align_center c-title"><b>Confirmation Date </b></label></td>
        </tr>
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_left">Date From</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class='input-group date' id="dfrom" data-date="<?php echo $cutofffrom?>" data-date-format="yyyy-mm-dd">
                                            <input type='text' class="form-control dfrom required-field" size="16" name="dfrom" id="dfrom" type="text" value="<?php echo $cutofffrom?>"/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right">Date To</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class='input-group date' id="dto" data-date="<?php echo $cutoffto?>" data-date-format="yyyy-mm-dd">
                                            <input type='text' class="form-control dto required-field" size="16" name="dto" id="dto" type="text" value="<?php echo $cutoffto?>"/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right c-title">Payroll Cut-off</label></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td >
                              <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right" >Schedule</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                         <div class="field">
                                          <select class="chosen align_left required-field" name="payrollschedule" id="schedule"><?php echo $this->payrolloptions->payschedule($psched);?></select>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    <tr>
                        <td>
                              <table width="100%">
                                <tr>
                                    <div class="form_row no-search" id="qload" <?php echo $ishidden?>></div>
                                    <div class="form_row no-search" id="qshow" <?php echo $ishidden?>></div>
                                   
                                    <td width='25%'> <label class="field_name align_right">Quarter</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class="field">
                                       <select class="chosen align_left required-field" name="payrollquarter" id="quarter"><?php echo $this->payrolloptions->quarter($pquater,TRUE,$psched);?></select>
                                      </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right">Start Date</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class="field">
                                            <div class='input-group date' id="payrolldfrom" data-date="<?php echo $pfrom?>" data-date-format="yyyy-mm-dd">
                                                <input type='text' class="form-control payrolldfrom required-field" size="16" name="payrolldfrom" id="payrolldfrom" type="text" value="<?php echo $pfrom?>"/>
                                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right">End Date</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class="field">
                                            <div class='input-group date' id="payrolldto" data-date="<?php echo $pto?>" data-date-format="yyyy-mm-dd">
                                                <input type='text' class="form-control payrolldto required-field" size="16" name="payrolldto" id="payrolldto" type="text" value="<?php echo $pto?>"/>
                                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;">
                <table width="100%">
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right">Start Date</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class="field">
                                            <div class='input-group date' id="confrmdate" data-date="<?php echo $confirmfrom?>" data-date-format="yyyy-mm-dd">
                                                <input type='text' class="form-control confrmdate required-field" size="16" name="confirm_dfrom" id="confrmdate" type="text" value="<?php echo $confirmfrom?>"/>
                                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right">End Date</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class="field">
                                            <div class='input-group date' id="confrmend" data-date="<?php echo $confirmto?>" data-date-format="yyyy-mm-dd">
                                                <input type='text' class="form-control confrmend required-field" size="16" name="confirm_dto" id="confrmend" type="text" value="<?php echo $confirmto?>"/>
                                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='95%'><label class="field_name align_right c-title">Confirmation Time</label></td>
                                    <td width='5%'>:</td>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right">Time From</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class="field">
                                            <div class='input-group time' style="width: 100%;">
                                                <input type='text' class="form-control tfrom required-field" name="tfrom" id="tfrom" value="<?= $tfrom ?>" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-time"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%">
                                <tr>
                                    <td width='25%'><label class="field_name align_right">Time To</label></td>
                                    <td width='5%'>:</td>
                                    <td>
                                        <div class="field">
                                            <div class='input-group time' style="width: 100%;">
                                                <input type='text' class="form-control tto required-field" name="tto" id="tto" value="<?= $tto ?>" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-time"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

</form>
<script>
    var toks = hex_sha512(" ");
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$('.chosen').chosen();
$('.time').datetimepicker({
    format: 'LT'
});
$("#button_save_modal").unbind("click").click(function(){

    var iscontinue = true;
    $(".required-field").each(function(){
        if(!$(this).val()) iscontinue = false;
    });

    if(!iscontinue){
        Swal.fire({
            icon: 'warning',
            title: "Warning!",
            text: "All fields are required.",
            showConfirmButton: true,
            timer: 2000
        });

        return;
    }


    $("#form_cutoff").submit();
}); 
    
$("#form_cutoff").submit(function(){
 //alert($("#form_cutoff").serialize());
 var formdata = "";  
 var isnodtr = "";
 if($("input[name='nodtr']").is(":checked")) isnodtr = 1;
 else isnodtr = "";
 $('#form_cutoff input, #form_cutoff select, #form_cutoff textarea').each(function(){
    if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val()+"&nodtr="+isnodtr;
    else formdata = $(this).attr('name')+'='+$(this).val()+"&nodtr="+isnodtr;
})

 $.ajax({
        url: "<?php echo site_url("process_/savecutoff")?>",
        type : "POST",
         dataType:"json",
        data : {formdata:GibberishAES.enc(formdata, toks), toks:toks},
        success:function(msg){
           if (msg.err_code== 0)
            {
                 Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: msg.msg,
                    showConfirmButton: true,
                    timer: 1000
                })
                 $("#modalclose").click();
                 loadcoffcontent();
            }
             else
            {
               loadcoffcontent();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: msg.msg,
                    showConfirmButton: true,
                    timer: 1000
                })
            }
        }
    });
    
return false;
 });
$("#schedule").change(function(){
    $("#qshow").hide();
    $("#qload").show().html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='<?php echo base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
        url: "<?php echo site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :    GibberishAES.enc($(this).val() , toks), 
          model     :   GibberishAES.enc( "quarter" , toks),
          toks:toks
        },
        success: function(msg){
            // alert(msg);
           $("#qload").hide();
           $("select[name='payrollquarter']").html(msg).trigger("liszt:updated");
           $("#qshow").show();
        }
    });
});
</script>