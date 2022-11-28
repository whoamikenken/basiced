<?php

/**
 * @author Justin
 * @copyright 2016
 */
  $usertype = $this->session->userdata("usertype");
  $applicable_field = $pgdcbox = $rescbox = $awardscbox = $schocbox = $semcbox = $twcbox = $resourcecbox = $orgcbox = $cominvolvecbox = $adminfcbox = "";

 if(isset($empinfo)){
   $empdetails = $empinfo;    
 }else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0];
 }
 
 $seminar = $this->db->query("select title,place,`date`,resource_speaker,credit_earn from employee_seminar where employeeid='{$empdetails['employeeid']}'")->result();
 $pgd = $this->db->query("select publication,title,datef,type,r.level,e.educ_level,r.ID from employee_pgd e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $researches = $this->db->query("select educational_level,school,honor,year_graduated,r.level,r.ID from employee_researches e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $ar = $this->db->query("select publication,title,datef,type,r.level,r.ID from employee_awardsrecog e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $scho = $this->db->query("select publication,title,datef,type,r.ID,r.level from employee_scholarship e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $scs = $this->db->query("select educational_level,school,honor,year_graduated,type,location,r.level,r.ID from employee_scs e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $tw = $this->db->query("select educational_level,school,honor,year_graduated,type,location,r.ID,r.level from employee_workshops e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $resource = $this->db->query("select educational_level,school,honor,year_graduated,type,location,r.ID,r.level from employee_resource e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $org   = $this->db->query("select educational_level,school,honor,year_graduated,r.ID,r.level from employee_proorg e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();
 $community   = $this->db->query("select educational_level,school,honor,year_graduated from employee_community where employeeid='{$empdetails['employeeid']}'")->result();
 $administrative   = $this->db->query("select educational_level,school,honor,year_graduated,r.ID,r.level from employee_administrative e INNER JOIN reports_item r ON e.educ_level = r.ID where employeeid='{$empdetails['employeeid']}'")->result();

 $applicable_field   = $this->db->query("SELECT * FROM employee_applicable_fields WHERE employeeid='{$empdetails['employeeid']}'");
if($applicable_field->num_rows > 0){
  $pgdcbox            = $applicable_field->row(0)->profGrowth;
  $rescbox            = $applicable_field->row(0)->researches;
  $awardscbox         = $applicable_field->row(0)->awards;
  $schocbox           = $applicable_field->row(0)->scholarship;
  $semcbox            = $applicable_field->row(0)->seminar;
  $twcbox             = $applicable_field->row(0)->training;
  $resourcecbox       = $applicable_field->row(0)->speakingEngagement;
  $orgcbox            = $applicable_field->row(0)->profOrg;
  $cominvolvecbox     = $applicable_field->row(0)->comInvolvement;
  $adminfcbox         = $applicable_field->row(0)->adminFunctions;
}
 
?>
<div class="widgets_area">
<div class="row">
<form id="seminarstraining">  
    <div class="col-md-12">
    <div class="well-content no-search" style="border: 0 !important;">
        <div class='align_right'><label class="text-info">
                <b>(Click SAVE for each tab you accomplish)</b></label> 
                <a href="#" class="btn btn-primary" id="savetrainingseminars">Save All Information</a>
        </div>
        <br />
        <!-- PGD -->
        <div class="well-header" style="background: #823982;">
            <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Professional Growth &amp; Development</h5>
        </div>
        <div class="well-content no-search" style="background: #f8f1fc;">
            <div>
                <input type="checkbox" name="pgdcbox" class="applicable-field" <?= ($pgdcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="pgdinfolist">
               <thead>
                  <tr>
                     <th>Type of Publication</th>
                     <th>Title</th>
                     <th>Type of Authorship</th>
                     <th>Date Published</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($pgd)>0){
                    foreach($pgd as $sm){
?>
                    <tr>
                        <td educpgd='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->title?></td>
                        <td><?=$sm->type?></td>
                        <td><?=$sm->datef?></td>
                        <td class="align_center">
                            <a class='btn btn-danger pgd' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_pgd" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add Professional Growth &amp; Development</a>
        </div>
        
        <!-- Researches -->
        <div class="well-header" style="background: #823982;">
            <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Researches</h5>
        </div>
        <div class="well-content no-search" style="background: #f8f1fc;">
            <div>
                <input type="checkbox" name="rescbox" class="applicable-field" <?= ($rescbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="researchesinfolist">
               <thead>
                  <tr>
                     <th>Date Published</th>
                     <th>Type of Research</th>
                     <th>Research Title</th>
                     <!-- <th>Honor</th> -->
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($researches)>0){
                    foreach($researches as $sm){
?>
                    <tr>
                        <td><?=$sm->school?></td>
                       <!--  <td><?=$sm->educational_level?></td> -->
                        <td educr='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->honor?></td>
                        <td><?=$sm->year_graduated?></td>
                        <td class="align_center">
                            <!--<a class='btn btn-danger researches' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>-->
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_researches" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add Researches</a>
        </div>
        
        <!-- AWARDS & RECOGNITION -->
        <div class="well-header" style="background: #823982;">
            <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Awards &amp; Recognition</h5>
        </div>
        <div class="well-content no-search" style="background: #f8f1fc;">
            <div>
                <input type="checkbox" name="awardscbox" class="applicable-field" <?= ($awardscbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="arinfolist">
               <thead>
                  <tr>
                     <th>Type of Publication</th>
                     <th>Title</th>
                     <th>Type of Authorship</th>
                     <th>Date Published</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($ar)>0){
                    foreach($ar as $sm){
?>
                    <tr>
                        <!-- <td><?=$sm->publication?></td> -->
                        <td educar='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->title?></td>
                        <td><?=$sm->type?></td>
                        <td><?=$sm->datef?></td>
                        <td class="align_center">
                            <a class='btn btn-danger ar' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_ar" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add Awards &amp; Recognition</a>
        </div>
        
         <!-- Scholarship -->
        <div class="well-header" style="background: #823982;">
            <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Scholarship</h5>
        </div>
        <div class="well-content no-search" style="background: #f8f1fc;">
            <div>
                <input type="checkbox" name="schocbox" class="applicable-field" <?= ($schocbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="schoinfolist">
               <thead>
                  <tr>
                     <th>Type of Publication</th>
                     <th>Title</th>
                     <th>Type of Authorship</th>
                     <th>Date Published</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($scho)>0){
                    foreach($scho as $sm){
?>
                    <tr>
                        <td educscho='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->title?></td>
                        <td><?=$sm->type?></td>
                        <td><?=$sm->datef?></td>
                        <td class="align_center">
                            <a class='btn btn-danger scho' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_scho" data-toggle="modal"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
        </div>
        
        <!-- Professional Involvements -->
        <div class="well-header" style="background: #823982;">
            <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Professional Involvements</h5>
        </div>
        <div class="well-content no-search" style="background: #f8f1fc;">
            <div>
                <input type="checkbox" name="semcbox" class="applicable-field" <?= ($semcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="scsinfolist">
               <thead>
                  <tr colspan="5"><h5><b>Seminar/Conventions/Conferences (Related to field of expertise)</b></h5></tr>  
                  <tr>
                     <th>Name of School</th>
                     <th>Education Level</th>
                     <th>Year Graduated</th>
                     <th>Honor</th>
                     <th>Type</th>
                     <th>Location</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($scs)>0){
                    foreach($scs as $sm){
?>
                    <tr>
                        <td><?=$sm->school?></td>
                       <!--  <td><?=$sm->educational_level?></td> -->
                        <td educpi='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->year_graduated?></td>
                        <td><?=$sm->honor?></td>
                        <td><?=$sm->type?></td>
                        <td><?=$sm->location?></td>
                        <td class="align_center">
                            <a class='btn btn-danger scs' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_scs" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
            <hr />
            <div>
                <input type="checkbox" name="twcbox" class="applicable-field" <?= ($twcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="twinfolist">
               <thead>
                  <tr colspan="5"><h5><b>Trainings/Workshops (Related to field ot expertise)</b></h5></tr>  
                  <tr>
                     <th>Name of School</th>
                     <th>Education Level</th>
                     <th>Year Graduated</th>
                     <th>Honor</th>
                     <th>Type</th>
                     <th>Location</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($tw)>0){
                    foreach($tw as $sm){
?>
                    <tr>
                        <td><?=$sm->school?></td>
                       <!--  <td><?=$sm->educational_level?></td> -->
                        <td eductw='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->year_graduated?></td>
                        <td><?=$sm->honor?></td>
                        <td><?=$sm->type?></td>
                        <td><?=$sm->location?></td>
                        <td class="align_center">
                            <a class='btn btn-danger tw' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_tw" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
            <hr />
            <div>
                <input type="checkbox" name="resourcecbox" class="applicable-field" <?= ($resourcecbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="resourceinfolist">
               <thead>
                  <tr colspan="5"><h5><b>Speaking Engagements/Resource Speaker</b></h5></tr>  
                  <tr>
                     <th>Name of School</th>
                     <th>Education Level</th>
                     <th>Year Graduated</th>
                     <th>Honor</th>
                     <th>Type</th>
                     <th>Location</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($resource)>0){
                    foreach($resource as $sm){
?>
                    <tr>
                        <td><?=$sm->school?></td>
                        <td educse='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->year_graduated?></td>
                        <td><?=$sm->honor?></td>
                        <td><?=$sm->type?></td>
                        <td><?=$sm->location?></td>
                        <td class="align_center">
                            <a class='btn btn-danger resource' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_resource" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
            <hr />
            <div>
                <input type="checkbox" name="orgcbox" class="applicable-field" <?= ($orgcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="orginfolist">
               <thead>
                  <tr colspan="5"><h5><b>Membership or Affiliation in Professional Organization</b></h5></tr>  
                  <tr>
                     <th>Name of School</th>
                     <th>Education Level</th>
                     <th>Year Graduated</th>
                     <th>Honor</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($org)>0){
                    foreach($org as $sm){
?>
                    <tr>
                        <td><?=$sm->school?></td>
                        <td educmapo='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->honor?></td>
                        <td><?=$sm->year_graduated?></td>
                        <td class="align_center">
                            <!--<a class='btn btn-danger org' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>-->
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_org" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
            <hr />
            <div>
                <input type="checkbox" name="cominvolvecbox" class="applicable-field" <?= ($cominvolvecbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="communityinfolist">
               <thead>
                  <tr colspan="5"><h5><b>Membership in Civic Organizations/Community Involvement</b></h5></tr>  
                  <tr>
                     <th>Name of School</th>
                     <th>Education Level</th>
                     <th>Year Graduated</th>
                     <th>Honor</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($community)>0){
                    foreach($community as $sm){
?>
                    <tr>
                        <td><?=$sm->school?></td>
                        <td><?=$sm->educational_level?></td>
                        <td><?=$sm->honor?></td>
                        <td><?=$sm->year_graduated?></td>
                        <td class="align_center">
                            <!--<a class='btn btn-danger community' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>-->
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_community" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
            <hr />
            <div>
                <input type="checkbox" name="adminfcbox" class="applicable-field" <?= ($adminfcbox == "0" ? "checked" : "") ?> >
                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
            </div>
            <table class="table table-hover" id="administrativeinfolist">
               <thead>
                  <tr colspan="5"><h5><b>Administrative Functions Handled</b></h5></tr>  
                  <tr>
                     <th>Name of School</th>
                     <th>Education Level</th>
                     <th>Year Graduated</th>
                     <th>Honor</th>
                     <th class="col-md-2">&nbsp;</th>
                  </tr>
               </thead>
               <tbody>
<?
               if(count($administrative)>0){
                    foreach($administrative as $sm){
?>
                    <tr>
                        <td><?=$sm->school?></td>
                        <td educafh='<?=$sm->ID?>'><?=$sm->level?></td>
                        <td><?=$sm->honor?></td>
                        <td><?=$sm->year_graduated?></td>
                        <td class="align_center">
                            <!--<a class='btn btn-danger administrative' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>-->
                            <a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
            <a href="#modal-view" tag="add_administrative" data-toggle="modal" style="color:#767cf6"><i class="icon glyphicon glyphicon-plus"></i> Add New</a>
            <hr />
        </div>
        <!--
        <div class="form_row">
            <div class="field">
                <a href="#" class="btn btn-primary" id="savetrainingseminars">Save All Information</a>
            </div>
        </div>
        -->
    </div>    
    </div>
</form>
</div>
</div>        
<script>
$(".delete_entry").click(function(){
    $(this).parent().parent().remove();
});


$(".pgd").click(function(){
    addpgd($(this));
});
$("a[tag='add_pgd']").click(function(){
    addpgd("");
});
function addpgd(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/pgd')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='sm_publication']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("input[name='sm_title']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='sm_type']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(3)").text());
              }else{
                 $("#pgdinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".researches").click(function(){
    addresearches($(this));
});
$("a[tag='add_researches']").click(function(){
    addresearches("");
});
function addresearches(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/researches')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_date']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
              }else{
                 $("#researchesinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".ar").click(function(){
    addar($(this));
});
$("a[tag='add_ar']").click(function(){
    addar("");
});
function addar(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/ar')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='sm_publication']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("input[name='sm_title']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='sm_type']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(3)").text());
              }else{
                 $("#arinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".scho").click(function(){
    addscho($(this));
});
$("a[tag='add_scho']").click(function(){
    addscho("");
});
function addscho(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/scho')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='sm_publication']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("input[name='sm_title']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='sm_type']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='sm_date']").val(tdcur.find("td:eq(3)").text());
              }else{
                 $("#schoinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}


$(".scs").click(function(){
    addscs($(this));
});
$("a[tag='add_scs']").click(function(){
    addscs("");
});
function addscs(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/scs')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                 // $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("option[value="+tdcur.find("td:eq(1)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                 $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
                 $(modal_display).find(":radio[value="+tdcur.find("td:eq(4)").text()+"]").attr("checked", true);
                 $(modal_display).find("option[value="+tdcur.find("td:eq(5)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
              }else{
                 $("#scsinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".tw").click(function(){
    addtw($(this));
});
$("a[tag='add_tw']").click(function(){
    addtw("");
});
function addtw(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/tw')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                 // $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("option[value="+tdcur.find("td:eq(1)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                 $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
                 $(modal_display).find(":radio[value="+tdcur.find("td:eq(4)").text()+"]").attr("checked", true);
                 $(modal_display).find("option[value="+tdcur.find("td:eq(5)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
              }else{
                 $("#twinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".resource").click(function(){
    addresource($(this));
});
$("a[tag='add_resource']").click(function(){
    addresource("");
});
function addresource(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/resource')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                 // $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("option[value="+tdcur.find("td:eq(1)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
                 $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
                 $(modal_display).find(":radio[value="+tdcur.find("td:eq(4)").text()+"]").attr("checked", true);
                 $(modal_display).find("option[value="+tdcur.find("td:eq(5)").text()+"]").attr("selected", "selected").trigger("liszt:updated");
              }else{
                 $("#resourceinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".org").click(function(){
    addorg($(this));
});
$("a[tag='add_org']").click(function(){
    addorg("");
});
function addorg(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/org')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
              }else{
                 $("#orginfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".community").click(function(){
    addcommunity($(this));
});
$("a[tag='add_community']").click(function(){
    addcommunity("");
});
function addcommunity(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/community')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
              }else{
                 $("#communityinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".administrative").click(function(){
    addadministrative($(this));
});
$("a[tag='add_administrative']").click(function(){
    addadministrative("");
});
function addadministrative(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Data" : "Add New");
    $("#button_save_modal").text("Save");    
    $.ajax({
        url: "<?=site_url('employee_/administrative')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_school']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='eb_yeargrad']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='eb_honor']").val(tdcur.find("td:eq(3)").text());
              }else{
                 $("#administrativeinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

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

$("#savetrainingseminars").click(function(){
        /** PGD */
       var pgd = "";
       $("#pgdinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             pgd += (pgd?"|":"");
             /*pgd += $(this).find("td:eq(0)").text();*/
             pgd += $(this).find("td:eq(0)").attr('educpgd');
             pgd += "~u~";
             pgd += $(this).find("td:eq(1)").text();
             pgd += "~u~";
             pgd += $(this).find("td:eq(2)").text();
             pgd += "~u~";
             pgd += $(this).find("td:eq(3)").text();
         }
       });
       /** Researches */
       var researches = "";
       $("#researchesinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             researches += (researches?"|":"");
             researches += $(this).find("td:eq(0)").text();
            
             researches += "~u~";
             /*researches += $(this).find("td:eq(1)").text();*/
             researches += $(this).find("td:eq(1)").attr('educr');
             researches += "~u~";
             researches += $(this).find("td:eq(2)").text();
             researches += "~u~";
             researches += $(this).find("td:eq(3)").text();
         }
       });
       /** Awards & Recognition */
       var ar = "";
       $("#arinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             ar += (ar?"|":"");
             /*ar += $(this).find("td:eq(0)").text();*/
             ar += $(this).find("td:eq(0)").attr('educar');
             ar += "~u~";
             ar += $(this).find("td:eq(1)").text();
             ar += "~u~";
             ar += $(this).find("td:eq(2)").text();
             ar += "~u~";
             ar += $(this).find("td:eq(3)").text();
         }
       });
       
       /** Scholarship */
       var scho = "";
       $("#schoinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             scho += (scho?"|":"");
            /* scho += $(this).find("td:eq(0)").text();*/
             scho += $(this).find("td:eq(0)").attr('educscho');
             scho += "~u~";
             scho += $(this).find("td:eq(1)").text();
             scho += "~u~";
             scho += $(this).find("td:eq(2)").text();
             scho += "~u~";
             scho += $(this).find("td:eq(3)").text();
         }
       });
       
       /** Seminar/Conventions/Conferences(Related to field of expertise) */
       var scs = "";
       $("#scsinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             scs += (scs?"|":"");
             scs += $(this).find("td:eq(0)").text();
             scs += "~u~";
            /* scs += $(this).find("td:eq(1)").text();*/
             scs += $(this).find("td:eq(1)").attr('educpi');
             scs += "~u~";
             scs += $(this).find("td:eq(2)").text();
             scs += "~u~";
             scs += $(this).find("td:eq(3)").text();
             scs += "~u~";
             scs += $(this).find("td:eq(4)").text();
             scs += "~u~";
             scs += $(this).find("td:eq(5)").text();
         }
       });
       
       /** Trainings/Workshops (Related to field ot expertise) */
       var tw = "";
       $("#twinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             tw += (tw?"|":"");
             tw += $(this).find("td:eq(0)").text();
             tw += "~u~";
             tw += $(this).find("td:eq(1)").attr('eductw');
             /*tw += $(this).find("td:eq(1)").text();*/
             tw += "~u~";
             tw += $(this).find("td:eq(2)").text();
             tw += "~u~";
             tw += $(this).find("td:eq(3)").text();
             tw += "~u~";
             tw += $(this).find("td:eq(4)").text();
             tw += "~u~";
             tw += $(this).find("td:eq(5)").text();
         }
       });
       /** Speaking Engagements/Resource Speaker */
       var resource = "";
       $("#resourceinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             resource += (resource?"|":"");
             resource += $(this).find("td:eq(0)").text();
             resource += "~u~";
             resource += $(this).find("td:eq(1)").attr('educse');
            /* resource += $(this).find("td:eq(1)").text();*/
             resource += "~u~";
             resource += $(this).find("td:eq(2)").text();
             resource += "~u~";
             resource += $(this).find("td:eq(3)").text();
             resource += "~u~";
             resource += $(this).find("td:eq(4)").text();
             resource += "~u~";
             resource += $(this).find("td:eq(5)").text();
         }
       });
       
       /** Membership or Affiliation in Professional Organization */
       var org = "";
       $("#orginfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             org += (org?"|":"");
             org += $(this).find("td:eq(0)").text();
             org += "~u~";
             org += $(this).find("td:eq(1)").attr('educmapo');
             /*org += $(this).find("td:eq(1)").text();*/
             org += "~u~";
             org += $(this).find("td:eq(2)").text();
             org += "~u~";
             org += $(this).find("td:eq(3)").text();
         }
       });
       
       /** Membership in Civic Organizations/Community Involvement */
       var community = "";
       $("#communityinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             community += (community?"|":"");
             community += $(this).find("td:eq(0)").text();
             community += "~u~";
             community += $(this).find("td:eq(1)").text();
             community += "~u~";
             community += $(this).find("td:eq(2)").text();
             community += "~u~";
             community += $(this).find("td:eq(3)").text();
         }
       });
       
       /** Administrative Functions Handled */
       var administrative = "";
       $("#administrativeinfolist").find("tbody tr").each(function(){
         if($(this).find("td").length>1){
             administrative += (administrative?"|":"");
             administrative += $(this).find("td:eq(0)").text();
             administrative += "~u~";
             /*administrative += $(this).find("td:eq(1)").text();*/
             administrative += $(this).find("td:eq(1)").attr('educafh');
             administrative += "~u~";
             administrative += $(this).find("td:eq(2)").text();
             administrative += "~u~";
             administrative += $(this).find("td:eq(3)").text();
         }
       });

       var usertype = "<?=$usertype?>";
       var $validator;
       if(usertype == "EMPLOYEE"){
          $validator = $("#seminarstraining").validate({
               rules: {
                  pgdcbox         :{required: {depends: function(element) {return (pgd          == "");}}},
                  rescbox         :{required: {depends: function(element) {return (researches   == "");}}},
                  awardscbox      :{required: {depends: function(element) {return (ar           == "");}}},
                  schocbox        :{required: {depends: function(element) {return (scho         == "");}}},
                  semcbox         :{required: {depends: function(element) {return (scs          == "");}}},
                  twcbox          :{required: {depends: function(element) {return (tw           == "");}}},
                  resourcecbox    :{required: {depends: function(element) {return (resource     == "");}}},
                  orgcbox         :{required: {depends: function(element) {return (org          == "");}}},
                  cominvolvecbox  :{required: {depends: function(element) {return (community    == "");}}},
                  adminfcbox      :{required: {depends: function(element) {return (administrative == "");}}},
            
              }
          });
          $( "input[name=pgdcbox]"         ).rules( "add", { required: { depends: function(element) { return (pgd           == ""); }}});
          $( "input[name=rescbox]"         ).rules( "add", { required: { depends: function(element) { return (researches    == ""); }}});
          $( "input[name=awardscbox]"      ).rules( "add", { required: { depends: function(element) { return (ar            == ""); }}});
          $( "input[name=schocbox]"        ).rules( "add", { required: { depends: function(element) { return (scho          == ""); }}});
          $( "input[name=semcbox]"         ).rules( "add", { required: { depends: function(element) { return (scs           == ""); }}});
          $( "input[name=twcbox]"          ).rules( "add", { required: { depends: function(element) { return (tw            == ""); }}});
          $( "input[name=resourcecbox]"    ).rules( "add", { required: { depends: function(element) { return (resource      == ""); }}});
          $( "input[name=orgcbox]"         ).rules( "add", { required: { depends: function(element) { return (org           == ""); }}});
          $( "input[name=cominvolvecbox]"  ).rules( "add", { required: { depends: function(element) { return (community     == ""); }}});
          $( "input[name=adminfcbox]"      ).rules( "add", { required: { depends: function(element) { return (administrative == ""); }}});
        }  

        if($("#seminarstraining").valid()){
            var form_data = $("#seminarstraining").serialize();
                form_data += "&job=employee/seminar_info";
                form_data += "&pgd="+pgd;  
                form_data += "&researches="+researches;
                form_data += "&ar="+ar;
                form_data += "&scho="+scho;
                form_data += "&scs="+scs;
                form_data += "&tw="+tw;
                form_data += "&resource="+resource;
                form_data += "&org="+org;
                form_data += "&community="+community;
                form_data += "&administrative="+administrative;
            $.ajax({
               url: "<?=site_url("employee_/validateinfo")?>",
               data : form_data,
               type : "POST",
               success:function(msg){
                 alert($(msg).find("message:eq(0)").text());
               }
            }); 
        }else {
          $validator.focusInvalid();
          $( "input[name=pgdcbox], input[name=rescbox], input[name=awardscbox], input[name=schocbox], input[name=semcbox], input[name=twcbox], input[name=resourcecbox], input[name=orgcbox], input[name=cominvolvecbox], input[name=adminfcbox]" ).rules( "remove" );
          return false;
        }
       
      
});
$('.chosen').chosen();
</script>