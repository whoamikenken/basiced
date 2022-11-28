<?php
    $CI =& get_instance();
    $CI->load->model('utils');
    $divisions      = $CI->utils->getManagementLevels('Select division level...');
    $departments    = $CI->utils->getOffice('Select all office');
    $datetoday = date("Y-m-d");
    $CI->load->model('disciplinary_action');
    $offense_types = $CI->disciplinary_action->getOffensesTypes();
?>
<form id="disciplinaryActionReport">
    <div>
<!--         <div class="form_row">
            <label class="field_name align_right">Division Level</label>
            <div class="field">
                <div class="col-md-11">
                    <select class="chosen col-md-12" id="division" name="division">
                    <?
                        
                        foreach ($divisions as $code => $desc) {?>
                             <option value="<?=$code?>"><?=$desc?></option>
                        <?}
                    ?>
                    </select>
                </div>
            </div>
        </div> -->
        <div class="form_row">
            <label class="field_name align_right">Office</label>
            <div class="field">
                <div class="col-md-11">
                    <select class="chosen col-md-12" id="department" name="department">
                    <?
                        foreach ($departments as $code => $desc) {?>
                            <option value="<?=$code?>"><?=$desc?></option>
                        <?}
                    ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form_row">
            <label class="field_name align_right">Type of Offenses</label>
            <div class="field">
                <div class="col-md-11">
                    <select class="chosen col-md-12" id="offenseType" name="offenseType">
                        <option value="">Select all Type of Offenses</option>
                    <?
                        foreach ($offense_types as $row) {?>
                            <option value="<?=$row->code?>"><?=$row->description?></option>
                        <?}
                    ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form_row">
            <label class="field_name align_right">Date From</label>
                <div class="field">
                    <div class="col-md-12"style="padding-left: 0px;">
                        <div class="col-md-5">
                            <div class='input-group date' id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control" size="16" name="dfrom" id="dfrom" type="text" value="<?=$datetoday?>"/>
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <span class="col-md-1"><b>To&nbsp;</b></span>
                        <div class="col-md-5" style="padding-right: 0px;">
                            <div class='input-group date' id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control" size="16" name="dto" id="dto" type="text" value="<?=$datetoday?>"/>
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="form_row">
            <label class="field_name align_right">Status</label>
            <div class="field">
                <div class="col-md-11">
                    <select class="chosen col-md-12" id="status" name="status">
                        <?
                            $array = array(""=>"All Status","YES"=>"Confirmed","NO"=>"Unconfirmed");
                            foreach ($array as $k => $v) {?>
                                <option value="<?=$k?>"><?=$v?></option>
                            <?}
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
var toks = hex_sha512(" ");
   $("#button_save_modal").unbind('click').click(function(){
       var form_data   =   "form="+ GibberishAES.enc("disciplinary_action_list", toks);
       $('#disciplinaryActionReport input, #disciplinaryActionReport select, #disciplinaryActionReport textarea').each(function(){
            form_data += '&'+$(this).attr('name')+'='+ GibberishAES.enc($(this).val() , toks);
        })
       form_data   +=  "&toks="+toks;
       var encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
   });
   
    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });
    $(".chosen").chosen();
</script>