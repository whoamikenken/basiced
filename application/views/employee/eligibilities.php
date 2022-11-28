<?php
/**
* @author Aaron P. Ruanto
* @copyright 2013

*/

$el_date = date("Y-m-d");
$ex_date = date("Y-m-d");
$eligibility_level = $this->extras->showreportseduclevel(' - Select Eligibilitiy - ','E');
?>
<style type="text/css">
    .form-group{
    padding-bottom: 10px;
    margin: 0px 0px 0px 0px;
    }
    #form_eligibilities{
    margin-top: 10px;
    }
    .modal-overflow .modal-body{
    margin-bottom: 0px;
    }
</style>
<input type="hidden" name="isApplicant" id="isApplicant" value="<?= $applicant ?>">
<input type="hidden" name="employeeid_" id="employeeid_">
<form id="form_eligibilities" class="form-horizontal">
    <input type="hidden" name="tbl_id">
    <div class="form_row">
        <div class="form-group">
        <label class="col-sm-4 control-label">Exam Description</label>
            <div class="col-sm-7">
                <select class="chosen required" name="el_description" id="el_description" required>
                    <?php foreach($eligibility_level as $el => $elevel):?>
                    <option <?=($eligibility_level==$elevel ? " selected" : "")?> value="<?= $el ?>"><?= $elevel ?></option>
                    <?php endforeach;?>
                </select> 
            </div>
        </div> 
        <div class="form-group">
            <label class="col-sm-4 control-label">License No.</label>
            <div class="col-sm-7">
                <input type="text" name="el_licenseNo" class="form-control upperCase" id="el_licenseNo" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Issued Date</label>
            <div class="col-sm-7">
                <div class='input-group date issuedDate' data-date="<?=date("Y-m-d",strtotime($el_date))?>" data-date-format="yyyy-mm-dd">
                <input type='text' class="form-control" name="el_issuedDate" value="<?= ($applicant != 'yes') ? date("Y-m-d",strtotime($el_date)) : ''?>" id="el_issuedDate"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group" id="expirydate_div">
            <label class="col-sm-4 control-label">Expiry Date</label>
            <div class="col-sm-7">
                <div class='input-group date expiryDate' data-date="<?=date("Y-m-d",strtotime($ex_date))?>" data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" name="el_expiryDate" value="<?= ($applicant != 'yes') ? date("Y-m-d",strtotime($ex_date)) : ''?>" id="el_expiryDate"/>
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Remarks</label>
            <div class="col-sm-7">
                <input type="text" name="el_remarks" class="form-control upperCase" value=""/>
            </div>
        </div>
        <div class="form-group">
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

        <div id="msg_header" style="display:none;">
            <strong></strong><span></span>
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
                      data: {id:GibberishAES.enc( $("input[name='tbl_id']").val(), toks), table:GibberishAES.enc( 'employee_eligibilities', toks), employeeid:GibberishAES.enc( $("#employeeid_").val(), toks) , toks:toks},
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

            if($("#el_description").val() == "CIVIL SERVICE"){
                // $("#el_licenseNo").prop('disabled', true);
                $("#el_expiryDate").prop('disabled', true);
                $("#expirydate_div").hide();
              }
              else{
                // $("#el_licenseNo").prop('disabled', false);
                $("#el_expiryDate").prop('disabled', false);
                $("#expirydate_div").show();
             }
             getDataRequestDetails();
        }, 500);
    })

function getDataRequestDetails(){
    $.ajax({
         url: "<?=site_url("applicant/getDataRequestDetails")?>",
         data : {table:'employee_eligibilities', employeeid: $("#employeeid_").val(), baseid: $("input[name='tbl_id']").val()},
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


$("#el_description").change(function(){
    if($(this).val() == "CIVIL SERVICE"){
        // $("#el_licenseNo").prop('disabled', true);
        // $("#el_issuedDate").prop('disabled', true);
        $("#el_expiryDate").prop('disabled', true).val('');
        $("#expirydate_div").hide();
        // $("#uploadFile").prop('disabled', true);
      }
      else{
        // $("#el_licenseNo").prop('disabled', false);
        // $("#el_issuedDate").prop('disabled', false);
        $("#el_expiryDate").prop('disabled', false);
        $("#expirydate_div").show();
        // $("#uploadFile").prop('disabled', false);
     }
});


    var toks = hex_sha512(" ");
    $('#el_issuedDate,#el_expiryDate').blur(function(){
        var d1 = new Date($("#el_issuedDate").val());
        var d2 = new Date($("#el_expiryDate").val());
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
        // document.getElementById('uploadFile').addEventListener('change', convertHeicToJpg, false);
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
    $(".button_save_modal").unbind("click").click(function()
    {   
        var tbl_id = "";
        var table = "";
        var userid = "";
        var status = "";
        var isApplicant = $("#isApplicant").val();
        if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
        else status = "PENDING";
            if($("input[name='applicantId']").val())
            {
                table = "applicant_eligibilities";
                userid = $("input[name='applicantId']").val();
            }
            else
            {
                table = "employee_eligibilities"; 
                userid = $("input[name='employeeid']").val();
            }
        var $validator = $("#form_eligibilities").validate(
        {
            rules: 
            {
                el_issuedDate: 
                {
                required: true
                },
                el_expiryDate: 
                {
                required: true
                },
                el_description: 
                {
                // required: true
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
            fd.append('date_issued',  GibberishAES.enc($("input[name='el_issuedDate']").val() , toks));
            fd.append('date_expired',  GibberishAES.enc($("input[name='el_expiryDate']").val() , toks));
            fd.append('description',  GibberishAES.enc($("select[name='el_description']").val() , toks));
            fd.append('license_number',  GibberishAES.enc($("input[name='el_licenseNo']").val() , toks));
            fd.append('remarks',  GibberishAES.enc( $("input[name='el_remarks']").val(), toks));
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
            fd.append('date_issued',  GibberishAES.enc($("input[name='el_issuedDate']").val() , toks));
            fd.append('date_expired',  GibberishAES.enc($("input[name='el_expiryDate']").val() , toks));
            fd.append('description',  GibberishAES.enc($("select[name='el_description']").val() , toks));
            fd.append('license_number',  GibberishAES.enc($("input[name='el_licenseNo']").val() , toks));
            fd.append('remarks',  GibberishAES.enc( $("input[name='el_remarks']").val(), toks));
            fd.append('status',  GibberishAES.enc( status, toks));
             fd.append('dra_remarks',  GibberishAES.enc( $("input[name='dra_remarks']").val(), toks));
            fd.append('toks', toks);
        }
            
            // fd.append('content', $("<td><a class='filename' content='"+base64String+"' mime='"+mime+"'>"+filename+"</td>"));
        if($("#form_eligibilities").valid())
        {
            var filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');
            var cobj = "";
                $("#eligibilitieslist").find("tbody tr").each(function()
                {
                if($(this).attr("iscurrent")==1) cobj = $(this);
                });              
            if(cobj)
            {
                $(cobj).find("td:eq(0)").text($("#el_description option:selected").text());
                $(cobj).find("td:eq(0)").attr("desc", $("select[name='el_description']").val());  
                $(cobj).find("td:eq(1)").text($("input[name='el_licenseNo']").val());     
                $(cobj).find("td:eq(2)").text($("input[name='el_issuedDate']").val());    
                $(cobj).find("td:eq(3)").text($("input[name='el_expiryDate']").val());    
                $(cobj).find("td:eq(4)").text($("input[name='el_remarks']").val());
                if ($("#uploadFile").val() != "") {
                    $(cobj).find("td:eq(5)").html("<a class='filename filenames' content='"+base64String+"' mime='"+mime+"'>"+filename+"</a>");
                }
                if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(cobj).find("td:eq(7)").text($("input[name='dra_remarks']").val()); 
                else $(ntr).append("<td></td>");

            /*save/update data first*/
                $.ajax(
                {
                    url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                    type: "POST",
                    data: fd,
                    dataType: "json",
                    processData:false,
                    contentType:false,
                    success:function(response)
                    {
                        if(response.status == "success")
                        {
                            tbl_id = response.tbl_id;
                            $(".modalclose").click();
                            // loadSuccessModal();
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Success!',
                                  text: 'Successfully updated data.',
                                  showConfirmButton: true,
                                  timer: 1000
                              })
                            if("<?= $this->session->userdata('usertype') ?>" == "ADMIN"){
                                if ($("#el_description option:selected").text().indexOf('LPT') > -1 || $("#el_description option:selected").text().indexOf('lpt') > -1 && table == 'employee_eligibilities'){
                                  updatePRC();
                                }
                            }
                            if(table == "employee_eligibilities"){
                              loadTable('employee_eligibilities_table');
                            }
                            return false;
                        }
                        else
                        {
                            $("#msg_header").addClass("alert alert-danger");
                            $("#msg_header").find("strong").text("Failed! ");
                            $("#msg_header").find("span").text(response.msg);
                            $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                            return;
                        }
                    }
                });
            }
            else
            {       
            /*save/update data first*/
                $.ajax(
                {
                    url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                    type: "POST",
                    data: fd,
                    dataType: "json",
                    processData:false,
                    contentType:false,
                    success:function(response)
                    {
                        if(response.status == "success")
                        {
                            tbl_id = response.tbl_id;
                            $(".modalclose").click();
                            Swal.fire({
                                  icon: 'success',
                                  title: 'Success!',
                                  text: 'Successfully added data.',
                                  showConfirmButton: true,
                                  timer: 1000
                              })
                            if("<?= $this->session->userdata('usertype') ?>" == "ADMIN"){
                                if ($("#el_description option:selected").text().indexOf('LPT') > -1 || $("#el_description option:selected").text().indexOf('lpt') > -1 && table == 'employee_eligibilities'){
                                  updatePRC();
                                }
                            }
                            if(table == "employee_eligibilities"){
                              loadTable('employee_eligibilities_table');
                            }
                            return false;
                            
                        }
                        else
                        {
                            $("#msg_header").addClass("alert alert-danger");
                            $("#msg_header").find("strong").text("Failed! ");
                            $("#msg_header").find("span").text(response.msg);
                            $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                            return;
                        }
                    }
                });
            var filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');
            var mtable = $("#eligibilitieslist").find("tbody");
                if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
                    var ntr = $("<tr></tr>");
                    
                    if(isApplicant == "yes"){
                        $(ntr).append("<td desc='"+$("select[name='el_description']").val()+"'>"+$("#el_description option:selected").text()+"</td>");
                        $(ntr).append("<td>"+$("input[name='el_licenseNo']").val().toUpperCase()+"</td>");
                        $(ntr).append("<td>"+$("input[name='el_issuedDate']").val().toUpperCase()+"</td>"); 
                        $(ntr).append("<td>"+$("input[name='el_expiryDate']").val().toUpperCase()+"</td>"); 
                        $(ntr).append("<td>"+$("input[name='el_remarks']").val().toUpperCase()+"</td>");
                    }else{
                        $(ntr).append("<td desc='"+$("select[name='el_description']").val()+"'>"+$("#el_description option:selected").text()+"</td>");
                        $(ntr).append("<td>"+$("input[name='el_licenseNo']").val()+"</td>");
                        $(ntr).append("<td>"+$("input[name='el_issuedDate']").val()+"</td>"); 
                        $(ntr).append("<td>"+$("input[name='el_expiryDate']").val()+"</td>"); 
                        $(ntr).append("<td>"+$("input[name='el_remarks']").val()+"</td>"); 
                    }
                         
                $(ntr).append("<td><a class='filename filenames' content='"+base64String+"' mime='"+mime+"'>"+filename+"</td>");
                if(userid = $("input[name='employeeid']").val()) 
                {
                    if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") {
                        $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
                    }else{
                        $(ntr).append("<td><a class='btn btn-danger' style='pointer-events:none;'>"+status+"</td>");
                    }
                }
                if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td>"+$("input[name='dra_remarks']").val()+"</td>");

                // else $(ntr).append("<td><a>"+status+"</a></td>");
            var mtd = $("<td></td>");
                if (userid = $("input[name='employeeid']").val()) 
                {
                    $("<div style='float: right; margin-right: 10px;'><a class='btn btn-warning' tbl_id = "+tbl_id+"><i class='glyphicon glyphicon-trash'></i></a></div>").click(function()
                    {
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
                            deleteEligibility($(this), tbl_id);
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
                        $("<div style='float: right;'><a class='btn btn-primary' href='#modal-view' data-toggle='modal' tbl_id='"+tbl_id+"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a></div>").click(function()
                    {
                        addeligibilities($(this).children(), tbl_id);
                    }).appendTo($(mtd));
                }
                else
                {
                    $("<div style='float: right;'><a class='btn btn-warning delete_eligibilities' tbl_id = '"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function()
                    {
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
                            delete_eligibilities($(this), tbl_id);
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
                    $("<div style='float: right;'><a class='btn btn-info eligibilities' href='#educationModal' data-toggle='modal' tbl_id='"+tbl_id+"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a></div>").click(function()
                    {
                        addeligibilities($(this).children(), tbl_id);
                    }).appendTo($(mtd));
                }
                $(ntr).append($(mtd));
                $(ntr).appendTo($("#eligibilitieslist").find("tbody"));      
            }  
            $(".modalclose").click();
            return false;
        }
        else 
        {
            $validator.focusInvalid();
            return false;
        }
    });
        $(".date").datetimepicker(
        {
            format: "YYYY-MM-DD"
        });
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
    function updatePRC(){
    $.ajax({
        url:  "<?= site_url('employee_/updatePRC') ?>",
        data: {employeeid:  GibberishAES.enc( $("input[name='employeeid']").val(), toks), prc_expiration:GibberishAES.enc( $("input[name='el_expiryDate']").val(), toks), prc:GibberishAES.enc($("input[name='el_licenseNo']").val() , toks), toks:toks},
        type: "POST",
        success: function(response) {

        }
    });
}
</script>