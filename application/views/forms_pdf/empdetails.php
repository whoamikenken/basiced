<?php
	$imgurl = __DIR__."/../../../";
 	ini_set('max_execution_time', 0);
	ini_set("memory_limit", "2G");
	$res = $this->employee->loadallemployee(array("employeeid"=>$id));
	$empdetails = $res[0];
	
	$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,5);
	$pdf->img_dpi = 50;
	$pdf->Bookmark('Start of the document'); 

	if(isset($empinfo))   $empdetails = $empinfo[0];  
	else{
		$empinfo = $this->session->userdata("personalinfo"); 
		$empdetails = $empinfo[0];
	}
	
	if($empdetails['employeeid']){
		$var = $this->employee->loadallemployee(array("employeeid"=>$empdetails['employeeid']));
		$empdetails = $var[0];
		
		$this->session->unset_userdata("personalinfo");

		$this->session->set_userdata("personalinfo",$var);

	$employeeid = $empdetails['employeeid'];
    $employeecode = $empdetails['employeecode'];
    $fname = $empdetails['fname'];
    $lname = $empdetails['lname'];
    $mname = $empdetails['mname'];
    $nname = $empdetails['nname'];
    $cityaddr = $empdetails['cityaddr'];
    $provaddr = $empdetails['provaddr'];
    $regaddr = $empdetails['regaddr'];
    $barangay = $empdetails['barangay'];
    $zip_code = $empdetails['zip_code'];
    $addr = $empdetails['addr'];
    $occupation = $empdetails['occupation'];
    $age    = $empdetails['age'];
    $gender = $empdetails['gender'];
    $civil_status = $empdetails['civil_status'];

    $emp_bank = $empdetails['emp_bank'];

    $spouse = $empdetails['spouse_name'];
    $bdate = isset($empdetails['bdate']) ? $empdetails['bdate'] : "";
    $mobile = $empdetails['mobile'];
    $citytelno = $empdetails['citytelno'];
    $email = $empdetails['email'];
    $employmentstat = $empdetails['employmentstat'];
    $emptype = $empdetails['emptype'];
    $empshift = $empdetails['empshift'];
    $date_active = $empdetails['date_active'];
    $rank = $empdetails['rank'];

    $dateemployed = isset($empdetails['dateemployed']) ? $empdetails['dateemployed'] : "";
    $campusid = $empdetails['campusid'];
    $maxregular = $empdetails['maxregular'];
    $maxparttime = $empdetails['maxparttime'];
    $bplace = $empdetails['bplace'];
    $deptid = $empdetails['deptid'];
    $office = $empdetails['office'];
    $leavetype = $empdetails['leavetype'];
    
    $position = $empdetails['position'];
    $datepos = (!empty($empdetails['dateposition']) && $empdetails['dateposition'] != "0000-00-00" && $empdetails['dateposition'] != "1970-01-01") ? date("Y-m-d",strtotime($empdetails['dateposition'])) : "";
    $assignment = $empdetails['assignment'];
    $remarks = $empdetails['remarks'];
    $management = $empdetails['management'];
    $dateresigned = $empdetails['dateresigned'] ? $empdetails['dateresigned'] : '';
    $resigned_reason = $empdetails['resigned_reason'];
    $tinno = $empdetails['tinno'];
    $sssno = $empdetails['sssno'];
    $philhealth = $empdetails['philhealth'];
    $pagibig = $empdetails['pagibig'];
    $emp_hmo = $empdetails['emp_hmo'];
    $peraa = $empdetails['peraa'];
    $medicare = $empdetails['medicare'];
    $emp_accno = $empdetails['emp_accno'];
    $citizenship = $empdetails['citizenship'];
    $religion = $empdetails['religion'];
    $nationality = $empdetails['nationality'];
    $prc = $empdetails['prc'];
    $passport = $empdetails['passport'];
    $visa = $empdetails['visa'];
    $icard = $empdetails['icard'];
    $crnno = $empdetails['crn'];
    $permanent_address = $empdetails['permanentaddress'];
    $legitimate_relations = $empdetails['legitimate_relations'];
    $mother = $empdetails['mother'];
    $motheroccu = $empdetails['motheroccu'];
    $father = $empdetails['father'];
    $fatheroccu = $empdetails['fatheroccu'];
    $distinguishingMarks = $empdetails['distinguishingMarks'];
    $hosp = $empdetails['hospitalized'];
    $hosptxt = $empdetails['hospitalizedtxt'];
    $operation = $empdetails['operation'];
    $operationtxt = $empdetails['operationtxt'];
    $operationdate = $empdetails['operationdate'];
    $medhistory = $empdetails['medhistory'];
    $medhistorytxt = $empdetails['medhistorytxt'];
    $medconditions = $empdetails['medconditions'];
    
    $cp_name = $empdetails['cp_name'];
    $cp_relation = $empdetails['cp_relation'];
    $cp_address = $empdetails['cp_address'];
    $cp_mobile = $empdetails['cp_mobile'];
    $cp_telno = $empdetails['cp_telno'];
    $teaching = $empdetails['teaching'];
    $teachingtype = $empdetails['teachingtype'];
    $accai = $empdetails['isactive'];
    $spouse_contact = $empdetails['spouse_contact'];

    $blood_type = $empdetails['blood_type'];
    $height = $empdetails['height'];
    $weight = $empdetails['weight'];
    $prc_expiration = $empdetails['prc_expiration'];
    $passport_expiration = $empdetails['passport_expiration'];
    $age    = $empdetails['age'];
    $landline = $empdetails['landline'];
    $personal_email = $empdetails['personal_email'];
    $permaProvince = $empdetails['permaProvince'];
    $permaMunicipality = $empdetails['permaMunicipality'];
    $permaRegion = $empdetails['permaRegion'];
    $permaBarangay = $empdetails['permaBarangay'];
    $permaZipcode = $empdetails['permaZipcode'];
    $permaAddress = $empdetails['permaAddress'];
    $dateresigned2 = (isset($empdetails['dateresigned2']) && $empdetails['dateresigned2'] != "-0001-11-30" && $empdetails['dateresigned2'] != "1970-01-01" && $empdetails['dateresigned2'] != "0000-00-00")  ? $empdetails['dateresigned2'] : '';
	}
	$employment_history = $this->employee->getEmploymentStatusHistory($employeeid);
	$work_history_related = $this->db->query("SELECT * from employee_work_history_related where employeeid='$employeeid' and status = 'APPROVED' ")->result();
	$employee_emergencyContact = $this->db->query("select * from employee_emergencyContact where employeeid='$employeeid' and status = 'APPROVED' ")->result();
	$employee_children = $this->db->query("SELECT * from employee_children where employeeid='$employeeid' and status = 'APPROVED' ")->result();
	$employee_family = $this->db->query("SELECT * from employee_family where employeeid='$employeeid' and status = 'APPROVED' ")->result();
	// $employee_skill = $this->db->query("SELECT * from employee_skills where employeeid='$employeeid' and status = 'APPROVED' ")->result();

	$educational_background = $this->db->query("SELECT school,course,units,year_graduated,date_graduated,datefrom,dateto,e.educ_level,r.level , e.id, e.status, a.description as schoolDesc, a.schoolid from employee_education e INNER JOIN reports_item r ON e.educ_level = r.level INNER JOIN code_school a ON e.schoolid = a.schoolid  where employeeid='{$empdetails['employeeid']}' and status = 'APPROVED' ")->result();
	$eligibilities = $this->db->query("SELECT * from employee_eligibilities where employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();

	// $eligibilities = $this->db->query("SELECT e.description, r.id, e.id as tbl_id, license_number, remarks, date_issued, date_expired, r.level, e.status from employee_eligibilities e INNER JOIN reports_item r on e.description = r.id where employeeid='{$empdetails['employeeid']}'")->result();
	$ar = $this->db->query("SELECT award,institution,datef,r.level,r.description,r.ID,address FROM employee_awardsrecog e LEFT JOIN reports_item r ON e.award = r.ID WHERE employeeid='{$empdetails['employeeid']}' AND e.status = 'APPROVED'")->result();
	$work_history = $this->db->query("SELECT date_from,date_to,position,company,address,contactnumber,salary from employee_work_history where employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
	$sctt = $this->db->query("SELECT a.id as tbl_id, a.employeeid, a.subj_id, b.subj_code, b.description,a.remarks, a.status FROM employee_subj_competent_to_teach a LEFT JOIN code_subj_competent_to_teach b ON a.subj_id=b.id WHERE a.employeeid='{$empdetails['employeeid']}' AND a.status = 'APPROVED'")->result();

	$pgd = $this->db->query("SELECT publication,title,publisher,datef,type, e.id as id, r.level as level, e.status from employee_pgd e INNER JOIN reports_item r ON e.publication = r.ID where employeeid='{$empdetails['employeeid']}' AND e.status = 'APPROVED'")->result();

    // $pts = $this->db->query("SELECT * FROM employee_pts WHERE employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();

    $pts = $this->db->query("SELECT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.location, a.other_title FROM employee_pts a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();

    // $pts_pdp1 = $this->db->query("SELECT * FROM employee_pts_pdp1 WHERE employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
    $pts_pdp1 = $this->db->query("SELECT DISTINCT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.datet, a.seminar_title, a.location, a.regfee, a.transfee, a.accfee, a.total FROM employee_pts_pdp1 a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
    // $pts_pdp2 = $this->db->query("SELECT * FROM employee_pts_pdp2 WHERE employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
    $pts_pdp2 = $this->db->query("SELECT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.location, a.other_title FROM employee_pts_pdp2 a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
    $pts_pdp3 = $this->db->query("SELECT * FROM employee_pts_pdp3 WHERE employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
    $ar = $this->db->query("SELECT award,institution,datef,r.level,r.description,r.ID,address, e.id as tbl_id, e.status FROM employee_awardsrecog e LEFT JOIN reports_item r ON e.award = r.ID WHERE employeeid='{$empdetails['employeeid']}' AND e.status = 'APPROVED'")->result();
    $scho = $this->db->query("SELECT s.id as id, s.type_of_scho, s.gr_agency, s.prog_study, s.ins_scho, s.datef, s.dateto, r.ID, r.level as scholarship, s.status from employee_scholarship s INNER JOIN reports_item r ON s.type_of_scho = r.ID where employeeid='{$empdetails['employeeid']}' AND s.status = 'APPROVED'")->result();
    $resource = $this->db->query("SELECT * from employee_resource where employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
    $org   = $this->db->query("SELECT * from employee_proorg  where employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
    $community   = $this->db->query("SELECT e.educational_level, r.level, e.id as id, e.school, e.ctype, e.honor, e.year_grad, e.status from employee_community e LEFT JOIN reports_item r ON e.educational_level = r.ID  where employeeid='{$empdetails['employeeid']}' AND e.status = 'APPROVED'")->result();
    $administrative   = $this->db->query("SELECT * from employee_administrative where employeeid='{$empdetails['employeeid']}' AND status = 'APPROVED'")->result();
	// $iquery = $this->db->query("SELECT * FROM employee_photo where employeeid='$employeeid'");
	
	// if($iquery->num_rows()>0){
	// 	$photo = json_decode(json_encode($iquery->result()), true);
	// 	$img = "<img src='data:image/jpg;base64,".$photo[0]['file']."' height='122px' width='122px' style='border: 1px solid #a1a1a1;'/>";
	// }else{
	// 	$img = "<img src='".base_url()."images/no_image.gif' height='122px' width='122px'/>";
	
	// }

	// $employee_photo = $this->db->query("SELECT * FROM employee_photo where employeeid = '$employeeid'");
	// $hasPhoto = $hasElfinderPhoto = 0;
	// if($employee_photo->num_rows() > 0){
	//     $hasPhoto++;
	//     $photo = json_decode(json_encode($employee_photo->result()), true);
	//     $img = "<img src='data:image/jpg;base64,".$photo[0]['file']."' height='122px' width='122px' style='border: 1px solid #a1a1a1;'/>";
	// }else{
	//   $employee_elfinder_file = $this->db->query("SELECT * FROM elfinder_file a WHERE a.name LIKE '%$employeeid%'")->result();
	//     foreach ($employee_elfinder_file as $key => $value) {
	//       $hasElfinderPhoto++;
	//       $photo = "data:image/jpg;base64,".base64_encode($value->content);
	//       $img = "<img src='".$photo."' height='122px' width='122px' style='border: 1px solid #a1a1a1;'/>";
	//     }
	//     if($hasElfinderPhoto == 0) $img = "<img src='".base_url()."images/no_image.gif' height='122px' width='122px'/>";
	// }

	$employee_photo =  $this->db->query("SELECT * FROM elfinder_file a WHERE a.name LIKE '%$employeeid%'");
	$hasPhoto = 0;
	if($employee_photo->num_rows() > 0){
	    $hasPhoto++;
	    foreach ($employee_photo->result() as $key => $value) {
	    	if($value->mime == 'image/jpeg' && substr(base64_encode($value->content),0,4) != '/9j/'){
	    		$photo = "data:image/webp;base64,".base64_encode($value->content);
	    		$img = "<img src=\"".$photo."\" height='122px' width='122px' style='border: 1px solid #a1a1a1;'/>";
	    	}else{
	   //  		$WIDTH                  = 400;
				// $HEIGHT                 = 300; 
				// $QUALITY                = 100; 
				// $org_w = $org_h = 800;
	   //  		$theme_image_little = imagecreatefromstring($value->content);
				// $image_little = imagecreatetruecolor($WIDTH, $HEIGHT);
				// imagecopyresampled($image_little, $theme_image_little, 0, 0, 0, 0, $WIDTH, $HEIGHT, $org_w, $org_h);

				$im = imagecreatefromstring($value->content);
				$source_width = imagesx($im);
				$source_height = imagesy($im);
				$ratio =  $source_height / $source_width;

				$new_width = 300; // assign new width to new resized image
				$new_height = $ratio * 300;


				$transparency = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
				imagefilledrectangle($thumb, 0, 0, $new_width, $new_height, $transparency);
				$thumb = imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($thumb, $im, 0, 0, 0, 0, $new_width, $new_height, $source_width, $source_height);


				ob_start();
				imagejpeg($thumb);
				$contents =  ob_get_contents();
				ob_end_clean();
				$theme_image_enc_little = base64_encode($contents);
			    $photo = "data:image/jpeg;base64,".$theme_image_enc_little;
	    		$img = "<img src='".$photo."' height='122px' width='122px' style='border: 1px solid #a1a1a1;'/>";
	    	}
	    }
	}else{
		$img = "<img src='".base_url()."images/no_image.gif' height='122px' width='122px'/>";
	}

 
	$info  = "  <style>
					.content{
						padding-left:1cm;
						padding-right:1cm;
					}
					
					.underline{
						border-bottom:1px solid black;
					}

					h5{
						font-size: 14px;
					}
					th, td{
						text-transform: uppercase;
					}
				</style>";
	$info .= "
		<body style='font-family:calibri;'>	
			<div>
				<table width='100%'>
					<tr>
						<td style='text-align: center;'><h4 style='font-size: 15px;'><b>Pinnacle Technologies Inc.</b></h4></td>
					</tr>
					<tr>
						<td style='text-align: center;'><h5 style='font-size: 10px;'><strong>D`Great</strong></h5></td>
					</tr>
				</table>
			<div class='content' style='margin-top:.1cm;'>
				<table  style='background-color: #d2d2d2;'>
					<tr>
						<td>
						<h5>GENERAL INFORMATION</h5>
							<table width='100%' style='background-color: white;'>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Employee ID:</b></td>
									<td width='5%' style='font-size: 10px; text-align: left'>".$employeeid."</td>
									<td width='350px' style='font-size: 10px; text-align: left'>&nbsp;</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Last Name:</b></td>
									<td width='5%' style='font-size: 10px;'>".$lname."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>First Name:</b></td>
									<td width='5%' style='font-size: 10px;'>".$fname."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Middle Name:</b></td>
									<td width='5%' style='font-size: 10px;'>".$mname."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Date Hired:</b></td>
									<td width='5%' style='font-size: 10px;'> ".date("F d, Y",strtotime($dateemployed))."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Rank:</b></td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->getemployeeRank($rank)."</td>
								</tr>
							</table>
						</td>
					<td align='right'>$img</td>
					</tr>
				</table>
			</div>
			
			<div class='content' style='margin-top:.1cm;'>
				<table width='100%' style='background-color: #d2d2d2;'>
					<tr>
						<td>
						<h5>IDENTIFICATION NUMBER</h5>
							<table width='100%' style='background-color: white;'>
								<tr>
									<td width='1%' style='font-size: 10px;'><b>TIN# :</b></td>
									<td width='1%' style='font-size: 10px;'>".$tinno."</td>
									<td width='1%' style='font-size: 10px;'><b>PAG-IBIG :</b></td>
									<td width='1%' style='font-size: 10px;'>".$pagibig."</td>
									<td width='1%' style='font-size: 10px;'><b>SSS# :</b></td>
									<td width='1%' style='font-size: 10px;'>".$sssno."</td>
								</tr>
								<tr>
									<td width='1%' style='font-size: 10px;'><b>Philhealth :</b></td>
									<td width='1%' style='font-size: 10px;'>".$philhealth."</td>
									<td width='1%' style='font-size: 10px;'><b>HMO# :</b></td>
									<td width='1%' style='font-size: 10px;'>".$emp_hmo."</td>
								</tr>
								<tr>
									<td width='1%' style='font-size: 10px;'><b>PRC# :</b></td>
									<td width='1%' style='font-size: 10px;'>".$prc."</td>
									<td width='1%' style='font-size: 10px;'><b>Date of Expiration# :</b></td>
									<td width='1%' style='font-size: 10px;'>".$prc_expiration."</td>
								</tr>
								<tr>
									<td width='1%' style='font-size: 10px;'><b>Passport# :</b></td>
									<td width='1%' style='font-size: 10px;'>".$passport."</td>
									<td width='1%' style='font-size: 10px;'><b>Date of expiration# :</b></td>
									<td width='1%' style='font-size: 10px;'>".$passport_expiration."</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>

			<div class='content' style='margin-top:.1cm;'>
				<table width='100%' style='background-color: #d2d2d2;'>
					<tr>
						<td>
						<h5>BANK DETAILS</h5>
							<table width='100%' style='background-color: white;'>
								<tr>
									<td width='5%' style='font-size: 10px; text-align: center'><b>Bank</b></td>
									<td width='5%' style='font-size: 10px; text-align: center'><b>Account Number</b></td>
								</tr>
								";
				                $convert_to_array = explode('/', $emp_bank);
				                for($i=0; $i < count($convert_to_array ); $i++){
				                    $key_value = explode('=', $convert_to_array [$i]);
				                    $banks[$key_value [0]] = isset($key_value [1]) ? $key_value [1] : "";
				                }
                        		foreach($this->extensions->getBankList() as $row): 
                        			$info .="
                        			<tr>
										<td width='5%' style='font-size: 10px; text-align: center'>".$row['bank_name']."</td> ";
										$bankvalue = "";
                                        foreach($banks as $key => $val){
                                            if(is_array($val)){
                                                $return = recursive_return_array_value_by_key($row['code'], $val);
                                            }
                                            else if($row['code'] === $key){
                                                $bankvalue = $val;
                                            }
                                        }
                                        $info .="
                                        <td width='5%' style='font-size: 10px; text-align: center'>".$bankvalue."</td>
									</tr>
									";
                       			endforeach;
								$info .="
							</table>
						</td>
					</tr>
				</table>
			</div>

			<div class='content' style='margin-top:.1cm;'>
				<table width='100%' style='background-color: #d2d2d2;'>
					<tr>
						<td>
						<h5>EMPLOYEE INFORMATION</h5>
							<table width='100%' style='background-color: white;'>
								<tr>
									<td width='1%' style='font-size: 10px;'><b>Type:</b></td>
									<td width='5%' style='font-size: 10px;'>".($teachingtype == 'teaching' ? 'Teaching' : 'Non-teaching' )."</td>
									<td width='5%' style='font-size: 10px;'><b>Account:</b></td>
									<td width='5%' style='font-size: 10px;'>".($accai == '1' ? 'Active' : 'In-active')."</td>
									<td width='5%' style='font-size: 10px;'>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Campus:</b></td>
									<td width='5%' style='font-size: 10px;'>".$campusid."</td>
									<td width='5%' style='font-size: 10px;'><b>Schedule List:</b></td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->getemployeeSchedule($empshift)."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Batch Scheduling:</b></td>
									<td width='5%' style='font-size: 10px;'>".$emptype."</td>
									<td width='5%' style='font-size: 10px;'><b>Effectivity Date:</b></td>
									<td width='5%' style='font-size: 10px;'>".$date_active."</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>

			<div class='content' style='margin-top:.1cm'>
				<table width='100%' style='background-color: #d2d2d2;'>
					<tr>
						<th colspan='6' style='font-weight:bold;text-align:left;font-size: 12px;background-color: #d2d2d2'>EMPLOYMENT STATUS</th>
					</tr>
					<tr>
						<td>
							<table  style='background-color: white;'>
								<tr>
									<th width='10%' style='font-size: 10px;'>Department</th>
									<th width='10%' style='font-size: 10px;'>Office</th>
									<th width='10%' style='font-size: 10px;'>Employement Status</th>
									<th width='10%' style='font-size: 10px;'>Position</th>
									<th width='10%' style='font-size: 10px;'>Start Date</th>
									<th width='10%' style='font-size: 10px;'>Date Resigned</th>
								</tr>
								";
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:center;'>".$this->extras->getemployeedepartment($deptid)."</td>
									<td style='font-size: 10px; text-align:center;'>".$this->extras->getemployeeoffice($office)."</td>
									<td style='font-size: 10px; text-align:center;'>".$this->extras->getemployeestatus($employmentstat)."</td>
									<td style='font-size: 10px; text-align:center;'>".$this->extras->showPosDesc($position)."</td>
									<td style='font-size: 10px; text-align:center;'>".$datepos."</td>
									<td style='font-size: 10px; text-align:center;'>&nbsp;</td>
								</tr>

								";
								$info .="
							</table>
						</td>
					</tr>
					<tr>
						<th colspan='6' style='font-weight:bold;text-align:left;font-style: italic;font-size: 12px; background-color: #d2d2d2'>HISTORY</th>
					</tr>
					<tr>
						<td>
							<table  style='background-color: white;'>
								<tr>
									<th width='10%' style='font-size: 10px;'>Department</th>
									<th width='10%' style='font-size: 10px;'>Office</th>
									<th width='10%' style='font-size: 10px;'>Employement Status</th>
									<th width='10%' style='font-size: 10px;'>Position</th>
									<th width='10%' style='font-size: 10px;'>Start Date</th>
									<th width='10%' style='font-size: 10px;'>Date Resigned</th>
								</tr>
								";
								if(count($employment_history)>0){
								foreach($employment_history as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:center;'>".$eb->deptdesc."</td>
									<td style='font-size: 10px; text-align:center;'>".$eb->officedesc."</td>
									<td style='font-size: 10px; text-align:center;'>".$eb->statdesc."</td>
									<td style='font-size: 10px; text-align:center;'>".$eb->posdesc."</td>
									<td style='font-size: 10px; text-align:center;'>".$eb->dateposition."</td>
									<td style='font-size: 10px; text-align:center;'>".$eb->dateresigned."</td>
								</tr>

							";
							}
							}
							$info .="	

							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class='content' style='margin-top:.1cm;'>
				<table style='background-color: #d2d2d2;'>
					<tr>
						<td>
						<h5>PERSONAL INFORMATION</h5>
							<table style='background-color: white;'>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Date of Birth#:</b></td>
									<td width='5%' style='font-size: 10px;'>".$bdate."</td>
									<td width='5%' style='font-size: 10px;'><b>Place of Birth:</b></td>
									<td width='5%' style='font-size: 10px;'>".$bplace."</td>
									<td width='5%' style='font-size: 10px;'><b>Age:</b></td>
									<td width='5%' style='font-size: 10px;'>".$age."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Gender:</b></td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->genderdesc($gender)."</td>
									<td width='5%' style='font-size: 10px;'><b>Nationality:</b></td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->nationalitydesc($nationality)."</td>
									<td width='5%' style='font-size: 10px;'><b>Religion:</b></td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->religiondesc($religion)."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Civil Status:</b></td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->civilstatusdesc($civil_status)."</td>
									<td width='5%' style='font-size: 10px;'><b>Citizenship:</b></td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->citizenshipdesc($citizenship)."</td>
									<td width='5%' style='font-size: 10px;'><b>Personal Email:</b></td>
									<td width='5%' style='font-size: 10px;'>".$personal_email."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Mobile:</b></td>
									<td width='5%' style='font-size: 10px;'>".$mobile."</td>
									<td width='5%' style='font-size: 10px;'><b>Landline:</b></td>
									<td width='5%' style='font-size: 10px;'>".$landline."</td>
									<td width='5%' style='font-size: 10px;'><b>Work Email:</b></td>
									<td width='5%' style='font-size: 10px;'>".$email."</td>
								</tr>
								";
								if($this->extras->civilstatusdesc($civil_status) != "SINGLE"):
									$info .="
										<tr>
											<td width='5%' style='font-size: 10px; font-style: italic;'><b>Spouse Details:</b></td>
										</tr>
										<tr>
											<td width='5%' style='font-size: 10px;'><b>Spouse:</b></td>
											<td width='5%' style='font-size: 10px;'>".$spouse."</td>
											<td width='5%' style='font-size: 10px;'><b>Occupation:</b></td>
											<td width='5%' style='font-size: 10px;'>".$occupation."</td>
											<td width='5%' style='font-size: 10px;'><b>Contact:</b></td>
											<td width='5%' style='font-size: 10px;'>".$spouse_contact."</td>
										</tr>";
								endif;
								$info .="
								<tr>
									<td width='5%' style='font-size: 10px; font-style:italic'><b>Current Address:</b></td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Region:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->regiondesc($regaddr)."</td>
									<td width='5%' style='font-size: 10px;'><b>Province:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->provincedesc($provaddr)."</td>
									<td width='5%' style='font-size: 10px;'><b>Municipality:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->municipalitydesc($cityaddr)."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>House#:</td>
									<td width='5%' style='font-size: 10px;'>".$addr."</td>
									<td width='5%' style='font-size: 10px;'><b>Barangay:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->barangaydesc($barangay)."</td>
									<td width='5%' style='font-size: 10px;'><b>ZipCode:</td>
									<td width='5%' style='font-size: 10px;'>".$zip_code."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px; font-style: italic;'><b>Permanent Address:</b></td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>Region:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->regiondesc($permaRegion)."</td>
									<td width='5%' style='font-size: 10px;'><b>Province:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->provincedesc($permaProvince)."</td>
									<td width='5%' style='font-size: 10px;'><b>Municipality:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->municipalitydesc($permaMunicipality)."</td>
								</tr>
								<tr>
									<td width='5%' style='font-size: 10px;'><b>House#:</td>
									<td width='5%' style='font-size: 10px;'>".$permaAddress."</td>
									<td width='5%' style='font-size: 10px;'><b>Barangay:</td>
									<td width='5%' style='font-size: 10px;'>".$this->extras->barangaydesc($permaBarangay)."</td>
									<td width='5%' style='font-size: 10px;'><b>ZipCode:</td>
									<td width='5%' style='font-size: 10px;'>".$permaZipcode."</td>
								</tr>
								
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1'  style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr border='1'>
						<th colspan='3' style='font-weight:bold;text-align:left;font-size: 12px; background-color: #d2d2d2; border: solid 1px #e2e2e2;'>FAMILY MEMBERS</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Name</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Relation</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Date of Birth</th>
					</tr>
				";
				if(count($employee_family)>0){
					foreach($employee_family as $eb){
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->name."</td>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$this->extras->getrelation($eb->relation)."</td>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->bdate."</td>
					</tr>

				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm;display:none;'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='5' style='font-weight:bold;font-size: 12px;text-align:left; background-color: #d2d2d2; border: solid 1px #e2e2e2;'>CHILDREN :</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Name</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Gender</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Birth Order #</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Date of Birth</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Age</th>
					</tr>
				";
				if(count($employee_children)>0){
					foreach($employee_children as $eb){
				$info .=	"
					<tr>
						<td style='text-align:left; font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->name."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->gender."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->birthorder."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->birthdate."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->age."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='6' style='font-weight:bold;font-size: 12px;text-align:left;background-color: #d2d2d2; '>EMERGENCY CONTACT INFO</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Name</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2; border: solid 1px #e2e2e2;'>Relation</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Mobile #</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Home #</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Office #</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Type</th>
					</tr>
				";
				if(count($employee_emergencyContact)>0){
					foreach($employee_emergencyContact as $eb){
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->name."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$this->extras->getrelation($eb->relation)."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->mobile."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2; '>".$eb->homeNo."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->officeNo."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->type."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>	
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='6' style='font-weight:bold;font-size: 12px;text-align:left; background-color: #d2d2d2; border: solid 1px #e2e2e2;'>EDUCATIONAL BACKGROUND</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Name of School</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Educational Level</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Course</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Units</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Inclusive Years</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
					</tr>
				";
				if(count($educational_background)>0){
					foreach($educational_background as $eb){
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->schoolDesc."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->educ_level."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->course."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->units."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->year_graduated."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='6' style='font-weight:bold;font-size: 12px;text-align:left; background-color: #d2d2d2; border: solid 1px #e2e2e2;'>ELIGIBILITY</th>
					</tr>
					<tr>
						<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Government Examination/Professional Exam Taken</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>License No.</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Issued Date</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Expiry Date</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Remarks</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
					</tr>
				";
				if(count($eligibilities)>0){
					foreach($eligibilities as $eb){
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->description."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->license_number."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->date_issued."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->date_expired."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->remarks."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='3' style='font-weight:bold;font-size: 12px;text-align:left; background-color: #d2d2d2; border: solid 1px #e2e2e2;'>SUBJECTS COMPETENT TO TEACH</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Subject Code</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Remarks</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
					</tr>
				";
				if(count($sctt)>0){
					foreach($sctt as $eb){
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->subj_code."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->remarks."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='6' style='font-weight:bold;font-size: 12px;text-align:left; background-color: #d2d2d2; border: solid 1px #e2e2e2;'>WORK EXPERIENCE</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Position Held</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Employer</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Inclusive Years</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Latest Salary</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Reason For Leaving</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
					</tr>
				";
				if(count($work_history_related)>0){
					foreach($work_history_related as $wh){
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$wh->position."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$wh->company."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$wh->remarks."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".number_format($wh->salary,2)."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$wh->reason."</td>
						<td style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;'>".$wh->status."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' style='background-color: #d2d2d2;'>
					<tr>
						<th colspan='11' style='font-weight:bold;text-align:left;font-size: 12px; background-color: #d2d2d2'>PROFESSIONAL TRAINING AND SEMINARS</th>
					</tr>
					<tr>
						<td>
							<table  style='background-color: white; '>
								
								<tr border='1'>
									<th colspan='11' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>TA/POVEDA SPIRITUALITY</th>
								</tr>
								<table  width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;' colspan='3'>Title</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'  colspan='2'>Date</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'  colspan='2'>Organizer</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'  colspan='2'>Location</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'  colspan='2'>Status</th>
								</tr>
								";
								if(count($pts)>0){
								foreach($pts as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left; border: solid 1px #e2e2e2;'  colspan='3'>".($eb->title_id == 'others' ? $eb->other_title : $eb->title_id)."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='2'>".$eb->datef."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='2'>".$eb->organizer."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='2'>".$eb->location."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='2'>".$eb->status."</td>
								</tr>
								";
								}
								}
								$info .="
								</table>
								<br>
								<tr border='1'>
									<th colspan='11' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>PROFESSIONAL DEVELOPMENT PROGRAM</th>
								</tr>
								<table  width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px;  border: solid 1px #e2e2e2;'>Seminar Title</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Location</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Date From</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Date To</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Organizer</th>
									
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
								</tr>
								";
								if(count($pts_pdp1)>0){
								foreach($pts_pdp1 as $eb){
									if($eb->seminar_title){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left; border: solid 1px #e2e2e2;'>".$eb->seminar_title."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->location."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->datef."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->datet."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->organizer."</td>
									
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
								</tr>

							";
							}
						}
							}
							$info .="
							</table>
							<br>
								<tr border='1'>
									<th colspan='11' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>PEP DEVELOPMENT PROGRAM</th>
								</tr>
								<table  width='100%' border='1' style='text-align:center;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='3'>Title</th>
									<th width='1%' style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;' colspan='2'>Date</th>
									<th width='1%' style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;' colspan='2'>Organizer</th>
									<th width='1%' style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;' colspan='2'>Location</th>
									<th width='1%' style='font-size: 10px;text-align:center; border: solid 1px #e2e2e2;' colspan='2'>Status</th>
								</tr>
								";
								if(count($pts_pdp2)>0){
								foreach($pts_pdp2 as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left; border: solid 1px #e2e2e2;' colspan='3'>".($eb->title_id == 'others' ? $eb->other_title : $eb->title_id)."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='2'>".$eb->datef."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='2'>".$eb->organizer."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='2'>".$eb->location."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;' colspan='2'>".$eb->status."</td>
								</tr>

							";
							}
							}
							$info .="
							</table>
							<br>
								<tr border='1'>
									<th colspan='11' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>PSYCHOSOCIAL - CULTURAL</th>
								</tr>
								<table  width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='3'>Title</th>
									<th width='1%' style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='3'>Date</th>
									<th width='1%' style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='3'>Organizer</th>
									<th width='1%' style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='2'>Status</th>
								</tr>
								";
								if(count($pts_pdp3)>0){
								foreach($pts_pdp3 as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left; border: solid 1px #e2e2e2;'  colspan='3'>".($eb->title == 'others' ? $eb->other_title : $eb->title)."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='3'>".$eb->datef."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='3'>".$eb->organizer."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'  colspan='2'>".$eb->status."</td>
								</tr>

							";
							}
							}
							$info .="	
							</table>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='6' style='font-weight:bold;font-size: 12px;text-align:left;background-color: #d2d2d2; border: solid 1px #e2e2e2;'>PUBLICATION</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Type of Publication</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Title</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Publisher </th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Date Published </th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Type of Authorship </th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Status </th>
					</tr>
				";
				if(count($pgd)>0){
					foreach($pgd as $eb){
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->publication."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->title."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->publisher."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->datef."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->type."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='5' style='font-weight:bold;font-size: 12px;text-align:left;background-color: #d2d2d2; border: solid 1px #e2e2e2;'>AWARDS & RECOGNITION</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Type of Award</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Granting Agency / Org</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Place</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Date Given</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
					</tr>
				";
				if(count($ar)>0){
					foreach($ar as $eb){
						// echo "<pre>"; print_r($eb->award); die;
				$info .=	"
					<tr>
						<td style='font-size: 10px; border: solid 1px #e2e2e2;'>".$eb->award."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->institution."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->address."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->datef."</td>
						<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
					<tr>
						<th colspan='7' style='font-weight:bold;font-size: 12px;text-align:left;background-color: #d2d2d2; border: solid 1px #e2e2e2;'>SCHOLARSHIP</th>
					</tr>
					<tr>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Type of Scholarship</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Granting Agency</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Program of Study</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Institution</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>From</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>To</th>
						<th style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
					</tr>
				";
				if(count($scho)>0){
					foreach($scho as $eb){
				$info .=	"
					<tr>
						<td style='font-size: 10px; text-align:left;  border: solid 1px #e2e2e2;'>".$eb->type_of_scho."</td>
						<td style='font-size: 10px;  text-align:center; border: solid 1px #e2e2e2;'>".$eb->gr_agency."</td>
						<td style='font-size: 10px;  text-align:center; border: solid 1px #e2e2e2;'>".$eb->prog_study."</td>
						<td style='font-size: 10px;  text-align:center; border: solid 1px #e2e2e2;'>".$eb->ins_scho."</td>
						<td style='font-size: 10px; text-align:center;  border: solid 1px #e2e2e2;'>".$eb->datef."</td>
						<td style='font-size: 10px; text-align:center;  border: solid 1px #e2e2e2;'>".$eb->dateto."</td>
						<td style='font-size: 10px; text-align:center;  border: solid 1px #e2e2e2;'>".$eb->status."</td>
					</tr>
				";
					}
				}
				$info .="	</table>
			</div>
			<div class='content' style='margin-top:.5cm'>
				<table width='100%' style='background-color: #d2d2d2;'>
					<tr>
						<th colspan='5' style='font-weight:bold;text-align:left;font-size: 12px; background-color: #d2d2d2'>PROFESSIONAL INVOLVEMENTS</th>
					</tr>
					<tr>
						<td>
							<table  style='background-color: white;'>
								
								<tr border='1'>
									<th colspan='5' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>Speaking Engagements/Resource Speaker</th>
								</tr>
								<table  width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px;  border: solid 1px #e2e2e2;'>Date</th>
									<th width='1%' style='font-size: 10px;  border: solid 1px #e2e2e2;'>Topic</th>
									<th width='1%' style='font-size: 10px;  border: solid 1px #e2e2e2;'>Organizer</th>
									<th width='1%' style='font-size: 10px;  border: solid 1px #e2e2e2;'>Venue</th>
									<th width='1%' style='font-size: 10px;  border: solid 1px #e2e2e2;'>Status</th>
								</tr>
								";
								if(count($resource)>0){
								foreach($resource as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left;  border: solid 1px #e2e2e2;'>".$eb->datef."</td>
									<td style='font-size: 10px; text-align:center;  border: solid 1px #e2e2e2;'>".$eb->topic."</td>
									<td style='font-size: 10px; text-align:center;  border: solid 1px #e2e2e2;'>".$eb->organizer."</td>
									<td style='font-size: 10px; text-align:center;  border: solid 1px #e2e2e2;'>".$eb->venue."</td>
									<td style='font-size: 10px; text-align:center;  border: solid 1px #e2e2e2;'>".$eb->status."</td>
								</tr>

								";
								}
								}
								$info .="
								</table>
								<br>
								<tr border='1'>
									<th colspan='5' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>Membership in Civic Organization</th>
								</tr>
								<table  width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Name of Organization</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Date</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Position</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
								</tr>
								";
								if(count($org)>0){
								foreach($org as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left; border: solid 1px #e2e2e2;'>".$eb->name_org."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->datef."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->position."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
								</tr>

							";
							}
							}
							$info .="
							</table>
							<br>
								<tr border='1'>
									<th colspan='5' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>Community Involvement</th>
								</tr>
								<table  width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Name of Organization</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Date</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Nature of Involvement</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
								</tr>
								";
								if(count($community)>0){
								foreach($community as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left; border: solid 1px #e2e2e2;'>".$eb->school."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->year_grad."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->honor."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
								</tr>

							";
							}
							}
							$info .="
							</table>
							<br>
								<tr border='1'>
									<th colspan='5' style='font-weight:bold;text-align:left;font-style: italic;font-size: 11px;'>Positions Held In Poveda</th>
								</tr>
								<table  width='100%' border='1' style='text-align:left;border-collapse:collapse;font-size: 12px;page-break-inside:avoid; border: solid 1px #e2e2e2;'>
								<tr>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;' colspan='2'>Position</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Department</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Date</th>
									<th width='1%' style='font-size: 10px; border: solid 1px #e2e2e2;'>Status</th>
								</tr>
								";
								if(count($administrative)>0){
								foreach($administrative as $eb){
								$info .=	"
								<tr>
									<td style='font-size: 10px; text-align:left; border: solid 1px #e2e2e2;'>".$eb->positionf."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->department."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->datef."</td>
									<td style='font-size: 10px; text-align:center; border: solid 1px #e2e2e2;'>".$eb->status."</td>
								</tr>

							";
							}
							}
							$info .="	
							</table>
							</table>
						</td>
					</tr>
				</table>
			</div>
	";
	$pdf->WriteHTML($info);

	$pdf->Output();
?>


