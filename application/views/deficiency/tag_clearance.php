<?php
    $CI =& get_instance();
    $CI->load->model('deficiency');
    $lookForList = $codeList = array();
	$datetoday = date("Y-m-d");
	$timetoday = "";
	// $departments = $this->extras->showoffice("Select office ...");
    $departments = $CI->deficiency->deptDeficiency();
    // $deficiencies = $CI->deficiency->getDeficiencyTypes('','',$concerneddept);
    $office = $this->extensions->getAllOfficeUnder($this->session->userdata("username"));
    $office = implode(',', $office);
    $employee = $this->employee->loadallemployee("","","","", false, "", "","all","", $office);
    $employee = Globals::resultarray_XHEP($employee);
    $yearToday = (int)date('Y');
    $headCol = array("divisionhead","head");
    $utype = $this->session->userdata('usertype');
	
?>
<style>
input[name=isCompleted]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}
</style>
<div class="widgets_area">
    <a href="#" class="btn btn-success" name='backlist' style="margin-bottom: 20px;">Back to employee list</a>
    <div class="row">  
        <div class="col-md-12">
            <div class="panel">
                <form id="formDeficiency">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Clearance</b></h4></div>
                    <div class="panel-body">
                        <div class="form_row">
                            <label class="field_name align_right"><b>Employee</b></label>
                            <div class="field" id='dept'>
                                <div class="col-md-6" style="height: 34px;">
                                    <select class="chosen" id="employee" name="employee" multiple style="width: 66%;" >
                                        <?php
                                            foreach ($employee as $value) { ?>
                                                <option value="<?php echo  $value['employeeid'] ?>"><?=($value['lname'] . ", " . $value['fname'] . ($value['mname']!="" ? " " . substr($value['mname'],0,1) . "." : ""))?></option>
                                                <?php
                                            }
                                        ?> 
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <label class="field_name align_right"><b>Concerned Office</b></label>
                            <div class="field" id='dept'>
                                <div class="col-md-6" style="height: 34px;">
                                <select class="chosen" id="departments" name="departments" style="width: 66%;" >
                                    <option value="">Select Office</option>
                                    <?php
                                        foreach ($departments as $value) { 
                                            if(in_array($value['code'], $this->extensions->getAllOfficeUnder($this->session->userdata("username"))) && $utype == "EMPLOYEE"){ ?>
                                            <option value="<?php echo  $value['code'] ?>"><?php echo  $value['description'] ?></option>
                                            
                                        <?php
                                            $codeList[$value['code']] = $value['code'];
                                     }else if($utype == "ADMIN"){ ?>
                                        <option value="<?php echo  $value['code'] ?>"><?php echo  $value['description'] ?></option>
                                            
                                        <?php
                                            $codeList[$value['code']] = $value['code'];
                                         
                                     }
                                 }
                                    ?>
                                </select>
                            
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <label class="field_name align_right"><b>Type of Clearance</b></label>
                            <div class="field" id='typedeficiencies'>
                                <div class="col-md-6">
                                 <select class="chosen" id="deficiencies" name="deficiencies" multiple style="width: 66%;">
                                    <option value="">Select Clearance</option>
                                    <? if($concerneddept){ foreach( $deficiencies as $each ): ?>
                                        <option value="<?=$each->id;?>"><?=Globals::_e($each->description);?></option>
                                    <? endforeach; } ?>
                                </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <label class="field_name align_right"><b>School Year</b></label>
                            <div class="field" id='schoolYear'>
                                <div class="col-md-6">
                                 <select class="chosen" id="sySelect" name="sySelect" style="width: 66%;">
                                    <?php
                                        for ($i=0; $i <= 10; $i++) { 
                                            ?>
                                                <option value="<?= $yearToday ?>"><?= $yearToday ?></option>
                                            <?php
                                            $yearToday = $yearToday-1;
                                        }
                                    ?>
                                </select>
                                </div>
                            </div>
                        </div>
                        <?php
                            $headList = $this->extensions->getApproverList($codeList);
                            if($headList){
                                foreach ($headList as $key => $value) {
                                    foreach ($headCol as $col) {
                                        if(!in_array($value[$col], $lookForList) && $this->extensions->getEmployeeName($value[$col])) $lookForList[$value[$col]] = array("empname" => $this->extensions->getEmployeeName($value[$col]), "code" => $value['code']);
                                    }
                                }
                            }
                        ?>
                        <div class="form_row">
                            <label class="field_name align_right"><b>Look For</b></label>
                            <div class="field" id="lookF">
                                <div class="col-md-6">
                                    <select class="chosen" id="lookfor" name="lookfor" style="width: 66%;">
                                        <option value="">Select Person to Look for</option>
                                        <?php
                                            foreach ($lookForList as $key => $value) {
                                                ?>
                                                    <option value="<?php echo  $key ?>" code_tag = <?php echo $value['code']?>><?php echo  $value['empname'] ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <label class="field_name align_right"><b>Remarks</b></label>
                            <div class="field" id="remarksF">
                                <div class="col-md-6">
                                <textarea rows="4" class="form-control isreq" name="remarks" id="remarks"  placeholder="Remarks"></textarea>
                                </div><br><br>
                    
                        </div>
                        </div><br>
                        <div class="form_row">
                        <label class="field_name align_right"></label>
                            <div class="field">
                                <div class="col-md-12">
                                    <div class="col-md-2" style="padding-left: 0px;width: auto; margin-right: -60px;">
                                        <label>Completed&nbsp;&nbsp;&nbsp;</label>
                                        <input type="checkbox" name="isCompleted" id="isCompleted" value="1"/>
                                    </div>
                                    <div class="col-md-1 " style="padding-left: 0px;width: 5%;">
                                    </div>
                                    <div class="col-md-2 date_completed" style="padding-left: 0px;width: auto; ">
                                        <label>&nbsp;&nbsp;&nbsp;Date Completed</label>
                                    </div>
                                    <div class="col-md-4 date_completed" style="width: 25%;">
                                        <div class='input-group date' id="datecompleted" data-date-format="yyyy-mm-dd">
                                            <input type='text' class="form-control" size="16" name="datecompleted" id="dcompleted" type="text" value=""/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form_row" >
                            <label class="field_name align_right">Deadline of Submission</label>
                            <div class="field">
                                <div class="col-md-3" >
                                    <div class='input-group date' id="datesub" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="datesub" id="dsub" type="text" value=""/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form_row align_right" style="margin-right: 2%; margin-bottom: 20px;">
                            <div id="loading" hidden=""></div>
                            <div id="saving">
                                <button type="button" id="save" action="add" class="btn btn-primary">&emsp;Save&emsp;</button>
                                <button type="button" id="cancelEdit" action="cancelEdit" class="btn btn-danger">Cancel</button>
                                <button type="button" id="edit" action="edit" class="btn btn-success">Save</button>
                                &nbsp;&nbsp;
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
       
<script>
var toks = hex_sha512(" ");
// if("<?=$this->session->userdata('canwrite')?>" == 0) $("#formDeficiency").css("pointer-events", "none");
// else $("#formDeficiency").css("pointer-events", "");
$(document).ready(function(){  
    
    checkCompleted();
    $("#edit, #cancelEdit").hide();
});



      
$("#save").click(function(){
    var iscontinue  = true;
    /*var start   = new Date($("#dfrom").val()),
        end   = new Date($("#dto").val()),
        diff  = new Date(end - start),
        days  = diff/1000/60/60/24;
    if(days < 0) return false;*/

     $("#formDeficiency .isreq").each(function(){
        if($(this).val() == ""){
            // $("#lookforAlert").show();
            iscontinue = false;
        }
        else
        {
             $(this).css("border-color","");

        }
    });
    if($('#isCompleted').is(':checked'))
    {
        if($("#dcompleted").val() == "")
        {

             $("#dcompleted").css("border-color","red").attr("placeholder", "This field is required.");  
            iscontinue = false;
        }
    }
    else
    {
        $("#dcompleted").css("border-color","").attr("placeholder", "");  
    }
    $( "#lookforAlert" ).remove();
    if($('#lookfor').val() == '')
    {
        $( "<label id='lookforAlert' style='color:red;'><b>This field is required.</b></label>" ).insertAfter( "#lookF" );
        iscontinue = false;
    }
    $( "#remarksAlert" ).remove();
    if($('#remarks').val() == '')
    {
        $( "<label id='remarksAlert' style='color:red;'><b>This field is required.</b></label>" ).insertAfter( "#remarksF" );
        iscontinue = false;
    }
    
    $( "#deptAlert" ).remove();
    if($('#departments').val() == '')
    {
        $( "<label id='deptAlert' style='color:red;'><b>This field is required.</b></label>" ).insertAfter( "#dept" );
        iscontinue = false;
    }
    
    $( "#deficienciesAlert" ).remove();
    if($('#deficiencies').val() == '')
    {
        $( "<label id='deficienciesAlert' style='color:red;'><b>This field is required.</b></label>" ).insertAfter( "#typedeficiencies" );
        iscontinue = false;
    }

    // return false;
    if(!iscontinue)  return false;
    else{
        var isCompleted = "0";
        if($("input[name='isCompleted']").is(":checked")) isCompleted = "1";
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

         $.ajax({
           url      :   "<?=site_url("deficiency_/saveEmployeeDeficiency")?>",
           type     :   "POST",
           dataType :   "json",
           data     :   {
                departments: GibberishAES.enc($("select[name='departments']").val(), toks),
                lookfor: GibberishAES.enc($("select[name='lookfor']").val(), toks),
                deficiencies: GibberishAES.enc($("select[name='deficiencies']").val(), toks),
                remarks: GibberishAES.enc($("#remarks").val(), toks),
                datesub: GibberishAES.enc($("input[name='datesub']").val(), toks),
                isCompleted: GibberishAES.enc(isCompleted, toks),
                datecompleted: GibberishAES.enc($("input[name='datecompleted']").val(), toks),
                sySelect: GibberishAES.enc($("select[name='sySelect']").val(), toks),
                toks:toks,
                employeeid:GibberishAES.enc($("select[name='employee']").val(), toks)
            },
           success  :   function(msg){
                Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: msg.msg,
                showConfirmButton: true,
                timer: 1500
            })
                location.reload();
            // alert(msg.msg);
            $("#saving").show();
            $("#loading").hide();
            
           },
           error : function(XMLHttpRequest, textStatus, errorThrown) { 
                console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
            }      
        });

        clearEntries();

    }
    
});

$("#edit").click(function(){
    var iscontinue  = true;
    var idkey = $(this).attr('idkey');

    $("#formDeficiency .isreq").each(function(){
        if($(this).val() == ""){
            $(this).css("border-color","red").attr("placeholder", "This field is required.");  
            iscontinue = false;
        }
        else
        {
             $(this).css("border-color","");  
        }
    });
    if($('#isCompleted').is(':checked'))
    {
        if($("#dcompleted").val() == "")
        {
             $("#dcompleted").css("border-color","red").attr("placeholder", "This field is required.");  
            iscontinue = false;
        }
    }
    else
    {
        $("#dcompleted").css("border-color","").attr("placeholder", "");  
    }
    $( "#lookforAlert" ).remove();
    if($('#lookfor').val() == '')
    {
        $( "<label id='lookforAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( "#lookF" );
        iscontinue = false;
    }
    $( "#remarksAlert" ).remove();
    if($('#remarks').val() == '')
    {
        $( "<label id='remarksAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( "#remarksF" );
        iscontinue = false;
    }
    
    $( "#deptAlert" ).remove();
    if($('#departments').val() == '')
    {
        $( "<label id='deptAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( "#dept" );
        iscontinue = false;
    }
    
    $( "#deficienciesAlert" ).remove();
    if($('#deficiencies').val() == '')
    {
        $( "<label id='deficienciesAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( "#typedeficiencies" );
        iscontinue = false;
    }

    if(!iscontinue)  return false;
    else{
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

        var form_data   =   $("#formDeficiency").serialize();
            form_data   += "&employeeid="+$("select[name='employee']").val();
            form_data   += "&def_id="+idkey;
         var isCompleted = "0";
         if($("input[name='isCompleted']").is(":checked")) isCompleted = "1";
         $.ajax({
           url      :   "<?=site_url("deficiency_/saveEmployeeDeficiency")?>",
           type     :   "POST",
           dataType :   "json",
           data     :   {
                def_id: GibberishAES.enc(idkey, toks),
                departments: GibberishAES.enc($("select[name='departments']").val(), toks),
                lookfor: GibberishAES.enc($("select[name='lookfor']").val(), toks),
                deficiencies: GibberishAES.enc($("select[name='deficiencies']").val(), toks),
                remarks: GibberishAES.enc($("#remarks").val(), toks),
                datesub: GibberishAES.enc($("input[name='datesub']").val(), toks),
                isCompleted: GibberishAES.enc(isCompleted, toks),
                datecompleted: GibberishAES.enc($("input[name='datecompleted']").val(), toks),
                sySelect: GibberishAES.enc($("select[name='sySelect']").val(), toks),
                toks:toks,
                employeeid:GibberishAES.enc($("select[name='employee']").val(), toks)
            },
           success  :   function(msg){
             Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg.msg,
            showConfirmButton: true,
            timer: 1500
        })
            // alert(msg.msg);
            $("#saving").show();
            $("#loading").hide();
            
           },
           error : function(XMLHttpRequest, textStatus, errorThrown) { 
                console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
            }      
        });

    }
    $("#save").show();
    $("#edit, #cancelEdit").hide();
    clearEntries();
    
});

$("#cancelEdit").click(function(){
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   });

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to cancel?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
        $("#save").show();
        $("#edit, #cancelEdit").hide();
        clearEntries();
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Data is safe.',
         'error'
       )
     }
   });
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

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$("a[name='backlist']").click(function(){
    location.reload();
});

/*
 *  FUNCTIONS
 */

function loadDeficiencyHistory(employeeid){
   // $("#a_history").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $("#a_history").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>"); 
   $.ajax({
      url      :   "<?=site_url("deficiency_/loadDeficiencyHistory")?>",
      type     :   "POST",
      data     :   {employeeid :  GibberishAES.enc(employeeid , toks), toks:toks},
      success  :   function(msg){
       $("#a_history").html(msg);
       if("<?=$this->session->userdata('canwrite')?>" == 0) $("#a_history").find(".btn").css("pointer-events", "none");
       else $("#a_history").find(".btn").css("pointer-events", "");
      }
   });
}
function clearEntries(){
    if($("#deptexist").val()) $('#deficiencies, #remarks, #lookfor ').val("").trigger("chosen:updated");
    else $('#departments, #deficiencies, #remarks, #lookfor ').val("").trigger("chosen:updated");
    $('#remarks').html('');
    $('#isCompleted').removeAttr('checked');
    $('#dsub').val('<?=$datetoday?>');
    $(".date_completed").hide();
    $('#sySelect').val('<?=$yearToday?>').trigger("chosen:updated");
}

$("input[name='isCompleted']").click(function(){
    checkCompleted();
})

function checkCompleted(){
    if($("input[name='isCompleted']").is(":checked")) $(".date_completed").show();
    else $(".date_completed").hide();
}

$("#departments").change(function(){
    loadDeficiencies($(this).val());
    lookforData($(this).val());
    
})

function loadDeficiencies(deptid, selected=''){
    $.ajax({
      url      :   "<?=site_url("deficiency_/getDeficiencytype")?>",
      type     :   "POST",
      data     :   {dept :GibberishAES.enc( deptid , toks), toks:toks},
      success  :   function(msg){
       $("#deficiencies").html(msg).trigger("chosen:updated");
       if(selected) $('#deficiencies').val(selected).trigger("chosen:updated");
       
      }
   });
}

function lookforData(deptid='', selected=''){
    $.ajax({
      url      :   "<?php echo site_url("deficiency_/getLookForList")?>",
      type     :   "POST",
      data     :   {dept :GibberishAES.enc(deptid  , toks), toks:toks},
      success  :   function(msg){
       $("#lookfor").html(msg).trigger("chosen:updated");
       if(selected) $('#lookfor').val(selected).trigger("chosen:updated");
      }
   });
}

$(".chosen").chosen();
</script>