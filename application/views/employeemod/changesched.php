<?php

/**
 * @author Justin
 * @copyright 2016
 */

$datetoday = date("d-m-Y");
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background:#4a4a4a;">
                        <h5>Change Schedule Request</h5>
                    </div>
                    <div class="well-content">
                        <div class="form_row">
                            <label class="field_name align_right">Date</label>
                            <div class="field">
                                <div class="input-group date" id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$datetoday?>" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                                <div class="input-group date" id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="datesetto" type="text" value="<?=$datetoday?>" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>  
                            </div>
                        </div>
                        
                        <div class="form_row no-search">
                            <label class="field_name align_right">Category</label>
                            <div class="field">
                                <select class="form-control" id="category">
                                    <?
                                        $opt = $this->extras->showCategory();
                                        foreach($opt as $key=>$val){
                                    ?>      
                                            <option value="<?=$key?>"><?=$val?></option><?
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form_row">
                            <div class="field">
                                <a href="#" class="btn btn-primary" id="search">Search</a>
                            </div>
                        </div>
                                                
                        <div style="width: 99.7%;text-align: right;padding: 2px;"><a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal">New Request</a></div>          
                    </div>            
                    <div class="well-header" style="background: #A548A2;">
                        <h5>History</h5>
                    </div>
                    <div id="changeschedhistory" class="well-content" style="padding-bottom: 32px;"></div>
                    
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                    
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
$(document).ready(function(){  
    changesched();
});

$("#search").click(function(){
    changesched()
});

$("#newrequest").click(function(){  
    if($(this).prop("disabled")) alert("Please Attach Post Activity first.");
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "changesched_apply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

/*
 *  FUNCTIONS
 */

function changesched(){
   $("#changeschedhistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("employeemod_/fileconfig")?>",
      type     :   "POST",
      data     :   {folder: "employeemod", view: "changesched_details", category: $("#category").val(), dfrom : $("input[name='datesetfrom']").val(), dto : $("input[name='datesetto']").val()},
      success  :   function(msg){
       $("#changeschedhistory").html(msg);
      }
   });
}
$(".chosen").chosen();
$("#datesetfrom,#datesetto").datepicker({
   autoclose: true,
   todayBtn : true
});
</script>