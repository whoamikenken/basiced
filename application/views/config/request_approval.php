<style>
  .cbox{
     -ms-transform: scale(1.5); /* IE */
     -moz-transform: scale(1.5); /* FF */
     -webkit-transform: scale(1.5); /* Safari and Chrome */
     -o-transform: scale(1.5); /* Opera */
  }
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                    <div class="panel-heading"><h4><b>Data Request Approval</b></h4></div>
                    <br>
                    <div class="col-md-12" style="padding: 0px;">
                    	<div class="fieldTitle">
                    		<label class="col-md-2 align_right">Department:</label>
                    		<div class="field col-md-4">
	                    		<select class="form chosen-select" id="department" name="department"><?=$this->extras->getDeptpartment()?></select>
                    		</div>
                    	</div>
                    	<div class="fieldTitle" style="padding: 0px;">
                    		<label class="col-md-1 align_right" style="padding-left: 0px;">Category:</label>
                    		<div class="field col-md-4">
	                    		<select class="chosen" id="requestedData" name="requestedData">
	                    			<?php foreach (Globals::dataRequestApprovalList() as $key => $value): ?>
	                    				<option value="<?=$key?>"><?=$value?></option>
	                    			<?php endforeach; ?>
		                        </select>
                    		</div>
                    	</div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <div class="col-md-12" style="padding: 0px;">
                    	<div class="fieldTitle">
                    		<label class="col-md-2 align_right">Office:</label>
                    		<div class="field col-md-4">
	                    		<select class="form chosen-select" id="office" name="office"><?=$this->extras->getOffice()?></select>
                    		</div>
                    	</div>
                    	<div class="fieldTitle" style="padding: 0px;">
                    		<label class="col-md-1 align_right" style="padding-left: 0px;">Employee:</label>
                    		<div class="field col-md-4">
	                    		<select class="chosen-select col-md-4" name="employeeid" id="employeeid" multiple="">
                                    <option value="">All Employee</option>
                                    <?
                                    $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")));
                                    foreach($opt_type as $val){
                                        ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . " " . $val['fname'] . " " . $val['mname'])?></option><?    
                                    }
                                    ?>
                                </select>
                    		</div>
                    	</div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <div id="request_approval">
                        
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<?php $approver = $this->session->userdata("username"); ?>
<input type="hidden" id="approverid" value="<?= $approver ?>">
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="<?=base_url()?>jsbstrap/library/jquery.inputmask.bundle.js"></script>
<script>
	var toks = hex_sha512(" ");

	loadrequest_approval();
	function loadrequest_approval(tbl='', title='', department='', office ='', employeeid='') {
		if(!tbl) tbl = 'employee_awardsrecog';
		if(!title) title='Awards & Recognition';
		$.ajax({
	        url: "<?=site_url('approval_/loadRequestApproval')?>",
	        data: {tbl: GibberishAES.enc( tbl, toks), title: GibberishAES.enc( title, toks), department: GibberishAES.enc( department, toks), office: GibberishAES.enc( office, toks), employeeid: GibberishAES.enc( employeeid, toks), toks:toks},
	        type: "POST",
	        success:function(res){
	            $("#request_approval").html(res);
                updateNotifCount();
	        }
	    })
	}

	$("#requestedData").change(function(){
		$("#request_approval").html("Loading...");
		loadrequest_approval($("#requestedData").val(), $("#requestedData option:selected").text(), $("#department").val(), $("#office").val(), $("#employeeid").val());
	})

  $("#department, #office, #employeeid").change(function(){
    loadRequestApprovalSSP();
    // $.ajax({
    //     url : $("#site_url").val() + "/extensions_/dataRetreival",
    //     type: "POST",
    //     success: function(msg){
    //     }
    // });
  })

	$("#department").change(function(){
	    $.ajax({
	        url : $("#site_url").val() + "/setup_/getOffice",
	        type: "POST",
	        data: {department:GibberishAES.enc($(this).val(), toks), toks:toks},
	        success: function(msg){
	            $("#office").html(msg).trigger("chosen:updated");
	        }
	    });
	});

	$('#department, #office').on('change',function(){
	    var office = ($('#department').val() != '') ? GibberishAES.enc($('#office').val(), toks) : GibberishAES.enc('', toks) ;
	    var department = GibberishAES.enc($('#department').val(), toks);
	    $.ajax({
	        type : "POST",
	        url: $("#site_url").val() + "/employee_/load201sort",
	        data: {department:department, office:office,toks:toks},
	        success: function(data){
	            $("select[name='employeeid']").html(data).trigger("chosen:updated");
	        }
	    });
	});

	$(document).on('click','.button_save_modal_family', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_family";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var $validator = $("#form_family").validate({
	        rules: {
	                eb_name: {
	                required: true,
	                minlength: 2
	            }
	        }
	    });

	    if($("#form_family").valid()){
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
                    dra_remarks: GibberishAES.enc($("input[name='dra_remarks']").val()  , toks),
                    toks:toks
                },
                dataType: "json",
                async: false,
                success:function(response){
                    if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
            });
	    }
	})

	$(document).on('click','.button_save_modal_emergency_contact', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_emergencyContact";
	    var userid = $("#employeeid_").val();
	    var status = "";
	    

	    var $validator = $("#form_family").validate({
	        rules: {
	                eb_name: {
	                required: true,
	                minlength: 2
	            }
	        }
	    });

	    if($("#form_emergencyContact").valid()){
            $.ajax({
	            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
	            type: "POST",
	            data: {
	              table:  GibberishAES.enc(table , toks),
	              tbl_id: GibberishAES.enc( $("input[name='tbl_id']").val() , toks),
	              employeeid:  GibberishAES.enc(userid , toks),
	              name:  GibberishAES.enc( $("input[name='eb_name']").val(), toks),
	              relation:  GibberishAES.enc( $("select[name='eb_relation']").val(), toks),
	              mobile: GibberishAES.enc($("input[name='eb_mobile']").val()  , toks),
	              homeNo:  GibberishAES.enc($("input[name='eb_homeNo']").val() , toks),
	              officeNo:  GibberishAES.enc($("input[name='eb_officeNo']").val() , toks),
	              type:  GibberishAES.enc($("select[name='eb_type']").val() , toks),
                dra_remarks: GibberishAES.enc($("input[name='dra_remarks']").val()  , toks),
	              toks:toks
	            },
	            dataType: "json",
	            success:function(response){
	              if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
	            }
	         });
	    }
	})

	$(document).on('click','.button_save_modal_education', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_education";
	    var userid = $("#employeeid_").val();
	    var status = "";
	    var units = $("input[name='eb_units']").val();
		var isApplicant = $("#isApplicant").val();
		 if (isApplicant == 'yes') {
		  completed = $("#completedApplicant").val();
		  if (completed == 1) {
		    display = "Completed";
		  }else{
		    display = "On-Going";
		  }
		}else {
		  completed = ($("#complete").prop("checked") == true) ? 1 : 0 ;
		  display = units;
		}
	    if($("#completedApplicant").val() == 0){
              var $validator = $("#form_education").validate({
                    rules: {
                        eb_level: {
                          required: true
                        },
                        eb_school: {
                          required: true,
                          minlength: 2
                        },
                        eb_course: {
                          required: true
                        },
                        // eb_units: {
                        //   required: true
                        // },
                        eb_datefrom: {
                          required: true,
                          minlength: 2
                        },
                        eb_dateto: {
                          required: true,
                          minlength: 2
                        }
                    }
                });
            }else{
              var $validator = $("#form_education").validate({
                    rules: {
                        eb_level: {
                          required: true
                        },
                        eb_school: {
                          required: true,
                          minlength: 2
                        },
                        eb_course: {
                          required: true
                        },
                        // eb_units: {
                        //   required: true
                        // },
                        eb_dategraduated: {
                          required: true
                        },
                        eb_datefrom: {
                          required: true,
                          minlength: 2
                        },
                        eb_dateto: {
                          required: true,
                          minlength: 2
                        }
                    }
                });
            }

	    var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('schoolid',  GibberishAES.enc($("select[name='eb_school']").val() , toks));
            fd.append('educ_level',  GibberishAES.enc($("select[name='eb_level'] :selected").text(), toks));
            fd.append('course',  GibberishAES.enc($("input[name='eb_course']").val() , toks));
            fd.append('units',  GibberishAES.enc(units, toks));
            fd.append('date_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('year_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('completed',  GibberishAES.enc(completed, toks));
            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('schoolid',  GibberishAES.enc($("select[name='eb_school']").val() , toks));
            fd.append('educ_level',  GibberishAES.enc($("select[name='eb_level'] :selected").text(), toks));
            fd.append('course',  GibberishAES.enc($("input[name='eb_course']").val() , toks));
            fd.append('units',  GibberishAES.enc(units, toks));
            fd.append('date_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('year_graduated',  GibberishAES.enc($("input[name='eb_dategraduated']").val(), toks));
            fd.append('completed',  GibberishAES.enc(completed, toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);


        }

	    if($("#form_education").valid()){
            $.ajax({
	            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
	            type: "POST",
	            data: fd,
	            dataType: "json",
	            processData:false,
	            contentType:false,
	            success:function(response){
	              if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
	            }
	          });
	    }
	})

	$(document).on('click','.button_save_modal_eligibility', function(){
		var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_eligibilities";
	    var userid = $("#employeeid_").val();
	    var fd = new FormData();
        var $validator = $("#form_eligibilities").validate(
        {
            rules: 
            {
                el_issuedDate: 
                {
                required: true
                },
                el_expiryDate: 
                {
                required: true
                },
                el_description: 
                {
                // required: true
                }
            }
        });
	    if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

             fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('date_issued',  GibberishAES.enc($("input[name='el_issuedDate']").val() , toks));
            fd.append('date_expired',  GibberishAES.enc($("input[name='el_expiryDate']").val() , toks));
            fd.append('description',  GibberishAES.enc($("select[name='el_description']").val() , toks));
            fd.append('license_number',  GibberishAES.enc($("input[name='el_licenseNo']").val() , toks));
            fd.append('remarks',  GibberishAES.enc( $("input[name='el_remarks']").val(), toks));
            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);
        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('date_issued',  GibberishAES.enc($("input[name='el_issuedDate']").val() , toks));
            fd.append('date_expired',  GibberishAES.enc($("input[name='el_expiryDate']").val() , toks));
            fd.append('description',  GibberishAES.enc($("select[name='el_description']").val() , toks));
            fd.append('license_number',  GibberishAES.enc($("input[name='el_licenseNo']").val() , toks));
            fd.append('remarks',  GibberishAES.enc( $("input[name='el_remarks']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);
        }

        if($("#form_eligibilities").valid()){
    		$.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                success:function(response)
                {
                    if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
            });
        }
	})

	$(document).on('click','.button_save_modal_sctt', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_subj_competent_to_teach";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('subj_id',  GibberishAES.enc($("select[name='el_subj']").val(), toks));
            fd.append('remarks',  GibberishAES.enc($("input[name='el_remarks']").val() , toks));
            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));
            fd.append('subj_id',  GibberishAES.enc($("select[name='el_subj']").val(), toks));
            fd.append('remarks',  GibberishAES.enc($("input[name='el_remarks']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);
        }

        $.ajax({
            url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
            type: "POST",
            data: fd,
            dataType: "json",
            processData:false,
            contentType:false,
            success:function(response){
              if(response.status == "success"){
                    $("#modalclose, .modalclose").click();
                    Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: 'Successfully updated data!',
                      showConfirmButton: true,
                      timer: 1000
                  })
                    loadRequestApprovalSSP();
                    return false;
                }
            }
         });
	    
	})

	$(document).on('click','.button_save_modal_whr', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_work_history_related";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();
        var $validator = $("#form_workhistory").validate({
            rules: {
                eb_level: {
                  required: true
                },
                wh_position: {
                  required: true,
                  minlength: 2
                },
                wh_company: {
                  required: true,
                  minlength: 2
                },
    /*            wh_address: {
                  required: true,
                  minlength: 2
                },
                wh_contact: {
                  required: true,
                  minlength: 2
                },
                wh_datefrom: {
                  required: true,
                },*/
                // wh_remarks: {
                //   required: true,
                // },
                // wh_salary: {
                //   required: true,
                //   minlength: 2
                // },
                wh_reason: {
                  required: true,
                }
            }
        });
	   if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('position',  GibberishAES.enc($("input[name='wh_position']").val(), toks));
            fd.append('company',  GibberishAES.enc($("input[name='wh_company']").val() , toks));
            fd.append('salary',  GibberishAES.enc($("input[name='wh_salary']").val(), toks));
            fd.append('remarks',  GibberishAES.enc($("input[name='wh_remarks']").val() , toks));
            fd.append('reason',  GibberishAES.enc($("input[name='wh_reason']").val(), toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('position',  GibberishAES.enc($("input[name='wh_position']").val(), toks));
            fd.append('company',  GibberishAES.enc($("input[name='wh_company']").val() , toks));
            fd.append('salary',  GibberishAES.enc($("input[name='wh_salary']").val(), toks));
            fd.append('remarks',  GibberishAES.enc($("input[name='wh_remarks']").val() , toks));
            fd.append('reason',  GibberishAES.enc($("input[name='wh_reason']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }
        if($("#form_workhistory").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
            });
	    }
	})

	$(document).on('click','.button_save_modal_pts', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_pts";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if($("#sm_title").val() == 'others'){
             var $validator = $("#myForm").validate({
                  rules: {
                      sm_title: {
                        required: true
                      },
                      sm_date: {
                        required: true
                      },
                      sm_organizer: {
                        required: true
                      },
                      // sm_venue: {
                      //   required: true
                      // },
                      sm_location: {
                        required: true
                      },
                      sm_other_title: {
                        required: true
                      },
                  }
              });
         }else{
             var $validator = $("#myForm").validate({
                rules: {
                    sm_title: {
                      required: true
                    },
                    sm_date: {
                      required: true
                    },
                    sm_organizer: {
                      required: true
                    },
                    // sm_venue: {
                    //   required: true
                    // },
                    sm_location: {
                      required: true
                    }
                }
            });
         }
        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val(), toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('other_title',  GibberishAES.enc( $("input[name='sm_other_title']").val(), toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('other_title',  GibberishAES.enc( $("input[name='sm_other_title']").val(), toks));

            fd.append('toks', toks);
          }
        if($("#myForm").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
        }
	    
	})

	$(document).on('click','.button_save_modal_pts_pdp1', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_pts_pdp1";
	    var userid = $("#employeeid_").val();
	    var status = "";
        var $validator = $("#myForm").validate({
            rules: {
                sm_title: {
                  required: true
                },
                sm_datef: {
                  required: true
                },
                sm_datet: {
                  required: true
                },
                sm_organizer: {
                  required: true
                },
                // sm_venue: {
                //   required: true
                // },
                sm_semtitle: {
                  required: true
                },
                sm_location: {
                  required: true
                }
                // sm_registration: {
                //   required: true
                // },
                // sm_accommodation: {
                //   required: true
                // },
                // sm_transportation: {
                //   required: true
                // },
                // sm_total: {
                //   required: true
                // }
            }
        });
	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('venue',  GibberishAES.enc($("select[name='sm_venue']").val(), toks));
            fd.append('datet',  GibberishAES.enc($("input[name='sm_datet']").val(), toks));
            fd.append('seminar_title',  GibberishAES.enc($("input[name='sm_semtitle']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val() , toks));
            fd.append('regfee',  GibberishAES.enc($("input[name='sm_registration']").val(), toks));
            fd.append('transfee',  GibberishAES.enc($("input[name='sm_transportation']").val() , toks));
            fd.append('accfee',  GibberishAES.enc($("input[name='sm_accommodation']").val() , toks));
            fd.append('total',  GibberishAES.enc($("input[name='sm_total']").val(), toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('venue',  GibberishAES.enc($("select[name='sm_venue']").val(), toks));
            fd.append('datet',  GibberishAES.enc($("input[name='sm_datet']").val(), toks));
            fd.append('seminar_title',  GibberishAES.enc($("input[name='sm_semtitle']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val() , toks));
            fd.append('regfee',  GibberishAES.enc($("input[name='sm_registration']").val(), toks));
            fd.append('transfee',  GibberishAES.enc($("input[name='sm_transportation']").val() , toks));
            fd.append('accfee',  GibberishAES.enc($("input[name='sm_accommodation']").val() , toks));
            fd.append('total',  GibberishAES.enc($("input[name='sm_total']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }
        if($("#myForm").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
        }
	    
	})

	$(document).on('click','.button_save_modal_pts_pdp2', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_pts_pdp2";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();
        if($("#sm_title").val() == 'others'){
             var $validator = $("#myForm").validate({
                  rules: {
                      sm_title: {
                        required: true
                      },
                      sm_date: {
                        required: true
                      },
                      sm_organizer: {
                        required: true
                      },
                      // sm_venue: {
                      //   required: true
                      // },
                      sm_location: {
                        required: true
                      },
                      sm_other_title: {
                        required: true
                      },
                  }
              });
         }else{
             var $validator = $("#myForm").validate({
                rules: {
                    sm_title: {
                      required: true
                    },
                    sm_date: {
                      required: true
                    },
                    sm_organizer: {
                      required: true
                    },
                    // sm_venue: {
                    //   required: true
                    // },
                    sm_location: {
                      required: true
                    }
                }
            });
         }
        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('other_title',  GibberishAES.enc( $("input[name='sm_other_title']").val(), toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('location',  GibberishAES.enc($("input[name='sm_location']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('other_title',  GibberishAES.enc( $("input[name='sm_other_title']").val(), toks));

            fd.append('toks', toks);
          }
        if($("#myForm").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
        }
	})

	$(document).on('click','.button_save_modal_pts_pdp3', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_pts_pdp3";
	    var userid = $("#employeeid_").val();
	    var status = "";

	     var fileName = filename = file = mime = ''; 
        var fd = new FormData();
        if($("#sm_title").val() == 'others'){
             var $validator = $("#myForm").validate({
                  rules: {
                      sm_title: {
                        required: true
                      },
                      sm_date: {
                        required: true
                      },
                      sm_organizer: {
                        required: true
                      },
                      // sm_venue: {
                      //   required: true
                      // },
                      sm_location: {
                        required: true
                      },
                      sm_other_title: {
                        required: true
                      },
                  }
              });
         }else{
             var $validator = $("#myForm").validate({
                rules: {
                    sm_title: {
                      required: true
                    },
                    sm_date: {
                      required: true
                    },
                    sm_organizer: {
                      required: true
                    },
                    // sm_venue: {
                    //   required: true
                    // },
                    sm_location: {
                      required: true
                    }
                }
            });
         }
        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('other_title',  GibberishAES.enc( $("input[name='sm_other_title']").val(), toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('title',  GibberishAES.enc($("select[name='sm_title']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));
            fd.append('other_title',  GibberishAES.enc( $("input[name='sm_other_title']").val(), toks));

            fd.append('toks', toks);
          }
        if($("#myForm").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
        }
	})

	$(document).on('click','.button_save_modal_pgd', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_pgd";
	    var userid = $("#employeeid_").val();
	    var status = "";

	     var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('publication',  GibberishAES.enc($("select[name='sm_publication']").val(), toks));
            fd.append('publisher',  GibberishAES.enc($("input[name='sm_publisher']").val() , toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_date']").val() , toks));
            fd.append('title',  GibberishAES.enc($("input[name='sm_title']").val() , toks));
            fd.append('type',  GibberishAES.enc($("input[name='sm_type']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('publication',  GibberishAES.enc($("select[name='sm_publication']").val(), toks));
            fd.append('publisher',  GibberishAES.enc($("input[name='sm_publisher']").val() , toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_date']").val() , toks));
            fd.append('title',  GibberishAES.enc($("input[name='sm_title']").val() , toks));
            fd.append('type',  GibberishAES.enc($("input[name='sm_type']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }

          var $validator = $("#myForm").validate({
                rules: {
                    // sm_publication: {
                    //   required: true
                    // },
                    sm_title: {
                      required: true
                    },
                    sm_publisher: {
                      required: true
                    },
                    sm_type: {
                      required: true
                    },
                    sm_date: {
                      required: true
                    }
                }
            });
        if($("#myForm").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                dataType: "json",
                async: false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             }); 
        }
	    
	})

	$(document).on('click','.button_save_modal_ar', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_awardsrecog";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('award',  GibberishAES.enc($("select[name='ar_award']").val(), toks));
            fd.append('institution',  GibberishAES.enc($("input[name='ar_instituition']").val() , toks));
            fd.append('address',  GibberishAES.enc($("input[name='ar_address']").val() , toks));
            fd.append('datef',  GibberishAES.enc($("input[name='ar_datef']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('award',  GibberishAES.enc($("select[name='ar_award']").val(), toks));
            fd.append('institution',  GibberishAES.enc($("input[name='ar_instituition']").val() , toks));
            fd.append('address',  GibberishAES.enc($("input[name='ar_address']").val() , toks));
            fd.append('datef',  GibberishAES.enc($("input[name='ar_datef']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }

        var $validator = $("#myForm").validate({
            rules: {
                ar_award: {
                  required: true
                },
                ar_instituition: {
                  required: true
                },
                ar_address: {
                  required: true
                },
                ar_datef: {
                  required: true
                }
            }
        });
        if($("#myForm").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                dataType: "json",
                async: false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
        }
	    
	})

	$(document).on('click','.button_save_modal_sho', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_scholarship";
	    var userid = $("#employeeid_").val();
	    var status = "";

	      var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('type_of_scho',  GibberishAES.enc($("select[name='sm_type_of_scho']").val(), toks));
            fd.append('gr_agency',  GibberishAES.enc($("input[name='sm_gr_agency']").val() , toks));
            fd.append('prog_study',  GibberishAES.enc($("input[name='sm_prog_study']").val() , toks));
            fd.append('ins_scho',  GibberishAES.enc($("input[name='sm_ins_scho']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('dateto',  GibberishAES.enc($("input[name='sm_datef_to']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

             fd.append('type_of_scho',  GibberishAES.enc($("select[name='sm_type_of_scho']").val(), toks));
            fd.append('gr_agency',  GibberishAES.enc($("input[name='sm_gr_agency']").val() , toks));
            fd.append('prog_study',  GibberishAES.enc($("input[name='sm_prog_study']").val() , toks));
            fd.append('ins_scho',  GibberishAES.enc($("input[name='sm_ins_scho']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val() , toks));
            fd.append('dateto',  GibberishAES.enc($("input[name='sm_datef_to']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }

          var $validator = $("#myForm").validate({
                rules: {
                    // sm_type_of_scho: {
                    //   required: true
                    // },
                    sm_gr_agency: {
                      required: true
                    },
                    sm_prog_study: {
                      required: true
                    },
                    sm_ins_scho: {
                      required: true
                    }
                }
            });
        if($("#myForm").valid()){
            $.ajax({
                 url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                dataType: "json",
                async: false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
        }
	})

	$(document).on('click','.button_save_modal_resource', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_resource";
	    var userid = $("#employeeid_").val();
	    var status = "";

	     var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val(), toks));
            fd.append('topic',  GibberishAES.enc($("input[name='sm_topic']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('venue',  GibberishAES.enc($("input[name='sm_venue']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val(), toks));
            fd.append('topic',  GibberishAES.enc($("input[name='sm_topic']").val() , toks));
            fd.append('organizer',  GibberishAES.enc($("input[name='sm_organizer']").val() , toks));
            fd.append('venue',  GibberishAES.enc($("input[name='sm_venue']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }
        var $validator = $("#form_education").validate({
            rules: {
              sm_datef: {
                  required: true
                },
                
                sm_topic: {
                  required: true
                },
                sm_organizer: {
                  required: true,
                }
            }
        });
        if($("#form_education").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                dataType: "json",
                async: false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
	    }
	})

	$(document).on('click','.button_save_modal_org', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_proorg";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('name_org',  GibberishAES.enc($("input[name='sm_name_org']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_date']").val() , toks));
            fd.append('position',  GibberishAES.enc($("input[name='sm_position']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('name_org',  GibberishAES.enc($("input[name='sm_name_org']").val(), toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_date']").val() , toks));
            fd.append('position',  GibberishAES.enc($("input[name='sm_position']").val() , toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }
        var $validator = $("#form_education").validate({
            rules: {
                sm_name_org: {
                  required: true
                },
                sm_date: {
                  required: true
                },
                sm_position: {
                  required: true,
                }
            }
        });
        if($("#form_education").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                dataType: "json",
                async: false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             });
	    }
	})

	$(document).on('click','.button_save_modal_community', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_community";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();

	    if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('school',  GibberishAES.enc($("input[name='sm_school']").val() , toks));
            fd.append('year_grad',  GibberishAES.enc($("input[name='sm_year_grad']").val() , toks));
            fd.append('honor',  GibberishAES.enc($("input[name='sm_honor']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('school',  GibberishAES.enc($("input[name='sm_school']").val() , toks));
            fd.append('year_grad',  GibberishAES.enc($("input[name='sm_year_grad']").val() , toks));
            fd.append('honor',  GibberishAES.enc($("input[name='sm_honor']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }

        var $validator = $("#form_education").validate({
            rules: {
                sm_school: {
                  required: true
                },
                sm_educational_level: {
                  required: true
                },
                sm_year_grad: {
                  required: true
                },
                sm_honor: {
                  required: true
                },
                sm_ctype: {
                  required: true,
                }
            }
        });
        if($("#form_education").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                dataType: "json",
                async: false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
            }); 
        }
	})

	$(document).on('click','.button_save_modal_administrative', function(){
	    var toks = hex_sha512(" ");
	    var tbl_id = "";
	    var table = "employee_administrative";
	    var userid = $("#employeeid_").val();
	    var status = "";

	    var fileName = filename = file = mime = ''; 
        var fd = new FormData();

        if ($("#uploadFile").val() != "") {
             fileName = $("#uploadFile").val();
             file = $("#uploadFile")[0].files[0];
             mime = $("#uploadFile")[0].files[0].type;
             filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');

            fd.append('content',  GibberishAES.enc( file, toks));
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('positionf',  GibberishAES.enc($("input[name='sm_positionf']").val() , toks));
            fd.append('department',  GibberishAES.enc($("input[name='sm_department']").val() , toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('filename',  GibberishAES.enc(filename, toks));
            fd.append('mime',  GibberishAES.enc(mime, toks));
            fd.append('content',  GibberishAES.enc(base64String , toks));
            fd.append('toks', toks);

        }else{
            fd.append('table',  GibberishAES.enc( table, toks));
            fd.append('tbl_id', GibberishAES.enc( $("input[name='tbl_id']").val() , toks));
            fd.append('employeeid',  GibberishAES.enc( userid, toks));

            fd.append('positionf',  GibberishAES.enc($("input[name='sm_positionf']").val() , toks));
            fd.append('department',  GibberishAES.enc($("input[name='sm_department']").val() , toks));
            fd.append('datef',  GibberishAES.enc($("input[name='sm_datef']").val(), toks));
            fd.append('dra_remarks',  GibberishAES.enc($("input[name='dra_remarks']").val() , toks));

            fd.append('toks', toks);
          }

        var $validator = $("#form_education").validate({
            rules: {
                sm_position: {
                  required: true
                },
                sm_department: {
                  required: true
                },
                sm_datef: {
                  required: true,
                }
            }
        });
        if($("#form_education").valid()){
            $.ajax({
                url: "<?= site_url('applicant/saveApplicantFilledForm') ?>",
                type: "POST",
                data: fd,
                dataType: "json",
                processData:false,
                contentType:false,
                dataType: "json",
                async: false,
                success:function(response){
                  if(response.status == "success"){
                        $("#modalclose, .modalclose").click();
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully updated data!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                        loadRequestApprovalSSP();
                        return false;
                    }
                }
             }); 
        }
	})
	$(document).on('click','#modalclose, .modalclose', function(){
		$("#savebtn").text("Save").attr("id", "button_save_modal").removeClass().addClass("btn btn-success button_save_modal");
	})

	function deleteData(table='',tbl_id='',userid='', isBatch=0){
		$.ajax({
            url: "<?= site_url('employee_/deleteData') ?>",
            type: "POST",
            data: {table: GibberishAES.enc( table, toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc( userid, toks), toks:toks},
            dataType: "JSON",
            success: function(msg){ 
            	if(isBatch == 0){
            		Swal.fire({
		                  icon: 'success',
		                  title: 'Success!',
		                  text: 'Successfully deleted!',
		                  showConfirmButton: true,
		                  timer: 1000
	              	})
            	}
                // loadRequestApprovalSSP();
                loadRequestApprovalSSP();
                updateNotifCount();
            }
        });
	}

    
    function updateNotifCount(){
        $.ajax({
            url: "<?= site_url('approval_/countPendingRequest') ?>",
            type: "POST",
            success: function(msg){ 
                if(msg > 0){
                    $("li .active").find(".notifcount").text(msg);
                    $("title").text("Data Request Approval "+msg);
                }
                else{
                    $("li .active").find(".notifdiv").html('');
                    $("title").text("Data Request Approval");
                }
            }
        });
    }

    function loadFile(){
      // $("#familyTable tbody tr").each(function(){
      //     var id = $(this).attr("id");
      //     var table = $(this).attr("table");
      //     var employeeid = $(this).find("input[name='empCheck']").attr("employeeid");
      //     var content = $(this).find('td:last').find('a.btn.btn-primary').attr('content');
      //     var file = mime = '';
      //       $.ajax({
      //           type: "POST",
      //           url: "<?= site_url('approval_/loadFile')?>",
      //           data: {id:GibberishAES.enc( id, toks), table:GibberishAES.enc( table, toks), employeeid:GibberishAES.enc( employeeid, toks) , toks:toks},
      //           dataType: "JSON",
      //           success:function(res){
      //             $("a[tbl_id='"+id+"']").attr('content', res[1]).attr('mime', res[2]);
      //           }
      //       })
      // })
    }

	$(".chosen").chosen();
</script>