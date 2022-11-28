<?php

/**
 * @author Justin
 * @copyright 2015
 */

?>

<div id="content"> <!-- Content start -->
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="panel animated fadeIn delay-1s">
           <div class="panel-heading"><h4><b>Manage Official Schedule</b></h4></div>
               <div class="panel-body">
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <div class="col-md-12" id="employeediv">
                        <select class="chosen col-md-6" name="employeeid" id="employeeid" >
                            <option value="">All Employee</option>
                          <?
                          $opt_type = $this->employee->loadallemployee("","lname,fname,mname");
                          foreach($opt_type as $val){
                          ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                          }
                        ?>
                        </select>
                         
                    </div>
                    <input type="checkbox" id="chk1" name="chkemp" value="chkemp" />
                </div>
            </div>
            <br><br>    
            <div class="form_row">
                <label class="field_name align_right">Shift Type</label>
                <div class="field">
                    <div class="col-md-12" id="shiftdiv">
                        <select class="chosen col-md-6" name="emptype" id="emptype">
                        <?
                        $opt_type = $this->extras->showemployeetype();
                        foreach($opt_type as $code=>$val){
                        ?><option value="<?=$code?>"><?=$val?></option>
                        <?}?>
                        </select>
                    </div>
                    <input type="checkbox" id="chk1" name="chkshift" value="chkshift" />
                </div>
            </div>
            <br><br>    
            <div class="form_row">    
                <div class="field">
                    <a href="#" class="btn btn-primary" id="search_button">Search</a>
                </div>
            </div>
            <div id="employeesched" style="padding: 5px;"></div>
            </form>            
            </div>

</div>
</div>
</div>
</div>
</div>
<script>
$(document).ready(function(){
   $("#employeediv").hide(); 
   $("#shiftdiv").hide();
});    

$("input[type='checkbox']").on('change', function() {
    $("input[type='checkbox']").not(this).prop('checked', false);
    //alert($("#chk1:checked").val());
    if($("#chk1:checked").prop('checked',true)){
    
        if($(this).val() == "chkemp"){
            $("#employeediv").show('slow','linear');
            $(this).hide('slow','linear');
            $("#shiftdiv").hide('slow','linear');
            $("input[type='checkbox']").not(this).show('slow','linear');
            $("#employeesched").hide();
            //$("#emptype").val("");
        }
        if($(this).val() == "chkshift"){
            $("#shiftdiv").show('slow','linear');
            $(this).hide('slow','linear');
            $("#employeediv").hide('slow','linear');
            $("input[type='checkbox']").not(this).show('slow','linear');
            $("#employeesched").hide();
            //$("#employeeid").val("");
        }
        
    }
});

$("#search_button").click(function(){
    if($("#chk1:checked").val() == "chkemp"){
        if($("#employeeid").val() == ""){
            alert("Please select an employee first.");
            $("#employeesched").hide();
        }else{
            var employeeid = $("select[name='employeeid']").val();
            var chkopt = $("#chk1:checked").val();
            //alert(chkopt);
            $("#employeesched").show();
            $.ajax({
                    url: "<?=site_url("process_/manageschedule")?>",
                    type: "POST",
                    data: {employeeid:employeeid, chkopt:chkopt},
                    success: function(msg){
                        $("#employeesched").html(msg);
                    }
                });
        }
    }
    else if($("#chk1:checked").val() == "chkshift"){
        if($("#emptype").val() == ""){
            alert("Please select shift type first.");
            $("#employeesched").hide();
        }else{
            var shifttype = $("#emptype").val();
            var chkopt = $("#chk1:checked").val();
            //alert(chkopt);
            $("#employeesched").show();
            $.ajax({
                    url: "<?=site_url("process_/manageschedule")?>",
                    type: "POST",
                    data: {shifttype:shifttype, chkopt:chkopt},
                    success: function(msg){
                        $("#employeesched").html(msg);
                    }
                });
        }
    }
    else{
        alert("Please Select atleast one checkbox..");
    }
    
});
$('.chosen').chosen();
</script>