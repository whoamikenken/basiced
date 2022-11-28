<?php

/**
 * @author Kennedy
 * @copyright 2019
 */
 $sm_date = date("Y-m-d");
?>
<input type="hidden" name="tbl_id">
<form id="form_education" class="form-horizontal">
  <div class="form-group">
  <label class="col-sm-4 control-label">Name of Organization</label>
    <div class="col-sm-7">
      <input type="text" name="sm_name_org" id="sm_name_org" class="form-control" value=""/>
    </div>
  </div>
</form>
<!-- <script>
$(".button_save_modal").unbind("click").click(function(){
   var tbl_id = "";
 var table = "";
 var userid = "";
 if($("input[name='applicantId']").val()){
  table = "applicant_proorg";
  userid = $("input[name='applicantId']").val();
 }
 else{
  table = "employee_proorg"; 
  userid = $("input[name='employeeid']").val();
 }
 var $validator = $("#form_education").validate({
        rules: {
            sm_name_org: {
              required: true
            },
            sm_date: {
              required: true
            },
            sm_position: {
              required: true,
            }
        }
    });
    
   if($("#form_education").valid()){
      var cobj = "";
      $("#orginfolist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='sm_name_org']").val());
         $(cobj).find("td:eq(1)").text($("input[name='sm_date']").val());
         $(cobj).find("td:eq(2)").text($("input[name='sm_position']").val());  
          /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table: table,
              tbl_id: $("input[name='tbl_id']").val(),
              employeeid: userid,
              name_org: $("input[name='sm_name_org']").val(),
              datef: $("input[name='sm_date']").val(),
              position: $("input[name='sm_position']").val()
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
              table: table,
              tbl_id: $("input[name='tbl_id']").val(),
              employeeid: userid,
              name_org: $("input[name='sm_name_org']").val(),
              datef: $("input[name='sm_date']").val(),
              position: $("input[name='sm_position']").val()
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
         var mtable = $("#orginfolist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='sm_name_org']").val()+"</td>");
          // $(ntr).append("<td educmapo='"+$("select[name='org_educlevel'] :selected").val()+"'>"+$("select[name='org_educlevel'] :selected").text()+"</td>");
        /* $(ntr).append("<td>"+$("select[name='eb_level']").val()+"</td>");*/
         $(ntr).append("<td>"+$("input[name='sm_date']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_position']").val()+"</td>");
         var mtd = $("<td class='align_center'></td>");
         /*
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addeducation($(this));
         }).appendTo($(mtd));
         */
         $("<div id='btn_pos'><a class='btn btn-warning delete_org' tbl_id='"+ tbl_id +"'><i class='glyphicon glyphicon-trash'></i></a></div>").click(function(){
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
            $(this).parent().parent().remove();
            deleteOrg($(this), tbl_id); 
         }).appendTo($(mtd));
         $("<div id='btn_pos'><a class='btn btn-info edit_org' tbl_id='"+ tbl_id +"' href='#modal-view' data-toggle='modal' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addOrg($(this).children(), tbl_id);
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#orginfolist").find("tbody"));      
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
</script> -->