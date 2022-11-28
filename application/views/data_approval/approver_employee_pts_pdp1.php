<div class="panel-body">
    <input type="hidden" id="tablename" value="<?= $tbl ?>">
    <table class="table table-striped table-bordered table-hover" id="familyTable">
        <thead>
            <tr>
                <th style="border-bottom-width: 0px;"><a class="btn btn-success batchapproved" id="batchapproved_<?= $tbl ?>">Batch Approved</a></th>
            </tr>
            <tr style="background: #0072c6">
            	<th class="align_center" width="5%"><input type="checkbox" class="cbox"  name="checkall" /></th>
                <th>Employee</th>
                <th>Title</th>
                <th>Date</th>
                <th>Organizer</th>
                <th>Venue</th>
                <th>Location</th>
                <th class="align_center" width="5%">Status</th>
            </tr>
        </thead>
        <tbody>
        	<?php foreach ($tblData as $value):  ?>
        		<tr id="<?= $value['id'] ?>" table="<?= $tbl ?>" title="<?= $title ?>">
        			<td class="align_center"><input type="checkbox" name="empCheck" id="empCheck" class="double-sized-cb empCheck" employeeid="<?=$value['employeeid'] ?>" trid="<?= $value['id'] ?>"></td>
        			<td><?= $value['lname'].', '.$value['fname'].' '.$value['mname'] ?></td>
                    <td><?= $value['title']?></td>
                    <td><?= $value['datef'].'-'.$value['datet']?></td>
                    <td><?= $value['organizer']?></td>
                    <td><?= $value['venue'] ?></td>
                    <td><?= $value['location']?></td>
                    <td>
                        <a class="btn btn-danger update_status tr_<?= $value['id'] ?>"><?= $value['status']?></a>
                    </td>
        		</tr>
        	<?php endforeach;  ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
var tablename = $("#tablename").val();

$(document).ready(function() {
    $('#familyTable').DataTable( {
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    } );
} );

$("input[name='checkall'], #familyTable_paginate").click(function(){
    if($("input[name='checkall']").prop("checked")){
        $('#familyTable input[name="empCheck"]').each(function(){
            this.checked = true; 
        });
    }else{
        $('#familyTable input[name="empCheck"]').each(function(){
            this.checked = false;
        });
    } 
});

$("#familyTable").delegate(".update_status", "click", function(){
    var table = $(this).closest("tr").attr("table");
    var title = $(this).closest("tr").attr("title");
    var id = $(this).closest("tr").attr("id");
    var isBatch = $(this).attr("isBatch");
    var status = updateTableStatus(table, id);
    if(status){
        loadrequest_approval(table, title);
        if(!isBatch) alert("Successfully approved application!");
    }
});

function updateTableStatus(table, id){
    var approverid = $("#approverid").val();
    var status = "";
    $.ajax({
        url: "<?= site_url('employee_/updateTableStatus') ?>",
        type:"POST",
        data: {table: GibberishAES.enc(table, toks), id:  GibberishAES.enc(id, toks), approverid: GibberishAES.enc(approverid , toks), toks:toks},
        async: false,
        success:function(response){
          status = response;
        }
    });
    return status;
}

$("#batchapproved_"+tablename).click(function(){
    var counter = 0;
    $('#familyTable input[name="empCheck"]').each(function(){
        if($(this).prop("checked") == true){
            var trid = $(this).attr("trid");
            $(".tr_"+trid).attr("isBatch", "Yes");
            $(".tr_"+trid).click();
            counter++;
        }
    });
    if(counter > 0){
        alert("Successfully approved "+counter+" pending applications!");
    }else{
        alert("Select data to approve first..");
    }
});
</script>
