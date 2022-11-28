
<table class="table table-striped table-bordered table-hover" id="emailTable">
    <thead>                         
        <tr style="background-color: #0072c6;">
            <th><b>From Email</b></th>
            <th><b>From Name</b></th>
            <th><b>Date Created</b></th>
            <th><b>Update By</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td><?=$row['from_email']?></td>
            <td><?=$row['from_name']?></td>
            <td><?=$row['date_created']?></td>
            <td><?=$row['updated_by']?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<script>  
    $(document).ready(function () {
        var table = $('#emailTable').DataTable({
            "order": []
        });
        new $.fn.dataTable.FixedHeader(table);
        $("input[name='from_email']").val("<?= (count($records))? $records[0]['from_email']:"" ?>");
        $("input[name='from_name']").val("<?= (count($records))? $records[0]['from_name']:"" ?>");
    });
</script>