<?php

/**
 * @author Justin
 * @copyright 2016
 */
$sm_datef = date("Y-m-d");
$SchoType = $this->extras->showreportseduclevel(' - Select Type - ','S');
?>
<input type="hidden" name="tbl_id">
<input type="hidden" name="employeeid_" id="employeeid_">
<form id="myForm" class="form-horizontal">

  <div class="form-group">
    <label class="col-sm-4 control-label">Type of Scholarship</label>
    <div class="col-sm-7">
      <input type="text" name="sm_type_of_scho" id="sm_type_of_scho" class="form-control" value=""/>
      <!-- <select class="form-control" name="sm_type_of_scho" id="sm_type_of_scho">
      <?php foreach($SchoType as $st => $Scho):?>
          <option <?=($st==$SchoType ? " selected" : "")?> value="<?= $st ?>"><?= $Scho ?></option>
      <?php endforeach;?>
    </select> -->
    </div>
  </div>

  <div class="form-group">
  <label class="col-sm-4 control-label">Granting Agency</label>
  <div class="col-sm-7">
    <input type="text" name="sm_gr_agency" id="sm_gr_agency" class="form-control" value=""/>
  </div>
</div>



  <div class="form-group">
  <label class="col-sm-4 control-label">Program of Study</label>
  <div class="col-sm-7">
    <input type="text" name="sm_prog_study" id="sm_prog_study" class="form-control" value=""/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Institution</label>
  <div class="col-sm-7">
    <input type="text" name="sm_ins_scho" id="sm_ins_scho" class="form-control" value=""/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Inclusive dates</label>
  <div class="col-sm-7">
    <div class="col-md-6" style="padding-left: 0px; padding-right: 10px;">
      <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($sm_datef))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="sm_datef" id="sm_datef" value="<?=date("Y-m-d",strtotime($sm_datef))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
    <div class="col-md-6" style="padding-left: 10px; padding-right: 0px;">
      <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($sm_datef))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="sm_datef_to" id="sm_datef_to" value="<?=date("Y-m-d",strtotime($sm_datef))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
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
            <label class="col-sm-4 control-label">Admin Remarks</label>
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

</form>
<script>

  $(document).ready(function(){
    var toks = hex_sha512(" ");
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
              data: {id:GibberishAES.enc( $("input[name='tbl_id']").val(), toks), table:GibberishAES.enc( 'employee_scholarship', toks), employeeid:GibberishAES.enc( $("#employeeid_").val(), toks) , toks:toks},
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
});

  function getDataRequestDetails(){
    $.ajax({
         url: "<?=site_url("applicant/getDataRequestDetails")?>",
         data : {table:'employee_scholarship', employeeid: $("#employeeid_").val(), baseid: $("input[name='tbl_id']").val()},
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

$('#sm_datef,#sm_datef_to').blur(function(){
      var d1 = new Date($("#sm_datef").val());
      var d2 = new Date($("#sm_datef_to").val());
      if(d1 > d2){
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: "Please fill-up a valid date.",
              showConfirmButton: true,
              timer: 2000
          })
          $(this).val("");
      }
  });

$(".button_save_modal").unbind("click").click(function(){
  var tbl_id = "";
 var table = "";
 var userid = "";
  var status = "";
  if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
  else status = "PENDING";
 if($("input[name='applicantId']").val()){
  table = "applicant_scholarship";
  userid = $("input[name='applicantId']").val();
 }
 else{
  table = "employee_scholarship"; 
  userid = $("input[name='employeeid']").val();
 }

 var $validator = $("#myForm").validate({
        rules: {
            // sm_type_of_scho: {
            //   required: true
            // },
            sm_gr_agency: {
              required: true
            },
            sm_prog_study: {
              required: true
            },
            sm_ins_scho: {
              required: true
            }
        }
    });

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

             fd.append('type_of_scho',  GibberishAES.enc($("input[name='sm_type_of_scho']").val(), toks));
            fd.append('gr_agency',  GibberishAES.enc($("input[name='sm_gr_agency']").val() , toks));
            fd.append('prog_study',  GibberishAES.enc($("input[name='sm_prog_study']").val() , toks));
            fd.append('ins_scho',  GibberishAES.enc($("input[name='sm_ins_scho']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('dateto',  GibberishAES.enc($("input[name='sm_datef_to']").val(), toks));

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

             fd.append('type_of_scho',  GibberishAES.enc($("input[name='sm_type_of_scho']").val(), toks));
            fd.append('gr_agency',  GibberishAES.enc($("input[name='sm_gr_agency']").val() , toks));
            fd.append('prog_study',  GibberishAES.enc($("input[name='sm_prog_study']").val() , toks));
            fd.append('ins_scho',  GibberishAES.enc($("input[name='sm_ins_scho']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('dateto',  GibberishAES.enc($("input[name='sm_datef_to']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc( $("input[name='dra_remarks']").val(), toks));


            fd.append('status',  GibberishAES.enc( status, toks));
            fd.append('toks', toks);
          }
    
   if($("#myForm").valid()){
      var cobj = "";
      $("#schoinfolist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         // $(cobj).find("td:eq(0)").text($("#sm_type_of_scho option:selected").text());
         // $(cobj).find("td:eq(0)").attr("relscho", $("select[name='sm_type_of_scho']").val()); 
          $(cobj).find("td:eq(0)").text($("input[name='sm_type_of_scho']").val());
         $(cobj).find("td:eq(1)").text($("input[name='sm_gr_agency']").val());
         $(cobj).find("td:eq(2)").text($("input[name='sm_prog_study']").val());
         $(cobj).find("td:eq(3)").text($("input[name='sm_ins_scho']").val());         
         $(cobj).find("td:eq(4)").text($("input[name='sm_datef']").val());
         $(cobj).find("td:eq(5)").text($("input[name='sm_datef_to']").val());

         if ($("#uploadFile").val() != "") {
              $(cobj).find("td:eq(6)").html("<a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</a>");
          }
          if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(cobj).find("td:eq(8)").text($("input[name='dra_remarks']").val()); 

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
                 if(table == "employee_scholarship"){
                    tableData('employee_scholarship_tbody');
                  }
                 loadSuccessModal('Successfully updated data!');
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
                 if(table == "employee_scholarship"){
                    tableData('employee_scholarship_tbody');
                  }
                 loadSuccessModal('Successfully saved data!');
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

         var mtable = $("#schoinfolist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         // $(ntr).append("<td relscho="+$("select[name='sm_type_of_scho']").val()+">"+$("#sm_type_of_scho option:selected").text()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_type_of_scho']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_gr_agency']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_prog_study']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_ins_scho']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_datef']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_datef_to']").val()+"</td>");
         $(ntr).append("<td><a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
         else if ("<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") $(ntr).append("<td><a class='btn btn-danger' style='pointer-events:none;'>"+status+"</td>");
         else $(ntr).append("<td><a>"+status+"</a></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td>"+$("input[name='dra_remarks']").val()+"</td>");
         else $(ntr).append("<td></td>");

         var mtd = $("<td class='align_center'></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN" || "<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") {
           $("<div id='btn_pos'><a class='btn btn-warning delete_scho' tbl_id='"+ tbl_id +"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
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
                  if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
                  $(this).parent().parent().remove();
                  deleteScho($(this), tbl_id); 
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
           $("<div id='btn_pos'><a class='btn btn-primary edit_scho' tbl_id='"+ tbl_id +"' href='#modal-view' data-toggle='modal' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
              addScho($(this).children(), tbl_id);
           }).appendTo($(mtd));
         }
         $(ntr).append($(mtd));
         
         $(ntr).appendTo($(mtable));      
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$('.chosen').chosen();
</script>