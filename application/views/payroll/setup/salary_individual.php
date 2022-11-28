 <?
 $emp = "";
 if($this->input->post("eid")){
     foreach($this->input->post("eid") as $key=>$val){
        if($emp)    $emp .= ",";
        $emp .= $val;
     }    
 }
 $data['eid'] = $emp;

?>
<style>
#myModal{
    width: 1160px;
    left: 0;
    right: 0;
    margin: 0 auto;
}
</style>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <span class="pull-right"><a href="#" class="btn btn-danger" close>X</a></span>
                    <form id="myform">  
                        <input type="hidden" name="model"   value="batchencode" />
                        <input type="hidden" name="dept"    value="<?=$this->input->post("dept")?>" />
                        <input type="hidden" name="tnt"     value="<?=$this->input->post("tnt")?>" />
                        <input type="hidden" name="estat"   value="<?=$this->input->post("estat")?>" />
                        <input type="hidden" name="eid"     value="<?=$emp?>" />
                        <input type="hidden" name="cat"     value="<?=$this->input->post("cat")?>" />
                        <div class="col-md-12" style="margin-bottom: 20px;">
                            <div class="col-md-8" >

                                    <?if($this->input->post("cat") == 6){?>
                                     <div class="col-md-12">  
                                        <h4>Deduction</h4>
                                            <div class="form_row">
                                                <label class="field_name">Deduction</label>
                                                <div class="field no-search">
                                                        <select id="deduction_drop" name="deduction_drop" class="chosen required">
                                                        <?=$this->payrolloptions->deduction();?>
                                                        </select>
                                                </div>
                                            </div>
                                            <div class="form_row" style="display: none;" >
                                                <label class="field_name">Member ID</label>
                                                <div class="field">
                                                     <input class="align_right col-md-4" id="memberid" name="memberid" type="text" value=""/>
                                                </div>
                                            </div>
                                            <div class="form_row" hidden="">
                                                <label class="field_name">Starting Date</label>
                                                <div class="field">
                                                         <div class="input-group date" id="datefrom" data-date="" data-date-format="yyyy-mm-dd">
                                                            <input size="16" class="align_center" type="text" name="datefrom" value="" readonly>
                                                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                         </div>
                                                    <!--
                                                    To   <div class="input-group date" id="dateto" data-date="" data-date-format="yyyy-mm-dd">
                                                            <input size="16" class="align_center" type="text" name="dateto" value="<?=$dateto?>" readonly>
                                                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                         </div>
                                                    -->
                                                </div>
                                            </div>
                                            <div class="form_row">
                                                <label class="field_name">Amount</label>
                                                <div class="field">
                                                     <input class="align_right col-md-4 required" id="amountdeduct" name="amountdeduct" type="text" value=""/>
                                                </div>
                                            </div>
                                            <div class="form_row">
                                                <label class="field_name">No. of Cut-off</label>
                                                <div class="field">
                                                     <input class="align_right col-md-4" id="nocutoff" name="nocutoff" type="text" value=""/>
                                                </div>
                                            </div>
                                           
                                            <div class="form_row">
                                                <label class="field_name">Schedule</label>
                                                <div class="field no-search">
                                                        <select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule()?></select>
                                                </div>
                                            </div>
                                            <div class="content" style="margin-top: 3px;" id="qload" hidden=""></div>
                                            <div class="form_row" id="qshow" >
                                                <label class="field_name">Quarter</label>
                                                <div class="field no-search">
                                                        <select id="period_drop" name="period_drop" class="form-control">
                                                        <?=$this->payrolloptions->quarter('',FALSE,'weekly');?>
                                                        </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?}?>
                                    <?if($this->input->post("cat") == 7 || $this->input->post("cat") == 10){
                                        $isAdjustment = $this->input->post("cat") == 10 ? true:false;
                                    ?>
                                    <div class="col-md-12">  
                                        <h4>Income <?=$isAdjustment?'ADJUSTMENT':''?></h4>
                                        <div class="form_row">
                                            <label class="field_name">Income <?=$isAdjustment?'ADJ':''?></label>
                                            <div class="field no-search">
                                                <select id="income_drop" name="income_drop" class="chosen required" name="tax_status"><?=$this->payrolloptions->income();?></select>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Start Date</label>
                                            <div class="field">
                                                <div class="input-group date" id="datefrom" data-date="" data-date-format="yyyy-mm-dd">
                                                    <input size="16" class="align_center required" type="text" name="datefrom" value="" readonly>
                                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Amount</label>
                                            <div class="field">
                                                <input class="align_right col-md-4 required" id="amountincome" name="amountincome" type="text" value=""/>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">No. of Cut-off</label>
                                            <div class="field">
                                                <input class="align_right col-md-4 required" id="nocutoff" name="nocutoff" type="text" value=""/>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Schedule</label>
                                            <div class="field no-search">
                                                <select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule();?></select>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Quarter</label>
                                            <div class="field no-search">
                                                <select id="period_drop" name="period_drop" class="form-control"><?=$this->payrolloptions->quarter("","","weekly");?></select>
                                            </div>
                                        </div>

                                        <?if($isAdjustment){?>
                                            <div class="form_row">
                                                <label class="field_name">Deduct</label>
                                                <div class="field" style="margin-top: 10px;">
                                                     <input type="radio" name="deduct" value="1" > YES &nbsp;
                                                     <input type="radio" name="deduct" value="0" checked=""> NO
                                                </div>
                                            </div>
                                        <?}?>

                                    </div>
                                    <?}?>
                                    <?if($this->input->post("cat") == 8){?>
                                    <div class="col-md-12">
                                        <h4>Loan</h4>
                                        <div class="form_row">
                                            <label class="field_name">Loan</label>
                                            <div class="field no-search">
                                                <select id="dloan_drop" name="dloan_drop" class="chosen required" name="dtax_status"><?=$this->payrolloptions->loan();?></select>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name ">Based on</label>
                                            <div class="field no-search">
                                                <select class="form-control" name="basdeon" id="basedon">
                                                    <option value="">-- Select --</option> 
                                                    <option value="1">Monthly</option> 
                                                    <option value="0">Term</option> 
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form_row">
                                            <label class="field_name">Deduction Date</label>
                                            <div class="field">
                                                <div class="input-group date" id="ddatefrom" data-date="" data-date-format="yyyy-mm-dd">
                                                    <input size="16" class="align_center required" type="text" name="ddatefrom" value="" readonly>
                                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Starting Balance</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="startingamountloan" name="startingamountloan" type="text" />
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Current Balance</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="currentamount" name="currentamount" type="text" disabled="" style='background-color:#E8E8E8'   />
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">No. of Cut-off</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="nocutoff" name="nocutoff" type="text" disabled="" style='background-color:#E8E8E8' />
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Amount</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="amountloans" name="amountloans" type="text" disabled="" style='background-color:#E8E8E8' />
                                            </div>
                                        </div>
                                        <!-- <div class="form_row">
                                            <label class="field_name">Starting Balance</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="startingamount" name="dstartingamount" type="text" value=""/>
                                            </div>
                                        </div> -->
                                      <!--   <div class="form_row">
                                            <label class="field_name">Amount</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="amountloan" name="damountloan" type="text" value="" readonly/>
                                            </div>
                                        </div>-->
                                        <div class="form_row" id='famounts' style="display: none">
                                            <label class="field_name">Last Amount</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="famount" name="dfamount" type="text" value="" readonly/>
                                            </div>
                                        </div>
                                        <!-- <div class="form_row">
                                            <label class="field_name">No. of Cut-off</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="nocutoff" name="dnocutoff" type="text" value=""/>
                                            </div>
                                        </div> -->
                                        <div class="form_row">
                                            <label class="field_name">Schedule</label>
                                            <div class="field no-search">
                                                <select class="chosen align_left" name="dschedule" id="dschedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
                                            </div>
                                        </div>
                                        <div class="content" style="margin-top: 3px;" id="qload" hidden=""></div>
                                        <div class="form_row" id="qshow" hidden="">
                                            <label class="field_name">Quarter</label>
                                            <div class="field no-search">
                                                <select id="dperiod_drop" name="dperiod_drop" class="form-control"><?=$this->payrolloptions->quarter($cperiod,FALSE,$schedule);?></select>
                                            </div>
                                        </div>
                                    </div>
                                    <?}?> 
                                    <?if($this->input->post("cat") == 9){?>
                                    <div class="col-md-12">
                                        <h4>Other Income</h4>
                                        <div class="form_row">
                                            <label class="field_name">Income</label>
                                            <div class="field no-search">
                                                <select id="othincome_drop" name="othincome_drop" class="chosen required" name="tax_status"><?=$this->payrolloptions->incomeoth();?></select>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Amount</label>
                                            <div class="field">
                                                 <input class="align_right col-md-4 required" id="othamountincome" name="othamountincome" type="text" value=""/>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                            <label class="field_name">Position</label>
                                            <div class="field no-search">
                                                 <select class="form-control" name="othpos" id="othpos">
                                                    <option value="lower" >Lower</option>
                                                    <option value="upper" >Upper</option>
                                                 </select>
                                            </div>
                                        </div>
                                    </div>            
                                    <?}?>


                    </form>

                                    <div class="col-md-8" style="margin-top: 10px;margin-bottom: 20px;" >
                                        <div class="form_row">
                                        </div>
                                        <div class="form_row">
                                            <div class="field">
                                                <div id="dhide" hidden=""></div>
                                                <div id="dshow">
                                                    <a href="#" class="btn btn-primary" id="savesalary">Save</a>
                                                    <a href="#" class="btn btn-danger" close>Close</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form_row">
                                        </div>
                                    </div>

                                    <?if($this->input->post("cat") == 6){
                                        $this->load->view('batch_encode/be_deduction_list',$data);
                                    }?> 

                                    <?if($this->input->post("cat") == 7){
                                         $this->load->view('batch_encode/be_income_list',$data);
                                    }?>
                                    <?if($this->input->post("cat") == 8){
                                        $this->load->view('batch_encode/be_loan_list',$data);
                                    }?> 
                                    <?if($this->input->post("cat") == 9){
                                        $this->load->view('batch_encode/be_income_oth_list',$data);
                                    }?>
                                    <?if($this->input->post("cat") == 10){
                                        $this->load->view('batch_encode/be_income_adj_list',$data);
                                    }?>

                            </div>
                        </div>
                    
</div>
</div>
</div>





<script>
    $("a[close]").click(function(){   $("#myModal").modal('toggle');  });

    $("#savesalary").click(function(){

        $validator = $("#myform").validate({
             
        });
        if($(".required").val() == ""){
            var required = $(".required").parent().parent().first().find('label').html();
            alert("Required "+required);
        }else{

            $(".required").rules("add", { 
              required:true
            });

            if($("#myform").valid()){
                var form_data = $("#myform").serialize();
                form_data += "&nocutoff="+$("#nocutoff").val();
                if ( $("#basedon").val() == 0) {
                    form_data+= "&amountloans="+$("#amountloans").val();
                }
                // console.log(form_data);return;
                $("#dhide").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
                $("#dshow").hide();
                $.ajax({
                    url     :   "<?=site_url("payroll_/loadmodelfunc")?>",
                    type    :   "POST",
                    data    :   form_data,
                    success :   function(msg){
                        alert(msg);
                        $("#dhide").hide();
                        $("#dshow").show();
                        $("#myModal").modal('toggle'); 
                    }
                });
            }
        }
    });

    $("#schedule").change(function(){
        $.ajax({
            url: "<?=site_url('payroll_/loadquarterforsched')?>",
            type: "POST",
            data: {
              schedule  :   $(this).val(), 
              model     :   "quarter"
            },
            success: function(msg){
               $("select[name='period_drop']").html(msg).trigger("liszt:updated");
            }
        });
    });

    $("#dschedule").change(function(){
        $("#qshow").hide();
        $("#qload").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-5"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
        $.ajax({
            url: "<?=site_url('payroll_/loadquarterforsched')?>",
            type: "POST",
            data: {
              schedule  :   $(this).val(), 
              model     :   "quarter"
            },
            success: function(msg){
               $("#qload").hide();
               $("select[name='dperiod_drop']").html(msg).trigger("liszt:updated");
               $("#qshow").show();
            }
        });
    });

	// ///< added script for loan auto compute
	// $('#startingamount, #nocutoff').on('input',function(){
	//   var startingamount  = $('#startingamount').val();
	//   var nocutoff        = $('#nocutoff').val();

	//   if(startingamount == '' || nocutoff == '' || nocutoff == 0){
	// 	$('#amountloan, #famount').val('');
	// 	return false;
	//   }
	//   var amt = startingamount / nocutoff;
	//   var amount = Math.floor(amt);
	//   var famount = (startingamount - (amount * nocutoff)) + amount;

	//   $('#amountloan').val(amount);
	//   $('#famount').val(famount);
	// });
    ///< added script for loan auto compute
    $('#currentamount, #nocutoff').on('input',function(){
      var currentamount  = $('#currentamount').val();
      var nocutoff        = $('#nocutoff').val();
      var amountloan  = $('#amountloans').val();
      // if((startingamount != '' && amountloan !='')){
      //   $("#nocutoff").val(startingamount/amountloan).toFixed(2);
      //   return false;
      // }
      // {
        if ($("#basedon").val() == 0) {
          if (currentamount != "" && nocutoff != "")
           {
          var amt = currentamount / nocutoff;
          var amount = Math.floor(amt);
           var famount = (((currentamount - (amount * nocutoff))/nocutoff) + amount);
            $('#amountloans').val(famount);
              $("#amountloans").css('color','black');
            }

          // $('#famount').val(famount);
         }
      // };
    });


    $('#amountloans, #currentamount').on('input',function(){
      var currentamount  = $('#currentamount').val();  
      var amountloan  = $('#amountloans').val();
      var nocutoff        = $('#nocutoff').val();
      if ($("#basedon").val() == 1) {
        // alert(currentamount);
        // alert(amountloan);
        // alert(nocutoff);
            if((currentamount != '' &&  amountloan !='')){
             var cutoffresult = Math.round(currentamount/amountloan);
             var famount = (currentamount - (amountloan * (cutoffresult - 1)))
             $("#nocutoff").val(cutoffresult);
             if (famount <= 0) {
                $("#famount").val(0);
             }
             else
             {
                $("#famount").val(famount);
             }
            return false;
            }
            else
            {
              $("#nocutoff").val('');  
            }
      }
      // else
      // {
      // var amt = (amountloan * nocutoff).toFixed(2);
      // $('#currentamount').val(amt);
      // // var amount = Math.floor(amt);
      // //  var famount = (((amountloan - (amount * nocutoff))/nocutoff) + amount).toFixed(2);
      // }
    });

    $("#basedon").unbind('change').on('change',function()
    {
        var basedon = $(this).val();
        if (basedon == "1") {
            $("#currentamount").val('');
            $("#nocutoff").val('');
            $("#amountloans").val('');
            $("#currentamount").prop('disabled',false);
            $("#currentamount").css('background-color','white');
            $("#nocutoff").css('background-color','#E8E8E8');
            $("#nocutoff").prop('disabled',true);
            $("#nocutoff").css('color','black');
            $("#amountloans").prop('disabled',false);
            $("#amountloans").css('background-color','white');
            $("#amountloans").css('color','black');
            $('#famounts').show();
        }
        else if (basedon == "0") 
        {
            $('#famounts').hide();
            $('#famounts').val('');
            $("#currentamount").val('');
            $("#nocutoff").val('');
            $("#amountloans").val('');
            $("#currentamount").prop('disabled',false);
            $("#currentamount").css('background-color','white');
            $("#amountloans").css('background-color','#E8E8E8');
            $("#amountloans").prop('disabled',true);
            $("#amountloans").css('color','white');
            $("#nocutoff").css('color','black');
            $("#nocutoff").prop('disabled',false);
            $("#nocutoff").css('background-color','white');

        }
        else
        {
            $("#currentamount").val('');
            $("#nocutoff").val('');
            $("#amountloans").val('');
            $("#currentamount").css('background-color','#E8E8E8');
            $("#amountloans").css('background-color','#E8E8E8');
            $("#nocutoff").css('background-color','#E8E8E8');
            $("#currentamount").prop('disabled',true);
            $("#amountloans").prop('disabled',true);
            $("#nocutoff").prop('disabled',true);

        }
    });

    $(".chosen").chosen();
    $('#datefrom,#dateto,#ddatefrom').datepicker({
        autoclose:true,
        todayBtn : true

    });
</script>







                