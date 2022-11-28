<?
/**
* @author Justin
* @copyright 2016
*/

$dates = explode(',',$cdate);
$sdate = $dates[0];
$edate = $dates[1];

$remainingCutoff = $this->extensions->getRemainingCutoff($sdate, $edate);

$function = ($type == "teaching") ? "emp_confirmed" : "emp_confirmed_nt";
$result = $this->attendance->{$function}($sdate, $edate, $type, "", "", "", "", "", $empstatus);
?>
<style>
  .cbox{
     -ms-transform: scale(1.5); /* IE */
     -moz-transform: scale(1.5); /* FF */
     -webkit-transform: scale(1.5); /* Safari and Chrome */
     -o-transform: scale(1.5); /* Opera */
  }
  .chzn-container .chzn-results li {
    display: none;
    line-height: 33px;
    height: 33px;
    padding: 0 7px;
    list-style: none;
    font-size: 10px;
    color: #333;
  }

  thead > tr, tfoot > tr{
    background-color: #0072c6;";
  }

</style>
<div class="content no-search">
<?if($type == "teaching"){?>
<table class="table table-bordered datatable" id="dble">
    <thead>
        <tr>
            <th class="sorting_asc" rowspan="2">Employee ID</th>
            <th rowspan="2">Name</th>
            <th class="align_center" colspan="3">Overtime (hr:min)</th>
            <th class="align_center">Late</th>
            <th class="align_center">Undertime</th>
            <th class="align_center" rowspan="2">Absent</th>                        
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" rowspan="2" >No. of Days</th>
            <th class="align_center" rowspan="2" >Holiday</th>
            <th class="align_center" rowspan="2" >Status</th>
            <th class="align_center" rowspan="2" >Final Pay</th>
            <!-- <th class="align_center" rowspan="2">Projection Cutoff</th> -->
            <th class="align_center" rowspan="2">Hold<br><input type="checkbox" class="cbox"  name="checkall" /></th>
        </tr>
        <tr>
            <th class="align_center">Regular</th>
            <th class="align_center">Rest Day</th>
            <th class="align_center">Holiday</th>
            <th class="align_center">Hr:min</th>            
            <th class="align_center">Hr:min</th>            
            <th class="align_center">VL</th>
            <th class="align_center">SL</th>
            <th class="align_center">Other</th>
            <!-- <th class="align_center">Service Credit</th> -->
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th style="text-align: right;">Schedule</th>
            <th colspan="3" style="text-align: center;"><select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select></th>
            <th colspan="2" style="text-align: right;">Payroll Cut-Off Date</th>
            <th colspan="5" style="text-align: center;"><div id="qhide" hidden=""></div><div id="qshow"><select class="chosen col-md-11" id="payrollcutoffdd" name="payrollcutoffdd"></select></div></th>
            <th colspan="2" style="text-align: right;">Cut-Off</th>
            <th colspan="2" style="text-align: center;"><div id="quhide" hidden=""></div><div id="qushow"><select class="chosen align_left" name="quarter" id="quarter"></select></div></th>
            <th style="text-align: center;" colspan="3">
                <a href="#" class="btn btn-success" id="savebtn" style="color: white;" >Save Cut-Off</a>
            </th>
        </tr>
    </tfoot>
    <tbody>
    <?
    foreach ($result as $key => $data): 
        $empid = $data["qEmpId"];
        $empFullname = $data["qFullname"];
        $overload = $data["overload"];
        // list($totr, $totrest, $tothol, $totsat, $totsun) = $this->attendance->getTeachingOvertime($data["id"]);
        $totr = $data["otreg"];
        $totrest = $data["otrest"];
        $tothol = $data["othol"]; 
        $tlec = $data["lateadmin"];
        $tutlec = "";
        $tabsent = $data["deducadmin"];
        $tel = $data["eleave"];
        $tvl = $data["vleave"];
        $tsl = $data["sleave"];
        $tol = $data["oleave"];
        $tsc = "";      
        // $tsl = $tsl + $tol;   
        $ishol = $data['isholiday'];
        $workdays = $workdays = round($data['workhours_admin'] / 8);
        $fixedday = $data['fixedday'];
        $status = $data["status"];
        $isFinal = $data["isFinal"];
        $hold_status = $data["hold_status"];
        $checkIfConsecutiveAbsent = $this->extensions->isConsecutiveAbsent($sdate, $edate, $empid);
        // if($tabsent) $tabsent = number_format(($this->attcompute->exp_time($tabsent) / (8 * 3600)), 2);
    ?>
        <tr class="pdata" id="<?= $empid ?>" <?= ($checkIfConsecutiveAbsent) ? 'style ="background-color: #ff6666;"' : '' ?> >
            <td class="pdataid"><?=$empid?></td>
            <td><?=$empFullname?></td>
            <td class="align_center"><?=$totr?></td>
            <td class="align_center"><?=$totrest?></td>
            <td class="align_center"><?=$tothol?></td>
            <td class="align_center"><?=($tlec) ? $tlec : 0?></td>
            <td class="align_center"><?=($tutlec) ? $tutlec: 0?></td>
            <td class="align_center"><?=($tabsent) ? $tabsent : 0?></td>
            <td class="align_center"><?=$tvl?></td>
            <td class="align_center"><?=$tsl?></td>
            <td class="align_center"><?=$tol?></td>
            <!-- <td class="align_center"><?=$tsc?></td> -->
            <td class="align_center"><?=$fixedday?$workdays:$workdays?></td>
            <td class="align_center"><?=$ishol?></td>
            <td class="align_center"><?=$status?></td>
            <td class="align_center">
                <input type="checkbox" name="final_pay" id="final_pay" class="double-sized-cb final_pay" employeeid="<?=$empid?>" <?= ($isFinal) ? "checked" : "---" ?>>
            </td>
            <!-- <td><input type="text" name="project_cutoff" class="project_cutoff" value="<?= $remainingCutoff ?>"></td> -->
            <td class="align_center">
                <input type="checkbox" name="hold_status" id="hold_status" class="double-sized-cb hold_status" employeeid="<?=$empid?>"  <?= ($hold_status) ? "checked" : "---" ?> >
            </td>
        </tr>  
    <?
    endforeach;
    ?>  
    <tr>
        <td colspan="17" style="font-weight: bold;font-style: italic;">Total Employees:<?= count($result) ?></td>
    </tr>
    </tbody>
</table>
<?}else{?>
<table class="table table-bordered datatable" id="dble">
    <thead>
        <tr>
            <th class="sorting_asc" rowspan="2">Employee ID</th>
            <th rowspan="2">Name</th>
            <th class="align_center" colspan="3">Overtime (hr:min)</th>
            <th class="align_center">Late</th>
            <th class="align_center">Undertime</th>
            <th class="align_center" rowspan="2">Absent</th>                        
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" rowspan="2" >No. of Days</th>
            <th class="align_center" rowspan="2" >Holiday</th>
            <th class="align_center" rowspan="2" >Status</th>
            <th class="align_center" rowspan="2" >Final Pay</th>
            <!-- <th class="align_center" rowspan="2">Projection Cutoff</th> -->
            <th class="align_center" rowspan="2">Hold<br><input type="checkbox" class="cbox"  name="checkall" /></th>
        </tr>
        <tr>
            <th class="align_center">Regular</th>
            <th class="align_center">Rest Day</th>
            <th class="align_center">Holiday</th>
            <th class="align_center">Hr:min</th>            
            <th class="align_center">Hr:min</th>            
            <th class="align_center">VL</th>
            <th class="align_center">SL</th>
            <th class="align_center">Other</th>
            <!-- <th class="align_center">Service Credit</th> -->
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th style="text-align: right;">Schedule</th>
            <th colspan="3" style="text-align: center;"><select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select></th>
            <th colspan="2" style="text-align: right;">Payroll Cut-Off Date</th>
            <th colspan="5" style="text-align: center;"><div id="qhide" hidden=""></div><div id="qshow"><select class="chosen col-md-11" id="payrollcutoffdd" name="payrollcutoffdd"></select></div></th>
            <th colspan="2" style="text-align: right;">Cut-Off</th>
            <th colspan="2" style="text-align: center;"><div id="quhide" hidden=""></div><div id="qushow"><select class="chosen align_left" name="quarter" id="quarter"></select></div></th>
            <th style="text-align: center;" colspan="3"><a href="#" class="btn btn-success" id="savebtn" style="color: white;" >Save Cut-Off</a></th>
        </tr>
    </tfoot>
    <tbody>
    <?
    foreach ($result as $key => $data):
        $empid = $data["qEmpId"];
        $empFullname = $data["qFullname"];
        $overload = $data["overload"];
        $totr = $data["otreg"];
        $totrest = $data["otrest"];
        $tothol = $data["othol"]; 
        $tlec = $data["lateut"];
        $tutlec = $data["ut"];
        $tabsent = $data["absent"];
        $tel = $data["eleave"];
        $tvl = $data["vleave"];
        $tsl = $data["sleave"];
        $tol = $data["oleave"];
        $tsc = $data["scleave"];      
        // $tsl = $tsl + $tol;   
        $ishol = $data['isholiday'];
        $workdays = $data['workdays'];
        $fixedday = $data['fixedday'];
        $status = $data["status"];
        $isFinal = $data["isFinal"];
        $hold_status = $data["hold_status"];
        $checkIfConsecutiveAbsent = $this->extensions->isConsecutiveAbsent($sdate, $edate, $empid);
        if($tabsent) $tabsent = number_format(($this->attcompute->exp_time($tabsent) / (8 * 3600)), 2);
    ?>
        <tr class="pdata" id="<?= $empid ?>" <?= ($checkIfConsecutiveAbsent) ? 'style ="background-color: #ff6666;"' : '' ?> >
            <td class="pdataid"><?=$empid?></td>
            <td><?=$empFullname?></td>
            <td class="align_center"><?=$totr?></td>
            <td class="align_center"><?=$totrest?></td>
            <td class="align_center"><?=$tothol?></td>
            <td class="align_center"><?=$tlec?></td>
            <td class="align_center"><?=$tutlec?></td>
            <td class="align_center"><?=$tabsent?></td>
            <td class="align_center"><?=$tvl?></td>
            <td class="align_center"><?=$tsl?></td>
            <td class="align_center"><?=$tol?></td>
            <!-- <td class="align_center"><?=$tsc?></td> -->
            <td class="align_center"><?=$fixedday?$workdays:$workdays?></td>
            <td class="align_center"><?=$ishol?></td>
            <td class="align_center"><?=$status?></td>
            <td class="align_center">
                <input type="checkbox" name="final_pay" id="final_pay" class="double-sized-cb final_pay" employeeid="<?=$empid?>" <?= ($isFinal) ? "checked" : "---" ?>>
            </td>
            <!-- <td><input type="text" name="project_cutoff" class="project_cutoff" value="<?= $remainingCutoff ?>"></td> -->
            <td class="align_center">
                <input type="checkbox" name="hold_status" id="hold_status" class="double-sized-cb hold_status" employeeid="<?=$empid?>"  <?= ($hold_status) ? "checked" : "---" ?> >
            </td>
        </tr>
    <?endforeach;?>
    <tr>
        <td colspan="17" style="font-weight: bold;font-style: italic;">Total Employees:<?= count($result) ?></td>
    </tr>
    </tbody>
</table>
<?}?>
</div>
<script>
var refresh_recompute = true;
var toks = hex_sha512(" ");
/*
 * Jquery Functions 
 */
validateCanWrite();
$(document).ready(function(){
    $("#final_pay").change(function(){
        if (this.checked){
            $('#hold_status').prop('checked', false);
        } 
    });
    $("#hold_status").change(function(){
        if (this.checked){
            $('#final_pay').prop('checked', false);
        } 
    });
});

$("#schedule").change(function(){
    $("#qushow,#savebtn").hide();
    $("#quhide").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-12 align_center"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          toks      :   toks,
          schedule  :   GibberishAES.enc($(this).val(), toks), 
          model     :   GibberishAES.enc("quarterpayroll", toks)
        },
        success: function(msg){
           $("#quhide").hide();
           $("select[name='quarter']").html(msg).trigger("chosen:updated");
           $("#qushow,#savebtn").show();
        }
    });
    
    $("#qshow").hide();
    $("#qhide").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-12 align_center"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
    $.ajax({
        url     : "<?=site_url('payroll_/loadpayrollcutoff')?>",
        type    : "POST",
        data    : {
                    toks      :   toks,
                    schedule  :   GibberishAES.enc("<?= $type ?>", toks), 
                    model     :   GibberishAES.enc("displaycutofffinalizedSaving", toks)
                  },
        success: function(msg){
           $("#qhide").hide();
           $("select[name='payrollcutoffdd']").html(msg).trigger("chosen:updated");
           $("#qshow").show();
        }
    });
});

$("#payrollcutoffdd").change(function(){
    $("#qushow,#savebtn").hide();
    $("#quhide").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-12 align_center"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          toks      :   toks,
          schedule  :   GibberishAES.enc($("#schedule").val(), toks),   
          cutoffdate  :   GibberishAES.enc($(this).val(), toks), 
          model     :   GibberishAES.enc("quarterpayroll", toks)
        },
        success: function(msg){
           $("#quhide").hide();
           $("select[name='quarter']").html(msg).trigger("chosen:updated");
           $("#qushow,#savebtn").show();
        }
    });
});

$("#savebtn").click(function(){
   var sched            =  $("#schedule").val();
   var payrollcutoffdd  =  $("#payrollcutoffdd").val();
   var quarter          =  $("#quarter").val();
   var finalpay_arr = getFinalPayList();
   // console.log(finalpay_arr);
   // return;

    var iscontinue = checkIfSystemIsRecomputing();

    if(sched != "" && payrollcutoffdd != "" && quarter != ""){
        const swalWithBootstrapButtons = Swal.mixin({
             customClass: {
               confirmButton: 'btn btn-success',
               cancelButton: 'btn btn-danger'
             },
             buttonsStyling: false
           })

           swalWithBootstrapButtons.fire({
             title: 'Are you sure?',
             text: "Do you really want to proceed?",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Yes, proceed!',
             cancelButtonText: 'No, cancel!',
             reverseButtons: true
           }).then((result) => {
             if (result.value) {

                /*if(!iscontinue){
                    alert('Module is still recomputing attendance. Please wait.');
                    $("#loading").show();
                    loadRecomputePercentage(tnt); 
                    setTimeout(function(){
                        $("#cmsg").fadeOut().fadeOut("slow").fadeOut(2000);
                    }, 3000);
                    return;
                }*/

                loadRecomputePercentage(tnt); 
                var timer = setInterval(function(){
                    loadRecomputePercentage(tnt); 
                    if(!refresh_recompute){
                        clearInterval(timer);
                    }
                }, 1000);

                $.ajax({
                    url         : "<?=site_url('payroll_/loadmodelfunc')?>",
                    type        : "POST",
                    data        : {
                                    toks        :   toks,
                                    cutoffstart :   GibberishAES.enc("<?=$sdate?>", toks),
                                    cutoffend   :   GibberishAES.enc("<?=$edate?>", toks),
                                    type        :   GibberishAES.enc("<?=$type?>", toks),
                                    schedule    :   GibberishAES.enc(sched, toks),
                                    quarter     :   GibberishAES.enc(quarter, toks),
                                    pcutoffdate :   GibberishAES.enc(payrollcutoffdd, toks),
                                    model       :   GibberishAES.enc("payrollattcutoffsave", toks),
                                    finalpay_arr:   finalpay_arr
                                  },
                    success     :   function(msg){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: msg,
                            showConfirmButton: true,
                            timer: 3000
                        });
                        setTimeout(function(){ location.reload(); }, 3000);
                    } 
                });
             } else if (
               result.dismiss === Swal.DismissReason.cancel
             ) {
               swalWithBootstrapButtons.fire(
                 'Cancelled',
                 'Action is cancelled.',
                 'error'
               )
             }
           })
   }else{
    alert("Please make sure all fields are not empty..");
    return false;
   }
});

$("input[name='checkall']").click(function(){
    if($("input[name='checkall']").prop("checked")){
        $('input[name="hold_status"]').each(function(){
            this.checked = true; 
        });
    }else{
        $('input[name="hold_status"]').each(function(){
            this.checked = false;
        });
    } 
});

$("input[name='checkallfinalpay']").click(function(){
    if($("input[name='checkallfinalpay']").prop("checked")){
        $('input[name="final_pay"]').each(function(){
            this.checked = true; 
        });
    }else{
        $('input[name="final_pay"]').each(function(){
            this.checked = false;
        });
    } 
});

function getFinalPayList(){
    var finalpay_arr = {};
    var isFinalPay = 0;
    $("#dble").find(".pdata").each(function(){
        tr_id = $(this).closest('tr').attr('id');
        var isOnhold = $(".hold_status[employeeid='"+ tr_id +"']").is(':checked') ? 1 : 0;
        // console.log($("input[name=hold_status][employeeid='"+ tr_id +"']").val());
        // if($(".hold_status[employeeid='"+ tr_id +"']").is(':checked')){
        //     console.log('riel '+tr_id);
        // }
        // if(isOnhold == 0){
            // isFinalPay = $("tr[id='"+ tr_id +"']").find("input[name=final_pay]").is(':checked') ? 1 : 0;
             isFinalPay = $(".final_pay[employeeid='"+ tr_id +"']").is(':checked') ? 1 : 0;
            isOnhold = isOnhold;
            project_cutoff = $("tr[id='"+ tr_id +"']").find("input[name=project_cutoff]").val();
            // if(!isOnhold){
                finalpay_arr[tr_id] = [
                    { 
                        "employeeid": GibberishAES.enc(tr_id, toks),
                        "isFinalPay": isFinalPay ,
                        "isOnhold": isOnhold,
                        "project_cutoff": project_cutoff
                    }
                ];
            // }
        // }

    });
    return finalpay_arr;
}

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#savebtn").css("pointer-events", "none");
    else $("#savebtn").css("pointer-events", "");
}

function checkIfSystemIsRecomputing(){
    var iscontinue = false;
    var tnt = $("#tnt").val();
    $.ajax({
        url : "<?=site_url('extensions_/checkIfSystemIsRecomputing')?>",
        type : "POST",
        async: false,
        data : {tnt:tnt},
        success:function(response){
            iscontinue = response;
        }
    });

    return iscontinue;
}

function loadRecomputePercentage(tnt){
    $.ajax({
        url: "<?=site_url('process_/recomputePercentage')?>",
        type: "POST",
        data: {tnt : GibberishAES.enc("<?=$type?>", toks), toks:toks},
        success:function(response){
            $('#processdeductlist').css("color", "green").html(response);
        }
    });
}

$(".chosen").chosen();
</script>