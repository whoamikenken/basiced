<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
$aw_date = date("Y-m-d");
?>
<form id="form_awards">
<div class="form_row">
    <label class="field_name align_right">Name of Awards / Achievements</label>
    <div class="field">
        <input type="text" name="aw_name" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Date Given</label>
    <div class="field">
        <div class="input-group date date_issued" data-date="<?=date("Y-m-d",strtotime($aw_date))?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" size="16" name="aw_date" type="text" value="<?=date("Y-m-d",strtotime($aw_date))?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Given By</label>
    <div class="field">
        <input type="text" name="aw_given" class="col-md-8" value=""/>
    </div>
</div>

<div class="form_row">
    <label class="field_name align_right"></label>
    <div class="field">
        <a href="#" class="btn btn-primary" id="saveaward">Save</a>
    </div>
</div>

</form>
<script>
$(".button_save_modal").unbind("click").click(function(){
 var $validator = $("#myForm").validate({
        rules: {
            aw_name: {
              required: true
            },
            aw_date: {
              required: true
            }
        }
    });
    
   if($("#myForm").valid()){
      var cobj = "";
      $("#awardslist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         // $(cobj).find("td:eq(3)").attr('educel',$("select[name='aw_award'] :selected").val()).text($("select[name='aw_award'] :selected").text());
         $(cobj).find("td:eq(0)").text($("input[name='aw_award']").val());
         $(cobj).find("td:eq(1)").text($("input[name='aw_institution']").val());
         $(cobj).find("td:eq(2)").text($("input[name='aw_address']").val());
         $(cobj).find("td:eq(3)").text($("input[name='aw_datef']").val());
            /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table: "applicant_awardsrecog",
              tbl_id: $("input[name='tbl_id']").val(),
              employeeid: $("input[name='applicantId']").val(),
              award: $("input[name='aw_award']").val(),
              institution: $("input[name='aw_institution']").val(),
              address: $("input[name='aw_address']").val(),
              datef: $("select[name='aw_datef']").val(),
              status:status
            },
            dataType: "json",
            async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 $(".modalclose").click();
                 loadSuccessModal();
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
              table: "applicant_awardsrecog",
              tbl_id: $("input[name='tbl_id']").val(),
              employeeid: $("input[name='applicantId']").val(),
              award: $("input[name='aw_award']").val(),
              institution: $("input[name='aw_institution']").val(),
              address: $("input[name='aw_address']").val(),
              datef: $("select[name='aw_datef']").val(),
              status:status
            },
            dataType: "json",
            async: false,
            success:function(response){
              if(response.status == "success"){
                 tbl_id = response.tbl_id;
                 $(".modalclose").click();
                 loadSuccessModal();
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
       
         var mtable = $("#awardslist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='aw_award']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='aw_institution']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='aw_address']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='aw_datef']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addpts($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
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
$(".date_issued").datepicker({
    autoclose: true,
    todayBtn: true
});
$('.chosen').chosen();


</script>