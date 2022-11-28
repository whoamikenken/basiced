<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

$link = $this->extras->getAMSLink($this->input->post('menuid'));
?>
<div id="content">
<!-- 	<form id="frm-bundy-clock" method="post" target="_blank">
		<input type="hidden" name="session_id" value="<?=$this->session->userdata("session_id");?>">
	</form> -->

	<!-- <h1>Page will appear in new tab..</h1> -->
</div>

<script type="text/javascript">
	$(document).ready(function(){
		// var hostname = (location.origin != "http://192.168.2.97") ? location.origin + "/": "";

		// $("#frm-bundy-clock").attr("action", hostname +"<?=$link?>");
		// $("#frm-bundy-clock").submit();

		var ams_url = window.location.href;
		ams_url  = ams_url .substring(0, ams_url .length - 9);
		window.location.href = ams_url + '<?=$link?>';
	});
</script>