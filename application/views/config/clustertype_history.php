<?php
$query = $this->db->query("SELECT a.id, a.code, a.description, a.description, a.schedid, b.description as scheddesc, a.date_active, a.edited_by, a.timestamp FROM code_type_history a LEFT JOIN code_schedule b ON b.schedid=a.schedid");
?>
   <div class="panel animated fadeIn delay-1s">
       <div class="panel-heading" style="background-color: #0072c6;"><h4><b>History</b></h4></div>
        <?if($query->num_rows() > 0){?>
       <div class="panel-body">
            <table class="table table-striped table-bordered table-hover" id="clusterHistoryTable">
                <thead style="background-color: #0072c6;">
                    <tr >
                        <th>Code</th>
                        <th>Description</th>
                        <th>Schedule List</th>
                        <th>Date Active</th>
                        <th>Edited By</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                <?foreach($query->result() as $row){?>
                    <tr>
                        <td><?=Globals::_e($row->code)?></td>
                        <td><?=Globals::_e($row->description)?></td>
                        <td><?=Globals::_e($row->scheddesc)?></td>
                        <td><?= ( $row->date_active != null && $row->date_active != '0000-00-00 00:00:00') ? date("F d,Y",strtotime($row->date_active)) : ''?></td>
                        <td><?=Globals::_e($row->edited_by)?></td>
                        <td><?=date("F d,Y",strtotime($row->timestamp))?></td>
                    </tr>
                <?}?>
                </tbody>
            </table>
            <br />
        </div>
        <?}?>
    </div> 

<script>
    $(document).ready(function(){
    var table = $('#clusterHistoryTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
</script>
