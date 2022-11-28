<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
$from = date("Y-m-d"); 
$to = date("Y-m-d");
?>

<style type="text/css">
    .form-group{
      padding-bottom: 10px;
      margin: 0px 0px 0px 0px;
    }
    #form_workhistory{
      margin-top: 10px;
    }
    .modal-overflow .modal-body{
      margin-bottom: 0px;
    }

    label.error {
     margin-left: 0px; 
    }

</style>
 <input type="hidden" name="employeeid_" id="employeeid_">
<form id="form_workhistory" class="form-horizontal">
  <input type="hidden" name="tbl_id">

  <div class="form-group">
  <label class="col-sm-4 control-label">Position Held</label>
    <div class="col-sm-7">
      <input type="text" name="wh_position" class="form-control" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Company Name</label>
    <div class="col-sm-7">
      <input type="text" name="wh_company" class="form-control" value=""/>
    </div>
  </div> 

<!--   <div class="form-group">
  <label class="col-sm-4 control-label">Address</label>
    <div class="col-sm-7">
      <input type="text" name="wh_address" class="form-control" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Contact Number</label>
    <div class="col-sm-7">
      <input type="text" name="wh_contact" class="form-control" id="wh_contact" value=""/>
    </div>
  </div> 
  -->

  <div class="form-group">
  <label class="col-sm-4 control-label">Inclusive Years</label>
    <div class="col-sm-7">
      <input type="text" name="wh_remarks" class="form-control" value=""/>
    </div>
  </div> 

<!--   <div class="form-group">
  <label class="col-sm-4 control-label">Salary</label>
    <div class="col-sm-7">
      <input type="number" name="wh_salary" class="form-control" value=""/>
    </div>
  </div> -->

  <div class="form-group">
  <label class="col-sm-4 control-label">Reason For Leaving</label>
    <div class="col-sm-7">
      <input type="text" name="wh_reason" class="form-control" value=""/>
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

<!--<div class="form-group">
  <label class="col-sm-4 control-label">Inclusive Dates</label>
  <div class="col-sm-7">
    <div class="col-md-6" style="padding-left: 0px; padding-right: 2px">
      <div class='input-group date date_from' data-date="<?=date("Y-m-d",strtotime($from))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="wh_datefrom" value="<?=date("Y-m-d",strtotime($from))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
    <div class="col-md-6" style="padding-left: 2px; padding-right: 0px;">
      <div class='input-group date date_to' data-date="<?=date("Y-m-d",strtotime($to))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="wh_dateto" value="<?=date("Y-m-d",strtotime($to))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>-->
          </span>
      </div>
    </div>
  </div>
</div>

</form>
<script>
  $(document).ready(function(){
  $("#wh_contact").inputmask("mask", {"mask": " +63(999)-999-9999"});
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
              data: {id:GibberishAES.enc( $("input[name='tbl_id']").val(), toks), table:GibberishAES.enc( 'employee_work_history_related', toks), employeeid:GibberishAES.enc( $("#employeeid_").val(), toks) , toks:toks},
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
         data : {table:'employee_work_history_related', employeeid: $("#employeeid_").val(), baseid: $("input[name='tbl_id']").val()},
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

$("#button_save_modal").unbind("click").click(function(){
var tbl_id = "";
var table = "";
var userid = "";
var status = "";
  if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
  else status = "PENDING";
if($("input[name='applicantId']").val()){
  table = "applicant_work_history_related";
  userid = $("input[name='applicantId']").val();
}
else{
  table = "employee_work_history_related"; 
  userid = $("input[name='employeeid']").val();
}
 var $validator = $("#form_workhistory").validate({
        rules: {
            eb_level: {
              required: true
            },
            wh_position: {
              required: true,
              minlength: 2
            },
            wh_company: {
              required: true,
              minlength: 2
            },
/*            wh_address: {
              required: true,
              minlength: 2
            },
            wh_contact: {
              required: true,
              minlength: 2
            },
            wh_datefrom: {
              required: true,
            },*/
            // wh_remarks: {
            //   required: true,
            // },
            // wh_salary: {
            //   required: true,
            //   minlength: 2
            // },
            wh_reason: {
              required: true,
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

            fd.append('position',  GibberishAES.enc($("input[name='wh_position']").val(), toks));
            fd.append('company',  GibberishAES.enc($("input[name='wh_company']").val() , toks));
            // fd.append('salary',  GibberishAES.enc($("input[name='wh_salary']").val(), toks));
            fd.append('remarks',  GibberishAES.enc($("input[name='wh_remarks']").val() , toks));
            fd.append('reason',  GibberishAES.enc($("input[name='wh_reason']").val(), toks));

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

            fd.append('position',  GibberishAES.enc($("input[name='wh_position']").val(), toks));
            fd.append('company',  GibberishAES.enc($("input[name='wh_company']").val() , toks));
            // fd.append('salary',  GibberishAES.enc($("input[name='wh_salary']").val(), toks));
            fd.append('remarks',  GibberishAES.enc($("input[name='wh_remarks']").val() , toks));
            fd.append('reason',  GibberishAES.enc($("input[name='wh_reason']").val(), toks));

            fd.append('status',  GibberishAES.enc( status, toks));
            fd.append('dra_remarks',  GibberishAES.enc( $("input[name='dra_remarks']").val(), toks));

            fd.append('toks', toks);
          }
          
    
   if($("#form_workhistory").valid()){
      var cobj = "";
      $("#workhistorylistrelated").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='wh_position']").val());
         $(cobj).find("td:eq(1)").text($("input[name='wh_company']").val());                                    
         // $(cobj).find("td:eq(2)").text($("input[name='wh_address']").val());
         // $(cobj).find("td:eq(3)").text($("input[name='wh_contact']").val());
         $(cobj).find("td:eq(2)").text($("input[name='wh_remarks']").val());
         // $(cobj).find("td:eq(3)").text($("input[name='wh_salary']").val());
         $(cobj).find("td:eq(3)").text($("input[name='wh_reason']").val());
         if ($("#uploadFile").val() != "") {
              $(cobj).find("td:eq(4)").html("<a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</a>");
          }
          if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(cobj).find("td:eq(6)").text($("input[name='dra_remarks']").val()); 

         //$(cobj).find("td:eq(4)").text($("input[name='wh_dateto']").val());

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
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                 if(table == "employee_work_history_related"){
                    loadTable('employee_work_history_related_table');
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
                          text: 'Successfully saved data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                 if(table == "employee_work_history_related"){
                    loadTable('employee_work_history_related_table');
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

         var mtable = $("#workhistorylistrelated").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='wh_position']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='wh_company']").val()+"</td>");
/*         $(ntr).append("<td>"+$("input[name='wh_address']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='wh_contact']").val()+"</td>");
         */
         $(ntr).append("<td>"+$("input[name='wh_remarks']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='wh_salary']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='wh_reason']").val()+"</td>");
         $(ntr).append("<td><a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
         else if ("<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") $(ntr).append("<td><a class='btn btn-danger' style='pointer-events:none;'>"+status+"</td>");
         else $(ntr).append("<td><a>"+status+"</a></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td>"+$("input[name='dra_remarks']").val()+"</td>");
         else $(ntr).append("<td></td>");

         //$(ntr).append("<td class='align_center'>"+$("input[name='wh_datefrom']").val()+"</td>"); 
         //$(ntr).append("<td class='align_center'>"+$("input[name='wh_dateto']").val()+"</td>");
         
         var mtd = $("<td></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN" || "<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") {
           $("<div style='float:right;'><a class='btn btn-warning delete_whr' tbl_id='"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
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
                deleteWHR($(this), tbl_id); 
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
           $("<div style='float: right;'><a class='btn btn-primary edit_whr' style='margin-right: 10px;' href='#modal-view' data-toggle='modal' tbl_id='"+tbl_id+"'><i class='glyphicon glyphicon-edit'></i></a></div>").click(function(){
              addworkhistoryrelated($(this).children(), tbl_id);
           }).appendTo($(mtd));
         }
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#workhistorylistrelated").find("tbody"));      
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