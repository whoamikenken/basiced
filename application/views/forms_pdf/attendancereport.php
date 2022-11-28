<?php
if($this->input->get()) exit('No direct script access allowed');
// http://192.168.2.97/icadtr/index.php/forms/loadForm?form=attendancereport&cdate=2018-10-10,2018-10-23&tnt=teaching&employeeid=&sort=0&campus=&category=
ini_set('memory_limit',-1);
set_time_limit(0);
/**
Modified by : Glen Mark 2018
**/
$CI =& get_instance();
$CI->load->model('payrollprocess');
$CI->load->model('payroll');
$CI->load->model('attendance');
$CI->load->model('extras');
// include "application/config/connection.php";
// include "application/views/forms_pdf/function/payrollfunc.php";



// function formatAmount($amount=''){
//     if($amount){
//         if($amount < 0) {
//             $amount = $amount * -1;
//             $amount = number_format( $amount, 2 );
//             $amount = '(' . $amount . ')';
//         }else{
//             $amount = number_format( $amount, 2 );
//         }
//     }else{
//         $amount = '0.00';
//     }
//     return $amount;
// }


if(!$_GET["cdate"]) die;
$cdate        = $_GET['cdate']; 
#echo '<pre>';var_dump($cdate);die;
$cdateexplode = explode(',', $cdate);
#echo '<pre>';var_dump($cdateexplode);die;
#echo $cdateexplode[0];die;
$month = date("F",$cdateexplode[0]);

$fromMonth = date("F",strtotime($cdateexplode[0]));
$toMonth = date("F",strtotime($cdateexplode[1]));

#echo $fromMonth .'- ' .$toMonth;die;

$from = date("j",strtotime($cdateexplode[0]));
$to = date("j",strtotime($cdateexplode[1]));

$fromYear = date("Y",strtotime($cdateexplode[0]));
$toYear = date("Y",strtotime($cdateexplode[1]));

$year = date("Y",strtotime($cdateexplode[0]));
$from_date = $cdateexplode[0];
$to_date = $cdateexplode[1];
$tnt       = $_GET['tnt'];
$employeeid 	=$_GET['employeeid'];
$dateRange = $CI->time->createRangeToDisplay($from_date, $to_date);
$departments = $CI->extras->showdepartment();
$category = $_GET['category'];
$campus = $_GET['campus'];
// echo $category." - ".$campus;die;


$mpdf = new mPDF('utf-8','LEGAL-L','10','','3','3','33','10','9','9');
/*$mpdf->SetHTMLHeader("<table class='header'>
		<tr>
		<td><img src='images/school_logo.jpg' style='width: 80px;'/></td>
		<td style='color:blue;'><b style='align_center'><h1>Pinnacle Technologies Inc.</h1></b>
		<b>Attendance Cut-Off : ".$month." ".$from." - ".$to.", ".$year." </b></td>
		</tr>
		</table>",'',false);*/

$mpdf->SetHTMLHeader("<table class='header'>
	<tr>
	<td><img src='images/school_logo.jpg' style='width: 80px;'/></td>
	<td style='color:blue;'><b style='align_center'><h1>Pinnacle Technologies Inc.</h1></b>
	<b>Attendance Cut-Off : ".$fromMonth." ".$from." ".$fromYear." - "." ".$toMonth." ".$to.", ".$toYear." </b></td>
	</tr>
	</table>",'',false);
// echo $tnt;die;
if ($tnt == "teaching") {
	
	
	$content .= '<table width="100%" id="asctblnt" border=1 >
				<thead>
					<tr>
					  <th rowspan="2">Employee ID</th>
					  <th rowspan="2" width="30%">Name</th>
					  <!--<th rowspan="2">Overload</th>-->
			          <th  colspan="3">Overtime (hr:min)</th>
			          <th  rowspan="2">Late (hr:min)</th>
			          <th  rowspan="2">Undertime (hr:min)</th>
			          <!--<th  rowspan="2">Absent</th>-->
			          <th  colspan="3">Leaves</th>
			          <th  rowspan="2">Service Credit</th>
			          <th  rowspan="2">Total Deduction (day/s)</th>
			          <th rowspan="2" width="10%">Signature </th>
			          <!--<th  colspan="3" > Work Hours </th>-->
			      </tr>
			      <tr>
			          <th >Regular</th>
			          <th >Rest Day</th>
			          <th >Holiday</th>
			          <!--<th >Lec</th>
			          <th >Lab</th>
			          <th >Admin</th>
			          <th >Lec</th>
			          <th >Lab</th>
			          <th >Admin</th>
			          <th >Subject</th>
			          <th >Emergency</th>-->
			          <th >Vacation</th>
			          <th >Sick</th>
			          <th >Other(s)</th>
			          <!--<th >Lec</th>
			          <th >Lab</th>
			          <th >Admin</th>-->
			          <!--<th > Lec</th>
			          <th > Lab</th>
			          <th > Admin</th>-->
			      </tr>
				</thead>
				<tbody>
			';
			$result = $this->attendance->emp_confirmedsorting($from_date, $to_date, $tnt,$employeeid,$category,$campus);
			#$result = $this->attendance->emp_confirmed($from_date, $to_date, $tnt,$employeeid);
			if (count($result) > 0) {
				$workhours_lec = $data["workhours_lec"];
				$workhours_lab = $data["workhours_lab"];
				$workhours_admin = $data["workhours_admin"];
				$fixedday = $data["fixedday"];
				$wLec = $fixedday?'':$workhours_lec;
				$wLab = $fixedday?'':$workhours_lab;
				$wAdmin = $fixedday?'':$workhours_admin;
				$campusid = "";
				

				foreach ($result as $key => $data) 
				{

					 	$totUndertime = $this->attcompute->exp_time($data['utadmin']) + $this->attcompute->exp_time($data['utlec']) + $this->attcompute->exp_time($data['utlab']);
					 	$totLate = $this->attcompute->exp_time($data['latelec']) + $this->attcompute->exp_time($data['latelab']) + $this->attcompute->exp_time($data['lateadmin']);
					 	$totDeduction = $this->attcompute->exp_time($data['deducadmin']) + $this->attcompute->exp_time($data['deduclec']) + $this->attcompute->exp_time($data['deduclab']);
					 	$totDeduction = $totDeduction / (8 * 3600);
				        /** HYPERION21587 **/
				        /** PAULO04-26-2018 **/
				        //Service Credit 
				        $service_credit = array();
				        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
				        foreach ($qdate as $rdate) {
		    				$sched = $this->attcompute->displaySched($empid,$rdate->dte);
		    				$countrow = $sched->num_rows();
		    				$isValidSchedule = true;
		    				if($countrow > 0){
						    	if($sched->row(0)->starttime == "00:00:00" && $sched->row(0)->endtime == "00:00:00") $isValidSchedule = false;
						    }
						    if($countrow > 0 && $isValidSchedule){
						    	foreach($sched->result() as $rsched){
							        $stime = $rsched->starttime;
							        $etime = $rsched->endtime; 
									$service_credit[] = $this->attcompute->displayServiceCredit($data["qEmpId"],$stime,$etime,$rdate->dte);
						    	}
						    }
				        }
	        			/** END OF HYPERION21587 **/
	        			
					 	// echo $this->attcompute->sec_to_hm($totUnder);die;
					  if ($campusid != $data['campusid'] && $category == "campus") {
					  	$content .= '	<tr class="pdept">
					  						<td colspan="8"><b><p>Department: '.$data["campusid"].'</p></b></td>
					  					</tr>';
					  }
					  $content .='
			  		    <tr>
			  		        <td>'.$data["qEmpId"].'</td>
			  		        <td>'.$data["qFullname"].'</td>
			  		        <!--<td>'.$data["overload"].'</td>-->
			  		        <td>'.$data["otreg"].'</td>
			  		        <td>'.$data["otrest"].'</td>
			  		        <td align="center">'.$data["othol"].'</td>
			  		        <td align="center">'.$this->attcompute->sec_to_hm($totLate).'</td>
			  		        <!--<td align="center">'.$data["latelab"].'</td>
			  		        <td align="center">'.$data["lateadmin"].'</td>-->
			  		        <td align="center">'.$this->attcompute->sec_to_hm($totUndertime).'</td>
			  		        <!--<td align="center">'.$data["absent"].'</td>
			  		        <td align="center">'.$data["utlab"].'</td>
			  		        <td align="center">'.$data["utadmin"].'</td>
			  		        <td align="center">'.$data["absent"].'</td>
			  		        <td align="center">'.$data["eleave"].'</td>-->
			  		        <td align="center">'.$data["vleave"].'</td>
			  		        <td align="center">'.$data["sleave"].'</td>
			  		        <td align="center">'.$data["oleave"].'</td>
			  		        <!--<td align="center">'.$data["deduclec"].'</td>
			  		        <td align="center">'.$data["deduclab"].'</td>-->
			  		        <td align="center">'.array_sum($service_credit).'</td>
			  		        <!-- <td align="center">'.$this->attcompute->sec_to_hm($totDeduction).'</td>-->
			  		        <td align="center">'.$totDeduction.'</td>
			  		       <!-- <td align="center">'.$wLec.'</td>
			  		        <td align="center">'.$wLab.'</td>
			  		        <td align="center">'.$wAdmin.'</td>-->
			  		        <td></td>
			  		        
			  			</tr>';
			  		$campusid = $data['campusid'];
			     }

			  				
			 $content .="</tbody></table>";
			 }
}//end of teaching
else
{

	$content .=' <table width="100%" id="asctblnt" border=1 >
        <thead>
            <tr>
                <th rowspan="2">Employee ID</th>
  			    <th rowspan="2">Name</th>
                <th  colspan="3">Overtime (hr:min)</th>
                <th >Late</th>
                <th >Undertime</th>
                <th rowspan="2">Absent</th>                        
                <th colspan="3">Leaves</th>
                <th rowspan="2" >No. of Days</th>
                <th rowspan="2" >Holiday</th>
                <th rowspan="2" width="10%"> Signature</th>
            </tr>
            <tr>
                <th >Regular</th>
                <th>Rest Day</th>
                <th >Holiday</th>
                <th >Hr:min</th>            
                <th >Hr:min</th>            
                <th >Emergency</th>
                <th >Vacation</th>
                <th >Sick</th>
            </tr>
        </thead>
      	<tbody>';
  	  $result = $this->attendance->emp_confirmed_nt($from_date, $to_date, $tnt,$employeeid);

  	 
  	   if (count($result) > 0) {
    	
    	foreach ($result as $key => $data) {
            $workdays = $data['workdays'];
            $fixedday = $data['fixedday'];
    		$noDays = $fixedday?'':$workdays;
    		$tabsent = "";
    		if($data["absent"]){
                $tabsent = number_format(($this->attcompute->exp_time($data["absent"]) / (8 *3600)),2);
            }
          $content .='<tr >
    		<td >'.$data["qEmpId"].'</td>
    		<td >'.$data["qFullname"].'</td>
    		<td align="center">'.$data["otreg"].'</td>
    		<td align="center">'.$data["otrest"].'</td>
    		<td align="center">'.$data["othol"].'</td>
    		<td align="center">'.$data["lateut"].'</td>
    		<td align="center">'.$data["ut"].'</td>
    		<td align="center">'.$tabsent.'</td>
    		<td align="center">'.$data["eleave"].'</td>
    		<td align="center">'.$data["vleave"].'</td>
    		<td align="center">'.$data["oleave"].'</td>
    		<td align="center">'.$noDays.'</td>
    		<td align="center" >'.$data["isholiday"].'</td>    		
    		<td></td>
    	</tr>';
       
        }
         
    }
     $content .="</tbody></table>";
}


$html = "
		<style>
		#attstbl tr th,#asctblnt tr th{
		    background-color: #3C8DBC;
		    color: white;
		   

		}
		#asctblnt
		{
			 border-collapse: collapse;
			 border-width:thin;
		}
		.header
		{
		 
		 position:absolute;
		 font-size:12px;
		 font-family:calibri;
		}
		
		</style>
		<body>".$content."</body>
		";

$mpdf->WriteHTML($html);
$mpdf->Output();
die;

?>
