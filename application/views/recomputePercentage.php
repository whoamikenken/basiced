<div id ="teaching_load" style="display:none;font-family: arial;color:black;">	
	<p id="loading"><img src="<?=base_url()?>images/loading.gif">Loading.. Please wait...</p>
	<p>Recomputing Teaching Employees: <?= isset($teaching[0]['emp_count']) ? $teaching[0]['emp_count'] : 0 ?> of <?= isset($teaching[0]['emp_total']) ? $teaching[0]['emp_total'] : 0 ?> employee. Success: <?=  isset($teaching[0]['success']) ? $teaching[0]['success'] : 0 ?> --- Failed: <?=  isset($teaching[0]['failed']) ? $teaching[0]['failed'] : 0 ?></p>
</div>
&nbsp;&nbsp;
<div id ="nonteaching_load" style="display:none;font-family: arial;color:black;">
	<p id="loading"><img src="<?=base_url()?>images/loading.gif">Loading.. Please wait...</p>
	<p>Recomputing:Non Teaching Employees <?= isset($nonteaching[0]['success']) ? $nonteaching[0]['success'] : 0 ?> of <?= isset($nonteaching[0]['emp_total']) ? $nonteaching[0]['emp_total'] : 0 ?> employee.  Success: <?=  isset($nonteaching[0]['success']) ? $nonteaching[0]['success'] : 0 ?> --- Failed: <?=  isset($nonteaching[0]['failed']) ? $nonteaching[0]['failed'] : 0?></p>
</div>


<script>
	$(document).ready(function(){
		if("<?=$selected?>" == "teaching"){
			if("<?=$teaching[0]['emp_count']?>" == "<?=$teaching[0]['emp_total']?>") $("#teaching_load").hide();
			else $("#teaching_load").show();
		}
		if("<?=$selected?>" == "nonteaching"){
			if("<?=$nonteaching[0]['emp_count']?>" == "<?=$nonteaching[0]['emp_total']?>") $("#nonteaching_load").hide();
			else $("#nonteaching_load").show();
		}
	});
</script>