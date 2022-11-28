<?php
    $CI =& get_instance();
    $CI->load->model('applicantt');
    $dCounter = 0;

?>
<thead>
	<tr>
		<th class="align_center">Document Name</th>
		<th class="align_center">Upload Document</th>
		<?php if($this->session->userdata('usertype') != "ADMIN" && $this->session->userdata('usertype') != "EMPLOYEE"): ?>
			<th class="align_center">Action</th>
		<?php endif ?>
	</tr>
	</thead>
<tbody id="pre_requirements">
	<?php foreach($record as $row): ($row['content'] === NULL || $row['content']== '') ? $dCounter = $dCounter + 1: '' ?>
		<tr employeeid="<?= $row['id'] ?>" style="border-bottom: 1px solid #ddd !important">
		    <td class="align_center description"><?=$row['description']?></td>
		    <td class="align_center" ><a class="filename" file="<?= $row['content'] ?>" mime="<?= $row['mime'] ?>" style="cursor: pointer;"><?= $row['title']?></a><input class="myInputs num_<?= $row['id'] ?>" id="num_<?= $row['id'] ?>" num="<?= $row['id'] ?>" type="file" style="visibility:hidden" required="required"/></td>
		    <?php if($this->session->userdata('usertype') != "ADMIN" && $this->session->userdata('usertype') != "EMPLOYEE"): ?>
		    	<td class="align_center"><a class="btn btn-info upload_requirements"><span class="glyphicon glyphicon-cloud-upload"></span>&nbsp;Upload File</a>&nbsp;<a class="btn btn-danger delete_docs" doc_id="<?=$row['id']?>"><span class="glyphicon glyphicon-trash"></span>&nbsp;Remove File</a>&nbsp;<a class="btn btn-primary add_pre_req"  tag='add_pre_req' doc_id="<?=$row['id']?>" <?=($row['original'] == 1 ? '' : 'style="display:none;"')?>><i class="glyphicon glyphglyphicon glyphicon-plus"></i></a><?=($row['original'] == 1 ? '' : '&emsp;&emsp;&emsp;')?></td>
		    <?php endif ?>
		</tr>
	<?php endforeach ?>
		<tr>
			<td><input type="text" name="counter" class="counter" id="counter" value="<?= $dCounter ?>"  hidden></td>
		</tr>
</tbody>

<script>   
	var toks = hex_sha512(" ");
	// $("#upload_documents").unbind().click(function(){
	// 		var confirm = window.confirm("Are you sure you want to submit your application form?");

	// 		iscontinue = true;
	// 		$("#info, input[type=text], input[type=checkbox], select").each(function(){
	// 		    if(!$(this).val() && typeof $(this).attr("name") != "undefined" && $(this).attr("name") != "usertype" && $(this).attr("name") != "txthealth" && $(this).attr("name") != "txtoperation" && $(this).attr("name") != "txtoperationdate" && $(this).attr("name") != "txtmedhis"){
	// 		  		iscontinue = false;
	// 		      	$(this).css("border-color", "red");
	// 		      	console.log($(this).attr("name"));
	// 		    }
	// 		});

	// 		if(!iscontinue){
	// 			alert("Please Complete All Fields In All Step.");
	// 			return;
	// 		}

	// 		var emergency_count = $("input[name='emergency_count']").val();
	// 		var children_count = $("input[name='children_count']").val();
	// 		if(!emergency_count || !children_count){
	// 			iscontinue = false;
	// 		      	$(this).css("border-color", "red");
	// 		}

	// 		$.ajax({
	// 			url : "<?= site_url('applicant/checkApplicationForm') ?>",
	// 			type: "POST",
	// 			data: {applicantid:  GibberishAES.enc( $("input[name='applicantId']").val(), toks), toks:toks},
	// 			success:function(response){
	// 				if(!response) return;
	// 			}
	// 		});
			
	//         if(confirm){
	//             var applicantid = $("input[name='applicantId']").val();
	//             $.ajax({
	//                 url : "<?= site_url('applicant/submitFormApplication') ?>",
	//                 type: "POST",
	//                 data: {applicantid: GibberishAES.enc( applicantid, toks), toks:toks},
	//                 success:function(response){
	//                     if(response){
	//                         $("#doc_upload_result").modal("toggle");
	//                         $(".ok").removeAttr('disabled');
	//                     }else{
	//                         alert("Failed to submit your application");
	//                         return;
	//                     }
	//                 }
	//             });
	//         }
 //    });

	$(".filename").click(function(){
		var trid = $(this).closest("tr");
		if($(trid).find(".filename").attr("file")){
			var data = $(trid).find(".filename").attr("file");
			var mime = $(trid).find(".filename").attr("mime");
			var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
	    	window.open(objectURL);
	    }else{
			var file_url = $(this).attr("content");
			window.open(file_url);
		}
	});


	$(".delete_docs").unbind().click(function(){
		var trid = $(this).closest("tr");
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
		    $.ajax({
				url: "<?= site_url('applicant/removeApplicantDoc') ?>",
				type: "POST",
				data: {id: GibberishAES.enc($(this).attr("doc_id"), toks), applicantid: GibberishAES.enc( $('input[name=applicantId]').val() , toks), toks:toks},
				dataType: "json",
				success:function(response){
		        	if(response.status == "success"){
		        		Swal.fire({
						    icon: 'success',
						    title: 'Success!',
						    text: response.msg,
						    showConfirmButton: true,
						    timer: 1000
						});
		        		loadPreRequirements();
		        	}else{
		        		Swal.fire({
						    icon: 'warning',
						    title: 'Warning!',
						    text: response.msg,
						    showConfirmButton: true,
						    timer: 1000
						});
		        	}
				}
			});
		  } else if (
		    result.dismiss === Swal.DismissReason.cancel
		  ) {
		        swalWithBootstrapButtons.fire(
		            'Cancelled',
		            'Documents is safe.',
		            'error'
		        )
		    }
		})
			
	});

	$(".upload_requirements").click(function(){
		var trid = $(this).closest("tr");
		$(trid).find(".myInputs").click();
	});

	$(".myInputs").change(function(){
		var trid = $(this).closest("tr");
		var code_id = $(trid).find(".myInputs").attr("id");
        var sizes = $("."+code_id).prop("files")[0].size/1024/1024;
        if(sizes > 2){
    		$("#msg_header").removeClass("alert alert-danger");
		    $("#msg_header").addClass("alert alert-danger");
    		$("#msg_header").find("strong").text("Failed! ");
    		$("#msg_header").find("span").text("File size exceeds 2 MB. Please try another file.");
    		$("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
    		Swal.fire({
			    icon: 'warning',
			    title: 'Warning!',
			    text: "File size exceeds 2 MB. Please try another file.",
			    showConfirmButton: true,
			    timer: 1000
			})
    		return;
        }

		var formdata = document.getElementById(code_id);
		var uploadname = formdata.files.item(0).name;
		$(trid).find(".filename").text(uploadname).css("color", "blue");
		var file_url = URL.createObjectURL(event.target.files[0]);
		$(trid).find(".filename").attr("content", file_url);
		uploadApplicantFiles(trid);

	});

    function b64toBlob(b64Data, contentType) {
        var byteCharacters = atob(b64Data)
        var byteArrays = []
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            var slice = byteCharacters.slice(offset, offset + 512),
                byteNumbers = new Array(slice.length)
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i)
            }
            var byteArray = new Uint8Array(byteNumbers)

            byteArrays.push(byteArray)
        }

        var blob = new Blob(byteArrays, { type: contentType })
        return blob
    }

	function uploadApplicantFiles(trid){
	  var code_id = "";
	  var filedata = "";
	  var appid = $("#appid").val();
        var applicantid = '';

        if(appid == 's'){
            applicantid = $("input[name='applicantId']").val();
        }else{
            applicantid = appid;
        }
	  var formdata  = new FormData();
	  var upload_choices = true;
	  if (upload_choices){
	    formdata.append("applicantid",  GibberishAES.enc( applicantid, toks));
        code_id = $(trid).find(".myInputs").attr("num");

        file_data = $(".num_"+code_id).prop("files")[0]
        formdata.append("files", file_data);
        formdata.append("doc_id", GibberishAES.enc(code_id , toks));
        formdata.append("toks",toks );
	    $.ajax({
	        url: "<?= site_url("applicant/getApplicantDocuments") ?>",
	        type: "POST",
	        contentType: false,
	        processData: false,
	        data: formdata,
	        dataType: "json",
	        success:function(response){
	        	if(response.status == "success"){
	        		Swal.fire({
					    icon: 'success',
					    title: 'Success!',
					    text: response.msg,
					    showConfirmButton: true,
					    timer: 1000
					});
	        		loadPreRequirements();
	        	}else{
	        		Swal.fire({
					    icon: 'warning',
					    title: 'Warning!',
					    text: response.msg,
					    showConfirmButton: true,
					    timer: 1000
					});
	        	}
	        }
	    });

	  }
	}

	$(".add_pre_req").click(function(){
		$.ajax({
	        url: "<?= site_url("setup_/addMultipleRequirement") ?>",
	        type: "POST",
	        data: {req_id: GibberishAES.enc($(this).attr("doc_id"), toks), applicantid: GibberishAES.enc( $('input[name=applicantId]').val() , toks), ini_or_pre: GibberishAES.enc("pre", toks), toks:toks},
	        success:function(response){
	        	loadPreRequirements();
	        }
	    });

	})
</script>