<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */



?>
<div id="content"> <!-- Content start -->
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="panel animated fadeIn delay-1s">
           <div class="panel-heading"><h4><b>Daily Time Record</b></h4></div>   
            <form id="request_form">
            <div class="panel-body">  
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <div class="col-md-12">
                        <select class="chosen col-md-6" name="employeeid">
                            <option value="">All Employee</option>
                        <?
                          $opt_type = $this->employee->loadallemployee("","lname,fname,mname");
                          foreach($opt_type as $val){
                          ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                          }
                        ?>
                        </select>
                        
                    </div>
                </div>
            </div>    
            <div class="form_row">    
                <div class="field">
                    <a href="#" class="btn btn-primary" id="search_button">Search</a>
                </div>
            </div>
            </form>            
            </div>
            <div id="employeesched" style="padding: 5px;"></div>

</div>
</div>
</div>
</div>
</div>
<script>
/**
$("#employeeid")
.click(function(){
    $(this).select();
})
.keypress(function(e){
    if(e.keyCode==13){
      $("#search_button").click();
      return false;
    }
});
$("#employeeid").autocomplete({
    source: function( request, response ){
        $.ajax({
            url: "<?=site_url("process_/searchemployee")?>",
            type: "POST",
            data: {
               s : request.term    
            },
            success: function(msg) {
                response(eval(msg));
            }
        });    
       }     
});
*/
$("#search_button").click(function(){
  
   if($("select[name='employeeid']").val()){
       $.ajax({
          url : "<?=site_url("process_/verifyemployee")?>",
          type: "POST",
          data: $("#request_form").serialize(),
          success: function(msg){
            var status = $(msg).find("status:eq(0)").text();
            var cutoffid = $(msg).find("cutoffid:eq(0)").text();
            var fullname = $(msg).find("fullname:eq(0)").text();
            var employeeid = $(msg).find("employeeid:eq(0)").text();
            
            if(status==0){
                alert("Please generate cut-off first.");
                $("#employeesched").html("");
                return;
            }else{
                $.ajax({
                    url: "<?=site_url("process_/displayschedule_dtr")?>",
                    type: "POST",
                    data: {cutoffid:cutoffid,employeeid:employeeid},
                    success: function(msg){
                        /** Then the body */
                        $("#employeesched").html(msg);
                    }
                });
            }
          }
       }); 
   }else {
       alert("Please select an employee first.");
       return false;
   }
});
$('.chosen').chosen();
</script>