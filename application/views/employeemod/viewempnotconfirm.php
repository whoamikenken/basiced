<h2>Employee Not Confirmed</h2>
<p><b>Type: <?= $teachingtype ?></b></p>
<input type="hidden" id="from_date" value="<?= $from_date ?>">
<input type="hidden" id="to_date" value="<?= $to_date ?>">
<input type="hidden" id="ttype" value="<?= $tnt ?>">
<div id="attstbl" class="well_content">
    <table class="table table-striped table-bordered table-hover datatable" id="asctblnt">
        <thead>
            <tr>
                <th rowspan="2">Employee ID</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Department</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Date Confirm</th>
                <th>Confirm</th>
            </tr>
            <tr>
                <th class="align_center" width="1%"><input type="checkbox" class="cbox" name="checkall" /></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $count_total = 0;
            foreach($result as $value):
            $count_total++;
             ?>
                <?php if($last_deptid != $value["qDepartment"]): ?>
                    <tr>
                        <td colspan="6"><b><?php echo  $value['qDepartment'] ?></b></td>
                        <td style='display: none'><?php echo  $value['qDepartment'] ?></td>
                        <td style='display: none'><?php echo  $value['qDepartment'] ?></td>
                        <td style='display: none'><?php echo  $value['qDepartment'] ?></td>
                        <td style='display: none'><?php echo  $value['qDepartment'] ?></td>
                        <td style='display: none'><?php echo  $value['qDepartment'] ?></td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td><?= Globals::_e($value['qEmpId']) ?></td>
                    <td><?= Globals::_e($value['qFullname']) ?></td>
                    <td><?= Globals::_e($value['qDepartment']) ?></td>
                    <td class="align_center"><i><?= isset($value['status']) ? $value['status'] : "Not yet confirmed" ?></i></td>
                    <td class="align_center"><i><?= isset($value['date_processed']) ? $value['date_processed'] : "Not yet confirmed" ?></i></td>
                    <td class="align_center" width="1%"><input type="checkbox" class="cbox" name="econfirm" value="<?=$value['qEmpId']?>" /></td>
                </tr>
                <?php $last_deptid = $value["qDepartment"]; ?>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<input type="hidden" id="totalnotConfirmed" value="<?=$count_total?>">
<div class="pull-right">
    <span id="cmsg"></span>
    <span id="recompute_percentage"></span>
    <input type="button" id="confirmattbtn" class="btn btn-primary" value="Confirm selected data" style="cursor: pointer;" />
</div>
<script type="text/javascript">
    $(document).ready(function() {
    $('#asctblnt').DataTable( {
        "lengthMenu": [[-1], ["All"]],
        "columnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, 2, 3, 4, 5 ] }, ],
        "order": [[2, 'asc']],
        "paging": false
    } );
} );

    $("#asctblnt_info").text("Showing 1 to "+$("#totalnotConfirmed").val()+" of "+$("#totalnotConfirmed").val()+" entries");
</script>
<script src="<?=base_url()?>js/attendance/manage_unconfirmed-un.js"></script>
<script>
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#confirmattbtn, input").css("pointer-events", "none");
    else $("#confirmattbtn, input").css("pointer-events", "");
</script>