<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
$cdatefrom = date("Y-m-d");
$cdateto = date("Y-m-d");


?>
<style>
.input { font-size:16px; border-color:#cccccc; border-style:solid; padding:9px; border-width:3px; border-radius:12px; text-align: center; font-weight: bolder; } 
.input:focus { outline:none; } 
/*
table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc, table.dataTable thead .sorting_asc_disabled, table.dataTable thead .sorting_desc_disabled {
     position: unset;
}*/
.col-sm-12{
  position: unset;
}
.col-sm-6 {
    position: unset;
}

.panel-body{
    margin-top: 30px;
    margin-bottom: 20px;
}

.form_row{
    padding-bottom: 10px;
}
     .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
    <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
               <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b> Employee Profile Config</b></h4></div>
                   <div class="panel-body">
                        <form id="frmeconfig">
                        <div class="container">
                            <div class="row">
                                <div class="form_row">
                                    <label class="field_name align_right">Date From</label>
                                    <div class="field" style="width: 90%;">
                                        <div class="col-md-4" style="padding-right: 25px;">
                                            <div class='input-group date' id='datetimepicker1'>
                                                <input type='text' class="form-control" name="datefrom"  value="<?= $cdatefrom ?>" />
                                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="padding: 0px; width: 0px; margin-left: -8px;"><label class="field_name align_center">To</label></div>
                                        <div class="col-md-4" style="padding-left: 25px;">
                                            <div class='input-group date' id='datetimepicker2'>
                                                <input type='text' class="form-control" name="dateto" value="<?= date("Y-m-d",strtotime($cdateto)) ?>" />
                                                <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_row">
                                    <label class="field_name align_right">Type</label>
                                    <div class="field">
                                        <div class="col-md-9 no-search">
                                            <select class="chosen " id="tnt" name="tnt">
                                                <? $type=array("teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                                foreach($type as $c=>$val){ ?>
                                                <option value="<?=$c?>">
                                                    <?=$val?>
                                                </option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_row" id="estat">
                                    <label class="field_name align_right">Employment Status</label>
                                    <div class="field">
                                        <div class="col-md-9">
                                            <select class="chosen" name="estatus" id="estatus">
                                                <option value="">All Status</option>
                                                <? $opt_status=$this->extras->showStatus(); foreach($opt_status->result() as $row){ ?>
                                                <option
                                                value="<?=$row->code?>">
                                                    <?=$row->description?></option>
                                                        <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_row" id="edept">
                                    <label class="field_name align_right">Department</label>
                                    <div class="field">
                                        <div class="col-md-9">
                                            <select class="chosen col-md-6" name="deptid">
                                                <option value="">All Department</option>
                                                <? $opt_department=$this->extras->showdepartment(); foreach($opt_department as $c=>$val){
                                                ?>
                                                <option value="<?=$c?>">
                                                    <?=$val?>
                                                </option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_row" id="empTeaching">
                                    <label class="field_name align_right">Employee</label>
                                    <div class="field">
                                        <div class="col-md-9">
                                            <? $opt_type=$this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true,'teaching'); ?>
                                            <select class="chosen " name="employeeid">
                                                <option value="">All Employee</option>
                                                <? foreach($opt_type as $val){ ?>
                                                <option value="<?=$val['employeeid']?>">
                                                    <?=($val[ 'employeeid'] . " - " . $val[ 'lname'] . ", " . $val[
                                                    'fname'] . " " . $val[ 'mname'])?>
                                                </option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
<!--                                 <div class="form_row" id="empNonTeaching" hidden>
                                    <label class="field_name align_right">Employee</label>
                                    <div class="field">
                                        <div class="col-md-9">
                                            <?php $opt_type=$this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true,'nonteaching'); ?>
                                            <select class="chosen " name="employeeid">
                                                <option value="">All Employee</option>
                                                <?php foreach($opt_type as $val){ ?>
                                                <option value="<?=$val['employeeid']?>">
                                                    <?=($val[ 'employeeid'] . " - " . $val[ 'lname'] . ", " . $val[
                                                    'fname'] . " " . $val[ 'mname'])?>
                                                </option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="form_row">
                                 <label class="field_name align_right"></label>
                                <div class="field">
                                    <div class="col-md-9">
                                    <div id="load" hidden></div><a href="#" class="btn btn-primary" id="savebtn">Save</a>
                                    </div>
                                </div>
                            </div>
                                  </div>
                                </div>
                                <br>
                            
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="position: unset !important;">
              <div id="removeAni" class="panel animated fadeIn delay-1s">
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b> Employee Access List</b></h4></div>
                       <div class="panel-body" id="tbodydata">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var toks = hex_sha512(" ");
validateCanWrite();
loadEmpData();
var dateToday = new Date();
$('#datetimepicker2').datetimepicker({

    format: 'YYYY-MM-DD',
    minDate: dateToday
});

$('#datetimepicker1').datetimepicker({
    format: 'YYYY-MM-DD',
    minDate: dateToday
});
$('.chosen').chosen();



  setTimeout(
      function() 
      {
        $("#removeAni").removeClass("animated fadeIn delay-1s");
      }, 2000);



function deleteRow(obj, eid = "", dfrom ="", dto =""){
    $.ajax({
    url     :   "<?=site_url("maintenance_/proconfig")?>",
    type    :   "POST",
    data    :   {eid : GibberishAES.enc(eid, toks), dfrom : GibberishAES.enc(dfrom, toks), dto : GibberishAES.enc(dto, toks), toks:toks},
    dataType: "JSON",
    success :   function(msg){
        // alert(msg);
        // loadeprofile();
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Employee Acces has been deleted successfully.',
          showConfirmButton: true,
          timer: 1000
      })
        loadEmpData();


    }
   });  
}

// $("#delbtn").click(function(){
//    eid   = $(this).attr("eid");
//    dfrom = $(this).attr("dfrom");
//    dto   = $(this).attr("dto");
//    var mtable = $("#EmpList").find("tbody");
//    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
//    $(this).parent().parent().remove();
//    $.ajax({
//     url     :   "<?=site_url("maintenance_/proconfig")?>",
//     type    :   "POST",
//     data    :   {eid : eid, dfrom : dfrom, dto : dto},
//     success :   function(msg){
//         // alert(msg);
//         // loadeprofile();
//     }
//    }); 
// });

$("#savebtn").click(function(){
  var datesetfrom = $("input[name='datefrom']").val();
    var datesetto = $("input[name='dateto']").val();
    if((datesetfrom == "") && (datesetto == "") || datesetfrom > datesetto){
           Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please fill a valid date',
            showConfirmButton: true,
            timer: 1000
          })
           return;

         }
         if(datesetfrom == ""){
           Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please fill a valid date',
            showConfirmButton: true,
            timer: 1000
          })
           return;

         }
var form_data = $("#frmeconfig").serialize();
$("#load").show().html("<td colspan='5' style='text-align: center'>Saving, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");
$("#savebtn").hide();
    $.ajax({
       url      :   "<?=site_url("configuration_/loadprofileconfig")?>",
       type     :   "POST",
       data     :   {formdata: GibberishAES.enc(form_data, toks), toks:toks},
       success  :   function(msg){
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: msg,
          showConfirmButton: true,
          timer: 1000
      })
        loadEmpData();
        $("#load").hide();
        $("#savebtn").show();
       }
    });
});

$("select[name='deptid']").change(function(){
  $.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: {
           deptid :  GibberishAES.enc($(this).val(), toks),
           etype:  GibberishAES.enc($("#tnt").val(), toks),
           estatus: GibberishAES.enc($("select[name='estatus']").val(), toks),
           toks:toks
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('chosen:updated');
        }
    });   
});

$("select[name='estatus']").change(function(){
    $.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: {
           estatus : GibberishAES.enc($(this).val(), toks),
           etype: GibberishAES.enc($("#tnt").val(), toks),
           deptid: GibberishAES.enc($("select[name='deptid']").val(), toks),
           toks:toks
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('chosen:updated');
        }
    });   
});

$("#tnt").change(function(){
   // if($(this).val() == "teaching"){
   //  $("#estat").hide();
   //  $("#edept").show();
   //  $("#empTeaching").show();
   //  $("#empNonTeaching").hide();
   //  loadempopt($(this).val());
   // }else if($(this).val() == "nonteaching"){
   //  $("#edept").hide();
   //  $("#estat").show();
   //  $("#empTeaching").hide();
   //  $("#empNonTeaching").show();
    loadempopt($(this).val())
   // }
});

function loadempopt(etype = ""){
    $.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: {
           estatus : GibberishAES.enc($("select[name='estatus']").val(), toks),
           etype: GibberishAES.enc(etype, toks),
           deptid:GibberishAES.enc($("select[name='deptid']").val(), toks),
           toks:toks
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('chosen:updated');
        }
    });   
}

function loadEmpData() {
    $.ajax({
        url: "<?=site_url("process_/loadRestrictionData")?>",
        type: "POST",
        success: function(msg) {
            $("#tbodydata").html(msg);
        }
    }); 
}

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");
}

</script> 