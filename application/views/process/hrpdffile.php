<?php

/**
 * @author Max Consul
 * @copyright 2019
 */

$curr_date = date('Y-m-d');
$tenDays = date('Y-m-d', strtotime('-14 days'));

?>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div style="float: right;">
                    <button class="btn btn-success listbtn" id="manage_document" style="margin-bottom: 5px;">Manage Documents</button>
                    <button class="btn btn-success listbtn" id="upload_documents" style="margin-bottom: 5px;">Upload Form</button>
                </div>
                <div class="panel animated fadeIn delay-1s list" id="list" style="margin-top: 3%">
                   <div class="panel-heading"><h4><b>Document Request</b></h4></div>
                   <div class="panel-body">
                        <div class="well-content col-md-12" style="cursor: pointer; margin-bottom: 40px;">    
                        <br>    
                            
                            <div class="form_row col-md-12">
                                <div class="field_name col-md-3 align_right">
                                    <label class="align_right">Status</label>
                                </div>           
                                <div class="fields no-search col-md-4"  style="padding: 0px; width: 33.9%">
                                    <select class="chosen" id="status">
                                          <option value="">All Category</option>
                                          <option value="pending">PENDING</option>
                                          <option value="process">ON PROCESS</option>
                                          <option value="approved">APPROVED</option>
                                          <option value="disapproved">DISAPPROVED</option>
                                    </select>
                                </div>
                            </div><br>
                            <div class="form_row col-md-12">
                                <div class="field_name col-md-3 align_right">
                                    <label class="align_right">Date From</label>
                                </div>  
                                <div class="col-md-2" style="padding: 0px;">
                                    <div class="input_field input-group date col-md-11">
                                        <input type='text' class="form-control dates" name="dfrom" id="dfrom" value="<?= $curr_date ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>         
                                <div class="col-md-1" style="padding: 0px; width: 2%">
                                    <label>To</label>
                                </div>  
                                <div class="col-md-2" style="padding: 0px;">
                                    <div class="input_field input-group date col-md-11">
                                        <input type='text' class="form-control dates" name="dto" id="dto" value="<?= $curr_date ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div><br>
                            <div class="form_row col-md-12">
                                <div class="field_name col-md-3 align_right">
                                    <label class="align_right">&nbsp;</label>
                                </div>           
                                <div class="fields no-search col-md-4" style="padding: 0px;">
                                    <a class="btn btn-primary" id="search_btn" style="margin-right: 15px;">Search</a>
                                    <a class="btn btn-primary" id="apply_doc">Request Document</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="panel animated fadeIn delay-1s list" id="lists">
                    <div class="panel-heading"><h4><b><span id="sorttitle">All Application List</span></b></h4></div>
                       <div class="panel-body">
                            <div class="well-content col-md-12" style="cursor: pointer; margin-bottom: 40px;">    
                            <br>     
                            <div class="container-fluid" id="docapp_details">             <!-- table data -->
                           
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-success" id='backlist' style="margin-bottom: 5px;display: none;">Back to Document Request</button>
                <div class="panel animated fadeIn result" hidden>
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Upload File for Document Request</b></h4></div>
                     <div class="panel-body">
                          <div id="uploadfile" style="padding: 5px;color:black;"></div>
                       </div>
                </div>

                <div class="panel animated fadeIn result" hidden>
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Uploaded Forms</b></h4></div>
                     <div class="panel-body">
                          <div id="uploadedforms" style="padding: 5px;color:black;">
                              <div class="well-content col-md-12">
                                <div class="col-md-12" id="date_range" style="margin: 20px;">
                                    <div class="col-md-1" style="margin-right: 5%;">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="col-md-6">
                                            <div class="field_name col-md-6 align_right">
                                                <label class="align_right" style="padding-top: 4%;">Date From</label>
                                            </div>           
                                            <div class="input_field input-group date col-md-6">
                                                <input type='text' class="form-control dates date_range" name="dfrom_uploaded" id="dfrom_uploaded" value="<?= $curr_date ?>"/>
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-5" style="padding: 0px;">
                                            <div class="field_name col-md-1" style="padding: 0px;">
                                                <label class="align_right" style="padding-top: 9%;">To</label>
                                            </div>       
                                            <div class="col-md-8">    
                                                <div class="input_field input-group date" style="padding: 0px;">
                                                    <input type='text' class="form-control dates date_range" name="dto_uploaded" id="dto_uploaded" value="<?= $curr_date ?>"/>
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1" style="padding: 0px;">
                                                <button class="btn btn-primary" id="search_uploaded">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                </div>
                                <div class="col-md-12" id="uploadedTable">
                                    
                                </div>
                              </div>
                          </div>
                       </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<!-- <div class="modal fade" id="upload_modal" role="dialog"> -->
<div class="modal fade" id="m_doc_modal" role="dialog"> <!-- Add Setup --> </div>
<div class="modal fade" id="app_modal" role="dialog"> <!-- Apply employee doc --> </div>
<div class="modal fade" id="process_modal" role="dialog"> <!-- Apply employee doc --> </div>

  
<script type="text/javascript">
    var toks = hex_sha512(" ");
    loadDocumentAppDetails("");
    uploadedTable();
    setTimeout(function(){
        $("#lists").removeClass("animated");
    }, 1200)

    $("#add_rec").unbind().click(function(){
        var markup = "<tr><td class='align_center'><span id='success'>SAVED</span><span id='failed'>NOT SAVED</span></td><td class='align_center'><input class='form-control' type='text' id='code'></td><td class='align_center'><input type='text' class='form-control' id='description'></td></tr>";
        $("#doc_rec #tbl_data").append(markup);
        $("#success, #failed").hide();
    });

    $("table").delegate('input','change', function(){
        var code = description = '';
        code = $("#code").val();
        description = $("#description").val();
    
        if(code && description){
            formdata = {
                code:  GibberishAES.enc(code , toks),
                description:  GibberishAES.enc(description , toks),
                toks:toks
            };
            setTimeout("saveDocumentData(formdata)", 500);
        }else{
            $("#failed").show();
            return;
        }
    });

    $("table").delegate('.edit_rec','click', function(){
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
        var alert = confirm("Are you sure you want to deletet this data?");
        if(alert == true) deleteDocumentRecord(code);
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

    // $("#upload_document").unbind().click(function(){
    //     $.ajax({
    //         url: $("#site_url").val() + '/documents_/loadUploadDocumentModal',
    //         success:function(response){
    //             $("#upload_modal").modal('toggle');
    //             $("#upload_modal").html(response);
    //         }
    //     })
    // });

    $("#upload_documents").click(function(){
        $(".list").hide();
        $(".listbtn").hide();
        $("#lists").addClass("animated");
        $("#uploadfile").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");
        $.ajax({
            url: $("#site_url").val() + '/documents_/loadUploadDocument',
            success:function(response){
                $("#backlist").show();
                $(".result").show();
                $("#uploadfile").html(response);
            }
        })
    });

    $("#backlist").click(function(){
        $(this).hide();
        $(".result").hide();
        $(".list").show();
        $(".listbtn").show();
        setTimeout(function(){
            $("#lists").removeClass("animated");
        },1200)
    });

    // $("#search_btn").unbind().click(function(){
    //     if(!$("input[name='dfrom']").val() || !$("input[name='dto']").val()){
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Warning!',
    //             text: 'All date is required!',
    //             showConfirmButton: true,
    //             timer: 1000
    //         });
    //         return;
    //     }

    //     var d1 = new Date($("input[name='dfrom']").val());
    //     var d2 = new Date($("input[name='dto']").val());
    //     if(d1 > d2){
    //         Swal.fire({
    //             icon: 'warning',
    //             title: 'Warning!',
    //             text: "Please fill-up a valid date.",
    //             showConfirmButton: true,
    //             timer: 2000
    //         })
    //         $(this).val("");
    //         return;
    //     }
    //     var formdata = {
    //         status:  GibberishAES.enc($("#status").val() , toks),
    //         datefrom: GibberishAES.enc( $("input[name='dfrom']").val() , toks),
    //         dto:  GibberishAES.enc($("input[name='dto']").val() , toks),
    //         toks:toks
    //     };

    //     $.ajax({
    //         url: $("#site_url").val() + '/documents_/loadApplyDocModalBySort',
    //         type: "POST",
    //         data: formdata,
    //         success:function(response){
    //             $("#docapp_details").html(response);
    //         }
    //     });

    // });

    $('#date_active, #dateresigned, #bdate, .datepos, .dateemployed, .date').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $(".chosen").chosen();

function loadTableDetails(){
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
                      text: 'Successfully saved data!',
                      showConfirmButton: true,
                      timer: 1000
                  })
            }
            else{
                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'Failed to save data!',
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
        data: {code: GibberishAES.enc( code, toks), toks:toks},
        success:function(response){
            if(response){
                 Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: 'Successfully deleted data!',
                      showConfirmButton: true,
                      timer: 1000
                  })
            }
            else{
                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'Failed to delete data!',
                      showConfirmButton: true,
                      timer: 1000
                  })
            }
            loadTableDetails();
        }
    });
}

$("#search_btn").on("click", function(){
    if(!$("input[name='dfrom']").val() || !$("input[name='dto']").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'All date is required!',
            showConfirmButton: true,
            timer: 1000
        });
        return;
    }

    var d1 = new Date($("input[name='dfrom']").val());
    var d2 = new Date($("input[name='dto']").val());
    if(d1 > d2){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please fill-up a valid date.",
            showConfirmButton: true,
            timer: 2000
        })
        $(this).val("");
        return;
    }
    var status = $("#status").val();
    var dfrom = $("#dfrom").val();
    var dto = $("#dto").val();
    loadDocumentAppDetails(status,dfrom,dto);
});


function loadDocumentAppDetails(status='',dfrom='',dto=''){
    $.ajax({
        url: $("#site_url").val() + '/documents_/loadApplicationList',
        type: "POST",
        data: {status: GibberishAES.enc( status, toks), dfrom: GibberishAES.enc(dfrom , toks), dto: GibberishAES.enc(dto , toks), toks:toks},
        success: function(response){
            $("#docapp_details").html(response);
            if(status == 'pending') $("#sorttitle").text("Pending Application List");
            else if(status == 'approved') $("#sorttitle").text("Approved Application List");
            else if(status == 'disapproved') $("#sorttitle").text("Disapproved Application List");
            else if(status == 'process') $("#sorttitle").text("On Process Application List");
            else $("#sorttitle").text("All Application List");
        }
    });
}

$(".date_range").on("change, blur", function(){
    if(!$("input[name='dfrom_uploaded']").val() || !$("input[name='dto_uploaded']").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'All date is required!',
            showConfirmButton: true,
            timer: 1000
        });
        return;
    }

    var d1 = new Date($("input[name='dfrom_uploaded']").val());
    var d2 = new Date($("input[name='dto_uploaded']").val());
    if(d1 > d2){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please fill-up a valid date.",
            showConfirmButton: true,
            timer: 2000
        })
        $(this).val("");
        return;
    }
    // uploadedTable($("#dfrom_uploaded").val(), $("#dto_uploaded").val())
})

$("#search_uploaded").click(function(){
    if(!$("input[name='dfrom_uploaded']").val() || !$("input[name='dto_uploaded']").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'All date is required!',
            showConfirmButton: true,
            timer: 1000
        });
        return;
    }

    var d1 = new Date($("input[name='dfrom_uploaded']").val());
    var d2 = new Date($("input[name='dto_uploaded']").val());
    if(d1 > d2){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please fill-up a valid date.",
            showConfirmButton: true,
            timer: 2000
        })
        $(this).val("");
        return;
    }
    uploadedTable($("#dfrom_uploaded").val(), $("#dto_uploaded").val())
});

function uploadedTable(datefrom='', dateto=''){
    $.ajax({
        url: $("#site_url").val() + "/documents_/uploadedFormsTable",
        type: "POST",
        data: {datefrom: GibberishAES.enc(datefrom , toks), dateto: GibberishAES.enc( dateto, toks), toks:toks},
        success:function(response){
            $("#uploadedTable").html(response);
        } 
    })
}
</script>