<?php

/**
 * @author Justin
 * @copyright 2016
 */
$from = date("Y-m-d"); 
$to = date("Y-m-d");
?>

<form id="form_workhistory">
<div class="form_row">
    <label class="field_name align_right">Position Held</label>
    <!-- <div class="field">
        <input type="text" name="wh_position" class="col-md-10" value=""/>
    </div> -->
    <div class="field">
      <select class="form-control" name="el_description"  >
      <?
        $educlevel = $this->extras->showreportseduclevel('','e');
        foreach($educlevel as $c=>$val){

        ?><option value="<?=$c?>" ><?=$val?></option><?    
        }
      ?>
      </select>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Company Name</label>
    <div class="field">
        <input type="text" name="wh_company" class="col-md-10" value="" required="" />
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Address</label>
    <div class="field">
        <input type="text" name="wh_address" class="col-md-10" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Contact Number</label>
    <div class="field">
        <input type="text" name="wh_contact" class="col-md-10" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Salary</label>
    <div class="field">
        <input type="text" name="wh_salary" class="col-md-10" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Inclusive Dates</label>
    <div class="field">
        <div class="input-group date date_from" data-date="<?=date("Y-m-d",strtotime($from))?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" size="16" name="wh_datefrom" type="text" value="<?=date("Y-m-d",strtotime($from))?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
        <div class="input-group date date_to" data-date="<?=date("Y-m-d",strtotime($to))?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" size="16" name="wh_dateto" type="text" value="<?=date("Y-m-d",strtotime($to))?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
</div>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
//$("#saveeducation").click(function(){
 var $validator = $("#form_workhistory").validate({
        rules: {
            eb_level: {
              required: true
            },
            eb_school: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_workhistory").valid()){
      var cobj = "";
      $("#workhistorylistunrelated").find("tbody tr").each(function(){
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
      }else{       
         var mtable = $("#workhistorylistunrelated").find("tbody");
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
            addworkhistoryunrelated($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#workhistorylistunrelated").find("tbody"));      
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$(".date_from, .date_to").datepicker({
    autoclose: true,
    todayBtn: true
});
$('.chosen').chosen();
</script>