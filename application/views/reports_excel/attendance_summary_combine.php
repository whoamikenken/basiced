<?php
/**
 * @author Justin
 * @copyright 2015
 */
 
  $this->load->library('lib_includer');
  $this->lib_includer->load("excel/Writer");
  $xls = New Spreadsheet_Excel_Writer();
  $xls->send("Attendance Summary.xls");
  #$this->session->timeout(3600);
    
  require 'excel_fonts.php';

  $dateRange = "";
  $from_date = $datesetfrom;
  $to_date = $datesetto;  
  $empid = $fv;
  $dept = $deptid;
  $edata = $edata;
  $pass = $this->extras->setPass();
  $isFlexi = false;
  
  $dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
  $departments = $this->extras->showdepartment();

  $result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);
  $flexi = $this->employee->getindividualemployee($empid);
   foreach($flexi as $row)    $isFlexi = (bool)$row->isFlexi;

  $headers = array(
    array(
      'headerVal' => "Attendance Summary",
      'headerFont' => $fntBigBold
    ),
    array(
      'headerVal' => $dateRange,
      'headerFont' => $fntNormal
    ),
  ); 

  $detailHeaders = array(
    array(
      'headerVal' => "Employee ID",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Name",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 33
    ),
    array(
      'headerVal' => "Signature",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "No. of Lates",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Total no. of Minutes Late",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "No. of Thu Lates",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "No. of Overtime",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Total no. of minutes Overtime",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "No. of Early Dismissal",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Total no. of minutes Early Dismissal",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Absences",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Leaves",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Halfday(s)",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "Failure(s) to Log In/Out",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
    array(
      'headerVal' => "No. of Holidays",
      'headerFont'=> $fntColT,
      'doMerge' => true,
      'mergeRow' => 1,
      'mergeCol' => 0,
      'ColWidth' => 10
    ),
  );

  $datesubHeader = array(
    array(
    'headerVal' => "IN",
    'headerFont' => $fntColT,
    'doMerge' => false,
    'mergeRow' => 0,
    'mergeCol' => 0,
    'ColWidth' => 10
    ),
    array(
    'headerVal' => "OUT",
    'headerFont' => $fntColT,
    'doMerge' => false,
    'mergeRow' => 0,
    'mergeCol' => 0,
    'ColWidth' => 10
    ),
  );

  $dateHeaders = array();



if (count($result) > 0) {
  // add a sheet
  $sheet = &$xls->addWorksheet("Sheet 1");
  $format =& $xls->addFormat();
    
  $format->setLocked(1);
  $sheet->protect($pass);
  $rowsMerged = 0;
  $r = $c = 0;

  // write the main headers
  foreach ($headers as $key => $head1) {
    $sheet->write($r,$c, $head1["headerVal"], $head1["headerFont"]);
    $r++;
  }

  // write the subheaders
  foreach ($detailHeaders as $key => $head2) {
    $sheet->write($r,$c, $head2["headerVal"], $head2["headerFont"]);
    if ($head2["doMerge"]) {
      if ($head2["mergeRow"] > 0) {
        $rowToMerge = $r + $head2["mergeRow"];
        $sheet->mergeCells($rowToMerge,$c,$r,$c);
        $sheet->write($rowToMerge,$c, "", $head2["headerFont"]);
        $sheet->setColumn($c,$c,$head2["ColWidth"]);
        $rowsMerged++;
      }
    }
    $c++;
  }
  //

  $colDateHeadStart =  $c;
  $rowDateHeadStart = $r;

  $r+=2;  // $c = 0;
  $dateHeaderCreated = false;
  $thedept = "";
  $sheet->freezePanes(array($r, 0, $r, 0)); 

  // parsing through the employee details
  foreach ($result as $key => $data) {
    $c = 0;
    $eid = $data["qEmpId"];
    $empEntry = $this->attendance->giveIndividualSummary($from_date, $to_date, $eid, $edata);
    $deptid = $this->employee->getindividualdept($eid);

    // creating a multidimensional array for the date headers
    if (!$dateHeaderCreated) {
      foreach ($empEntry as $key => $empdata) {
        if (!in_array($empdata["queLogDate"], $dateHeaders)) {

          $datehead = array(
            'headerVal' => date("d (l)",strtotime($empdata["queLogDate"])),
            'headerFont' => $fntColT,
            'doMerge' => true,
            'mergeRow' => 0,
            'mergeCol' => 1
          );

          array_push($dateHeaders, $datehead);
        }
      }
      $dateHeaderCreated = true;
    }

    if ($thedept != $data["qDepartment"]) {
      $sheet->write($r,$c, $data["qDepartment"], $fntBold);
      $r++;
    }

    $sheet->write($r,$c, $data["qEmpId"], $fntNormal);
    $c++;
    $sheet->write($r,$c, $this->extras->changeenye($data["qFullname"]), $fntNormal);
    $c+=2;

    $infoStartCol = $c;

    $c+= (count($detailHeaders) - 3);
    // arrays to handle values
    $arrLates = array();
    $arrLatesTH = array();    
    $arrOvertime = array();
    $arrUndertime = array();
    $arrAbsence = array();
    $arrLeaves = array();
    $arrHalfdays = array();
    $arrFailures = array();
    $arrMultiples = array();
    $arrHolidays = array();

    $tabsences  = $dispTotalLateToday = $dispTotalLateTH = $dispHalfDayToday = $dispFailureToLog = $dispTotalAbsentToday = $dispTotalUndertime = $tdispTotalUndertime = $disptotalut = $disptotallate = $disptotalTH = $ut =0;
    
    // parsing through the entries of individual employee summary
    foreach ($empEntry as $key => $empdata) {
        
        $intervalfail = 0;
        // sched for in and out interval of 0 and 1 minute will be mark as absent and failure to log..
        if(!empty($empdata["queLogin"]) && $empdata["queLogout"]){
        $to_time = strtotime($empdata["queLogout"]);
        $from_time = strtotime($empdata["queLogin"]);
            if(round(abs($to_time - $from_time) / 60,2) <= 1){
                $dispFailureToLog += 1;
                $intervalfail     += 1;
                if ($empdata["queTotalAbsentToday"] == 0 && $empdata["queIsHoliday"] == 0 && $empdata["queFailureToLog"] == 0 && $dispFailureToLog == 0) $dispTotalAbsentToday+=1;
            }
        }

           if($empdata["queFailureToLog"] == 0 && $intervalfail ==0 && $empdata["queIsHoliday"] == 0){
            $dispTotalAbsentToday += ($empdata["queTotalAbsentToday"] > 0 && $empdata["queIsHoliday"] == 0 && date('D',strtotime($empdata["queLogDate"]))<>'Thu') ? $empdata["queTotalAbsentToday"] : "";
           }
           
			// get all leaves
			if ($empdata["queIsOnLeave"] > 0 && $empdata["queIsHoliday"] == 0 && /*$empdata["queFailureToLog"] == 0 &&*/ $intervalfail ==0) {
				array_push($arrLeaves, $empdata["queIsOnLeave"]);
			}
			//get all holidays
			if ($empdata["queIsHoliday"] > 0) {
				array_push($arrHolidays, $empdata["queIsHoliday"]);
			}
			// get all overtime
			if (($this->time->hoursToMinutes($empdata["queOvertime"])) > 0 && $empdata["queIsHoliday"] == 0 /*&& $empdata["queFailureToLog"] == 0*/ && $intervalfail ==0) {
				array_push($arrOvertime, ($this->time->hoursToMinutes($empdata["queOvertime"])));
			}
            
            $dispFailureToLog += ($empdata["queFailureToLog"] > 0) ? $empdata["queFailureToLog"] : "";

            $ut = ($this->time->hoursToMinutes($empdata["queUndertime"]) > 0 && $empdata["queIsHoliday"] == 0 /*&& $empdata["queFailureToLog"] == 0 */&& $intervalfail ==0) ? $this->time->hoursToMinutes($empdata["queUndertime"]) : 0;
            // get all undertime
            if($isFlexi == TRUE)
                $dispTotalUndertime += 0;
            else{
                #if(($this->time->hoursToMinutes($empdata["queUndertime"]) <= 75) && ($empdata["queIsHoliday"] == 0) && ($empdata["queFailureToLog"] == 0 && $intervalfail ==0)){
                if(($this->time->hoursToMinutes($empdata["queUndertime"]) <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($empdata["queLogDate"]))) ) && ($empdata["queIsHoliday"] == 0) && ($empdata["queFailureToLog"] == 0 && $intervalfail ==0)){    
                    $dispTotalUndertime += ($this->time->hoursToMinutes($empdata["queUndertime"]) > 0 && $empdata["queIsHoliday"] == 0) ? $this->time->hoursToMinutes($empdata["queUndertime"]) : 0;
                    $tdispTotalUndertime += ($this->time->hoursToMinutes($empdata["queUndertime"]) > 0 && $empdata["queIsHoliday"] == 0) ? 1 : 0;
                    #if($ut > 0 && $ut <= 75) $disptotalut++;
                    if($ut > 0 && $ut <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($empdata["queLogDate"])))) $disptotalut++;
                }
            }
            
            if(($empdata["queSchedStart"] != "" && $empdata["queSchedStart"] != "00:00:00") && ($empdata["queSchedEnd"] != "" && $empdata["queSchedEnd"] != "00:00:00")){
                #if($empdata["queHalfDayToday"] > 0 && $empdata["queIsHoliday"] == 0){
                #    $dispTotalLateToday += 0;
                #    $dispTotalLateTH += 0;
                #}else{
                    #if($ut < 75 && $empdata["queIsHoliday"] == 0){
                    #if($ut < $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($empdata["queLogDate"]))) && $empdata["queIsHoliday"] == 0){
                    if($empdata["queIsHoliday"] == 0){
                                                
                        if($empdata["queLogin"] > $empdata["queTimeToBeAbsent2"])   $exempt  = 1;   else  $exempt = 0;
                                                
                            if(!$exempt){
                            $dispTotalLateToday += ($this->time->hoursToMinutes($empdata["queTotalLateToday"]) > 0) ? $this->time->hoursToMinutes($empdata["queTotalLateToday"]) : "";
                            if(($this->time->hoursToMinutes($empdata["queTotalLateToday"]) > 0)) $disptotallate++;
                            }
                            $dispTotalLateTH += ($this->time->hoursToMinutes($empdata["queHalfTHLate"]) > 0)
                              ? $this->time->hoursToMinutes($empdata["queHalfTHLate"]) : "";
                            if(($this->time->hoursToMinutes($empdata["queHalfTHLate"]) > 0) && !$isFlexi) $disptotalTH++;

                        $exempt = 0;
                    }
               #}
            }
            
        
        if($empdata["queIsHoliday"] == 0 && !empty($empdata["queLogout"]) && ($empdata["queLogout"] < $empdata["queTimeToBeAbsent2"]) && ($empdata["queFailureToLog"] == 0 && $intervalfail ==0)){
        $dispTotalAbsentToday += 1;
        }
        
        //get halfday
        if((/*$empdata["queFailureToLog"] == 0 && */$intervalfail ==0) && $empdata["queIsHoliday"] == 0 /*&& !empty($empdata["queLogout"])*/) 
            $dispHalfDayToday += ($empdata["queHalfDayToday"] > 0) ? $empdata["queHalfDayToday"] : "";
            
        if(!empty($empdata["queLogout"]) && ($empdata["queLogout"] < $empdata["queTimeToBeAbsent2"]))
            $dispHalfDayToday += 1;

        #if($ut > 75 && $empdata["queIsHoliday"] == 0 && (/*$empdata["queFailureToLog"] == 0 && */$intervalfail ==0)) $dispHalfDayToday += 1;
        if($ut > $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($empdata["queLogDate"]))) && $empdata["queIsHoliday"] == 0 && (/*$empdata["queFailureToLog"] == 0 && */$intervalfail ==0)) $dispHalfDayToday += 1;
        
        # Thursday absent will count as halfday..
        if($empdata["queHalfDayToday"] == 0)  $dispHalfDayToday += (date('D',strtotime($empdata["queLogDate"]))=='Thu' && $empdata["queTotalAbsentToday"] > 0) ? $empdata["queTotalAbsentToday"] : "";
           
      // fill yellow an empty log time for halfday..
      $habyc = 0;
      if($empdata["queHalfDayToday"] == 1 && date("l",strtotime($empdata['queLogDate']) || !empty($empdata["queLogout"]) && ($empdata["queLogout"] < $empdata["queTimeToBeAbsent2"])) == "Thursday")   $habyc = 1;  
      
      // get login
      $login = ($empdata['queLogin'] != "") ? date("h:i A",strtotime($empdata['queLogin'])) : "";

      $fntForLogin =  ($habyc == 1) ? $fntFailCenter : (($empdata["queIsHoliday"] > 0) ? $fntHolidayCenter :(((($empdata['queLogin'] == "") && (( ($empdata["queTotalAbsentToday"]) > 0) /*|| (($empdata["queFailureToLog"]) > 0)*/ )) 
        ? $fntFailCenter : ((($this->time->hoursToMinutes($empdata["queTotalLateToday"])) > 0) ? $fntTardyCenter : $fntNormal))));

      // get logout
      $logout = ($empdata['queLogout'] != "") ? date("h:i A",strtotime($empdata['queLogout'])) : "";

      $fntForLogout = ($habyc == 1) ? $fntFailCenter : (($empdata["queIsHoliday"] > 0) ? $fntHolidayCenter : (((($empdata['queLogout'] == "") && (( ($empdata["queTotalAbsentToday"]) > 0) /*|| ( ($empdata["queFailureToLog"]) > 0)*/ )) 
        ? $fntFailCenter : (( ($this->time->hoursToMinutes($empdata["queUndertime"])) > 0) ? $fntTardyCenter : $fntNormal))));
        
        
      // writing logs
      $sheet->write($r,$c,$login,$fntForLogin);
      $c++;
      $sheet->write($r,$c,$logout,$fntForLogout);
      $c++;
    }// end foreach
    
    // Total Late - Total Late Thursday
    $disptotallate      -=  $disptotalTH;
    if($disptotallate < 0)  $disptotallate = 0;
    #$dispTotalLateToday -=  $dispTotalLateTH;
        
    // associating total values into a single array
    $info = array(
      'numLate' => $disptotallate,
      'totalLate' => $dispTotalLateToday,
      'numLateTH' => $disptotalTH,
      'numOvertime' => count($arrOvertime),
      'totalOvertime' => array_sum($arrOvertime),
      'numUndertime' => $tdispTotalUndertime,
      'totalUndertime' => $dispTotalUndertime,
      'numAbsent' => $dispTotalAbsentToday,
      'numLeave' => count($arrLeaves),
      'numHalfdays' => $dispHalfDayToday,
      'numFailures' => $dispFailureToLog,
      'numHolidays' => count($arrHolidays)
    );

    $infoCol = $infoStartCol;
    // writing totals
    foreach ($info as $key => $infoval) {
      $sheet->write($r,$infoCol,$infoval,$fntNormalCenter);
      $infoCol++;
    }
    $r++; // next row

    $thedept = $data["qDepartment"]; // assigning current department
  } 

  // writing the date headers and subheaders ("IN"/"OUT")
  $col = $colDateHeadStart;
  $row = $rowDateHeadStart;
  foreach ($dateHeaders as $key => $dtehdval) {
    $sheet->write($row,$col, $dtehdval["headerVal"], $dtehdval["headerFont"]);
    if ($dtehdval["doMerge"]) {
      if ($dtehdval["mergeCol"] > 0) {
        $colToMerge = $dtehdval["mergeCol"];
        $col+= $colToMerge;
        $sheet->write($row, $col,"",$dtehdval["headerFont"]);
        $sheet->mergeCells($row,$col-$colToMerge,$row,$col);
      }
    }
    $col++;
    $subheadrow = $row+1;
    $subheadcol = $col-2;
    foreach ($datesubHeader as $key => $subhead) {
      $sheet->write($subheadrow,$subheadcol, $subhead["headerVal"], $subhead["headerFont"]);
      $subheadcol++;
    }
  }

  }//end if




  $xls->close();
