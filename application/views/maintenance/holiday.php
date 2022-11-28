<?php

/**
 * @date 6-25-2014
 * @time 17:6
 */

?>
<div class="inner_content" width="100%">
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="panel" style="margin-top: 37px">
                 <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Holiday Names</b></h4></div>
                   <div class="panel-body" width="100%">
                       <table class="table table-striped table-bordered table-hover" id="holiday_name" width="100%">
                        <thead width="100%">
                          <tr>
                            <th colspan="5"><a id="addh" class="btn btn-primary" href="#dtr-modal" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a></th>
                          </tr> 
                          <tr style="background-color: #0072c6;">
                            <th class="align_center col-md-1"></th>
                            <th class="sorting_asc">Code</th>
                            <th class="sorting_asc">Description</th>
                            <th class="sorting_asc">Holiday Type</th>
                            <th class="sorting_asc">Active</th>
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
var toks = hex_sha512(" "); 
$(function(){
    var table = $('#holiday_name').dataTable({
            // "bProcessing": true,
            // "bServerSide": true,
            "sAjaxSource": "<?=site_url("maintenance_/holidaylist")?>",
            "sServerMethod": "POST",
            "fnDrawCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                // codes here 
                if("<?=$this->session->userdata('canwrite')?>" == 0) $("#holiday_name").find(".btn").css("pointer-events", "none");
                else $("#holiday_name").find(".btn").css("pointer-events", "");
                $("a[tag='edit_d']").one('click', function() {
                  // console.log("wew");
                   var holiday_id = $(this).attr("holiday_id");
                   dotoggleuserinfo("Edit Holiday Name",{job: GibberishAES.enc("edit", toks),holiday_id: GibberishAES.enc(holiday_id, toks), toks:toks});
                });
                
                $("a[tag='delete_d']").one('click', function() {
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
                          var holiday_id = $(this).attr("holiday_id");
                           $.ajax({
                               url:"<?=site_url("maintenance_/deleteholiday")?>",
                               data: {holiday_id:GibberishAES.enc(holiday_id, toks), toks:toks},
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
    
    $("#addh").click(function(){  
       dotoggleuserinfo("New Holiday Name",{job:GibberishAES.enc("new", toks), toks:toks});
    });
    function dotoggleuserinfo(title,data){
		//alert(data);
       $("#dtr-modal").find("h3[tag='title']").html(title); 
       $("#dtr-modal").find("div[tag='display']").html("Loading, please wait...");
       //$("#dtr-modal").addClass("container");
       $('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
       $(".save-dtr-setup").text("Save");   
       $.ajax({
           url:"<?=site_url("maintenance_/manage_holidays")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#dtr-modal").find("div[tag='display']").html(msg);
           }
         });
    }

    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");

    $("#dtr-modal").on("hidden", function () {
      // table.ajax.reload();
      $('#holiday_name').DataTable().ajax.reload();
    });
});
</script>