<style type="text/css">
  .swal2-cancel{
   margin-right: 20px;
 }
</style>
<table class="table table-striped table-bordered table-hover" id="user_datatable">
    <thead style="background-color: #0072c6;">
      <tr>
        <th width="10%">Action</th>
        <th width="10%">Code</th>
        <th width="80%">Description</th>
      </tr>
    </thead>   
    <tbody>
    	 <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a class="btn btn-info" href="#dtr-modal" tag="edit_d" data-toggle="modal" schedid="<?= $row['schedid'] ?>" isedit="1"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                <a class="btn btn-danger" href="#" tag="delete_d" schedid="<?= $row['schedid'] ?>"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td><?=Globals::_e($row['schedcode'])?></td>
            <td><?=Globals::_e($row['description'])?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<script>
var toks = hex_sha512(" "); 
var table = $('#user_datatable').DataTable({
});
new $.fn.dataTable.FixedHeader( table );


$("#user_datatable_length").append('&nbsp;<a id="addschedule" class="btn btn-primary" href="#dtr-modal" data-toggle="modal" style="margin-left: 10px;"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>');
$('.no-search .dataTables_length select').chosen();

$("#user_datatable").on("click", ".btn-info", function(){
   var schedid = $(this).attr("schedid");
   var isedit = $(this).attr("isedit");
   dotoggleuserinfo("Edit Schedule",{job:GibberishAES.enc("edit", toks),schedid:GibberishAES.enc(schedid, toks),isedit:GibberishAES.enc(isedit, toks), toks:toks});
});

$("#user_datatable").on("click", ".btn-danger", function(){
  var res='';
  var schedid = $(this).attr("schedid");
   const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "You won't be able to revert this!",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
         $.ajax({
         url:"<?=site_url("maintenance_/saveschedule")?>",
         data: {schedid:GibberishAES.enc(schedid, toks),job: GibberishAES.enc("delete", toks), toks:toks},
         type: "POST",
         success: function(msg){
         SCtable();
           res = msg.substring(0, 12);
          if(res == "Successfully" || msg == "Schedule has been deleted successfully."){
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: msg,
                showConfirmButton: true,
                timer: 1000
            })
          }else{
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: msg,
                showConfirmButton: true,
                timer: 1000
            })
          }
            

         }
     });
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Data is safe.',
         'error'
       )
     }
   })
});

$("#addschedule").click(function(){  
   dotoggleuserinfo("New Schedule",{job:GibberishAES.enc("new", toks), toks:toks});
});
function dotoggleuserinfo(title,data){
   $("#dtr-modal").find("h3[tag='title']").html(title); 
   $("#dtr-modal").find("div[tag='display']").html("Loading, please wait...");
   $("#dtr-modal").addClass("container");
   $("#dtr-modal").addClass("animated fadeInDown");
   $(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
   $("#button_save_modal").text("Save");   
   $.ajax({
       url:"<?=site_url("maintenance_/addnewschedule")?>",
       data: data,
       type: "POST",
       success: function(msg){
          $("#dtr-modal").find("div[tag='display']").html(msg);
       }
   }); 
}

if("<?=$this->session->userdata('canwrite')?>" == 0) $("#user_datatable_length").find(".btn").css("pointer-events", "none");
else $("#user_datatable_length").find(".btn").css("pointer-events", "");

</script>