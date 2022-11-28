<?php

/**
 * @author Justin
 * @copyright 2016
 */

?>
<!-- <style type="text/css">
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
    white-space: pre-wrap;
}
</style> -->
<div class="row">
    <div class="col-md-12">
        <table class="table table-striped table-bordered table-hover" id="ldetails">
            <thead style="background-color: #0072c6">
                <tr>
                    <th class="align_center">Leave Type</th>
                    <th class="align_center">Leave Credits</th>
                    <th class="align_center">Leave Availments</th>
                    <th class="align_center">Leave Balance</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="4"><a class="btn btn-primary" id="applyleave" data-target="#mymodalleave" data-toggle="modal" class="btn btn-default">Apply For Leave</a></td>
                </tr>
            </tfoot>
            <tbody>
                    <?
                        $query = $this->employeemod->displayleavetype();
                        if($query->num_rows() > 0){
                            foreach($query->result() as $row){
                    ?>
                        <tr>
                            <td width='25%'><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" title="<?=$row->details?>"> <?=$row->description?></td>
                            <td class="align_center" width='25%'><?=$row->credit?></td>
                            <td class="align_center" width='25%'><?=$row->avail?></td>
                            
                            <td class="align_center" width='25%'><?=$row->balance?></td>
                            
                        </tr>   
                    <?
                            }
                        }else{
                    ?>
                        <tr>
                            <td colspan="2" class="align_center"><i>No Data Exists..</i></td>
                        </tr>
                    <?}?>   
            </tbody>
        </table>
    </div>
</div>
<script>
var toks = hex_sha512(" ");
$("#applyleave").click(function(){  
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {toks:toks,folder: GibberishAES.enc("employeemod", toks), view: GibberishAES.enc("leave_app/leave_apply", toks)},
        success: function(msg){
            $("#mymodalleave").html(msg);
        }
    });
});
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>