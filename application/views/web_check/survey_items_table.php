<table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr>
            <th><button class="btn btn-primary" id="addnew"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New</span></button></th>
        </tr>
        <tr>
            <th align="center">Category</th>
            <th align="center">Description</th>
            <th align="center">Date Created</th>
            <th align="center">Status</th>
            <th align="center">Audience</th>
            <th align="center">Actions</th>
        </tr>
    </thead>
    <tbody id="employeelist" style="cursor: pointer;">
        <?php foreach($record as $row): 
            ?>
            <tr>
                <td align="center"><?= $this->extensions->getCategoryDescription($row['category'])?></td>
                <td align="center"><?=$row['description']?></td>
                <td align="center"><?=$row['date_created']?></td>
                <td align="center"><?=$row['status']?></td>
                <td align="center"><a title="<?= $row['description'] ?>" audience="<?= $row['audience'] ?>" class="btn btn-primary audiencebtn" href="#modal-view" ><i class="glyphicon glyphicon-eye-open"></i></a></td>
                <td align="center">
                    <a code="<?=$row['id']?>" title="<?= $row['description'] ?>" class="btn btn-info viewbtn" href="#modal-view" ><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;&nbsp;
                    <a code="<?=$row['id']?>" class="btn btn-info editbtn" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?=$row['id']?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>
<script>
    $(document).ready(function(){
        var table = $('#tables').DataTable();
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#addnew").click(function () {
        var code = "none";
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + "/webcheckin_/manageSurveyitemsSetup",
            data: {code: code},
            success: function (response) {
                $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
                $("#modal-view").modal();
                $("#button_save_modal").show();
                $("#modal-view").find("h3[tag='title']").text("Add New Setup");
                $("#modal-view").find("div[tag='display']").html(response);
            }
        });
    });

    $("#tables").delegate(".editbtn", "click", function() {
        $.ajax({
            url : $("#site_url").val() + "/webcheckin_/manageSurveyitemsSetup",
            type: "POST",
            data: {code: $(this).attr("code")},
            success: function(msg){
                $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
                $("#modal-view").modal();
                $("#button_save_modal").show();
                $("#modal-view").find("h3[tag='title']").text("Edit Setup");
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });
    });

    $("#tables").delegate(".viewbtn", "click", function() {
        var title = $(this).attr("title");
        $.ajax({
            url : $("#site_url").val() + "/webcheckin_/viewSurveyitemsSetup",
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

    $("#tables").delegate(".audiencebtn", "click", function() {
        var audience = $(this).attr("audience");
        var title = $(this).attr("title");
        $.ajax({
            url : $("#site_url").val() + "/webcheckin_/viewSurveyitemsAudience",
            type: "POST",
            data: {audience: audience, title: title},
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