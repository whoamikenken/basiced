<?php $query = $this->employeemod->loadleaveCred(); ?>
<style>
    #setdate {
        table-layout: fixed;
        width: 100% !important;
    }
    #setdate td,
    #setdate th{
        width: auto !important;
        word-wrap: break-word;
    }
</style>
<a tag="add_appd" class="btn btn-primary" data-toggle="modal" data-target="#modal-view" style="margin-bottom: 5px;"><span><i class="glyphicon glyphicon-plus-sign"></i></span> Add New</a>
<div class="panel">
    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Leave</b></h4></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" id="setdate">
            <thead style="background-color: #0072c6;">
                <tr>
                    <th></th>
                    <th>Leave Type</th>
                    <th>Employment Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Date Created</th>
                    <th>Created by</th>
                </tr>
            </thead>
            <?if($query->num_rows() > 0){?>
            <tbody>
                <?foreach($query->result() as $data){?>
                <tr>
                    <td tag='deduct' class="align_center col-md-1">
                        <div class="btn-group">
                            <a class="btn btn-info" href="#modal-view" tag='edit_d' data-toggle="modal" code='<?=$data->id?>'><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?php echo $data->id ?>" from="<?php echo $data->dfrom ?>"  to="<?php echo $data->dto ?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
                          </div>
                    </td>
                    <td><?=$this->employeemod->othLeaveDesc($data->leavetype)?></td>
                    <td><?=Globals::_e($data->employmentStatus)?></td>
                    <td><?=date("F d, Y",strtotime($data->dfrom))?></td>
                    <td><?=date("F d, Y",strtotime($data->dto))?></td>
                    <td><?=date("F d, Y",strtotime($data->timestamp))?></td>
                    <td><?=$data->user?></td>
                </tr>
                <?}?>
            </tbody>
            <?}?>
        </table>
    </div>
</div>
<script>
    
$(document).ready(function(){
    var table = $('#setdate').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

function loaddata(){
    $.ajax({
        url: "<?=site_url("main/siteportion")?>",
        data: {view : "maintenance/leave_appdate"},
        type:"POST",
        success: function(msg){
            $("#tab2").html(msg);
            $(".grey,#button_save_modal").show();
            $("#leaveloading").hide();
        }
    });
}

$("a[tag='add_appd']").click(function(){
    var code = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Validity Date" : "Setup Validity Date ");
    $("#button_save_modal").text("Save");     
    var form_data = {
        code: code
    };
    $.ajax({
        url: "<?=site_url('maintenance_/manage_leave_date')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});

$("#setdate").on("click","a[tag='edit_d']", function(){
    var code = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Validity Date" : "Setup Validity Date ");
    $("#button_save_modal").text("Save");     
    var form_data = {
        code: code
    };
    $.ajax({
        url: "<?=site_url('maintenance_/manage_leave_date')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});

$("#setdate").on("click", ".delbtn", function(){
        var code = $(this).attr('code');
        var from = $(this).attr('from');
        var to = $(this).attr('to');

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

            swal.fire({
                html: '<h4>Processing.....</h4>',
                showConfirmButton: false,
                onRender: function() {
                    $('.swal2-content').prepend(sweet_loader);
                }
            });

            $.ajax({
                type: "POST",
                url: $("#site_url").val() +  "/maintenance_/deleteLeaveAppDate",
                data: {code: encodeURIComponent(GibberishAES.enc(code, toks)), from: encodeURIComponent(GibberishAES.enc(from, toks)), to: encodeURIComponent(GibberishAES.enc(to, toks)), toks:toks},
                dataType: "json",
                success:function(msg){
                    Swal.fire({
                        icon: msg.icon,
                        title: msg.msg,
                        text: msg.data,
                        showConfirmButton: true,
                        timer: 3000
                    })

                    if (msg.status) {
                        loaddata();
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

if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
else $(".btn").css("pointer-events", "");

</script>