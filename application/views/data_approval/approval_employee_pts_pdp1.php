<div class="panel-body">
    <input type="hidden" id="tablename" value="<?= $tbl ?>">
    <table class="table table-striped table-bordered table-hover" id="familyTable" width="100%">
        <thead>
            <tr>
                <th  style="border-bottom-width: 0px; border: 0px;"><a class="btn btn-success batchapproved" id="batchapproved_<?= $tbl ?>">Batch Approved</a></th>
                <th  style="border-bottom-width: 0px; border: 0px;"><a class="btn btn-danger batchdelete" id="batchdelete_<?= $tbl ?>">Batch Delete</a></th>
            </tr>
            <tr style="background: #0072c6">
            	<th class="align_center" width="5%"><input type="checkbox" class="cbox"  name="checkall" /></th>
                <th>Employee</th>
                <th>Title</th>
                <th>Date</th>
                <th>Organizer</th>
                <!-- <th>Venue</th> -->
                <th>Location</th>
                <th class="align_center" width="5%">Status</th>
                <th width="10%">Admin Remarks</th>
                <th class="align_center" width="10%">Actions</th>
            </tr>
        </thead>
        <tbody>
        	<!-- <?php foreach ($tblData as $value):  
            list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            ?>
        		<tr id="<?= $value['id'] ?>" table="<?= $tbl ?>" title="<?= $title ?>">
        			<td class="align_center"><input type="checkbox" name="empCheck" id="empCheck" class="double-sized-cb empCheck" employeeid="<?=$value['employeeid'] ?>" trid="<?= $value['id'] ?>"></td>
        			<td><?= $value['lname'].', '.$value['fname'].' '.$value['mname'] ?></td>
                    <td><?= $value['seminar_title']?></td>
                    <td><?= $value['datef'].'-'.$value['datet']?></td>
                    <td><?= $value['organizer']?></td>
                    <td><?= $value['venue'] ?></td>
                    <td><?= $value['location']?></td>
                    <td>
                        <a class="btn btn-danger update_status tr_<?= $value['id'] ?>"><?= $value['status']?></a>
                    </td>
                    <td>
                        <div style="float: right">
                            <a class='btn btn-primary edit_pts_pdp1' href='#modal-view' data-toggle='modal' tbl_id = "<?= $value['id'] ?>" empname = "<?= $value['lname'].', '.$value['fname'].' '.$value['mname'] ?>"  employeeid="<?=$value['employeeid'] ?>" seminar_title="<?=$value['seminar_title'] ?>" datef="<?=$value['datef'] ?>" datet="<?=$value['datet'] ?>" organizer="<?=$value['organizer'] ?>" venue="<?=$value['venue'] ?>" location="<?=$value['location'] ?>" regfee="<?=$value['regfee'] ?>" accfee="<?=$value['accfee'] ?>" transfee="<?=$value['transfee'] ?>" total="<?=$value['total'] ?>" mime="<?= $value['mime']?>" content="<?= $value['content']?>"><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_<?= $value['id'] ?>_delete' tbl_id = "<?= $value['id'] ?>" table="<?= $tbl ?>" employeeid="<?=$value['employeeid'] ?>"><i class='glyphicon glyphicon-trash'></i></a>
                        </div>
                    </td>
        		</tr>
        	<?php endforeach;  ?> -->
        </tbody>
    </table>
</div>
<script type="text/javascript">
  var toks = hex_sha512(" ");
loadRequestApprovalSSP();
var tablename = $("#tablename").val();
var batchdelete  = 0;
// $(document).ready(function() {
//     $('#familyTable').DataTable( {
//         "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
//     } );
// } );

function loadRequestApprovalSSP(){
    $('#familyTable').DataTable().destroy();
    var tbl = GibberishAES.enc($("#requestedData").val(), toks);
    var department = GibberishAES.enc($("#department").val(), toks);
    var office =  GibberishAES.enc($("#office").val(), toks);
    var employeeid = GibberishAES.enc($("#employeeid").val(), toks);
    table = $('#familyTable').DataTable({
        "sAjaxSource": "<?=site_url("approval_/loadRequestApprovalSSP")?>",
        "sServerMethod": "POST",
        "fnServerParams" : function(aoData){
            aoData.push({"name":"toks", "value":toks});
            aoData.push({"name":"tbl", "value":tbl});
            aoData.push({"name":"department", "value":department});
            aoData.push({"name":"office", "value":office});
            aoData.push({"name":"employeeid", "value":employeeid});
        },
        'createdRow': function( row, data, dataIndex ) {

              $(row).attr('table', $("#requestedData").val()).attr('title', $("#requestedData option:selected").text()).attr("id", data[9]);
        },
        'columnDefs': [
             {
                'targets': 0,
                'createdCell':  function (td, cellData, rowData, row, col) {
                   $(td).addClass("align_center");
                }
             }
          ],
        "drawCallback": function(settings) {
          loadFile();
        }
    });
    new $.fn.dataTable.FixedHeader(table);
}

$("input[name='checkall'], #familyTable_paginate").click(function(){
    if($("input[name='checkall']").prop("checked")){
        $('#familyTable input[name="empCheck"]').each(function(){
            this.checked = true; 
        });
    }else{
        $('#familyTable input[name="empCheck"]').each(function(){
            this.checked = false;
        });
    } 
});

$("#familyTable").delegate(".update_status", "click", function(){
    var table = $(this).closest("tr").attr("table");
    var title = $(this).closest("tr").attr("title");
    var department = $(this).closest("tr").attr("department");
    var office = $(this).closest("tr").attr("office");
    var employeeid = $(this).closest("tr").attr("employeeid");
    var id = $(this).closest("tr").attr("id");
    var isBatch = $(this).attr("isBatch");
    var status = updateTableStatus(table, id);
    if(status){
        // loadrequest_approval(table, title, department, office,employeeid);
        loadRequestApprovalSSP();
        if(!isBatch){
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully approved application!',
                showConfirmButton: true,
                timer: 1000
            })
        } 
    }
});

function updateTableStatus(table, id){
    var approverid = $("#approverid").val();
    var status = "";
    $.ajax({
        url: "<?= site_url('employee_/updateTableStatus') ?>",
        type:"POST",
        data: {table: GibberishAES.enc(table, toks), id:  GibberishAES.enc(id, toks), approverid: GibberishAES.enc(approverid , toks), toks:toks},
        async: false,
        success:function(response){
          status = response;
        }
    });
    return status;
}

$("#batchapproved_"+tablename).click(function(){
    var counter = 0;
    var idlist = '';
    $('#familyTable input[name="empCheck"]').each(function(){
        if($(this).prop("checked") == true){
            var trid = $(this).attr("trid");
            idlist += trid+'~';
            counter++;
        }
    });

    if(counter > 0){
        $.ajax({
            url: "<?= site_url('approval_/updateTableStatusBatch') ?>",
            type:"POST",
            data: {table: GibberishAES.enc($("#tablename").val(), toks), idlist:  GibberishAES.enc(idlist, toks), approverid: GibberishAES.enc(approverid , toks), toks:toks},
            async: false,
            success:function(response){
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: "Successfully approved "+counter+" pending applications!",
                  showConfirmButton: true,
                  timer: 1000
              });
              $("input[name='checkall']").click();
              loadRequestApprovalSSP();
            }
        });
    }else{
        Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "Select data to approve first..",
                showConfirmButton: true,
                timer: 1000
            })
    }
});

$("#batchdelete_"+tablename).click(function(){
    batchdelete  = 1;
    var counterDelete = 0;
    var idlist = '';
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
        $('#familyTable input[name="empCheck"]').each(function(){
            if($(this).prop("checked") == true){
                var trid = $(this).attr("trid");
                idlist += trid+'~';
                counterDelete++;
            }
        });
        if(counterDelete > 0){
            $.ajax({
                url: "<?= site_url('approval_/deleteTableStatusBatch') ?>",
                type:"POST",
                data: {table: GibberishAES.enc($("#tablename").val(), toks), idlist:  GibberishAES.enc(idlist, toks), toks:toks},
                async: false,
                success:function(response){
                  Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: "Successfully deleted "+counterDelete+" pending applications!",
                      showConfirmButton: true,
                      timer: 1000
                  })
                  $("input[name='checkall']").click();
                  loadRequestApprovalSSP();
                }
            });
        }else{
            Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: "Select data to delete first..",
                    showConfirmButton: true,
                    timer: 1000
                })
        }
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

$("#familyTable").delegate(".edit_pts_pdp1", "click", function(){
    addpts_pdp1($(this), $(this).attr("tbl_id"), $(this).attr("empname"), $(this).attr("employeeid"), $(this).attr("seminar_title"), $(this).attr("datef"), $(this).attr("datet"), $(this).attr("organizer"), $(this).attr("venue"), $(this).attr("location"), $(this).attr("regfee"), $(this).attr("transfee"), $(this).attr("accfee"), $(this).attr("total"), $(this).attr("mime"), $(this).attr("content"), $(this).attr("dra_remarks") );
});

$("#familyTable").delegate(".delete_family", "click", function(){
    if(batchdelete == 0){
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
            deleteData($(this).attr('table'), $(this).attr("tbl_id"), $(this).attr("employeeid"));
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
    }else{
        deleteData($(this).attr('table'), $(this).attr("tbl_id"), $(this).attr("employeeid"), 1);
    }
});

function addpts_pdp1(obj, tbl_id="", empname="", employeeid="", seminar_title="", datef="", datet="", organizer="", venue="", location="", regfee="", transfee="", accfee="", total="", mime="", content="", dra_remarks=""){
        $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Program of "+empname : "Add Program");
        $("#button_save_modal").text("Save").attr("id", "savebtn").removeClass().addClass("btn btn-success button_save_modal_pts_pdp1");   
        $.ajax({
            url: "<?=site_url('employee_/pts_pdp1')?>",
            type: "POST",
            success: function(msg){
                var modal_display = $("#modal-view").find("div[tag='display']");
                $.when($(modal_display).html(msg)).done(function(){ 
                   if(obj){
                     var tdcur = $(obj).parent().parent().parent();
                     $(tdcur).attr("iscurrent",1);
                     $(modal_display).find("select[name='sm_title']").val(seminar_title).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_semtitle']").val(seminar_title);
                     $(modal_display).find("input[name='sm_datef']").val(datef);
                     $(modal_display).find("input[name='sm_datet']").val(datet);
                     $(modal_display).find("input[name='sm_organizer']").val(organizer);
                     $(modal_display).find("select[name='sm_venue']").val(venue).trigger('chosen:updated');
                     $(modal_display).find("input[name='sm_location']").val(location);
                     $(modal_display).find("input[name='sm_registration']").val(regfee);
                     $(modal_display).find("input[name='sm_transportation']").val(transfee);
                     $(modal_display).find("input[name='sm_accommodation']").val(accfee);
                     $(modal_display).find("input[name='sm_total']").val(total);
                     $(modal_display).find("#file_uploaded").attr("file", content).attr("mime", mime);
                     $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                     $(modal_display).find("input[name='employeeid_']").val(employeeid);
                     $(modal_display).find("input[name='dra_remarks']").val(dra_remarks);
                 $(modal_display).find("#draremarks").css("display", "block");

                  }else{
                     $("#pts_pdp1infolist").find("tr").each(function(){
                       $(this).attr("iscurrent",0); 
                     }) 
                  }
                  $(".modalclose").click(function(){
                        $("#pts_pdp1infolist").find("tr").each(function(){
                        $(this).attr("iscurrent",0);
                });
            });
                });
            }
        });  
    }
</script>