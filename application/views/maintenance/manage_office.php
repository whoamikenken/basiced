<?php

    $cdisable = false;
    $description = "";
    $division = "";
    $officehead = "";
    $clusterhead = "";
    $department_id = "";
    $isBED = "";
    if($code){
        $cdisable = true;
        $description = $records['0']['description'];
        $department_id = $records['0']['department_id'];
        $division = $records['0']['managementid'];
        $officehead = $records['0']['head'];
        $clusterhead = $records['0']['divisionhead'];
        $isBED = $records['0']['isBED'];
    }
    $department = $this->extras->getOfficeDescription();
?>

<style>
    .cbox{
        -ms-transform: scale(1.5); /* IE */
        -moz-transform: scale(1.5); /* FF */
        -webkit-transform: scale(1.5); /* Safari and Chrome */
        -o-transform: scale(1.5); /* Opera */
    }
</style>

<div class="container" style="width: 100%;">
    <form id="form_Office">
        <div class="form-group">
            <label class="field_name align_right">Code</label>
            <div class="field">
                <input class="form-control" id="mh_code" name="mh_code" type="text" value="<?=$code?>"<?=($cdisable?" readonly":"")?>/>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Description</label>
            <div class="field">
                <input class="form-control" id="mh_description" name="mh_description" type="text" value="<?=$description?>"/>
            </div>
        </div>
        <div class="form-group">
          <label class="field_name align_right">Department</label>
          <div class="field">
              <select class="chosen form-control" name="mh_department" id="mh_department" style="width: 300px;" >
                <option value="">Select Department</option>
              <?
                foreach($department as $key => $val){
                ?><option <?=($key==$department_id ? " selected" : "")?> value="<?=$key?>"><?=$val?></option><?    
                }
              ?>
              </select>
          </div>
        </div>
        <div class="form-group" style="display: none;">
          <label class="field_name align_right">Division Level</label>
          <div class="field">
              <select class="chosen form-control" name="mh_division" id="mh_division" style="width: 300px;" >
              <?
                $opt_type = $this->extras->showManagement();
                foreach($opt_type as $c=>$val){
                ?><option <?=($c==$division ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                }
              ?>
              </select>
          </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Area coordinator / Immediate Supervisor</label>
            <div class="field">
                <select class="chosen form-control" id="mh_div" name="mh_div" style="width: 300px;" >
                    <option value="<?= $clusterhead ?>"><?= $this->extensions->getEmployeeName($clusterhead) ?></option>
                    <?=$this->employee->loadallempid($clusterhead)?>     
                    </select>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Department Head / Vice Principal</label>
            <div class="field">
                <select class="chosen form-control" id="mh_office" name="mh_office" style="width: 300px;" >
                <option value="<?= $officehead?>"><?= $this->extensions->getEmployeeName($officehead) ?></option>
                <?=$this->employee->loadallempid($officehead)?>
                </select>
            </div>
        </div>
        <div class="form-group">
            &nbsp;<input type="checkbox" name="isBED" id="isBED" class="cbox" value="1" <?= (isset($isBED) && $isBED) ? " checked" : "" ?> >
            <label>&nbsp;&nbsp;Tag as BED:</label>
        </div>
        <input type="text" id="job" value="<?=$job?>" style="display: none;">
    </form>
</div>

<script>
    var toks = hex_sha512(" ");
    $('.chosen').chosen();
    $("#button_save_modal").unbind("click").click(function(){
        var isValid = true;
        var isBED = 0;
        if($("#isBED").prop("checked") == true) isBED = 1;
        else isBED = 0;

        $('#form_Office').each(function() {
            if ( $(this).val() === '' && $(this).attr("name")) isValid = false;
        });
        if(isValid){   
            $.ajax({
                url:"<?=site_url("maintenance_/save_office")?>",
                type:"POST",
                data:{
                    toks: toks,
                    code: GibberishAES.enc($("input[name='mh_code']").val(), toks),
                    description: GibberishAES.enc($("input[name='mh_description']").val(), toks),
                    department_id: GibberishAES.enc($("#mh_department").val(), toks),
                    division: GibberishAES.enc($("#mh_division").val(), toks),
                    head: GibberishAES.enc($("#mh_office").val(), toks),
                    divhead: GibberishAES.enc($("#mh_div").val(), toks),
                    job: GibberishAES.enc($("#job").val(), toks),
                    isBED: GibberishAES.enc(isBED, toks),
                    last_dept :  GibberishAES.enc("<?php echo $department_id ?>", toks)
                },
                success: function(msg){
                    // console.log($("#mh_department").val());
                    var notif = "";
                    if(msg == "add"){
                        notif = "added";
                    }else{
                        notif = "updated";
                    }

                    if (msg == "duplicate") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Office code is already existing.',
                            showConfirmButton: true,
                            timer: 200000
                        })
                    }else{
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Office has been '+notif+' successfully.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                        office_setup(); 
                        $('#modalclose').click();
                    }
                }
             });
        }else{
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please fill-up all required fields!",
                showConfirmButton: true,
                timer: 1000
            })
            return false;
        }
    });

</script>