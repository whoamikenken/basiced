<?php 
 ?>
<table class="table table-striped table-bordered table-hover" id="facial_History">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" action="add" code=""><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th class="align_center"><b>Person Id</b></th>
            <th class="align_center"><b>Name</b></th>
            <th class="align_center"><b>Time</b></th>
            <th class="align_center"><b>I.P Adress</b></th>
            <th class="align_center"><b>Image</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center"><?=$row['personId']?></td>
            <td class="align_center"><?=$this->employee->getfullname($row['employeeid']) ?></td>
            <td class="align_center"><?= date('Y-m-d H:i:s', $row['time']/1000) ?></td>
            <td class="align_center"><?=$row['ip']?></td>
            <td class="align_center"><a href="<?=$row['path']?>" class="btn btn-primary">Download Snapshot</a></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    
</table>
<script>

$(document).ready(function(){
    var table = $('#facial_History').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
</script>