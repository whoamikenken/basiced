<style>
    .rank{
        pointer-events: none;
    }
</style>

<div id="removeAni" class="panel animated fadeIn delay-1s">
    <div class="panel-heading"><h4><b>Employee List</b></h4></div>
    <div class="panel-body emplist">
        <table class="table table-hover table-bordered datatable" id="be_salary">
            <thead style="background-color: #0072c6">
                <tr>
                    <th class="sorting_asc">Employee</th>
                    <th class="align_center" style="width: 160px">Fullname</th>
                    <th class="align_center" width="10px" style="font-size: 10px;" hidden>Monthly Rate</th>
                    <th class="align_center">Type</th>
                    <th class="align_center" hidden>Rank</th>
                    <th class="align_center">Monthly</th>
                    <th class="align_center">Semi Monthly</th>
                    <th class="align_center">Daily</th>
                    <th class="align_center">Hourly</th>
                    <th class="align_center">Per minute</th>
                    <th class="align_center" hidden>LEC/LAB Rate (Hourly)</th>
                    <th class="align_center">Schedule</th>
                    <th class="align_center">Tax Status</th>
                </tr>
            </thead>
            <tbody id="employeelist">
                <?php 
                    if(isset($list) && sizeof($list) > 0){
                        foreach ($list as $employeeid => $detail) { 
                            $schedule = isset($detail['schedule']) ? $detail['schedule'] : '';
                            $tax_status = isset($detail['tax_status']) ? $detail['tax_status'] : '';
                    ?>
                            <tr class="data-list" employeeid="<?=$employeeid?>" teachingtype="<?=$detail['teachingtype']?>" iscollege="<?=$detail['iscollege']?>">
                                <td><?=$employeeid?></td>
                                <td style="white-space: nowrap;"><?=$detail['fullname']?></td>
                                <td class="align_center" hidden> <input type="checkbox" name="isFixed" class="double-sized-cb" <?=isset($detail['fixedday']) ? ($detail['fixedday'] ? 'checked' : '') :''?> oldvalue="<?=isset($detail['fixedday']) ? $detail['fixedday']:''?>"></td>
                                <td class="align_center" hidden>
                                    <select class="form-control type" name="type">
                                        <option value="">- Select Type -</option>
                                        <?php foreach($type_config as $value): ?>
                                            <option value="<?= $value['id'] ?>" <?= ($detail['type'] == $value['id']) ? "selected" : "" ?> ><?= $value['description'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>
                                <td class="align_center">
                                    <input type="hidden" name="existing_rank" class="existing_rank" value="<?=isset($detail['rank']) ? $detail['rank'] : ''?>">
                                    <select class="form-control rank" name="rank" readonly>
                                        <option value="">- Select Rank -</option>
                                    </select>
                                </td>
                                <td class="align_center"> <input type="text"  name="monthly"     class="form-control salary-input monthly"  value="<?=isset($detail['monthly']) ? number_format($detail['monthly'],2) : number_format(0, 2)?>"  
                                                                                                                    oldvalue="<?=isset($detail['monthly']) ? number_format($detail['monthly'],2) : number_format(0, 2)?>"   onkeypress="return numbersonly(this)"/> </td>
                                <td class="align_center"> <input type="text"  name="semimonthly" class="form-control salary-input semimonthly"  value="<?=isset($detail['semimonthly']) ? number_format($detail['semimonthly'],2) : number_format(0, 2)?>" onkeypress="return numbersonly(this)"/> </td>
                                <td class="align_center"> <input type="text"  name="daily"       class="form-control salary-input daily"  value="<?=isset($detail['daily']) ? number_format($detail['daily'],2) : number_format(0, 2)?>"       onkeypress="return numbersonly(this)"/> </td>
                                <td class="align_center"> <input type="text"  name="hourly"      class="form-control salary-input hourly"  value="<?=isset($detail['hourly']) ? number_format($detail['hourly'],2) : number_format(0, 2)?>"      onkeypress="return numbersonly(this)"/> </td>
                                <td class="align_center"> <input type="text"  name="minutely"    class="form-control salary-input minutely"  value="<?=isset($detail['minutely']) ? number_format($detail['minutely'],2) : number_format(0, 2)?>"    onkeypress="return numbersonly(this)"/> </td>
                                <td class="align_center" hidden>
                                    <div class="wrap_leclabpay">

                                        <?php if(sizeof($detail['perdept_arr']) > 0){

                                            foreach ($detail['perdept_arr'] as $key => $row){ ?>
                                                <input type="hidden" class="old-value" value="<?=$row['lechour'].'~u~'.$row['labhour'].'~u~'.$row['aimsdept']?>"> <!-- OLD VALUES -->
                                                
                                                <div class="leclab-pay">

                                                    Lec : <input name="lechour" class="form-control lechour" type="text" style="width: 15%;" value="<?=$row['lechour']?>" onkeypress="return numbersonly(this)"/>
                                                    Lab : <input name="labhour" class="form-control labhour" type="text" style="width: 15%;" value="<?=$row['labhour']?>" onkeypress="return numbersonly(this)"/>


                                                    <select name="aimsdept" style="width: 30%;" class="form-control aimsdept">
                                                        <option value="" <?=$row['aimsdept']==''?' selected':''?> >Choose Aims department..</option>
                                                        <? foreach ($aimsdept_arr as $key => $desc) { ?>
                                                              <option value="<?=$key?>" <?=$row['aimsdept']==$key?' selected':''?> ><?=$desc?></option>
                                                        <? } ?>
                                                    </select>

                                                    <div class="btn-group">
                                                        <a class="btn btn-primary add_leclabpay"><i class="icon-plus-sign"></i></a>
                                                        <a class="btn btn-danger del_leclabpay"><i class="icon-trash"></i></a>
                                                    </div>
                                                </div>

                                            <? }
                                        }else{ ?>

                                                <div class="leclab-pay">
                                                    Lec : <input name="lechour" class="form-control lechour" type="text" style="width: 15%;" value="0" onkeypress="return numbersonly(this)"/>
                                                    Lab : <input name="labhour" class="form-control labhour" type="text" style="width: 15%;" value="0" onkeypress="return numbersonly(this)"/>


                                                    <select name="aimsdept" type="text" style="width: 30%;" class="form-control aimsdept">
                                                        <option value="" selected="">Choose Aims department..</option>
                                                        <? foreach ($aimsdept_arr as $key => $desc) { ?>
                                                              <option value="<?=$key?>"><?=$desc?></option>
                                                        <? } ?>
                                                    </select>

                                                    <div class="btn-group">
                                                        <a class="btn btn-primary add_leclabpay"><i class="icon-plus-sign"></i></a>
                                                        <a class="btn btn-danger del_leclabpay"><i class="icon-trash"></i></a>
                                                    </div>
                                                </div>
                                        <? } ?>

                                    </div><!--end LEC / LAB HOUR PER DEPT -->
                                </td>
                              
                                <td class="align_center"><select class="form-control schedule" name="schedule" oldvalue="<?=$schedule?>"><?=$this->payrolloptions->payschedule($schedule);?></select></td>
                                <td class="align_center"><select class="form-control tax_status" name="tax_status" oldvalue="<?=$tax_status?>" readonly style="pointer-events: none"><?=$this->payrolloptions->taxdependents($tax_status);?></select></td>                          
                            </tr>
                    <? } 

                    }?>                           
            </tbody>
        </table>
    </div>
</div>

<div id="be_modal" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
    <div class="modal-header">
        <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="row-fluid span12" tag='display'></div>
        </div></div>
    <div class="modal-footer">
        <a href="#" data-dismiss="modal" aria-hidden="true" class="btn grey">Close</a>
    </div>
</div>

<script type="text/javascript">
    setTimeout(function(){ 
        $("#removeAni").removeClass("animated fadeIn delay-1s");
    }, 2000);
</script>
<script src="<?=base_url()?>js/batch_encode/be_salary.js"></script>