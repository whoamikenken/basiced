<?php
/**
 * @author Max Consul
 * @copyright 2019
 *
 */
?>
<style type="text/css">

#content {
    padding: 33px !important;
}
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>

<div id="content"> <!-- Content start -->
    <div class="panel animated fadeIn delay-1s">
       <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Special Voucher</b></h4></div>
       <div class="panel-body">
           <div class="row">
                <div class="col-md-6">
                    <div class="form_row">
                        <label class="field_name align_right">Employee</label>
                        <div class="field">
                            <div class="col-md-12">
                               <select class="chosen select col-md-4 employee-list" multiple name="employee[]" id="employee">
                                    <option value="all">All Employee</option>
                                    <?
                                        $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")));
                                        foreach($opt_type as $val){
                                    ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                        }
                                    ?>
                                </select>
                                <span id="emp_remarks" style="color:red;display: none;">* required</span>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="col-md-6">
                    <div class="form_row">
                        <label class="field_name align_right">Cut-off</label>
                        <div class="field">
                            <div class="col-md-12">
                                <select class="chzn-select" name="cutoff" id="cutoff">
                                    <?= $this->extras->getPayrollCutoffSelect(""); ?>
                                </select>
                                <span id="cutoff_remarks" style="color:red;display: none;">* required</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-md-6">
                    <div class="form_row">
                        <label class="field_name align_right">Category</label>
                        <div class="field">
                            <div class="col-md-12">
                                <select class="chosen col-md-4" name="category" id="category">
                                  <option value="">Select Category</option>
                                  <option value="income">Income</option>
                                  <option value="deduction">Deduction</option>
                                  <option value="loan">Loan</option>
                                  <option value="witholdingtax">Witholding Tax</option>
                                  <option value="regdeduction">Reglamentory Deduction</option>
                                </select>
                                <span id="categ_remarks" style="color:red;display: none;">* required</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form_row">
                        <label class="field_name align_right">Account</label>
                        <div class="field">
                            <div class="col-md-12">
                                <select class="chosen col-md-4" name="account" id="account">
                                  
                                </select>
                                <span id="acc_remarks" style="color:red;display: none;">* required</span>
                            </div>
                        </div>
                    </div> 
                </div>
            </div><br>
            <div class="row">
            <div class="col-md-6">
                <div class="form_row">
                    <label class="field_name align_right">Year</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-4" name="year" id="year">
                              
                            </select>
                            <span id="year_remarks" style="color:red;display: none;">* required</span>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="col-md-6">
                <div class="form_row">
                    <label class="field_name align_right">Amount</label>
                    <div class="field">
                        <div class="col-md-12">
                           <input type="number" class="form-control" name="amount" id="amount">
                           <span id="amount_remarks" style="color:red;display: none;">* required</span>
                        </div>
                    </div>
                </div> 
            </div>
            </div><br>
            <div class="row">
                <div class="col-md-6">
                    <div class="form_row">
                        <label class="field_name align_right">Remarks</label>
                        <div class="field">
                            <div class="col-md-12">
                               <textarea class="form-control" rows="3" name="remarks" id="remarks"></textarea>
                               <span id="rem_remarks" style="color:red;display: none;">* required</span>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="col-md-6"><br>
                    <div class="form_row">
                        <div class="field">
                            <div class="col-md-12" id="save_div">
                              <button class="btn btn-primary" style="width: 100px;" id="saveencode">Save</button>
                            </div>
                            <div class="col-md-4" id="load_div" style="display: none;">
                              <img src='<?=base_url()?>images/loading.gif'/> <span>Saving.. Please wait.</span>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="animated fadeIn delay-1s">
    <div class="panel">
       <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Encoded History</b></h4></div>
       <div class="panel-body">
            <div class="form_row">
                <label class="field_name align_right">Sort History</label>
                <div class="field">
                    <div class="col-md-5">
                        <select class="chosen col-md-4" name="sort_history" id="sort_history">
                          <option value="">Select Account</option>
                          <option value="income">Income</option>
                          <option value="deduction">Deduction</option>
                          <option value="loan">Loan</option>
                          <option value="witholdingtax">Witholding Tax</option>
                          <option value="regdeduction">Reglamentory Deduction</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="encodedhistory">
                
            </div>
        </div>
    </div> 
    </div>
</div>
<script>
    validateCanWrite();
    setTimeout(function(){ 
        $(".animated").removeClass("animated fadeIn delay-1s");
     }, 1500);

    $("#employee").on("change", function(){
        var emplist = $(this).val();
        if(emplist !== null){
            $.each( emplist, function( key, value ) {
                if(value == "all"){
                    $("#employee_chosen .chosen-drop").css("pointer-events", "none");
                }
                else{
                    if(emplist != null){
                    var itemToDisable = $("option:contains('All Employee')");
                    itemToDisable.css("pointer-events", "none");
                    $("#employee").trigger("chosen:updated");
                    }
                    else{
                    
                    }
                }
            });
        }else{
            $('#employee').trigger("chosen:updated"); 
            $(".chosen-drop").css("pointer-events", "");
            var itemToEnable = $("option:contains('All Employee')");
            itemToEnable.css("pointer-events", "");
            $("#employee").trigger("chosen:updated");
        }

    });

    $(document).ready(function(){
        getAvailableYear();
        getEncodedHistory(false);
    });

    $("#employee").on("change", function(){
        var elementId = $(this).attr("id");
        var value = $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
    });

    $("#category").on("change", function(){
        var category = $(this).val();
        if(!category){
            alert('Please select a category');
        }else{
            $.ajax({
                url : "<?= site_url('extensions_/getAccountSetup') ?>",
                type : "POST",
                data : {category:category},
                success:function(response){
                    $("#account").html(response).trigger('chosen:updated')
                }
            });
        }
    });

    function getAvailableYear(){
        $.ajax({
            url : "<?= site_url('extensions_/getAvailableYearInCutoff') ?>",
            success:function(response){
                $("#year").html(response).trigger('chosen:updated');
            }
        });
    }

    function getEncodedHistory(type = ''){
        $.ajax({
            url : "<?= site_url('extensions_/loadEncodedHistory') ?>",
            type : "POST",
            data : {type:type},
            success:function(response){
                $("#encodedhistory").html(response);
            }
        });
    }

    $("#saveencode").click(function(){
        var category = $("#category").val();
        if(category != "witholdingtax"){
            var data = {
                employee_list : $("#employee").val(),
                category : $("#category").val(),
                account : $("#account").val(),
                year : $("#year").val(),
                amount : $("#amount").val(),
                cutoff : $("#cutoff").val(),
                remarks : $("#remarks").val()
            };
        }else{
             var data = {
                employee_list : $("#employee").val(),
                category : $("#category").val(),
                year : $("#year").val(),
                amount : $("#amount").val(),
                cutoff : $("#cutoff").val(),
                remarks : $("#remarks").val()
            };
        }

        if($("#employee").val() == "" || $("#category").val() == "" || $("#account").val() == "" || $("#year").val() == "" || $("#amount").val() == "" || $("#cutoff").val() == "" || $("#remarks").val() == ""){
            alert("All fields are required.");

            if(!$("#employee").val()) $("#emp_remarks").show();
            else $("#emp_remarks").hide();

            if(!$("#category").val()) $("#categ_remarks").show();
            else $("#categ_remarks").hide();

            if(!$("#account").val()) $("#acc_remarks").show();
            else $("#acc_remarks").hide();

            if(!$("#year").val()) $("#year_remarks").show();
            else $("#year_remarks").hide();

            if(!$("#cutoff").val()) $("#cutoff_remarks").show();
            else $("#cutoff_remarks").hide();

            if(!$("#remarks").val()) $("#rem_remarks").show();
            else $("#rem_remarks").hide();

            if(!$("#amount").val()) $("#amount_remarks").show();
            else $("#amount_remarks").hide();
            setTimeout( function(){ 
                $("#emp_remarks, #categ_remarks, #acc_remarks, #year_remarks, #cutoff_remarks, #rem_remarks, #amount_remarks").fadeOut("slow");
            }  , 2000 );
            
        }
        $("#save_div").hide();
        $("#load_div").show();
        $.ajax({
            url  : "<?= site_url('extensions_/validateEncodedData') ?>",
            type : "POST",
            data : data,
            success:function(response){
                // alert(response);
                // console.log(response);
                $("#load_div").hide();
                $("#save_div").show();
                category = $("#category").val();
                $(':input').val('');
                $('option').attr('selected', false).trigger('chosen:updated');
                $('textarea').val('');
                getEncodedHistory();
            }
        });

    });

    function validateCanWrite(){
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#saveencode").css("pointer-events", "none");
        else $("#saveencode").css("pointer-events", "");
    }

    $(".chosen").chosen();

</script>