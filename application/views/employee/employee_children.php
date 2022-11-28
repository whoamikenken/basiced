<?php

$code_gender = $this->extras->showgender();
 
?>
<input type="hidden" name="tbl_id">
<input type="hidden" name="isApplicant" id="isApplicant" value="<?= $applicant ?>">
<form id="form_children">
  <div class="col-md-12">
    <div class="col-md-12">
      <div class="form-group">
          <div class="col-md-12">
              <label class="col-sm-3">Name:</label>
              <div class="col-sm-9">
                  <input type="text" name="eb_name" class="form-control required upperCase" value=""/>
              </div> 
          </div>
      </div>
      <br><br>
      <div class="form-group">
           <div class="col-md-12">
            <label class="col-sm-3">Date of Birth:</label>
            <div class="col-sm-9">
              <div class='input-group eb_dob' id="eb_dob">
                  <input class="form-control col-md-12" type="text" name="eb_dob"></input>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                  <!-- <span id="clearResigned" name="clearResigned" style="color:blue;float: right;"><b>&nbsp;&nbsp;&nbsp;CLEAR</b></span> -->
              </div>
            </div> 
        </div>
      </div>
      <br><br>
      <div class="form-group">
          <div class="col-md-12">
              <label class="col-sm-3">Age:</label>
              <div class="col-sm-9">
                  <input type="text" name="eb_age" id="eb_age" class="form-control required upperCase"/>
              </div>
          </div>
      </div>
      <br><br>
            <div class="form-group">
              <div class="col-md-12">
                    <label class="col-sm-3">Gender</label>
                    <div class="col-sm-9">
                    <select class="form-control" name="eb_gender" id="eb_gender" required>
                        <?php foreach($code_gender as $key => $value):?>
                        <option <?=($key==$code_gender ? " selected" : "")?> value="<?= $key ?>"><?= $value ?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>
              </div>
      <br><br>
      <div class="form-group" hidden>
          <div class="col-md-12">
              <label class="col-sm-3">Birth Order:</label>
              <div class="col-sm-9">
                  <input type="text" name="eb_b_order" id="eb_b_order" class="form-control required upperCase"/>
              </div>
          </div>
      </div>
  </div>
</div>
<div id="msg_header" style="display:none;">
  <strong></strong> <span></span>
</div>
</form>
<script>
  var toks = hex_sha512(" ");
$('#eb_dob').on('dp.change', function(e){ 
  $("#eb_age").val(getAge(e.date));
})
$(".button_save_modal").unbind("click").click(function(){
 var tbl_id = b_order = "";
 var table = "";
 var userid = "";
 var status = "";
 var isApplicant = $("#isApplicant").val();
 if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
 else status = "PENDING";
 if($("input[name='applicantId']").val()){
  table = "applicant_children";
  userid = $("input[name='applicantId']").val();
 }
 else{
  table = "employee_children"; 
  userid = $("input[name='employeeid']").val();
 }

 var $validator = $("#form_children").validate({
        rules: {
            eb_name: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_children").valid()){
      var cobj = "";
      $("#childrenlist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='eb_name']").val());
         $(cobj).find("td:eq(1)").text($("#eb_gender option:selected").text());
         $(cobj).find("td:eq(1)").attr("reldata", $("select[name='eb_gender']").val());                                 
         $(cobj).find("td:eq(2)").text($("input[name='eb_b_order']").val());                               
         $(cobj).find("td:eq(3)").text($("input[name='eb_dob']").val());
         $(cobj).find("td:eq(4)").text($("input[name='eb_age']").val());  

         /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table:  GibberishAES.enc( table, toks),
              tbl_id:  GibberishAES.enc($("input[name='tbl_id']").val() , toks),
              employeeid:  GibberishAES.enc( userid, toks),
              name:  GibberishAES.enc( $("input[name='eb_name']").val(), toks),
              birthorder:  GibberishAES.enc( $("input[name='eb_b_order']").val(), toks),
              birthdate: GibberishAES.enc($("input[name='eb_dob']").val(), toks),
              age:  GibberishAES.enc( $("input[name='eb_age']").val(), toks),
              gender:  GibberishAES.enc($("select[name='eb_gender']").val() , toks),
              toks:toks
            },
            dataType: "json",
            async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 b_order = response.b_order;
                 $(".modalclose").click();
                 loadChildrendatatable(userid);
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
            data: {
              table:  GibberishAES.enc( table, toks),
              tbl_id:  GibberishAES.enc( $("input[name='tbl_id']").val(), toks),
              employeeid:  GibberishAES.enc( userid, toks),
              name:  GibberishAES.enc( $("input[name='eb_name']").val(), toks),
              birthorder:  GibberishAES.enc( $("input[name='eb_b_order']").val(), toks),
              birthdate:  GibberishAES.enc( $("input[name='eb_dob']").val(), toks),
              age:  GibberishAES.enc( $("input[name='eb_age']").val(), toks),
              gender: GibberishAES.enc( $("select[name='eb_gender']").val() , toks),
              toks:toks
            },
            dataType: "json",
            async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 b_order = response.b_order;
                 $(".modalclose").click();
                 loadChildrendatatable(userid);
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

         var mtable = $("#childrenlist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         if(isApplicant == "yes") $(ntr).append("<td>"+$("input[name='eb_name']").val().toUpperCase()+"</td>");
         else $(ntr).append("<td>"+$("input[name='eb_name']").val()+"</td>");
         $(ntr).append("<td reldata="+$("select[name='eb_gender']").val()+">"+$("#eb_gender option:selected").text()+"</td>");
         if(isApplicant == "yes") $(ntr).append("<td>"+$("input[name='eb_b_order']").val().toUpperCase()+"</td>");
         else $(ntr).append("<td>"+b_order+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_dob']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_age']").val()+"</td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
         else if(isApplicant != 'yes') $(ntr).append("<td><a>"+status+"</a></td>");
         
         
         var mtd = $("<td></td>");
         if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") {
          $("<a class='btn btn-info echildren' href='#modal-view' data-toggle='modal' tbl_id='"+tbl_id+"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addchildren($(this).children(), tbl_id);
           }).appendTo($(mtd));
           $("<a class='btn btn-warning delete_entry' tbl_id='"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
              if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
              $(this).parent().parent().remove();
              deleteEligibility($(this), tbl_id); 
           }).appendTo($(mtd));
         }else if(isApplicant == "yes"){
              $("<a class='btn btn-info echildren' href='#infoModal' data-toggle='modal' tbl_id='"+tbl_id+"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
              addchildren($(this), tbl_id);
             }).appendTo($(mtd));
             $("<a class='btn btn-warning delete_entry' tbl_id='"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
                if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
                $(this).parent().parent().remove();
                delete_entry($(this), tbl_id); 
             }).appendTo($(mtd));
         }
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#childrenlist").find("tbody")); 

        }
   }else {
       $validator.focusInvalid();
       return false;
   }
   
});

// $('#eb_dob').datetimepicker({
//     format: 'YYYY-MM-DD',
//     defaultDate: new Date()
// });
$(".eb_dob").datetimepicker({
    format: "YYYY-MM-DD"
});

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [month, day, year].join('/');
}

function getAge(dateString) {
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}
</script>