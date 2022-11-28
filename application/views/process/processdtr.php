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
        <div class="well blue">
            <div class="well-header">
                <h5>Process Daily Time Record</h5>
                <ul>
                    <li class="color_pick"><a href="#"><i class="glyphicon glyphicon-th"></i></a>
                        <ul>
                            <li><a class="blue set_color" href="#"></a></li>
                            <li><a class="light_blue set_color" href="#"></a></li>
                            <li><a class="grey set_color" href="#"></a></li>
                            <li><a class="pink set_color" href="#"></a></li>
                            <li><a class="red set_color" href="#"></a></li>
                            <li><a class="orange set_color" href="#"></a></li>
                            <li><a class="yellow set_color" href="#"></a></li>
                            <li><a class="green set_color" href="#"></a></li>
                            <li><a class="dark_green set_color" href="#"></a></li>
                            <li><a class="turq set_color" href="#"></a></li>
                            <li><a class="dark_turq set_color" href="#"></a></li>
                            <li><a class="purple set_color" href="#"></a></li>
                            <li><a class="violet set_color" href="#"></a></li>
                            <li><a class="dark_blue set_color" href="#"></a></li>
                            <li><a class="dark_red set_color" href="#"></a></li>
                            <li><a class="brown set_color" href="#"></a></li>
                            <li><a class="black set_color" href="#"></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <form id="request_form">
            <div class="well-content">
            <!--
            <div class="form_row">
                <div class="field">
                    <a class="btn btn-primary" id="processdtr" href="#modal-view" data-toggle="modal">Process Daily Time Record</a>
                </div>
            </div>
            -->
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
                    <a class="btn btn-primary" id="search_button">Display Time Record</a>
                </div>
            </div>
            </form>            
            </div>
            <div id="displaydtr" style="padding: 5px;"></div>

</div>
</div>
</div>
</div>
</div>
<script>
$("#search_button").click(function(){
   
    
   if($("select[name='employeeid']").val()){
       $.ajax({
          url : "<?=site_url("process_/viewdtr")?>",
          type: "POST",
          data: {employeeid:$("select[name='employeeid']").val()},
          success: function(msg){
              $("#displaydtr").html(msg);      
          }
       }); 
   }else {
        alert("Please select an employee first.");
       return false;
   }
});
$("#processdtr").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Processing Daily Time Record");  
    var form_data = {
        message: "This may take a while, please wait...",
        view: "process/loading"
    };

    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
           
            $.when($.ajax({
                url: "<?=site_url("process_/processdtr")?>",
                type: "POST",
                data: {},
                success: function(msg) {
                  if($(msg).find("result:eq(0)").text()==0){
                     $("#modal-view").find("div[tag='display']").html($(msg).find("message:eq(0)").text());
                     $("#modalclose").delay(1200).click();
                     return;
                  }
                }
            })).done(function(){
            $.ajax({
                    url: "<?=site_url("process_/processpayrollcutoff")?>",
                    type: "POST",
                    data: {},
                    success: function(msg) {  
                      $("#shademessage_extra").html("Done processing...");
                      $("#modalclose").delay(1200).click();
                    }
                });
             });    
        }
    });  
});
$('.chosen').chosen();
</script>