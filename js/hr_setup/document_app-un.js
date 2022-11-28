var toks = hex_sha512(" ");
if($("select[name='update_stat']").val() == "APPROVED") $("#dateto_claim").show();
else  $("#dateto_claim").hide();

$("#apply_emp").click(function(){
    var formdata = "";  
    $('#doc_form input, #doc_form select, #doc_form textarea').each(function(){
        if(formdata) formdata += '&'+$(this).attr('name')+'='+ GibberishAES.enc( $(this).val(), toks);
        else formdata = $(this).attr('name')+'='+ GibberishAES.enc($(this).val() , toks);
    })
    formdata += '&toks='+ GibberishAES.enc( $(this).val(), toks);
    var checkbox_array = {};
    var count = 0;
    $("input:checkbox[name=documents]:checked").each(function(){
        count++;
        checkbox_array[count] = GibberishAES.enc($(this).val(), toks);
    });
    if(count == 0) checkbox_array =  GibberishAES.enc($("#documents").val(), toks);
    var formdata = {
        toks:toks,
        employee: GibberishAES.enc($("#employee").val(), toks),
        documents: checkbox_array,
        date_req: GibberishAES.enc($("input[name='date_req']").val(), toks),
        purpose: GibberishAES.enc($("#purpose").val(), toks)
    }
    $.ajax({
        url: $("#site_url").val() + '/documents_/validateDocApplication',
        type: 'POST',
        data: formdata,
        success:function(response){
            if(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Document Request has been saved successfully.',
                    showConfirmButton: true,
                    timer: 1000
                })
                // loadDocumentAppDetails("","","");
                location.reload();
                $("#app_modal").modal('toggle');
            }else{
                Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: 'Unable to save document request.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                return;
            }
        }
    })
});

$("#update_stat").change(function(){
    if($(this).val() == "APPROVED") $("#dateto_claim").show();
    else  $("#dateto_claim").hide();
});

$("#process_app").click(function(){
    var formdata = "";  
    $('#process_form input, #process_form select, #process_form textarea').each(function(){
        if(formdata) formdata += '&'+$(this).attr('name')+'='+ GibberishAES.enc( $(this).val(), toks);
        else formdata = $(this).attr('name')+'='+ GibberishAES.enc($(this).val() , toks);
    })
    formdata += '&toks='+toks;
    var encodedData = encodeURIComponent(window.btoa(formdata));
    $.ajax({
        url: $("#site_url").val() + '/documents_/validateProcessApplication',
        type: "POST",
        data: {formdata:encodedData},
        success:function(response){
             Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'Document Request has been updated successfully.',
                  showConfirmButton: true,
                  timer: 1000
              })
            $("#process_modal").modal('toggle');
            // loadDocumentAppDetails();
            location.reload();
        }
    });
});

 $('#date_active, #dateresigned, #bdate, .datepos, .dateemployed, .date').datetimepicker({
    format: 'YYYY-MM-DD'
});
$(".chosen").chosen();


function loadDocumentAppDetails(){
    $.ajax({
        url: $("#site_url").val() + '/documents_/loadApplicationList',
        success: function(response){
            $("#docapp_details").html(response);
        }
    });
}