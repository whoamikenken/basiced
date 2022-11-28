<?php $eb_relation = $this->extras->listRelation(); ?>
<input type="hidden" name="tbl_id">
<input type="hidden" name="employeeid_" id="employeeid_">
<input type="hidden" name="isApplicant" id="isApplicant" value="<?= $applicant ?>">
<form id="form_family" autocomplete="off">
    <div class="col-md-12">
        <div class="col-md-12">
            <div class="form-group" style="padding-bottom: 25px;">
                <div class="col-md-12">
                    <label class="col-sm-3">Name:</label>
                    <div class="col-sm-9">
                        <input type="text" name="eb_name" class="form-control required upperCase" value=""/>
                    </div> 
                </div>
            </div>
            <div class="form-group" style="padding-bottom: 25px;">
                <div class="col-md-12">
                    <label class="col-sm-3">Relation:</label>
                    <div class="col-sm-9">
                        <select class="form-control chosen" name="eb_relation" id="eb_relation" required>
                            <?php foreach($eb_relation as $er => $rstat):?>
                                <option <?=($er==$eb_relation ? " selected" : "")?> value="<?= $er ?>"><?= $rstat ?></option>
                            <?php endforeach;?>
                        </select>  
                    </div>
                </div>
            </div>
            <div class="form-group" style="padding-bottom: 25px;">
                <div class="col-md-12">
                    <label class="col-sm-3">Date of Birth:</label>
                    <div class="col-sm-9">
                        <div class='input-group eb_dob' id="eb_dob">
                            <input class="form-control col-md-12" type="text" name="eb_dob">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div> 
                </div>
            </div>
            <div class="form-group" id="draremarks" style="padding-bottom: 25px; display: none;">
                <div class="col-md-12">
                    <label class="col-sm-3">Remarks:</label>
                    <div class="col-sm-9">
                        <input type="text" name="dra_remarks" class="form-control upperCase" value=""/>
                    </div> 
                </div>
            </div>
             <div class="form-group data_request_details_requested_date" style="display: none;">
            <label class="col-sm-4 control-label">Date Requested</label>
            <div class="col-sm-7">
                <label class="control-label" name="requested_date"></label>
            </div>
        </div>
        </div>
        <div id="msg_header" style="display:none;">
            <strong></strong> <span></span>
        </div>
    </div>
</form>
<script>

   setTimeout(function(){
      getDataRequestDetails();
  }, 500);

  function getDataRequestDetails(){
    $.ajax({
         url: "<?=site_url("applicant/getDataRequestDetails")?>",
         data : {table:'employee_family', employeeid: $("#employeeid_").val(), baseid: $("input[name='tbl_id']").val()},
         dataType: 'JSON',
         type : "POST",
         success:function(msg){
            if(msg.err_code == 1){
                $("label[name='requested_date']").text(msg.request_date);
                $(".data_request_details_requested_date").css("display", "block");
            }
         }
      }); 
}
  $(".chosen").chosen();
    $(".button_save_modal").unbind("click").click(function(){
        var toks = hex_sha512(" ");
        var tbl_id = "";
        var table = "";
        var userid = "";
        var status = "";
        var isApplicant = $("#isApplicant").val();
        if("<?= $this->session->userdata('usertype') ?>" == "ADMIN") status = "APPROVED";
        else status = "PENDING";

        if($("input[name='applicantId']").val()){
            table = "applicant_family";
            userid = $("input[name='applicantId']").val();
        }
        else{
            table = "employee_family"; 
            userid = $("input[name='employeeid']").val();
        }

        var $validator = $("#form_family").validate({
            rules: {
                    eb_name: {
                    required: true,
                    minlength: 2
                }
            }
        });

        if($("#form_family").valid()){
            var cobj = "";
            $("#familylist").find("tbody tr").each(function(){
                if($(this).attr("iscurrent")==1) cobj = $(this);
            });
                       
            if(cobj){
                $(cobj).find("td:eq(0)").text($("input[name='eb_name']").val());
                $(cobj).find("td:eq(1)").text($("#eb_relation option:selected").text());
                $(cobj).find("td:eq(1)").attr("reldata", $("select[name='eb_relation']").val());  
                $(cobj).find("td:eq(2)").text($("input[name='eb_dob']").val());
                if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(cobj).find("td:eq(4)").text($("input[name='dra_remarks']").val()); 

                /*save/update data first*/
                $.ajax({
                    url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                    type: "POST",
                    data: {
                        table:  GibberishAES.enc( table, toks),
                        tbl_id: GibberishAES.enc( $("input[name='tbl_id']").val() , toks),
                        employeeid:  GibberishAES.enc(userid , toks),
                        name:  GibberishAES.enc($("input[name='eb_name']").val() , toks),
                        relation:  GibberishAES.enc( $("select[name='eb_relation']").val(), toks),
                        bdate: GibberishAES.enc($("input[name='eb_dob']").val()  , toks),
                        status:  GibberishAES.enc(status , toks),
                        dra_remarks: GibberishAES.enc($("input[name='dra_remarks']").val()  , toks),
                        toks:toks
                    },
                    dataType: "json",
                    // async: false,
                    success:function(response){
                        if(response.status == "success"){
                            tbl_id = response.tbl_id;
                            $(".modalclose").click();
                            Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: 'Successfully updated data.',
                              showConfirmButton: true,
                              timer: 1000
                          })
                            if(table == "employee_family"){
                              loadTable('employee_family_table');
                            }
                            return false;
                        }else{
                            $("#msg_header").addClass("alert alert-danger");
                            $("#msg_header").find("strong").text("Failed! ");
                            $("#msg_header").find("span").text(response.msg);
                            $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                            return;
                        }
                    }
                });

            }else{       

                /*save/update data first*/
                $.ajax({
                    url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                    type: "POST",
                    data: {
                        table:  GibberishAES.enc( table, toks),
                        tbl_id: GibberishAES.enc( $("input[name='tbl_id']").val() , toks),
                        employeeid:  GibberishAES.enc(userid , toks),
                        name:  GibberishAES.enc($("input[name='eb_name']").val() , toks),
                        relation:  GibberishAES.enc( $("select[name='eb_relation']").val(), toks),
                        bdate: GibberishAES.enc($("input[name='eb_dob']").val()  , toks),
                        status:  GibberishAES.enc(status , toks),
                        dra_remarks: GibberishAES.enc($("input[name='dra_remarks']").val()  , toks),
                        toks:toks
                    },
                    dataType: "json",
                    // async: false,
                    success:function(response){
                        if(response.status == "success"){
                            tbl_id = response.tbl_id;
                            $(".modalclose").click();
                            // loadSuccessModal();
                            Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: 'Successfully added data.',
                              showConfirmButton: true,
                              timer: 1000
                          })

                            if(table == "employee_family"){
                              loadTable('employee_family_table');
                            }
                            return false;
                        }else{
                            $("#msg_header").addClass("alert alert-danger");
                            $("#msg_header").find("strong").text("Failed! ");
                            $("#msg_header").find("span").text(response.msg);
                            $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                            return;
                        }
                    }
                });

                var mtable = $("#familylist").find("tbody");
                if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
                var ntr = $("<tr></tr>");
                if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN" || "<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") $(ntr).append("<td>"+$("input[name='eb_name']").val()+"</td>");
                else $(ntr).append("<td style='text-transform: uppercase'>"+$("input[name='eb_name']").val()+"</td>");
                $(ntr).append("<td reldata="+$("select[name='eb_relation']").val()+">"+$("#eb_relation option:selected").text()+"</td>");
                $(ntr).append("<td>"+$("input[name='eb_dob']").val()+"</td>");
                if(isApplicant != 'yes'){
                     if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td><a class='btn btn-success' style='pointer-events:none;'>"+status+"</td>");
                    else if ("<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") $(ntr).append("<td><a class='btn btn-danger' style='pointer-events:none;'>"+status+"</td>");
                    else $(ntr).append("<td><a>"+status+"</a></td>");
                }
                if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN") $(ntr).append("<td>"+$("input[name='dra_remarks']").val()+"</td>");
                else $(ntr).append("<td></td>");

                var mtd = $("<td></td>");
                if ("<?= $this->session->userdata("usertype") ?>" == "ADMIN" || "<?= $this->session->userdata("usertype") ?>" == "EMPLOYEE") {
                   
                    $("<div style='float:right'><a class='btn btn-warning delete_entry' tbl_id='"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
                        if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
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
                            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
                             $(this).parent().parent().remove();
                             deletefamily($(this), tbl_id);
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
                    }).appendTo($(mtd));
                     $("<div style='float:right'><a class='btn btn-primary add_family' href='#modal-view' data-toggle='modal' tbl_id='"+tbl_id+"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
                        addfamily($(this), tbl_id);
                    }).appendTo($(mtd));
                }else{
                    
                     $("<div style='float:right'><a class='btn btn-warning delete_family' tbl_id='"+tbl_id+"'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
                        if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
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
                            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
                             $(this).parent().parent().remove();
                            delete_family($(this), tbl_id); 
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
                     }).appendTo($(mtd));
                     $("<div style='float:right'><a class='btn btn-info edit_family' href='#infoModal' data-toggle='modal' tbl_id='"+tbl_id+"' style='margin-right: 10px;'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
                      addfamily($(this), tbl_id);
                     }).appendTo($(mtd));
                }
                $(ntr).append($(mtd));
                $(ntr).appendTo($("#familylist").find("tbody")); 
            }
        }else{
            $validator.focusInvalid();
            return false;
        }

    });


    $(".eb_dob").datetimepicker({
        format: "YYYY-MM-DD"
    });

    function formatDate(date) {
        var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [month, day, year].join('/');
    }

</script>