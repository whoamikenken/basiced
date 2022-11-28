<?php

/**
 * @author Justin
 * @copyright 2015
 */

$curr_date = date('Y-m-d');
?>
<div id="content">
<div class="widgets_area">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well blue">
                            <div class="well-header">
                                <h5>Overtime Management</h5>
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
                            
                                <div id="absentmngmnt">
                                    <div class="form_row">
                                        <label class="field_name align_right">Date</label>
                                        <div class="field">
                                            <div class="input-group date" id="dset" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                                <input class="align_center" size="16" name="dset" type="text" value="<?=$curr_date?>" readonly>
                                                <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                            </div>
                                            <div class="input-group date" id="dsetto" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                                <input class="align_center" size="16" name="dsetto" type="text" value="<?=$curr_date?>" readonly>
                                                <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="form_row">
                                        <label class="field_name align_right">Department</label>
                                        <div class="field">
                                                <select class="chosen col-md-6" name="deptid">
                                                  <option value="">All Department</option>
                                                <?
                                                  $opt_department = $this->extras->showdepartment();
                                                  foreach($opt_department as $c=>$val){
                                                  ?><option value="<?=$c?>"><?=$val?></option><?
                                                  }
                                                ?>
                                                </select>
                                        </div>
                                    </div>
                                    -->
                                    <div class="form_row">
                                        <label class="field_name align_right">Employee</label>
                                        <div class="field">
                                                <select class="chosen col-md-6" name="employeeid">
                                                    <option value="">All Employee</option>
                                                <?
                                                  $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true);
                                                  foreach($opt_type as $val){
                                                  ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                                  }
                                                ?>
                                                </select>
                                        </div>
                                    </div>
                                    <div class="form_row">
                                        <div class="field">
                                            <div id="load" hidden=""></div>
                                            <a href="#" class="btn btn-primary" id="searchbtn">Search</a>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                                    <div id="displayot"></div><br />
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
</div>    
</div>
<script>
$("#searchbtn").click(function(){
    if($("select[name='employeeid']").val() == "")  alert("Employee is Required");
    else{
    $("#displayot").html("<img src='<?=base_url()?>images/loading.gif'>Loading, please wait...");
    $.ajax({
        url: "<?=site_url("process_/showindividualot")?>",
        type: "POST",
        data: {
           dset     :   $("input[name='dset']").val(),
           dsetto   :   $("input[name='dsetto']").val(),
           //deptid: $("select[name='deptid']").val(),
           fv       :   $("select[name='employeeid']").val()
        },
        success: function(msg) {
           $("#displayot").html(msg);
        }
    });  
    } 
});
$('.chosen').chosen();
$("#dset,#dsetto").datepicker({
    autoclose: true,
    todayBtn : true
});
</script> 
