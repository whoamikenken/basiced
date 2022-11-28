 <?php

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\leaveapply.php
 *
 */
$CI =& get_instance();
$CI->load->model('leave');
$CI->load->model('utils');
$CI->load->model('leave_application');

// $server_date = $this->extensions->getServerTime();
// $server_date = explode(" ", $server_date);

// $datetoday = $server_date[0];

$datetoday = date("d-m-Y");

$teachingtype = $this->employee->loadfieldemployee('teachingtype',$this->session->userdata('username'));

$isdisabled = isset($leaveid) ? 'readonly': ''; 

$base_id    = isset($base_id)   ? $base_id      : '';
$leavetype  = isset($leavetype) ? $leavetype    : '';
$paid       = isset($paid)      ? $paid         : '';
$nodays     = isset($nodays)    ? $nodays       : '';
$isHalfDay  = isset($isHalfDay) ? $isHalfDay    : '';
$dfrom      = isset($dfrom)     ? $dfrom        : '';
$dto        = isset($dto)       ? $dto          : '';
$reason     = isset($reason)    ? $reason       : '';
$othertype  = isset($othertype) ? $othertype    : '';
$sched_affected  = isset($sched_affected) ? $sched_affected    : '';



# newly added for ica-hyperion 21194
# by justin (with e)
$empID = $this->session->userdata('username');
$remAllowance = $CI->leave_application->getRemAllowance($empID);
$isAdmin = $this->extras->findIfAdmin($empID);
$sel_emp = '';

# > kapag edit, tapos si admin ang user
if($isAdmin && $base_id){
    # > get selected employee sa employee list..
    $sel_emp = $this->db->query("SELECT * FROM leave_app_emplist WHERE base_id='{$base_id}'")->row()->employeeid;
}

# end for ica-hyperion 21194

$otherleave_list = $CI->leave_application->getAvailableEmployeeOtherLeaveAdmin();

?>

<input type="text" name="rem_allowance" id="rem_allowance" value="<?= $remAllowance ?>" style="display: none;">
<input type="text" name="rem_allowance_og" id="rem_allowance_og" value="<?= $remAllowance ?>" style="display: none;">
<form id="frmleave">
<input type="hidden" name="base_id" value="<?=$base_id?>">
<input type="hidden" name="liquidated" value="NO">
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
            <center><b><h3 tag="title" class="modal-title"><?= ($base_id) ? "Edit " : "Add " ?>Leave Application</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
                <div id="wrapUpload" style="padding-bottom: 10px;">
                    <?php if(!$base_id){ ?>
                        <span id="fileErrorMsg" style="margin-left: 15px;"><b>Upload Supporting Documents</b></span>
                        <input type="file" name="filess" id="filess" style="margin-left:30px;display: inline;" >
                        <span id="fileErrorMsg" style="color: red; margin-left: 1px;"></span>
                    <?php }else{ ?>
                        <label id="processing" style="display: none;margin-left: 20%;"><img src='<?=base_url()?>images/loading.gif' />  Your request is processing, Please Wait..</label>
                        <label style="margin-left: 20%;color: blue;text-decoration: underline;" id="filename" name="filename" file="" mime="">Click to view uploaded image.</label><br>
                    <?php } ?>
                </div><br>
                <!-- for ica-hyperion 21194 -->
                <!-- by justin (with e) -->
                <?if($isAdmin){
                    # kapag admin ang nag applay ng leave request.. lilitaw ito..
                ?>
                <!-- Approve by approver section -->

                <div class="form_row">
                    <input type="hidden" name="othleave" id="othleave">
                    <label class="field_name align_right">Will be approve by approver?</label>
                    <div class="field no-search">
                        <select class="form-control" name="allowApprover" id="allowApprover" style="width: 85%;">
                            <option value="1">YES</option> <!-- kapag yes, dadaan sa sequence approver -->
                            <option value="0">NO</option> <!-- kapag no, deretso approved na -->
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Employee</label>
                    <div class="field" style="padding-bottom: 10px;width: 68%;">
                        <select class="chosen col-md-6" id="employee" name="employee" <?=($base_id) ? "disabled" : ""?> style="width: 85%;">>
                            <?
                                $emplist = $CI->utils->getEmpListToCbo();

                                $i = 0;
                                # displayed employee list
                                foreach ($emplist as $key => $value) {
                                    if($i > 0){
                            ?>
                                    <option value="<?=$key?>" <?=($sel_emp == $key)? "selected" : "" ?>><?=$key ." - ".$value?></option>
                            <?      } # end of if condition
                                    $i += 1;
                                } # end of foreach 
                            ?>
                        </select>
                    </div>
                </div>
                <!-- teaching or non-teaching section -->
                <div class="form_row" hidden>
                    <label class="field_name align_right">Type</label>
                    <div class="field" style="padding-bottom: 10px;">
                        <input type="radio" name="tnt" value="teaching" checked=""> Teaching &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="tnt" value="nonteaching"> Non-Teaching
                    </div>
                </div>
                <!-- hinide ko muna ito, since ang validation para sa no. of days ay hindi gagana.. dahil sa iba iba ang schedule nila.. just in case na may ipabago dito o kaylangan ng multiple select para sa employee ay i-unhide nalang ito.. -->
                <!-- employee list section -->
                <div class="form_row" hidden>
                    <label class="field_name align_right">Employee</label>
                    <div class="field">
                        <!-- kapag admin ito ang lilitaw para sa multiple select -->
                        <select class="chosen col-md-6" id="employeeid" name="employeeid" multiple="">
                            <?
                                # default ko yung teaching
                                $emplist = $CI->utils->findEmpListPerType('teaching');
                                foreach ($emplist as $code => $desc) {?>
                                    <option value="<?=$code?>"><?=$desc?></option>
                                <?}
                            ?>
                        </select>&nbsp;&nbsp;
                        <!-- <span id="loadingemp" hidden=""></span> -->
                    </div>    
                </div>
                <?}?>
                <!-- end for ica-hyperion 21194 -->
                <?
                    $is_editable_ltype = ($base_id) ? false : true;
                    $old_isdisabled = $isdisabled;
                    if(!$is_editable_ltype) $isdisabled = 'disabled';
                    if($isAdmin) $isdisabled = '';
                ?>
                <div class="form_row">
                    <label class="field_name align_right">Leave Type </label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <select name="ltype" class="form-control" style="width: 85%;">

                        </select>
                    </div>
                </div>
                <!-- <div class="form_row">
                    <label class="field_name align_right">Category</label>
                    <div class="field no-search">
                        <select class="form-control" name="category" id="category" placeholder="Category" style="width: 85%;">
                            <?php foreach($this->extras->showreportseduclevel(" - Select a category - ","CATEG") as $key => $value):?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div> -->
                <div class="form_row" id="categoryLeave" hidden>
                    <label class="field_name align_right">Purpose</label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <select name="catleave" class="form-control" style="width: 85%;">
                            <option value="">-Select Purpose-</option>
                            <?
                                $leavecateg = "";
                                if(isset($leavecategory)) $leavecateg = $leavecategory;
                            // var_dump($leavecategory); die;
                                $catlist = $CI->utils->getLeaveCategories();
                                foreach ($catlist as $code => $desc) {?>
                                    <option value="<?=Globals::_e($desc->level)?>" <?= ($desc->level == $leavecateg) ? " selected" : "" ?> ><?=Globals::_e($desc->level)?></option>
                                <?}
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field no-search">
                        <select class="form-control" name="lwithpay" id="lwithpay" placeholder="Withpay" style="width: 85%;" <?= ($base_id && $this->session->userdata("usertype") != "ADMIN") ? "disabled" : "" ?> >
                            <option value="Select">- Select -</option>
                            <?=$this->employeemod->withPay($paid);?></select>
                    </div>
                </div>
				<? if(!$is_editable_ltype) $isdisabled = $old_isdisabled; ?>
				<!-- ///< For half day leave -->

                <div class="form_row">
                    <div class="field" style="padding-bottom: 10px;">
                    &nbsp;<input type="checkbox" class="double-sized-cb" name="ishalfday" value="1" <?=$isHalfDay?'checked':''?> >&nbsp;&nbsp; <b>Check if leave is halfday</b>
                    </div>
                </div>
				
                <div class="form_row" style="padding-bottom: 10px;">
                    <div class="align_left" style="margin-left: 20%;"><b>Leave From <span id="datetotext" style="margin-left: 32%;">To</span></b></div>
                    <div class="field" style="width: 85%;">
                        <div class="col-md-12" id="date_div" style="padding-left: 0px;">
                            <!-- temporary codes -->
                        </div>
                    </div>
                </div>
                <div class="form_row" style="padding-bottom: 10px; display: none">
                    <label class="field_name align_right">No. of days</label>
                    <div class="field no-search">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <input type="text" class="form-control" name="nodays" id="nodays" placeholder="No. days" style="width: 87%;" value="<?=isset($dayscount) ? $dayscount : ''?>" readonly />
                            <span id="loadingdays" hidden=""></span>
                        </div>
                    </div>
                </div>
                <div class="form_row" style="padding-bottom: 10px;">
                    <label class="field_name align_right">No. of leave credit/s to be deducted</label>
                    <div class="field no-search">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <input type="text" class="form-control" name="ndays" id="ndays" placeholder="No. days" style="width: 87%;" value="<?=($nodays) ? $nodays : 0.00?>" readonly />
                            <span id="loadingdays" hidden=""></span>
                        </div>
                    </div>
                </div>
				
				<div class="form_row" id="wrap_sched_affected" <?=($isHalfDay) ? "": 'style="display: none;"'?>>
                    <label class="field_name align_right">Check Schedules Affected</label>
                    <div class="field" id="sched_affected">
                        No Schedule     
                    </div>
                </div>
				
                <div id="seminar_app" style="display: none;">
                    <div class="row">
                        <!--<div class="col-md-12" style="margin-left: 0px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar Category</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="category" placeholder="Seminar Category" style="display: inline;margin-left: 13px;width: 92%;">
                                        <option value=""> - Select Seminar Category - </option>
                                        <?php
                                            $seminarList = Globals::seminarList();
                                            foreach($seminarList as $c=>$val){
                                                ?><option value="<?=$c?>" <?= (isset($category) && $category==$c) ? "selected" : "" ?> ><?=$val?></option><?    
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>-->
                        <!--<div class="col-md-12" style="margin-left: 0px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar - Workshop/Training</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="seminar" placeholder="Seminar - Workshop/Training" style="display: inline;margin-left: 13px;width: 92%;">

                                    </select>
                                </div>
                            </div>
                        </div>-->
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar Title</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <textarea class="form-control" id="seminar_title" value="<?= isset($title) ? $title : '' ?>" placeholder="Type of Seminar Title" style="width: 92%;height: 80px;margin-left: 13px;"><?= isset($title) ? $title : '' ?></textarea>
                                    <input type="hidden" name="title" value="<?= isset($title) ? $title : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Organizer</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" class="form-control" name="organizer" value="<?= isset($organizer) ? $organizer : '' ?>" placeholder="Organizer" style="margin-left: 13px;width: 92%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Remaining Allowance</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                     <input type="text" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"  data-type="currency"  name="remaining_allowance" id="remaining_allowance" value="<?= isset($remAllowance) ? number_format($remAllowance, 2).' PHP' : '' ?>" class="form-control sem-fees col-md-8"  style="margin-left: 13px;width: 34.5%; border: 1px solid #ccc0; pointer-events: none;font-weight: bold; color: red">
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-md-12" style="margin-left: 0px;padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Venue</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="venue" placeholder="Venue" style="display: inline;margin-left: 13px;width: 35%;">
                                        <option value="sample">Sample</option>
                                    </select>
                                </div>
                            </div>
                        </div>-->
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Location</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" name="location" value="<?= isset($location) ? $location : '' ?>" class="form-control" placeholder="Location" style="margin-left: 13px;width: 34.5%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Registration Fee</label>
                                    <div class="col-md-8">
                                        <input type="text"  data-type="currency" name="fee" value="<?= isset($fee) ? $fee : '' ?>" class="form-control sem-fees" placeholder="Registration Fee" style="margin-left: 8px;"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding-left: 0px;">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Deadline of Registration</label>
                                    <div class="col-md-8">
                                        <div class="input-group date" data-date="<?= isset($datesetto) ? $datesetto : '' ?>" data-date-format="yyyy-mm-dd" placeholder="Deadline of Registration" style="width: 86%;">
                                            <input type="text" name="deadline" class="form-control" value="<?= isset($deadline) ? $deadline : '' ?>"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Transportation Fee</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"  data-type="currency" name="transportation" value="<?= isset($transportation) ? $transportation : '' ?>" class="form-control sem-fees" placeholder="Transportation" style="margin-left: 13px;width: 34.5%;">
                                </div>
                            </div>
                        </div>
                       <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Accomodation Fee</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"  data-type="currency" name="accomodation" value="<?= isset($accomodation) ? $accomodation : '' ?>" class="form-control sem-fees" placeholder="Accomodation" style="margin-left: 13px;width: 34.5%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Others Fees</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"  data-type="currency" name="others" value="<?= isset($others) ? $others : '' ?>" class="form-control sem-fees" placeholder="Others" style="margin-left: 13px;width: 34.5%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right " style="margin-left: 0px;">Total:</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"  data-type="currency"  name="totals" id="idTotals" value="<?= isset($total) ? $total : '' ?>" class="form-control sem-fees"  style="margin-left: 13px;width: 34.5%; border: 1px solid #ccc0; pointer-events: none; font-weight: bold;">
                                     <input type="text" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"  data-type="currency"  name="total" id="idTotal" value="<?= isset($total) ? $total : '' ?>" class="form-control sem-fees"  style="margin-left: 13px;width: 34.5%; border: 1px solid #ccc0; pointer-events: none; display: none;">
                                </div>
                                <!-- <input type="hidden" class="sem-fees" name="total" id="idTotal" value="<?= isset($total) ? $total : 0 ?>"> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right remarks">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" class="form-control" name="reason" id="reason" placeholder="Reason" style="width: 85%;"><?=$reason?></textarea>
                    </div>
                </div>
                <div class="alert alert-success" id="msg_header" style="display: none;">
                    <strong>Success!</strong><span>Indicates a successful or positive action.</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span id="errormsg" hidden="" style="color: red; margin-right: 20px;"></span>
            <span id="loading" hidden=""></span>
            <span id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">Submit</button>                
            </span>
        </div>
    </div>
</div>
</form>

<script>
var toks = hex_sha512(" ");
getSeminar($("select[name='category']").val());
getAvailableOtherLeave();
getAvailableDateForLeave();
checkIfSeminarApp("<?=$leavetype?>");
canApplyWithPay();
// convertToFloat();
checkUploadedFile();

var with_pay = '';
var sel_ltype = '';
var dateToday = new Date();
$("#datesetfrompicker,#datesettopicker, .date").datetimepicker({
    format: "YYYY-MM-DD"
});


var sel_ltype = $("select[name='ltype']").val();

if("ABSENT" == sel_ltype){
    $('#lwithpay').val("NO").prop('disabled', true).trigger("liszt:updated");
}else if("VL" == sel_ltype || "SL" == sel_ltype){
    // $('#lwithpay').val("YES").prop('disabled', true).trigger("liszt:updated");
}

$("select[name='ltype']").change(function(){
    sel_ltype = $(this).val();
    if(sel_ltype == "EL") $('#lwithpay').val("YES").prop('disabled', true).trigger("liszt:updated");
});

$(".chosen").chosen();

$('input[name=ishalfday],#datesetfrom,#datesetto').on('change', checkSchedAffected);
$("input[name='datesetfrom'], input[name='datesetto']").blur(function(){
    checkSchedAffected();
});

$(document).off('change').on('change','.sched_affected',function(){
    $("input[name='sched_affected[]']").not(this).prop('checked', false);
    $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    var hrs = 0,
        days = 0.00,
        hasAm = 0,
        hasPm = 0,
        forWholeDay = 0;

    $('.sched_affected').each(function(){
        if($(this).is(':checked')){
            hrs += (+$(this).attr('hrs'));

            if($(this).attr('isAm') != 0) hasAm = 1;
            if($(this).attr('isAm') == 0 && $(this).attr('isBoth') == 0) hasPm = 1;
            if($(this).attr('isBoth') != 0 || (hasAm != 0 && hasPm != 0)) forWholeDay = 1;

        }
    });

    ///< CONDITIONS
    ///< for teaching
    ///< halfday - hindi abot ng 12
    ///< pero pag abot ng 12 (5hrs - whole) (<5 half)

    ///< for nonteaching
    ///< if 1 sched is checked (half day), if 2 (whole day)

    if("<?=$teachingtype?>"=="teaching"){
        if(forWholeDay != 0) days = hrs >= 5 ? 1 : 0.5;
        else            days = hrs > 0 ? 0.5 : 0;
    }else{
        if(forWholeDay != 0) days = 1;
        else            days = hrs > 0 ? 0.5 : 0;
    }

    var start = $("input[name='datesetfrom']").val();
    var end = $("input[name='datesetto']").val();
    var nodays = countDaysWithinScheduleHalfday(start, end);
    days *= nodays;
    if($("#lwithpay").val() == "YES"){
        $("input[name='ndays']").val(days);
        countDays(start, end);
    }else{
        countDays(start, end);
    }
    $("#loadingdays").hide();
});
///< end of script for halfday leave
//pao

$("#othleave").change(function(){
   if($(this).val() == "DA"){
        loaddailyleave();
    }    
});
$("select[name='ltype']").on('change', function() {
    getAvailableDateForLeave();
    $("select[name='ltype']").not(this).prop('checked', false);
    if($(this).val() == "other"){
        $("#othleave").val($(this).val());
    }else if ($(this).val() == "SL") {
        $("#sick").css("display","block");
        $("#sickdisplay").css("display","block");
    }
    else{
        $("#sick").css("display","none");
        $("#sickdisplay").css("display","none");    
        $("#othleave").css("pointer-events","none").val("");
    }

});

$("select[name='ltype']").on('change', function() {
    if($(this).val() == "PL-SEM"){
        getSeminarAllowance();
        $("#categoryLeave").show();
    }
    else{
        $("#categoryLeave").hide();
    }
});

$("select[name='category']").change(function(){
    var code = $(this).val();
    getSeminar(code);
});

$("#employee").unbind("change").change(function(){
    getEmployeeGender();
    getAvailableOtherLeave();
});


$("select[name='ltype']").unbind('click').on('click',function(){
    $(this).prop("checked",true);
    $("select[name='ltype']").not(this).prop('checked', false);

    if($(this).val() == "other")
        $("#othleave").css("pointer-events","");
    else{
        $("#othleave").css("pointer-events","none").val("");
    }

    if($(this).val() == "ABSENT")                           {$('#lwithpay').val("NO").prop('disabled', true).trigger("liszt:updated");}
    // else if($(this).val() == "VL" || $(this).val() == "SL") {$('#lwithpay').val("YES").prop('disabled', true).trigger("liszt:updated");}
    else                                                    {$('#lwithpay').val("Select").prop('disabled', false).trigger("liszt:updated");}


    var dateToday = new Date();
    <?if(!$isAdmin):?>
    if($(this).val() == "VL"){ ///< for VL disable 3 days after today (ICA-HYPERION21710)
        var valid_day = dateToday;

        $.ajax({
           url      :   "<?=site_url("leave_/getVL_MinimumDateToApply")?>",
           type     :   "POST",
           data     :   {
                           <?if($isAdmin){?>
                            empID : $("select[name='employee']").val()
                            <?}?>
                        },
           success  :   function(ret){
                valid_day = ret;

                var current_sel_date = $('#datesetfrom').val();
                if(current_sel_date != ''){
                    var current_sel_date = new Date(current_sel_date);
                    var valid_day = new Date(valid_day);
                    if(current_sel_date < valid_day){
                        $('#datesetfrom').val('');
                        $('#ndays').val(0.00);
                    }
                }
           }
        });
    }
   
    <?endif;?>

});

$("#datesetfrompicker").on("dp.change", function (e) {
    if($('input[name=ishalfday]').is(":checked")){}
    else{
        var start = $(this).find("input").val(),
            end   = $("#datesettopicker").find("input").val();
        countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
        countDays(start, end);
    }
});

$("#datesettopicker").on("dp.change", function (e) {
    var end = $(this).find("input").val(),
        start   = $("#datesetfrompicker").find("input").val();
    countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
    countDays(start, end);
});

$("#filess").change(function(){
    var sizes = $(this).prop("files")[0].size/1024/1024;
    if(sizes > 2){
        $("#msg_header").removeClass("alert alert-danger");
        $("#msg_header").addClass("alert alert-danger");
        $("#msg_header").find("strong").text("Failed! ");
        $("#msg_header").find("span").text("File size exceeds 2 MB. Please try another file.");
        $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
        return;
    }

    var formdata = document.getElementById("filess");
    var uploadname = formdata.files.item(0).name;
    var file_url = URL.createObjectURL(event.target.files[0]);
});

$("#seminar_title").keyup(function(){
    $("input[name='title']").val($(this).val());
});

$("#save").click(function(){
    var empid = "<?= $empID ?>";
    var leave_balance_days = 0;
    var leave_count = 0;
    var total = 0;
    var cancontinue = true;
    
    var base_id = "<?=$base_id?>";

    var allowed = checkIfAllowedProLeave();
    /*if(allowed >= 1 && $("#lwithpay").val() == "YES"){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'You are not allowed to apply professional leave.',
            showConfirmButton: true,
            timer: 2000
        });
        return;
    }*/

    if ((!$("input[name=datesetfrom]").val() && !$("input[name=datesetto]")) && !$('input[name="ishalfday"]').is(':checked')) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Invalid date applied. Please fill-up date from and date to fields.',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    if ((!$("input[name=datesetfrom]").val() || !$("input[name=datesetto]").val() ) && !$('input[name="ishalfday"]').is(':checked')) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Invalid date applied. Please fill-up date from and date to fields.',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }

    if ((!$("input[name=datesetfrom]").val() || !$("input[name=datesetto]").val() )) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Invalid date applied. Please fill-up date from and date to fields.',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }

    if(hasFiledLeave() >= 1 && base_id==""){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Already applied leave on this date.",
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    
    if(hasFiledOB() >= 1 && base_id==""){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Already applied OB on this date.",
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    
    if($('input[name=ishalfday]').is(":checked")){
        if(!$('.sched_affected').is(":checked")){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Schedule affected is required.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }
    }

    var imgVal = $('#filess').val(); 
    /*if(imgVal=='' && base_id=='') { 
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please upload a file!',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }*/

    var reason = $("#frmleave").find('#reason').val(); 
    if(reason=='') { 
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Reason is required!',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    if($("select[name='ltype']").val() == ""){
        // alert("Leave Type is required!.");
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Leave type is required',
            showConfirmButton: true,
            timer: 1000
        });
        return false;
    }

    if($("select[name='lwithpay']").val() == "" || $("select[name='lwithpay']").val() == "Select"){
        // alert("Leave Type is required!.");
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'With pay is required!',
            showConfirmButton: true,
            timer: 1000
        });
        return false;
    }
    <?if($isAdmin):?>
    $("select[name='employee']").removeAttr("disabled");
    <?endif;?>
    $("input[select='ltype']").removeAttr("disabled");

    var timefrom = timeto = "";
    if($('input[name=ishalfday]').is(":checked")){
        if($('.sched_affected').is(":checked")){
            var sched_affected = $('.sched_affected:checked').val().split("|");
            timefrom = sched_affected[0];
            timeto = sched_affected[1];
        }
    }
    
    $("#errormsg").html('');
    var ndays = $("input[name='ndays']").val();
    if((ndays <= 0 && $("#lwithpay").val() == "YES") && $("select[name='ltype']").val() != "PL-SEM"){
        $("#errormsg").show().html("Insufficient Leave Credits or Selected date has no valid schedule.");
        return false;
    }else if($("select[name='ltype']").val() == "PL-SEM"){
        console.log(parseInt($("#idTotal").val()));
        if($("#rem_allowance_og").val() == 0){
            $("#errormsg").show().html("You have no remaining allowance to be used.");
            return false;
        }else if(parseInt($("#idTotal").val()) > parseInt($("#rem_allowance_og").val())){
            
            $("#errormsg").show().html("Exceeded remaining allowance.");
            return false;
        }

        var form_data  = new FormData();
        var file_data = "";
        if($("#filess").val()) file_data = $("#filess").prop("files")[0]
        form_data.append("files",file_data);
        form_data.append("toks",toks);
        var leaveform = decodeURIComponent($("#frmleave").serialize() + "&withpay=" + $("#lwithpay").val() + "&isAdmin=<?=$isAdmin?>" + "&reason=" + reason + "&timefrom=" + timefrom + "&timeto=" + timeto);
        form_data.append("formdata", GibberishAES.enc(leaveform, toks));
        var iscontinue = true;
        $("#frmleave input, #frmleave select, #frmleave textarea").each(function(){
            if($(this).attr("type") != "hidden" && $("select[name='ltype']").val().includes("PL-") && $(this).attr("name") != "filess" && $(this).attr("name") === "undefined" && $(this).attr("name") != "employeeid"){
                if(!$(this).val()){
                    $(this).css("border-color","red").attr("placeholder", "This field is required!.").focus();
                    iscontinue = false;
                }
            }  
        });
        
        if($("select[name='ltype']").val() == "PL-SEM"){
            var app_tot = $("input[name='total']").val();
            var tot_availed = getSeminarAvailedAmount($("select[name='ltype']").val());
            if(parseFloat(app_tot) + parseFloat(tot_availed) > 10000){
                $("#msg_header").removeClass("alert alert-danger");
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text("Exceed 10,000.00 budget.");
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                return;
            }
        }
        if(iscontinue){ 
            $(this).attr("disabled", "disabled");
            $.ajax({
               url      :   "<?=site_url("leave_application_/saveLeaveApp")?>",
               type     :   "POST",
               data     :   form_data,
               contentType: false,
               processData: false,
               dataType : 'json',
               success  :   function(msg){
                    // alert(msg.msg);
                    if(msg.err_code == 1){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: msg.msg,
                            showConfirmButton: true,
                            timer: 5000
                        })
                    }else{
                        $("#save").prop("disabled", false);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: msg.msg,
                            showConfirmButton: true,
                            timer: 5000
                        })
                    }
                    loadTableLeaveHistory("<?=$isAdmin?>");
                    if(msg.err_code)$("#close").click();
                    else{
                        $("#saving").show();
                        $("#loading").hide().html("");
                    }
               }
            });
        }
    }
    else{
        /*if($("select[name='ltype']").val() != "PL-SEM"){
            var total_amount = $("input[name='total']").val();
            if(total_amount > 0) $("input[name='liquidated']").val("YES");
            else $("input[name='liquidated']").val("NO");
        }*/
        /*compare no of days leave*/
        // if($("#withpay").val() == "YES"){
            if($("select[name='ltype']").val() == "VL"){
                <?if($isAdmin && !$base_id):?> cancontinue = validateVacationLeave($("select[name='employee']").val(), ndays); <?endif;?>
                <?if(!$isAdmin && !$base_id):?> cancontinue = validateVacationLeave(empid, ndays); <?endif;?>
                if(!cancontinue){
                    // alert("Insufficient vacation leave credits.");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Insufficient vacation leave credits.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    return;
                }
            }

            <?if($isAdmin):?> leave_balance_days = validateDays($("select[name='employee']").val(), $("select[name='ltype']").val()); <?endif;?>
            <?if(!$isAdmin):?> leave_balance_days = validateDays(empid, $("select[name='ltype']").val()); <?endif;?>
            total = leave_balance_days - ndays;
            if(total < 0 && !"<?=$base_id?>"){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Insufficient Leave Balance.',
                    showConfirmButton: true,
                    timer: 20000
                });

                return;
            }else{
                if($("select[name='ltype']").val() == "VL"){
                    if(!checkVLDays(ndays)) return false;
                }

                if($("#lwithpay").val() == "YES"){
                    <?if($isAdmin):?> leave_count = countLeaveRequest($("select[name='employee']").val(), $("select[name='ltype']").val()); <?endif;?>
                    <?if(!$isAdmin):?> leave_count = countLeaveRequest(empid, $("select[name='ltype']").val()); <?endif;?>

                    if(parseFloat(leave_count) < parseFloat(ndays) && !"<?=$base_id?>"){
                        // alert("Insufficient leave credits.");
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Insufficient leave credits.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                        return;
                    }
                }
                var form_data  = new FormData();
                var file_data = "";
                if($("#filess").val()) file_data = $("#filess").prop("files")[0]
                form_data.append("files",file_data);
                form_data.append("toks",toks);
                var leaveform = decodeURIComponent($("#frmleave").serialize() + "&withpay=" + $("#lwithpay").val() + "&isAdmin=<?=$isAdmin?>" + "&reason=" + reason + "&timefrom=" + timefrom + "&timeto=" + timeto);
                form_data.append("formdata", GibberishAES.enc(leaveform, toks));
                var iscontinue = true;
                $("#frmleave input, #frmleave select, #frmleave textarea").each(function(){
                    if($(this).attr("type") != "hidden" && $("select[name='ltype']").val().includes("PL-") && $(this).attr("name") != "filess" && $(this).attr("name") === "undefined" && $(this).attr("name") != "employeeid"){
                        if(!$(this).val()){
                            $(this).css("border-color","red").attr("placeholder", "This field is required!.").focus();
                            iscontinue = false;
                        }
                    }  
                });
                
                if($("select[name='ltype']").val() == "PL-SEM"){
                    var app_tot = $("input[name='total']").val();
                    var tot_availed = getSeminarAvailedAmount($("select[name='ltype']").val());
                    if(parseFloat(app_tot) + parseFloat(tot_availed) > 10000){
                        $("#msg_header").removeClass("alert alert-danger");
                        $("#msg_header").addClass("alert alert-danger");
                        $("#msg_header").find("strong").text("Failed! ");
                        $("#msg_header").find("span").text("Exceed 10,000.00 budget.");
                        $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                        return;
                    }
                }
                if(iscontinue){ 
                    $(this).attr("disabled", "disabled");
                    $.ajax({
                       url      :   "<?=site_url("leave_application_/saveLeaveApp")?>",
                       type     :   "POST",
                       data     :   form_data,
                       contentType: false,
                       processData: false,
                       dataType : 'json',
                       success  :   function(msg){
                            // alert(msg.msg);
                            if(msg.err_code == 1){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: msg.msg,
                                    showConfirmButton: true,
                                    timer: 1000
                                })
                            }else{
                                $("#save").prop("disabled", false);
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning!',
                                    text: msg.msg,
                                    showConfirmButton: true,
                                    timer: 1000
                                })
                            }
                            loadTableLeaveHistory("<?=$isAdmin?>");
                            if(msg.err_code)$("#close").click();
                            else{
                                $("#saving").show();
                                $("#loading").hide().html("");
                            }
                       }
                    });
                }
            }
        // }

    }
    
});

$('input[name="tnt"]').unbind('change').change(function(){
    var tnt = $(this).val();

    $.ajax({
        url : "<?=site_url("leave_/getEmpListBYTeachingType")?>",
        type : "POST",
        data : {toks:toks,tnt : GibberishAES.enc(tnt, toks)},
        success : function(msg){
            $("select[name='employeeid']").html(msg).trigger("liszt:updated");
        }
    });
});

$("select[name='ltype'], select[name='lwithpay']").change(function(){
    var ltype = $("select[name='ltype']").val();
    var withpay = $("select[name='lwithpay']").val();
    var start = $("#datesetfrompicker").find("input").val();
    var end   = $("#datesettopicker").find("input").val();
    if(!$('input[name=ishalfday]').is(":checked")){
        countDaysWithinSchedule( start, end );
        checkIfSeminarApp($("select[name='ltype']").val());
    }else{
        checkSchedAffected();
    }
});

$("input[name='transportation'], input[name='accomodation'], input[name='others'], input[name='fee']").blur(function(){
    var cur_re = /\D*(\d+|\d.*?\d)(?:\D+(\d{2}))?\D*$/;
    var transportation = $("input[name='transportation']").val() ? cur_re.exec($("input[name='transportation']").val()) : 0;
    var accomodation = $("input[name='accomodation']").val() ? cur_re.exec($("input[name='accomodation']").val()) : 0;
    var others = $("input[name='others']").val() ? cur_re.exec($("input[name='others']").val()) : 0;
    var fee = $("input[name='fee']").val() ? cur_re.exec($("input[name='fee']").val()) : 0;
    transportation = transportation ? parseFloat(transportation[1].replace(/\D/,'')+'.'+(transportation[2]?transportation[2]:'00')) : 0;
    accomodation = accomodation ? parseFloat(accomodation[1].replace(/\D/,'')+'.'+(accomodation[2]?accomodation[2]:'00')) : 0;
    others = others ? parseFloat(others[1].replace(/\D/,'')+'.'+(others[2]?others[2]:'00')) : 0;
    fee = fee ? parseFloat(fee[1].replace(/\D/,'')+'.'+(fee[2]?fee[2]:'00')) : 0;
    var total = transportation + accomodation + others + fee;
    var remaining = $("#rem_allowance_og").val() - total;
    // console.log(remaining);
    if(remaining >= 0){
        $("#rem_allowance").val(remaining);
        $("#errormsg").hide().html("");
        $("#save").css("pointer-events", "unset");
    }else{
        $("#rem_allowance").val(0);
        $("#errormsg").show().html("Exceeded remaining allowance.");
        $("#save").css("pointer-events", "none");
    }
    $("input[name='total']").val(total);
    formatCurrency($("input[name='total']"), "blur");
    formatCurrency($("#rem_allowance"), "blur");
    $("#remaining_allowance").val($("#rem_allowance").val()+ ' PHP');
    $("#idTotals").val($("input[name='total']").val()+ ' PHP');
});

function checkUploadedFile(){
    if("<?=$base_id?>"){
        $.ajax({
          url:"<?=site_url('leave_application_/getLeaveAttachments')?>",
          type: "POST",
          data:{base_id:"<?=$base_id?>"},
          dataType: "json",
          cache:false,
          async:false,
          success:function(response){
            if(response.file && response.mime) $("label[name='filename']").show();
            else  $("label[name='filename']").hide();
          }
        })
    }
}

$("label[name='filename']").click(function(){
    $("label[name='filename']").hide();
    $("#processing").show();
    $.ajax({
      url:"<?=site_url('leave_application_/getLeaveAttachments')?>",
      type: "POST",
      data:{base_id:"<?=$base_id?>"},
      dataType: "json",
      cache:false,
      async:false,
      success:function(response){

        $("label[name='filename']").attr("file", response.file);
        $("label[name='filename']").attr("mime", response.mime);
      }
    }).done(function(){
         if($("label[name='filename']").attr("file")){
              var data = $("label[name='filename']").attr("file");
              var mime = $("label[name='filename']").attr("mime");
              var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
              window.open(objectURL);
          }else{
              var file_url = $("label[name='filename']").attr("content");
              window.open(file_url);
          }
          $("label[name='filename']").show();
          $("#processing").hide(); 
    });
   /* setTimeout(function(){ 
      if($("label[name='filename']").attr("file")){
          var data = $("label[name='filename']").attr("file");
          var mime = $("label[name='filename']").attr("mime");
          var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
          window.open(objectURL);
      }else{
          var file_url = $("label[name='filename']").attr("content");
          window.open(file_url);
      }
      $("label[name='filename']").show();
      $("#processing").hide();
    }, 1000);*/
});

function b64toBlob(b64Data, contentType) {
    var byteCharacters = atob(b64Data)
    var byteArrays = []
    for (let offset = 0; offset < byteCharacters.length; offset += 512) {
        var slice = byteCharacters.slice(offset, offset + 512),
            byteNumbers = new Array(slice.length)
        for (let i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i)
        }
        var byteArray = new Uint8Array(byteNumbers)

        byteArrays.push(byteArray)
    }

    var blob = new Blob(byteArrays, { type: contentType })
    return blob
}

function checkIfAllowedProLeave(){
    var allowed = 0;
    var employeeid = $("select[name='employee']").val();
    if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
    if($("select[name='ltype']").val().includes("PL-")){
        $.ajax({
            url : "<?=site_url('leave_application_/allowedProLeave')?>",
            type: "POST",
            data: {toks:toks,employeeid : GibberishAES.enc(employeeid, toks)},
            async : false,
            success:function(response){
                allowed = response;
            }
        });
    }

    return allowed;
}

function getSeminarAvailedAmount(ltype){
    var employeeid = $("select[name='employee']").val();
    if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
    var tot_availed = 0;
    $.ajax({
        url: "<?=site_url('leave_application_/getSeminarAvailedAmount')?>",
        type: "POST",
        data:{toks:toks,ltype:GibberishAES.enc(ltype, toks), employee:GibberishAES.enc(employeeid, toks)},
        async: false,
        success:function(response){
            tot_availed = response;
        }
    });

    return tot_availed;
};

// $(".sem-fees").change(function(){
//     var amount = parseFloat($(this).val());
//     $(this).val(amount.toFixed(2));
// });

function convertToFloat(){
    $(".sem-fees").each(function(){
        var amount = parseFloat($(this).val());
        $(this).val(amount.toFixed(2));
    });
}

function checkIfSeminarApp(ltype){
    if(ltype){
        if(ltype.includes("PL-")){
            $("#seminar_app").show();
            $(".remarks").text("Other Remarks");
            $("#categoryLeave").show();
        }
        else{
            $("#seminar_app").hide();
            $(".remarks").text("Reason");
            $("#categoryLeave").hide();
        }
    }
}

function getAvailableDateForLeave(){
    $.ajax({
        url: "<?= site_url('leave_/getAvailableDateForLeave') ?>",
        type: "POST",
        data: {
            toks:toks,
            dfrom: GibberishAES.enc("<?= $dfrom ?>", toks),
            dto: GibberishAES.enc("<?= $dto ?>", toks),
            leavetype: GibberishAES.enc($("select[name='ltype']").val(), toks)
        },
        success:function(response){
            $("#date_div").html(response);
        }
    });
}

function getAvailableOtherLeave(){
    var employeeid = $("select[name='employee']").val();
    if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
    $.ajax({
        url: "<?= site_url('leave_/getEmployeeLeaveList') ?>",
        type: "POST",
        data: {toks:toks,employeeid: GibberishAES.enc(employeeid, toks), leavetype: GibberishAES.enc("<?=$leavetype?>", toks)},
        success:function(response){
            $("select[name='ltype']").html(response).trigger("liszt:updated");
        }
    })
}

function getSeminarAllowance(){
    var employeeid = $("select[name='employee']").val();
    if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
    $.ajax({
        url: "<?= site_url('leave_/getSeminarAllowance') ?>",
        type: "POST",
        data: {
            toks:toks,
            employeeid:GibberishAES.enc(employeeid, toks)
        },
        success:function(response){
            $("#remaining_allowance").val(response+' PHP');
        }
    });
}

function checkVLDays(ndays){
    var isValidVL = true;
    /*if(ndays >= 3 && ndays <= 10){
        $("#errormsg").show().html("Leave of atleast 3 days should be filed 2 weeks before the day of leave.");
       isValidVL = false;
    }else if(ndays > 10){
        $("#errormsg").show().html("Atleast 10-day leave is allowed.");
        isValidVL = false;
    }*/
    return isValidVL;
}
 
function validateDays(employeeid, ltype){
    var leave_days = 0;
    if(employeeid && ltype){
        var start = $("#datesetfrompicker").find("input").val();
        $.ajax({
            type: "POST",
            async: false,
            url: "<?= site_url('leave_application_/validateLeaveRequest') ?>",
            data: {
                    toks:toks,
                    employeeid : GibberishAES.enc(employeeid, toks),
                    dfrom : GibberishAES.enc(start, toks),
                    ltype : GibberishAES.enc(ltype, toks),
                    other : GibberishAES.enc($("#othleave").val(), toks)
                  },
            success:function(response){
                leave_days = response;
            }
        });
    }
    return leave_days;
}

function countLeaveRequest(employeeid, ltype){
    var leave_days = 0;
    if(employeeid && ltype){
        var start = $("#datesetfrompicker").find("input").val();
        $.ajax({
            type: "POST",
            async: false,
            url: "<?= site_url('leave_application_/countLeaveRequest') ?>",
            data: {
                    toks:toks,
                    employeeid : GibberishAES.enc(employeeid, toks),
                    dfrom : GibberishAES.enc(start, toks),
                    ltype : GibberishAES.enc(ltype, toks),
                    other : GibberishAES.enc($("#othleave").val(), toks)
                  },
            success:function(response){
                leave_days = response;
            }
        });
    }
    return leave_days;
}

function validateVacationLeave(employeeid, ndays){
    var cancontinue = 0;
    var start = $("#datesetfrompicker").find("input").val();
    if(employeeid){
        $.ajax({
            type: "POST",
            async: false,
            url: "<?= site_url('leave_application_/validateVacationLeave') ?>",
            data: {
                    toks:toks,
                    employeeid : GibberishAES.enc(employeeid, toks),
                    start : GibberishAES.enc(start, toks),
                    ndays : GibberishAES.enc(ndays, toks)
                  },
            success:function(response){
                cancontinue = response;
            }
        });
    }
    return cancontinue;
}

function getEmployeeGender(){
    $.ajax({
        url: "<?= site_url("extensions_/getEmployeeGender") ?>",
        type: "POST",
        data: {toks:toks,employeeid: GibberishAES.enc($("select[name='employee']").val(), toks)},
        success:function(response){
            $("#gender").val(response);
        }
    });
}

function checkSchedAffected(){
    $("#errormsg").hide();
    if($('input[name=ishalfday]').is(":checked")){

        var start = $("#datesetfrompicker").find("input").val();
        var end = $("#datesettopicker").find("input").val();
        // $("#datesettopicker").find("input").val(start);
        // $("#datesettopicker").hide();
        // $("#datetotext").hide();
        
        $("input[name='ndays']").val('0.00');
        if(start != '' || end != ''){

                var issame_sched = checkIfSameSched();
                // console.log(issame_sched);
                if(issame_sched){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'You are not allowed to apply with different schedule.',
                        showConfirmButton: true,
                        timer: 2000
                    });
                }

                $.ajax({
                   url      :   "<?=site_url("leave_/getEmployeeScheduleStartEnd")?>",
                   type     :   "POST",
                   data     :   {
                                    toks : toks,
                                    start : GibberishAES.enc(start, toks),
                                    end : GibberishAES.enc(end, toks)
                                    
                                    <?if($isAdmin){?>
                                    , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                                    <?}?>
                                },
                   success  :   function(ret){
                    var arr_sched = JSON.parse(ret);

                    var hrs = 0.00;
                    var fromtime    = '',
                        totime      = '',
                        isAm        = '',
                        isBoth      = '';
                    // $("input[name='ndays']").val(hrs);
                    // $("#nodays").val(hrs);
                    ///< append sched affected
                    if($(arr_sched).size() > 0){
                        $('#sched_affected').html("");

                        for (var key in arr_sched) {

                            /*if(key=='FLEXI'){
                                if($("#lwithpay").val() == "YES"){
                                    $("input[name='ndays']").val(0.5);
                                    $("#nodays").val(0.5);
                                }else{
                                    $("input[name='ndays']").val(0.00);
                                    // $("#nodays").val(0);
                                }
                            }else{*/

                                var key_arr = key.split('|');
                                fromtime = key_arr[0] ? key_arr[0] : '';
                                totime   = key_arr[1] ? key_arr[1] : '';
                                hrs      = key_arr[2] ? key_arr[2] : 0;
                                isAm     = key_arr[3] ? key_arr[3] : 0;
                                isBoth      = key_arr[4] ? key_arr[4] : 0;
                                // $sched_affected

                                // for ica-hyperion 21194
                                // modified by justin (with e)
                                var val = fromtime +"|"+ totime;
                                var selSched = '', isChecked = '';
                                if("<?=$sched_affected?>") selSched = "<?=$sched_affected?>";
                                
                                if(val == selSched){
                                    isChecked = "checked";
                                    if("<?=$paid?>" == "YES"){
                                        var days = ("<?=$nodays?>" != '' ? "<?=$nodays?>" : 0.5);
                                        var nodays = countDaysWithinScheduleHalfday(start, end);
                                        // days *= nodays;
                                        if(days == 0) days = 0.00;
                                        $("input[name='ndays']").val(days);
                                        // $("#nodays").val(days);
                                    }else{
                                        $("input[name='ndays']").val(0.00);
                                        // $("#nodays").val(0);
                                    }
                                } 
                                // end for ica-hyperion 21194

                                $('#sched_affected').append('<span class="col-md-4"><input type="checkbox" name="sched_affected[]" class="sched_affected" fromtime="'+fromtime+'" totime="'+totime+'" hrs="'+hrs+'" isAm="'+isAm+'" isBoth="'+isBoth+'" value="'+ val +'" '+ isChecked +'> '+arr_sched[key]+'</span>');

                                $('#wrap_sched_affected').show();
                            // }

                        }
                    }else{
                        $('#sched_affected').html("No Schedule");
                        $('#wrap_sched_affected').show();
                    }
                   }
                });
        }
    }else{
        $("#datesettopicker").show();
        $("#datetotext").show();
        $('#wrap_sched_affected').hide();
        var start = $("#datesetfrompicker").find("input").val();
            end   = $("#datesettopicker").find("input").val();
        countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
        countDays(start, end);
    }
}

function checkIfSameSched(){
     var issame = true;
     var start = $("#datesetfrompicker").find("input").val();
     var end = $("#datesettopicker").find("input").val();
     $.ajax({
           url      :   "<?=site_url("leave_/checkIfSameSchedLeave")?>",
           type     :   "POST",
           data     :   {
                            toks : toks,
                            start : GibberishAES.enc(start, toks),
                            end : GibberishAES.enc(end, toks)
                            
                            <?if($isAdmin){?>
                            , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                            <?}?>
                        },
            async    :   false,
            success  :   function(response){
                issame = response;
            }

       });

     return issame;
}

function loadTableLeaveHistory(isAdmin){
    if(isAdmin) view_leave_status();
    else        loadleavehistory('',0,'apply');
}

<?if($isHalfDay == 1){?>
    // checkSchedAffected();
<?}?>

function countDaysWithinSchedule(start, end){
    $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
       type     :   "POST",
       data     :   {
                        toks:toks,
                        start : GibberishAES.enc(start, toks), 
                        end : GibberishAES.enc(end, toks),
                        withpay : GibberishAES.enc($("select[name='lwithpay']").val(), toks)

                        // added by justin (with e) for ica-hyperion 21194
                        <?if($isAdmin){?>
                        , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                        <?}?>
                        // end for ica-hyperion 21194
                    },
       success  :   function(days){
        if(days == 0) days = 0.00;
        if($('input[name=ishalfday]').is(":checked")) $("input[name='ndays']").val(0.00);
        else $("input[name='ndays']").val(days);
        $("#loadingdays").hide();
       }
    });
}

function countDays(start, end){
    $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
       type     :   "POST",
       data     :   {
                        alldays : true, 
                        toks:toks,
                        start : GibberishAES.enc(start, toks), 
                        end : GibberishAES.enc(end, toks),
                        leavetype : GibberishAES.enc($("select[name='ltype']").val(), toks)

                        // added by justin (with e) for ica-hyperion 21194
                        <?if($isAdmin){?>
                        , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                        <?}?>
                        // end for ica-hyperion 21194
                    },
       success  :   function(days){
        if(days == 0) days = 0.00;
        if($('input[name=ishalfday]').is(":checked")) $("input[name='ndays']").val(0.00);
        else $("input[name='ndays']").val(days);
        $("#loadingdays").hide();
       }
    });
}

function countDaysWithinScheduleHalfday(start, end){
    var scheddays = 0;
    $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
       type     :   "POST",
       data     :   {
                        toks:toks,
                        start : GibberishAES.enc(start, toks), 
                        end : GibberishAES.enc(end, toks),
                        withpay : GibberishAES.enc($("select[name='lwithpay']").val(), toks)

                        // added by justin (with e) for ica-hyperion 21194
                        <?if($isAdmin){?>
                        , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                        <?}?>
                        // end for ica-hyperion 21194
                    },
                    async: false,
       success  :   function(days){
            scheddays = days;
            $("#loadingdays").hide();
       }
    });

    return scheddays;
}

function getSeminar(code){
    $.ajax({
        url : "<?= site_url('extensions_/showreportseduclevel') ?>",
        type: "POST",
        data: {code: code, idkey: "<?= isset($seminar) ? $seminar : '' ?>"},
        success:function(response){
            $("select[name='seminar']").html(response).trigger("chosen:updated");
        }
    })
}

function canApplyWithPay(){
    return;
    var empid = "<?= $empID ?>";
    var leave_balance_days = 0;
    <?if($isAdmin && !$base_id):?> leave_balance_days = validateDays($("select[name='employee']").val(), $("select[name='ltype']").val()); <?endif;?>
    <?if(!$isAdmin && !$base_id):?> leave_balance_days = validateDays(empid, $("select[name='ltype']").val()); <?endif;?>
    // console.log(leave_balance_days);
    if(leave_balance_days >= 1) $('#lwithpay').val("YES").prop('disabled', true).trigger("liszt:updated");
    // else $('#withpay').val("NO").prop('disabled', true).trigger("liszt:updated");
}

function hasFiledLeave(){
    var employeeid = $("select[name='employee']").val();
    if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
    var hasfiled = timefrom = timeto = "";
    if($('input[name=ishalfday]').is(":checked")){
        if($('.sched_affected').is(":checked")){
            var sched_affected = $('.sched_affected:checked').val().split("|");
            timefrom = sched_affected[0];
            timeto = sched_affected[1];
        }
    }
    $.ajax({
        url: "<?= site_url('leave_application_/hasFiledLeave') ?>",
        type: "POST",
        data:{
            toks:toks,
            employeeid : GibberishAES.enc(employeeid, toks),
            datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
            datesetto : GibberishAES.enc($("input[name='datesetto']").val(), toks),
            ishalfday : GibberishAES.enc($('input[name=ishalfday]').is(":checked"), toks),
            timefrom : GibberishAES.enc(timefrom, toks),
            timeto : GibberishAES.enc(timeto, toks)
        },
        async: false,
        success:function(response){
            if(response >= 1) hasfiled = response;
        }
    });
    return hasfiled;
}

function hasFiledOB(){
    var employeeid = $("select[name='employee']").val();
    if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
    var hasfiled = timefrom = timeto = "";
    if($('input[name=ishalfday]').is(":checked")){
        if($('.sched_affected').is(":checked")){
            var sched_affected = $('.sched_affected:checked').val().split("|");
            timefrom = sched_affected[0];
            timeto = sched_affected[1];
        }
    }
    $.ajax({
        url: "<?= site_url('ob_application_/hasFiledOB') ?>",
        type: "POST",
        data:{
            toks:toks,
            employeeid : GibberishAES.enc(employeeid, toks),
            datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
            datesetto : GibberishAES.enc($("input[name='datesetto']").val(), toks),
            ishalfday : GibberishAES.enc($('input[name=ishalfday]').is(":checked"), toks),
            timefrom : GibberishAES.enc(timefrom, toks),
            timeto : GibberishAES.enc(timeto, toks)
        },
        async: false,
        success:function(response){
            if(response >= 1) hasfiled = response;
        }
    });
    return hasfiled;
}

$(".sem-fees").on({
    keyup: function() {
      formatCurrency($(this));
    },
    blur: function() { 
      formatCurrency($(this), "blur");
    }
});


function formatNumber(n) {
  // format number 1000000 to 1,234,567
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}


function formatCurrency(input, blur) {
  // appends $ to value, validates decimal side
  // and puts cursor back in right position.
  
  // get input value
  var input_val = input.val();
  
  // don't validate empty input
  if (input_val === "") { return; }
  
  // original length
  var original_len = input_val.length;

  // initial caret position 
  var caret_pos = input.prop("selectionStart");
    
  // check for decimal
  if (input_val.indexOf(".") >= 0) {

    // get position of first decimal
    // this prevents multiple decimals from
    // being entered
    var decimal_pos = input_val.indexOf(".");

    // split number by decimal point
    var left_side = input_val.substring(0, decimal_pos);
    var right_side = input_val.substring(decimal_pos);

    // add commas to left side of number
    left_side = formatNumber(left_side);

    // validate right side
    right_side = formatNumber(right_side);
    
    // On blur make sure 2 numbers after decimal
    if (blur === "blur") {
      right_side += "00";
    }
    
    // Limit decimal to only 2 digits
    right_side = right_side.substring(0, 2);

    // join number by .
    input_val =  left_side + "." + right_side;

  } else {
    // no decimal entered
    // add commas to number
    // remove all non-digits
    input_val = formatNumber(input_val);
    input_val = input_val;
    
    // final formatting
    if (blur === "blur") {
      input_val += ".00";
    }
  }
  
  // send updated string to input
  input.val(input_val);

  // put caret back in the right position
  var updated_len = input_val.length;
  caret_pos = updated_len - original_len + caret_pos;
  input[0].setSelectionRange(caret_pos, caret_pos);
}

</script>