<?php

/**
 * @author Justin
 * @copyright 2015   
 */
 $income_base = "";
 $ishidden = $isdisabled = $isreadonly = "";
 $applicable_field = $educcbox = $eligcbox = $scttcbox = $wunrelatedcbox = $wrelatedcbox = $wothercbox = "";
 $usertype = $this->session->userdata("usertype");
 if($usertype == "EMPLOYEE"){
   $ishidden   = " hidden";
   $isdisabled = " disabled";
   $isreadonly = " style='pointer-events: none;'";
 }
 if(isset($empinfo)){
   $empdetails = $empinfo;    

 }else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0];
   $employeeid = $empdetails["employeeid"];
 }
 
 // $work_history = $this->db->query("SELECT date_from,date_to,position,company,address,contactnumber,salary from employee_work_history where employeeid='{$empdetails['employeeid']}'")->result();
 // $work_history_unrelated = $this->db->query("SELECT date_from,date_to,position,company,address,contactnumber,salary from employee_work_history_unrelated where employeeid='{$empdetails['employeeid']}'")->result();
 
 // /*$eligibilities = $this->db->query("select date_issued,description,affiliating_center,educ_level from employee_eligibilities where employeeid='{$empdetails['employeeid']}'")->result();*/

 // $affiliations = $this->db->query("SELECT position,organization,date_registration from employee_affiliations where employeeid='{$empdetails['employeeid']}'")->result();
 // $awards = $this->db->query("SELECT awards,date_given,given_by from employee_awards where employeeid='{$empdetails['employeeid']}'")->result();
 // $skills = $this->db->query("SELECT skills,experience,comments from employee_skills where employeeid='{$empdetails['employeeid']}'")->result();
 // $language = $this->db->query("SELECT language,skills,comments from employee_language where employeeid='{$empdetails['employeeid']}'")->result();
 // $positionh = $this->db->query("SELECT position, dateposition, assignment, remarks FROM employee_position_history where employee='{$empdetails['employeeid']}'")->result();

 // $ot = $this->db->query("SELECT r.level, c.id as tbl_id, c.skills, r.id, c.profiency FROM employee_credentials c INNER JOIN reports_item r on c.profiency = r.id WHERE employeeid='{$empdetails['employeeid']}' ")->result();
#for applicable checkbox
 $applicable_field   = $this->db->query("SELECT * FROM employee_applicable_fields WHERE employeeid='{$empdetails['employeeid']}'");
 if($applicable_field->num_rows > 0){
  $educcbox       = $applicable_field->row(0)->educBackground;
  $eligcbox       = $applicable_field->row(0)->eligibility;
  $scttcbox       = $applicable_field->row(0)->sctt;
  $wunrelatedcbox = $applicable_field->row(0)->workUnrelated;
  $wrelatedcbox   = $applicable_field->row(0)->workRelated;
  $wothercbox     = $applicable_field->row(0)->workOther;
}


?>
<style>
  h5{
    font-size: 18px;
  }

  .filename{
      cursor:pointer;
    }

  .tooltip {
  position: relative;
  display: inline-block;
  opacity: 1;
  /*border-bottom: 1px dotted black;*/
}

.tooltip .tooltiptext {
  visibility: hidden;

  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}
.tooltip .tooltiptext {
  top: -5px;
  left: 105%;
  padding: 5%;
}
.tooltip .tooltiptext::after {
  content: " ";
  position: absolute;
  top: 50%;
  right: 100%; /* To the left of the tooltip */
  margin-top: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent black transparent transparent;
}
.tooltip:hover .tooltiptext {
  visibility: visible;
}

.scrollbar{
   overflow: auto;
   margin-bottom: 10px;
}

  .scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  /* Track */
  .scrollbar::-webkit-scrollbar-track {
    box-shadow: inset 0 0 0 grey; 
    border-radius: 10px;
  }
   
  /* Handle */
  .scrollbar::-webkit-scrollbar-thumb {
    background: #0072c6;
    border-radius: 10px;
  }

  /* Handle on hover */
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background: #fadd14; 
  }

  .myInput{
    width: 10% !important;
  }

</style>
<div class="widgets_area animated fadein delay-1s">
<div class="row">
<form id="education">  
    <div class="col-md-12">
      <div class="form_row" style="margin-top: 3%;"></div>
    <div class="well-content no-search" style="border: 0 !important;">
        <div class="panel">
         <div class="panel-heading" style="background-color: #0072c6;"><h4><b>EDUCATIONAL BACKGROUND</b></h4></div>
           <div class="panel-body" id="educ_table" style="background: #fdfdf0;" style="background: #fdfdf0;">
            <div>
                <input type="checkbox" name="educcbox" id="educcbox" class="applicable-field" <?= ($educcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
            </div>
            <div class="scrollbar employee_education_table">
            
          </div>
            <a href="#modal-view" tag="add_education" data-toggle="modal" class="btn btn-success" id="agree" type="submit"  name="agree"><i class="icon glyphicon glyphicon-plus"></i> Add Educational Attainment</a>
        </div>
      </div>

      <div class="panel">
         <div class="panel-heading" style="background-color: #0072c6;"><h4><b>ELIGIBILITY</b></h4></div>
           <div class="panel-body" id="elig_table" style="background: #fdfdf0;">
            <div>
                <input type="checkbox" name="eligcbox" id="eligcbox" class="applicable-field" <?= ($eligcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
            </div>
            <div class="scrollbar employee_eligibilities_table" >
            
          </div>
            <a href="#modal-view" tag="add_eligibilities" data-toggle="modal" class="btn btn-success" id="elig_agree"><i class="icon glyphicon glyphicon-plus"></i> Add Eligibilty</a>
        </div>
      </div>
        <!-- Subject competent to teach start -->
      <div class="panel">
         <div class="panel-heading" style="background-color: #0072c6;"><h4><b>SUBJECTS COMPETENT TO TEACH</b></h4></div>
           <div class="panel-body" id="sctt_table" style="background: #fdfdf0;">
            <div>
                <input type="checkbox" name="scttcbox" id="scttcbox" class="applicable-field" <?= ($scttcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
            </div>
            <div class="scrollbar employee_subj_competent_to_teach_table">
            
          </div>
            <a href="#modal-view" tag="add_sctt" data-toggle="modal" class="btn btn-success" id="asc_agree"><i class="icon glyphicon glyphicon-plus"></i> Add Subject Competent to Teach</a>
        </div>
      </div>
        <!-- Subject competent to teach start -->
        <!-- OTHER CREDENTIAL -->
      <!--<div class="panel">
         <div class="panel-heading"><h4><b>OTHER CREDENTIALS</b></h4></div>
           <div class="panel-body" style="background: #fdfdf0;">
            <div>
                <input type="checkbox" name="otcbox" id="oc" class="applicable-field" <?= ($scttcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="otlist">
               <thead>
                  <tr>
                     <th class="col-md-4">Skills</th>
                     <th class="col-md-4">Proficiency</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($ot)>0){
                    foreach($ot as $row){
?>
                    <tr>
                        <td><?=$row->skills?></td>
                        <td relprof='<?=$row->profiency?>'><?=$row->level?></td>
                        <td>
                          <?php if ($this->session->userdata("usertype") == "ADMIN"): ?>
                            <div style="float: right;">
                                <a class='btn btn-primary edit_ot' href='#modal-view' data-toggle='modal' style="margin-right: 10px;" tbl_id="<?=$row->tbl_id?>"><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_ot' tbl_id="<?=$row->tbl_id?>"><i class='glyphicon glyphicon-trash'></i></a>
                            </div>
                          <?php endif ?>
                        </td>
                    </tr>    
<?                            
                    }
               }else{
?>
                    <tr>
                        <td colspan="4">No existing data</td>
                    </tr>
<?                    
               }
?>                        
               </tbody>
            </table>
            <a href="#modal-view" tag="add_ot" data-toggle="modal" class="btn btn-success" id="oc_agree"><i class="icon glyphicon glyphicon-plus"></i> Add Other Credentials</a>
        </div>
      </div>

        OTHER CREDENTIAL END -->
      <div class="panel">
         <div class="panel-heading" style="background-color: #0072c6;"><h4><b>WORK EXPERIENCE OUTSIDE POVEDA</b></h4></div>
           <div class="panel-body" id="wrelated_table" style="background: #fdfdf0;">
<!--             <div style="display: none;">
              <div>
                  <input type="checkbox" name="wunrelatedcbox" id="wunrelatedcbox" class="applicable-field" <?= ($wunrelatedcbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic; color:#e49191">&nbsp;Check this box if Not Applicable</span>
              </div>
              <table class="table table-hover" id="workhistorylistunrelated">
                 <thead>
                    <tr colspan="5"><h5><b>Teaching Experience in Unrelated Disciplines</b></h5></tr>
                    <tr>
                       
                       <th rowspan="2">Position Held</th>
                       <th rowspan="2">Company Name</th>
                       <th rowspan="2">Address</th>
                       <th rowspan="2">Contact Number</th>
                       <th rowspan="2">Salary</th>
                       <th colspan="2" class="align_center">Inclusive Date of Employment</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                    <tr>
                       <th class="align_center">From</th>
                       <th class="align_center">To</th>
                    </tr>
                 </thead>
                 <tbody>
  <?
                 if(count($work_history_unrelated)>0){
                      foreach($work_history_unrelated as $wh){
  ?>
                      <tr>
                          <td><?=$wh->position?></td>
                          <td><?=$wh->company?></td>
                          <td><?=$wh->address?></td>
                          <td><?=$wh->contactnumber?></td>
                          <td><?=$wh->salary?></td>
                          <td class="align_center"><?=$wh->date_from?></td>
                          <td class="align_center"><?=$wh->date_to?></td>
                          <td>
                          <?php if ($this->session->userdata("usertype") == "ADMIN"): ?>
                            <a class='btn btn-primary workhistoryunrelated' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          <?php endif ?> 
                          </td>
                      </tr>    
  <?                            
                      }
                 }else{
  ?>
                      <tr>
                          <td colspan="6">No existing data</td>
                      </tr>
  <?                    
                 }
  ?>                        
                 </tbody>
              </table>
            <a href="#modal-view" tag="add_workhistory_unrelated" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Teaching Experience Related to Discipline</a>
            </div> -->
            <div>
                <input type="checkbox" name="wrelatedcbox" id="wrelatedcbox" class="applicable-field" <?= ($wrelatedcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
            </div>
            <div class="scrollbar employee_work_history_related_table">
            
          </div>
              <a href="#modal-view" tag="add_workhistory_related" data-toggle="modal" class="btn btn-success" id="we_agree"><i class="icon glyphicon glyphicon-plus"></i> Add Work Experience</a>
<!--             <div style="display: none;">
              <hr />
              <div>
                  <input type="checkbox" name="wothercbox" id="wothercbox" class="applicable-field" <?= ($wothercbox == "0" ? "checked" : "") ?> >
                  <span style="font-style: italic;color:#e49191">&nbsp;Check this box if Not Applicable</span>
              </div>
              <table class="table table-hover" id="workhistorylist">
                 <thead>
                    <tr colspan="5"><h5><b>Other Professional Experiences</b></h5></tr>
                    <tr>
                       
                       <th rowspan="2">Position Held</th>
                       <th rowspan="2">Company Name</th>
                       <th rowspan="2">Address</th>
                       <th rowspan="2">Contact Number</th>
                       <th rowspan="2">Salary</th>
                       <th colspan="2" class="align_center">Inclusive Date of Employment</th>
                       <th class="col-md-2">&nbsp;</th>
                    </tr>
                    <tr>
                       <th class="align_center">From</th>
                       <th class="align_center">To</th>
                    </tr>
                 </thead>
                 <tbody>
  <?
                 if(count($work_history)>0){
                      foreach($work_history as $wh){
  ?>
                      <tr>
                          <td><?=$wh->position?></td>
                          <td><?=$wh->company?></td>
                          <td><?=$wh->address?></td>
                          <td><?=$wh->contactnumber?></td>
                          <td><?=$wh->salary?></td>
                          <td class="align_center"><?=$wh->date_from?></td>
                          <td class="align_center"><?=$wh->date_to?></td>
                          <td>
                          <?php if ($this->session->userdata("usertype") == "ADMIN"): ?>
                            <a class='btn btn-primary workhistory' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
                          <?php endif ?>
                          </td>
                      </tr>    
  <?                            
                      }
                 }else{
  ?>
                      <tr>
                          <td colspan="6">No existing data</td>
                      </tr>
  <?                    
                 }
  ?>                        
                 </tbody>
              </table>
              <a href="#modal-view" tag="add_workhistory" data-toggle="modal" class="btn btn-success" id="we_agree" ><i class="icon glyphicon glyphicon-plus"></i> Add Other Professional Experiences</a>
              <hr />
            </div> -->
        </div>
    </div>    
	</div>
</form>
</div>
</div>  


<!-- <div class="modal fade" id="success_modal2" role="dialog">
    <div class="modal-dialog modal-sm" style="top: 35%;">
        <div class="modal-content">
            <div class="modal-body" style="margin-bottom: 0px;">
                <p style="color:green;font-weight: bold;">Your information has been saved.</p>
            </div>
        </div>
    </div>
</div> -->
<?php $approver = $this->session->userdata("username"); ?>
<input type="hidden" id="approverid" value="<?= $approver ?>">
<script>
$(document).ready(function(){

        if($("#educcbox").prop('checked')){
          $("#educ_table a").attr('disabled', true);
          $("#educ_table a").css('pointer-events', 'none');
        }
        else{
          $("#educ_table a").attr('disabled', false);
          $("#educ_table a").css('pointer-events', '');
        }

        if($("#eligcbox").prop('checked')){
          $("#elig_table a").attr('disabled', true);
          $("#elig_table a").css('pointer-events', 'none');
        }
        else{
          $("#elig_table a").attr('disabled', false);
          $("#elig_table a").css('pointer-events', '');
        }

        if($("#scttcbox").prop('checked')){
          $("#sctt_table a").attr('disabled', true);
          $("#sctt_table a").css('pointer-events', 'none');
        }
        else{
          $("#sctt_table a").attr('disabled', false);
          $("#sctt_table a").css('pointer-events', '');
        }

        if($("#wunrelatedcbox").prop('checked')){
          $("#wunrelated_table a").attr('disabled', true);
          $("#wunrelated_table a").css('pointer-events', 'none');
        }
        else{
          $("#wunrelated_table a").attr('disabled', false);
          $("#wunrelated_table a").css('pointer-events', '');
        }

        loadTable('employee_education_table'); 
        loadTable('employee_eligibilities_table'); 
        loadTable('employee_subj_competent_to_teach_table');
        loadTable('employee_work_history_related_table');
      });


 $("#educcbox, #eligcbox, #scttcbox, #wrelatedcbox").click(function(){
          var cb1 = $("#educcbox").prop('checked') == true ? 0 : 1 ;
          var cb2 = $("#eligcbox").prop('checked') == true ? 0 :1 ;
          var cb3 = $("#scttcbox").prop('checked') == true ? 0 : 1;
          var cb4 = $("#wrelatedcbox").prop('checked') == true ? 0 : 1;
        if($("#educcbox").prop('checked')){ 
          $("#educ_table a").attr('disabled', true);
          $("#educ_table a").css('pointer-events', 'none');
        }
        else{
          $("#educ_table a").attr('disabled', false);
          $("#educ_table a").css('pointer-events', '');
        }

        if($("#eligcbox").prop('checked')){
          $("#elig_table a").attr('disabled', true);
          $("#elig_table a").css('pointer-events', 'none');
        }
        else{
          $("#elig_table a").attr('disabled', false);
          $("#elig_table a").css('pointer-events', '');
        }

        if($("#scttcbox").prop('checked')){
          $("#sctt_table a").attr('disabled', true);
          $("#sctt_table a").css('pointer-events', 'none');
        }
        else{
          $("#sctt_table a").attr('disabled', false);
          $("#sctt_table a").css('pointer-events', '');
        }

        if($("#wrelatedcbox").prop('checked')){
          $("#wrelated_table a").attr('disabled', true);
          $("#wrelated_table a").css('pointer-events', 'none');
        }
        else{
          $("#wrelated_table a").attr('disabled', false);
          $("#wrelated_table a").css('pointer-events', '');
        }

        $.ajax({
            url: "<?= site_url('applicant/EducationalCheckbox') ?>",
            type: "POST",
            data: {
              employeeid : GibberishAES.enc($("input[name='employeeid']").val(),toks),
              educBackground : GibberishAES.enc(cb1,toks),
              eligibility : GibberishAES.enc(cb2,toks),
              sctt: GibberishAES.enc(cb3,toks),
              workRelated : GibberishAES.enc(cb4, toks),
              toks:toks
            },
            dataType: "json",
            success:function(response){
            }
         });
    });


$(".delete_entry").click(function(){
    var mtable = $("#legitlist").find("tbody");
    $(this).parent().parent().remove();
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
});

$(".delete_ot").click(function(){
    var mtable = $("#otlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    $(this).parent().parent().parent().remove();
    deleteOT($(this), $(this).attr("tbl_id"));
});

$(".edit_ot").click(function(){
    addOT($(this), $(this).attr("tbl_id"));
});

$("a[tag='add_education']").click(function(){
    addeducation("");
});

function addeducation(obj, tbl_id = ""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Educational Background" : "Add Educational Background");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/education')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("select[name='eb_school']").val(tdcur.find("td:eq(0)").attr("schoolid")).trigger('chosen:updated');
                 $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger('chosen:updated');
                 $(modal_display).find("input[name='eb_course']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_units']").val(tdcur.find("td:eq(3)").text());
                 $(modal_display).find("input[name='eb_dategraduated']").val(tdcur.find("td:eq(4)").text());
                 $(modal_display).find("input[name='eb_datefrom']").val(tdcur.find("td:eq(5)").text());
                 $(modal_display).find("select[name='completed']").val(tdcur.find("td:eq(5)").attr("completed")).trigger('chosen:updated');
                 if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(6)").find("a").text().includes("APPROVED")){
                  setTimeout(function(){
                        $(modal_display).find("select[name='eb_school']").parent().parent().css("display", "none");
                        $(modal_display).find("select[name='eb_level']").parent().parent().css("display", "none");
                        $(modal_display).find("select[name='completed']").parent().parent().css("display", "none");
                        $(modal_display).find("#complete").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='eb_course']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='eb_units']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='eb_datefrom']").parent().parent().css("display", "none");
                        $(modal_display).find("input[name='eb_dategraduated']").parent().parent().css("display", "none");
                        $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                      }, 300);
                  }
                 $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(5)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(5)").find("a").attr("mime"));
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                 if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(7)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                 $("#educationlist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
              $(".modalclose").click(function(){
                $("#educationlist").find("tr").each(function(){
                    $(this).attr("iscurrent",0);
                });
            });
            });
        }
    });  
}

$(".workhistory").click(function(){
    addworkhistory($(this));
});
$("a[tag='add_workhistory']").click(function(){
    addworkhistory("");
});
function addworkhistory(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/workhistory')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='wh_position']").val(tdcur.find("td:eq(0)").text());
                 $(modal_display).find("input[name='wh_company']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='wh_address']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='wh_contact']").val(tdcur.find("td:eq(3)").text());
                 $(modal_display).find("input[name='wh_salary']").val(tdcur.find("td:eq(4)").text());
                 $(modal_display).find("input[name='wh_datefrom']").val(tdcur.find("td:eq(5)").text()); 
                 $(modal_display).find("input[name='wh_dateto']").val(tdcur.find("td:eq(6)").text());
              }else{
                 $("#workhistorylist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

// $(".workhistoryunrelated").click(function(){
//     addworkhistoryunrelated($(this));
// });
// $("a[tag='add_workhistory_unrelated']").click(function(){
//     addworkhistoryunrelated("");
// });
// function addworkhistoryunrelated(obj){
//     $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
//     $("#button_save_modal").text("Save");  
//     $.ajax({
//         url: "<?=site_url('employee_/workhistoryunrelated')?>",
//         type: "POST",
//         success: function(msg){
//             var modal_display = $("#modal-view").find("div[tag='display']");
//             $.when($(modal_display).html(msg)).done(function(){ 
//                if(obj){
//                  var tdcur = $(obj).parent().parent();
//                  $(tdcur).attr("iscurrent",1);
//                  $(modal_display).find("input[name='wh_position']").val(tdcur.find("td:eq(0)").text());
//                  $(modal_display).find("input[name='wh_company']").val(tdcur.find("td:eq(1)").text());
//                  $(modal_display).find("input[name='wh_address']").val(tdcur.find("td:eq(2)").text());
//                  $(modal_display).find("input[name='wh_contact']").val(tdcur.find("td:eq(3)").text());
//                  $(modal_display).find("input[name='wh_salary']").val(tdcur.find("td:eq(4)").text());
//                  $(modal_display).find("input[name='wh_datefrom']").val(tdcur.find("td:eq(5)").text()); 
//                  $(modal_display).find("input[name='wh_dateto']").val(tdcur.find("td:eq(6)").text());
//               }else{
//                  $("#workhistorylistunrelated").find("tr").each(function(){
//                    $(this).attr("iscurrent",0); 
//                  }) 
//               }
//             });
//         }
//     });  
// }

$(".workhistoryrelated").click(function(){
    addworkhistoryrelated($(this));
});
$("a[tag='add_workhistory_related']").click(function(){
    addworkhistoryrelated("");
});


function addworkhistoryrelated(obj, tbl_id="", wh_address="", wh_contactnumber=""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Work Experience" : "Add Work Experience");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/workhistoryrelated')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='wh_position']").val(tdcur.find("td:eq(0)").text());
                 $(modal_display).find("input[name='wh_company']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='wh_remarks']").val(tdcur.find("td:eq(2)").text());
                 // $(modal_display).find("input[name='wh_salary']").val(tdcur.find("td:eq(3)").text()); 
                 $(modal_display).find("input[name='wh_reason']").val(tdcur.find("td:eq(3)").text());
                  if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(5)").find("a").text().includes("APPROVED")){
                      $(modal_display).find("input[name='wh_company']").parent().parent().css("display", "none");
                      $(modal_display).find("input[name='wh_position']").parent().parent().css("display", "none");
                      $(modal_display).find("input[name='wh_remarks']").parent().parent().css("display", "none");
                      $(modal_display).find("input[name='wh_reason']").parent().parent().css("display", "none");
                      $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                    }
                 $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(4)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(4)").find("a").attr("mime"));
                 
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                 if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(6)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                 $("#workhistorylistrelated").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            $(".modalclose").click(function(){
                $("#workhistorylistrelated").find("tr").each(function(){
                    $(this).attr("iscurrent",0);
                });
            });
            });
        }
    });  
}

$("a[tag='add_eligibilities']").click(function(){
    addeligibilities("");
});
function addeligibilities(obj, tbl_id = ""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Eligibility" : "Add Eligibility");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/eligibilities')?>",
        type: "POST",
        success: function(msg)
        {
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj)
                {
                    var tdcur = $(obj).parent().parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("select[name='el_description']").val(tdcur.find("td:eq(0)").attr("desc")).trigger('chosen:updated');
                    // $(modal_display).find("option[value="+tdcur.find("td:eq(0)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                    $(modal_display).find("input[name='el_licenseNo']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("input[name='el_issuedDate']").val(tdcur.find("td:eq(2)").text()); 
                    $(modal_display).find("input[name='el_expiryDate']").val(tdcur.find("td:eq(3)").text()); 
                    $(modal_display).find("input[name='el_remarks']").val(tdcur.find("td:eq(4)").text());
                    if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(6)").find("a").text().includes("APPROVED")){
                      $(modal_display).find("select[name='el_description']").parent().parent().css("display", "none");
                      $(modal_display).find("input[name='el_licenseNo']").parent().parent().css("display", "none");
                      $(modal_display).find("input[name='el_remarks']").parent().parent().css("display", "none");
                      $(modal_display).find("input[name='el_issuedDate']").parent().parent().parent().css("display", "none");
                      $(modal_display).find("input[name='el_expiryDate']").parent().parent().parent().css("display", "none");
                      $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                    }
                    $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(5)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(5)").find("a").attr("mime"));
                    $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                    if("<?=$usertype?>" == "ADMIN"){
                      $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(7)").text());
                      $(modal_display).find("#draremarks").css("display", "block");
                   }
                }
                else
                {
                  if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                    $("#eligibilitieslist").find("tr").each(function(){
                    $(this).attr("iscurrent",0); 
                 }) 
            }
            $(".modalclose").click(function()
            {
                $("#eligibilitieslist").find("tr").each(function()
                {
                    $(this).attr("iscurrent",0);
                });
            });
            });
        }
    });  
}
//SCTT
$(".sctt").click(function(){
    addSctt($(this));
});



$("a[tag='add_sctt']").click(function(){
    addSctt("");
});

$("a[tag='add_ot']").click(function(){
    addOT("");
});

$(".update_status").click(function(){
    var current_status = $(this).text();
    // if(current_status == "APPROVED"){
    //     alert("This information is already APPROVED!");
    //     return;
    // }
    var table = $(this).closest("tr").attr("table");
    var id = $(this).closest("tr").attr("id");
    var status = updateTableStatus(table, id);
    // $(this).text(status)
    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
    else $(this).removeClass("btn-success").addClass("btn-danger");
});

function updateTableStatus(table, id){
    var approverid = $("#approverid").val();
    var status = "";
    $.ajax({
        url: "<?= site_url('employee_/updateTableStatus') ?>",
        type:"POST",
        data: {table:  GibberishAES.enc( table, toks), id:  GibberishAES.enc(id , toks), approverid: GibberishAES.enc(approverid , toks), toks:toks},
        async: false,
        success:function(response){
          status = response;
        }
    });

    return status;
}

function addSctt(obj, tbl_id=""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Subject Competent to Teach" : "Add Subject Competent to Teach");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/sctt')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("select[name='el_subj']").val(tdcur.find("td:eq(0)").attr("relsctt")).trigger('chosen:updated');
                 $(modal_display).find("input[name='el_remarks']").val(tdcur.find("td:eq(1)").text());
                 if("<?=$usertype?>" == "EMPLOYEE" && tdcur.find("td:eq(3)").find("a").text().includes("APPROVED")){
                      $(modal_display).find("select[name='el_subj']").parent().parent().css("display", "none");
                      $(modal_display).find("input[name='el_remarks']").parent().parent().css("display", "none");
                      $(modal_display).find("#file_uploaded").parent().parent().css("margin-top", "20px");
                    }
                 $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(2)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(2)").find("a").attr("mime"));
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                 if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
              }else{
                if("<?=$usertype?>" == "ADMIN"){
                    $(modal_display).find("#draremarks").css("display", "block");
                 }
                 $("#scttlist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
              $(".modalclose").click(function(){
                $("#scttlist").find("tr").each(function(){
                    $(this).attr("iscurrent",0);
                });
            });
            });
        }
    });  
}

function addOT(obj, tbl_id=""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Other Credentials" : "Add Other Credentials");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/ot')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='el_skills']").val(tdcur.find("td:eq(0)").text());
                 $(modal_display).find("select[name='el_proficiency']").val(tdcur.find("td:eq(1)").attr("relprof")).trigger('chosen:updated');
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
              }else{
                 $("#otlist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            $(".modalclose").click(function(){
                $("#otlist").find("tr").each(function(){
                    $(this).attr("iscurrent",0);
                });
            });
            });
        }
    });  
}

function addWHR(obj, tbl_id=""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Other Credentials" : "Add Other Credentials");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/ot')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='el_skills']").val(tdcur.find("td:eq(0)").text());
                 $(modal_display).find("select[name='el_proficiency']").val(tdcur.find("td:eq(1)").attr("relprof")).trigger('chosen:updated');
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
              }else{
                 $("#workhistorylistrelated").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            $(".modalclose").click(function(){
                $("#workhistorylistrelated").find("tr").each(function(){
                    $(this).attr("iscurrent",0);
                });
            });
            });
        }
    });  
}



function deleteOT(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_credentials";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_credentials"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){ 
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted!',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });  
}





// function loadSuccessModal(){
//     if(!$('#success_modal').is(':visible')){
//         $("#success_modal").modal("show");
//         setTimeout(function(){ $("#success_modal").modal("hide"); }, 800);
//     }
// }

$.validator.setDefaults({
  debug: true,
  success: "valid",
  ignore: ":hidden:not(.chzn-done)",
  errorPlacement: function(error, element) {
        if (element.hasClass('chosen')) {
            error.insertAfter(element.next('.chzn-container'));
        }else if(element.hasClass('yesno') || element.hasClass('applicable-field')){
            error.insertBefore(element.parents("div").eq(0));
        }else{
            error.insertAfter(element);
        }
    }
});

$("#eligibilitieslist, #educationlist, #sctt_table, #workhistorylistrelated").delegate(".filename", "click", function(){
    var trid = $(this).closest("tr");
      var data = $(trid).find(".filename").attr("content");
      var mime = $(trid).find(".filename").attr("mime");
      openFile(data, mime);
      
});
  function openFile(data, mime){
      if(data){
      var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
        window.open(objectURL);
      }else{
        var file_url = $(this).attr("content");
        window.open(file_url);
      }
  }
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

  $(".tooltip").hover(function(){
    var id = $(this).attr('id');
    var table = $(this).attr('table');
    loadStatusHistory(id, table);
  });


  function loadStatusHistory(id, table){
    $.ajax({
        url: $("#site_url").val() + "/employee_/loadStatusHistory",
        type: "POST",
        data: {id: GibberishAES.enc(id , toks), table: GibberishAES.enc(table , toks), toks:toks},
        success: function(history){ 
            if(history != '')  $(".tooltiptext_"+id+"_"+table).html(history);
            else $(".tooltiptext_"+id+"_"+table).html("No History");
            
        }
    });
  }

$('.chosen').chosen();

 setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn delay-1s");
  }, 2000);

</script>