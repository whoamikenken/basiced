<?php
$CI =& get_instance();
$CI->load->model('utils');
$divisions      = $CI->utils->getManagementLevels('Select division level...');
$departments    = $CI->utils->getDepartments('All Department');
$offices    = $CI->utils->getOffice('All Office');
$username = $this->session->userdata('username');
$utype = $this->session->userdata('usertype');
$office = $this->extensions->getAllOfficeUnder($this->session->userdata("username"));

$depthead = $this->employee->checkifDefDepartmentHead($username);
$ishidden = '';
$deptid = '';
if($depthead > 0){
    $ishidden = 'hidden';
    // $deptid = $this->extras->getHeadOffice($username);
} 
?>
<input type="hidden" id="depthead" value="<?= $depthead ?>">
<form id="deficiencyReport">
    <div>
        <!-- <div class="form_row" <?= $ishidden ?>>
            <label class="field_name align_right">Division Level</label>
            <div class="field">
                <div class="col-md-12">
                <select class="chosen" id="division" name="division">
                    <?
                        
                        foreach ($divisions as $code => $desc) {?>
                             <option value="<?=$code?>"><?=$desc?></option>
                        <?}
                    ?>
                </select>
                </div>
            </div>
        </div> -->
        <div class="form_row" hidden>
            <label class="field_name align_right">Department</label>
            <div class="field">
                <!-- <div class="col-md-12"  <?= ($deptid != '') ? 'style="pointer-events:none;"' : ''; ?>> -->
                <div class="col-md-12">
                <select class="chosen" id="department" name="department">
                    <?
                        foreach ($departments as $code => $desc) {?>
                            <option value="<?=$code?>" <?= ($deptid==$code) ? 'selected' : '' ?> ><?=$desc?></option>
                        <?}
                    ?>
                </select>
                </div>
            </div>
        </div>
        <div class="form_row">
            <label class="field_name align_right">Office</label>
            <div class="field">
                <!-- <div class="col-md-12"  <?= ($deptid != '') ? 'style="pointer-events:none;"' : ''; ?>> -->
                <div class="col-md-12">
                <select class="chosen" id="office" name="office" multiple>
                    <?
                        foreach ($offices as $code => $desc) {
                            if(in_array($code, $office)){
                            ?>
                            <option value="<?=$code?>" ><?=$desc?></option>
                        <?  }
                        }
                    ?>
                </select>
                </div>
            </div>
        </div>
        <div class="form_row" <?= $ishidden ?>>
            <label class="field_name align_right">Concerned Department</label>
            <div class="field">
                <div class="col-md-12">
                <select class="chosen" id="cdepartment" name="cdepartment">
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
            <label class="field_name align_right">Status</label>
            <div class="field">
                <div class="col-md-12">
                <input name="statusComplete" value="Completed" type="checkbox" style="-ms-transform: scale(1.5);-moz-transform: scale(1.5);-webkit-transform: scale(1.5); -o-transform: scale(1.5);margin:10px" checked>Completed
                &nbsp
                <input name="statusIncomplete" value="Incomplete" type="checkbox" style="-ms-transform: scale(1.5);-moz-transform: scale(1.5);-webkit-transform: scale(1.5); -o-transform: scale(1.5);margin:10px" checked>Incomplete
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var toks = hex_sha512(" ");
   $("#button_save_modal").unbind('click').click(function(){
    if ($("input[name='statusComplete']").is(":checked") || $("input[name='statusIncomplete']").is(":checked")) {
        var form_data   =   "form=" + GibberishAES.enc("deficiency_list", toks);
       form_data  += "&depthead="+GibberishAES.enc($("#depthead").val() , toks);
       $('#deficiencyReport input, #deficiencyReport select, #deficiencyReport textarea').each(function(){
        if ($(this).attr('name') == "statusComplete" || $(this).attr('name') == "statusIncomplete") {
            if ($(this).is(":checked")) form_data += '&'+$(this).attr('name')+'='+ GibberishAES.enc( $(this).val(), toks); 
            else form_data += '&'+$(this).attr('name')+'='+ GibberishAES.enc("", toks); 
        }else{
            form_data += '&'+$(this).attr('name')+'='+ GibberishAES.enc( $(this).val(), toks); 
        }
        })
       form_data  += "&toks="+toks;
       var encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata:encodedData})
    }else{
        Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please check at least one status.',
                showConfirmButton: true,
                timer: 1000
          })
    }
       
   });

$(".chosen").chosen();
</script>