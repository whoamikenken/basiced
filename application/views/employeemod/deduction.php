<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
$query = $this->employeemod->empothincome($this->session->userdata("username"));
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #A548A2;">
                        <h5>Deduction</h5>
                    </div>
                    <?if($query->num_rows() == 0){?>
                    <div class="well-content">
                        <table class="table table-bordered table-hover table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Other Income</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?foreach($query->result() as $row){?>
                                <tr>
                                    <td><?=$row->description?></td>
                                    <td>&#8369; <?=number_format($row->amount,2)?></td>
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
<script>

</script>