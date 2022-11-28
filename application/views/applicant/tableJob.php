<style type="text/css">
    .col-sm-12{
        padding: 0px;
    }
</style>
<table class="table table-hover" id="jobtable">
	<thead>
		<tr>
			<th align="center" style="text-align: center"><i class="fas fa-user-tie fa-4x"></i><b style="font-size: 20px;color: #f9b721;font-weight: 600;">&nbsp;JOB TITLE</b></th>
			<th align="center" style="text-align: center"><i class="fas fa-business-time fa-4x"></i><b style="font-size: 20px;color: #f9b721;font-weight: 600;">&nbsp;EXPERIENCE</b></th>
            <th align="center" style="text-align: center"><i class="fas fa-calendar-alt fa-4x"></i><b style="font-size: 20px;color: #f9b721;font-weight: 600;">&nbsp;OPEN UNTIL</b></th>
            <th align="center" style="text-align: center"><i class="fa fa-object-group fa-4x"></i><b style="font-size: 20px;color: #f9b721;font-weight: 600;">&nbsp;DEPARTMENT</b></th>
			<th align="center" style="text-align: center"><i class="fa fa-pencil fa-4x"></i><b style="font-size: 20px;color: #f9b721;font-weight: 600;">&nbsp;SUBJECT</b></th>
			<th width="20%"></th>
		</tr>
	</thead>
	<tbody>
        <?php foreach($record as $row): ?>
        <tr>
            <td align="center"><b style="font-size: 17px;font-weight: 500;"><?=$row->description?></b></td>
            <td align="center"><b style="font-size: 17px;font-weight: 500;"><?=$row->experience?> Years</b></td>
            <td align="center"><b style="font-size: 17px;font-weight: 500;"><?=$row->UNTIL?></b></td>
            <td align="center"><b style="font-size: 17px;font-weight: 500;"><?=$this->extensions->getCourseDescription($row->course)?></b></td>
            <td align="center"><b style="font-size: 17px;font-weight: 500;"><?=$this->extensions->getSubjectDescription($row->subject)?></b></td>
            <td align="center">
                <button class="btn btn-primary viewInfo" file="<?=$row->FILE?>" filename="<?=$row->filename?>" style="border-radius: 0px;"><b>View Details</b></button>&nbsp;&nbsp;
                <button class="btn btn-warning applynow" tag='<?= $row->description ?>' course='<?= $this->extensions->getCourseDescription($row->course)?>' subject='<?= $this->extensions->getSubjectDescription($row->subject) ?>' site='<?=site_url('applicant/validate')?>' style="border-radius: 0px;"><b style="color: black;">Apply Now</b></button>
            </td>
        </tr>
        <?php endforeach ?>
	</tbody>
</table>

<script>
var toks = hex_sha512(" ");
oTable = $('#jobtable').DataTable({
    searching: true,
    paging: false,
    info: false
});

$(document).ready(function(){
    $("#jobtable_filter").parent().parent().hide();
});

  $("#searchJob").on("click", function() {
    var value = $("#srcJob").val().toLowerCase();
    oTable.search(value).draw();
  });

  $(".applynow").click(function(){
    var selected = $(this).attr("tag");
    var course = $(this).attr("course");
    var subject = $(this).attr("subject");
    var isteaching = "";
    if(course != "--" && subject != "--") isteaching = true;
    $.ajax({
        type: 'post',
        data: {
            selected: GibberishAES.enc( selected, toks),
            course: GibberishAES.enc(course , toks),
            subject: GibberishAES.enc( subject, toks),
            isteaching: GibberishAES.enc( isteaching, toks),
            toks:toks
        },
        url : "<?= site_url('applicant/applicantSignupForm') ?>",
        success:function(response){
            $("#display").html(response);
            $("#signup_modal").modal('toggle');
        }
    });
  });

$(".viewInfo").click(function(){
    var data = $(this).attr("file");
    if(!data){
        alert("No details available.");
        return;
    }
    var filename = $(this).attr("filename");
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