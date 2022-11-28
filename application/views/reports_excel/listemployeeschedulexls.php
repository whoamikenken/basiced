<?php
 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    // Creating a workbook
    $xls = new Spreadsheet_Excel_Writer();
    // sending HTTP headers
    $xls->send('Employee List By Schedule.xls');
    // Format::setBorder (array("Border"=>1));
    
     /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10,'Align'=>'Center','Border' => 1));
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
    
    $bigbold =& $xls->addFormat(array('Size' => 11,'Align' => 'Center'));
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
    
    $division  =  isset($scheddiv) ? $scheddiv : ''; 
    $deptid    = isset($scheddeptid) ? $scheddeptid : '';
    $tnt       = isset($schedtnt) ? $schedtnt : '';
    $dfrom     =  isset($scheddfrom) ? $scheddfrom : '';
    $isactive    = isset($isactive) ? $isactive : '';
    
    $cdata      = $this->reports->loadempdataschedule($division,$deptid,$tnt,$dfrom, $isactive); 
    $merge = array();
    $fields = array();
    $subfields = array();

    $numfield = count( $subfields )-1;
    if($numfield < 5) {
        $numfield = 1;
        $offset = 0;
        $hr = 10;   
    }else{
        $offset = intval(($numfield - 2) / 2);
        $hr = 0;
    }
    array_push($fields, array("#",1,5,1));
    array_push($fields, array("EMPLOYEE ID ",1,20,1));
    array_push($fields, array("EMPLOYEE NAME",1,30,1));
    array_push($fields, array("DEPARTMENT",1,30,1));
    array_push($fields, array("SCHEDULE",1,50,1));
    
    
    $numfield = count($fields)-1;
    // Creating a worksheet
    $sheet =& $xls->addWorksheet('Sheet 1');

    // $sheet->setMerge(0, 0, 0, $numfield);
    // $sheet->setMerge(1, 0, 1, $numfield);
    // $sheet->setMerge(2, 0, 2, $numfield);
    // $sheet->setMerge(3, 0, 3, $numfield);
    // $sheet->setMerge(4, 0, 4, $numfield);
    // $sheet->setMerge(5, 0, 5, $numfield);
    // foreach($merge as $m)
    // {
    //     $sheet->setMerge(10, $m, 7, $m);
    // }
    
    $c = 0;$r = 0;
    $bitmap = "images/school_logo.bmp";
    
    $sheet->insertBitmap( $r , 2  + $offset , $bitmap , $hr , 8 , .10 ,.20 );
    $r++;$c++;
    $sheet->write(0,3,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(1,3,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(2,3,strtoupper($reportTitle)." REPORT",$bigboldcenter);
    $r++;
    $sheet->write(3,3,"As of ".date("F Y"),$normalcenter);
    
    $r = 6;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$boldcenter);
    
    $r = 7;
    $empcount = 1;
    $counter = 0;
    $dept = '!!';
    foreach ($cdata as $emp) {
        $sched="";
        if($dept != $emp->deptid){
            if($counter != 0){
                $empcount = $empcount - 1;
                for ($i=0; $i < 4; $i++) { 
                    if($i == 3){
                        $sheet->write($r-1,$i,"TOTAL:",$boldcenter);
                        $sheet->write($r-1,$i+1,$empcount,$boldcenter);
                    }else{
                        $sheet->write($r-1,$i,' ',$boldcenter);
                    }
                }
                $empcount = 1;
                $counter = 0;
                $r++;
            }
        }
        $dept = $emp->deptid;
        $sheet->write($r,$c,$empcount,$normal);
        $c++;
        $sheet->write($r,$c,$emp->employeeid,$normal);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $emp->fullname),$normal);
        $c++;
        // $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $this->extras->getDeptDesc($emp->deptid, "yes")),$normal);
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $this->extensions->getDeparmentDescriptionReport($emp->deptid)),$normal);
        $c++;
        $sched = $this->extras->getEmpSchedule($emp->employeeid);
        if($sched)
        {
            $day1=$start1=$end2=$start1=$end2="";
            $day="";
            $string ="";
            $i= 0; //CHANGE TO 0
            foreach($sched as $row)
            {
                $i += 1;
                // echo "<pre>"; print_r($row); die;
                // echo "<pre>"; print_r($row->dayofweek); die;
                if($day != $row->dayofweek)
                {
                    // CHANGE TO ARRAY
                    $array=array();
                    $day = $row->dayofweek;
                    // foreach($sched as $row)
                    // {
                    //     array_push($array,(array)$r);
                    // }
                    $start1 = date("g:i A", strtotime($row->starttime));
                    $end1 = date("g:i A", strtotime($row->endtime));
                    
                    // $d = $this->extras->sched($i,$sched);
                    // echo "<pre>"; print_r($day);
                    // echo "<pre>"; print_r($sched[$i]); die;
                    if(isset($sched[$i]->dayofweek)){
                        if($day != $sched[$i]->dayofweek)
                        {
                            if($row->dayofweek == "M") $day1 ="MONDAY";
                            else if($row->dayofweek == "T") $day1 ="TUESDAY";
                            else if($row->dayofweek == "W") $day1 ="WENESDAY";
                            else if($row->dayofweek == "TH") $day1 ="THURSDAY";
                            else if($row->dayofweek == "F") $day1 ="FRIDAY";
                            else if($row->dayofweek == "S") $day1 ="SATURDAY";
                            else if($row->dayofweek == "SUN") $day1 ="SUNDAY";

                            $string = $day1 . " " . $start1 . " - " . $end1;
                            $sheet->write($r,$c,$string,$normal);
                            $r++;
                            $string = '';
                        }
                        else
                        {
                            
                            $start2 = date("g:i A", strtotime($sched[$i]->starttime));
                            $end2 = date("g:i A", strtotime($sched[$i]->endtime));
                                   
                            if($row->dayofweek == "M") $day1 ="MONDAY";
                            else if($row->dayofweek == "T") $day1 ="TUESDAY";
                            else if($row->dayofweek == "W") $day1 ="WENESDAY";
                            else if($row->dayofweek == "TH") $day1 ="THURSDAY";
                            else if($row->dayofweek == "F") $day1 ="FRIDAY";
                            else if($row->dayofweek == "S") $day1 ="SATURDAY";
                            else if($row->dayofweek == "SUN") $day1 ="SUNDAY";
                            
                            $string .= $day1 . " " . $start1 . " - " . $end1 . " | " . $start2 . " - " . $end2;
                            
                            $sheet->write($r,$c,$string,$normal);
                            $r++;
                            $string = '';
                        }
                    }else{
                        if($row->dayofweek == "M") $day1 ="MONDAY";
                        else if($row->dayofweek == "T") $day1 ="TUESDAY";
                        else if($row->dayofweek == "W") $day1 ="WENESDAY";
                        else if($row->dayofweek == "TH") $day1 ="THURSDAY";
                        else if($row->dayofweek == "F") $day1 ="FRIDAY";
                        else if($row->dayofweek == "S") $day1 ="SATURDAY";
                        else if($row->dayofweek == "SUN") $day1 ="SUNDAY";

                        $string = $day1 . " " . $start1 . " - " . $end1;
                        $sheet->write($r,$c,$string,$normal);
                        $r++;
                        $string = '';
                    }
                }
            }
        }
        $empcount++;
        $counter++;
        $r++;
        $c=0;
    }

    $empcount = $empcount - 1;
    for ($i=0; $i < 4; $i++) { 
        if($i == 3){
            $sheet->write($r,$i,"TOTAL:",$boldcenter);
            $sheet->write($r,$i+1,$empcount,$boldcenter);
        }else{
            $sheet->write($r,$i,' ',$boldcenter);
        }
    }
    $xls->close();
    
    function displaytablefields($sheet,$r,$c,$fields,$coltitle=''){
        global $coltitles;   
        foreach($fields as $colinfo){ 
        list($caption,$span,$width,$extra) = $colinfo;  
        if($span > 1) $sheet->setMerge($r, $c, $r, (($c-1) + $span)); 
        $sheet->write($r,$c,$caption,$coltitle);
        if(is_array($extra)){
            $xr = $r + 1;
            displaytablefields($sheet,$xr,$c,$extra,$coltitles);  
        }else{
            $sheet->setColumn($c,$c,$width);  
        }
        $c += $span;
        }
    }
    
    


?>