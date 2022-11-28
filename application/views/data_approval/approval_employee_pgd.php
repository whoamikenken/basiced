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
                <th>Publication</th>
                <th>Title</th>
                <th>Publisher</th>
                <th>Type</th>
                <th>Date</th>
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
                    <td><?= $value['publication']?></td>
                    <td><?= $value['titles'] ?></td>
                    <td><?= $value['publisher']?></td>
                    <td><?= $value['type'] ?></td>
                    <td><?= $value['datef']?></td>
                    <td>
                        <a class="btn btn-danger update_status tr_<?= $value['id'] ?>"><?= $value['status']?></a>
                    </td>
                    <td>
                        <div style="float: right">
                            <a class='btn btn-primary edit_pgd' href='#modal-view' data-toggle='modal' tbl_id = "<?= $value['id'] ?>" empname = "<?= $value['lname'].', '.$value['fname'].' '.$value['mname'] ?>"  employeeid="<?=$value['employeeid'] ?>" publication="<?=$value['publication'] ?>" title="<?=$value['titles'] ?>" publisher="<?=$value['publisher'] ?>" type="<?=$value['type'] ?>" datef="<?=$value['datef'] ?>" mime="<?= $value['mime']?>" content="<?= $value['content']?>"><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
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
              $(row).attr('table', $("#requestedData").val()).attr('title', $("#requestedData option:selected").text()).attr("id", data[10]);
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

$("#familyTable").delegate(".edit_pgd", "click", function(){
    add_pgd($(this), $(this).attr("tbl_id"), $(this).attr("empname"), $(this).attr("employeeid"), $(this).attr("publication"), $(this).attr("title"), $(this).attr("publisher"), $(this).attr("type"), $(this).attr("datef"), $(this).attr("mime"), $(this).attr("content"), $(this).attr("dra_remarks") );
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

function add_pgd(obj, tbl_id="", empname="", employeeid="", publication="", title="", publisher="", type="", datef="", mime="", content="", dra_remarks=""){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Publication of "+empname : "Add Publication");
    $("#button_save_modal").text("Save").attr("id", "savebtn").removeClass().addClass("btn btn-success button_save_modal_pgd");    
    $.ajax({
        url: "<?=site_url('employee_/pgd')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("select[name='sm_publication']").val(publication).trigger('chosen:updated');
                 $(modal_display).find("input[name='sm_title']").val(title);
                 $(modal_display).find("input[name='sm_publisher']").val(publisher);
                 $(modal_display).find("input[name='sm_type']").val(type);
                 $(modal_display).find("input[name='sm_date']").val(datef);
                 $(modal_display).find("#file_uploaded").attr("file", content).attr("mime", mime);
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
                 $(modal_display).find("input[name='employeeid_']").val(employeeid);
                 $(modal_display).find("input[name='dra_remarks']").val(dra_remarks);
                 $(modal_display).find("#draremarks").css("display", "block");

              }else{
                 $("#pgdinfolist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
              $(".modalclose").click(function(){
                    $("#pgdinfolist").find("tr").each(function(){
                    $(this).attr("iscurrent",0);
            });
        });
            });
        }
    });  
}
</script>