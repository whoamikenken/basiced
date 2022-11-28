<?php

$eb_relation = $this->extras->listRelation($gender, $civil_status);
$educlevel = $this->extras->showreportseduclevel(' - Select TYPE - ','ECT');
if((isset($applicant) && $applicant == "yes")){
  $existingType = $this->extras->getExistingData($applicantId, "applicant_emergencyContact", "type");
  foreach ($existingType as $k => $v) {
     if($type == $v['type']) unset($existingType[$k]);
  }
  foreach ($existingType as $key => $value) {
      unset($educlevel[$value['type']]);
  }
}
?>
<input type="hidden" name="tbl_id">
<input type="hidden" name="employeeid_" id="employeeid_">
<input type="hidden" name="isApplicant" id="isApplicant" value="<?= $applicant ?>">
<form id="form_emergencyContact">
	<div class="form_row">
		<label class="field_name align_right">Name</label>
		<div class="field" style="width: 69%;">
			<input type="text" name="eb_name" class="form-control upperCase" value=""/>
		</div>
	</div><br>
	<div class="form_row">
		<label class="field_name align_right">Relation</label>
		<div class="field" style="width: 69%;">
			<select class="form-control chosen" name="eb_relation" id="eb_relation" required>
          <?php foreach($eb_relation as $er => $rstat):?>
              <option <?=($er==$eb_relation ? " selected" : "")?> value="<?= $er ?>"><?= $rstat ?></option>
          <?php endforeach;?>
      </select>        
		</div>
	</div><br>
	<div class="form_row">
		<label class="field_name align_right">Mobile #</label>
		<div class="field" style="width: 69%;">
			<input type="text" name="eb_mobile" class="form-control upperCase" value=""/>
		</div>
	</div><br>
	<div class="form_row">
		<label class="field_name align_right telephone">Home #</label>
		<div class="field" style="width: 69%;">
			<input type="text" id="eb_homeNo" name="eb_homeNo" class="form-control upperCase" value=""/>
		</div>
	</div><br>
	<div class="form_row">
		<label class="field_name align_right business">Office #</label>
		<div class="field" style="width: 69%;">
			<input type="text" name="eb_officeNo" class="form-control upperCase" value=""/>
		</div>
	</div><br>
  <div class="form_row">
    <label class="field_name align_right">Emergency Type</label>
    <div class="field" style="width: 69%;">
      <select class="form-control chosen" name="eb_type" id="eb_type" required>
                <?php foreach($educlevel as $el => $type):?>
                    <option <?=($el==$educlevel ? " selected" : "")?> value="<?= $el ?>"><?= $type ?></option>
                <?php endforeach;?>
        </select>         
    </div>
  </div><br>
  <div class="form_row" id="draremarks" style="display: none;">
    <label class="field_name align_right">Remarks</label>
    <div class="field" style="width: 69%;">
      <input type="text" name="dra_remarks" class="form-control upperCase" value=""/> 
    </div>
  </div>
  <div class="form-group data_request_details_requested_date" style="display: none;">
            <label class="col-sm-4 control-label">Date Requested</label>
            <div class="col-sm-7">
                <label class="control-label" name="requested_date"></label>
            </div>
        </div>
        
</form>
<script>
  setTimeout(function(){
      getDataRequestDetails();
  }, 500);

  function getDataRequestDetails(){
    $.ajax({
         url: "<?=site_url("applicant/getDataRequestDetails")?>",
         data : {table:'employee_emergencyContact', employeeid: $("#employeeid_").val(), baseid: $("input[name='tbl_id']").val()},
         dataType: 'JSON',
         type : "POST",
         success:function(msg){
            if(msg.err_code == 1){
                $("label[name='requested_date']").text(msg.request_date);
                $(".data_request_details_requested_date").css("display", "block");
            }
         }
      }); 
}
  if($("input[name='applicantId']").val()){
    $(".telephone").text("Telephone #");
    $(".business").text("Business #");
   }
$(".button_save_modal").unbind("click").click(function(){
 emergency_count = 0;
 var tbl_id = "";
 var table = "";
 var userid = "";
 var status = "";
 var isApplicant = $("#isApplicant").val();
 if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
 else status = "PENDING";
 if($("input[name='applicantId']").val()){
  table = "applicant_emergencyContact";
  userid = $("input[name='applicantId']").val();
  $(".telephone").text("Telephone #");
  $(".business").text("Business #");


 }
 else{
  table = "employee_emergencyContact"; 
  userid = $("input[name='employeeid']").val();
 }

 var $validator = $("#form_emergencyContact").validate({
        rules: {
            eb_name: {
              required: true,
            },
			eb_relation: {
              required: true,
            }
        }
    });
    
   if($("#form_emergencyContact").valid()){
      var cobj = "";
      $("#emergencycontactlist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });     
               
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='eb_name']").val());
         $(cobj).find("td:eq(1)").text($("#eb_relation option:selected").text());
         $(cobj).find("td:eq(1)").attr("reldata", $("select[name='eb_relation']").val());  
         $(cobj).find("td:eq(2)").text($("input[name='eb_mobile']").val());                               
         $(cobj).find("td:eq(3)").text($("input[name='eb_homeNo']").val());                               
         $(cobj).find("td:eq(4)").text($("input[name='eb_officeNo']").val());
         $(cobj).find("td:eq(5)").text($("#eb_type option:selected").text());
         $(cobj).find("td:eq(5)").attr("reltype", $("select[name='eb_type']").val());  
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(cobj).find("td:eq(7)").text($("input[name='dra_remarks']").val()); 


          $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table:  GibberishAES.enc(table , toks),
              tbl_id: GibberishAES.enc( $("input[name='tbl_id']").val() , toks),
              employeeid:  GibberishAES.enc(userid , toks),
              name:  GibberishAES.enc( $("input[name='eb_name']").val(), toks),
              relation:  GibberishAES.enc( $("select[name='eb_relation']").val(), toks),
              mobile: GibberishAES.enc($("input[name='eb_mobile']").val()  , toks),
              homeNo:  GibberishAES.enc($("input[name='eb_homeNo']").val() , toks),
              officeNo:  GibberishAES.enc($("input[name='eb_officeNo']").val() , toks),
              type:  GibberishAES.enc($("select[name='eb_type']").val() , toks),
              status:  GibberishAES.enc(status , toks),
              dra_remarks: GibberishAES.enc($("input[name='dra_remarks']").val()  , toks),
              toks:toks
            },
            dataType: "json",
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
                 if(table == "employee_emergencyContact"){
                    loadTable('employee_emergencyContact_table');
                  }
                 return false;
              }else{
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text(response.msg);
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
              }
            }
         }); 
                                      
      }else{     

          $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table:  GibberishAES.enc(table , toks),
              tbl_id: GibberishAES.enc( $("input[name='tbl_id']").val() , toks),
              employeeid:  GibberishAES.enc(userid , toks),
              name:  GibberishAES.enc( $("input[name='eb_name']").val(), toks),
              relation:  GibberishAES.enc( $("select[name='eb_relation']").val(), toks),
              mobile: GibberishAES.enc($("input[name='eb_mobile']").val()  , toks),
              homeNo:  GibberishAES.enc($("input[name='eb_homeNo']").val() , toks),
              officeNo:  GibberishAES.enc($("input[name='eb_officeNo']").val() , toks),
              type:  GibberishAES.enc($("select[name='eb_type']").val() , toks),
              status:  GibberishAES.enc(status , toks),
              dra_remarks: GibberishAES.enc($("input[name='dra_remarks']").val()  , toks),
              toks:toks
            },
            dataType: "json",
            success:function(response){
              emergency_count += 1;
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
                 if(table == "employee_emergencyContact"){
                    loadTable('employee_emergencyContact_table');
                  }
                 return false;
              }else{
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text(response.msg);
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
              }
            }
         });  

         var mtable = $("#emergencycontactlist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         if(isApplicant == "yes") $(ntr).append("<td>"+$("input[name='eb_name']").val().toUpperCase()+"</td>");
         else  $(ntr).append("<td>"+$("input[name='eb_name']").val()+"</td>");
         $(ntr).append("<td reldata="+$("select[name='eb_relation']").val()+">"+$("#eb_relation option:selected").text()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_mobile']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_homeNo']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_officeNo']").val()+"</td>");
         $(ntr).append("<td reltype="+$("select[name='eb_type']").val()+">"+$("#eb_type option:selected").text()+"</td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
         else if ("<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") $(ntr).append("<td><a class='btn btn-danger' style='pointer-events:none;'>"+status+"</td>");
         else if(isApplicant != 'yes') $(ntr).append("<td><a>"+status+"</a></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td>"+$("input[name='dra_remarks']").val()+"</td>");
         else $(ntr).append("<td></td>");
         
         var mtd = $("<td></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN" || "<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") {
           $("<div style='float:right'><a class='btn btn-warning deleterelation' tbl_id='"+ tbl_id +"'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
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
                  deleterelation($(this), tbl_id); 
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
           $("<div style='float:right'><a class='btn btn-primary eEmergencyContact' href='#modal-view' data-toggle='modal' tbl_id='"+ tbl_id +"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
              addemergencycontact($(this).children(), tbl_id);
           }).appendTo($(mtd));
         
         }else{
          $("<div style='float:right'><a class='btn btn-warning deleterelation' tbl_id='"+ tbl_id +"'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
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
                  delete_emergency($(this), tbl_id); 
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
           $("<div style='float:right'><a class='btn btn-info eEmergencyContact' href='#infoModal' data-toggle='modal' tbl_id='"+ tbl_id +"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
              addemergencycontact($(this), tbl_id);
           }).appendTo($(mtd));
         }
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#emergencycontactlist").find("tbody"));      
      }

   }else {
       $validator.focusInvalid();
       return false;
   }
   
});

$(".chosen").chosen();

$("input[name='eb_mobile']").inputmask("mask", {"mask": "9999-9999999"});
$("input[name='eb_homeNo']").inputmask("mask", {"mask": "9999-9999"});
$("input[name='eb_officeNo']").inputmask("mask", {"mask": "9999-9999999"});
</script>
