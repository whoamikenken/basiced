<?php

$el_date = date("Y-m-d");
$proficiency = $this->extras->showreportseduclevel('','OC');
?>

<style type="text/css">
    .form-group{
      padding-bottom: 10px;
      margin: 0px 0px 0px 0px;
    }
    #form_ot{
      margin-top: 10px;
    }

    .modal-overflow .modal-body{
      margin-bottom: 0px;
    }

</style>


<input type="hidden" name="tbl_id">
<input type="hidden" name="isApplicant" id="isApplicant" value="<?= $applicant ?>">
<form id="form_ot" class="form-horizontal">

  <div class="form-group">
  <label class="col-sm-3 control-label">Skills</label>
    <div class="col-sm-6">
      <input type="text" name="el_skills" class="form-control upperCase" id="el_skills" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-3 control-label">Proficiency</label>
    <div class="col-sm-6">
      <select class="chosen required" name="el_proficiency" id="el_proficiency" required>
            <?php foreach($proficiency as $ot => $prof):?>
                <option <?=($proficiency==$prof ? " selected" : "")?> value="<?= $ot ?>"><?= $prof ?></option>
            <?php endforeach;?>
        </select> 
    </div>
  </div> 

</form>
<script>
  var toks = hex_sha512(" ");

$(".button_save_modal").unbind("click").click(function(){
 var tbl_id = "";
 var table = "";
 var userid = "";
 var isApplicant = $("#isApplicant").val();
 if($("input[name='applicantId']").val()){
  table = "applicant_credentials";
  userid = $("input[name='applicantId']").val();
 }
 else{
  table = "employee_credentials"; 
  userid = $("input[name='employeeid']").val();
 }

 var $validator = $("#form_ot").validate({
        rules: {
            el_skills: {
              required: true
            }
        }
    });
    
   if($("#form_ot").valid()){
      var cobj = "";
      $("#otlist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              

      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='el_skills']").val());
         $(cobj).find("td:eq(1)").text($("#el_proficiency option:selected").text());
         $(cobj).find("td:eq(1)").attr("relprof", $("select[name='el_proficiency']").val());             
     

         /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table:  GibberishAES.enc(table , toks),
              tbl_id:  GibberishAES.enc($("input[name='tbl_id']").val() , toks),
              employeeid:  GibberishAES.enc(userid , toks),
              skills:  GibberishAES.enc($("input[name='el_skills']").val() , toks),
              profiency:  GibberishAES.enc($("select[name='el_proficiency']").val() , toks),
              toks:toks
            },
            dataType: "json",
            async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 $(".modalclose").click();
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
              table:  GibberishAES.enc(table , toks),
              tbl_id:  GibberishAES.enc($("input[name='tbl_id']").val() , toks),
              employeeid:  GibberishAES.enc(userid , toks),
              skills:  GibberishAES.enc($("input[name='el_skills']").val() , toks),
              profiency:  GibberishAES.enc($("select[name='el_proficiency']").val() , toks),
              toks:toks
            },
            dataType: "json",
            async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 $(".modalclose").click();
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

         var mtable = $("#otlist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         if(isApplicant == "yes") $(ntr).append("<td>"+$("input[name='el_skills']").val().toUpperCase()+"</td>");
         else $(ntr).append("<td>"+$("input[name='el_skills']").val()+"</td>");
         $(ntr).append("<td relprof="+$("select[name='el_proficiency']").val()+">"+$("#el_proficiency option:selected").text()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<div style='float: right'><a class='btn btn-warning delete_ot' tbl_id='"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
            $(this).parent().parent().remove();
            deleteOT($(this), tbl_id); 
         }).appendTo($(mtd));
         $("<div style='float: right'><a class='btn btn-primary edit_ot' href='#educationModal' data-toggle='modal' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit' tbl_id='"+tbl_id+"'></i></a></div>").click(function(){
            addOT($(this).children(), tbl_id);
         }).appendTo($(mtd));
         
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#otlist").find("tbody"));      
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$(".date_issued").datepicker({
    autoclose: true,
    todayBtn: true
});
$('.chosen').chosen();
</script>