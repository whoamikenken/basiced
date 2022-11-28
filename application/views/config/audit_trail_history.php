<style type="text/css">
    .dataTables_paginate {
        margin-top: 7px;
        padding-right: 30px;
    }

    td{
        word-wrap: break-word;
    }

    /*.datatable th, .datatable td {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }*/
</style>
<table class="table table-striped table-bordered table-hover table-responsive" id="auditTable">
    <thead style="background-color: #0072c6;">                           
        <tr>
            <th><b>Username</b></th>
            <th><b>Fullname</b></th>
            <th><b>Accessed Module</b></th>
            <th><b>Query</b></th>
            <th><b>Date / Time</b></th>
        </tr>
    </thead>
    <tbody>
        <?
        if($auditTrail):
            foreach($auditTrail as $at):?>
                <tr>
                    <td ><?=$at->username?></td>
                    <td ><?=$at->fullname?></td>
                    <td ><?=$at->title?></td>
                    <td  style="max-width: 600px;"><?=$at->que?></td>
                    <td ><?=$at->dtime?></td>
                </tr>
        <?
            endforeach;
        endif;
        ?>
    </tbody>
    
</table>

<script type="text/javascript">
    $(document).ready(function(){
        $('#auditTable').DataTable();
    });
</script>