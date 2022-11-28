<form id="authform">
	<input type="hidden" name="email">	
</form>

<div style="margin:auto;width: 40%;">
	<img src="<?=base_url()?>img/googleloading.gif" style="display: inline;">
</div>

<script>
    $.ajax({
        url: "<?=site_url("main/googleAuth")?>",
        type: "POST",
        data: $("#authform").serialize(),
        success: function(msg){
        	if(msg){
	            sessionStorage.clear();
	            sessionStorage.setItem('userLoggedin','PovedaPinnacle'); 
	        }else{
	        	alert("Email address not exisiting. Please try again.");
	            window.location.href = window.location;
	        }
        }
    });
</script>