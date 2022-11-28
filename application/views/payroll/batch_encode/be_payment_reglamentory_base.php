<input type="hidden" name="deptid" value="<?=$deptid?>">
<input type="hidden" name="employmentstat" value="<?=$employmentstat?>">
<input type="hidden" name="cutoff" value="<?=$cutoff?>">
<input type="hidden" name="reglamentory" value="<?=$reglamentory?>">

<div id="encode_body" class="panel"   >
    <div class="panel-heading income_categ" style="background-color: #0072c6;"  id="income_categ"><h4><b>Reglamentory</b></h4></div>
    <div class="panel-body" id="">
        <div class="col-md-6">
            
            <div class="col-md-12" id="">
                <label class="field_name col-md-3">Reglamentory :</label>
                <div class="field col-md-9">
                    <div class="span12">
                        <select class="form-control span6" id="reglamentory" name="reglamentory">
                            <option value=''> -All Reglamentory- </option>
                            <?foreach ($reglamentory as $code) {?>
                            <option value='<?=strtolower($code)?>'><?=$code?></option>
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
                            <select class="form-control span6" name="schedule" id="schedule">
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

<script>

    $("#reglamentory, #schedule").change(function(){
        $("select[name='reglamentory']").val($(this).val());
        loadBatchEncodeEmployee();
    });


</script>