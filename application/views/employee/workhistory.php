<?php

/**
 * @author Kennedy
 * @copyright 2019
 */
$from = date("Y-m-d"); 
$to = date("Y-m-d");
?>
<input type="hidden" name="tbl_id">
<form id="form_workhistory" class="form-horizontal">

  <div class="form-group">
  <label class="col-sm-4 control-label">Position Held</label>
    <div class="col-sm-7">
      <input type="text" name="eb_school" class="form-control" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Employer</label>
    <div class="col-sm-7">
      <input type="text" name="wh_company" class="form-control" value=""/>
    </div>
  </div> 

  <div class="form-group">
  <label class="col-sm-4 control-label">Address</label>
    <div class="col-sm-7">
      <input type="text" name="wh_address" class="form-control" value=""/>
    </div>
  </div>

  <div class="form-group">
  <label class="col-sm-4 control-label">Contact Number</label>
    <div class="col-sm-7">
      <input type="text" name="wh_contact" class="form-control" value=""/>
    </div>
  </div>

  <div class="form-group">
  <label class="col-sm-4 control-label">Salary</label>
    <div class="col-sm-7">
      <input type="text" name="wh_salary" class="form-control" value=""/>
    </div>
  </div>
  
<div class="form-group">
  <label class="col-sm-4 control-label">Inclusive Dates</label>
  <div class="col-sm-7">
    <div class="col-md-6">
      <div class='input-group date date_from' data-date="<?=date("Y-m-d",strtotime($from))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="wh_datefrom" value="<?=date("Y-m-d",strtotime($from))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
    <div class="col-md-6">
      <div class='input-group date date_to' data-date="<?=date("Y-m-d",strtotime($to))?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" size="16" name="wh_dateto" value="<?=date("Y-m-d",strtotime($to))?>"/>
          <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
          </span>
      </div>
    </div>
  </div>
</div>

</form>

<div id="msg_header" style="display:none;">
  <strong></strong> <span></span>
</div>

<script>
  var toks = hex_sha512(" ");
$(".button_save_modal").unbind("click").click(function(){
var tbl_id = "";
 var table = "";
 var userid = "";
 if($("input[name='applicantId']").val()){
  table = "applicant_work_history";
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
            wh_address: {
              required: true,
              minlength: 2
            },
            wh_contact: {
              required: true,
              minlength: 2
            },
            wh_salary: {
              required: true,
              minlength: 2
            },
            wh_datefrom: {
              required: true,
            },
            wh_dateto: {
              required: true,
            }
        }
    });
    
   if($("#form_workhistory").valid()){
      var cobj = "";
      $("#workhistorylist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='wh_position']").val());
         $(cobj).find("td:eq(1)").text($("input[name='wh_company']").val());                                    
         $(cobj).find("td:eq(2)").text($("input[name='wh_address']").val());
         $(cobj).find("td:eq(3)").text($("input[name='wh_contact']").val());
         $(cobj).find("td:eq(4)").text($("input[name='wh_salary']").val());
         $(cobj).find("td:eq(5)").text($("input[name='wh_datefrom']").val());
         $(cobj).find("td:eq(6)").text($("input[name='wh_dateto']").val());

         /*save/update data first*/
         $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: {
              table:  GibberishAES.enc( table, toks),
              tbl_id:  GibberishAES.enc($("input[name='tbl_id']").val() , toks),
              employeeid:  GibberishAES.enc(userid , toks),
              date_from: GibberishAES.enc( $("input[name='wh_datefrom']").val() , toks),
              date_to: GibberishAES.enc( $("input[name='wh_dateto']").val() , toks),
              position:  GibberishAES.enc( $("input[name='wh_position']").val(), toks),
              company:  GibberishAES.enc($("input[name='wh_company']").val() , toks),
              address:  GibberishAES.enc($("input[name='wh_address']").val() , toks),
              contactnumber: GibberishAES.enc( $("input[name='wh_contact']").val() , toks),
              salary:  GibberishAES.enc( $("input[name='wh_salary']").val(), toks),
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
              table:  GibberishAES.enc( table, toks),
              tbl_id:  GibberishAES.enc($("input[name='tbl_id']").val() , toks),
              employeeid:  GibberishAES.enc(userid , toks),
              date_from: GibberishAES.enc( $("input[name='wh_datefrom']").val() , toks),
              date_to: GibberishAES.enc( $("input[name='wh_dateto']").val() , toks),
              position:  GibberishAES.enc( $("input[name='wh_position']").val(), toks),
              company:  GibberishAES.enc($("input[name='wh_company']").val() , toks),
              address:  GibberishAES.enc($("input[name='wh_address']").val() , toks),
              contactnumber: GibberishAES.enc( $("input[name='wh_contact']").val() , toks),
              salary:  GibberishAES.enc( $("input[name='wh_salary']").val(), toks),
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

         var mtable = $("#workhistorylist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='wh_position']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='wh_company']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='wh_address']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='wh_contact']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='wh_salary']").val()+"</td>");
         $(ntr).append("<td class='align_center'>"+$("input[name='wh_datefrom']").val()+"</td>"); 
         $(ntr).append("<td class='align_center'>"+$("input[name='wh_dateto']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addworkhistory($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#workhistorylist").find("tbody"));      
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