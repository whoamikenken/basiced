<?php

/**
 * @date 6-25-2014
 * @time 17:6
 */
$hide ="";
$usertype = $this->session->userdata("usertype");
if ($usertype) {
  $hide = 'style="display:none"';
}
?>
<style type="text/css">
  .swal2-cancel{
   margin-right: 20px;
 }
</style>
<div class="inner_content" width="100%">
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="panel" style="margin-top: 37px;">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Holiday Type</b></h4></div>
                   <div class="panel-body" width="100%">
                    <table id="user_datatable" class="table table-striped table-bordered table-hover" width="100%">
                        <thead width="100%">
                          <tr>
                            <th colspan="4"><a id="addschedule" class="btn btn-primary" href="#dtr-modal" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a></th>
                          </tr> 
                          <tr style="background-color: #0072c6;">
                            <th class="align_center col-md-2">Actions</th>
                            <th class="sorting_asc">Code</th>
                            <th class="sorting_asc">Description</th>
                            <!-- <th class="sorting_asc" >Rate</th> -->
                          <!--   <th class="sorting_asc"  <?=$hide?> >With Pay</th> -->
                          </tr>
                        </thead>   
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>   
</div> 
<script>
var table;
var toks = hex_sha512(" "); 
$(function(){

    table = $('#user_datatable').DataTable({
        "sAjaxSource": "<?=site_url("maintenance_/holidaytypelist")?>",
        "sServerMethod": "POST",
        "fnDrawCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            // codes here 
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#user_datatable").find(".btn").css("pointer-events", "none");
            else $("#user_datatable").find(".btn").css("pointer-events", "");
            $("a[tag='edit_d']").click(function(){
               var holiday_type = $(this).attr("holiday_type");
               dotoggleuserinfo("Edit Holiday Type",{job:GibberishAES.enc("edit", toks),holiday_type:GibberishAES.enc(holiday_type, toks), toks:toks});
            });
            
            $("a[tag='delete_d']").click(function(){
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
                    var holiday_type = $(this).attr("holiday_type");
                     $.ajax({
                         url:"<?=site_url("maintenance_/saveholidaytype")?>",
                         data: {holiday_type:GibberishAES.enc(holiday_type, toks),job:GibberishAES.enc("delete", toks), toks:toks},
                         type: "POST",
                         success: function(msg){
                          Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: msg,
                              showConfirmButton: true,
                              timer: 1000
                          })
                          location.reload();
                          table.fnDraw();

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
        }
    });
    new $.fn.dataTable.FixedHeader(table);

    $('.no-search .dataTables_length select').chosen();
    
    $("#addschedule").click(function(){  
       dotoggleuserinfo("New Holiday Type",{job:GibberishAES.enc("new", toks), toks:toks});
    });
    function dotoggleuserinfo(title,data){
       $("#dtr-modal").find("h3[tag='title']").html(title); 
       $("#dtr-modal").find("div[tag='display']").html("Loading, please wait...");
       //$("#dtr-modal").addClass("container");
       $('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
       $("#save-dtr-setup").text("Save");
       $.ajax({
           url:"<?=site_url("maintenance_/addnewholidaytype")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#dtr-modal").find("div[tag='display']").html(msg);

           }
       }); 
    }
});
</script>