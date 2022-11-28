    var toks = hex_sha512(" ");
    loadDocumentAppDetails();
    var msg = "Document has been saved successfully.";
    $("#add_rec").unbind().click(function(){
        var markup = "<tr><td class='align_center'><span id='success'>SAVED</span><span id='failed'>NOT SAVED</span></td><td class='align_center'><input class='form-control' type='text' id='code'></td><td class='align_center'><input type='text' class='form-control' id='description'></td></tr>";
        $("#doc_rec #tbl_data").append(markup);
        $("#success, #failed").hide();
    });

    $("table").delegate("#code, #description",'change, blur', function(){
        var code = description = '';
        code = $("#code").val();
        description = $("#description").val();
    
        if(code && description){
            formdata = {
                code:  GibberishAES.enc(code, toks),
                description:  GibberishAES.enc(description, toks),
                toks:toks
            };
            setTimeout("saveDocumentData(formdata)", 1000);
        }else{
            $("#failed").show();
            return;
        }
    });

    $("table").delegate('.edit_rec','click', function(){
        msg = "Document has been updated successfully.";
        var id = $(this).closest('tr').attr('id');
        var code = $(this).attr('code');
        $("#" + id).find(".refresh_rec").show();
        $("#" + id).find(".edit_rec").hide();
        $("#" + id).find(".exist-code").html("<input type='text' class='form-control' id='code' value='"+code+"' disabled style='text-align:center;'>");
        $("#" + id).find(".exist-desc").html("<input type='text' class='form-control' id='description'>");
        $(".refresh_rec_"+id).show();
        $(".edit_rec_"+id).hide();
        $(".exist-code_"+id).html("<input type='text' class='form-control' id='code' value='"+code+"' disabled style='text-align:center;'>");
        $(".exist-desc_"+ id).html("<input type='text' class='form-control' id='description'>");

    });

    $("table").delegate('.refresh_rec', 'click', function(){
        loadTableDetails();
    });

    $("table").delegate('.delete_rec', 'click', function(){
        var code = $(this).attr('code');
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
            deleteDocumentRecord(code);
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

    $("#manage_document").unbind().click(function(){
        $.ajax({
            url: $("#site_url").val() + '/documents_/loadModalSetup',
            success:function(response){
                $("#m_doc_modal").modal('toggle');
                $("#m_doc_modal").html(response);
                loadTableDetails();
            }
        });
    });

    $("#apply_doc").unbind().click(function(){
        $.ajax({
            url: $("#site_url").val() + '/documents_/loadApplyDocModal',
            success:function(response){
                $("#app_modal").modal('toggle');
                $("#app_modal").html(response);
            }
        });
    });

    $("#upload_document").unbind().click(function(){
        $.ajax({
            url: $("#site_url").val() + '/documents_/loadUploadDocumentModal',
            success:function(response){
                $("#upload_modal").modal('toggle');
                $("#upload_modal").html(response);
            }
        })
    });

    $("#search").unbind().click(function(){
        var formdata = {
            status:  GibberishAES.enc($("#status").val() , toks),
            datefrom:  GibberishAES.enc( $("input[name='dfrom']").val(), toks),
            dto:  GibberishAES.enc($("input[name='dto']").val() , toks),
            toks:toks
        };

        $.ajax({
            url: $("#site_url").val() + '/documents_/loadApplyDocModalBySort',
            type: "POST",
            data: formdata,
            success:function(response){
                $("#docapp_details").html(response);
            }
        });

    });

    $('#date_active, #dateresigned, #bdate, .datepos, .dateemployed, .date').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $(".chosen").chosen();

function loadTableDetails(){
    msg = "Document has been saved successfully.";
    $.ajax({
        url: $("#site_url").val() + '/documents_/loadAvailableDocuments',
        success:function(body){
            $("#tbl_data").html(body);
        }
    });
}

function saveDocumentData(formdata){
    $.ajax({
        type: "POST",
        url: $("#site_url").val() + '/documents_/validateDocumentData',
        data: formdata,
        success:function(response){
            if(response){
                Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: msg,
                      showConfirmButton: true,
                      timer: 1000
                  })
            }
            else{
                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: "Unable to save document.",
                      showConfirmButton: true,
                      timer: 1000
                  })
            }
            $("#success").show();
            $("#failed").hide();
            loadTableDetails();
        }
    });
}

function deleteDocumentRecord(code){
    $.ajax({
        type: "POST",
        url: $("#site_url").val() + '/documents_/readyDocumentData',
        data: {code: GibberishAES.enc(code , toks), toks:toks},
        success:function(response){
            if(response){
                Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: "Document has been deleted successfully.",
                  showConfirmButton: true,
                  timer: 1000
              })
            }
            else{
                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: "Unable to delete document.",
                      showConfirmButton: true,
                      timer: 1000
                  })
            }
            loadTableDetails();
        }
    });
}

$("#dfrom, #dto").on("change, blur", function(){
    var status = $("#status").val();
    var dfrom = $("#dfrom").val();
    var dto = $("#dto").val();
    loadDocumentAppDetails(status,dfrom,dto);
});

$("#status").change(function(){
    var status = $(this).val();
    var dfrom = $("#dfrom").val();
    var dto = $("#dto").val();
    loadDocumentAppDetails(status,dfrom,dto);
});

function loadDocumentAppDetails(status='ALL',dfrom='',dto=''){
    $.ajax({
        url: $("#site_url").val() + '/documents_/loadApplicationList',
        type: "POST",
        data: {status: GibberishAES.enc(status, toks), dfrom: GibberishAES.enc(dfrom, toks), dto: GibberishAES.enc(dto, toks),toks:toks},
        success: function(response){
            $("#docapp_details").html(response);
        }
    });
}