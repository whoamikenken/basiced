<?php

	$this->load->model("attendance");
	$this->attendance->initialize($from_date,$to_date);