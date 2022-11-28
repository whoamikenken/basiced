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

$isdisabled = 'disabled'; 

$base_id    = isset($base_id)   ? $base_id      : '';
$leaveid    = isset($leaveid)   ? $leaveid      : '';
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
$isAdmin = $this->extras->findIfAdmin($empID);
if($isAdmin) $empID = $employeeid;
$remAllowance = $CI->leave_application->getRemAllowance($empID);
$sel_emp = '';

# > kapag edit, tapos si admin ang user
if($isAdmin && $base_id){
    # > get selected employee sa employee list..
    $sel_emp = $this->db->query("SELECT * FROM leave_app_emplist WHERE base_id='{$base_id}'")->row()->employeeid;
}

# end for ica-hyperion 21194

$otherleave_list = $CI->leave_application->getAvailableEmployeeOtherLeaveAdmin();

?>

<input type="hidden" name="rem_allowance" id="rem_allowance" value="<?= $remAllowance ?>">
<form>
<input type="hidden" name="base_id" value="<?=$base_id?>">

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
            <center><b><h3 tag="title" class="modal-title">Leave Application Details</h3></b></center>
        </div>
        <label id="processing" style="display: none;margin-left: 20%;"><img src='<?=base_url()?>images/loading.gif' />  Your request is processing, Please Wait..</label>
        <label style="margin-left: 20%;color: blue;text-decoration: underline;" id="filename_det" name="filename_det" file="" mime="">Click to view uploaded image.</label><br>
        <div class="modal-body">
            <div class="content">
                <?if($isAdmin){
                ?>
                <div class="form_row">
                    <label class="field_name align_right">Employee</label>
                    <div class="field" style="padding-bottom: 10px;width: 72.8%;">
                        <select class="chosen col-md-6" id="employee" name="employee" <?=($base_id) ? "disabled" : ""?>>
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
                <div class="form_row">
                    <label class="field_name align_right">Leave Type</label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <select name="ltype_d" class="form-control" style="width: 60%;" <?=$isdisabled?>>

                        </select>
                    </div>
                </div>
                <div class="form_row" style="display: none;">
                    <label class="field_name align_right">Category</label>
                    <div class="field no-search">
                        <select class="form-control" name="category" id="category" placeholder="Category" style="width: 60%;">
                            <?php foreach($this->extras->showreportseduclevel(" - Select a category - ","CATEG") as $key => $value):?>
                                <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form_row" id="categoryLeave" hidden>
                    <label class="field_name align_right">Purpose</label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <select name="catleave" class="form-control" style="width: 60%;" disabled>
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
                        <select disabled class="form-control" name="lwithpay_d" id="lwithpay_d" placeholder="Withpay" style="width: 60%;" <?= ($base_id && $this->session->userdata("usertype") != "ADMIN") ? "disabled" : "" ?> ><?=$this->employeemod->withPay($paid);?></select>
                    </div>
                </div>
                <!-- ///< For half day leave -->

                <div class="form_row">
                    <div class="field" style="padding-bottom: 10px;">
                    &nbsp;<input disabled type="checkbox" class="double-sized-cb" name="ishalfday_d" value="1" <?=$isHalfDay?'checked':''?> >&nbsp;&nbsp; <b>Check if leave is halfday</b>
                    </div>
                </div>
                
                <div class="form_row" style="padding-bottom: 10px;">
                    <div class="align_left" style="margin-left: 20%;"><b>Leave From <span id="datetotext" style="margin-left: 21%;">To</span></b></div>
                    <div class="field" style="width: 60%;">
                        <div class="col-md-12" id="date_div_details" style="padding-left: 0px;">
                            <!-- temporary codes -->
                            <div class="col-md-5" style="padding-left: 0px;">
                                <div class='input-group date' data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd" disabled="">
                                    <input type='text' class="form-control date" size="16" name="datesetfrom_d" type="text" value="<?=$dfrom?>" autcomplete="off" readonly/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-5" style="margin-left: 3px;">
                                <div class="input-group date" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd" disabled="">
                                    <input type='text' class="form-control date" size="16" name="datesetto_d" type="text" value="<?=$dto?>" autcomplete="off" readonly />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form_row" style="padding-bottom: 10px; display: none">
                    <label class="field_name align_right">No. of days</label>
                    <div class="field no-search">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <input type="text" class="form-control" name="nodays_d" id="nodays_d" placeholder="No. days" style="width: 61%;" value="<?=($dayscount) ? $dayscount : 0?>" readonly />
                            <span id="loadingdays" hidden=""></span>
                        </div>
                    </div>
                </div>

                <div class="form_row" style="padding-bottom: 10px;">
                    <label class="field_name align_right">No. of leave credit/s to be deducted</label>
                    <div class="field no-search">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <input type="text" class="form-control" name="ndays_d" id="ndays_d" placeholder="No. days" style="width: 61%;" value="<?=($dayscount) ? $dayscount : 0?>" readonly />
                            <span id="loadingdays" hidden=""></span>
                        </div>
                    </div>
                </div>
                
                <div class="form_row" id="wrap_sched_affected_details" <?=($isHalfDay) ? "": 'style="display: none;"'?>>
                    <label class="field_name align_right">Check Schedules Affected</label>
                    <div class="field" id="sched_affected_details">
                        No Schedule     
                    </div>
                </div>
                
                <div class="seminar_app" style="display: none;">
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
                        <div class="col-md-12" style="margin-left: 0px;display:none;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar - Workshop/Training</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="seminar" placeholder="Seminar - Workshop/Training" style="display: inline;margin-left: 13px;width: 92%;">

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar Title</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <textarea <?=$isdisabled?> class="form-control" id="seminar_title" value="<?= isset($title) ? $title : '' ?>" style="width: 92%;height: 80px;margin-left: 13px;"><?= isset($title) ? $title : '' ?></textarea>
                                    <input type="hidden" name="title" value="<?= isset($title) ? $title : '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Organizer</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" class="form-control" name="organizer" value="<?= isset($organizer) ? $organizer : '' ?>" placeholder="Organizer" style="margin-left: 13px;width: 92%;" <?=$isdisabled?>>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Remaining Allowance</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                     <span id="remaining_allowance" style="margin-left: 3%;font-weight: bold; color: red" ><?= isset($remAllowance) ? $remAllowance.".00 PHP" : '0.00 PHP' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: 0px;padding-bottom: 10px;display:none">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Venue</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="venue" placeholder="Venue" style="display: inline;margin-left: 13px;width: 35%;">
                                        <option value="sample">Sample</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Location</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" name="location" value="<?= isset($location) ? $location : '' ?>" class="form-control" placeholder="Location" style="margin-left: 13px;width: 34.5%;" <?=$isdisabled?>>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Registration Fee</label>
                                    <div class="col-md-8">
                                        <input type="number" name="fee" value="<?= isset($fee) ? $fee : '' ?>" class="form-control sem-fees" value="" placeholder="Registration Fee" style="margin-left: 8px;" <?=$isdisabled?>/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding-left: 0px;">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Deadline of Registration</label>
                                    <div class="col-md-8">
                                        <div class="input-group date" data-date="<?= isset($datesetto) ? $datesetto : '' ?>" data-date-format="yyyy-mm-dd" placeholder="Deadline of Registration" style="width: 86%;">
                                            <input type="text" name="deadline" class="form-control" value="<?= isset($deadline) ? $deadline : '' ?>" <?=$isdisabled?>/>
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
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Transportation</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="number" name="transportation" value="<?= isset($transportation) ? $transportation : '' ?>" class="form-control sem-fees" placeholder="Transportation" style="margin-left: 13px;width: 34.5%;" <?=$isdisabled?>>
                                </div>
                            </div>
                        </div>
                       <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Accomodation</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="number" name="accomodation" value="<?= isset($accomodation) ? $accomodation : '' ?>" class="form-control sem-fees" placeholder="Accomodation" style="margin-left: 13px;width: 34.5%;" <?=$isdisabled?>>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Others</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="number" name="others" value="<?= isset($others) ? $others : '' ?>" class="form-control sem-fees" placeholder="Others" style="margin-left: 13px;width: 34.5%;" <?=$isdisabled?>>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Total:</label> <span id="budget_total" style="margin-left: 3%;font-weight: bold;" ><?= isset($total) ? $total." PHP" : '0' ?></span>
                                <input type="hidden" class="sem-fees" name="total" id="idTotal" value="<?= isset($total) ? $total : '0' ?>" <?=$isdisabled?>>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right remarks">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" class="form-control" placeholder="Reason" style="width: 90.5%;" <?=$isdisabled?>><?=$reason?></textarea>
                    </div>
                </div>
                <br>
                <div class="form_row">
                    <label class="field_name align_right">Status</label>
                    <div class="field">
                        <input type="text" name="status" class="form-control" value="<?=$status?>" disabled='' style="width: 90.5%;">
                    </div>
                </div>
                <br>
                <div class="form_row" style="<?= ($status != "DISAPPROVED") ? 'display:none;' : ""?>">
                    <label class="field_name align_right">Remarks</label>
                    <div class="field">
                        <input type="text" name="status" class="form-control" value="<?=$remarks?>" disabled='' style="width: 90.5%;">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </span>
        </div>
    </div>
</div>
</form>

<script>
var toks = hex_sha512(" ");
// <?if($isHalfDay == 1){?>
//     checkSchedAffected();
// <?}?>

checkSchedAffected();
getSeminar($("select[name='category']").val());
getAvailableOtherLeave();
// getAvailableDateForLeave();
checkIfSeminarApp("<?=$leavetype?>");
canApplyWithPay();
convertToFloat();
countDays();
checkUploadedFile();

var with_pay = '';
var sel_ltype = '';
var dateToday = new Date();
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();

$("#close").click(function(){
    $("#mymodalleave").find("modal-body").html("");
});

function convertToFloat(){
    $(".sem-fees").each(function(){
        var amount = parseFloat($(this).val());
        $(this).val(amount.toFixed(2));
    });
}

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
            if(response.file && response.mime) $("label[name='filename_det']").show();
            else  $("label[name='filename_det']").hide();
          }
        })
    }
}

function checkIfSeminarApp(ltype){
    if(ltype){
        if(ltype.includes("PL-")){
            $(".seminar_app").show();
            $(".remarks").text("Other Remarks");
            $("#categoryLeave").show();
        }
        else{
            $(".seminar_app").hide();
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
            leavetype: GibberishAES.enc($("select[name='ltype_d']").val(), toks),
            isdetails: GibberishAES.enc(1, toks)
        },
        success:function(response){
            $("#date_div_details").html(response);
        }
    });
}

function validateDays(employeeid, ltype){
    var leave_days = 0;
    if(employeeid && ltype){
        $.ajax({
            type: "POST",
            async: false,
            url: "<?= site_url('leave_application_/validateLeaveRequest') ?>",
            data: {
                    toks:toks,
                    employeeid : GibberishAES.enc(employeeid, toks),
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

function getAvailableOtherLeave(){
    var employeeid = $("select[name='employee']").val();
    if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
    $.ajax({
        url: "<?= site_url('leave_/getEmployeeLeaveList') ?>",
        type: "POST",
        data: {toks:toks,employeeid: GibberishAES.enc(employeeid, toks), leavetype: GibberishAES.enc("<?=$leavetype?>", toks)},
        success:function(response){
            $("select[name='ltype_d']").html(response).trigger("liszt:updated");
        }
    })
}

function checkSchedAffected(){
    $("#errormsg").hide();
    // console.log();
    if($('input[name=ishalfday_d]').is(":checked")){

        var start = $("input[name='datesetfrom_d']").val();
        var end = $("input[name='datesetto_d']").val();
        /*$("#datesettopicker").find("input").val(start);
        $("#datesettopicker").hide();
        $("#datetotext").hide();*/
    // console.log(start);
        
        
        $("input[name='ndays_d']").val('0');
        if(start != ''){

                $.ajax({
                   url      :   "<?=site_url("leave_/getEmployeeLeaveSchedule")?>",
                   type     :   "POST",
                   data     :   {
                                    toks:toks,
                                    base_id : GibberishAES.enc("<?=$leaveid?>", toks),
                                    start : GibberishAES.enc(start, toks)
                                    
                                    <?if($isAdmin){?>
                                    , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                                    <?}?>
                                },
                   success  :   function(ret){
                    var arr_sched = JSON.parse(ret);

                    var hrs = 0;
                    var fromtime    = '',
                        totime      = '',
                        isAm        = '',
                        isBoth      = '';
                    $("input[name='ndays_d']").val(hrs);
                    ///< append sched affected
                    if($(arr_sched).size() > 0){
                        $('#sched_affected_details').html("");

                        for (var key in arr_sched) {

                            if(key=='FLEXI'){
                                $("input[name='ndays_d']").val(0.5);
                            }else{

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
                                    // var days = 0.5;
                                    // var nodays = countDaysWithinScheduleHalfday(start, end);
                                    countDaysWithinScheduleHalfday(start, end);
                                    // console.log(nodays);
                                    // days *= nodays;
                                    // if($("#lwithpay_d").val() == "YES") $("input[name='ndays_d']").val(days);
                                } 
                                // end for ica-hyperion 21194

                                $('#sched_affected_details').append('<span class="col-md-4"><input type="checkbox" name="sched_affected[]" class="sched_affected" fromtime="'+fromtime+'" totime="'+totime+'" hrs="'+hrs+'" isAm="'+isAm+'" isBoth="'+isBoth+'" value="'+ val +'" '+ isChecked +'> '+arr_sched[key]+'</span>');

                                $('#wrap_sched_affected_details').show();
                            }

                        }
                    }else{
                        $('#sched_affected_details').html("No Schedule");
                        $('#wrap_sched_affected_details').show();
                    }
                   }
                });
        }
    }else{
        $("#datesettopicker").show();
        $("#datetotext").show();
        $('#wrap_sched_affected_details').hide();
        var start = $("input[name='datesetfrom_d']").val();
        var end = $("input[name='datesetto_d']").val();
        countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
    }
}



function countDaysWithinSchedule(start, end){
    // $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
       type     :   "POST",
       data     :   {
                        toks:toks,
                        start : GibberishAES.enc(start, toks), 
                        end : GibberishAES.enc(end, toks)

                        // added by justin (with e) for ica-hyperion 21194
                        <?if($isAdmin){?>
                        , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                        <?}?>
                        // end for ica-hyperion 21194
                    },
       success  :   function(days){
        if("<?=$paid?>" == "NO") days = 0; 
        $("input[name='ndays_d']").val(days);
        // $("#loadingdays").hide();
       }
    });
}

function countDaysWithinScheduleHalfday(start, end){
    var scheddays = 0;
    // var days = 0.5;        
    var days = ("<?=$nodays?>" != '' ? "<?=$nodays?>" : 0.5);
    if("<?=$paid?>" == "NO") days = 0;                                      
    // $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
       type     :   "POST",
       data     :   {
                        toks:toks,
                        start : GibberishAES.enc(start, toks), 
                        end : GibberishAES.enc(end, toks),
                        fordetails : GibberishAES.enc("1", toks),
                        withpay : GibberishAES.enc($("#lwithpay_d").val(), toks),
                        empID : GibberishAES.enc("<?=$employeeid?>", toks)
                    },
                    // async: false,
       success  :   function(nodays){
            scheddays = nodays;
            // days = scheddays;
            // console.log("<?=$paid;?>");
            // console.log($("input[name='ndays_d']").val());
            $("input[name='ndays_d']").val(days);
       }
    });

    // return scheddays;
}

function getSeminar(code){
    $.ajax({
        url : "<?= site_url('extensions_/showreportseduclevel') ?>",
        type: "POST",
        data: {toks:toks,code: GibberishAES.enc(code, toks), idkey: GibberishAES.enc("<?= isset($seminar) ? $seminar : '' ?>", toks)},
        success:function(response){
            $("select[name='seminar']").html(response).trigger("chosen:updated");
        }
    })
}

function canApplyWithPay(){
    var empid = "<?= $empID ?>";
    var leave_balance_days = 0;
    <?if($isAdmin):?> leave_balance_days = validateDays($("select[name='employee']").val(), $("select[name='ltype_d']").val()); <?endif;?>
    <?if(!$isAdmin):?> leave_balance_days = validateDays(empid, $("select[name='ltype_d']").val()); <?endif;?>
    if(leave_balance_days >= 1) $('#lwithpay_d').val("YES").prop('disabled', true).trigger("liszt:updated");
}

$("#filename_det").click(function(){
        $("#filename_det").hide();
        $("#processing").show();
        $.ajax({
          url:"<?=site_url('leave_application_/getLeaveAttachments')?>",
          type: "POST",
          data:{toks:toks,base_id:GibberishAES.enc("<?=$base_id?>", toks)},
          dataType: "json",
          cache:false,
          async:false,
          success:function(response){

            $("#filename_det").attr("file", response.file);
            $("#filename_det").attr("mime", response.mime);
          }
        }).done(function(){
             if($("#filename_det").attr("file")){
                  var data = $("#filename_det").attr("file");
                  var mime = $("#filename_det").attr("mime");
                  var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
                  window.open(objectURL);
              }else{
                  var file_url = $("#filename_det").attr("content");
                  window.open(file_url);
              }
              $("#filename_det").show();
              $("#processing").hide(); 
        });
       /* setTimeout(function(){ 
          if($("#filename").attr("file")){
              var data = $("#filename").attr("file");
              var mime = $("#filename").attr("mime");
              var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
              window.open(objectURL);
          }else{
              var file_url = $("#filename").attr("content");
              window.open(file_url);
          }
          $("#filename").show();
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

    function countDays(start, end){
        if(start && end){
            $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
            $.ajax({
               url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
               type     :   "POST",
               data     :   {
                                alldays : true, 
                                toks:toks,
                                start : GibberishAES.enc(start, toks), 
                                end : GibberishAES.enc(end, toks)

                                // added by justin (with e) for ica-hyperion 21194
                                <?if($isAdmin){?>
                                , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                                <?}?>
                                // end for ica-hyperion 21194
                            },
               success  :   function(days){
                if(!$('input[name=ishalfday_d]').is(":checked")) $("#nodays_d").val(days);
                $("#loadingdays").hide();
               }
            });
        }
    }


</script>