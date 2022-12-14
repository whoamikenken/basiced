<?php

/**
 * @author Justin
 * @copyright 2016
 */
 $imgurl = __DIR__."/../../../";
 //var_dump($imgurl, file_exists($imgurl));die;
 
$res = $this->employee->loadallemployee(array("employeeid"=>$id));
$empdetails = $res[0];
$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,5);
$pdf->Bookmark('Start of the document');
$rc = 5;
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
    $cityaddr = $empdetails['cityaddr'];
    $provaddr = $empdetails['provaddr'];
    $occupation = $empdetails['occupation'];
    $age    = $empdetails['age'];
    $gender = $empdetails['gender'];
    $civil_status = $empdetails['civil_status'];
    $spouse = $empdetails['spouse_name'];
    $bdate = date("F d, Y",strtotime($empdetails['bdate']));
    $mobile = $empdetails['mobile'];
    $citytelno = $empdetails['citytelno'];
    $email = $empdetails['email'];
    $employmentstat = $empdetails['employmentstat'];
    $emptype = $empdetails['emptype'];
    $empshift = $empdetails['empshift'];
    $dateemployed = isset($empdetails['dateemployed']) ? $empdetails['dateemployed'] : "";
    $maxregular = $empdetails['maxregular'];
    $maxparttime = $empdetails['maxparttime'];
    $bplace = $empdetails['bplace'];
    $deptid = $empdetails['deptid'];
    $leavetype = $empdetails['leavetype'];
    
    $position = $empdetails['position'];
    $datepos = (!empty($empdetails['dateposition']) && $empdetails['dateposition'] != "0000-00-00" && $empdetails['dateposition'] != "1970-01-01") ? date("Y-m-d",strtotime($empdetails['dateposition'])) : "";
    $assignment = $empdetails['assignment'];
    $remarks = $empdetails['remarks'];
    $management = $empdetails['management'];
    $dateresigned = $empdetails['dateresigned'] ? $empdetails['dateresigned'] : NULL;
    $resigned_reason = $empdetails['resigned_reason'];
    $tinno = $empdetails['tinno'];
    $sssno = $empdetails['sssno'];
    $philhealth = $empdetails['philhealth'];
    $pagibig = $empdetails['pagibig'];
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
 }
 
$iquery = $this->db->query("SELECT * FROM elfinder_file where title='$employeeid'");
$img = ($iquery->num_rows() > 0 ? "<img src='".site_url('forms/loadForm')."?form=imgview&eid={$empdetails["employeeid"]}' height='120px' width='120px'/>" : "<img src='".base_url()."images/no_image.gif' height='120px' width='120px'/>");
$info  = "  <style>
                .content{
                    height: 100%;
                }
                .content-header{
                    padding: 4px;
                    /*
                    background: #823982;
                    color: #FFC700;
                    */
                    border: 1px solid black;
                    background: #00FFFF;
                    text-align: center;
                    font-size: 12px;
                }
                .content-body{
                    border: 1px solid black;
                    padding-top: 8px;
                    padding-bottom: 8px;
                    padding-left: 8px;
                }
            </style>";
			// commented to show logo via direct path : base_url()."images/school_logo.jpg
$info .= "
<body style='font-family:calibri;'>	
    <div>
        <table width='63.5%' style='margin-left: 150px;padding: 0;'>
                    <tr>
                        <td rowspan='2' width='30%' style='text-align: right;'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                        <td valign='bottom' style='padding: 0;text-align: center;'><h4 style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></h4></td>
                    </tr>
                    <tr>
                        <td valign='baseline' style='padding: 0;text-align: center;'><h5 style='font-size: 10px;'><strong>Poblacion, Dasmari??as City, Cavite</strong></h5></td>
                    </tr>
                </table>
    </div>
    <div class='content'>
        <!--
    	<div class='content-header'>
            <span><strong>GENERAL INFORMATION</strong></span>
    	</div>
        <div class='content-body'>
            <table width='100%' style='font-size: 13px;'>
                <tr>
                    <td width='25%' align='right'>Employee ID : </td>
                    <td width='40%' style='padding-left: 10px'>{$empdetails["employeeid"]}</td>
                    <td rowspan=4 align='center'>$img</td>
                </tr>
                <tr>
                    <td width='25%' align='right'>First Name : </td>
                    <td width='40%' style='padding-left: 10px'>".strtoupper($empdetails["fname"])."</td>
                </tr>
                <tr>
                    <td width='25%' align='right'>Last Name : </td>
                    <td width='40%' style='padding-left: 10px'>".strtoupper($empdetails["lname"])."</td>
                </tr>
                <tr>
                    <td width='25%' align='right'>Middle Name : </td>
                    <td width='40%' style='padding-left: 10px'>".strtoupper($empdetails["mname"])."</td>
                </tr>
            </table>
        </div>
        -->
        <!--
        <div class='content-header'>
            <span><strong>Identification Numbers</strong></span>
    	</div>
        <div class='content-body'>
            <table width='100%' style='font-size: 13px;'>
                <tr>
                    <td width='16%' align='right'>TIN # : </td>
                    <td width='16%' style='padding-left: 10px'>{$tinno}</td>
                    <td width='16%' align='right'>SSS # : </td>
                    <td width='16%' style='padding-left: 10px'>{$sssno}</td>
                    <td width='16%' align='right'>PhilHealth # : </td>
                    <td width='20%' style='padding-left: 10px'>{$philhealth}</td>
                </tr>
                <tr>
                    <td width='16%' align='right'>PAG-IBIG # : </td>
                    <td width='16%' style='padding-left: 10px'>{$pagibig}</td>
                    <td width='16%' align='right'>HMO # : </td>
                    <td width='16%' style='padding-left: 10px'>{$medicare}</td>
                    <td width='16%' align='right'>PRC # : </td>
                    <td width='20%' style='padding-left: 10px'>{$prc}</td>
                </tr>
                <tr>
                    <td width='16%' align='right'>Account No. : </td>
                    <td width='16%' style='padding-left: 10px'>{$emp_accno}</td>
                </tr>
            </table>
        </div>
        -->
        <div class='content-header'>
            <div style='font-size: 15px;'><strong>PERSONNEL UPDATE FORM</strong></div>
            <div><strong>Personal Information</strong></div>
    	</div>
        <div class='content-body'>
            <table width='100%' style='font-size: 13px;border-spacing: 5px;'>
                <tr>
                    <td width='16%' align='right'>Name : </td>
                    <td colspan=5>
                        <table width='100%' style='font-size: 13px;border-collapse: collapse;'>
                            <tr>
                                <td style='width:210px;padding-left: 6px;border: 1px solid black;border-right: 0;' >".strtoupper($empdetails["fname"])."</td>
                                <td style='width:210px;padding-left: 16px;border: 1px solid black;border-right: 0;border-left: 0;' >".strtoupper($empdetails["lname"])."</td>
                                <td style='width:210px;padding-left: 16px;border: 1px solid black;border-left: 0;' >".strtoupper($empdetails["mname"])."</td>
                            </tr>
                        </table>
                    </td>                    
                </tr>                
                <tr>
                    <td colspan=6>
                        <table width='100%' style='font-size: 12px;'>
                            <tr>
                                <td style='width:130px;'></td>
                                <td style='width:210px;padding-left: 10px;' valign='top' >(Surname)</td>
                                <td style='width:210px;padding-left: 10px;' >(Given Name)</td>
                                <td style='width:210px;padding-left: 10px;' >(Middle Name)</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td width='16%' align='right'>Age : </td>
                    <td width='20%'>
                        <table width='100%' style='font-size: 13px;border-collapse: collapse'>
                            <tr>
                                <td style='width:60px;padding-left: 10px;border: 1px solid black;'>{$age}</td>
                                <td style='width:60px;padding-right: 5px;text-align: right;'>Sex :</td>
                                <td style='width:66px;border: 1px solid black;'>".$this->extras->genderdesc($gender)."</td>
                            </tr>
                        </table>
                    </td>
                    <td width='16%' align='right'>Civil Status : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;'>".$this->extras->civilstatusdesc($civil_status)."</td>
                    <td width='16%' align='right'>Citizenship : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;'>".$this->extras->citizenshipdesc($civil_status)."</td>
                    <td width='1%'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='16%' align='right'>Date of Birth : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;'>{$bdate}</td>
                    <td width='16%' align='right'>Place of Birth : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;'>{$bplace}</td>
                    <td width='16%' align='right'>Contact Number : </td>
                    <td width='20%' style='padding-left: 10px;border: 1px solid black;'>{$mobile}</td>
                </tr>
                <tr>
                    <td width='16%' align='right'>City Address :</td>
                    <td colspan=5 style='padding-left: 10px;border: 1px solid black;'>{$cityaddr}</td>
                </tr>
                <tr>
                    <td width='16%' align='right'>Provincial Address :</td>
                    <td colspan=5 style='padding-left: 10px;border: 1px solid black;'>{$provaddr}</td>
                </tr>
                <!--
                <tr>
                    <td width='16%' align='right'>Religion : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;'>".$this->extras->religiondesc($religion)."</td>
                    <td width='16%' align='right'>Nationality : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;'>".$this->extras->nationalitydesc($nationality)."</td>
                </tr>
                -->
                <tr>
                    <td width='16%' align='right'>Name of Spouse : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;'>{$spouse}</td>
                    <td width='16%' align='right'>Occupation : </td>
                    <td width='20%' style='padding-left: 10px;border: 1px solid black;'>{$occupation}</td>
                </tr>
                <tr>
                    <td width='16%' align='left' colspan=2><b>Number of Children : </b></td>
                </tr>
            </table>
            <table width='90%' style='font-size: 12px;border-collapse: collapse;'>
                <tr>
                    <td width='17.5%' style='border: 0;'>&nbsp;</td>
                    <td width='52%' align='center'>Name</td>
                    <td width='20%' align='center'>Date of Birth</td>
                    <td width='20%' align='center'>Age</td>
                </tr>
                ";
                $employee_child = $this->db->query("select * from employee_children where employeeid='$employeeid'")->result();
                if(count($employee_child)>0){
                    foreach($employee_child as $eb){
                        $info .= "<tr>
                                    <td width='17.5%' style='border: 0;'>&nbsp;</td>
                                    <td width='52%' align='center' style='border: 1px solid black;'>".$eb->name."</td>
                                    <td width='20%' align='center' style='border: 1px solid black;'>".date("F d, Y",strtotime($eb->birthdate))."</td>
                                    <td width='20%' align='center' style='border: 1px solid black;'>".$eb->age."</td>
                                  </tr>  
                                 ";
                    }
                }
    $info .= "
            </table>
            <table width='90%' style='font-size: 12px;border-collapse: collapse;'>
                <tr>
                    <td width='16%' align='center'><b>Parents : </b></td>
                </tr>
                <tr>
                    <td width='17.5%' align='right'>Mother : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;' colspan=2>{$mother}</td>
                    <td width='16%' align='right'>Occupation : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;' colspan=2>{$motheroccu}</td>
                </tr>
                <tr>
                    <td width='16%' align='right'>Father : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;' colspan=2>{$father}</td>
                    <td width='16%' align='right'>Occupation : </td>
                    <td width='16%' style='padding-left: 10px;border: 1px solid black;' colspan=2>{$fatheroccu}</td>
                </tr>
            </table>
    </div>
        <div class='content-header'>
            <span><strong>Confidential</strong></span>
    	</div>
        <div class='content-body'>
            <table width='100%' style='font-size: 13px;'>
                <tr>
                    <td width='16%' align='center'><b>Health : </b></td>
                    <td width='80%' align='center'>&nbsp;</b></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>a.) Have you ever been hospitalized in the past 2 years? <img src='".$imgurl."images/".($hosp == 1 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> Yes <img src='".$imgurl."images/".($hosp == 2 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> No</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If yes, for what sickness? <input value='$hosptxt' size='100' readonly></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>b.) Have you undergone any operation? <img src='".$imgurl."images/".($operation == 1 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> Yes <img src='".$imgurl."images/".($operation == 2 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> No</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If yes, for what sickness? <input value='$operationtxt' size='70' readonly> When? <input value='$operationdate' size='20' readonly></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td> c.) Do you have any present or past medical history which will involve special consideration as to job assignment? <img src='".$imgurl."images/".($medhistory == 1 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> Yes <img src='".$imgurl."images/".($medhistory == 2 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> No</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If so, indicate the condition? <input value='$medhistorytxt' size='95' readonly></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td> d.) Check any of these conditions you have or have had:</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='".$imgurl."images/".(in_array(1,explode(",",$medconditions)) == 1 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> Allergic Disorders (Asthma,fever,hives) </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='".$imgurl."images/".(in_array(2,explode(",",$medconditions)) == 1 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> Cardiovascular conditions (Elevated blood pressure,anemia,heart,abnormalities)  </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='".$imgurl."images/".(in_array(3,explode(",",$medconditions)) == 1 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> Gastrointestinal problems (ulcers,liver desease, browel problems)  </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='".$imgurl."images/".(in_array(4,explode(",",$medconditions)) == 1 ? "checkedbox" : "blankbox").".jpg' height='10px' width='10px'/> Musculoskeletal (fractured bone,disc or joint problems)  </td>
                </tr>
            </table>
        </div>
        <!-- Educational Background --> 
    	<div class='content-header'>
            <span><strong>Educational Background</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,minor,year_graduated from employee_education where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->minor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr>
                                            <td width='25%' align='center'>&nbsp;</td>
                                            <td width='25%' align='center'></td>
                                            <td width='25%' align='center'></td>
                                            <td width='25%' align='center'></td>
                                          </tr>  
                                         ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr>
                                    <td width='25%' align='center'>&nbsp;</td>
                                    <td width='25%' align='center'></td>
                                    <td width='25%' align='center'></td>
                                    <td width='25%' align='center'></td>
                                  </tr>  
                                 ";
                    }
                }
    $info .= "
             </table>
        </div>
        
    </div>
    <div class='content'>
        
        <!-- Eligibility --> 
    	<div class='content-header'>
            <span><strong>Eligibility</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr>
                    <th width='50%' align='center'>Government Examination/Professional Exam Taken</th>
                    <th width='20%' align='center'>Rate</th>
                    <th width='30%' align='center'>Date and Place Taken</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select date_issued,description,affiliating_center from employee_eligibilities where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='50%' align='center'>".$row->description."</td>
                                    <td width='20%' align='center'>".$row->affiliating_center."</td>
                                    <td width='30%' align='center'>".$row->date_issued."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'>&nbsp;</td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
        <br />
        <!-- Work Experience --> 
    	<div class='content-header'>
            <span><strong>Work Experience</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='7' align='center'><h4><b>Teaching Experience in Unrelated Disciplines</b><h4></td></tr>
                <tr>
                    <th width='15%' align='center' rowspan=2>Position Held</th>
                    <th width='19%' align='center' rowspan=2>Company Name</th>
                    <th width='20%' align='center' rowspan=2>Address</th>
                    <th width='10%' align='center' rowspan=2>Contact Number</th>
                    <th width='10%' align='center' rowspan=2>Salary</th>
                    <th width='26%' align='center' colspan=2>Inclusive Date of Employment</th>
                </tr>
                <tr>
                    <th align='center'>From</th>
                    <th align='center'>To</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select date_from,date_to,position,company,address,contactnumber,salary from employee_work_history_unrelated where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='15%' align='center'>".$row->position."</td>
                                    <td width='25%' align='center'>".$row->company."</td>
                                    <td width='20%' align='center'>".$row->address."</td>
                                    <td width='10%' align='center'>".$row->contactnumber."</td>
                                    <td width='10%' align='center'>".$row->salary."</td>
                                    <td width='13%' align='center'>".date("M. d, Y",strtotime($row->date_from))."</td>
                                    <td width='13%' align='center'>".date("M. d, Y",strtotime($row->date_to))."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>  ";
                    }
                }
    $info .= "
             </table>
             <br />
             <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='7' align='center'><h4><b>Teaching Experience in Related Disciplines</b><h4></td></tr>
                <tr>
                    <th width='15%' align='center' rowspan=2>Position Held</th>
                    <th width='19%' align='center' rowspan=2>Company Name</th>
                    <th width='20%' align='center' rowspan=2>Address</th>
                    <th width='10%' align='center' rowspan=2>Contact Number</th>
                    <th width='10%' align='center' rowspan=2>Salary</th>
                    <th width='26%' align='center' colspan=2>Inclusive Date of Employment</th>
                </tr>
                <tr>
                    <th align='center'>From</th>
                    <th align='center'>To</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select date_from,date_to,position,company,address,contactnumber,salary from employee_work_history_related where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='15%' align='center'>".$row->position."</td>
                                    <td width='25%' align='center'>".$row->company."</td>
                                    <td width='20%' align='center'>".$row->address."</td>
                                    <td width='10%' align='center'>".$row->contactnumber."</td>
                                    <td width='10%' align='center'>".$row->salary."</td>
                                    <td width='13%' align='center'>".date("M. d, Y",strtotime($row->date_from))."</td>
                                    <td width='13%' align='center'>".date("M. d, Y",strtotime($row->date_to))."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>  ";
                    }
                }
    $info .= "
             </table>
             <br />
             <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='7' align='center'><h4><b>Other Professional Experiences</b><h4></td></tr>
                <tr>
                    <th width='15%' align='center' rowspan=2>Position Held</th>
                    <th width='19%' align='center' rowspan=2>Company Name</th>
                    <th width='20%' align='center' rowspan=2>Address</th>
                    <th width='10%' align='center' rowspan=2>Contact Number</th>
                    <th width='10%' align='center' rowspan=2>Salary</th>
                    <th width='26%' align='center' colspan=2>Inclusive Date of Employment</th>
                </tr>
                <tr>
                    <th align='center'>From</th>
                    <th align='center'>To</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select date_from,date_to,position,company,address,contactnumber,salary from employee_work_history where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='15%' align='center'>".$row->position."</td>
                                    <td width='25%' align='center'>".$row->company."</td>
                                    <td width='20%' align='center'>".$row->address."</td>
                                    <td width='10%' align='center'>".$row->contactnumber."</td>
                                    <td width='10%' align='center'>".$row->salary."</td>
                                    <td width='13%' align='center'>".date("M. d, Y",strtotime($row->date_from))."</td>
                                    <td width='13%' align='center'>".date("M. d, Y",strtotime($row->date_to))."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
        <br />
        <!-- Professional Growth & Development --> 
    	<div class='content-header'>
            <span><strong>Professional Growth & Development</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr>
                    <th width='25%' align='center'>Type of Publication</th>
                    <th width='25%' align='center'>Title</th>
                    <th width='25%' align='center'>Type of Authorship</th>
                    <th width='25%' align='center'>Date Published</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select publication,title,datef,type from employee_pgd where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->publication."</td>
                                    <td width='25%' align='center'>".$row->title."</td>
                                    <td width='25%' align='center'>".$row->type."</td>
                                    <td width='25%' align='center'>".$row->datef."</td>
                                  </tr>
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
        <br />
        <!-- Researches --> 
    	<div class='content-header'>
            <span><strong>Researches</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,honor,year_graduated from employee_researches where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->minor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
    </div>
    <div class='content'>
        <!-- Awards & Recognition --> 
    	<div class='content-header'>
            <span><strong>Awards & Recognition</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr>
                    <th width='25%' align='center'>Type of Publication</th>
                    <th width='25%' align='center'>Title</th>
                    <th width='25%' align='center'>Type of Authorship</th>
                    <th width='25%' align='center'>Date Published</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select publication,title,datef,type from employee_awardsrecog where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->publication."</td>
                                    <td width='25%' align='center'>".$row->title."</td>
                                    <td width='25%' align='center'>".$row->type."</td>
                                    <td width='25%' align='center'>".$row->datef."</td>
                                  </tr>
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
        <br />
        <!-- Scholarship --> 
    	<div class='content-header'>
            <span><strong>Scholarship</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr>
                    <th width='25%' align='center'>Type of Publication</th>
                    <th width='25%' align='center'>Title</th>
                    <th width='25%' align='center'>Type of Authorship</th>
                    <th width='25%' align='center'>Date Published</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select publication,title,datef,type from employee_scholarship where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->publication."</td>
                                    <td width='25%' align='center'>".$row->title."</td>
                                    <td width='25%' align='center'>".$row->type."</td>
                                    <td width='25%' align='center'>".$row->datef."</td>
                                  </tr>
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
        <br />
        <!-- Professional Involvements --> 
    	<div class='content-header'>
            <span><strong>Professional Involvements</strong></span>
    	</div>
        <div class='content-body' style='padding: 0;'>           
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='4' align='center'><h4><b>Seminar/Conventions/Conferences (Related to field of expertise)</b><h4></td></tr>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,honor,year_graduated from employee_scs where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->honor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        <br />        
        <!-- Trainings/Workshops (Related to field ot expertise) --> 
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='4' align='center'><h4><b>Trainings/Workshops (Related to field ot expertise)</b><h4></td></tr>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,honor,year_graduated from employee_workshops where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->honor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
             <br />
        <!-- Speaking Engagements/Resource Speaker --> 
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='4' align='center'><h4><b>Speaking Engagements/Resource Speaker</b><h4></td></tr>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,honor,year_graduated from employee_resource where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->honor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        <!-- Membership or Affiliation in Professional Organization --> 
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='4' align='center'><h4><b>Membership or Affiliation in Professional Organization</b><h4></td></tr>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,honor,year_graduated from employee_proorg where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->honor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= 5){
                            for($x; $x<=5; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=5; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
    </div>
    <div class='content'>   
        <div class='content-body' style='padding: 0;'>          
        <!-- Membership in Civic Organizations/Community Involvement --> 
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='4' align='center'><h4><b>Membership in Civic Organizations/Community Involvement</b><h4></td></tr>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,honor,year_graduated from employee_community where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->honor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
             <br />
        <!-- Administrative Functions Handled --> 
            <table width='100%' style='font-size: 13px;border-collapse: collapse;' border=1>
                <tr style='background: #00FFFF;'><td colspan='4' align='center'><h4><b>Administrative Functions Handled</b><h4></td></tr>
                <tr>
                    <th width='25%' align='center'>Name of School</th>
                    <th width='25%' align='center'>Educational Level</th>
                    <th width='25%' align='center'>Year Graduated</th>
                    <th width='25%' align='center'>Honors</th>
                </tr>
                ";
                $x = 1;
                $query = $this->db->query("select educational_level,school,honor,year_graduated from employee_administrative where employeeid='{$employeeid}'")->result();
                if(count($query)>0){
                    foreach($query as $row){
                        $x++;
                        $info .= "<tr>
                                    <td width='25%' align='center'>".$row->school."</td>
                                    <td width='25%' align='center'>".$row->educational_level."</td>
                                    <td width='25%' align='center'>".$row->year_graduated."</td>
                                    <td width='25%' align='center'>".$row->honor."</td>
                                  </tr>  
                                 ";
                    }
                        if($x <= $rc){
                            for($x; $x<=$rc; $x++){
                                $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                            }
                        }
                }else{
                    for($x; $x<=$rc; $x++){
                        $info .= "<tr><td width='25%' align='center'>&nbsp;</td><td width='25%' align='center'></td><td width='25%' align='center'></td><td width='25%' align='center'></td></tr>  ";
                    }
                }
    $info .= "
             </table>
        </div>
        <div style='padding: 10px;font-size: 13px;'>
        I hereby certify that the above statements information are true and correct, and I am fully aware that any
        falsification or misrepresentation herein constitutes sufficient ground for my immediate separation from service.
        <br />
        <br />
        _____________________________________<br />
        Name and signature of employee &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        Date accomplished: ______________________________<br /><br />
        _____________________________________<br />
        Department
        </div>
     </div>
     
</body>";
$pdf->WriteHTML($info);

$pdf->Output();
// end of file
?>


