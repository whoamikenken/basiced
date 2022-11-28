<?php

/**
 * @author Aaron Ruanto
 * @copyright 2014
 */
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Attendance.xls");
    
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();
    
    $tardycenter =& $xls->addFormat(array('Size' => 10));
    $tardycenter->setAlign("center");
    $tardycenter->setColor("red");
    $tardycenter->setLocked();
    
    $holidaycenter =& $xls->addFormat(array('Size' => 10));
    $holidaycenter->setAlign("center");
    $holidaycenter->setBgColor("grey");
    $holidaycenter->setFgColor("grey");
    $holidaycenter->setLocked();
    
    $failcenter =& $xls->addFormat(array('Size' => 10));
    $failcenter->setAlign("center");
    $failcenter->setBgColor("yellow");
    $failcenter->setFgColor("yellow");
    $failcenter->setLocked();
    
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();
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

/** Query */
    $condition = "";
    $datedisplay = "";
    if($fv){
      $condition = " and TRIM(b.userid)='{$fv}'";  
    }
    if($datesetfrom && $datesetto){
      # $condition .=  " and DATE(b.logtime) BETWEEN '".date("Y-m-d",strtotime($datesetfrom))."' AND '".date("Y-m-d",strtotime($datesetto))."'";
       if(date("Y-m",strtotime($datesetfrom))==date("Y-m",strtotime($datesetto)) && $datesetfrom!=$datesetto) $datedisplay = date("F d-",strtotime($datesetfrom)) . date("d Y",strtotime($datesetto));
       else if(date("Y",strtotime($datesetfrom))==date("Y",strtotime($datesetto)) && $datesetfrom!=$datesetto) $datedisplay = date("F d - ",strtotime($datesetfrom)) . date("F d, Y",strtotime($datesetto));
       else if(date("Y-m-d",strtotime($datesetfrom))!=date("Y-m-d",strtotime($datesetto))) $datedisplay = date("F d, Y - ",strtotime($datesetfrom)) . date("F d, Y",strtotime($datesetto));
       else $datedisplay = date("F d, Y",strtotime($datesetfrom));
    }else if(!$datesetfrom && $datesetto){
      # $condition .=  " and DATE(b.logtime)='".date("Y-m-d",strtotime($datesetto))."'"; 
       $datedisplay = date("F d, Y",strtotime($datesetto));
    }else if($datesetfrom && !$datesetto){
      # $condition .=  " and DATE(b.logtime)='".date("Y-m-d",strtotime($datesetfrom))."'"; 
       $datedisplay = date("F d, Y",strtotime($datesetfrom));
    }
    /**
     * 
     * b.logtime     changed to e.dte 
     * a.employeeid  changed to {$fv} 
     */ 
    $sql = $this->db->query("SELECT a.employeeid,CONCAT(a.LName,', ',a.FName,' ',a.MName) AS Fullname,e.dte,DATE(e.dte) AS logdate,
                                IFNULL(GET_EMPLOYEE_SCHED('{$fv}',e.dte,'START'),'') AS sched_IN,
                                IFNULL(GET_EMPLOYEE_SCHED('{$fv}',e.dte,'END'),'') AS sched_OUT, 
                                IF(IFNULL(b.timein,'')='0000-00-00 00:00:00','',IFNULL(b.timein,'')) AS log_IN,
                            IF(IFNULL(b.timeout,'')='0000-00-00 00:00:00','',IFNULL(b.timeout,'')) AS log_OUT,
                            IFNULL(GET_EMPLOYEE_SCHED('{$fv}',e.dte,'TARDY'),'') AS sched_TARDYSTART  
                             FROM (SELECT '".date("Y-m-d",strtotime($datesetfrom))."' + INTERVAL a + b + c DAY dte
                                    FROM
                                     (SELECT 0 a UNION SELECT 1 a UNION SELECT 2 UNION SELECT 3
                                        UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
                                        UNION SELECT 8 UNION SELECT 9 ) d,
                                     (SELECT 0 b UNION SELECT 10 UNION SELECT 20 UNION SELECT 30 UNION SELECT 40 UNION SELECT 60 UNION SELECT 70 UNION SELECT 80 UNION SELECT 90) m , 
                                     (SELECT 0 c UNION SELECT 100 UNION SELECT 200 UNION SELECT 300 UNION SELECT 400 UNION SELECT 600 UNION SELECT 700 UNION SELECT 800 UNION SELECT 900) h
                                    WHERE '".date("Y-m-d",strtotime($datesetfrom))."' + INTERVAL a + b + c DAY  <=  '".date("Y-m-d",strtotime($datesetto))."'
                                    ORDER BY a + b + c) e
                             LEFT JOIN timesheet b ON (DATE(b.timein)=e.dte OR DATE(b.timeout)=e.dte)  {$condition}  
                             LEFT JOIN employee a ON a.employeeid=b.userid
                             
                             WHERE e.dte<>''
                             GROUP BY DATE(e.dte)
                             ORDER BY DATE(e.dte)")->result_array();
    $count_result = count($sql);                          
    if($count_result>0){
    $noc = 2;
    $maxe = 10;
    $tmax = $maxe;
    if($count_result>($maxe*$noc)){
      $tmax = floor($count_result/$noc) + (($count_result % $noc)>$noc ? round(($count_result % $noc)/$noc) : ($count_result % $noc));   
    }
    
    $cn = 1;
/** End of query */
    
    /** End of Font Format */
    $sheet = &$xls->addWorksheet("Sheet 1");
    $c = 0;$r = 0;
    $sheet->write($r,$c,"Attendance",$bigbold);
    $r++;
    $sheet->write($r,$c,$datedisplay,$normal);
    $r++;
    $sheet->write($r,$c,$this->user->get_employee_fullname($fv),$normal);
    
    $r++;
    $sheet->write($r,$c,"Date",$coltitle);
    
    $c++;
    $sheet->write($r,$c,"Scheduled Time",$coltitle);
    $c++;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r,$c-1,$r,$c); 
    
    $c++;
    $sheet->write($r,$c,"Actual Log Time",$coltitle);
    $c++;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r,$c-1,$r,$c);
    
    $c++;
    $sheet->write($r,$c,"Tardiness",$coltitle);
    
    $c++;
    $sheet->write($r,$c,"Undertime",$coltitle);
    
    $c++;
    $sheet->write($r,$c,"Over Time",$coltitle);
    
    $c++;
    $sheet->write($r,$c,"Absences",$coltitle);
    
    $c++;
    $sheet->write($r,$c,"Leaves",$coltitle);
    
    $c++;
    $sheet->write($r,$c,"Half Day",$coltitle);
    
    $c++;
    $sheet->write($r,$c,"Failure to Log",$coltitle);
    
    
    
    $r++;$c = 0;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r-1,$c,$r,$c);
    $c++;
    $sheet->write($r,$c,"IN",$coltitle);
    $c++;
    $sheet->write($r,$c,"OUT",$coltitle);
    $c++;
    $sheet->write($r,$c,"IN",$coltitle);
    $c++;
    $sheet->write($r,$c,"OUT",$coltitle);
    $c++;
    $sheet->write($r,$c,"Mins.",$coltitle);
    $c++;
    $sheet->write($r,$c,"Mins.",$coltitle);
    $c++;
    $sheet->write($r,$c,"Mins.",$coltitle);
    $c++;
    $sheet->write($r,$c,"Days",$coltitle);
    $c++;
    $sheet->write($r,$c,"Days",$coltitle);
    $c++;
    $sheet->write($r,$c,"Freq",$coltitle);
    $c++;
    $sheet->write($r,$c,"Freq",$coltitle);
    /**
    $c++;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r-1,$c,$r,$c);
    $c++;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r-1,$c,$r,$c);
    $c++;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r-1,$c,$r,$c);
    $c++;
    $sheet->write($r,$c,"",$coltitle);
    $sheet->mergeCells($r-1,$c,$r,$c);
    */
    
    $cr = 0;$emp = "";$lt = 0;
    
   
    foreach($sql as $mrow){
        $isleave=false;
        $colorin =  $normalcenter;
        $colorout =  $normalcenter;    
        $normalcenterh=$normalcenter;
        $sched_in = $mrow['sched_IN']?strtotime($mrow['sched_IN']):"";
        $sched_out = $mrow['sched_OUT']?strtotime($mrow['sched_OUT']):"";
        #$time_in = $mrow['log_IN']?strtotime(date("H:i:s",strtotime($mrow['log_IN']))):"";
        #$time_out = $mrow['log_OUT']?strtotime(date("H:i:s",strtotime($mrow['log_OUT']))):"";
   
        $time_in = $mrow['log_IN'];
        $time_out = $mrow['log_OUT'];   
        $holiday=0;
        $dayabsent=0;
        $leave=0;
        $tardy=0;
        $undertime=0;
        $failure=0;
        $arraytime=array();
                        
         $holidaycss="";
         if($this->time->getHoliday($mrow['dte'])){
            #$time_in=$sched_in;
            #$time_out=$sched_out;
            $holiday++;
            $holidaycss="background-color:#A5C66F;";
         }
        
        
         if($this->time->getLeave($mrow['dte'],$fv,$arraytime)){
            list($time_in,$time_out,$isleave)=$arraytime;
         if($isleave)$leave++;   
         }
        
        if($holiday){
           $normalcenterh=$holidaycenter;
           $tardy = 0;
           $overtime = $this->time->getOverTime($time_out,$mrow['sched_OUT'],$time_in,$mrow['sched_IN'],TRUE);   
         }else if(($time_in=="" && $time_out=="") && ($mrow['sched_IN']!="" && $mrow['sched_OUT']!="")){
           $colorout =  $failcenter;
           $colorin =  $failcenter; 
           if(!$holiday)$dayabsent++;
           
           }
        else{
           if($time_out!="")$undertime = $this->time->getUnderTime($time_out,$mrow['sched_OUT']);                       
           if(($time_in!="" && $time_out=="") || ($time_in=="" && $time_out!=""))$failure++;   
           $tardy = $this->time->getTardy($time_in,$mrow['sched_IN'],$mrow['sched_TARDYSTART']);
           $overtime = $this->time->getOverTime($time_out,$mrow['sched_OUT'],$time_in,$mrow['sched_IN']);
           if($tardy>0)$colorin =  $tardycenter;
           else if( ($time_in=="" && $time_out!=""))$colorin =  $failcenter;
            
           if($undertime>0)$colorout =  $tardycenter;
           else if(($time_in!="" && $time_out==""))$colorout =  $failcenter;
         }
                                               
        
         
        
         
        $r++;$c = 0;
        $sheet->write($r,$c,date("d-M (l)",strtotime($mrow['dte'])),$normal);
        $c++;
        $sheet->write($r,$c,($sched_in!="" ? date("h:i A",$sched_in) : ""),$normalcenterh);
        $c++;
        $sheet->write($r,$c,($sched_out!="" ? date("h:i A",$sched_out) : ""),$normalcenterh);
        $c++;
        $sheet->write($r,$c,($time_in!="" ? date("h:i A",strtotime($time_in)) : ""),$colorin);
        $c++;
        $sheet->write($r,$c,($time_out!="" ? date("h:i A",strtotime($time_out)) : ""),$colorout);
        $c++;
        $sheet->write($r,$c,($tardy?$tardy:""),$normalcenter);
        $c++;
        $sheet->write($r,$c,($undertime?$undertime:""),$normalcenter);
        $c++;
        $sheet->write($r,$c,($overtime?$overtime:""),$normalcenter);
        $c++;
        $sheet->write($r,$c,($dayabsent?$dayabsent:""),$normalcenter);
        $c++;
        $sheet->write($r,$c,($leave?$leave:""),$normalcenter);
        $c++;
        $sheet->write($r,$c,"",$normalcenter);
        $c++;
        $sheet->write($r,$c,($failure?$failure:""),$normalcenter);
        
        $emp = $mrow['employeeid'];
        $lt = $lt==0?1:0;
        $cr++;    
    }
                
    
    }
    $xls->close();
?>
