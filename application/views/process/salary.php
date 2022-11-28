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
                <h5>Attendance</h5>
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
            <div class="well-content">
            <div class="form_row">
                <label class="field_name align_right">Processed Cut-off</label>
                <div class="field">
                    <div class="col-md-12">
                        <select class="chosen col-md-6" name="cutoffid">
                            <option value=""></option>
                        <?
                          $opt_type = $this->extras->showcutoffprocessed();
                          foreach($opt_type as $c => $val){
                          ?><option value="<?=$c?>"><?=$val?></option><?    
                          }
                        ?>
                        </select>
                        
                    </div>
                </div>
            </div>  
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
                        
            </div>
            <div id="displaydtr" style="padding: 5px;"></div>

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
            url: "site_url("process_/searchemployee")?>",
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
   $.ajax({
      url : "<?=site_url("process_/salary")?>",
      type: "POST",
      data: {employeeid:$("select[name='employeeid']").val(),cid:$("select[name='cutoffid']").val()},
      success: function(msg){
          $("#displaydtr").html(msg);      
      }
   }); 
   return false;
});
$('.chosen').chosen();
</script>