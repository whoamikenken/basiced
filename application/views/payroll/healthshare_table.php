<table class="table table-striped table-bordered table-hover" id="philhealthShareTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary modalbtn" id="addphilhealth" href="#modal-view" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add New</a>
            </th>
        </tr>   
        <tr style="background-color: #0072c6;">
            <th class="align_center col-md-1"></th>
            <th class="sorting_asc">ID</th>
            <th>Minimum Salary</th>
            <th>Maximum Salary</th>
            <th>Percentage</th>
            <th>Default Amount</th>
        </tr>
    </thead>   
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center col-md-1">
                <a class="btn btn-info modalbtn" href="#modal-view" data-toggle="modal" code='<?=$row['id']?>'><span class="glyphicon glyphicon-edit"></span></a>&nbsp;
                <a class="btn btn-danger deletebtn" href="#" code='<?=$row['id']?>'><span class="glyphicon glyphicon-trash"></span></a>
            </td>
            <td class="align_center"><?=$row['id']?></td>
            <td><?=$row['min_salary']?></td>
            <td><?=$row['max_salary']?></td>
            <td><?=$row['percentage']?></td>
            <td><?=$row['def_amount']?></td>
        </tr>
<?php endforeach ?>
    </tbody>
</table>
<script>
    $(document).ready(function(){
        var table = $('#philhealthShareTable').DataTable({
        });
     
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#philhealthShareTable,#addphilhealth").on("click", ".modalbtn", function(){
        var code = "";
        var job = "add";  
        if($(this).attr("code")) code = $(this).attr("code");
        $("#modal-view").find("h3[tag='title']").text(code ? "Edit Setup" : "Add Setup");
        if(code) job = 'edit'
        $("#button_save_modal").text("Save");    
        var form_data = {
            code: code,
            job: job
        };
        $.ajax({
            url: "<?=site_url('setup_/managePhilhealthShare')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });  
    });

    $("#philhealthShareTable").on("click", ".deletebtn", function(){
         var ans = confirm("Are you sure you want to continue?");
         if(ans){
             $.ajax({
                url:"<?=site_url("payroll_/deletePhilhealthShare")?>",
                type:"POST",
                data:{code: $(this).attr("code"),},
                success: function(msg){
                    Swal.fire({
                        icon: 'Success',
                        title: 'Success!',
                        text: "Successfully deleted philhealth setup!",
                        showConfirmButton: true,
                        timer: 2000
                    }); 
                    empshare_setup();
                }
             }); 
         }   
         return false;   
    });
</script>