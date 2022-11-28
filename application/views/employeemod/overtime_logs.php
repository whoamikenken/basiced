<?php
/**
 * @author Angelica
 * @copyright 2018
 */

$total = "";

if(isset($ot_logs)){
    foreach ($ot_logs as $date => $log_detail) {
        // echo '<pre>';var_dump($ot_logs);
        // $total += $log_detail['ottotal'];

        $total += $this->attcompute->exp_time($log_detail['otsubtotal']);

        ?>

                <div class="form_row " >
                    <label class="field_name align_right">Date</label>
                    <div class="field dates">
                            <div class="input-group date" id="" data-date="<?=$date?>" data-date-format="yyyy-mm-dd">
                                <input class="align_center" size="16" name="date" type="text" value="<?=$date?>" readonly  style="width: 85px;">
                                <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                            </div>
                            &nbsp;&nbsp;
                            Start
                            &nbsp;&nbsp;
                            <div class="input-group bootstrap-timepicker">
                                <input class="input-small align_center" type="text" name="otstart" id="otstart" value="<?=$log_detail['otstart']?>" readonly=""  style="width: 68px;" />
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                            &nbsp;&nbsp;
                            End
                            &nbsp;&nbsp;
                            <div class="input-group bootstrap-timepicker">
                                <input class="input-small align_center" type="text" name="otend" id="otend" value="<?=$log_detail['otend']?>" readonly=""  style="width: 68px;" />
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                            &nbsp;&nbsp;
                            Total Hr./Min.
                            &nbsp;&nbsp;
                            <input type="text" class="align_center" id="totalot" name="totalot" value="<?=$log_detail['otsubtotal']?>" readonly=""  style="width: 50px;"/>
                            &nbsp;&nbsp;
                            Credited Hr./Min.
                            &nbsp;&nbsp;
                            <input type="text" class="align_center" id="creditedOT" name="creditedOT" value="<?=$log_detail['creditedOT']?>" readonly=""  style="width: 50px;"/>
                    </div>
                </div>




    <?} //end for
}

$tmp = $total;
$tmp_tot = $tmp - ($tmp % 1800);
if($tmp_tot < 3600) $tmp_tot = 0;
$total = $this->attcompute->sec_to_hm($tmp_tot);

?>

<input type="hidden" name="total_computed" value="<?=$total?>"> 