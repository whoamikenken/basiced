var toks = hex_sha512(" ");
$("#doc_app").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});

$('#doc_app tbody').on('click', '.delete_application', function () {
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
            var id = $(this).attr('id');
            $.ajax({
              url: $("#site_url").val() + '/documents_/deleteApplyDoc',
              type: "POST",
              data: {id: GibberishAES.enc(id , toks), toks:toks},
              success:function(response){
                Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Document Request has been deleted successfully.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                loadDocumentAppDetails();
                updatedDocumentNotification();
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

$('#doc_app tbody').on('click', '.edit_application', function () {
    var id = $(this).attr('id');
    $.ajax({
    	url: $("#site_url").val() + '/documents_/processApplyDoc',
    	type: "POST",
    	data: {id: GibberishAES.enc(id , toks), toks:toks},
    	success:function(response){
    		$("#process_modal").modal('toggle').html(response);
    	}
    });
});

$("#mars").click(function(){
  if($(this).prop("checked") == true){
    var cboxCounter = false;
    $('input[name="mar"]').each(function(){
       if($(this).attr('isread') == 'no'){
       $(this).attr('checked', true).attr("disabled", true);
        cboxCounter = true;
        $.ajax({
           url      :   $("#site_url").val() + "/documents_/markAsReadApplication",
           type     :   "POST",
           data     :   {id : GibberishAES.enc($(this).attr("idkey") , toks) , val : GibberishAES.enc(1, toks), toks:toks},
           success  : function(response){
           }
        });
       }
    })
    setTimeout(function(){
      if(cboxCounter == true){
        location.reload();
      }
    }, 500)
  }
})

$('#doc_app tbody').on('click', 'input[name="mar"]', function () {
   var cval  = $(this).val();
   var idkey = $(this).attr("idkey");
   $(this).attr("disabled",true);
   $(this).closest("tr").removeAttr("style");
   $.ajax({
           url      :   $("#site_url").val() + "/documents_/markAsReadApplication",
           type     :   "POST",
           data     :   {id : GibberishAES.enc($(this).attr("idkey") , toks) , val : GibberishAES.enc(cval, toks), toks:toks},
           success  : function(response){
            if(response) location.reload();
            else alert("Failed to mark as read application.");
           }
        }); 
});

function updatedDocumentNotification(){
  $.ajax({
      url: $("#site_url").val() + '/documents_/updatedDocumentNotification',
      success:function(response){
        $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifcount").text(response);
      }
  });
}

// function loadDocumentAppDetails(status,dfrom,dto){
//     $.ajax({
//         url: $("#site_url").val() + '/documents_/loadApplicationList',
//         type: "POST",
//         data: {status:status, dfrom:dfrom, dto:dto},
//         success: function(response){
//             $("#docapp_details").html(response);
//         }
//     });
// }