<?php
$sm_datef = date("Y-m-d");
$venLevel = $this->extras->showreportseduclevel(' - Select Venue - ','PTS');
$titleLevel = $this->extras->showreportseduclevel(' - Select PROFESSIONAL DEVELOPMENT PROGRAM - ','PTS_PDP1');
?>
<input type="hidden" name="tbl_id">
<input type="hidden" name="employeeid_" id="employeeid_">
<form id="myForm" class="form-horizontal">
<!--   <div class="form-group" hidden="">
    <label class="col-sm-3 control-label">Title</label>
    <div class="col-sm-7">
      <select class="form-control" name="sm_title" id="sm_title" required>
                <?php foreach($titleLevel as $tl => $title):?>
                    <option <?=($tl==$titleLevel ? " selected" : "")?> value="<?= $tl ?>"><?= $title ?></option>
                <?php endforeach;?>
      </select> 
    </div>
  </div> -->

  <div class="form-group">
    <label class="col-sm-3 control-label">Seminar Title</label>
    <div class="col-sm-7">
      <input type="text" name="sm_semtitle" id="sm_semtitle" class="form-control" value=""/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Location</label>
    <div class="col-sm-7">
      <input type="text" name="sm_location" id="sm_location" class="form-control" value=""/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Date From</label>
    <div class="col-sm-7">
      <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($sm_datef))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="sm_datef" id="sm_datef" value="<?=date("Y-m-d",strtotime($sm_datef))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Date To</label>
    <div class="col-sm-7">
      <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($sm_datef))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="sm_datet" id="sm_datet" value="<?=date("Y-m-d",strtotime($sm_datef))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Organizer</label>
    <div class="col-sm-7">
      <input type="text" name="sm_organizer" id="sm_organizer" class="form-control" value=""/>
    </div>
  </div>
 <!--  <div class="form-group">
    <label class="col-sm-3 control-label">Venue</label>
    <div class="col-sm-7">
      <select class="form-control" name="sm_venue" id="sm_venue" required>
                <?php foreach($venLevel as $ven => $venue):?>
                    <option <?=($ven==$venLevel ? " selected" : "")?> value="<?= $ven ?>"><?= $venue ?></option>
                <?php endforeach;?>
      </select> 
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">Registration Fee</label>
    <div class="col-sm-7">
      <input type="text" name="sm_registration" id="sm_registration" class="form-control groupOfTexbox" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value=""/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Transportation</label>
    <div class="col-sm-7">
      <input type="text" name="sm_transportation" id="sm_transportation" class="form-control groupOfTexbox" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value=""/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Accommodation</label>
    <div class="col-sm-7">
      <input type="text" name="sm_accommodation" id="sm_accommodation" class="form-control groupOfTexbox" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" value=""/>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Total</label>
    <div class="col-sm-7">
      <input type="text" name="sm_total" id="sm_total" class="form-control" value="" style="pointer-events: none;" />
    </div>
  </div> -->
  <div class="form-group" id="uploaddoc">
        <label class="col-sm-3 control-label">Upload Documents</label>
        <div class="col-sm-7">
            <input type="file" name="el_document" class="form-control file" id="uploadFile" value=""/>
            <label id="file_loading"><img src='<?=base_url()?>images/loading.gif'/>Checking uploaded file/image...</label>
            <label style="display:none;color: blue;text-decoration: underline;" id="file_uploaded">Click to view uploaded image.</label>
        </div>
    </div>
    <div class="form-group" id="draremarks" style="display: none;">
            <label class="col-sm-3 control-label">Admin Remarks</label>
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
              data: {id:GibberishAES.enc( $("input[name='tbl_id']").val(), toks), table:GibberishAES.enc( 'employee_pts_pdp1', toks), employeeid:GibberishAES.enc( $("#employeeid_").val(), toks) , toks:toks},
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
         data : {table:'employee_pts_pdp1', employeeid: $("#employeeid_").val(), baseid: $("input[name='tbl_id']").val()},
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

  $('#sm_datef,#sm_datet').blur(function(){
      var d1 = new Date($("#sm_datef").val());
      var d2 = new Date($("#sm_datet").val());
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

  $('.groupOfTexbox').on('change', function(){
    var regFee = (isNaN(parseFloat($('#sm_registration').val()))) ? 0 : parseFloat($('#sm_registration').val());
    var accFee = (isNaN(parseFloat($('#sm_accommodation').val()))) ? 0 : parseFloat($('#sm_accommodation').val());
    var transFee = (isNaN(parseFloat($('#sm_transportation').val()))) ? 0 : parseFloat($('#sm_transportation').val());
    var totals = regFee + accFee + transFee;
    $('#sm_total').val(totals); 
  })
$(".button_save_modal").unbind("click").click(function(){
  var tbl_id = "";
  var table = "";
  var userid = "";
  var status = "";
  if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
  else status = "PENDING";
 if($("input[name='applicantId']").val()){
  table = "applicant_pts_pdp1";
  userid = $("input[name='applicantId']").val();
 }
 else{
  table = "employee_pts_pdp1"; 
  userid = $("input[name='employeeid']").val();
 }

 var $validator = $("#myForm").validate({
        rules: {
            sm_title: {
              required: true
            },
            sm_datef: {
              required: true
            },
            sm_datet: {
              required: true
            },
            sm_organizer: {
              required: true
            },
            // sm_venue: {
            //   required: true
            // },
            sm_semtitle: {
              required: true
            },
            sm_location: {
              required: true
            }
            // sm_registration: {
            //   required: true
            // },
            // sm_accommodation: {
            //   required: true
            // },
            // sm_transportation: {
            //   required: true
            // },
            // sm_total: {
            //   required: true
            // }
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

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            // fd.append('venue',  GibberishAES.enc($("select[name='sm_venue']").val(), toks));
            fd.append('datet',  GibberishAES.enc($("input[name='sm_datet']").val(), toks));
            fd.append('seminar_title',  GibberishAES.enc($("input[name='sm_semtitle']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val() , toks));
            // fd.append('regfee',  GibberishAES.enc($("input[name='sm_registration']").val(), toks));
            // fd.append('transfee',  GibberishAES.enc($("input[name='sm_transportation']").val() , toks));
            // fd.append('accfee',  GibberishAES.enc($("input[name='sm_accommodation']").val() , toks));
            // fd.append('total',  GibberishAES.enc($("input[name='sm_total']").val(), toks));

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

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            // fd.append('venue',  GibberishAES.enc($("select[name='sm_venue']").val(), toks));
            fd.append('datet',  GibberishAES.enc($("input[name='sm_datet']").val(), toks));
            fd.append('seminar_title',  GibberishAES.enc($("input[name='sm_semtitle']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val() , toks));
            // fd.append('regfee',  GibberishAES.enc($("input[name='sm_registration']").val(), toks));
            // fd.append('transfee',  GibberishAES.enc($("input[name='sm_transportation']").val() , toks));
            // fd.append('accfee',  GibberishAES.enc($("input[name='sm_accommodation']").val() , toks));
            // fd.append('total',  GibberishAES.enc($("input[name='sm_total']").val(), toks));

            fd.append('status',  GibberishAES.enc( status, toks));
            fd.append('dra_remarks',  GibberishAES.enc( $("input[name='dra_remarks']").val(), toks));

            fd.append('toks', toks);
          }
    
   if($("#myForm").valid()){
      var cobj = "";
      $("#pts_pdp1infolist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         // $(cobj).find("td:eq(0)").text($("#sm_title option:selected").text());
         // $(cobj).find("td:eq(0)").attr("reltitle", $("select[name='sm_title']").val());  
         $(cobj).find("td:eq(0)").text($("input[name='sm_semtitle']").val());
         $(cobj).find("td:eq(1)").text($("input[name='sm_location']").val());
         $(cobj).find("td:eq(2)").text($("input[name='sm_datef']").val());
         $(cobj).find("td:eq(3)").text($("input[name='sm_datet']").val());
         $(cobj).find("td:eq(4)").text($("input[name='sm_organizer']").val());
         // $(cobj).find("td:eq(5)").text($("#sm_venue option:selected").text());
         // $(cobj).find("td:eq(5)").attr("relvenue", $("select[name='sm_venue']").val()); 
         // $(cobj).find("td:eq(6)").text($("input[name='sm_registration']").val()); 
         // $(cobj).find("td:eq(7)").text($("input[name='sm_transportation']").val()); 
         // $(cobj).find("td:eq(8)").text($("input[name='sm_accommodation']").val()); 
         // $(cobj).find("td:eq(9)").text($("input[name='sm_total']").val()); 
         if ($("#uploadFile").val() != "") {
              $(cobj).find("td:eq(5)").html("<a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</a>");
          }
          if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(cobj).find("td:eq(7)").text($("input[name='dra_remarks']").val()); 

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
                 loadSuccessModal('Successfully updated data!');
                 if(table == "employee_pts_pdp1"){
                    tableData('employee_pts_pdp1_tbody');
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
                 loadSuccessModal('Successfully saved data!');
                 if(table == "employee_pts_pdp1"){
                    tableData('employee_pts_pdp1_tbody');
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
       
         var mtable = $("#pts_pdp1infolist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         // $(ntr).append("<td reltitle="+$("select[name='sm_title']").val()+" style='display:none'>"+$("#sm_title option:selected").text()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_semtitle']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_location']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_datef']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_datet']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_organizer']").val()+"</td>");
         // $(ntr).append("<td relvenue='"+$("select[name='sm_venue']").val()+"'>"+$("#sm_venue option:selected").text()+"</td>");
         // // $(ntr).append("<td>"+$("select[name='sm_venue']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='sm_registration']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='sm_transportation']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='sm_accommodation']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='sm_total']").val()+"</td>");
         $(ntr).append("<td><a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
         else if ("<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") $(ntr).append("<td><a class='btn btn-danger' style='pointer-events:none;'>"+status+"</td>");
         else $(ntr).append("<td><a>"+status+"</a></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td>"+$("input[name='dra_remarks']").val()+"</td>");
         else $(ntr).append("<td></td>");

         
         var mtd = $("<td class='align_center'></td>");
        if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN" || "<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") {
         $("<div id='btn_pos'><a class='btn btn-warning delete_pts_pdp1' tbl_id='"+ tbl_id +"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
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
            deletepts_pdp1($(this), tbl_id);
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
         $("<div id='btn_pos'><a class='btn btn-primary edit_pts_ptp1' href='#modal-view' data-toggle='modal' tbl_id='"+ tbl_id +"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addpts_pdp1($(this).children(), tbl_id);
         }).appendTo($(mtd));
         }
         $(ntr).append($(mtd));
         
         $(ntr).appendTo($(mtable));      
      }  
      $(".modalclose").click();
       
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