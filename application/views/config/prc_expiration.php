<style>
  	.cbox{
     	-ms-transform: scale(1.5); /* IE */
     	-moz-transform: scale(1.5); /* FF */
     	-webkit-transform: scale(1.5); /* Safari and Chrome */
     	-o-transform: scale(1.5); /* Opera */
  	}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                    <div class="panel-heading"><h4><b>PRC Expiration</b></h4></div>
                    <div class="panel-body" style="padding: 0px; margin-top: 15px; margin-bottom: 15px;" id="prc_expiration">

                    </div>
               	</div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
	loadPRCExpiration();

	function loadPRCExpiration(){
		$.ajax({
			url: $("#site_url").val() + "/expiration_/expiryPRC",
			success:function(res){
				$("#prc_expiration").html(res);
			}
		})
	}

</script>
