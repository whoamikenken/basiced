<table class="table table-hover table-responsive" id="emergencycontactlist" >
    <thead>
        <tr>
            <th>Name</th>
            <th>Relation</th>
            <th>Mobile #</th>
            <th>Home #</th>
            <th>Office #</th>
            <th>Type #</th>
            <th>Data Approval Status</th>
            <th>Admin Remarks</th>
            <th class="col-md-2">&nbsp;</th>
        </tr>
    </thead>

    <tbody class="emergencycontactlist" >
        <?
            $employee_emergencyContact = $this->db->query("select * from employee_emergencyContact where employeeid='$employeeid'")->result();
            if(count($employee_emergencyContact)>0){
                foreach($employee_emergencyContact as $eb){
            ?>
                <tr id="<?= $eb->id ?>" table="employee_emergencyContact" style="border-top: 1px solid #ddd !important;">
                    <td><?=Globals::_e($eb->name)?></td>
                    <td reldata='<?=$eb->relation?>'><?=$this->extras->getrelation($eb->relation)?></td>
                    <td><?=$eb->mobile?></td>
                    <td><?=$eb->homeNo?></td>
                    <td><?=$eb->officeNo?></td>
                    <td reltype='<?=$eb->type?>'><?=$eb->type?></td>
                    <td class="tooltip" id="<?= $eb->id ?>" table="employee_emergencyContact" style="z-index: 1000;" style="border-top: 0px solid #ddd !important;">
                        <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$eb->status?><span class="tooltiptext tooltiptext_<?=$eb->id ?>_employee_emergencyContact">Loading..</span></a><?php } ?>
                        <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$eb->status?></a><?php } ?>
                    </td>
                    <td><?=$eb->dra_remarks?></td>
                    <td>
                    <?php if ($this->session->userdata("usertype") == "ADMIN" || $eb->status!='APPROVED'): ?>
                    <div style="float: right; border-top: 1px solid #ddd !important;">
                    <a class='btn btn-primary eEmergencyContact' tbl_id = "<?=$eb->id?>" href='#modal-view' data-toggle='modal' style="margin-right: 10px;"><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning deleterelation' tbl_id = "<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                    <?php endif ?>
                    </td>

                    
                    </div>
                </tr>    
                <?                            
                }
            }else{
            ?>
                <!-- <tr>
                    <td colspan="9">No existing data</td>
                </tr> -->
            <? } ?>                      
    </tbody>
</table>
<script type="text/javascript">
    $("#emergencycontactlist").dataTable();

    $("#emergencycontactlist .tooltip").hover(function(){
    var id = $(this).attr('id');
    var table = $(this).attr('table');
    loadStatusHistory(id, table);
  });

    $('#emergencycontactlist tbody').on('click', '.update_status', function () {
        var current_status = $(this).text();

        var table = $(this).closest("tr").attr("table");
        var id = $(this).closest("tr").attr("id");
        var status = updateTableStatus(table, id);
        $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
        // $(this).text(status)
        if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
        else $(this).removeClass("btn-success").addClass("btn-danger");
    });

    $("a[tag='add_emergencyContact']").click(function(){
        addemergencycontact("");
    });

    $('#emergencycontactlist tbody').on('click', '.eEmergencyContact', function () {
        addemergencycontact($(this), $(this).attr("tbl_id"));
    });

    function addemergencycontact(obj, tbl_id = ""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Emergency Contact" : "Add Emergency Contact");
        $("#button_save_modal").text("Save");  
        $.ajax({
            url: $("#site_url").val() + "/employee_/eEmergencyContact",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                     $(modal_display).find("select[name='eb_relation']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger("chosen:updated");
                     $(modal_display).find("input[name='eb_mobile']").val(tdcur.find("td:eq(2)").text());
                     $(modal_display).find("input[name='eb_homeNo']").val(tdcur.find("td:eq(3)").text());
                     $(modal_display).find("input[name='eb_officeNo']").val(tdcur.find("td:eq(4)").text());
                     $(modal_display).find("select[name='eb_type']").val(tdcur.find("td:eq(5)").attr("reltype")).trigger("chosen:updated");
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     if($("#usertype").val() == "ADMIN"){
                        $(modal_display).find("input[name='dra_remarks']").val(tdcur.find("td:eq(7)").text());
                        $(modal_display).find("#draremarks").css("display", "block");
                     }
                  }else{
                    if($("#usertype").val() == "ADMIN"){
                        $(modal_display).find("#draremarks").css("display", "block");
                     }
                     $("#emergencycontactlist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                    $("#emergencycontactlist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                    });
                });
                }); 
            }
        });  
    }

    $('#emergencycontactlist tbody').on('click', '.deleterelation', function () {
        var mtable = $("#emergencycontactlist").find("tbody");
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
            deleterelation($(this), $(this).attr("tbl_id"));
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

    function deleterelation(obj, tbl_id = ""){
        var table = "";
        var userid = "";
        if($("input[name='applicantId']").val()){
            table = "applicant_emergencyContact";
            userid = $("input[name='applicantId']").val();
        }
        else{
            table = "employee_emergencyContact"; 
            userid = $("input[name='employeeid']").val();
        }
        $.ajax({
            url: $("#site_url").val() + "/employee_/deleteData",
            type: "POST",
            data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
            dataType: "JSON",
            success: function(msg){ 
                loadTable('employee_emergencyContact_table');
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