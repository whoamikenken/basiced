<?php

/**
 * @author Justin
 * @copyright 2016
 */

?>
<html>
<head>
<style>
p:hover{
    background-color: #14E5BF;
}
</style>
<title>Upload Form</title>
</head>
<body>
<h3><?=$message?></h3>
<p class="btn btn-primary"><?php echo anchor('main', '<b>Back to main..</b>','style="text-decoration: none;"'); ?></p>
</body>
</html>
<script>
$(document).ready(function(){
  setInterval(function(){ window.location = "<?=site_url()?>" }, 3000);  
});
</script>