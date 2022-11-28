<input type="hidden" id="deptartmentID" value="<?= (isset($deptid)) ? $deptid : '' ?>">
<table class="table table-striped table-bordered table-hover" id="deficiencyTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Type</b></th>
            <th><b>Description</b></th>
            <th><b>Concerned Office</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row):
        if($row['status'] != 0){ ?>
        <tr>
            <td class="align_center">
                <a id="<?=$row['id'];?>" class="btn btn-info editbtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>
                <a id="<?=$row['id'];?>" defType="<?=$row['type'];?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td><?=$row['type'];?></td>
            <td><?=$row['defdesc'];?></td>
            <td><?=$row['deptdesc'];?></td>
        </tr>
        <? } 
        endforeach; ?>
    </tbody>
</table>
<div id="delete-alert" class="hide">
    <div class="align_center"><h5>Are You sure you want to delete <span id="chosen-row" class="text-error"></span> ?</h5></div>
</div>
<div style="display: none;">
<div id="delete-alert-footer">
    <input type="hidden" class="hiddenid" />
    <a href="#" class="btn btn-success" id="del-submit">Yes</a>
    <a href="#" class="btn btn-danger del-close" data-dismiss="modal">No</a>
</div>
</div>
<script>
var toks = hex_sha512(" ");

$(".addbtn,.editbtn").click(function(){
    $("#modal-view").find("#delete-alert-footer").html("");
    $("#modal-view").find("#delete-alert-footers").html("");
    $("#modal-view").find("#modalclose").show();
    $("#modal-view").find("#button_save_modal").show();
    var infotype = "code_deficiency";
    var id = "";
    var departmentid = "";
    if($("#deptartmentID").val()) departmentid = "<?= (isset($deptid)) ? $deptid : '' ?>"; 
    if($(this).attr("id")) id = $(this).attr("id");
    $("#modal-view").find("h3[tag='title']").text(id ? "Edit Clearance" : "Add Clearance");
    $("#button_save_modal").text("Save");
    var form_data = {
        info_type:  GibberishAES.enc(infotype , toks),
        action:  GibberishAES.enc(id , toks),
        departmentid: GibberishAES.enc( departmentid, toks),
        toks:toks
    };
    $.ajax({
        url: "<?=site_url('deficiency_/viewForm')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
            
        }
    });  
});

$(".delbtn").unbind().click(function(){
    var id = $(this).attr("id");
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
        deleteDeficiency(id);
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

function deleteDeficiency(id){
    var infotype = "code_deficiency";
    $.ajax({
        url: "<?=site_url('deficiency_/deleteRow')?>",
        type: "POST",
        data: {id: GibberishAES.enc( id, toks), infotype: GibberishAES.enc(infotype, toks), toks:toks},
        success: function(msg){
            loaddeficiencydata();
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Clearance has been deleted successfully.',
              showConfirmButton: true,
              timer: 1000
          }) 
        }
    });
}

function loaddeficiencydata(){
  var deptid = $("#deptartmentID").val();
    $.ajax({
        url: "<?= site_url('deficiency_/loadDeficiency')?>",
        type: "POST",
        data: {deptid: GibberishAES.enc( deptid, toks),toks:toks},
        success:function(response){
            $("#data_table").html(response);
        }
    });
}

$(document).ready(function(){
    var table = $('#deficiencyTable').DataTable({
    });
 
    new $.fn.dataTable.FixedHeader( table );
}); 

if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
else $(".btn").css("pointer-events", "");

</script>