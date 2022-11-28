<?php
$datetoday = date('Y-m-d');
?>
<input type="hidden" name="code_deduction" value="<?=$code_deduction?>">
<!-- <div class="well-blue">
    <div class="well-header">
        <h5>Employee List</h5>
    </div>
    <div class="well-content">
        <span class="pull-right">
            <span id="errorMsg"></span>&nbsp;&nbsp;&nbsp;
            <a href="#" class="btn blue" id="save_payment"><b>SAVE</b></a>
        </span><br><br>
        <table class="table table-striped table-bordered table-hover datatable">
            <thead>
                <tr>
                    <th class="align_center">OR NUMBER</th>
                    <th class="align_center">DATE PAID</th>
                </tr>
            </thead>
            <tbody id="employeelist">
                <td class="align_center">
                    <input type="text" name="or_number" class="or_number" id="or_number" value="" onkeydown="nextInput(this,event)">
                </td>
                <td class="align_center">
                    <div class="input-append date datepaid" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="datepaid" id="datepaid" type="text" value="" readonly>
                        <span class="add-on">&nbsp;<i class="icon-calendar"></i>&nbsp;</span>
                    </div>
                </td>
            </tbody>
        </table>
    </div>
</div> -->
        <div id="encode_body" class="panel"   >
            <div class="panel-heading " style="background-color: #0072c6;"  id=""><h4><b>Reglementary</b></h4></div>
            <div class="panel-body" id="">
                <div class="col-md-6">
                    
                    <div class="col-md-12" id="">
                        <label class="field_name col-md-3">Reglementary :</label>
                        <div class="field col-md-9">
                            <div class="span12">
                                <select class="form-control span6" id="reglamentory" name="reglamentory">
                                    <option value=''> -All Reglementary- </option>
                                    <?foreach ($reglamentory as $code) {?>
                                    <option value='<?=strtolower($code)?>' <?= (strtolower($code) == $code_deduction) ? "selected" : "" ?> ><?=$code?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                    </div> 
                    <br><br> 
                    <div class="form-row">
                        <div class="col-md-12" id="edept">
                            <label class="field_name col-md-3">Cut-Off :</label>
                            <div class="field col-md-9">
                                <div class="span12 no-search">
                                    <select class="form-control span6" name="cutoff" id="cutoff">
                                        <option value="">- Select Cut-Off -</option>
                                        <?=$cutoff?>
                                    </select>
                                </div>
                            </div>
                        </div>    
                    </div>  
                </div>
            </div> 
        </div>
        <div id="wrapListEncode" style="position: static;">
        </div>
            <a id="showsetup" href="#" data-toggle="modal" data-target="#encode_processs" hidden="" ></a>
        <div class="modal fade" id="encode_processs" data-backdrop="static">
        </div>
        <div class="panel animated fadeIn delay-1s reglamentoryPanel" <?=($showlist) ? '' : ' style="display: none;"' ?> >
        <div class="panel-heading"><h4><b>Reglementary Payment</b></h4></div>
            <div class="panel-body emplist">
                <div class="form_row">
                    <div class="align_right" id="div_loading" style="color: red" hidden>
                        <img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.
                    </div>
                </div>
                <?foreach ($reglamentory as $code):?>
                    <div class="form_row col-md-12" id='reglamentoryTable' style="margin-top: 20px;">
                        <div id="<?=strtolower($code)?>div">
                            <div class="form-group col-sm-5">
                                <div class="field_name col-sm-6 align_right">
                                    <label class="align_right"><?=$code?> OR&nbsp;NUMBER</label>
                                </div>
                                <div class="field col-sm-6" style="margin-left: 0px;">
                                    <input type="text" name="or_number" class="form form-control or_number" id="or_number" value="" onkeydown="nextInput(this,event)">
                                </div>
                            </div>
                            <div class="form-group col-sm-5">
                                <div class="field_name col-sm-4 align_right">
                                    <label class="align_right">DATE&nbsp;PAID</label>
                                </div>
                                <div class='input-group col-sm-8 date' style="margin-left: 0px;">
                                  <input class="form-control" type="text" name="datepaid" id="datepaid" value="" >
                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                            </div>
                            <div class="form-group col-sm-2">
                                <span id="errorMsg"></span>&nbsp;&nbsp;&nbsp;
                                    <a href="#" class="btn btn-primary save_payment" code="<?=strtolower($code)?>" id="save_payment"><b>SAVE</b></a>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
                <div class="form_row" id='reglamentoryTable' hidden>
                    <table class="table table-bordered table-hover table-responsive" id="reglamentory_table" width="100%">
                        <thead style="background-color: #0072c6">
                            <tr valign="center">
                                <th class="align_center">OR NUMBER</th>
                                <th class="align_center">DATE PAID</th>
                            </tr>
                        </thead>
                        <tbody id="employeelist">
                            <td class="align_center">
                                <input type="text" name="or_number" class="form form-control or_number col-md-10" id="or_number" value="" onkeydown="nextInput(this,event)">
                            </td>
                            <td class="align_center">
                                <!-- <div class="input-group date datepaid" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="datepaid" id="datepaid" type="text" value="" readonly>
                                    <span class="add-on">&nbsp;<i class="icon-calendar"></i>&nbsp;</span>
                                </div>
                                <div class='input-group date col-sm-9' style="padding-right: 20px;">
                                    <input class="form-control" name="dateemployed" type="text" style="margin-left: 19px; padding-right: 20px;"/><span class="add-on">&nbsp;<i class="icon-calendar"></i>&nbsp;</span>
                                </div>  -->
                                <div class='input-group date'>
                                      <input class="form-control col-md-10" type="text" name="datepaid" id="datepaid" value="" >
                                      <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                </div>
                            </td>
                        </tbody>
                    </table>
                    <br>
                </div>
            </div>
        </div>
        <div id="reglamentory_modal" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
            <div class="modal-header">
                <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
            </div>
            <div class="modal-body">
                <div class="row-fluid">
                    <div class="row-fluid span12" tag='display'></div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn grey">Close</a>
            </div>
        </div>
<?
    function formatAmount($amount=''){
        if($amount){
            $amount = number_format( $amount, 2 );
        }else{
            $amount = '0.00';
        }
        return $amount;
    }

?>
<script>

    $("#reglamentory, #cutoff").change(function(){
        $("select[name='reglamentory']").val($("#reglamentory").val());
        $("select[name='cutoff']").val($("#cutoff").val());
        if($("#cutoff").val()) loadBatchEncodeEmployee();
    });

    /*$("select[name='cutoff']").on('change', function () {
        $("select[name='cutoff']").val()
        $('.reglamentoryPanel').show();
      
    });*/
    $("input[name=or_number], input[name=datepaid]").on('change',function(){
        var tr_ = $(this).closest('tr');
        var to_update = validateChanges(tr_);

        changeStatusTag(to_update,tr_);
    });

    $('.save_payment').on('click',function(){
        var code = $(this).attr("code");
        code = code+"div";
        var form_data = { 
            list            : <?php echo json_encode($list); ?>,
            cutoff          : $("#"+code).find('#cutoff').val(),
            datepaid        : $("#"+code).find("#datepaid").val(),
            or_number       : $("#"+code).find(".or_number").val(),
            reglamentory    : $(this).attr("code"),
            code_deduction  : $(this).attr("code"),
        }
        // console.log(form_data);
        // return;
        $('#errorMsg').html("<img src='<?=base_url()?>images/loading.gif'/> Saving.. Please wait.");

        $.ajax({
            url : "<?=site_url('payroll_/savePaymentReglamentoryBatch')?>",
            type: "POST",
            dataType: 'json',
            data: form_data,
            success: function(msg){
                console.log(msg);
                var data_failed = msg.data_failed;
                var failed = '';
                for (var key in data_failed) {
                    failed += data_failed[key] + ", ";
                }
                if(failed) failed = failed.substring(0, failed.length-2);
                else failed = 'NONE';

                if(msg.err_code == 0){
                  
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg.msg+'\n'+'Success count: '+msg.success_count+'\n'+'Data insert failed: '+failed,
                        showConfirmButton: true,
                        timer: 2000
                    });  
                }else{
                  Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg+'\n'+'Success count: '+msg.success_count+'\n'+'Data insert failed: '+failed,
                        showConfirmButton: true,
                        timer: 2000
                    });
                }

                $('#errorMsg').html('');
            }
        });
        
    });

    $(".date").datetimepicker({
        format: 'YYYY-MM-DD'
    });


    ///< @Angelica - validations - save only those that are changed
    
    function validateChanges(tr_){
        var to_update = false;

        ///< or_number
        var or_input = $(tr_).find('input[name=or_number]');
        if($(or_input).attr('oldvalue') != $(or_input).val())   to_update = true;
        else                                                    to_update = false;

        if(to_update) return to_update;

        ///< datepaid
        var datepaid = $(tr_).find('input[name=datepaid]');
        if($(datepaid).attr('oldvalue') != $(datepaid).val())   to_update = true;
        else                                                    to_update = false;

        return to_update;
    }

    function changeStatusTag(to_update,tr_){
        if(to_update)   updateStatusTag(tr_);
        else            removeStatusTag(tr_);
    }

    function updateStatusTag(tr_){
        $(tr_).attr('status-tag','NOTSAVED');
        $(tr_).find('.status-tag').html('NOT SAVED').css('color','red');
    }

    function removeStatusTag(tr_){
        $(tr_).attr('status-tag','');
        $(tr_).find('.status-tag').html('');
    }



    function nextInput(input,event){
        var x = event.keyCode;
        if (x == 13) {  // 13 is the ENTER key
            var tr_ = $(input).closest('tr');
            var next_ = $(tr_).next('tr');
            $(next_).find('input[name=or_number]').focus();
        }
    }
</script>