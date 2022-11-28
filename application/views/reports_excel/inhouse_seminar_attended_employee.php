<?php
//KENNEDY 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");

    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Summary of Attendance.xls");
 
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();
    
    $tardycenter =& $xls->addFormat(array('Size' => 10));
    $tardycenter->setAlign("center");
    $tardycenter->setColor("red");
    $tardycenter->setLocked();
    
    $failcenter =& $xls->addFormat(array('Size' => 10));
    $failcenter->setAlign("center");
    $failcenter->setBgColor("yellow");
    $failcenter->setFgColor("yellow");
    $failcenter->setLocked();
    
    $halfcenter =& $xls->addFormat(array('Size' => 10));
    $halfcenter->setAlign("center");
    $failcenter->setBgColor("yellow");
    $halfcenter->setColor("red");
    $halfcenter->setLocked();        
    
    $tits =& $xls->addFormat(array('Size' => 10));
    $tits->setBold();
    $tits->setAlign("center");
    $tits->setLocked();
    
    $titsnormal =& $xls->addFormat(array('Size' => 10));
    $titsnormal->setAlign("center");
    $titsnormal->setLocked();
    
    $coltitle =& $xls->addFormat(array('Size' => 10));
    $coltitle->setBorder(2);
    $coltitle->setBold();
    $coltitle->setAlign("center");
    $coltitle->setFgColor('black');
    $coltitle->setColor('yellow');
    $coltitle->setLocked();

    $colOffice =& $xls->addFormat(array('Size' => 10));
    // $colOffice->setBorder(2);
    $colOffice->setBold();
    $colOffice->setAlign("center");
    $colOffice->setFgColor('yellow');
    $colOffice->setColor('black');
    $colOffice->setLocked();

    $colnumber =& $xls->addFormat(array('Size' => 8));
    $colnumber->setNumFormat("#,##0.00");
    $colnumber->setBorder(1);
    $colnumber->setAlign("center");
 
    $messbord =& $xls->addFormat(array('Size' => 8));
    $messbord->setBorder(1);
    $messbord->setAlign("center");
    $messbord->setLocked();
    
    $messbordpink =& $xls->addFormat(array('Size' => 8));
    $messbordpink->setBorder(1);
    $messbordpink->setBgColor(12);
    $messbordpink->setFgColor(12);
    $messbordpink->setAlign("center");
    $messbordpink->setLocked();
    
    $big =& $xls->addFormat(array('Size' => 12));
    $big->setLocked();
    
    $bigbold =& $xls->addFormat(array('Size' => 11));
    $bigbold->setBold();
    $bigbold->setLocked();
    
    $bigboldcenter =& $xls->addFormat(array('Size' => 12));
    $bigboldcenter->setBold();
    $bigboldcenter->setAlign("center");
    $bigboldcenter->setLocked();
    
    $bold =& $xls->addFormat(array('Size' => 12));
    $bold->setBold();
    $bold->setLocked();
    
    $boldcenter =& $xls->addFormat(array('Size' => 8));
    $boldcenter->setAlign("center");
    $boldcenter->setBold();
    $boldcenter->setLocked();
    
    $amount =& $xls->addFormat(array('Size' => 8));
    $amount->setNumFormat("#,##0.00");
    $amount->setLocked();
    
    $amountbold =& $xls->addFormat(array('Size' => 8));
    $amountbold->setNumFormat("#,##0.00_);\(#,##0.00\)");
    $amountbold->setAlign("center");
    $amountbold->setBold();
    $amountbold->setLocked();
    
    $number =& $xls->addFormat(array('Size' => 8));
    $number->setNumFormat("#,##0");
    $number->setLocked();
    
    $numberbold =& $xls->addFormat(array('Size' => 8));
    $numberbold->setNumFormat("#,##0");
    $numberbold->setBold();
    $numberbold->setLocked();
    
    $dateform =& $xls->addFormat(array('Size' => 8));
    $dateform->setNumFormat("D-MMM-YYYY");
    $dateform->setLocked();
    
    $timeform =& $xls->addFormat(array('Size' => 8));
    $timeform->setNumFormat("h:mm:ss AM/PM");
    $timeform->setLocked();

    // justify and center vertically
    $format_justify_center =& $xls->addFormat(array('Size' => 11));
    $format_justify_center->setVAlign('vjustify');
    $format_justify_center->setVAlign('vcenter');
    $format_justify_center->setAlign('center');
    $format_justify_center->setLocked();
    $result = $this->reports->allEmpByCS();   

    $totalSeminar = $empSeminarAttended = $empcount = 0;
    $schoolYear = explode("-", $year);
    $seminarSY = $this->seminar->getSeminarWithinSY($schoolYear[0], $schoolYear[1], $attendees, $month_from, $month_to, $month);
    $employeeList = $this->seminar->getAttendedEmployeeList($sortby, $status, $employees);
    $currentTime = new DateTime(date('Y-m-d', strtotime($this->extensions->getServerTime())));
    $inhouse_title = "Summary Of Attendance (".$year.")";

    $fields = array();
    array_push($fields, array("#",1,10,1));
    array_push($fields, array("EMPLOYEE ID",1,20,1));
    array_push($fields, array("FULL NAME",1,40,1));
    $existSeminar = $existSeminars = array();
    if(!in_array("all", $attendees)){
        foreach ($seminarSY as $value) {
            $attend = explode(',', $value['attendees']);
            if(is_array($attend)){
                foreach ($attend as $k => $v) {
                    if(!in_array($value['username'], $existSeminar)){
                        if(in_array($v, $attendees)){
                            $existSeminar[$value['id']] = $value['username'];
                            $totalSeminar+=1;
                            $seminarDate = $this->extensions->reportCodeDescription($value['workshop'])." - ".date('M j', strtotime($value['date_from']));
                            array_push($fields, array($seminarDate,1,40,1));
                        }
                    }
                }
            }
        }
    }else{
        foreach ($seminarSY as $value) {
            $totalSeminar+=1;
            $seminarDate = $this->extensions->reportCodeDescription($value['workshop'])." - ".date('M j', strtotime($value['date_from']));
            array_push($fields, array($seminarDate,1,40,1));
        }
    }
    array_push($fields, array("TOTAL",1,20,1));
    $subfields = array();
    $i=0;
    $merge = array();

    $numfield = count( $subfields )-1;

    if($numfield < 5) {
        $numfield = 1;
        $offset = 0;
        $hr = 10;   
    }else{
        $offset = intval(($numfield - 6) / 6);
        $hr = 0;
    }

    $sheet = &$xls->addWorksheet("Sheet 1");
   
    $sheet->setMerge(0, 0, 0, $numfield);
    $sheet->setMerge(1, 0, 1, $numfield);
    $sheet->setMerge(2, 0, 2, $numfield);
    $sheet->setMerge(3, 0, 3, $numfield);
    $sheet->setMerge(4, 0, 4, $numfield);
    $sheet->setMerge(5, 0, 5, $numfield);
    foreach($merge as $m)
    {
        $sheet->setMerge(10, $m, 7, $m);
    }

    $c = 0;$r = 0;
    $bitmap = "images/school_logo.bmp";
    
    $sheet->insertBitmap( $r , $c  + $offset , $bitmap , $hr , 8 , .20 ,.20 );
    $r++;$c++;
    $sheet->write(0,2,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(1,2,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(2,2,"SEMINAR ATTENDEES REPORT",$bigboldcenter);
    $r++;
    $sheet->write(3,2,$inhouse_title,$normalcenter);

    $r = 6;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$coltitle);
    $ofc = 'sometext';
    $r = 7;
     //echo "<pre>".print_r($cdata);die;
    $officeRow = $totalSeminar+4;
    $officeDescStart = ($officeRow/2)-2;
    $colspan = "colspan=".$officeRow;
    foreach ($employeeList as $key => $value){
        
        if($sortby=='Office'){
            if($ofc != $value['office']){
                for ($i=0; $i < $officeRow; $i++) { 
                    if($i == 0){
                        $sheet->setMerge($r,0,$r,$colOffice);
                        $sheet->write($r,$i,$this->extensions->getOfficeDescriptionReport($value['office']),$colOffice,$bigboldcenter);
                    }else{
                        $sheet->write($r,$i,'',$colOffice);
                    }
                }
                $r++;
                $c=0;
            }
        }
        $empSeminarAttended=0;
        if(!in_array("all", $attendees)){
            $dateemployed = new DateTime($value['dateemployed']);
            $yearOfService = $dateemployed->diff($currentTime)->y + 1;
            if(in_array($yearOfService, $attendees)){
                $empcount++;
                $sheet->write($r,$c,$empcount,$normalcenter);
                $c++;
                $sheet->write($r,$c,$value['employeeid'],$normalcenter);
                $c++;
                $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $value['fullname']),$normalcenter);
                $c++;
                    foreach ($seminarSY as $val) {
                        $existSeminars = array();
                        $attend = explode(',', $val['attendees']);
                        if(is_array($attend)){
                            foreach ($attend as $k => $v) {
                                if(!in_array($val['username'], $existSeminars)){
                                    if(in_array($v, $attendees)){
                                        $existSeminars[$val['id']] = $val['username'];
                                        list($timein, $timeout) = $this->seminar->getEmployeeSeminarAttendance($val['username'], $value['employeeid']);
                                        list($leavetype, $leaveColspan) = $this->seminar->getLeavetypeAndColspan($value['employeeid'], $val['date_from'], $val['date_to']);
                                        if($leavetype != ''){
                                            // $tilColumn = $c+2;
                                            if($leavetype != $leavedesc){
                                                $sheet->write($r,$c,$leavetype,$format_justify_center);
                                                $sheet->setMerge($r, $c, $r, $c);
                                                $c++;
                                                // $c = $tilColumn+1;
                                            }
                                            $leavedesc = $leavetype;
                                        }else{
                                            if($timein == '' && $timeout == ''){
                                                $columnStyle = $tardycenter;
                                                $attended = "---";
                                            }else if($timein && $timeout){
                                                $timein = new DateTime(date('H:i:s', strtotime($timein)));
                                                $timeout = new DateTime(date('H:i:s', strtotime($timeout)));
                                                $timefrom = new DateTime($val['time_from']);
                                                $timeto = new DateTime($val['time_to']);
                                                if($timein <= $timefrom && $timeout >= $timeto) {
                                                    $columnStyle = $tardycenter;
                                                    $attended = "&#10004;";
                                                    $empSeminarAttended+=1;
                                                }
                                                else if($timein > $timefrom && $timeout < $timeto){ 
                                                    $columnStyle = $normalcenter;
                                                    $attended = "Late & Early Exit";
                                                    $empSeminarAttended+=1;
                                                }
                                                else if($timein > $timefrom && $timeout >= $timeto){
                                                    $columnStyle = $tardycenter;
                                                    $attended = "Late";
                                                    $empSeminarAttended+=1;
                                                }
                                                else{
                                                    $columnStyle = $tardycenter;
                                                    $attended = "Early Exit";
                                                    $empSeminarAttended+=1;
                                                }
                                            }else{
                                                $columnStyle = $tardycenter;
                                                $attended = "Did not log out";
                                            }
                                            $sheet->write($r,$c,$attended,$columnStyle);
                                            $c++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                $gTotal = $empSeminarAttended."/".$totalSeminar;
                $sheet->write($r,$c,$gTotal,$normalcenter);
                $c=0;
                $r++;
                $ofc = $value['office'];
            }
        }else{
            $empcount++;
            $sheet->write($r,$c,$empcount,$normalcenter);
            $c++;
            $sheet->write($r,$c,$value['employeeid'],$normalcenter);
            $c++;
            $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $value['fullname']),$normalcenter);
            $c++;
                foreach ($seminarSY as $val) {
                    list($timein, $timeout) = $this->seminar->getEmployeeSeminarAttendance($val['username'], $value['employeeid']);
                    list($leavetype, $leaveColspan) = $this->seminar->getLeavetypeAndColspan($value['employeeid'], $val['date_from'], $val['date_to']);
                    if($leavetype != ''){
                        $tilColumn = $c+($leaveColspan-1);
                        if($leavetype != $leavedesc){
                            $sheet->write($r,$c,$leavetype,$format_justify_center);
                            $sheet->setMerge($r, $c, $r, $c);
                            $c++;
                        }else{
                            $attended = "---";
                            $sheet->write($r,$c,$attended,$columnStyle);
                            $c++;
                        }
                        $leavedesc = $leavetype;
                    }else{
                        if($timein == '' && $timeout == ''){
                            $columnStyle = $tardycenter;
                            $attended = "---";
                        }else if($timein && $timeout){
                            $timein = new DateTime(date('H:i:s', strtotime($timein)));
                            $timeout = new DateTime(date('H:i:s', strtotime($timeout)));
                            $timefrom = new DateTime($val['time_from']);
                            $timeto = new DateTime($val['time_to']);
                            if($timein <= $timefrom && $timeout >= $timeto) {
                                $columnStyle = $tardycenter;
                                $attended = "&#10004;";
                                $empSeminarAttended+=1;
                            }
                            else if($timein > $timefrom && $timeout < $timeto){ 
                                $columnStyle = $normalcenter;
                                $attended = "Late & Early Exit";
                                $empSeminarAttended+=1;
                            }
                            else if($timein > $timefrom && $timeout >= $timeto){
                                $columnStyle = $tardycenter;
                                $attended = "Late";
                                $empSeminarAttended+=1;
                            }
                            else{
                                $columnStyle = $tardycenter;
                                $attended = "Early Exit";
                            }
                        }else{
                            $columnStyle = $tardycenter;
                            $attended = "Did not log out";
                        }
                        $sheet->write($r,$c,$attended,$columnStyle);
                        $c++;
                    }
                }
            $gTotal = $empSeminarAttended."/".$totalSeminar;
            $sheet->write($r,$c,$gTotal,$normalcenter);
            $c=0;
            $r++;
            $ofc = $value['office'];
        }
    }
    $xls->close();


    function displaytablefields($sheet,$r,$c,$fields,$coltitle=''){ 
        foreach($fields as $colinfo){ 
        list($caption,$span,$width,$extra) = $colinfo;  
        if($span > 1) $sheet->setMerge($r, $c, $r, (($c-1) + $span)); 
        $sheet->write($r,$c,$caption,$coltitle);
        $sheet->setColumn($c,$c,$width);  
        $c += $span;
        }
    }
   
?>