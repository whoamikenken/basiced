<table class="table table-striped table-bordered table-hover" id="_datatable">
    <thead>
      <tr>
        <th>Action</th>
        <th>Code</th>
        <th>Description</th>
      </tr>
    </thead>   
    <tbody>
       <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a class="btn btn-info" href="#modal-view" tag="edit_d" data-toggle="modal" rid="<?= $row['rid'] ?>" isedit="1"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                <a class="btn btn-danger" href="#" tag="delete_d" rid="<?= $row['rid'] ?>"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td><?=$row['u_code']?></td>
            <td><?=$row['u_description']?></td>
        </tr> 
        <?php endforeach ?>
    </tbody>
</table>

<script>
var table = $('#user_datatable').DataTable({
});
new $.fn.dataTable.FixedHeader( table );


$("#user_datatable_length").append('&nbsp;<a id="addschedule" class="btn btn-primary" href="#modal-view" data-toggle="modal" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>');
$('.no-search .dataTables_length select').chosen();

$("#user_datatable").on("click", ".btn-info", function(){
   var rid = $(this).attr("rid");
   dotoggleuserinfo("Edit Request Type",{job:"edit",rid:rid});
});

$("#user_datatable").on("click", ".btn-danger", function(){
   var ans = confirm("Are you sure you want to continue?");
   if(ans){ 
   var rid = $(this).attr("rid");
   $.ajax({
       url:"<?=site_url("maintenance_/saveschedule")?>",
       data: {rid:rid,job:"delete"},
       type: "POST",
       success: function(msg){
       RTtable();
         alert(msg);

       }
   });
   }
});

$("#addschedule").click(function(){  
   dotoggleuserinfo("New Schedule",{job:"new"});
});
function dotoggleuserinfo(title,data){
   $("#modal-view").find("h3[tag='title']").html(title); 
   $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
   $("#modal-view").addClass("container");
   $("#modal-view").addClass("animated fadeInDown");
   $(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
   $("#button_save_modal").text("Save");   
   $.ajax({
       url:"<?=site_url("maintenance_/addnewschedule")?>",
       data: data,
       type: "POST",
       success: function(msg){
          $("#modal-view").find("div[tag='display']").html(msg);
       }
   }); 
}

</script>