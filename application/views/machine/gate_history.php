<div id="history_length"></div>
<table class="table table-striped table-bordered table-hover datatable" id="gate_historylist">
    <thead style="background-color: #0072c6;">
        <tr>
            <th class="align_center sorting_asc">User</th>
            <th class="align_center">IP</th>
            <th class="align_center">Date Log in</th>
            <th class="align_center">Date Log out</th>
            <th class="align_center">Logged out by</th>
        </tr>
    </thead>   
        <tbody>
            <?php foreach($records as $row): ?>
                <tr>
                    <td class="align_center"><?=Globals::_e($row['username'])?></td>
                    <td class="align_center"><?=Globals::_e($row['ip'])?></td>
                    <td class="align_center"><?=Globals::_e($row['login']) != '0000-00-00 00:00:00' ? (date('M d, Y h:i:s A')):''?></td>
                    <td class="align_center"><?=Globals::_e($row['logout']) != '0000-00-00 00:00:00' ? (date('M d, Y h:i:s A')):''?></td>
                    <td class="align_center"><?=Globals::_e($row['logout_by'])?></td>
                </tr> 
            <?php endforeach ?>
    </tbody>
</table>
<script src="<?=base_url()?>js/terminal_setup/terminal_history.js"></script>

<script>
var history_table = $('#gate_historylist').DataTable({
});
new $.fn.dataTable.FixedHeader( history_table );
</script>