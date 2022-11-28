<!-- by Riel 2020 -->
<style type="text/css">
    .form_row{
        padding-bottom: 10px;
    }
    #educForm{
        margin-top: 10px;
    }
    .modal-overflow .modal-body {
        margin-bottom: 0px;
    }
    .error {
        color: red;
    } 
</style>
<?php
$syy[1] = $syy[0] = '';
    if(isset($sydata)){
        $sy = $sydata[0]->sy;
        $syy = explode('-', $sy);
        $month_from = $sydata[0]->month_from;
        $month_to = $sydata[0]->month_to;
        $id = $sydata[0]->id;
    }else{
        $sy = $month_from = $month_to = $id = '';
    }
    unset($sydata);
?>
<form id="educForm">
    <input type="hidden" name="id" value="<?= $id ?>">
    <!-- <div class="form_row" hidden>
        <div class="col-md-11">
            <div class="col-md-4">
                <label class="field_name align_right" style="width: 90%;">School Year</label>
            </div>
            <div class="col-md-7">
                <select class="chosen" id="sy" name="sy">
                    <?= Globals::selectSchoolYear($sy, $existing) ?>
                </select>
            </div>
        </div>
    </div> -->
    <div class="form_row">
        <div class="col-md-11">
            <div class="col-md-3">
                <label class="field_name align_right" style="width: 90%;">School Year From</label>
            </div>
            <div class="col-md-4">
                <input type="text" name="yr_from" class="form-control" value='<?=$syy[0]?>' oninput="return setNumberOnly(this)"/>
            </div>
            <div class="col-md-1">
                <label class="field_name align_right" style="width: 90%;">To</label>
            </div>
            <div class="col-md-4">
                <input type="text" name="yr_to" class="form-control" value='<?=$syy[1]?>' oninput="return setNumberOnly(this)"/>
            </div>
        </div>
    </div>
    <div class="form_row">
        <div class="col-md-11">
            <div class="col-md-3">
                <label class="field_name align_right" style="width: 90%;">Month From</label>
            </div>
            <div class="col-md-4">
                <select class="chosen" id="month_from" name="month_from">
                    <?php foreach(Globals::monthList() as $key => $value):  ?>
                        <option value="<?= $key ?>" <?= ($month_from == $key) ? 'selected' : ''; ?>><?= $value ?></option>
                    <?php endforeach;   ?>
                </select>
            </div>
            <div class="col-md-1">
                <label class="field_name align_right" style="width: 90%;">To</label>
            </div>
            <div class="col-md-4">
                <select class="chosen" id="month_to" name="month_to">
                    <?php foreach(Globals::monthList() as $key => $value):  ?>
                        <option value="<?= $key ?>" <?= ($month_to == $key) ? 'selected' : ''; ?>><?= $value ?></option>
                    <?php endforeach;   ?>
                </select>
            </div>
        </div>
    </div><!-- 
    <div class="form_row" hidden>
        <div class="col-md-11">
            <div class="col-md-4">
                <label class="field_name align_right" style="width: 90%;">From</label>
            </div>
            <div class="col-md-7">
                <select class="chosen" id="month_from" name="month_from">
                    <?php foreach(Globals::monthList() as $key => $value):  ?>
                        <option value="<?= $key ?>" <?= ($month_from == $key) ? 'selected' : ''; ?>><?= $value ?></option>
                    <?php endforeach;   ?>
                </select>
            </div>
        </div>
    </div>
    <div class="form_row" hidden>
        <div class="col-md-11">
            <div class="col-md-4">
                <label class="field_name align_right" style="width: 90%;">To</label>
            </div>
            <div class="col-md-7">
                <select class="chosen" id="month_to" name="month_to">
                    <?php foreach(Globals::monthList() as $key => $value):   ?>
                        <option value="<?= $key ?>" <?= ($month_to == $key) ? 'selected' : ''; ?>><?= $value ?></option>
                    <?php endforeach;   ?>
                </select>
            </div>
        </div>
    </div> -->
</form>
<script>
    $("#button_save_modal").unbind("click").click(function(){
        if($("input[name='yr_from']").val() == ''){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'School year from is required!',
                showConfirmButton: true,
                timer: 1000
            })
        }else if($("input[name='yr_to']").val() == ''){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'School year to is required!',
                showConfirmButton: true,
                timer: 1000
            })
        }else{
            var form_data = $("#educForm").serialize();
            $.ajax({
                url: "<?= site_url('reportsitem_/saveSchoolYear') ?>",
                data: form_data,
                type: "POST",
                success:function(res){
                    console.log(form_data);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: res,
                        showConfirmButton: true,
                        timer: 1000
                    })
                    $("#modalclose").click();
                    loadSchoolYearData();
                }
            })
        }
            
    });

    $('.chosen').chosen();
</script>