<?php

/**
 * @author Justin
 * @copyright 2015
 */

if($this->input->post("id")){
    $query          = $this->payroll->processeddata($this->input->post("id"));
    $id             = $query->row(0)->id;
    $empid          = $query->row(0)->employeeid;
    $schedule       = $query->row(0)->schedule;
    $cutoffstart    = $query->row(0)->cutoffstart;
    $cutoffend      = $query->row(0)->cutoffend;
    $quarter        = $query->row(0)->quarter;
    $salary         = $query->row(0)->salary;
    $income         = $query->row(0)->income;
    $withholdingtax = $query->row(0)->withholdingtax;
    $fixeddeduc     = $query->row(0)->fixeddeduc;
    $otherdeduc     = $query->row(0)->otherdeduc;
    $loan           = $query->row(0)->loan;
    $tardy          = $query->row(0)->tardy;
    $absents        = $query->row(0)->absents;
}
?>

<form id="modPayroll" autocomplete="off">
<input name="model" value="modPayroll" hidden=""/>
<input type="hidden" name="id" value="<?=$id?>" />
<input type="hidden" name="schedule" value="<?=$schedule?>" />
<input type="hidden" name="cutoffstart" value="<?=$cutoffstart?>" />
<input type="hidden" name="cutoffend" value="<?=$cutoffend?>" />
<input type="hidden" name="quarter" value="<?=$quarter?>" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><b>Modify Payroll Processed Data</b></h4>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form_row">
                    <label class="field_name align_left" style="width: 30%; display: inline-block;"><b>Employee ID</b></label>
                    <div class="field">
                        <input class="align_center" type="text" name="empid" value="<?=$empid?>" readonly=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_left" style="width: 30%; display: inline-block;"><b>Regular Pay</b></label>
                    <div class="field">
                        <input class="align_center" type="text" name="salary" value="<?=$salary?>" readonly=""/>
                    </div>
                </div>
                    <?
                    $x = 1;
                    if($this->payrolloptions->incometitlep('',$schedule,$quarter,$cutoffstart,$cutoffend)){
                        foreach($this->payrolloptions->incometitlep('',$schedule,$quarter,$cutoffstart,$cutoffend) as $row){
                            if($row){
                            ?>
                            <div class="form_row">
                               <label class="field_name align_left" style="width: 30%; display: inline-block;"><b><?=$this->payrolloptions->incomedesc($row)?></b></label> <!-- INCOME TITLE-->
                               <div class="field">
                                    <input class="align_center" type="text" name="income<?=$x?>" value="<?=$this->payrolloptions->modpayroll($empid,$schedule,$quarter,$cutoffstart,$cutoffend,'income',$row);?>"/>
                                </div>
                            </div>
                            <?
                            $x++;
                            }
                        }
                    }
                    ?>
                    <div class="form_row">
                        <label class="field_name align_left" style="width: 30%; display: inline-block;"><b>WithHolding Tax</b></label> 
                        <div class="field">
                            <input class="align_center" type="text" name="whtax" value="<?=$withholdingtax?>"/>
                        </div>
                    </div>
                    <?
                    $x = 1;
                    if($this->payrolloptions->deducttitlep('',$schedule,$quarter,$cutoffstart,$cutoffend)){
                        foreach($this->payrolloptions->deducttitlep('',$schedule,$quarter,$cutoffstart,$cutoffend) as $row){
                            if($row){
                            ?>
                            <div class="form_row">
                                <label class="field_name align_left" style="width: 30%; display: inline-block;"><b><?=$row?></b></label> <!-- FIXED DEDUCTIONS TITLE-->
                                <div class="field">
                                    <input class="align_center" type="text" name="fixd<?=$x?>" value="<?=$this->payrolloptions->modpayroll($empid,$schedule,$quarter,$cutoffstart,$cutoffend,'fixeddeduc',$row);?>"/>
                                </div>
                            </div>
                            <?
                            $x++;
                            }
                        }
                    }
                    ?>
                    <?   
                    $x = 1;
                    if($this->payrolloptions->loantitlep('',$schedule,$quarter,$cutoffstart,$cutoffend)){
                        foreach($this->payrolloptions->loantitlep('',$schedule,$quarter,$cutoffstart,$cutoffend) as $row){
                            if($row){
                            ?>
                            <div class="form_row">
                                <label class="field_name align_left" style="width: 30%; display: inline-block;"><b><?=$this->payrolloptions->loandesc($row)?></b></label> <!-- LOANS TITLE-->
                                <div class="field">
                                    <input class="align_center" type="text" name="loans<?=$x?>" value="<?=$this->payrolloptions->modpayroll($empid,$schedule,$quarter,$cutoffstart,$cutoffend,'loan',$row);?>"/>
                                </div>
                            </div>
                            <?
                            $x++;
                            }
                        }
                    }
                    ?>
                    <?
                    $x = 1;
                    if($this->payrolloptions->deducttitleothp('',$schedule,$quarter,$cutoffstart,$cutoffend)){
                        foreach($this->payrolloptions->deducttitleothp('',$schedule,$quarter,$cutoffstart,$cutoffend) as $row){
                            if($row){
                            ?>
                            <div class="form_row">
                                <label class="field_name align_left" style="width: 30%; display: inline-block;"><b><?=$this->payrolloptions->deductiondesc($row)?></b></label> <!-- OTHER DEDUCTIONS TITLE-->
                                <div class="field">
                                    <input class="align_center" type="text" name="otd<?=$x?>" value="<?=$this->payrolloptions->modpayroll($empid,$schedule,$quarter,$cutoffstart,$cutoffend,'otherdeduc',$row);?>"/>
                                </div>
                            </div>
                            <?
                            $x++;
                            }
                        }
                    }
                    ?>
                    <div class="form_row">
                        <label class="field_name align_left" style="width: 30%; display: inline-block;"><b>Tardy</b></label> 
                        <div class="field">
                            <input class="align_center" type="text" name="tardy" value="<?=$tardy?>"/>
                        </div>
                    </div>
                    <div class="form_row">
                        <label class="field_name align_left" style="width: 30%; display: inline-block;"><b>EA</b></label> 
                        <div class="field">
                            <input class="align_center" type="text" name="absents" value="<?=$absents?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$("#save").click(function(){
    var form_data   =   $("#modPayroll").serialize();
    //alert(form_data.toString());
    $("#saving").hide();
    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..")
    $.ajax({
       url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        alert(msg);
        $("#close").click();
        location.reload();
       }
    });
});
</script>