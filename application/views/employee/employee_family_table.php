<table class="table table-hover table-responsive" id="familylist" width="100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Relation</th>
            <th>Date of Birth</th>
            <th>Data Approval Status</th>
            <th>Admin Remarks</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?
            $employee_child = $this->db->query("select * from employee_family where employeeid='$employeeid'")->result();
            if(count($employee_child)>0){
                foreach($employee_child as $eb){
            ?>
            <tr id="<?= $eb->id ?>" table="employee_family" style="border-top: 1px solid #ddd !important;">
                <td><?=Globals::_e($eb->name)?></td>
                <td reldata="<?=$eb->relation?>"><?=$this->extras->getrelation($eb->relation)?></td>
                <td><?=$eb->bdate?></td>
                <td class="tooltip" id="<?= $eb->id ?>" table="employee_family" style="border-top: 0px solid #ddd;">
                    <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$eb->status?><span class="tooltiptext tooltiptext_<?=$eb->id?>_employee_family" >Loading..</span></a><?php } ?>
                    <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a> <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$eb->status?></a><?php } ?>
                </td>
                <td><?=$eb->dra_remarks?></td>
                <td>
                  <div style="float: right; border-top: 1px solid #ddd !important;">
                    <?php if ($this->session->userdata("usertype") == "ADMIN" || $eb->status!='APPROVED'): ?>
                        <a class='btn btn-primary edit_children' tbl_id = "<?=$eb->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                        <a class='btn btn-warning delete_entry' tbl_id = "<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                    <?php endif ?>
                    </div>
                </td>
                
            </tr>    
            <?                            
                }
        }
        ?>                      
    </tbody>
</table>
<script type="text/javascript">
    $("#familylist").dataTable();

    $('a[tag="add_family"]').click(function(){
        addfamily("");
    })

    $('#familylist tbody').on('click', '.edit_children', function () {
        addfamily($(this), $(this).attr("tbl_id"));
    });

     $('#familylist tbody').on('click', '.update_status', function () {
        var current_status = $(this).text();

        var table = $(this).closest("tr").attr("table");
        var id = $(this).closest("tr").attr("id");
        var status = updateTableStatus(table, id);
        $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
        // $(this).text(status)
        if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
        else $(this).removeClass("btn-success").addClass("btn-danger");
    });

     $("#familylist .tooltip").hover(function(){
    var id = $(this).attr('id');
    var table = $(this).attr('table');
    loadStatusHistory(id, table);
  });

    function addfamily(obj, tbl_id = ""){
         $("#familylist").find("tr").each(function(){
           $(this).attr("iscurrent",0); 
         }) 
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Family Member" : "Add Family Member");
        $("#button_save_modal").text("Save");  
        $.ajax({
            url: $("#site_url").val() + "/employee_/efamily",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                     $(modal_display).find("select[name='eb_relation']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger("chosen:updated");
                     $(modal_display).find("input[name='eb_dob']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if($("#usertype").val() == "ADMIN"){
                        $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(4)").text());
                        $(modal_display).find("#draremarks").css("display", "block");
                     }
                  }else{
                    if($("#usertype").val() == "ADMIN"){
                        $(modal_display).find("#draremarks").css("display", "block");
                     }
                     $("#familylist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                }); 
            }
        });  
    }

$('#familylist tbody').on('click', '.delete_entry', function () {
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
        deletechildren($(this), $(this).attr("tbl_id"));
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

function deletechildren(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_children";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_family"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: $("#site_url").val() + "/employee_/deleteData",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){ 
            loadTable('employee_family_table');
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Successfully deleted!',
              showConfirmButton: true,
              timer: 1000
          })
        }
    });  
}


</script>