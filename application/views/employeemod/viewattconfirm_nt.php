<h2>Attendance Confirmed NONTEACHING</h2>
<p><?=$dateRange?></p>
<div id="attstbl" class="well_content">
    <table class="table table-striped table-bordered table-hover datatable" id="asctblnt">
        <thead >
              <tr style="background-color: #0072c6;">
                <th class="sorting_asc" rowspan="2" style="background-color: #0072c6;">Employee ID</th>
                <th rowspan="2" style="background-color: #0072c6;">Name</th>
                <th class="align_center" colspan="3" style="background-color: #0072c6;">Overtime (hr:min)</th>
                <th class="align_center" style="background-color: #0072c6;">Late/Undertime Deduction</th>
                <th class="align_center" rowspan="2" style="background-color: #0072c6;">Absent</th>                        
                <th class="align_center" colspan="3" style="background-color: #0072c6;">Leaves</th>
                <th class="align_center" rowspan="2" style="background-color: #0072c6;">No. of Days</th>
                <th class="align_center" rowspan="2" style="background-color: #0072c6;">Holiday</th>
            </tr>
            <tr >
                <th class="align_center" style="background-color: #0072c6;">Regular</th>
                <th class="align_center" style="background-color: #0072c6;">Rest Day</th>
                <th class="align_center" style="background-color: #0072c6;">Holiday</th>
                <th class="align_center" style="background-color: #0072c6;">Hr:min</th>            
                <th class="align_center" style="background-color: #0072c6;">VL</th>
                <th class="align_center" style="background-color: #0072c6;">SL</th>
                <th class="align_center" style="background-color: #0072c6;">Other</th>
            </tr>
        </thead>
        <tbody>

            <?php 
            $curDept = '';
            foreach($result as $key => $data):
                if($data['department'] != $curDept){
             ?>
                <tr>
                    <td colspan="16"><b> <?=$data['department']?></b></td>
                </tr>
            <?php } ?>
                <tr class="pdata">
                    <td class="align_center"><?=$key?></td>
                    <td class="align_center"><?=Globals::_e($data['fullname'])?></td>
                    <td class="align_center"><?=$data['otreg']?></th>
                    <td class="align_center"><?=$data['otrest']?></th>
                    <td class="align_center"><?=$data['othol']?></th>
                    <td class="align_center"><?=$data['lateutdeduc']?></th>
                    <td class="align_center"><?=($data['totDeduction']) ? $data['totDeduction'] : 0?></td>
                    <td class="align_center"><?=$data['vleave']?></td>
                    <td class="align_center"><?=$data['sleave']?></td>
                    <td class="align_center"><?=$data['oleave']?></td>
                    <td class="align_center"><?=$data['fixedday']?></td>
                    <td class="align_center"><?=$data['isholiday']?></td>
                </tr>
            <?php
            $curDept = $data['department'];
             endforeach ?>
        </tbody>
    </table>

</div>
<!--<input type="button" id="generate" class="btn btn-info align_right" value="Generate" style="cursor: pointer;margin: 0px 5px 5px 5px; float: right;" />
<?if($showfinalize && $this->session->userdata("usertype") == "ADMIN"){?>
    <div class="pull-right">
        <span id="cmsg" style="color: red;font-weight: bold;"></span>
        <input type="button" id="finalize" class="btn btn-primary" value="Finalize" style="cursor: pointer;" />
    </div>
<?}?>-->

<input type="hidden" id="att_cutoff" value="<?= $cutoff ?>">
<script src="<?=base_url()?>js/attendance/att_confirmreport.js"></script>
