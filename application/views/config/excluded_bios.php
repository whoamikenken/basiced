
 <table class="table table-striped table-bordered table-hover" id="tablesexcluded" >
    <thead>
        <tr>
            <th>
                <select class="chosen" id="empexcl">
                    <option value="">- Please Select -</option>
                    <?php foreach ($employee as $row) { ?>
                        <option value="<?= $row['employeeid'] ?>" data="<?= $row['fullname'] ?>"><?= $row['employeeid'] ?> - <?= $row['fullname'] ?></option>
                    <? } ?>
                </select>
            </th>
            <th>
                <a type="button" class="btn btn-primary" id="addemp"><span class="glyphicon glyphicon-plus"></span>Add</a>
            </th>
        </tr> 
        <tr>
            <th align="center">Employee ID</th>
            <th align="center">Name</th>
            <th align="center">Teaching Type</th>
            <th align="center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($bio as $row): 
            ?>
            <tr>
                <td><?=$row['employeeid']?></td>
                <td><?=$row['fullname']?></td>
                <td><?=$row['teachingtype']?></td>
                <td><button class="btn btn-danger deleteEx" employeeid='<?=$row['employeeid']?>'>Delete</button></td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>

<script>
    $(document).ready(function(){
        var table = $('#tablesexcluded').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
        $('.chosen').chosen();
    });

    $("#tablesexcluded").delegate(".deleteEx", "click", function() {
        if($(this).attr("employeeid")){
            var form_data = {
                code : $(this).attr("employeeid")
            }; 
            $.ajax({
                url : "<?= site_url('setup_/removeToExcluded')?>",
                type: "POST",
                data: form_data,
                success: function(msg){
                    getEmployeeExcluded();
                }
            });
        }
    });

    $("#addemp").click(function(){
        var code = '';
        code = $("#empexcl option:selected").val();
        if (!code) {
            alert("Please Select An Employee!");
            return
        }
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/addToExcluded')?>",
            data: {code:code, name:$("#empexcl option:selected").attr("data")},
            success:function(response){
                if(response) msg = "Successfully Added! ";
                else msg = "error";
                alert(msg);
                getEmployeeExcluded();
            }
        });
    });

</script>