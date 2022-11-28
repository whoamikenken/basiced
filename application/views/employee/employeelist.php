<table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr>
            <th class="col-md-1">Employee List</th>
        </tr>
    </thead>
    <tbody id="employeelist" style="cursor: pointer;">
                                    
        <?php foreach($employee as $row): ?>
            <tr employeeid='<?=$row['employeeid']?>'>
                <td>
                    <div class="media">
                        <div class="media-left">
                            <img src="<?= $row['user_img']?>" class="img-circle" style="width:60px;height: 80px;">
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?=$row['fullname']?></h4>
                            <ul>
                                <li>Employee ID : <?=$row['employeeid']?></li>
                                <li>Type : <?= $row['teachingtype'] ?></li>
                                <li>Department : <?= $row['deptid'] ?></li>
                                <li>Sched Cluster : <?= $row['emptype'] ?></li>
                            </ul>
                        </div>
                        <div class="media-right">
                            <div class="row">
                                <div class="container">
                                    <div class="col-md-6 col-md-offset-6">
                                        <div class="pull-right">
                                            <span><b><?= $row['remarks'] ?></b></span>&nbsp;&nbsp;
                                            <img src="<?= $row['remarks_icon']?>" class="media-object" style="width: 40px;">
                                            <p style="color:black;"><i>Status: <b><?= $row['status']?> </b></i></p>&nbsp;&nbsp;
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                  </div>
              </td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>    
<script>
    $(document).ready(function(){
        $('#tables').DataTable().destroy();
        $("#tables").DataTable( {
            "sPaginationType": "full_numbers",
            "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
            "aLengthMenu": [[15, 25, 50, -1], [15, 25, 50, "All"]],
            "pageLength": 5
        });
    });

    $("#tables #employeelist").delegate("tr", "click", function() {
        if($(this).attr("employeeid")){
            var form_data = {
                job : "edit",
                employeeid : $(this).attr("employeeid"),
                view: "employee/personal_info"
            }; 
            $.ajax({
                url : $("#site_url").val() + "/main/siteportion",
                type: "POST",
                data: form_data,
                success: function(msg){
                    $("#content").html(msg);
                }
            });
        }
    });
    
</script>