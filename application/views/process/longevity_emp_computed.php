<?php
/**
 * @author Angelica
 * @copyright 2018
 *
 */
?>

<div class="well blue">
    <div class="well-header">
    </div>
    <div class="well-content"> 

        <a href="#" class="btn btn-primary" id="butt_printresult" style="float:right">Print</a><br>
        
        <table class="table table-striped table-bordered table-hover" id="longevity_computed_table" >
            <thead>
                <tr style="background-color:#343434;color:white">
                    <th class="align_center">Employee ID </th>
                    <th class="align_center">Employee Name</th>
                    <th class="align_center" width="10%">Date Hired</th>
                    <th class="align_center">Date of Regular Appointment</th>
                    <th class="align_center"># of Credited Yrs. of Service as Regular</th>
                    <th class="align_center">Previous Basic Pay<br><?=date("Y",strtotime("01-01-".$cutoff_year."- 2 year"))." - ".date("Y",strtotime("01-01-".$cutoff_year."- 1 year"))?></th>
                    <th class="align_center">Present Basic Pay<br><?=date("Y",strtotime("01-01-".$cutoff_year."- 1 year"))." - ".date("Y",strtotime("01-01-".$cutoff_year))?></th>
                    <th class="align_center"><?=date("Y",strtotime("01-01-".$cutoff_year."- 4 year"))." - ".date("Y",strtotime($cutoff_year."- 1 year"))?><br>Longevity Pay Per Month</th>
                    <th class="align_center"><?=date("Y",strtotime("01-01-".$cutoff_year."- 1 year"))." - ".date("Y",strtotime("01-01-".$cutoff_year))?><br>Longevity Pay Per Month</th>
                    <th class="align_center">Proposed Increase Per Month</th>
                    <th class="align_center">Is Included</th>
                </tr>
            </thead>
            <tbody>

                <? foreach ($emplist as $deptid => $emp_det) {?>
                            <tr><td colspan="11"><?=$officelist[$deptid]?></td></tr>

                        <?foreach ($emp_det as $employeeid => $peremp) {
                ?>
                            <tr>
                                <td><?=$employeeid?></td>
                                <td><?=$peremp['fullname']?></td>
                                <td><?=$peremp['dateemployed']?></td>
                                <td><?=$peremp['regdate']?></td>
                                <td class="align_center"><?=$peremp['credited_years']?></td>
                                <td><?=$peremp['prev_basicpay']?></td>
                                <td><?=$peremp['present_basicpay']?></td>
                                <td></td>
                                <td class="align_right"><?=$peremp['amount']?></td>
                                <td></td>
                                <td class="align_center">
                                    <input type="checkbox" name="" class="double-sized-cb" <?=$peremp['isIncluded'] ? 'checked':''?>>
                                </td>
                            </tr>
                <?            
                        }
                } ?>

            </tbody>
        </table>
        <br>
    </div>
</div>


<script>
   
</script>