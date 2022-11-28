<html>
	<head>
		<meta http-equiv="refresh" content="100; url="<?php echo $_SERVER['PHP_SELF']; ?>">
	</head>
	
	<body>
qwe
	</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script>

	setInterval(
		function(){ 
			$.ajax({
				url: "<?= site_url('test_/sample') ?>",
				success:function(response){
					console.log(response);
				}
			});
	 	}, 2000
	 );

	function checkAttendance(){
		$(document).ready(function(){
			$.ajax({
				url: "<?= site_url('test_/sample') ?>",
				success:function(response){
					console.log(response);
				}
			});
		});
	}
</script>

</html>