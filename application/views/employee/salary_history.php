<?php

/**
 * @author Justin
 * @copyright 2017
 */

$query = $this->employeemod->compensationhistory($this->input->post('eid'));
?>
<style type="text/css">
    th{
        background-color: #0072c6;
    }
</style>
<div class="col-md-12" style="margin-top: 39px;">
    <div class="panel">
       <div class="panel-heading"><h4><b>Salary History</b></h4></div>
       <div class="panel-body">
            <table class="table table-striped table-bordered table-hover" id="ph">
                <thead>
                    <tr>
                        <th>Working Days</th>
                        <th>Monthly</th>
                        <th>Semi-Monthly</th>
                        <th>Daily</th>
                        <th>Hourly</th>
                        <th>Minutely</th>
                        <th>Date Effective</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                <?foreach($query->result() as $row){?>
                    <tr>
                        <td><?=$row->workdays?></td>
                        <td>&#8369; <?=number_format($row->monthly,2)?></td>
                        <td>&#8369; <?=number_format($row->semimonthly,2)?></td>
                        <td>&#8369; <?=number_format($row->daily,2)?></td>
                        <td>&#8369; <?=number_format($row->hourly,2)?></td>
                        <td>&#8369; <?=number_format($row->minutely,2)?></td>
                        <td><?=date("F d,Y",strtotime($row->date_effective))?></td>
                        <td><?=date("F d,Y",strtotime($row->timestamp))?></td>
                    </tr>
                <?}?>
                </tbody>
            </table>
            <br/>
        </div>
    </div> 
</div>
<script>
$(document).ready(function(){
    var table = $('#ph').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
</script>