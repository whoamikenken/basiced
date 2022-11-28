<table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr>
            <th class="text-center">Employee</th>
            <th class="text-center">Fullname</th>
            <th class="text-center">Date</th>
            <th class="text-center">Category</th>
            <th class="text-center">Survey Description</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($employee as $row): 
            ?>
            <tr>
                <td align="center"><?=$row['employeeid']?></td>
                <td align="center"><?=$row['fullname']?></td>
                <td align="center"><?=$row['date_created']?></td>
                <td align="center"><?=$this->extensions->getCategoryDescription($row['category'])?></td>
                <td align="center"><?=$row['description']?></td>
                <td align="center"><a code="<?=$row['id']?>" title="<?=$row['description']?>" class="btn btn-info viewInfo"><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;&nbsp;<a code="<?=$row['id']?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a></td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDjPDJx-ya24arAX0jx5fdgPsm6EJex8SE&callback=initMap" async defer></script>
<script>
    $(document).ready(function(){
        var table = $('#tables').DataTable();
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#tables").delegate(".viewInfo", "click", function() {
        var title = $(this).attr("title");
        $.ajax({
            url : $("#site_url").val() + "/webcheckin_/viewSurveyResponseSetup",
            type: "POST",
            data: {code: $(this).attr("code")},
            success: function(msg){
                $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
                $("#modal-view").modal();
                $("#button_save_modal").hide();
                $("#modal-view").find("h3[tag='title']").text(title);
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });
    });
    
</script>