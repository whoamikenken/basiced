<?php

$curr_date = date('Y-m-d');
?>


<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                   <div class="panel-heading"><h4><b>Service Credit Management</b></h4></div>
                   <div class="panel-body">   
                        <div class="form_row">
                            <label class="field_name align_right">Date</label>
                            <div class="field">
                                <div class="col-md-12"style="padding-left: 0px;">
                                  <div class="col-md-5" style="padding-left: 0px;">
                                    <div class='input-group date' id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                      <input type='text' class="form-control" size="16" name="ldfrom" type="text" value="<?=$curr_date?>"/>
                                      <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                    </div>
                                  </div>
                              <div class="col-md-5">
                                <div class='input-group date' id="ldto" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                  <input type='text' class="form-control" size="16" name="ldto" type="text" value="<?=$curr_date?>"/>
                                  <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                              </div>
                                </div>
                            </div>
                        </div>                          
                        <br>
                        <div class="form_row no-search">
                            <div class="field">
                            <div class="col-md-5" style="padding-left: 0px">
                              <select class="select blue" id="category">
                                <?
                                    $opt = $this->extras->showCategory();
                                    foreach($opt as $key=>$val){
                                    if($key == "PENDING")  $sel = " selected";
                                    else                    $sel = "";
                                ?>      
                                        <option value="<?=$key?>" <?=$sel?>><?=$val?></option><?
                                    }
                                ?>
                                </select>
                            </div>
                              <div class="col-md-5">
                                  <div id="load" hidden=""></div>
                                  <a href="#" class="btn btn-primary" id="searchlbtn">Search</a>
                              </div>  
                            </div>
                        </div><br>
                    <div id="managesc"></div> 
                    <div id="managescu"></div> 
                    </div>
                </div>    
            </div>
        </div>
    </div>    
</div>
<script>
$(document).ready(function(){   
    loadSCAppList('','','PENDING');
});

$("#searchlbtn").click(function(){
    // ot_leave_status();
    var category = $("#category").val(), 
        dfrom    = $("input[name='ldfrom']").val(), 
        dto      = $("input[name='ldto']").val();
    loadSCAppList(dfrom, dto, category);
    loadSCUAppList(dfrom, dto, category);
});

function loadSCAppList(datefrom, dateto, status){
    var form_data = {
                        datefrom    : datefrom, 
                        dateto      : dateto,
                        status      : status
                    }
    $("#managesc").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("service_credit_/getSCAppListToManage")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#managesc").html(msg);
       }
    });
	loadSCUAppList(datefrom,dateto,status);
}

function loadSCUAppList(datefrom, dateto, status){
    var form_data = {
                        datefrom    : datefrom, 
                        dateto      : dateto,
                        status      : status
                    }
    $("#managesc").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("service_credit_/getSCUAppListToManage")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#managescu").html(msg);
       }
    });
}

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$("#category").chosen();

</script> 
