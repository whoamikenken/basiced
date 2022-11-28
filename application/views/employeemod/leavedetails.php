<?php

/**
 * @author Justin
 * @copyright 2016
 */
echo "adssdsaasddsadsa";
?>
<style>
/*.datatable thead tr th{*/
#ldetails thead tr th{
    background-color: #4a4a48;
    color: #ffffff;
}
</style>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <table class="table table-hover table-bordered datatable" id="ldetails">
            <thead>
                <tr>
                    <th class="align_center">Leave Type</th>
                    <th class="align_center">Leave Credits</th>
                    <th class="align_center">Leave Availments</th>
                    <th class="align_center">Leave Balance</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="4"><a class="btn btn-primary" id="applyleave" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">Apply Leave</a></td>
                </tr>
            </tfoot>
            <tbody>
                    <?
                        $query = $this->employeemod->displayleavetype();
                        if($query->num_rows() > 0){
                            foreach($query->result() as $row){
                    ?>
                        <tr>
                            <td width='50%'><?=$row->description?></td>
                            <td class="align_center" width='30%'><?=$row->credit?></td>
                            <td class="align_center" width='20%'><?=$row->avail?></td>
                            <?if($row->leavetype == "VL"){?>
                                <td class="align_center" width='20%' rowspan="2"><?=$row->VLELBAL?></td>
                            <?}?>
                            <?if($row->leavetype == "SL"){?>
                                <td class="align_center" width='20%'><?=$row->balance?></td>
                            <?}?>
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
$("#applyleave").click(function(){  
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "leave_app/leave_apply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });
});
</script>