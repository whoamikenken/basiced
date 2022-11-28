<?php
	$absent_tag = "";
?>

<div class="content" style="font-family: avenir!important;">
	<div class="box" style="display:block;font-family: avenir!important;">
		<legend style="font-weight: bold;font-family: avenir!important; font-size: 20px; text-align: center;">You have a notification message from Attendance Module!</legend><br>
		<?php if($absent > 0) {?>
		<p>It's seems that you already have a total of <b><span style="color:red;"><?= $absent ?> </b></span><?=($absent > 1) ? $absent_tag = "absences" : $absent_tag = "absent"?>  in our current cutoff. Click "See my Attendance" below to directly read your messages.</p>
		<?php } ?>
		<?php if($islacking_in_out){?>
		<p>You <?= ($absent > 0) ? "also" : ""?> have a total of <b><span style="color:red;"><?= $islacking_in_out ?></span></b> days that lacking a TIMEIN or TIMEOUT in current cutoff. Click "See my Attendance" below to directly verify your attendance.</p>
		<?php } ?>
		<?php if($half_day){?>
		<p>You <?= ($absent > 0) ? "also" : ""?> have a total of <b><span style="color:red;"><?= $half_day ?></span></b> half-day in current cutoff. Click "See my Attendance" below to directly verify your attendance.</p>
		<?php } ?>
	</div>
</div>


<script>
	$(".closeABSENTMESSAGEmodal").unbind('click').click(function(){
	    $("#ABSENTMESSAGEmodal").modal('toggle');
	});
</script>
