 <table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr>
            <th align="center">Employee ID</th>
            <th align="center">Name</th>
            <th align="center">Teaching Type</th>
        </tr>
    </thead>
    <tbody id="employeelist" style="cursor: pointer;">
        <?php foreach($employee as $row): 
            ?>
            <tr employeeid='<?=$row['employeeid']?>'>
                <td><?=$row['employeeid']?></td>
                <td><?=$row['fullname']?></td>
                <td><?=$row['teachingtype']?></td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>
<script>
    $(document).ready(function(){
        var table = $('#tables').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#tables #employeelist").delegate("tr", "click", function() {
        if($(this).attr("employeeid")){
            var form_data = {
                employeeid : $(this).attr("employeeid"),
                view: "config/finger_capturing"
            }; 
            $.ajax({
                url : $("#site_url").val() + "/main/siteportion",
                type: "POST",
                data: form_data,
                success: function(msg){
                    $("#tableEmp").html(msg);
                }
            });
        }
    });
    
</script>