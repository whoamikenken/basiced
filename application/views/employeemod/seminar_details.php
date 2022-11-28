<div class="seminar_details">
	<div class="col-md-12">
		<div class="field col-md-12">
			<label class="col-md-4 align_right">Seminar&nbsp;Category:</label>
			<div class="col-md-8">
				<span><?= $seminardetails[0]['Description'] ?></span>
			</div>
		</div>
		<div class="field col-md-12">
			<label class="col-md-4 align_right">Seminar Title:</label>
			<div class="col-md-8">
				<span><?= $seminardetails[0]['level'] ?></span>
			</div>
		</div>
		<div class="field col-md-12">
			<label class="col-md-4 align_right">Date Included:</label>
			<div class="col-md-8">
				<span><?= $seminardetails[0]['date_from'].' - '.$seminardetails[0]['date_to'] ?></span>
			</div>
		</div>
		<div class="field col-md-12">
			<label class="col-md-4 align_right">Time:</label>
			<div class="col-md-8">
				<span><?= isset($seminardetails[0]) ?  date('h:i:s A', strtotime($seminardetails[0]['time_from'])).' - '.date('h:i:s A', strtotime($seminardetails[0]['time_to'])) : ''; ?></span>
			</div>
		</div>
		<div class="field col-md-12">
			<label class="col-md-4 align_right">Organizer:</label>
			<div class="col-md-8">
				<span><?= $seminardetails[0]['organizer'] ?></span>
			</div>
		</div>
		<div class="field col-md-12">
			<label class="col-md-4 align_right">Venue:</label>
			<div class="col-md-8">
				<span><?= $seminardetails[0]['venue'] ?></span>
			</div>
		</div>
		<!-- <div class="field col-md-12">
			<label class="col-md-4 align_right">Location:</label>
			<div class="col-md-8">
				<span><?= $seminardetails[0]['location'] ?></span>
			</div>
		</div> -->
	</div>
</div>