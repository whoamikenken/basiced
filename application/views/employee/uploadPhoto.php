<!-- <div class="col-md-12">
	<div class="uploadPhotoContent col-md-12">
		<div class="col-md-12">
			<input type="file" class="form-control" id="uploadedPhoto" accept=".jpg" >
			<span style="font-size: 12px;">Note: Only images with the <i>".jpg"</i>&nbsp;&nbsp;file extension are accepted.<span class="imageErr"></span></span>
		</div>
	</div>
</div>
<input type="hidden" id="fileName" value="<?= $fileName ?>">
<input type="hidden" id="applicant" value="<?= isset($applicant) ? $applicant : 0 ?>">
<script type="text/javascript">
	$(".button_save_modal").click(function(){
		$(".imageErr").text("").css("display", "unset");
		var forApplicant = $("#applicant").val();
		var url = '';
		var fd = new FormData();
		var fileName = $("#fileName").val();
		var file = $("#uploadedPhoto")[0].files[0];
		fd.append('file', file);
		fd.append('employeeid', fileName);
		var noUpload = $("#uploadedPhoto").val();
		if(forApplicant == 1){
			url = "<?= site_url('employee_/uploadingPhoto')?>";
		}else{
			url = $("#site_url").val() + "/employee_/uploadingPhoto";
		}
		if(!file){
			$(".imageErr").html("<br><i class='glyphicon glyphicon-remove'></i>&emsp;No File Chosen!").css("color", "red");
	        setTimeout(function(){
	            $(".imageErr").fadeOut("slow");
	        },3000);
		}else{
			$.ajax({
	            url : url,
	            type : 'POST',
	            data: fd,
	            dataType: "json",
	            processData:false,
	            contentType:false,
	            success : function(response){
	            	if(response.err_code == 1 || response.err_code == 3){
	            		$("#uploadedPhoto").val("");
	            		$(".imageErr").html("<br><i class='glyphicon glyphicon-ok'></i>&emsp;"+response.msg+"&emsp; Please wait..").css("color", "green");
	            		if(forApplicant == 1) loadUploadedPhoto(fileName);
	            		else  loadUploadedPhoto();
	            		
				        setTimeout(function(){
				            $(".imageErr").fadeOut("slow");
				            $(".modalclose").click();
				            // $(".active").click();
				            // if(forApplicant == 1){
				            // 	// $(".appPhoto").html();
				            // 	location.reload();
				            // }
				        },2000);
	            	}else{
	            		$("#uploadedPhoto").val("");
	            		$(".imageErr").html("<br><i class='glyphicon glyphicon-remove'></i>&emsp;"+response.msg).css("color", "red");
				        setTimeout(function(){
				            $(".imageErr").fadeOut("slow");
				        },2000);
	            	}
		        }
	       	});
		}
	});
</script> -->

<div class="col-md-12">
	<div class="uploadPhotoContent col-md-12">
		<div class="col-md-12">
			<input type="file" class="form-control" id="uploadedPhoto" accept=".jpg" >
			<span style="font-style: italic;font-size: 12px;color:#5c5a5a">Suggested Image Resolution: (200 x 200 px) not more than 500 kb only. <br> Suggested File Type: ".jpg" (jpeg) files only.  <span class="imageErr"></span></span>
		</div>
	</div>
</div>
<input type="hidden" id="fileName" value="<?php echo  $fileName ?>">
<input type="hidden" id="applicant" value="<?php echo  isset($applicant) ? $applicant : 0 ?>">
<script type="text/javascript">
	$(".button_save_modal").click(function(){
		$(".imageErr").text("").css("display", "unset");
		var forApplicant = $("#applicant").val();
		var url = '';
		var fd = new FormData();
		var fileName = $("#fileName").val();
		var file = $("#uploadedPhoto")[0].files[0];
		fd.append('file', file);
		fd.append('employeeid', fileName);
		var noUpload = $("#uploadedPhoto").val();
		if(forApplicant == 1){
			url = "<?php echo  site_url('employee_/uploadingPhoto')?>";
		}else{
			url = $("#site_url").val() + "/employee_/uploadingPhoto";
		}
		if(!file){
			$(".imageErr").html("<br><i class='glyphicon glyphicon-remove'></i>&emsp;No File Chosen!").css("color", "red");
	        setTimeout(function(){
	            $(".imageErr").fadeOut("slow");
	        },3000);
		}else{

			var filetype = $("#uploadedPhoto").prop("files")[0].type;
			var sizes = $("#uploadedPhoto").prop("files")[0].size/1024/1024;
		    if(sizes > 0.5){
		        Swal.fire({
		            icon: 'warning',
		            title: 'Warning!',
		            text: 'Image exceeds recommended file size. Please try another file.',
		            showConfirmButton: true,
		            timer: 3000
		        });
		        return;
		    }

		     if(filetype != "image/jpeg"){
		        Swal.fire({
		            icon: 'warning',
		            title: 'Warning!',
		            text: 'You can upload (.jpg) jpeg files only.',
		            showConfirmButton: true,
		            timer: 3000
		        });
		        return;
		    }

			$.ajax({
	            url : url,
	            type : 'POST',
	            data: fd,
	            dataType: "json",
	            processData:false,
	            contentType:false,
	            success : function(response){
	            	if(response.err_code == 1 || response.err_code == 3){
	            		$("#uploadedPhoto").val("");
	            		$(".imageErr").html("<br><i class='glyphicon glyphicon-ok'></i>&emsp;"+response.msg+"&emsp; Please wait..").css("color", "green");
				        setTimeout(function(){
				            $(".imageErr").fadeOut("slow");
				            $(".modalclose").click();
				            // $(".active").click();
				            if(forApplicant == 1){
				            	// $(".appPhoto").html();
				            	// location.reload();
				            	loadUploadedPhoto(fileName);
				            }else{
	            				loadUploadedPhoto();
				            }
				        },1000);
	            	}else{
	            		$("#uploadedPhoto").val("");
	            		$(".imageErr").html("<br><i class='glyphicon glyphicon-remove'></i>&emsp;"+response.msg).css("color", "red");
				        setTimeout(function(){
				            $(".imageErr").fadeOut("slow");
				        },2000);
	            	}
		        }
	       	});
		}
	});
</script>