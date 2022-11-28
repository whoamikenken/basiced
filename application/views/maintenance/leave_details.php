<?php

/**
 * @author Justin
 * @2016
 */
/**
 * @Kennedy Hipolitp
 * @2019
 * @Updated UI
 */
    $id = 0;
    $readonly = false;
    $description = "";
    $details = "";
    $credits = "";
    $genderApplicable = "";
    $up = $upt = $boff = $findir = $pres = "";
    $q = $w = $e = $cp = $dp = $r = $t = $y = $c = $cp = $bo ="";
    $mngt = 1;
    if($code){
        $sql = $this->db->query("select * from code_request_form WHERE code_request='{$code}'");
        if($sql->num_rows()>0){
            $id = $sql->row(0)->id;
            $description = $sql->row(0)->description;
            $details = $sql->row(0)->details;
            $credits = $sql->row(0)->credits;
            $up = $sql->row(0)->univphy;
            $upt = $sql->row(0)->univphyt;
            $boff = $sql->row(0)->budgetoff;
            $findir = $sql->row(0)->financedir;
            $pres = $sql->row(0)->president;
            $q = $sql->row()->dhseq;
            $w = $sql->row()->hhseq;
            $c = $sql->row()->chseq;
            $cp = $sql->row()->cpseq;
            $dp = $sql->row()->dpseq;
            $e = $sql->row()->upseq;
            $bo = $sql->row()->boseq;
            $t = $sql->row()->fdseq;
            $y = $sql->row()->pseq;

            $genderApplicable = $sql->row()->genderApplicable;
        }
        $readonly = " readonly";
    }
    $cdate = date("Y-m-d");
    // echo "<pre>";print_r($this->db->last_query());die;
?>
<style>
    #form_leave .form-control{
        width: 89%!important;
    }
    .error{
        color:red;
    }
</style>
<div class="container" style="width: 100%;">
    <form id="form_leave">
        <div class="form-group">
            <label class="field_name align_right">Code</label>
            <div class="field">
                <input class="form-control" id="mh_code" name="mh_code" type="text" value="<?=$code?>" <?=$readonly?>/>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Description</label>
            <div class="field">
                <input class="form-control" id="mh_description" name="mh_description" type="text" value="<?=$description?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Details</label>
            <div class="field">
                <input class="form-control" id="mh_details" name="mh_details" type="text" value="<?=$details?>"/>
            </div>
        </div>
        <div class="form-group" hidden>
            <label class="field_name align_right">Leave Credits</label>
            <div class="field">
                <input class="form-control" id="mh_credits" name="mh_credits" type="number" value="<?=$credits?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Applicable Gender</label>
            <div class="field" style="width:89%;">
                <select class="chosen" name="mh_gender" id="mh_gender" >
                    <option value="all">- All -</option>
                    <?
                    $genderList = array("m"=>"Male","f"=>"Female");
                    foreach($genderList as $r => $v){
                    ?>
                    <option value="<?=$r?>" <?=$r==$genderApplicable?"selected":""?> id="genderApplicable"><?=$v?></option>
                    <?
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Leave Start Period</label>
            <div class="field">
                <table id="eligibilityPeriod">
                    <tr>
                        <td></td>
                        <!-- <td></td> -->
                        <!-- <td></td> -->
                        <td></td>
                        <td>Teaching</td>
                        <td>Non</td>
                    </tr>
                    <?
                    $code_status = $this->db->query("SELECT * FROM code_status ORDER BY seqno");
                    if($code_status->num_rows()){
                        foreach($code_status->result() as $cs){
                            ?>
                            <tr code="<?=$cs->code?>">
                            <td width="20%" style="text-align:center;"><?=$cs->description?></td>
                                <!-- <td width="10%"> -->
                                    <?
                                    $count_mode = 0;
                                    $mode = "day";
                                    $leave_creadit = 0;
                                    $leave_creadit_non=0;
                                    if($code){
                                        $sql = $this->db->query("SELECT * FROM code_request_eligibility_period WHERE code_request='".$code."' AND emp_status='".$cs->code."'");

                                        $count_mode = isset($sql->row()->count)?$sql->row()->count:0;
                                        $mode = isset($sql->row()->mode)?$sql->row()->mode:"day";
                                        $leave_creadit = isset($sql->row()->credit)?$sql->row()->credit:0;
                                        $leave_creadit_non = isset($sql->row()->credit_non)?$sql->row()->credit_non:0;
                                    }
                                    ?> 
                                    <!-- <input type="text" class="form-control" value="<?=$count_mode?>"/ onkeypress="return numbersonly(event)" maxlength="2" > -->
                                <!-- </td> -->
                                <!-- <td width="20%">
                                    <select class="chosen" value="<?=$count_mode?>"> -->
                                        <?
                                        // $list = array("day"=>"Day","month"=>"Month","year"=>"Year");
                                        // foreach($list as $k => $v){
                                        ?>
                                            <!-- <option value="<?=$k?>" <?=$k==$mode?"selected":""?> ><?=$v?></option> -->
                                        <?
                                        // }
                                        ?>
                                   <!--  <select>
                                </td> -->
                                <td width="10%">
                                    <label class="field_name ">Leave Credits</label>
                                </td>
                                <td width="10%">
                                    <input class="form-control" type="text" value="<?=$leave_creadit?>"/ onkeypress="return numbersonly(event)">
                                </td>
                                <td width="10%">
                                    <input class="form-control" type="text" value="<?=$leave_creadit_non?>"/ onkeypress="return numbersonly(event)">
                                </td>
                            </tr>
                            <?
                        }
                    }

                    ?>
                </table>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right"><strong>Approved By:</strong></label>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Area coordinator / Immediate Supervisor</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="chseq"  value="<?=$c?>" onkeypress="return numbersonly(event)" maxlength="1"/>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Department Head / Vice Principal</label>
            <div class="field">
                <input type="text" class="form-control seqinput"  id="dhseq"  value="<?=$q?>"  <?=$code=='OBS'?$readonly:""?> onkeypress="return numbersonly(event)" maxlength="1" min='0'/>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">HR Director</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="hhseq"   value="<?=$w?>"  onkeypress="return numbersonly(event)" maxlength="1" min='0'/>
            </div>
        </div>
        <!-- <div class="form-group">
            <label class="field_name align_right">Campus Principal</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="cpseq"  value="<?=$cp?>" onkeypress="return numbersonly(event)" maxlength="1" />
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Department Principal</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="dpseq"  value="<?=$dp?>" onkeypress="return numbersonly(event)" maxlength="1" />
            </div>
        </div> -->
<!--         <div class="form-group">
            <label class="field_name align_right">Univ. Physician</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="upseq"  value="<?=$e?>" onkeypress="return numbersonly(event)" maxlength="1"/>
                <br>
                <div class="field" style="width:89%;">
                    <select class="chosen" name="mh_univphy" id="mh_univphy" ><?=$this->extras->showEmployee($up);?></select>
                </div>
            </div>
            <br>
            <div class="field" style="margin-top: 5px;">
                <div class="field" style="width:89%;">
                    <select class="chosen" name="mh_univphytwo" id="mh_univphytwo" ><?=$this->extras->showEmployee($upt);?></select>
                </div>
            </div>
        </div><br/>
        <div class="form-group">
            <label class="field_name align_right">Budget Officer</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="boseq"  value="<?=$bo?$bo:""?>" onkeypress="return numbersonly(event)" maxlength="1"/>
                <br>
                <div class="field" style="width:89%;">
                    <select class="chosen" name="mh_boff" id="mh_boff"><?=$this->extras->showEmployee($boff);?></select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Finance Director</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="fdseq"  onkeypress="return numbersonly(event)" maxlength="1" value="<?=$t?>" />
                <br>
                <div class="field" style="width:89%;">
                    <select class="chosen" name="mh_findir" id="mh_findir"><?=$this->extras->showEmployee($findir);?></select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">President</label>
            <div class="field">
                <input type="text" class="form-control seqinput" id="pseq"  value="<?=$y?>" onkeypress="return numbersonly(event)" />
                <br>
                <div class="field" style="width:89%;">
                    <select class="chosen" name="mh_president" id="mh_president"><?=$this->extras->showEmployee($pres);?></select>
                </div>
            </div>
        </div> -->
        <div class="form-group">
            <label class="field_name align_right">Management : </label>
            <div class="field" style="width:89%;">
                <select class="chosen" style="width: 50%;" name="mngt" id="mngt" >
                    <option value="1" <?=($mngt == 1)?"selected":""?>>Main Leave Type</option>
                    <option value="0" <?=($mngt == 0)?"selected":""?>>Other Leave Type</option>
                </select>
            </div>
        </div>
    </form>
</div>
<script>
var toks = hex_sha512(" ");

$(".seqinput").blur(function(){
    $(this).removeClass("seqinput");
    var curr_count = 0;
    var input_seq = parseInt($(this).val());
    $(".seqinput").each(function(){
        var seq_count = parseInt($(this).val());
        if(seq_count){
            if(parseInt(seq_count) > curr_count) curr_count = parseInt(seq_count);
        }
    });
    $(this).addClass("seqinput");
    if(curr_count+1 > 1){
        if(curr_count + 1 != input_seq){
            $(this).val("");
        }
    }
    if(curr_count == 0 && $(this).val() != 1){
        $(this).val("");
    }
});

$("#dhseq,#hhseq,#chseq,#cpseq,#dpseq,#upseq,#boseq,#fdseq,#pseq").on("blur",function(){

    if ($(this).val() == "") {
        $(this).val("0");
    }
});


var a = ["dhseq","hhseq","chseq"];

$("#dhseq,#hhseq").unbind('input').on("input",function(){ 
    var id = $(this).attr('id');
    if (a.indexOf(id) > -1) {
        a.splice(a.indexOf(id),1);
    }
    var value = $(this).val();
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{

        jQuery.each(a,function(i,v){
            // alert(value);
            if ($("#"+v).val() == value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please correct the sequence of the approver.',
                    showConfirmButton: true,
                    timer: 2000
                });
            }

        });

    }

});

$("#hhseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#chseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#cpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#dpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#upseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#boseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#fdseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#pseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }

});

$("#chseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#hhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#cpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#dpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#upseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#boseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#fdseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#pseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }

});

$("#cpseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#hhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#chseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#dpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#upseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#boseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#fdseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#pseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }
});

$("#dpseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#hhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#chseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#cpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#upseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#boseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#fdseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#pseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }
});

$("#upseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#hhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#chseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#cpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#dpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#boseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#fdseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#pseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }
});

$("#boseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#hhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#chseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#cpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#dpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#upseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#fdseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#pseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }
});
$("#fdseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#hhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#chseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#cpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#dpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#upseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#boseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#pseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }
});

$("#pseq").on("input",function(){
    if ($(this).val() == 9) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else  if ($(this).val() == 0) {
        $(this).val($(this).val().replace($(this).val(),"0"));
    }
    else{
        if ($("#dhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#hhseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#chseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#cpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#dpseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#upseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#boseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
        else if ($("#fdseq").val() == $(this).val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please correct the sequence of the approver.',
                showConfirmButton: true,
                timer: 2000
            });
            $(this).val($(this).val().replace($(this).val(),"0"));
        }
    }

});

$("#button_save_modal").unbind("click").click(function(){
    $.ajax({
        url      :   "<?=site_url("maintenance_/leavedatevalidity")?>",
        type     :   "POST",
        data     :   {
        toks: toks,
        dfrom: GibberishAES.enc($("input[name='datefrom']").val(), toks),
        dto: GibberishAES.enc($("input[name='dateto']").val(), toks),
        ltype: GibberishAES.enc($("#mh_type").val(), toks)
        },
        success  :   function(msg){
        if(msg == "0"){
            if($("#form_leave").valid()){

                var eligibilityPeriod = "";
                $('#eligibilityPeriod tr').each(function() {
                    eligibilityPeriod += eligibilityPeriod?"|":"";
                    eligibilityPeriod += GibberishAES.enc($(this).attr("code"), toks);
                    // eligibilityPeriod += "~u~";
                    // eligibilityPeriod += GibberishAES.enc($(this).find('td:eq(1)').find("input").val(), toks); 
                    // eligibilityPeriod += "~u~";
                    // eligibilityPeriod += GibberishAES.enc($(this).find('td:eq(2)').find("select").find(":selected").val(), toks); 
                    eligibilityPeriod += "~u~";
                    eligibilityPeriod += GibberishAES.enc($(this).find('td:eq(4)').find("input").val(), toks); 
                    eligibilityPeriod += "~u~";
                    eligibilityPeriod += GibberishAES.enc($(this).find('td:eq(5)').find("input").val(), toks); 
                    });
                    var gender = $("#mh_gender").val();
                    if(gender == "all"){
                        gender = "all";
                    }
                    $.ajax({
                        url:"<?=site_url("maintenance_/save_leave")?>",
                        type:"POST",
                        data:{
                            toks: toks,
                            id:GibberishAES.enc("<?=$id?>", toks),
                            code: GibberishAES.enc($("input[name='mh_code']").val(), toks),
                            description: GibberishAES.enc($("input[name='mh_description']").val(), toks),
                            details: GibberishAES.enc($("input[name='mh_details']").val(), toks),
                            credits: GibberishAES.enc($("input[name='mh_credits']").val(), toks),
                            /*
                            dfrom: $("input[name='datefrom']").val(),
                            dto: $("input[name='dateto']").val(),
                            ltype: $("#mh_type").val()
                            */
                            genderApplicable : GibberishAES.enc(gender, toks),
                            eligibilityPeriod : eligibilityPeriod,

                            dhseq: GibberishAES.enc($("#dhseq").val(), toks),
                            hhseq: GibberishAES.enc($("#hhseq").val(), toks),
                            chseq: GibberishAES.enc($("#chseq").val(), toks),
                            cpseq: GibberishAES.enc($("#cpseq").val(), toks),
                            dpseq: GibberishAES.enc($("#dpseq").val(), toks),
                            upseq: GibberishAES.enc($("#upseq").val(), toks),
                            boseq: GibberishAES.enc($("#boseq").val(), toks),
                            fdseq: GibberishAES.enc($("#fdseq").val(), toks),
                            pseq : GibberishAES.enc($("#pseq").val(), toks),
                            bo   : GibberishAES.enc($("#mh_boff").val(), toks),
                            up   : GibberishAES.enc($("#mh_univphy").val(), toks),
                            upt  : GibberishAES.enc($("#mh_univphytwo").val(), toks),
                            fd   : GibberishAES.enc($("#mh_findir").val(), toks),
                            pres : GibberishAES.enc($("#mh_president").val(), toks),
                            mngt : GibberishAES.enc($("#mngt").val(), toks)
                        },
                        dataType: "json",
                        success: function(response){
                            if(response.code == 1){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.msg,
                                    showConfirmButton: true,
                                    timer: 2000
                                });
                                setTimeout(function(){ 
                                    $("#modalclose").click();
                                    location.reload(); 
                                }, 3000);
                            }else{
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning!',
                                    text: response.msg,
                                    showConfirmButton: true,
                                    timer: 2000
                                });
                            }
                        }
                    });
                }else {
                    $validator.focusInvalid();
                    return false;
                }
            }else{
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'The date you set has a conflict.',
                    showConfirmButton: true,
                    timer: 2000
                });
                return false;
            }
        }
    });
});
/*$("#datefrom,#dateto").datepicker({
autoclose: true,
todayBtn : true
});
$(".chosen").chosen();*/

function numbersonly(evt, myfield, e, dec, id){ ///< edited for cross-browser compatibility
    var key;
    var keychar;
    var e = evt || window.event;
    if (e)         key = e.which || e.keyCode;
    // else if (window.event)   key = window.event.keyCode;
    // else                return true;
    keychar = String.fromCharCode(key);

    // control keys
    if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;

    // numbers
    else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;

    // decimal point jump
    // else if (dec && (keychar == "."))
    // {
    //     myfield.form.elements[dec].focus();
    //     return false;
    // }
    else    return false;
}

$(".chosen").chosen();
</script>