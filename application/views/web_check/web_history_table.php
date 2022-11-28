<table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr>
            <th class="text-center">Employee ID</th>
            <th class="text-center">Employee Name</th>
            <th class="text-center">Date</th>
            <th class="text-center">Check In Type</th>
            <th class="text-center">View Details</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($employee as $row): 
            ?>
            <tr>
                <td align="center"><?=Globals::_e($row['employeeid'])?></td>
                <td align="center"><?=Globals::_e($row['fullname'])?></td>
                <td align="center"><?=Globals::_e($row['localtimein'])?></td>
                <td align="center"><?=Globals::_e($row['log_type'])?></td>
                <td align="center"><a code="<?=Globals::_e($row['id'])?>" class="btn btn-info viewInfo"><i class="glyphicon glyphicon-eye-open"></i></a></td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDjPDJx-ya24arAX0jx5fdgPsm6EJex8SE&callback=initMap" async defer></script>
<script>
    var toks = hex_sha512(" ");
    $(document).ready(function(){
        var table = $('#tables').DataTable();
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#tables").on("click", ".viewInfo", function(e){
        if($(this).attr("code")){
            var form_data = {
                id : GibberishAES.enc($(this).attr("code") , toks),
                toks:toks
            }; 

            $.ajax({
                url : $("#site_url").val() + "/webcheckin_/loadWebHistoryDetail",
                type: "POST",
                data: form_data,
                success: function(msg){
                    $("#checkInDetails").html(msg);
                }
            });
        }
    });
    
</script>