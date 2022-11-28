<?php

/**
 * @author Justin
 * @copyright 2016
 */

$datetoday = date('Y-m-d');

$dept = $this->extras->getemployeecol($this->session->userdata("username"),"deptid");
//$cnoti = $this->employeemod->manageseminarnotif(true)->num_rows();
$cnoti = $this->employeemod->manageseminarnotifnew();

?>
<div id="content"> <!-- Content start -->

    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #3b5998;">
                        <h5>Manage Professional Development</h5>
                    </div>
                    <div class="well-content">
                        <div class="field no-search">
                            <label class="field_name">Category</label>
                            <select class="select blue" id="category">
                                <?
                                    $opt = $this->extras->showCategory();
                                    foreach($opt as $key=>$val){
                                        if($key == "PENDING")  $sel = " selected";
                                        else                    $sel = "";
                                        // if($this->employeemod->manageseminarnotif(true)->num_rows() > 0 && $key == "APPROVED") $sel = " selected";
                                ?>      
                                        <option value="<?=$key?>" <?=$sel?>><?=$val?></option><?
                                    }
                                ?>
                            </select>
                        </div><br />
                        <label class="field_name">Date</label>
                        <div class="input-group date" id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$datetoday?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <div class="input-group date" id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetto" type="text" value="<?=$datetoday?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>  
                        <a href="#" class="btn btn-primary" id="search">Search</a>  
                        <!-- <?if($this->employee->getHeadDeptCode($this->session->userdata("username"))){?>
                            <div style="width: 99.7%;text-align: right;padding: 2px;"><a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal">New Request</a></div>
                        <?}?> -->           
                    </div>            
                    <div class="well-header" style="background: #3b5998;">
                        <h5>Application</h5>
                    </div>
                    <div id="manageseminar" style="padding-bottom: 31px;"></div>
                    
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                    
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
$(document).ready(function(){   
    if("<?=$cnoti?>" > 0)   view_seminar_status("<?=$cnoti?>"); 
});

$("#search").click(function(){
    view_seminar_status();
});

function view_seminar_status(cnoti=""){
    var form_data = {
                        folder   : "employeemod", 
                        view     : "mailseminarapp_details",
                        category : $("#category").val(), 
                        dfrom    : $("input[name='datesetfrom']").val(), 
                        dto      : $("input[name='datesetto']").val(),
                        cnoti    : cnoti 
                    }
    $("#manageseminar").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#manageseminar").html(msg);
       }
    });
}
$("#newrequest").click(function(){
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "seminarapply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});
$("#dfrom,#datesetfrom,#datesetto").datepicker({
    autoclose: true,
    todayBtn : true
});
$("#category").chosen();

/*$( "#upload" ).submit(function( event ) {
  if ( $('#file').get(0).files.length === 0 ) {
    $( "#msg" ).text( "No files selected.." ).show();
    return false;
  }
});*/
</script> 
