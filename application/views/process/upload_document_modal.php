<style type="text/css">
    .swal2-cancel{
    margin-right: 20px;
}
</style>
<div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" id="submit">
        <div class="modal-content">
            <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                    <h5>D`Great</h5>
                </div>
            </div>
            <center><h4 class="modal-title">Upload File For Document Requests</h4></center>
            </div>
            <div class="modal-body" style="margin-top: 20px;">
                    <div class="form_row">
                        <div class="field_name  col-md-4">
                            <label class="align_right">Description</label>
                        </div>
                        <div class="fields col-md-8">
                            <input type="text" name="description" class=" form-control" required>
                        </div>
                    </div><br>
                    <div class="form_row">
                        <div class="field_name col-md-4">
                            <label class="align_right">File</label>
                        </div>
                        <div class="fields col-md-8">
                            <input type="file" name="upload" class="form-control" required>
                        </div>
                    </div><br>
                <br>
                <legend style="color: black;">
                    Uploaded Forms
                </legend>
                <table class="table-striped table-bordered table-hover datatable" style="width: 100%;">
                    <thead>
                        <tr style="background-color: #0072c6;font-weight: bold; color:black;">
                            <td class="align_center" style="font-weight: bold;">Actions</td>
                            <td class="align_center" style="font-weight: bold;">Filename</td>
                            <td class="align_center" style="font-weight: bold;">Description</td>
                        </tr>
                    </thead>
                    <tbody id="uploaded_table">
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
    uploadedForms();

    $('#submit').submit(function(e){
        e.preventDefault(); 
         $.ajax({
            url: $("#site_url").val() + "/documents_/insert",
            type:"post",
            data:new FormData(this),
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            success: function(response){
                if(response == "File uploaded successfully"){
                     Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: response,
                          showConfirmButton: true,
                          timer: 1000
                      })
                }else{
                        Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: response,
                          showConfirmButton: true,
                          timer: 1000
                      })
                }
                $("#upload_modal").modal('toggle');

           }
         });
    }); 

    $("table").delegate('.edit_forms','click', function(){
        var id = $(this).closest('tr').attr('id');
        var id = $(this).attr('id');
        $(".refresh_forms_"+id).show();
        $(".edit_forms_"+id).hide();
        $(".exist-id_"+id).html("<input type='text' class='form-control' id='file_id' value='"+id+"' disabled style='text-align:center;'>");
        $(".exist-desc_"+id).html("<input type='text' class='form-control' id='description'>");
    });

    $("table").delegate('.delete_forms', 'click', function(){
        var id = $(this).attr('id');
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            deleteDocumentRecord(id)
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

    $("table").delegate('input','change', function(){
        $("input[name='description']").val($(this).val());  /*current description*/

        var id = description = '';
        id = $("#file_id").val();
        description = $("#description").val();
    
        if(id && description){
            formdata = {
                id: id,
                description: description
            };
            setTimeout("saveFormData(formdata)", 1000);
        }else{
            Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: "Please fill-up required fields.",
              showConfirmButton: true,
              timer: 1000
          })
            return;
        }
    });

    $("table").delegate('.refresh_forms', 'click', function(){
        uploadedForms();
    });

    function saveFormData(formdata){
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + '/documents_/validateFormData',
            data: formdata,
            success:function(response){
                if(response){
                    Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: "Successfully saved",
                      showConfirmButton: true,
                      timer: 1000
                  })
                } 
                else{
                    Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: "Failed to saved.",
                          showConfirmButton: true,
                          timer: 1000
                      })
                } 
                uploadedForms();
            }
        });
    }

    function uploadedForms(){
        $.ajax({
            url: $("#site_url").val() + "/documents_/uploadedFormsList",
            success:function(response){
                $("#uploaded_table").html(response);
            } 
        })
    }

    function deleteDocumentRecord(id){
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + '/documents_/deleteUploadedForm',
            data: {id:id},
            success:function(response){
                if(response){
                    Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: "Successfully deleted form. ",
                      showConfirmButton: true,
                      timer: 1000
                  })
                } 
                else{
                    Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: "Failed to deleted form. .",
                          showConfirmButton: true,
                          timer: 1000
                      })
                } 
                uploadedForms();
            }
        });
    }
</script>