<?php

/**
 * @author Kennedy
 * @copyright 2019
 */
 
?>
<?php

$educlevel = $this->extras->showreportseduclevel('','eb');
$schoolName = $this->extras->getSchoolName();
 
?>

<style type="text/css">
    .form-group{
      padding-bottom: 10px;
      margin: 0px 0px 0px 0px;
    }
    #form_education{
      margin-top: 30px;
    }
    .cbox{
-ms-transform: scale(1.5); /* IE */
-moz-transform: scale(1.5); /* FF */
-webkit-transform: scale(1.5); /* Safari and Chrome */
-o-transform: scale(1.5); /* Opera */
}

</style>


<input type="hidden" name="tbl_id">
<input type="hidden" name="employeeid_" id="employeeid_">
<input type="hidden" name="isApplicant" id="isApplicant" value="<?= $applicant ?>">
<form id="form_education" class="form-horizontal">
  <div class="form-group">
  <label class="col-sm-4 control-label">Name of School</label>
    <div class="col-sm-7">
        <select class="chosen required" name="eb_school" id="eb_school" required>
              <option value="">Select an Option</option>
            <?php foreach($schoolName as $schoolid => $schooldescription):?>
                <option <?=($schoolName==$schooldescription ? " selected" : "")?> value="<?= $schoolid ?>"><?= $schooldescription ?></option>
            <?php endforeach;?>
        </select>  
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Educational Level</label>
    <div class="col-sm-7">
      <select class="chosen required" name="eb_level" id="eb_level" required>
            <?php foreach($educlevel as $el => $elevel):?>
                <option <?=($educlevel==$elevel ? " selected" : "")?> value="<?= $elevel ?>"><?= $elevel ?></option>
            <?php endforeach;?>
        </select>  
    </div>
  </div>
  <div class="form-group" id="complete_div">
  <label class="col-sm-4 control-label">&nbsp;</label>
  <div class="col-sm-7">
    <input type="checkbox" class="cbox upperCase" name='complete' id="complete" value=""/>&emsp;is complete</input>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Course</label>
  <div class="col-sm-7">
    <input type="text" name="eb_course" class="form-control upperCase" value=""/>
  </div>
</div> 

<div class="form-group" id="unit_complete_div">
  <label class="col-sm-4 control-label">Is Complete</label>
  <div class="col-sm-7">
    <select class="form-control chosen" name="completed" id="completedApplicant">
      <option value="0">Ongoing</option>
      <option value="1">Completed</option>
    </select>
  </div>
</div> 


<div class="form-group" id="units_div">
  <label class="col-sm-4 control-label">Units</label>
  <div class="col-sm-7">
    <input type="number" name="eb_units" class="form-control upperCase" value=""/>
  </div>
</div> 

<div class="form-group">
  <label class="col-sm-4 control-label">Inclusive Years</label>
  <div class="col-sm-7">
        <input type='text' class="form-control upperCase" name="eb_dategraduated" value=""/>
    </div>
  </div>
  <div class="form-group" id="uploaddoc">
        <label class="col-sm-4 control-label">Upload Documents</label>
        <div class="col-sm-7">
            <input type="file" name="el_document" class="form-control file" id="uploadFile" value=""/>
            <label id="file_loading"><img src='<?=base_url()?>images/loading.gif'/>Checking uploaded file/image...</label>
            <label style="display:none;color: blue;text-decoration: underline;" id="file_uploaded">Click to view uploaded image.</label>
        </div>
    </div> 
    <div class="form-group" id="draremarks" style="display: none;">
    <label class="col-sm-4 control-label">Remarks</label>
    <div class="col-sm-7">
      <input type="text" name="dra_remarks" class="form-control upperCase" value=""/>
    </div>
  </div>
  <div class="form-group data_request_details_requested_date" style="display: none;">
            <label class="col-sm-4 control-label">Date Requested</label>
            <div class="col-sm-7">
                <label class="control-label" name="requested_date"></label>
            </div>
        </div>
        <div class="form-group data_request_details_attachment" style="display: none;">
            <label class="col-sm-4 control-label">Attachment</label>
            <div class="col-sm-7">
                <label class="control-label" name="attachment"></label>
            </div>
        </div> 
</div> 


<!-- <div class="form-group">
  <label class="col-sm-4 control-label">Inclusive Dates</label>
  <div class="col-sm-7">
    <div class="col-md-6" style="padding-left: 0px; padding-right: 2px;">
      <div class='input-group date date_from' data-date="" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="eb_datefrom" value=""/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
    <div class="col-md-6"  style="padding-left: 2px; padding-right: 0px">
      <div class='input-group date date_to' data-date="" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="eb_dateto" value=""/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
  </div>
</div> -->
<div id="msg_header" style="display:none;">
  <strong></strong> <span></span>
</div>

</form>
<script>
$(document).ready(function(){
  var toks = hex_sha512(" ");
  if ($("#isApplicant").val() == "yes") {
    $("#units_div, #uploaddoc").hide();
  }else{
    $("#unit_complete_div").show();
  }
  setTimeout(function(){
      var attr = $("#file_uploaded").attr("file");
      if(typeof attr !== typeof undefined && attr !== false && attr != ''){
        $("#file_uploaded").css("display", "unset");
        $("#file_loading").css("display", "none");
      }
      else{
        $.ajax({
              type: "POST",
              url: "<?= site_url('approval_/loadFile')?>",
              data: {id:GibberishAES.enc( $("input[name='tbl_id']").val(), toks), table:GibberishAES.enc( 'employee_education', toks), employeeid:GibberishAES.enc( $("#employeeid_").val(), toks) , toks:toks},
              dataType: "JSON",
              success:function(res){
                if(res[1] && res[2]){
                  $("#file_uploaded").attr("file", res[1]).attr("mime", res[2]);
                  $("#file_uploaded").css("display", "unset");
                  $("#file_loading").css("display", "none");
                }else{
                  $("#file_loading").css("display", "none");
                }
              }
          })
      }
      getDataRequestDetails();
  }, 500);

  function getDataRequestDetails(){
    $.ajax({
         url: "<?=site_url("applicant/getDataRequestDetails")?>",
         data : {table:'employee_education', employeeid: $("#employeeid_").val(), baseid: $("input[name='tbl_id']").val()},
         dataType: 'JSON',
         type : "POST",
         success:function(msg){
            if(msg.err_code == 1){
                $("label[name='requested_date']").text(msg.request_date);
                $(".data_request_details_requested_date").css("display", "block");
                $("label[name='attachment']").text(msg.attachment);
                $(".data_request_details_attachment").css("display", "block");
            }
         }
      }); 
}

  setTimeout(function(){
    isComplete();
      if($("#eb_level").val() != "COLLEGE" && $("#eb_level").val() != "SECONDARY" && $("#eb_level").val() != "VOCATIONAL"){
        $("#units_div").show();
        $("#complete_div").show();
      }
      else{
        $("#units_div").hide();
        $("#complete_div").hide();
      }
  }, 100);
  
});
$("#eb_level").change(function(){
  if($(this).val() == "COLLEGE"){
    $("#units_div").hide();
    $("#complete_div").hide();
  }else if($(this).val() == "VOCATIONAL"){
    $("#units_div").hide();
    $("#complete_div").hide();
  }else if($(this).val() == "SECONDARY"){
    $("#units_div").hide();
    $("#complete_div").hide();
  }
  else{
    $("#complete_div").hide();
    $("#units_div").show();
    if($(this).val() == "MASTERAL") $("#complete_div").show();
  }
});

var base64String = '';
if (window.File && window.FileReader && window.FileList && window.Blob) 
{
    document.getElementById('uploadFile').addEventListener('change', handleFileSelect, false);
} else 
{
    // alert('The File APIs are not fully supported in this browser.');
    Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'The File APIs are not fully supported in this browser.',
          showConfirmButton: true,
          timer: 1000
      })
}

$(".button_save_modal").unbind("click").click(function(){
 var tbl_id = "";
 var table = "";
 var userid = "";
 var status = "";
 var completed = "";
 var display = "";
 var units = $("input[name='eb_units']").val();
 var isApplicant = $("#isApplicant").val();
 if (isApplicant == 'yes') {
  completed = $("#completedApplicant").val();
  if (completed == 1) {
    display = "Completed";
  }else{
    display = "OnGoing";
  }
}else {
  completed = ($("#complete").prop("checked") == true) ? 1 : 0 ;
    display = units;
}

 if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
 else status = "PENDING";
 if($("input[name='applicantId']").val()){
  table = "applicant_education";
  userid = $("input[name='applicantId']").val();
 }
 else{
  table = "employee_education"; 
  userid = $("input[name='employeeid']").val();
 }

if($("#completedApplicant").val() == 0){
  var $validator = $("#form_education").validate({
        rules: {
            eb_level: {
              required: true
            },
            eb_school: {
              required: true,
              minlength: 2
            },
            eb_course: {
              required: true
            },
            eb_units: {
              required: true
            },
            eb_datefrom: {
              required: true,
              minlength: 2
            },
            eb_dateto: {
              required: true,
              minlength: 2
            }
        }
    });
}else{
  var $validator = $("#form_education").validate({
        rules: {
            eb_level: {
              required: true
            },
            eb_school: {
              required: true,
              minlength: 2
            },
            eb_course: {
              required: true
            },
            // eb_units: {
            //   required: true
            // },
            eb_dategraduated: {
              required: true
            },
            eb_datefrom: {
              required: true,
              minlength: 2
            },
            eb_dateto: {
              required: true,
              minlength: 2
            }
        }
    });
}


      var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('schoolid',  GibberishAES.enc($("select[name='eb_school']").val() , toks));
            fd.append('educ_level',  GibberishAES.enc($("select[name='eb_level'] :selected").text(), toks));
            fd.append('course',  GibberishAES.enc($("input[name='eb_course']").val() , toks));
            fd.append('units',  GibberishAES.enc(units, toks));
            fd.append('date_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('year_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('completed',  GibberishAES.enc($("#completedApplicant").val(), toks));
            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('status',  GibberishAES.enc( status, toks));
            fd.append('dra_remarks',  GibberishAES.enc( $("input[name='dra_remarks']").val(), toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('schoolid',  GibberishAES.enc($("select[name='eb_school']").val() , toks));
            fd.append('educ_level',  GibberishAES.enc($("select[name='eb_level'] :selected").text(), toks));
            fd.append('course',  GibberishAES.enc($("input[name='eb_course']").val() , toks));
            fd.append('units',  GibberishAES.enc(units, toks));
            fd.append('date_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('year_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('completed',  GibberishAES.enc($("#completedApplicant").val(), toks));
            fd.append('status',  GibberishAES.enc( status, toks));
            fd.append('dra_remarks',  GibberishAES.enc( $("input[name='dra_remarks']").val(), toks));
            fd.append('toks', toks);


        }
   if($("#form_education").valid()){
      var cobj = "";
      $("#educationlist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("#eb_school option:selected").text());
         $(cobj).find("td:eq(0)").attr("schoolid", $("select[name='eb_school']").val()); 
         $(cobj).find("td:eq(1)").text($("select[name='eb_level']").val());
         $(cobj).find("td:eq(2)").text($("input[name='eb_course']").val()); 
         $(cobj).find("td:eq(3)").text(display);  
         $(cobj).find("td:eq(4)").text($("input[name='eb_dategraduated']").val());  
          $(cobj).find("td:eq(5)").attr("completed", $("#completedApplicant").val()); 
         if ($("#uploadFile").val() != "") {
              $(cobj).find("td:eq(5)").html("<a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</a>");
          }
          if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(cobj).find("td:eq(7)").text($("input[name='dra_remarks']").val()); 
         // $(cobj).find("td:eq(5)").text($("input[name='eb_datefrom']").val());                                  
         // $(cobj).find("td:eq(6)").text($("input[name='eb_dateto']").val());                                  
         /*save/update data first*/
          $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: fd,
            dataType: "json",
            processData:false,
            contentType:false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 $(".modalclose").click();
                 Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                 if(table == "employee_education"){
                    loadTable('employee_education_table');
                  }
                 return false;
              }else{
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text(response.msg);
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                return;
              }
            }
          });
      }else{  
        /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: fd,
            dataType: "json",
            processData:false,
            contentType:false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 $(".modalclose").click();
                 Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully added data.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                 if(table == "employee_education"){
                    loadTable('employee_education_table');
                  }
                 return false;
              }else{
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text(response.msg);
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                return;
              }
            }
         });
   

          var mtable = $("#educationlist").find("tbody");
          if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
          var ntr = $("<tr></tr>");
          $(ntr).append("<td schoolid="+$("select[name='eb_school']").val()+">"+$("#eb_school option:selected").text()+"</td>");
          if(isApplicant == "yes"){
            $(ntr).append("<td reldata="+$("select[name='eb_level']").val()+" educl="+$("select[name='eb_level']").val()+">"+$("#eb_level option:selected").text()+"</td>");
           /* $(ntr).append("<td>"+$("select[name='eb_level']").text()+"</td>");*/

            $(ntr).append("<td>"+$("input[name='eb_course']").val().toUpperCase()+"</td>");
            $(ntr).append("<td>"+display+"</td>");
            $(ntr).append("<td>"+$("input[name='eb_units']").val()+"</td>");
            $(ntr).append("<td>"+$("input[name='eb_dategraduated']").val().toUpperCase()+"</td>");
            // $(ntr).append("<td><a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</td>");
            if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
          }else{
            $(ntr).append("<td reldata="+$("select[name='eb_level']").val()+">"+$("#eb_level option:selected").text()+"</td>");
           /* $(ntr).append("<td>"+$("select[name='eb_level']").text()+"</td>");*/

            $(ntr).append("<td>"+$("input[name='eb_course']").val()+"</td>");
            $(ntr).append("<td>"+$("input[name='eb_units']").val()+"</td>");
            $(ntr).append("<td>"+$("input[name='eb_dategraduated']").val()+"</td>");
            $(ntr).append("<td  completed='"+$("#completedApplicant").val()+"'><a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</td>");
            if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
            else if ("<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") $(ntr).append("<td><a class='btn btn-danger' style='pointer-events:none;'>"+status+"</td>");
            if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td>"+$("input[name='dra_remarks']").val()+"</td>");
            else $(ntr).append("<td></td>");
          }
            
          // else  $(ntr).append("<td><a>"+status+"</a></td>");
          // $(ntr).append("<td>"+$("input[name='eb_datefrom']").val()+"</td>");
          // $(ntr).append("<td>"+$("input[name='eb_dateto']").val()+"</td>");
         
          var mtd = $("<td></td>");
          if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN" || "<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") {
           $("<div style='float: right;'><a class='btn btn-warning delete_educlevel' tbl_id = '"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
              var mtable = $("#educationlist").find("tbody");
              if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
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
                  if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
                  $(this).parent().parent().remove();
                  if(isApplicant == "yes") delete_education($(this), tbl_id);
                  else deleteEduclevel($(this), tbl_id);  
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
            }).appendTo($(mtd));
           $("<div style='float: right;'><a class='btn btn-primary edit_educlevel' tbl_id = '"+tbl_id+"' href='#modal-view' data-toggle='modal' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a></div>").click(function(){
              addeducation($(this).children(), tbl_id);
           }).appendTo($(mtd));
          }
          else{
            $("<div style='float: right;'><a class='btn btn-warning delete_education' tbl_id = '"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
              var mtable = $("#educationlist").find("tbody");
              if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
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
                  $(this).parent().parent().remove();
                  delete_education($(this), tbl_id);
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
            }).appendTo($(mtd));
           $("<div style='float: right;'><a class='btn btn-info edit_education' tbl_id = '"+tbl_id+"' href='#educationModal' data-toggle='modal' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a></div>").click(function(){
              addeducation($(this).children(), tbl_id);
           }).appendTo($(mtd));
          }
          $(ntr).append($(mtd));
          $(ntr).appendTo($("#educationlist").find("tbody"));
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
   
});

function isComplete(){
  var tbl = '';
  if($("input[name='applicantId']").val()){
    tbl = "applicant_education";
   }
   else{
    tbl = "employee_education"; 
   }
  $.ajax({
    url: "<?= site_url('employee_/isComplete') ?>",
    type: "POST",
    data: {id:GibberishAES.enc( $("input[name='tbl_id']").val() , toks), tbl: GibberishAES.enc(tbl , toks), toks:toks },
    success:function(res){
      if(res == 1){
        $("#complete").prop("checked", true);
      }
    }
  })
}
// $(".date").datetimepicker({
//     format: "YYYY-MM-DD"
// });
$('.chosen').chosen();

function handleFileSelect(evt) {
        var fileName = $("#uploadFile").val();
        var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
        var newfilename = fileName.split('.').shift().replace(/^.*\\/, "");
        if(fileNameExt == "heic") {
            var blob = $("#uploadFile")[0].files[0]; //ev.target.files[0];
            heic2any({
                blob: blob,
                toType: "image/jpg",
            })
                .then(function (resultBlob) {
                    var url = URL.createObjectURL(resultBlob);
                    let fileInputElement = $("#uploadFile")[0];
                    let container = new DataTransfer();
                    let file = new File([resultBlob], newfilename+".jpg",{type:"image/jpeg", lastModified:new Date().getTime()});
                    container.items.add(file);

                    fileInputElement.files = container.files;
                     var f = file;
                    var reader = new FileReader();
                    reader.onload = (function(theFile) {
                        return function(e) {
                            var binaryData = e.target.result;
                            base64String = window.btoa(binaryData);
                        };
                    })(f);
                    reader.readAsBinaryString(f);
                })
                .catch(function (x) {
                    console.log(x.code);
                    console.log(x.message);
                });
        }else{
            var f = evt.target.files[0];
            var reader = new FileReader();
            reader.onload = (function(theFile) {
                return function(e) {
                    var binaryData = e.target.result;
                    base64String = window.btoa(binaryData);
                };
            })(f);
            reader.readAsBinaryString(f);
        }
    }

    $("#file_uploaded").click(function(){
        if($(this).attr("file")){
            var data = $(this).attr("file");
            var mime = $(this).attr("mime");
            var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';

            window.open(objectURL);
        }else{
            var file_url = $(this).attr("content");
            window.open(file_url);
        }
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
</script>