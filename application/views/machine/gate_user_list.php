<style type="text/css">
    #userstable_wrapper{
        padding-top: 15px;
    }
</style>

<br>
<table class="table table-striped table-bordered table-hover datatable" id="userstable" >
    <thead style="background-color: #0072c6;">
        <tr>
            <th class="sorting_asc">Username</th>
            <th>Terminal</th>
            <th>Campus</th>
            <th>Building</th>
            <th>Floor</th>
            <!-- <th>Template</th> -->
            <th>Status</th>
            <th>Actions</th>
      </tr>
    </thead>   
    <tbody>
        <?php foreach($records as $row):  ?>
            <tr>
                <td><?=Globals::_e($row->username)?></td>
                <td><?=Globals::_e($row->terminal_name)?></td>
                <td><?=Globals::_e($this->extensions->getCampusDescription($row->campus))?></td>
                <td><?=Globals::_e($row->building)?></td>
                <td><?=Globals::_e($row->floor)?></td>
                <!-- <td><?= ($row->template == 1)? "Template 1":"Template 2" ?></td> -->
                <td class="align_center">
                    <div class="onoffswitch">
                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox gateswitch" id="<?='id-'.$row->username?>" username="<?=$row->username?>" online_id="<?=$row->online_id?>" <?= $this->setup->checkIfGateIsActive($row->username) ? ' checked activity="Yes"':' disabled activity="No"' ?>>
                        <label class="onoffswitch-label" for="<?='id-'.$row->username?>">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>  
                </td>
                <td align="center">
                    <div class="actionBtn_<?= $row->username ?>">
                        <a eid="<?=$row->id?>" class="btn btn-info editbtn" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;&nbsp;<a did="<?=$row->id?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
                    </div>
                </td>
            </tr> 
        <?php endforeach ?>
    </tbody>
</table>
<script src="<?=base_url()?>js/terminal_setup/terminal_history.js"></script>
<script>
var history_tables = $('#userstable').DataTable({
});
new $.fn.dataTable.FixedHeader( history_tables );
</script>