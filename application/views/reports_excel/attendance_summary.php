<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Attendance Summary.xls");
    
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
    
    $coltitle =& $xls->addFormat(array('Size' => 8));
    $coltitle->setBorder(2);
    $coltitle->setAlign("center");
    $coltitle->setBgColor(11);
    $coltitle->setFgColor(11);
    $coltitle->setLocked();
    
    $colnumber =& $xls->addFormat(array('Size' => 8));
    $colnumber->setNumFormat("#,##0.00");
    $colnumber->setBorder(1);
    $colnumber->setAlign("center");
    $coltitle->setLocked();
    
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
    
    $bigbold =& $xls->addFormat(array('Size' => 12));
    $bigbold->setBold();
    $bigbold->setLocked();
    
	$bigboldcenter =& $xls->addFormat(array('Size' => 12));
	$bigboldcenter->setBold();
    $bigboldcenter->setAlign("center");
	$bigboldcenter->setLocked();
    
    $bold =& $xls->addFormat(array('Size' => 8));
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
    /* END */
    
    $condition = "";
    $condition_date = "";
    $datedisplay = "";
    $pass = $this->extras->setPass();
    $department_desc = $this->extras->showdepartment();
    if($edata == "OLD") $tbl = "timesheet_bak"; 
    else                $tbl = "timesheet";
    if($fv){
      $condition = " and (TRIM(mtable.employeeid)='{$fv}' or TRIM(mtable.employeecode)='{$fv}' or TRIM(CONCAT(mtable.lname,', ',mtable.lname,' ',mtable.lname))='{$fv}')";  
    }
    if($datesetfrom && $datesetto){
       if(date("Y-m",strtotime($datesetfrom))==date("Y-m",strtotime($datesetto))) $datedisplay = date("F d-",strtotime($datesetfrom)) . date("d Y",strtotime($datesetto));
       else if(date("Y-m-d",strtotime($datesetfrom))!=date("Y-m-d",strtotime($datesetto))) $datedisplay = date("F d, Y - ",strtotime($datesetfrom)) . date("F d, Y",strtotime($datesetto));
       else $datedisplay = date("F d, Y",strtotime($datesetfrom));
    }else if(!$datesetfrom && $datesetto){
       $datedisplay = date("F d, Y",strtotime($datesetto));
    }else if($datesetfrom && !$datesetto){
       $datedisplay = date("F d, Y",strtotime($datesetfrom));
    }
    if($deptid){
       $condition .= " and mtable.deptid='{$deptid}'";   
    }
    
    $sql = $this->db->query("SELECT mtable.employeeid,mtable.employeecode,CONCAT(mtable.lname,', ',mtable.fname,' ',mtable.mname) AS Fullname,mtable.deptid,c.description AS department,DATE(mtable.dte) AS logdate,
                            IF(IFNULL(d.timein,'')='0000-00-00 00:00:00','',IFNULL(d.timein,'')) AS log_IN,
                            IF(IFNULL(d.timeout,'')='0000-00-00 00:00:00','',IFNULL(d.timeout,'')) AS log_OUT,
                            IFNULL(GET_EMPLOYEE_SCHED(mtable.employeeid,mtable.dte,'START'),'') AS sched_IN,
                            IFNULL(GET_EMPLOYEE_SCHED(mtable.employeeid,mtable.dte,'END'),'') AS sched_OUT,
                            IFNULL(GET_EMPLOYEE_SCHED(mtable.employeeid,mtable.dte,'TARDY'),'') AS sched_TARDYSTART,
                            IFNULL(GET_EMPLOYEE_SCHED(mtable.employeeid,mtable.dte,'ABSENT_HALF1'),'') AS sched_ABSENTSTART,
                            IFNULL(GET_EMPLOYEE_SCHED(mtable.employeeid,mtable.dte,'ABSENT_HALF2'),'') AS sched_HALFDAYABSENT  
                            FROM 
                            (SELECT a.employeeid,a.employeecode,a.dateresigned,a.lname,a.fname,a.mname,a.deptid,e.dte
                            FROM employee a
                            ,
                            (SELECT '".date("Y-m-d",strtotime($datesetfrom))."' + INTERVAL a + b + c DAY dte
                            FROM 
                             (SELECT 0 a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 ) d, 
                             (SELECT 0 b UNION SELECT 10 UNION SELECT 20 UNION SELECT 30 UNION SELECT 40 UNION SELECT 60 UNION SELECT 70 UNION SELECT 80 UNION SELECT 90) m , 
                             (SELECT 0 c UNION SELECT 100 UNION SELECT 200 UNION SELECT 300 UNION SELECT 400 UNION SELECT 600 UNION SELECT 700 UNION SELECT 800 UNION SELECT 900) h 
                            WHERE ('".date("Y-m-d",strtotime($datesetfrom))."' + INTERVAL a + b + c DAY) <= '".date("Y-m-d",strtotime($datesetto))."' ORDER BY a + b + c) e) mtable
                            LEFT JOIN 
                            (SELECT a.userid,a.timein,a.timeout,a.mac_add_in,a.mac_add_out FROM $tbl a 
                               UNION 
                               SELECT b.userid,b.timein,'' AS timeout,b.mac_add AS mac_add_in,'' AS mac_add_out FROM timesheet_history b)
                             d ON d.userid=mtable.employeeid AND (DATE(d.timein)=mtable.dte OR DATE(d.timeout)=mtable.dte) 
                            LEFT JOIN code_office c ON c.code=mtable.deptid
                            WHERE mtable.employeeid<>'' AND IF(FIND_IN_SET(IFNULL(mtable.dateresigned,''),'1970-01-01,0000-00-00,'),(1=1),(mtable.dateresigned>mtable.dte)){$condition}
                            GROUP BY mtable.employeeid,DATE(mtable.dte) ORDER BY department,Fullname,DATE(mtable.dte)")->result_array();
    $count_result = count($sql);                            
    if($count_result>0){
    $noc = 2;
    $maxe = 10;
    $tmax = $maxe;
    if($count_result>($maxe*$noc)){
      $tmax = floor($count_result/$noc) + (($count_result % $noc)>$noc ? round(($count_result % $noc)/$noc) : ($count_result % $noc));   
    }
    
    $cn = 1;
    $gad_date = array();
    foreach($sql as $mrow){
      if(!in_array($mrow['logdate'],$gad_date)) array_push($gad_date,$mrow['logdate']);
    }
    array_multisort($gad_date);              
    
    $sheet = &$xls->addWorksheet("Sheet 1");
    $format =& $xls->addFormat();
    
    $format->setLocked(1);
    $sheet->protect($pass);
    $c = 0;$r = 0;
    $sheet->write($r,$c,"Attendance Summary",$bigbold);
    $r++;
    $sheet->write($r,$c,$datedisplay,$normal);
    if($deptid){
     $r++;        
     $sheet->write($r,$c,"Department : " . $department_desc[$deptid],$normal);    
    }
    
    $r++;
    $sheet->write($r,$c,"Employee ID",$coltitle);
    $c++;
    $sheet->write($r,$c,"Name",$coltitle);
    $sheet->setColumn($c,$c,33);
    foreach($gad_date as $date){
       $c++; 
       $sheet->write($r,$c,date("d-M Y (l)",strtotime($date)),$coltitle);
       $c++; 
       $sheet->write($r,$c,"",$coltitle);
       $sheet->mergeCells($r,$c-1,$r,$c); 
    }
    $r++;
    $c = 0;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r-1,$c,$r,$c); 
    $c++;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r-1,$c,$r,$c);
    
    foreach($gad_date as $date){
       $c++; 
       $sheet->write($r,$c,"IN",$coltitle);
       $c++; 
       $sheet->write($r,$c,"OUT",$coltitle); 
    }
    
    $r++;$c = 0;
    $sheet->freezePanes(array($r, 0, $r, 0)); 
    $cr = 0;$emp = "";$lt = 0;$lastdate = "";$dept_g = "";$cc= 0;
    $late = 0;
    $absence = 0;
    $undertime = 0;
    $halfday = 0;
    $noinout = 0;
    $tardytot = 0;
    $dayabsent=0;
    $undertimetot=0;
    $failure=0;
    foreach($sql as $mrow){
    /** if new employee */    
    if($emp!=$mrow['employeeid']){
    /** if not first employee */    
    if($emp!=""){
    foreach($gad_date as $dateset){
       if($lastdate!="" && $lastdate<$dateset){
           $cc+=2;
           $c++; 
           $c++;     
       }else if($lastdate==""){
           $cc+=2;  
           $c++; 
           $c++; 
       }
    }   
    $lastdate = "";    
    $late = 0;
    $absence = 0;
    $undertime = 0;
    $halfday = 0;
    $noinout = 0;
    $tardytot = 0;
    $dayabsent=0;
    $undertimetot=0;
    $failure=0;
    $r++;$c = 0;
    }    
    
    /** display department */
    if($dept_g != $mrow['department']){
      $sheet->write($r,$c,$mrow['department'],$bold);
      $r++;
    }
    
    $cc = 0;
        $sheet->write($r,$c,$mrow['employeeid'],$normalcenter);
        $c++;
        $sheet->write($r,$c,$this->extras->changeenye($mrow['Fullname']),$normal);  
    }
    
    foreach($gad_date as $dateset){
       $colorin =  $normalcenter;
       $colorout =  $normalcenter;
       if($dateset==$mrow['logdate']){
         
          $time_in = $mrow['log_IN'];
          $time_out = $mrow['log_OUT'];   
   
         if($this->time->getLeave($dateset,$mrow['employeeid'],$arraytime)){
         list($time_in,$time_out,$isleave)=$arraytime;
         }
           
           $undertime=0;
           
           // HALFDAY ABSENT
           $halfday_absent = $this->time->getHalfAbsent($time_in,$mrow['sched_HALFDAYABSENT'],$mrow['sched_ABSENTSTART']);
            if($halfday_absent > 0){
                $colorin = $halfcenter;
            }               
           
           $tardy = $this->time->getTardy($time_in,$mrow['sched_IN'],$mrow['sched_TARDYSTART']);
           if($time_out!="")$undertime = $this->time->getUnderTime($time_out,$mrow['sched_OUT']);
           $undertimetot += $undertime;
           $tardytot += $tardy;
           if(($time_in=="" && $time_out=="") && ($mrow['sched_IN']!="" && $mrow['sched_OUT']!=""))$dayabsent++; 
           if(($time_in!="" && $time_out=="") || ($time_in=="" && $time_out!=""))$failure++; 
           
           if($tardy>0)$colorin =  $tardycenter;
           else if( ($time_in=="" && $time_out!=""))$colorin =  $failcenter;
           
           if($undertime>0)$colorout =  $tardycenter;
           else if(($time_in!="" && $time_out==""))$colorout =  $failcenter;
           
           if(($time_in=="" && $time_out=="") && ($mrow['sched_IN']!="" && $mrow['sched_OUT']!="")){
           $colorout =  $failcenter;
           $colorin =  $failcenter;
           } 
           
           $cc+=2;
           $c++;
           $sheet->write($r,$c,($time_in!="" ? date("h:i A",strtotime($time_in)) : ""),$colorin);  
           $c++;
           $sheet->write($r,$c,($time_out!="" ? date("h:i A",strtotime($time_out)) : ""),$colorout);
        $lastdate = $dateset;
       }else if($lastdate==""){
           $cc+=2;
           $c++;
           $c++;     
       }else if($lastdate<$dateset && $dateset<$mrow['logdate']){
           $cc+=2;
           $c++;
           $c++; 
        $lastdate = $dateset;
       } 
    }
    $dept_g = $mrow['department'];
    $emp = $mrow['employeeid'];
    $lt = $lt==0?1:0;
    $cr++;    
    }
    
    /** Just end all td */
    foreach($gad_date as $dateset){
       if($lastdate!="" && $lastdate<$dateset){
          $cc+=2; 
          $c++;
          $c++;     
       }else if($lastdate==""){
          $cc+=2;
          $c++;
          $c++;    
       }
    } 
    
    }
    $xls->close();
?>