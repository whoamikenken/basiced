<h2>Employee Confirmed</h2>
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
                <th rowspan="2">Confirmed By</th>
                <th>Unconfirm</th>
            </tr>
            <tr>
                <th class="align_center" width="1%"><input type="checkbox" class="cbox"  name="checkall" /></th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $count_total = 0;
            foreach($result as $value):
            $confirmedby = $value["confirmedby"];
            if($value["usertype"] == "EMPLOYEE") $confirmedby = $this->extensions->getEmployeeName($confirmedby);
            // $is_processed = $this->payroll->is_payroll_processed($value['qEmpId'], $value["payroll_cutoffstart"], $value["payroll_cutoffend"]);
            // if($is_processed > 0) $value["status"] = "PROCESSED";
            // else $value["status"] = "";
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
                        <td style='display: none'><?php echo  $value['qDepartment'] ?></td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td><?= Globals::_e($value['qEmpId']) ?></td>
                    <td><?= Globals::_e($value['qFullname']) ?></td>
                    <td><?= Globals::_e($value['qDepartment']) ?></td>
                    <td><b><?= $value['status'] ?></b></td>
                    <td><?= $value['date_processed'] ?></td>
                    <td><?= strtoupper($confirmedby) ?></td>
                    <td class="align_center" width="1%"><input type="checkbox" class="cbox"  name="econfirm" value="<?=$value['qEmpId']?>" status="<?=$value["status"]?>" <?= ($value["status"] == "PROCESSED") ? "disabled" : "" ?> /></td>
                </tr>
                <?php $last_deptid = $value["qDepartment"]; ?>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
<input type="hidden" id="totalnotConfirmed" value="<?=$count_total?>">
<div class="pull-right">
    <span id="cmsg"></span>
    <input type="button" id="confirmattbtn" class="btn btn-primary" value="Unconfirm selected data" style="cursor: pointer;" />
</div>
<script type="text/javascript">
    $(document).ready(function() {
    $('#asctblnt').DataTable( {
        "lengthMenu": [[-1], ["All"]],
        "columnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, 2, 3, 4, 5, 6 ] }, ],
        "order": [[2, 'asc']],
        "paging": false
    } );
} );

    $("#asctblnt_info").text("Showing 1 to "+$("#totalnotConfirmed").val()+" of "+$("#totalnotConfirmed").val()+" entries");
</script>
<script src="<?=base_url()?>js/attendance/manage_confirmed-un.js"></script>
<script>
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#confirmattbtn, input").css("pointer-events", "none");
    else $("#confirmattbtn, input").css("pointer-events", "");
</script>