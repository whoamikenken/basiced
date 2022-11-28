<?php
/**
 * @author Justin
 * @copyright 2015
 */
 
$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
$xls = New Spreadsheet_Excel_Writer();
$xls->send("Tardiness Report.xls");

// importing the fonts configuration file
require 'excel_fonts.php';    

$dateRange = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;
$edata = $edata;
$pass = $this->extras->setPass();

$dateRange      = $this->time->createRangeToDisplay($from_date, $to_date);
$departments    = $this->extras->showdepartment();
$result         = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);

$isFlexi = false;
$flexi = $this->employee->getindividualemployee($empid);
foreach($flexi as $row)    $isFlexi = (bool)$row->isFlexi;

    $leftSubHeaders = array(
        array(
            'headerVal' => "EMPLOYEE ID",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "EMPLOYEE NAME",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Late Frequency",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Thursday Late Frequency",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Minutes Late",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Early Dismissal",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Halfday",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Whole Day Absences",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Total Absences",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Failure to Log In/Out",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "10 day attendance Bonus Balance",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Overtime",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        )
    );
    $colsMerge = count($leftSubHeaders) - 1;

    $mainHeaders = array(
        array(
            'headerVal' => "Saint Jude Catholic School",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "327 Ycaza Street, San Miguel Manila",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "PAYROLL DEDUCTIONS",
            'headerFont' => $fntBoldCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "For the period of " . $dateRange,
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
    );

$isDateHeadersCreated = false;
$dateHeaders = array();

if (count($result) > 0) {
    $sheet = &$xls->addWorksheet("Sheet 1");
    $format =& $xls->addFormat();
    
    $format->setLocked(1);
    $sheet->protect($pass);
    $r = $c = 0;

    foreach ($mainHeaders as $key => $mainhead) {
        $c = 0;
        $sheet->write($r,$c, $mainhead["headerVal"], $mainhead["headerFont"]);
        if ($mainhead["doMerge"]) {
            if ($mainhead["mergeCol"]>0) {
                $colToMerge = $mainhead["mergeCol"];
                $c+= $colToMerge;
                $sheet->write($r, $c, "", $mainhead["headerFont"]);
                $sheet->mergeCells($r,$c-$colToMerge,$r,$c);
            }// end if
        }// end if
        $r++;
    }
    $c = 0;

    foreach ($leftSubHeaders as $key => $leftsub) {
        $sheet->write($r,$c, $leftsub["headerVal"], $leftsub["headerFont"]);
        $c++;
    }

    $r++;
    $thedept = "";
    $sheet->freezePanes(array($r, 0, $r, 0));

    $empDept = "";
    $arrLates = array();
    // write information to excel sheet
    foreach ($result as $key => $datum) {
        $c = 0;
        $theName = $datum["qFullname"];
        $theId   = $datum["qEmpId"];
        $deptid = $this->employee->getindividualdept($theId);
            $arrLeaves = array();
    		$arrOvertime = array();
    		$arrHolidays = array();
            $lbal   = $this->attendance->checkLeaveBalance($theId);
			$tabsences  = $dispTotalLateToday = $dispTotalLateTH = $dispHalfDayToday = $dispFailureToLog = $dispTotalAbsentToday = $dispTotalUndertime = $disptotalut = $disptotallate = $disptotalTH = 0;
            
			$empsumm = $this->attendance->giveIndividualSummary($from_date, $to_date, $theId, $edata);
			foreach ($empsumm as $key => $entry) {
			    $intervalfail = 0;
               // sched for in and out interval of 0 and 1 minute will be mark as absent and failure to log..
                if(!empty($entry["queLogin"]) && $entry["queLogout"]){
                    $to_time = strtotime($entry["queLogout"]);
                    $from_time = strtotime($entry["queLogin"]);
                    if(round(abs($to_time - $from_time) / 60,2) <= 1){
                        $dispFailureToLog += 1;
                        $intervalfail     += 1;
                        #if(empty($dispTotalAbsentToday) && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail == 0) $dispTotalAbsentToday += 1;
                        if ($entry["queTotalAbsentToday"] == 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $dispFailureToLog == 0) $dispTotalAbsentToday+=1;
                    }
                }
                
               if($entry["queFailureToLog"] == 0 && $intervalfail ==0 && $entry["queIsHoliday"] == 0){
                $dispTotalAbsentToday += ($entry["queTotalAbsentToday"] > 0 && $entry["queIsHoliday"] == 0 && date('D',strtotime($entry["queLogDate"]))<>'Thu') ? $entry["queTotalAbsentToday"] : "";
               }
               
    			// get all leaves
    			if ($entry["queIsOnLeave"] > 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail ==0) {
    				array_push($arrLeaves, $entry["queIsOnLeave"]);
    			}
    			//get all holidays
    			if ($entry["queIsHoliday"] > 0) {
    				array_push($arrHolidays, $entry["queIsHoliday"]);
    			}
    			// get all overtime
    			if (($this->time->hoursToMinutes($entry["queOvertime"])) > 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail ==0) {
    				array_push($arrOvertime, ($this->time->hoursToMinutes($entry["queOvertime"])));
    			}
                
                $dispFailureToLog += ($entry["queFailureToLog"] > 0) ? $entry["queFailureToLog"] : "";
    
                $ut = ($this->time->hoursToMinutes($entry["queUndertime"]) > 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail ==0) ? $this->time->hoursToMinutes($entry["queUndertime"]) : 0;
                // get all undertime
                #if($isFlexi == TRUE)
                    #$dispTotalUndertime += 0;
                #else{
                #if(($this->time->hoursToMinutes($entry["queUndertime"]) <= 75) && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
                if(($this->time->hoursToMinutes($entry["queUndertime"]) <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) ) && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
                    $dispTotalUndertime += ($this->time->hoursToMinutes($entry["queUndertime"]) > 0) ? $this->time->hoursToMinutes($entry["queUndertime"]) : 0;
                    #if($ut > 0 && $ut <= 75) $disptotalut++;
                    if($ut > 0 && $ut <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) ) $disptotalut++;
                }
                #}
                
                /*
                if($entry["queHalfDayToday"] > 0 && $entry["queIsHoliday"] == 0){
                $dispTotalLateToday += 0;
                $dispTotalLateTH += 0;
                }else{
                */
                if(($entry["queSchedStart"] != "" && $entry["queSchedStart"] != "00:00:00") && ($entry["queSchedEnd"] != "" && $entry["queSchedEnd"] != "00:00:00")){
                #if($ut < 75 && $entry["queIsHoliday"] == 0){
                #if($ut < $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) && $entry["queIsHoliday"] == 0){
                    #if(!empty($entry["queLogout"]) && ($entry["queLogout"] > $entry["queTimeToBeAbsent2"])){
                    if($entry["queLogin"] > $entry["queTimeToBeAbsent2"])   $exempt  = 1;   else  $exempt = 0;
                    
                        if(!$exempt){
                            $dispTotalLateToday += ($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0) ? $this->time->hoursToMinutes($entry["queTotalLateToday"]) : "";
                            if(($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0)) $disptotallate++;
                        }
                        $dispTotalLateTH += ($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0)
                          ? $this->time->hoursToMinutes($entry["queHalfTHLate"]) : "";
                        if(($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0)  && !$isFlexi) $disptotalTH++;
                        
                        $exempt = 0;
                    #}
                #}
                }   
                
            
            if($entry["queIsHoliday"] == 0 && !empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]) && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
            $dispTotalAbsentToday += 1;
            }
            
            //get halfday
            if(($entry["queFailureToLog"] == 0 && $intervalfail ==0) && $entry["queIsHoliday"] == 0 && !empty($entry["queLogout"])) 
                $dispHalfDayToday += ($entry["queHalfDayToday"] > 0) ? $entry["queHalfDayToday"] : "";
                
            if(!empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]))
                $dispHalfDayToday += 1;
                                        
            #if($ut > 75 && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)) $dispHalfDayToday += 1;
            if($ut > $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)) $dispHalfDayToday += 1;
            
            # Thursday absent will count as halfday..
            if($entry["queHalfDayToday"] == 0)  $dispHalfDayToday += (date('D',strtotime($entry["queLogDate"]))=='Thu' && $entry["queTotalAbsentToday"] > 0) ? $entry["queTotalAbsentToday"] : "";                                
			}//end foreach
            $disptotallate      -=  $disptotalTH;
            if($disptotallate < 0)  $disptotallate = 0;
        
        if ($thedept != $datum["qDepartment"]) {
          $sheet->write($r,$c, $datum["qDepartment"], $fntBold);
          $r++;
        }
        // write employee id fullname and totals
        $sheet->write($r,$c, $theId, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $this->extras->changeenye($theName), $fntNormal);
        $c++;
        $sheet->write($r,$c, $disptotallate, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $disptotalTH, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $dispTotalLateToday, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $dispTotalUndertime, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $dispHalfDayToday, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $dispTotalAbsentToday, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, (($dispHalfDayToday / 2) + $dispTotalAbsentToday), $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $dispFailureToLog, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $lbal, $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, $this->extras->OtTime($theId,$from_date,$to_date), $fntNormalCenter);
        $c++;
        

        $thedept = $datum["qDepartment"];   //set current department
        $r++; // next row
    }// end foreach
}// end if
$xls->close(); // closes spreadsheet object