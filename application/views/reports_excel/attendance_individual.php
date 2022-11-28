<?php

    // variables
    $datedisplay = "";
    $from_date = $datesetfrom;
    $to_date = $datesetto;
    $empid = $fv;
    $edata = $edata;
    $pass = $this->extras->setPass();
    $deptid = $this->employee->getindividualdept($empid);
    #$this->session->timeout(3600);
    
    // load the library
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    #$this->lib_includer->load("excel/writer/Format");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Attendance.xls");

    // import fonts
    require 'excel_fonts.php';
    
    // create date range to display
    $daterange = $this->time->createRangeToDisplay($from_date, $to_date);

    // getting data from database
    $results = $this->attendance->giveIndividualSummary($from_date, $to_date, $empid, $edata);

    foreach ($results as $key => $row) {
        if ($row["queFullName"] != "") {
            $empFullname = $row["queFullName"];
            break;
        }else{                            
            $empFullname = "";          
            break;                      
        }
    }
    $flexi = $this->employee->getindividualemployee($empid);
     foreach($flexi as $row)    $isFlexi = (bool)$row->isFlexi;
     
    $mainHeaders = array(
        array(
            'headerName' => "reporType",
            'headerVal' => "Attendance",
            'headerFont' => $fntBigBold
        ),
        array(
            'headerName' => "dateCovered",
            'headerVal' => $daterange,
            'headerFont' => $fntNormal
        ),
        array(
            'headerName' => "empFullname",
            'headerVal' => $this->extras->changeenye($empFullname),
            'headerFont' => $fntNormal
        )
    );

    $detailHeaders = array(
        array(
            'headerName' => "dateHeader",
            'headerVal' => "Date",
            'headerFont' => $fntColT,
            'doMerge' => true,
            'mergeRow' => 1,
            'mergeCol' => 0
        ),
        array(
            'headerName' => "schedHeader",
            'headerVal' => "Scheduled Time",
            'headerFont' => $fntColT,
            'doMerge' => true,
            'mergeRow' => 0,
            'mergeCol' => 1
        ),
        array(
            'headerName' => "logHead",
            'headerVal' => "Actual Log Time",
            'headerFont' => $fntColT,
            'doMerge' => true,
            'mergeRow' => 0,
            'mergeCol' => 1
        ),
        array(
            'headerName' => "tardHead",
            'headerVal' => "Tardiness",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),
        array(
            'headerName' => "underHead",
            'headerVal' => "Undertime",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),
        array(
            'headerName' => "overHead",
            'headerVal' => "Overtime",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),
        array(
            'headerName' => "absentHead",
            'headerVal' => "Absences",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),
        array(
            'headerName' => "leaveHead",
            'headerVal' => "Leave",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),
        array(
            'headerName' => "halfdayHead",
            'headerVal' => "Halfdays",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),
        array(
            'headerName' => "failureHead",
            'headerVal' => "Failure To Log",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),
     /*   array(
            'headerName' => "multiplesHead",
            'headerVal' => "Multiple Logs",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        ),*/
        array(
            'headerName' => "holidayHead",
            'headerVal' => "Holiday",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeRow' => 0,
            'mergeCol' => 0
        )
    );

    $subHeaders = array(
        array(
            'headerVal' => "IN",
            'headerFont' => $fntColT,
        ),
        array(
            'headerVal' => "OUT",
            'headerFont' => $fntColT,
        ),
        array(
            'headerVal' => "IN",
            'headerFont' => $fntColT,
        ),
        array(
            'headerVal' => "OUT",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "minHead",
            'headerVal' => "Mins.",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "minHead",
            'headerVal' => "Mins.",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "minHead",
            'headerVal' => "Mins.",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "dayHead",
            'headerVal' => "Days",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "dayHead",
            'headerVal' => "Days",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "freqHead",
            'headerVal' => "Freq",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "freqHead",
            'headerVal' => "Freq",
            'headerFont' => $fntColT,
        ),
        array(
            'headerName' => "freqHead",
            'headerVal' => "Freq",
            'headerFont' => $fntColT,
        )/*,
        array(
            'headerName' => "blankHead",
            'headerVal' => "",
            'headerFont' => $fntColT,
        )*/
    );

    $mergedRows = 0;

    $sheet =& $xls->addWorksheet("Sheet 1");
    $format =& $xls->addFormat();
    
    $format->setLocked(1);
    $sheet->protect($pass);
    
    $c = 0;$r = 0;
    foreach ($mainHeaders as $key => $heading) {
       $sheet->write($r,$c, $heading["headerVal"], $heading["headerFont"]);
       $r++;
    }
    foreach ($detailHeaders as $key => $header) {
        $sheet->write($r,$c, $header["headerVal"], $header["headerFont"]);
        if ($header["doMerge"]) {
            if ($header["mergeRow"] > 0) {
                $rowToMerge = $r + $header["mergeRow"];
                $sheet->mergeCells($rowToMerge,$c,$r,$c);
                $sheet->write($rowToMerge,$c, "", $header["headerFont"]);
                $mergedRows++;
            }
            if ($header["mergeCol"] > 0) {
                $colToMerge = $header["mergeCol"];
                $c+= $colToMerge;
                $sheet->write($r, $c,"",$header["headerFont"]);
                $sheet->mergeCells($r,$c-$colToMerge,$r,$c);
            }
        }
        $c++;
    }
    $r++; $c = $mergedRows;

    foreach ($subHeaders as $key => $subhead) {
        $sheet->write($r,$c, $subhead["headerVal"], $subhead["headerFont"]);
        $c++;
    }
    $r++;
    foreach ($results as $key => $row) {
        $c = 0;

        $date = date("d-M (l)",strtotime($row['queLogDate']));
        
        $schedin = ($row["queSchedStart"] != "" && $row["queSchedStart"] != "00:00:00") ? date("h:i A",strtotime($row["queSchedStart"])) : "";

        $schedout = ($row['queSchedEnd'] != "" && $row["queSchedStart"] != "00:00:00") ? date("h:i A",strtotime($row['queSchedEnd'])) : "";

        $login = ($row['queLogin'] != "") ? date("h:i A",strtotime($row['queLogin'])) : "";

        $logout = ($row['queLogout'] != "") ? date("h:i A",strtotime($row['queLogout'])) : "";
        
        // display late
        #if($row["queHalfDayToday"] > 0)
        #$tardy = "";
        #else
        $tardy = ($this->time->hoursToMinutes($row["queTotalLateToday"]) > 0)
          ? $this->time->hoursToMinutes($row["queTotalLateToday"]) : "";

        // hide undertime if the schedule is Flexible..
        if($isFlexi == TRUE)
        $under = "";
        else
        $under = ($this->time->hoursToMinutes($row["queUndertime"]) > 0)
          ? $this->time->hoursToMinutes($row["queUndertime"]) : "";

        $over = ($this->time->hoursToMinutes($row['queOvertime']) > 0) 
            ? $this->time->hoursToMinutes($row['queOvertime']) : "";

        $absence = ($row['queTotalAbsentToday'] > 0 && date('D',strtotime($row["queLogDate"])) <> 'Thu') ? $row['queTotalAbsentToday'] : "";

        $leave = ($row['queIsOnLeave'] > 0) ? $row['queIsOnLeave'] : "";

        $half = ($row['queHalfDayToday'] > 0) ? $row['queHalfDayToday'] : "";

        $fails = ($row['queFailureToLog'] > 0) ? $row['queFailureToLog'] : "";

        $multiple = ($row['queMultipleLogFreq'] > 0) ? $row['queMultipleLogFreq'] : "";

        $holiday = ($row['queIsHoliday'] > 0) ? $row['queIsHoliday'] : "";
        
        // condition added 09-02-15
        #$chalf = 0;
        $halfinout = 0;
        #if($under > 60){ $under = ""; $half += 1; $chalf += 1;}

        #if(($under > 75) && ($holiday == 0)){ $under = ""; $half += 1; $tardy = ""; $halfinout = 2;} 
        if(($under > $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($row["queLogDate"]))) ) && ($holiday == 0)){ $under = ""; $half += 1; /*$tardy = "";*/ $halfinout = 2;}
        
        if($row['queLogin']  > $row["queTimeToBeAbsent2"])   $halfinout = 1;      // halfday in
        
        
        if($holiday == 0 && !empty($row["queLogout"]) && ($row["queLogout"] < $row["queTimeToBeAbsent2"])){
        $absence += 1;
        $half      = "";
        $tardy     = "";
        }
        
        // fill yellow an empty log time for halfday..
        $habyc = 0;
        if($half == 1 && date("l",strtotime($row['queLogDate'])) == "Thursday")   $habyc = 1; 
        
        
        // sched for in and out interval of 0 and 1 minute will be mark as absent and failure to log..
        if(!empty($row["queLogin"]) && $row["queLogout"]){
            $to_time = strtotime($row["queLogout"]);
            $from_time = strtotime($row["queLogin"]);
            if(round(abs($to_time - $from_time) / 60,2) <= 1){
                $fails += 1;
                if(empty($absence)) $absence += 1;
            }
        }
        
        #if($fails > 0){$tardy = $under = $over = $absence = $half = "";}
        
        if($holiday > 0){$tardy = $under = $over = $absence = $half = "";} 
        
        if($row["queLogin"] > $row["queTimeToBeAbsent2"])   $tardy = "";
        
        # Thursday absent will count as halfday..
        if($row["queHalfDayToday"] == 0 && date('D',strtotime($row["queLogDate"]))=='Thu' && $row["queTotalAbsentToday"] > 0)   $half = $row["queTotalAbsentToday"];

        $excelEntry = array(
            array(
                'entryVal' => $date,
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $schedin,
                #'entryFont' => (($holiday > 0) ? $fntHolidayCenter: $fntNormal)
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $schedout,
                #'entryFont' => (($holiday > 0) ? $fntHolidayCenter: $fntNormal)
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $login,
                'entryFont' => ((($login == "") && ((($absence > 0) || ($fails > 0)) || $habyc == 1)) ? $fntFailCenter : (($tardy != "") ? $fntTardyCenter : (($holiday > 0) ?  $fntHolidayCenter : (($halfinout == 1) ? $fntHalfDayCenter : $fntNormal))))
            ),
            array(
                'entryVal' => $logout,
                'entryFont' => ((($logout == "") && ((($absence > 0) || ($fails > 0)) || $habyc == 1)) ? $fntFailCenter : (($under > 0 && $under <= 75) ? $fntEarlyDismissal : (($holiday > 0) ? $fntTardyCenter : (($halfinout == 2) ? $fntHalfDayCenter : $fntNormal))))
            ),
            array(
                'entryVal' => $tardy,
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $under,
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $over,
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $absence,
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $leave,
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $half,
                'entryFont' => $fntNormal
            ),
            array(
                'entryVal' => $fails,
                'entryFont' => $fntNormal
            ),
          #  array(
          #      'entryVal' => $multiple,
          #      'entryFont' => $fntNormal
          #  ),
            array(
                'entryVal' => $holiday,
                'entryFont' => (($holiday > 0) ? $fntHolidayCenter: $fntNormal)
            ),
        );

        foreach ($excelEntry as $key => $entry) {
            $sheet->write($r,$c, $entry["entryVal"] , $entry["entryFont"]);
            $c++;
        }
        $r++;
    }
    $xls->close();
