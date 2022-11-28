<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
?>

<style>
  .cbox{
     -ms-transform: scale(1.5); /* IE */
     -moz-transform: scale(1.5); /* FF */
     -webkit-transform: scale(1.5); /* Safari and Chrome */
     -o-transform: scale(1.5); /* Opera */
  }

</style>

<div style="margin-left: 40px;">
    <form id="form_legit">
    <div class="row">
        <label class="col-md-11">Name</label>
        <div class="col-md-11">
            <input type="text" name="lr_name" class="form-control" value=""/>
        </div>
    </div>
    <div class="row">
        <label class="col-md-11">Relationship</label>
        <div class="col-md-11">
            <select id="lr_relationship" name="lr_relationship" class="chosen"><?=$this->extras->showrelation()?></select>
            <input type="text" name="lr_relationship" class="form-control" value=""/>
        </div>
    </div>
    <div class="row">
        <label class="col-md-11">Address</label>
        <div class="col-md-11">
            <input type="text" name="lr_address" class="form-control" value=""/>
        </div>
    </div>
    <div class="row">
        <label class="col-md-11">Contact #</label>
        <div class="col-md-11">
            <input type="text" name="lr_contactno" class="form-control" value=""/>
        </div>
    </div>
    <div class="row">
        <label class="col-md-12">Date of Birth</label>
        <div class="col-md-11">
          <div class='input-group date' id='birthdate_lr' data-date="<?=date("Y-m-d")?>">
                <input type='text' class="form-control" name="birthdate_lr" value="<?=date("Y-m-d")?>" />
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div><br>
    <div class="row">
        <label class="col-md-2">Legitimate</label>
        <div class="col-md-4">
                <input name="legit_lr" class="cbox" type="checkbox"/>
        </div>
    </div>
    <div class="row">
        <label class="col-md-11"></label>
        <div class="col-md-11">
            <a href="#" class="btn blue" id="savelegit">Save</a>
        </div>
    </div>
    </form>
</div>
<script>
$("#button_save_modal").unbind("click").click(function(){
//$("#savelegit").click(function(){
 var $validator = $("#form_legit").validate({
        rules: {
            lr_name: {
              required: true,
              minlength: 2
            },
            lr_relationship: {
              required: true
            }
        }
    });
    
   if($("#form_legit").valid()){
      var cobj = "";
      $("#legitlist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='lr_name']").val());
         //$(cobj).find("td:eq(1)").text($("input[name='lr_relationship']").val());
         $(cobj).find("td:eq(1)").attr("reldata",$("select[name='lr_relationship'] option:selected").val());
         $(cobj).find("td:eq(1)").text($("select[name='lr_relationship'] option:selected").text());
         $(cobj).find("td:eq(2)").text($("input[name='lr_address']").val());
         $(cobj).find("td:eq(3)").text($("input[name='lr_contactno']").val());
         $(cobj).find("td:eq(4)").text($("input[name='birthdate_lr']").val());
         $(cobj).find("td:eq(5)").text($("input[name='legit_lr']").is(":checked")?"YES":"NO");
                                             
      }else{       
         // $("#legitlist").find("tbody").append
         var mtable = $("#legitlist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='lr_name']").val()+"</td>"); 
         //$(ntr).append("<td>"+$("input[name='lr_relationship']").val()+"</td>");
         $(ntr).append("<td reldata='"+$("select[name='lr_relationship'] option:selected").val()+"'>"+$("select[name='lr_relationship'] option:selected").text()+"</td>");
         $(ntr).append("<td>"+$("input[name='lr_address']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='lr_contactno']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='birthdate_lr']").val()+"</td>");
         $(ntr).append("<td>"+($("input[name='legit_lr']").is(":checked")?"YES":"NO")+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn orange' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addlegit($(this));
         }).appendTo($(mtd));
         $("<a class='btn orange'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
         }).appendTo($(mtd));
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#legitlist").find("tbody"));      
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

$(".chosen").chosen();
</script>