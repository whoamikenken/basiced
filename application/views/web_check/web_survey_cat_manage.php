<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
?>	
<div class="col-md-10 col-md-offset-1">
	<form class="form-horizontal" id="survey-cat-setup-manage">
		<input type="hidden" name="action" value="<?= $tag ?>">
		<input type="hidden" name="id" value="<?= $id ?>">
		<div class="form-group">
		    <label class="col-sm-3 control-label">Rank</label>
		    <div class="col-sm-9">
		    	<input type="number" name="rank" class="form-control" value="<?= $rank ?>">
		    </div>
		</div>
		<div class="form-group">
		    <label class="col-sm-3 control-label">Name</label>
		    <div class="col-sm-9">
		    	<input type="text" name="name" class="form-control" value="<?= $name ?>">
		    </div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('#button_save_modal').unbind('click').bind('click', function (e) {
		if ($("input[name='name']").val() == "") {
			Swal.fire({
		        icon: 'warning',
		        title: 'Warning!',
		        text: 'Please Input Name',
		        showConfirmButton: true,
		        timer: 1500
		    })
			return false;
		}

    	var formdata = $("#survey-cat-setup-manage").serialize();
        $.ajax({
        type: "POST",
        url: $("#site_url").val() + "/webcheckin_/saveSurveyCat",
        data: formdata,
        success:function(response){
            if(response == "dup"){
                Swal.fire({
			        icon: 'warning',
			        title: 'Warning!',
			        text: 'Rank duplicate',
			        showConfirmButton: true,
			        timer: 1500
			    })
            }else if(response == "added." || response == "updated."){
            	Swal.fire({
			        icon: 'success',
			        title: 'Success!',
			        text: 'Setup is successfully '+ response,
			        showConfirmButton: true,
			        timer: 1500
			    })
			    setTimeout(function() {
			    	surveyCatSetup();
            		$('#modal-view').modal('toggle');
			    }, 1600);
            }else{

            }
        }
        });
	  
	});

</script>