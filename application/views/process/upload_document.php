<input type="text" name="msg" id="msg" value="Form has been uploaded successfully." class="form-control hidden" >
<div class="well-content col-md-12" style='border: transparent !important;'><br />
	<form method="POST" enctype="multipart/form-data" id="submit">
		<div class="form_row col-md-12" style="margin-bottom: 20px !important;width: 80%;margin-right: 20px;">
		    <div class="field_name  col-md-4 align_right" >
		        <label class="align_right">Description</label>
		    </div>
		    <div class="fields col-md-8">
		        <input type="text" name="description" class=" form-control" required>
		    </div>
		</div>
		<div class="form_row col-md-12" style="margin-bottom: 20px !important;width: 80%;margin-right: 20px;">
		    <div class="field_name col-md-4 align_right">
		        <label class="align_right">File</label>
		    </div>
		    <div class="fields col-md-6">
		        <input type="file" name="upload" class="form-control">
		    </div>
        <div class="fields col-md-1" style="padding-left: 0px; padding-right: 0px;">
          <a class="btn btn-danger" id="cancel_btn" style="float: right; display: none;">Cancel</a>
        </div>
        <div class="fields col-md-1" style="padding-left: 0px;">
            <button type="submit" class="btn btn-primary" id="upload_btn" tag="add" style="float: right;">Upload</button>
        </div>
        <div class="col-md-4">
            
          </div>
          <div class="col-md-6">
            <a style="color: blue;text-decoration: underline;display: none;" id="uploaded_link" href="" target="_blank">Click to view uploaded document.</a>
          </div>
        
		</div>

		<input type="text" name="id" value="" class="form-control hidden" >
	</form>
</div>
<script type="text/javascript">
  var toks = hex_sha512(" ");
	$('#submit').submit(function(e){
        if($("input[name='upload']").val() == " "){
          Swal.fire({
              icon: 'Warning',
              title: 'warning!',
              text: $("#msg").val(),
              showConfirmButton: true,
              timer: 1000
          });
          return;
        }
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
                          text: $("#msg").val(),
                          showConfirmButton: true,
                          timer: 1000
                      })
                     uploadedTable();
                     $("input[name='id']").val('');
                     $("input[name='upload']").val('');
                     $("input[name='description']").val('');
                     $("#cancel_btn").css("display", "none");
                     $("input[name='upload']").css("width", "100%");
                     $("#uploaded_link").attr("href", "");
                     $("#uploaded_link").hide();
                     $("input[name='msg']").val("Form has been uploaded successfully.");
                }else{
                        Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: response,
                          showConfirmButton: true,
                          timer: 1000
                      })
                }


           }
         });
    });

    $("#cancel_btn").click(function(){

      const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
          confirmButton: 'btn btn-success',
          cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
      });

      swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "Do you really want to cancel?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
      }).then((result) => {
      if (result.value) {
      $("#cancel_btn").css("display", "none");
      $("input[name='upload']").css("width", "100%");
        $("input[name='id']").val('');
        $("input[name='upload']").val('');
        $("input[name='description']").val('');
        $("input[name='id']").val('');
        $("#uploaded_link").attr("href", "");
        $("#uploaded_link").hide();
        $("#upload_btn").attr("tag", "add").text("Upload");
        $("input[name='msg']").val("Form has been saved successfully.");
        } else if (
          result.dismiss === Swal.DismissReason.cancel
        ) {
        swalWithBootstrapButtons.fire(
            'Cancelled',
            'Data is safe.',
            'error'
          )
        }
      });
    });
</script>