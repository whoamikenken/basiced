<?php

/**
 * @author Justin
 * @copyright 2015   
 */

$usertype = $this->session->userdata("usertype");
$CI =& get_instance();
$CI->load->model('applicantt');
  $oc=$empdetails="";
 $applicable_field = $educcbox = $eligcbox = $scttcbox = $wunrelatedcbox = $wrelatedcbox = $wothercbox =  $otherCredcbox = "";
 $educational_background = $work_history = $work_history_related = $work_history_unrelated = $eligibilities = $sctt = array();

if(!$applicantId) $applicantId = $CI->applicantt->getApplicantId($lname, $fname, $mname, $email, $positionid);

if($applicantId){

    $educational_background = $this->db->query("SELECT units,completed,educational_level,school,minor,major,year_graduated,e.id as tbid,r.level,e.educ_level, e.course, e.units, e.datefrom, e.dateto, a.schoolid, a.description as schoolDesc from applicant_education e LEFT JOIN reports_item r ON e.educ_level = r.ID INNER JOIN code_school a ON e.schoolid = a.schoolid where employeeid='$applicantId'")->result();
     // echo "<pre>"; print_r($this->db->last_query()); die;
    $work_history = $this->db->query("SELECT date_from,date_to,position,company,address,contactnumber,salary from applicant_work_history where employeeid='$applicantId'")->result();
    $work_history_unrelated = $this->db->query("SELECT id,date_from,date_to,position,company,address,contactnumber,salary,remarks,reason from applicant_work_history_unrelated where employeeid='$applicantId'")->result();
    $work_history_related = $this->db->query("SELECT date_from,date_to,position,company,address,contactnumber,salary from applicant_work_history_related where employeeid='$applicantId'")->result();
    $eligibilities = $this->db->query("SELECT e.description,date_issued,affiliating_center,educ_level,e.id as tbid,r.level,e.license_number,e.date_expired,e.remarks ,e.content, e.filename , e.mime from applicant_eligibilities e LEFT JOIN reports_item r ON e.educ_level = r.ID where employeeid='$applicantId'")->result();
   
    $sctt = $this->db->query("SELECT a.id,a.employeeid,a.subj_id,b.subj_code,b.`description`,a.`remarks`
                          FROM applicant_subj_competent_to_teach a
                          LEFT JOIN code_subj_competent_to_teach b ON a.`subj_id`=b.`id`
                          WHERE a.`employeeid`='$applicantId'")->result();
    $oc = $this->db->query("SELECT a.id as id, a.skills, a.profiency, b.level FROM applicant_credentials a LEFT JOIN reports_item b ON a.`profiency` = b.`ID` WHERE employeeid='$applicantId' ")->result();
    #for applicable checkbox
    $applicable_field   = $this->db->query("SELECT * FROM applicant_applicable_fields WHERE employeeid='$applicantId'");
    if($applicable_field->num_rows > 0){
      $educcbox       = $applicable_field->row(0)->educBackground;
      $eligcbox       = $applicable_field->row(0)->eligibility;
      $scttcbox       = $applicable_field->row(0)->sctt;
      $wunrelatedcbox = $applicable_field->row(0)->workUnrelated;
      $wrelatedcbox   = $applicable_field->row(0)->workRelated;
      $wothercbox     = $applicable_field->row(0)->workOther;
      $otherCredcbox  = $applicable_field->row(0)->otherCredentials;
    }

}

?>
<style>
hr{
  border-top: 1px solid #3a4651;
  border-color: #a0d1ca;
}

thead{
  color: #000;
}
h5{
  font-weight: 500;
}
.modal {
    outline: none;
    position: fixed!important;
    margin-top: 0;
    top: 50%;
    overflow: visible;
}
.modal.fade.in {
    top: 13%!important;
}
.upperCase{
    text-transform:uppercase;
}
</style>
<div class="widgets_area">
    <div class="row">
        <form id="education">  
            <div class="col-md-12">
                <div class="well-content no-search" style="border: 0 !important;">
                    <input name="fname" type="hidden" value="<?=$fname?>"/>
                    <input name="lname" type="hidden" value="<?=$lname?>"/>
                    <input name="mname" type="hidden" value="<?=$mname?>"/>
                    <input name="usertype" type="hidden" value="<?=$usertype?>"/>

<!--                     <div class='align_right'><label class="text-info">
                        <b>(Click SAVE for each tab you accomplish)</b></label> 
                        <a href="#" class="btn btn-info" id="saveeducationbackground">Save All Information</a>
                    </div> -->
                    <br />
                    <div class="panel animated fadeIn">
                        <div class="panel-heading"><h4><b>Educational Background</b></h4></div>
                        <div class="panel-body" id="educationTable">
                            <div id="educationSpan" style="display: none;">
                                <span style="color:red">This field is required.</span>
                            </div>
                            <div class="col-md-12">
                                
                                <input type="checkbox" name="educcbox" id="educcbox" class="applicable-field" <?= ($educcbox == "1" ? "checked" : "") ?> >
                                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                                <table class="table table-hover" id="educationlist">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Name of School</th>
                                            <th rowspan="2">Educational Level</th>
                                            <th rowspan="2">Course</th>
                                            <th rowspan="2">Status</th>
                                            <th rowspan="2">Units</th>
                                            <th rowspan="2">Year Graduated</th>
<!--                                             
                                            <th colspan="2" class="align_center">Inclusive Date</th>
                                            <th rowspan="2" class="col-md-2">&nbsp;</th>
                                        </tr>
                                  <tr>
                                            <th class="align_center">From</th>
                                            <th class="align_center">To</th>
                                        </tr> -->
                                    </thead>
                                    <tbody>
                                        <?
                                            if(count($educational_background)>0){
                                                foreach($educational_background as $eb){
                                                    ?>
                                                    <tr>
                                                        <td schoolid='<?=$eb->schoolid?>'><?=$eb->schoolDesc?></td>
                                                        <td educl='<?=$eb->educ_level?>'><?=$eb->educ_level?></td>
                                                        <td><?=strtoupper($eb->course)?></td>
                                                        <td completed="<?=$eb->completed?>"><?= ($eb->completed == 1)? 'Completed':'On-Going'; ?></td>
                                                        <td><?=strtoupper($eb->units)?></td>
                                                        <td><?=strtoupper($eb->year_graduated)?></td>
<!--                                                         <td><?=$eb->minor?></td> -->
<!--                                                         <td><?=$eb->datefrom?></td>
                                                        <td><?=$eb->dateto?></td> -->
                                                        <td>
                                                            <div style="float: right">
                                                                <a class='btn btn-info edit_education' tbl_id="<?=$eb->tbid ?>" href='#educationModal' data-toggle='modal' ><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                                                <a  class='btn btn-warning delete_education' tbl_id = "<?=$eb->tbid ?>" ><i class='glyphicon glyphicon-trash'></i></a>
                                                            </div>
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
                                <a type="button" href="#educationModal" tag="add_education" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Educational Attainment</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel animated fadeIn">
                        <div class="panel-heading"><h4><b>Eligibility</b></h4></div>
                        <div class="panel-body" id="eligibilityTable">
                            <div id="eligibilitySpan" style="display: none;">
                                    <span style="color:red">This field is required.</span>
                                </div>
                            <div class="col-md-12">
                                
                                <input type="checkbox" name="eligcbox" id="eligcbox" class="applicable-field" <?= ($eligcbox == "1" ? "checked" : "") ?> >
                                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                                <table class="table table-hover" id="eligibilitieslist">
                                    <thead>
                                    <tr>
                                        <th>Government Examination/Professional Exam Taken</th>
                                        <th>License No.</th>
                                        <th>Issued Date</th>
                                        <th>Expiry Date</th>
                                        <th>Remarks</th>
                                        <th>License Copy</th>
                                        <th class="col-md-2">&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?
                                        if(count($eligibilities)>0){
                                        foreach($eligibilities as $el){
                                        list($el->filename, $el->content, $el->mime) = $this->extensions->getEmployee201Files("applicant_eligibilities", $el->tbid, $applicantId);
                                    ?>
                                        <tr id="<?= $el->tbid ?>">
                                            <td educel='<?=$el->description?>'><?=strtoupper($el->description)?></td>
                                            <td><?=strtoupper($el->license_number)?></td>
                                            <td><?=strtoupper($el->date_issued)?></td>
                                            <td><?=strtoupper($el->date_expired)?></td>
                                            <td><?=strtoupper($el->remarks)?></td>
                                            <td><a class="filenames" content="<?= $el->content ?>" mime="<?= $el->mime ?>"><?= $el->filename ?></a><input class="myInput" id="<?= $el->tbid ?>"  type="file" style="visibility:hidden" required="required"/></td>
                                            <td>
                                                <div style="float: right">
                                                    <a type="button" class='btn btn-info eligibilities' href='#educationModal' data-toggle='modal' tbl_id ="<?= $el->tbid ?>" ><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                                    <a type="button" type="button" class='btn btn-warning delete_eligibilities' tbl_id ="<?= $el->tbid ?>"><i class='glyphicon glyphicon-trash'></i></a>
                                                </div>
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
                                <a type="button" href="#educationModal" tag="add_eligibilities" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Eligibilty</a>
                            </div>
                        </div>
                    </div>

                    <div class="panel animated fadeIn">
                        <div class="panel-heading"><h4><b>Subjects Competent to Teach</b></h4></div>
                        <div class="panel-body" id="scttTable">
                            <div id="scttSpan" style="display: none;">
                                    <span style="color:red">This field is required.</span>
                                </div>
                            <div class="col-md-12">
                                
                                <input type="checkbox" name="scttcbox" id="scttcbox" class="applicable-field" <?= ($scttcbox == "1" ? "checked" : "") ?> >
                                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                                <table class="table table-hover" id="scttlist">
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Remarks</th>
                                            <th class="col-md-2">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?
                                        if(count($sctt)>0){
                                            foreach($sctt as $row){
                                                ?>
                                                <tr>
                                                    <td educel='<?=$row->subj_id?>'><?=strtoupper($row->subj_code)?></td>
                                                    <td><?=strtoupper($row->remarks)?></td>
                                                    <td>
                                                        <div style="float: right;">
                                                            <a type="button" class='btn btn-info sctt' tbl_id="<?= $row->id ?>" href='#educationModal' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                                            <a type="button" class='btn btn-warning delete_scct' tbl_id="<?= $row->id ?>"><i class='glyphicon glyphicon-trash'></i></a>
                                                        </div>
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
                                <a type="button" href="#educationModal" tag="add_sctt" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Subject Competent to Teach</a>
                            </div>
                        </div>
                    </div>


                    <div class="panel animated fadeIn" hidden="">
                        <div class="panel-heading"><h4><b>Other Credentials</b></h4></div>
                        <div class="panel-body" id="otTable">
                            <div id="otSpan" style="display: none;">
                                    <span style="color:red">This field is required.</span>
                                </div>
                            <div class="col-md-12">
                                
                                <input type="checkbox" name="otherCredcbox" id="otherCredcbox" class="applicable-field" <?= ($otherCredcbox == "0" ? "checked" : "") ?> >
                                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                                <table class="table table-hover" id="otlist">
                                    <thead>
                                        <tr>
                                            <th>Skills</th>
                                            <th>Proficiency</th>
                                            <th class="col-md-2">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?
                                    if($oc){
                                        foreach($oc as $row){
                                            ?>
                                            <tr>

                                                <td><?=strtoupper($row->skills)?></td>
                                                <td relprof='<?=$row->profiency?>'><?=strtoupper($row->profiency)?></td>
                                                <td>
                                                    <div style="float: right">
                                                        <a type="button" class='btn btn-info edit_ot' tbl_id="<?=$row->id?>" href='#educationModal' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                                        <a type="button" class='btn btn-warning delete_ot' tbl_id="<?=$row->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                                                    </div>
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
                                <a type="button" href="#educationModal" tag="add_ot" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Other Credentials</a>
                            </div>
                        </div>
                    </div>
                    <!-- OTHER CREDENTIALS END -->

                    <div class="panel animated fadeIn">
                        <div class="panel-heading"><h4><b>Work Experience</b></h4></div>
                        <div class="panel-body" id="wunrelatedTable">
                            <div id="wunrelatedSpan" style="display: none;">
                                    <span style="color:red">This field is required.</span>
                                </div>
                            <div class="col-md-12">
                                
                                <input type="checkbox" name="wunrelatedcbox" id="wunrelatedcbox" class="applicable-field" <?= ($wunrelatedcbox == "1" ? "checked" : "") ?> >
                                <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                                <table class="table table-hover" id="workhistorylistunrelated">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">Position Held</th>
                                            <th rowspan="2">Employer</th>
                                            <th rowspan="2">Inclusive Years</th>
                                            <!-- <th rowspan="2">Latest Salary</th> -->
                                            <th rowspan="2">Reason For Leaving</th>
                                            <!-- <th rowspan="2">Status</th> -->
                                            <!-- <th rowspan="2">Address</th>
                                            <th rowspan="2">Contact Number</th>
                                            <th rowspan="2">Salary</th> 
                                            <th colspan="2" class="align_center">Inclusive Date of Employment</th>
                                            <th class="col-md-2">&nbsp;</th>
                                        </tr>
                                        <tr>
                                            <th class="align_center">From</th>
                                            <th class="align_center">To</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?
                                        if(count($work_history_unrelated)>0){
                                            foreach($work_history_unrelated as $wh){
                                        ?>
                                            <tr>
                                                <td><?=strtoupper($wh->position)?></td>
                                                <td><?=strtoupper($wh->company)?></td>
                                                <td><?=strtoupper($wh->remarks)?></td>
                                                <!-- <td><?= $wh->salary?></td> -->
                                                <td><?=strtoupper($wh->reason)?></td>
                                                <!-- <td><?=$wh->address?></td>
                                                <td><?=$wh->contactnumber?></td>
                                                <td><?=$wh->salary?></td> -->
                                                <!-- <td class="align_center"><?=$wh->date_from?></td>
                                                <td class="align_center"><?=$wh->date_to?></td> -->
                                                <td>
                                                    <div style="float: right;">
                                                        <a type="button" tbl_id="<?=$wh->id?>" class='btn btn-info edit_workhistoryunrelated' href='#educationModal' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                                        <a type="button" tbl_id="<?=$wh->id?>"  class='btn btn-warning delete_workhistoryunrelated'><i class='glyphicon glyphicon-trash'></i></a>
                                                    </div>
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
                                <a type="button" href="#educationModal" tag="add_workhistory_unrelated" data-toggle="modal" class="btn btn-success"><i class="icon glyphicon glyphicon-plus"></i> Add Work Experience</a>

                            </div>
                        </div>
                    </div>
                </div>    
            </div>
        </form>
    </div>
</div>  

<div class="modal fade" id="educationModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">

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
                <center><b><h3 tag="title" class="modal-title">Modal Header</h3></b></center>
            </div>
            <div class="modal-body" style="background-color: white;">
                <div class="row">
                    <div id="display">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</a>
                <a type="button" class="btn btn-success" id='button_save_modal'>Save</a>
            </div>
        </div>

    </div>
</div>

<script>
var isApplicant = "yes";
var toks = hex_sha512(" ");
load2ndTabCBStatus();
 $("#eligibilitieslist").delegate(".filenames", "click", function(){
    var trid = $(this).closest("tr");
      var data = $(trid).find(".filenames").attr("content");
      var mime = $(trid).find(".filenames").attr("mime");
      openFiles(data, mime);
      
});
  function openFiles(data, mime){
      if(data){
      var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
        window.open(objectURL);
      }else{
        var file_url = $(this).attr("content");
        window.open(file_url);
      }
  }
  function b64toBlobs(b64Data, contentType) {
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
validateInputs();

$("#educationModal").find("#button_save_modal").addClass("button_save_modal");
$("#educationModal").find("#modalclose").addClass("modalclose");

$(".delete_entry").click(function(){
    var mtable = $("#scttlist").find("tbody");
    $(this).parent().parent().remove();
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
});
$(".delete_education").click(function(){
    var mtable = $("#educationlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
            $(this).parent().parent().parent().remove();
            delete_education($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Data is safe.',
                'error'
            )
        }
    })
});

$(".delete_eligibilities").click(function(){
    var mtable = $("#eligibilitieslist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
            $(this).parent().parent().parent().remove();
            delete_eligibilities($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Data is safe.',
                'error'
            )
        }
    })
    
});

$(".delete_scct").click(function(){
    var mtable = $("#scttlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
            $(this).parent().parent().parent().remove();
            delete_scct($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Data is safe.',
                'error'
            )
        }
    })
});
$(".education").click(function(){
    addeducation($(this));
});

$("a[tag='add_education']").click(function(){
    addeducation("");
});

$(".edit_education").click(function(){
    addeducation($(this), $(this).attr("tbl_id"));
});

function addeducation(obj, tbl_id=''){
    $("#educationModal").find("h3[tag='title']").text(obj ? "Edit Educational Background" : "Add Educational Background");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant: GibberishAES.enc(isApplicant , toks), toks:toks},
        url: "<?=site_url('employee_/education')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#educationModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("select[name='eb_school']").val(tdcur.find("td:eq(0)").attr("schoolid")).trigger('chosen:updated');
                    $(modal_display).find("select[name='eb_level']").val(tdcur.find("td:eq(1)").attr("educl")).trigger('chosen:updated');
                    $(modal_display).find("input[name='eb_course']").val(tdcur.find("td:eq(2)").text());
                    $(modal_display).find("select[name='completed']").val(tdcur.find("td:eq(3)").attr("completed")).trigger('chosen:updated');
                    $(modal_display).find("input[name='eb_units']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("input[name='eb_dategraduated']").val(tdcur.find("td:eq(5)").text());
                    $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                }else{
                    $("#educationlist").find("tr").each(function(){
                        $(this).attr("iscurrent",0); 
                    }) ;
                }
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
    $("#educationModal").find("h3[tag='title']").text(obj ? "Edit Work Experience" : "Add Work Experience");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant: GibberishAES.enc(isApplicant , toks), toks:toks},
        url: "<?=site_url('employee_/workhistory')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#educationModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("input[name='wh_position']").val(tdcur.find("td:eq(0)").text());
                    $(modal_display).find("input[name='wh_company']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("input[name='wh_remarks']").val(tdcur.find("td:eq(2)").text());
                    $(modal_display).find("input[name='wh_contact']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='wh_salary']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("input[name='wh_datefrom']").val(tdcur.find("td:eq(5)").text()); 
                    $(modal_display).find("input[name='wh_dateto']").val(tdcur.find("td:eq(6)").text());
                }else{
                    $("#workhistorylist").find("tr").each(function(){
                        $(this).attr("iscurrent",0); 
                    }) ;
                }
            });
        }
    });  
}

$(".edit_workhistoryunrelated").click(function(){
    addworkhistoryunrelated($(this), $(this).attr("tbl_id"));
});

$("a[tag='add_workhistory_unrelated']").click(function(){
    addworkhistoryunrelated("");
});

$(".delete_workhistoryunrelated").click(function(){
    var mtable = $("#workhistorylistunrelated").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
        $(this).parent().parent().parent().remove();
        delete_workhistory($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Data is safe.',
                'error'
            )
        }
    })
});

function addworkhistoryunrelated(obj, tbl_id=""){
    $("#educationModal").find("h3[tag='title']").text(obj ? "Edit Work Experience" : "Add Work Experience");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant: GibberishAES.enc(isApplicant , toks), toks:toks},
        url: "<?=site_url('employee_/workhistoryunrelated')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#educationModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("input[name='wh_position']").val(tdcur.find("td:eq(0)").text());
                    $(modal_display).find("input[name='wh_company']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("input[name='wh_remarks']").val(tdcur.find("td:eq(2)").text());
                    // $(modal_display).find("input[name='wh_salary']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='wh_reason']").val(tdcur.find("td:eq(3)").text());
                    
                    // $(modal_display).find("input[name='wh_datefrom']").val(tdcur.find("td:eq(2)").text()); 
                    // $(modal_display).find("input[name='wh_dateto']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                }else{
                    $("#workhistorylistunrelated").find("tr").each(function(){
                        $(this).attr("iscurrent",0); 
                    });
                }
                $(".modalclose").click(function(){
                        $("#workhistorylistunrelated").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
            });
        }
    });  
}

function delete_workhistory(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_work_history_unrelated";
    }
    else{
        table = "employee_work_history_unrelated"; 
    }
    $.ajax({
        url: "<?=  site_url('applicant/deleteData') ?>",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), toks:toks},
        dataType: "JSON",
        success: function(msg){   
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted data.',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });  
}

function delete_education(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_education";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_education"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('applicant/delete_education')?>",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), toks:toks, employeeid: GibberishAES.enc( userid, toks)},
        dataType: "JSON",
        success: function(msg){ 
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted data.',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });  
}

function delete_eligibilities(obj, tbl_id=''){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_eligibilities";
        userid = $("input[name='applicantId']").val();
        }
    else{
        table = "employee_eligibilities"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('applicant/deleteData')?>",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), toks:toks, employeeid: GibberishAES.enc( userid, toks)},
        dataType: "JSON",
        success: function(msg){ 
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted data.',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });
}

function delete_scct(obj, tbl_id=''){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_subj_competent_to_teach";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_subj_competent_to_teach"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('applicant/deleteData')?>",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), toks:toks, employeeid: GibberishAES.enc( userid, toks)},
        dataType: "JSON",
        success: function(msg){ 
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted data.',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });
}

$(".workhistoryrelated").click(function(){
    addworkhistoryrelated($(this));
});

$("a[tag='add_workhistory_related']").click(function(){
    addworkhistoryrelated("");
});

function addworkhistoryrelated(obj){
    $("#educationModal").find("h3[tag='title']").text(obj ? "Edit Work Experience" : "Add Work Experience");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant: isApplicant},
        url: "<?=site_url('employee_/workhistoryrelated')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#educationModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("input[name='wh_position']").val(tdcur.find("td:eq(0)").text());
                    $(modal_display).find("input[name='wh_company']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("input[name='wh_remarks']").val(tdcur.find("td:eq(2)").text());
                    $(modal_display).find("input[name='wh_contact']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='wh_salary']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("input[name='wh_datefrom']").val(tdcur.find("td:eq(5)").text()); 
                    $(modal_display).find("input[name='wh_dateto']").val(tdcur.find("td:eq(6)").text());
                }else{
                    $("#workhistorylistrelated").find("tr").each(function(){
                        $(this).attr("iscurrent",0); 
                    }) 
                }
            });
        }
    });  
}

$(".eligibilities").click(function(){
    addeligibilities($(this), $(this).attr("tbl_id"));
});

$("a[tag='add_eligibilities']").click(function(){
    addeligibilities("");
});

function addeligibilities(obj, tbl_id = ""){
    // if(obj) tbl_id = obj.attr("tbl_id");
    $("#educationModal").find("h3[tag='title']").text(obj ? "Edit Eligibility" : "Add Eligibility");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant:  GibberishAES.enc( isApplicant, toks), toks:toks},
        url: "<?=site_url('employee_/eligibilities')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#educationModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("input[name='tbl_id']").val(tbl_id); 
                    $(modal_display).find("select[name='el_description']").val(tdcur.find("td:eq(0)").attr("educel")).trigger('chosen:updated');
                    /*     $(modal_display).find("option[value="+tdcur.find("td:eq(0)").text()+"]").attr("selected", "selected").trigger("liszt:updated");*/
                    $(modal_display).find("input[name='el_licenseNo']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("input[name='el_issuedDate']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='el_expiryDate']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='el_remarks']").val(tdcur.find("td:eq(4)").text()); 
                    $(modal_display).find("#file_uploaded").attr("file", tdcur.find("td:eq(5)").find("a").attr("content")).attr("mime", tdcur.find("td:eq(5)").find("a").attr("mime"));
                }else{
                    $("#eligibilitieslist").find("tr").each(function(){
                        $(this).attr("iscurrent",0); 
                    });
                }
            });
        }
    });  
}

//SCTT
$(".sctt").click(function(){
    addSctt($(this), $(this).attr("tbl_id"));
});

$("a[tag='add_sctt']").click(function(){
    addSctt("");
});

function addSctt(obj){
    var tbl_id = "";
    if(obj) tbl_id = obj.attr("tbl_id");
    $("#educationModal").find("h3[tag='title']").text(obj ? "Edit Subject Competent to Teach" : "Add Subject Competent to Teach");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant:  GibberishAES.enc( isApplicant, toks), toks:toks},
        url: "<?=site_url('employee_/sctt')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#educationModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    // $(modal_display).find("select[name='el_subj'] :selected").val(tdcur.find("td:eq(0)").attr('educel'  ));
                    // $(modal_display).find("option[value="+tdcur.find("td:eq(0)").attr('educel')+"]").attr("selected", "selected").trigger("liszt:updated");
                    $(modal_display).find("select[name='el_subj']").val(tdcur.find("td:eq(0)").attr("educel")).trigger('chosen:updated');
                    $(modal_display).find("input[name='el_remarks']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                }else{
                    $("#scttlist").find("tr").each(function(){
                    $(this).attr("iscurrent",0); 
                    });
                }
            });
        }
    });  
}

//OT
$(".edit_ot").click(function(){
    addOT($(this), $(this).attr("tbl_id"));
});

$("a[tag='add_ot']").click(function(){
    addOT("");
});

$(".delete_ot").click(function(){
    var mtable = $("#otlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
            $(this).parent().parent().parent().remove();
            deleteOT($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Data is safe.',
                'error'
            )
        }
    })
    
    });

function addOT(obj, tbl_id=""){
    var tbl_id = "";
    if(obj) tbl_id = obj.attr("tbl_id");
    $("#educationModal").find("h3[tag='title']").text(obj ? "Edit Other Credentials" : "Add Other Credentials");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant:  GibberishAES.enc( isApplicant, toks), toks:toks},
        url: "<?=site_url('employee_/oc')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#educationModal").find("#display");
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
                    });
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

function deleteOT(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_credentials";
    }
    else{
        table = "employee_credentials"; 
    }
    $.ajax({
        url: "<?=  site_url('applicant/deleteData') ?>",
        type: "POST",
        data: {table: GibberishAES.enc( table, toks), tbl_id: GibberishAES.enc(tbl_id , toks),toks:toks},
        dataType: "JSON",
        success: function(msg){   
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted data.',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });  
}

function validateInputs(){
    if($("input[name='usertype']").val() == "ADMIN"){
        $("#tab2").find("button, input[type=checkbox]").prop("disabled", true);
    }
}

// function loadSuccessModal(){
//     if(!$('#success_modal').is(':visible')){
//         $("#success_modal").modal("show");
//         setTimeout(function(){ $("#success_modal").modal("hide"); }, 1000);
//     }
// }
function loadSuccessModal(msg){
        // if(!$('#success_modal').is(':visible')){
        //     $("#success_modal").modal("show");
        //     setTimeout(function(){ $("#success_modal").modal("hide"); }, 800);
        // }

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg,
            showConfirmButton: true,
            timer: 1000
        })
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

function load2ndTabCBStatus(){
    if($("#educcbox").prop('checked')){ 
      $("#educationTable a, #educationTable button").attr('disabled', true);
      $("#educationTable a, #educationTable button").css('pointer-events', 'none');
    }
    else{
      $("#educationTable a, #educationTable button").attr('disabled', false);
      $("#educationTable a, #educationTable button").css('pointer-events', '');
    }

    if($("#eligcbox").prop('checked')){ 
      $("#eligibilityTable a, #eligibilityTable button").attr('disabled', true);
      $("#eligibilityTable a, #eligibilityTable button").css('pointer-events', 'none');
    }
    else{
      $("#eligibilityTable a, #eligibilityTable button").attr('disabled', false);
      $("#eligibilityTable a, #eligibilityTable button").css('pointer-events', '');
    }

    if($("#scttcbox").prop('checked')){ 
      $("#scttTable a, #scttTable button").attr('disabled', true);
      $("#scttTable a, #scttTable button").css('pointer-events', 'none');
    }
    else{
      $("#scttTable a, #scttTable button").attr('disabled', false);
      $("#scttTable a, #scttTable button").css('pointer-events', '');
    }

    if($("#wunrelatedcbox").prop('checked')){ 
      $("#wunrelatedTable a, #wunrelatedTable button").attr('disabled', true);
      $("#wunrelatedTable a, #wunrelatedTable button").css('pointer-events', 'none');
    }
    else{
      $("#wunrelatedTable a, #wunrelatedTable button").attr('disabled', false);
      $("#wunrelatedTable a, #wunrelatedTable button").css('pointer-events', '');
    }

}

$("#educcbox").change(function(){
    if($("#educcbox").prop('checked')){ 
      $("#educationTable a, #educationTable button").attr('disabled', true);
      $("#educationTable a, #educationTable button").css('pointer-events', 'none');
      updateCheckBox(1, "educBackground");
    }
    else{
      $("#educationTable a, #educationTable button").attr('disabled', false);
      $("#educationTable a, #educationTable button").css('pointer-events', '');
      updateCheckBox(0, "educBackground");
    }
});

$("#eligcbox").change(function(){
    if($("#eligcbox").prop('checked')){ 
      $("#eligibilityTable a, #eligibilityTable button").attr('disabled', true);
      $("#eligibilityTable a, #eligibilityTable button").css('pointer-events', 'none');
      updateCheckBox(1, "eligibility");
    }
    else{
      $("#eligibilityTable a, #eligibilityTable button").attr('disabled', false);
      $("#eligibilityTable a, #eligibilityTable button").css('pointer-events', '');
      updateCheckBox(0, "eligibility");
    }
});

$("#scttcbox").change(function(){
    if($("#scttcbox").prop('checked')){ 
      $("#scttTable a, #scttTable button").attr('disabled', true);
      $("#scttTable a, #scttTable button").css('pointer-events', 'none');
      updateCheckBox(1, "sctt");
    }
    else{
      $("#scttTable a, #scttTable button").attr('disabled', false);
      $("#scttTable a, #scttTable button").css('pointer-events', '');
      updateCheckBox(0, "sctt");
    }
});

$("#wunrelatedcbox").change(function(){
    if($("#wunrelatedcbox").prop('checked')){ 
      $("#wunrelatedTable a, #wunrelatedTable button").attr('disabled', true);
      $("#wunrelatedTable a, #wunrelatedTable button").css('pointer-events', 'none');
      updateCheckBox(1, "workUnrelated");
    }
    else{
      $("#wunrelatedTable a, #wunrelatedTable button").attr('disabled', false);
      $("#wunrelatedTable a, #wunrelatedTable button").css('pointer-events', '');
      updateCheckBox(0, "workUnrelated");
    }
});

$('.chosen').chosen();

</script>