<?
	$title = $hiring = $typeid = $query = $from = $to = $hiringtill = $filename = $isteaching = $course = $subject = $file = $comment = $documentFilename = $document = "";

	$empModel = new Employee();
	$types = $empModel->getPersonnelInfoConfigList('position_type');
	$arr_types = array();
	if(sizeof($types)>0){
	    foreach ($types as $row) {
	        $arr_types[$row->id] = $row->description;
	    }
	}

	if($id){
		$query  = $this->db->query("SELECT a.description, c.hiring, c.hiringtill, a.experience, c.filename, c.file, b.id AS typeid, b.`description` AS typedesc, a.isteaching, a.course, a.subject, c.document, c.documentFilename, a.comment
										FROM code_position a
										LEFT JOIN code_position_type b ON b.id=a.type 
										LEFT JOIN code_position_hiring c ON a.positionid = c.base_id 
										WHERE positionid='$id'")->result();
		// echo "<pre>";print_r($this->db->last_query());die;
		$title = $query[0]->description;
		$hiring = $query[0]->hiring;
		$hiringtill = $query[0]->hiringtill;
		$experience = $query[0]->experience;
		$isteaching = $query[0]->isteaching;
		$course = $query[0]->course;
		$subject = $query[0]->subject;
		$filename = $query[0]->filename;
		$file = $query[0]->file;
		$comment = $query[0]->comment;
		$documentFilename = $query[0]->documentFilename;
		$document = $query[0]->document;
		if ($experience) {
			$range = explode("-", $experience);
			$from = $range[0];
			$to = $range[1];
		}
		$typeid = $query[0]->typeid;
	}

	else { $id = "";}
	$action = ($query) ? "edit" : "add";
?>


<form id="info_form">
	<input type="hidden" name="mh_typeid" id="mh_typeid" value="1">
	<input type="hidden" name="id" id="id" value="<?=$id;?>" />
	<div class="form-group">
      <label class="col-sm-3 align_right">Job Title</label>
      <div class="col-sm-9">
      	<input class="form-control required" id="mh_title" name="title" type="text" value="<?=$title?>"/>
      </div>
    </div><br><br>
	<div class="form-group" style="margin-top: 2%;">
      <label class="col-sm-3 align_right"></label>
      <div class="col-sm-9">
      	<span style="margin-left:2%"><input type="checkbox" id="mh_hiring" name="hiring" style='transform:scale(2)' <?=($hiring)?"checked":""?> value="YES"> <label style="margin-left:2%">Hiring<label></span>&nbsp;&nbsp;&nbsp;&nbsp;
<!--       	<span style="margin-left:2%"><input type="checkbox" id="isteaching" name="isteaching" style='transform:scale(2)' <?=($isteaching)?"checked":""?> value="YES"> <label style="margin-left:2%">Is Teaching?<label></span> -->
      </div>
    </div><br><br>
    <div class="form-group">
      <label class="col-sm-3 align_right">Teaching Type</label>
      <div class="col-sm-9">
      	<select class="chosen form-control" id="isteaching" name="isteaching">
      		<option value="all" <?=($id) ? "selected" : "" ?> > Select type </option>
	      	<option value="YES" <?=($isteaching == "YES") ? "selected" : "" ?> >Teaching</option>
	      	<option value="" <?=($isteaching == "" && $id) ? "selected" : ""?> >Non-Teaching</option>
      	</select>
      </div>
    </div>
    <div class="form-group teachingopt" style="display: none;">
      <br><br>
      <label class="col-sm-3 align_right">Department</label>
      <div class="col-sm-9">
      	<select class="chosen form-control" id="course" name="course">
      		<option value="">Select Department</option>
     		<?php foreach($course_list as $courses): ?>
	      		<!-- <option value="<?=$courses['CourseCode']?>" <?=($courses['CourseCode'] == $course) ? "selected" : "" ?> ><?=strtoupper($courses['Description'])?></option> -->
	      		<option value="<?=$courses['code']?>" <?=($courses['code'] == $course) ? "selected" : "" ?> ><?=strtoupper($courses['description'])?></option>
	      	<?php endforeach ?>
      	</select>
      </div>
    </div>
    <div class="form-group teachingopt" style="display: none;">
      <br><br>
      <label class="col-sm-3 align_right">Subject</label>
      <div class="col-sm-9">
      	<select class="chosen form-control" id="subject" name="subject">
      		<option value="">Select Subject</option>
      		<?php foreach($subject_list as $subjects): ?>
	      		<option value="<?=$subjects['id']?>" <?=($subjects['id'] == $subject) ? "selected" : "" ?> ><?=$subjects['description']?></option>
	      	<?php endforeach ?>
      	</select>
      </div>
    </div><br><br>
	<div class="form-group" id="Expform" style="margin-bottom: 9%;">
      <label class="col-sm-3 align_right">Experience</label>
      <div class="col-sm-9" style="padding-left: 0px;">
      	<div class="col-md-6">
      		<input class="form-control" id="from" name="from" type="number" placeholder="From" value="<?= $from ?>" />
      	</div>
      	<div class="col-md-6">
      		<input class="form-control" id="to" name="to" type="number" placeholder="To" value="<?=$to ?>" />
      	</div>
     </div>
    </div>
	<div class="form-group" id="Expform" style="margin-bottom: 17%;">
	     <label class="col-sm-3 align_right">Upload Banner</label>
	     <div class="col-sm-9">
	      	<input name="file" id="file" class="form-control" accept="image/jpeg,image/x-png" type="file" style="height:35px;"/>
	      	<?php 
		   	if ($filename) { ?>
		   		<a type="button" class="viewInfo" file="<?= $file?>" filename="<?= $filename?>" style="font-weight: 700;color: blue;text-decoration: underline;margin: 1%">Click to view uploaded image.</a>
		    <?php  } ?>
		    <span style="font-style: italic;font-size: 12px;opacity: 0.8;">Note: Only images with the "Jpeg" / "Png"  file extension are accepted.</span>
	     </div>
    </div>
    <div class="form-group" id="Expform" style="margin-bottom: 17%;">
	     <label class="col-sm-3 align_right">Upload Details</label>
	     <div class="col-sm-9">
	      	<input name="document" id="document" class="form-control" accept="application/pdf" type="file" style="height:35px;"/>
	      	<?php 
		   	if ($document) { ?>
		   		<a type="button" class="viewInfo" file="<?= $document?>" filename="<?= $documentFilename?>" style="font-weight: 700;color: blue;text-decoration: underline;margin: 1%">Click to view uploaded file.</a>
		    <?php  } ?>
            <span style="font-style: italic;font-size: 12px;opacity: 0.8;">Note: Only images with the "PDF" file extension are accepted.</span>
	     </div>
    </div>
	<div class="form-group" id="Hiringform" >
      <label class="col-sm-3 align_right" style="margin-top:13px">Hiring Until</label>
      <div class="col-sm-9" style="margin-top:13px">
            <div class='input-group date' id='datetimepicker1'>
                <input type='text' class="form-control" id="tillHire" name="tillHire" value="<?= $hiringtill ?>" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
      </div>
      <br><br>
    </div>
<!--     <div class="form-group">
      <label class="col-sm-3 align_right">Type</label>
      <div class="col-sm-9">
      	<select class="chosen form-control" id="mh_typeid" name="typeid">
				<?
					foreach ($arr_types as $key => $value) { ?>
						<option value="<?=$key?>" <?=$key==$typeid?' selected':'';?>><?=$value?></option>
					<?}
				?>
		</select>
      </div>
    </div> -->
<!--     <div class="form-group">
      <label class="col-sm-3 align_right">Description</label>
      <div class="col-sm-9">
      	<a class="add" href="#" style='color:green'><i class="glyphicon glyphicon-plus-sign"></i> Add</a>
      </div>
    </div> -->
    <div class="form-group" >
      <label class="col-sm-3 align_right" style="margin-top:13px">Description</label>
      <div class="col-sm-9" style="margin-top:13px">
      	<textarea class="form-control" rows="2" name="comment" id="comment"><?=$comment?></textarea>
      </div>
    </div>
    <!-- <br><br>
    <div class="form-group">
      <div class="col-md-12">
      <table id="tbl" style="width:100%;border-collapse: separate;border-spacing: 0px 20px;">
				<?if($id){
					$que = $this->db->query("SELECT description FROM code_position_description WHERE positionid = '$id'")->result();
					foreach($que as $row)
					{
						?>
						<tr>
							<td width="100%">
								<div class="col-md-12"><textarea class="form-control" rows="2" id="comment"><?=$row->description?></textarea></div>
							</td>
							<td class="align_center">
								<div class="col-md-3"><a class="btn btn-danger del" href="#"><i class="glyphicon glyphicon-trash"></i></a></div>
							</td>
						</tr>
						<?
					}
				}?>
			</table>
      </div>
    </div> -->
	<div class="form_row">
		<div class="field">
			<span id="errmsg"></span>
		</div>
	</div>

</form>

<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
	getTeachingOption();
	if (!$('#mh_hiring').is(':checked')) {
		$('#Expform, #Hiringform').hide();
	}
	$(".chosen").chosen();
});

function getTeachingOption(){
	if($("#isteaching").val() == "YES") $(".teachingopt").show();
	else $(".teachingopt").hide();
}

$("#isteaching").change(function(){
	getTeachingOption();
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$('#mh_hiring').change(function(){
	if ($(this).is(':checked')) {
		$('#Expform, #Hiringform').show();
	}else $('#Expform, #Hiringform').hide();
})

	$(".add").click(function(){
        var tbl = $("#tbl");
		$(tbl).append('<tr><td width="100%"><div class="col-md-12"><textarea class="form-control" rows="2" id="comment"></textarea></div></td><td class="align_center"><div class="col-md-3"><a class="btn btn-danger del" href="#"><i class="glyphicon glyphicon-trash"></i></a></div></td></tr>');
		
		$(".del").click(function(){
			var tbl = $("#tbl");
			$(this).closest('<div class="form-group">').remove();
		});
	});
	
	$(".del").click(function(){
		var tbl = $("#tbl");
		$(this).closest('tr').remove();
    });
	
	$("#button_save_modal").unbind("click").click(function(){
		var description = $("#comment").val();
		if($("#isteaching").val() == "all"){
			Swal.fire({
			    icon: 'warning',
			    title: 'Warning!',
			    text: 'Please select a teaching type.',
			    showConfirmButton: true,
			    timer: 1000
			});
			return;
		}

		// if($( "#tbl" ).find("textarea").length != 0)
		// {
		// 	$( "#tbl" ).find("textarea").each(function() {
		// 		if($( this ).val() != "")
		// 		{
		// 			if(description){ description += "<=>"; }
		// 			description += $( this ).val();
		// 		}
		// 		else
		// 		{
		// 			$(this).closest('tr').remove();
		// 		}
		// 	});
		// }
		
		// $("#errmsg").html("<h6>This may take a while, please wait...</h6>");
		var form_data = $('#info_form').serialize();
		
		 var $validator = $("#info_form").validate({
            rules: {
                title: {
                  required: true,
                  minlength: 2
                }
            }
        });
		if($("#info_form").valid()){   
			var title = $("#mh_title").val();
			var hiring = "";
			var experience = "";
			var till = "";
			var isteaching = "";
			if($('#isteaching').val() == "YES"){
				isteaching = $("#isteaching").val();
			}
			if($('#mh_hiring').is(":checked"))
			{
				hiring = $("#mh_hiring").val();
				from = $('#from').val();
				to = $('#to').val();
				//if (from == "" || to == "") { alert("Please Complete Experience Input"); return;}
				experience = from+"-"+to;
				till = $('#tillHire').val();
			}
			var id = $("#id").val();
			var type = $('#mh_typeid').val();

			var course = $("#course").val();
			var subject = $('#subject').val();

			var file = $('#file')[0].files[0];
			var doc = $('#document')[0].files[0];
			// return;
			// if (!typeof file === 'undefined') {
			// 	var fileType = file["type"];
			// 	var validImageTypes = ["image/jpeg"];
			// 	if ($.inArray(fileType, validImageTypes) < 0) {
			// 	    alert("Please upload only a jpg file.");
			// 	    return;
			// 	}
			// }
			var fileName = document.getElementById("file").value;
	        var idxDot = fileName.lastIndexOf(".") + 1;
	        var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
	        // alert(extFile);
	        if (fileName){
	            if (extFile!="jpg" && extFile!="jpeg" && extFile!="png") {
	            	if($('#mh_hiring').is(':checked')){
		            Swal.fire({
					    icon: 'warning',
					    title: 'Warning!',
					    text: 'Please upload a file needed.',
					    showConfirmButton: true,
					    timer: 1000
					});
		            return;
			        }
	            }
	            
	        }else{
	        } 
			//text: 'Please upload a file.',


	        var docName = document.getElementById("document").value;
	        var idxDot = docName.lastIndexOf(".") + 1;
	        var extFile = docName.substr(idxDot, docName.length).toLowerCase();
	        if (docName){
    	        if (extFile!="pdf"){
    	            if($('#mh_hiring').is(':checked')){
    		            Swal.fire({
    					    icon: 'warning',
    					    title: 'Warning!',
    					    text: 'Please upload a file.',
    					    showConfirmButton: true,
    					    timer: 1000
    					})
    		            return;
    		        }
    	        }else{
    	        	
    	        } 
	        }

			// if (!typeof doc === 'undefined') {
			// 	var fileTypeDoc = doc["type"];
			// 	var validFileTypes = ["application/pdf"];
			// 	if ($.inArray(fileTypeDoc, validFileTypes) < 0) {
			// 	    alert("Please upload a pdf file.");
			// 	    return;
			// 	}
			// }

			var fd = new FormData();
			fd.append('file', file);
			fd.append('doc', doc);
			fd.append('id', GibberishAES.enc($("input[name='id']").val(), toks));
			fd.append('info_type', GibberishAES.enc('<?=$info_type;?>', toks));
			fd.append('title', GibberishAES.enc(title, toks));
			fd.append('hiring', GibberishAES.enc(hiring, toks));
			fd.append('experience', GibberishAES.enc(experience, toks));
			fd.append('id', GibberishAES.enc(id, toks));
			fd.append('action', GibberishAES.enc('<?=$action;?>', toks));
			fd.append('desc', GibberishAES.enc(description, toks));
			fd.append('type', GibberishAES.enc(type, toks));
			fd.append('till', GibberishAES.enc(till, toks));
			fd.append('isteaching', GibberishAES.enc(isteaching, toks));
			fd.append('course', GibberishAES.enc(course, toks));
			fd.append('subject', GibberishAES.enc(subject, toks));
			fd.append('toks', toks);

            $.ajax({
                url:"<?=site_url("configuration_/saveFormPosition")?>",
                type:"POST",
                data:fd,
                dataType:"JSON",
                processData: false,  // tell jQuery not to process the data
       			contentType: false,  // tell jQuery not to set contentType
                success: function(msg){
                	var action = '';
                	if ('<?=$action;?>' == "edit") action = "updated successfully.";
                	else action = "saved successfully.";
                    if(msg.err_code == 0) {
                    	Swal.fire({
	                        icon: 'success',
	                        title: 'Success!',
	                        text: 'Position has been '+action,
	                        showConfirmButton: true,
	                        timer: 1000
	                  	})
	                  setTimeout(function() {
	                    location.reload();
	                  }, 1500); 
	              	}else if(msg.err_code == 2){
	              		Swal.fire({
	                        icon: 'warning',
	                        title: 'Warning!',
	                        text: 'Job Title already exist.',
	                        showConfirmButton: true,
	                        timer: 1000
	                  })
	              	}
                }
            });

		}else {
			$("#errmsg").html("");
			$validator.focusInvalid();
			return false;
		}
	});


$(".viewInfo").click(function(){
    var data = $(this).attr("file");
    if(!data){
        alert("No details available.");
        return;
    }
    var filename = $(this).attr("filename");
    if(!filename){
        alert("No details available.");
        return;
    }
    var cutname = filename.split(".");

    if (cutname[1] == "jpg") {
        objectURL = URL.createObjectURL(b64toBlob(data, "image/"+cutname[1]));
    }else objectURL = URL.createObjectURL(b64toBlob(data, 'application/pdf')) + '#toolbar=0&navpanes=0&scrollbar=0';

    window.open(objectURL);
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
  });
</script>