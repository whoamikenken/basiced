<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
$query = $this->employeemod->compensationhistory($this->session->userdata("username"));
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #A548A2;">
                        <h5>Compensation</h5>
                    </div>
                    <?if($query->num_rows() > 0){?>
                    <div class="well-content">
                        <table class="table table-bordered table-hover table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Working Days</th>
                                    <th>Monthly</th>
                                    <th>Semi-Monthly</th>
                                    <th>Daily</th>
                                    <th>Hourly</th>
                                    <th>Minutely</th>
                                    <th>Date</th>
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
                                    <td><?=date("F d,Y",strtotime($row->timestamp))?></td>
                                </tr>
                                <?}?>
                            </tbody>
                        </table>
                    </div>
                    <?}?>
                </div>
            </div>
        </div>        
    </div>        
</div>