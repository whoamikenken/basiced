<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
?>

<div class="modal-dialog">

	<div class="modal-content">
		<div class="modal-header">
			<div class="media">
				<div class="media-left">
					<img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
				</div>
				<div class="media-body" style="font-weight: bold;padding-top: 10px;">
					<h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                     <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
				</div>
			</div>
			<center><b><h3 tag="title" class="modal-title">Process DTR</h3></b></center>
		</div>
		<div class="modal-body">
			<div class="row">
		        <div style="text-align: center;">
		        	<a class="btn btn-primary" href="#" id="ewc"> Employee who confirmed</a>
		            <a class="btn btn-primary" href="#" id="enyc"> Employee not yet confirmed</a>
		        </div>
		    </div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
		</div>
	</div>

</div>

<script src="<?=base_url()?>js/attendance/att_confirm.js"></script>
