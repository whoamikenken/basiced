<?php
//KENNEDY 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");

    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Employee List By Birthday.xls");
 
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

    $colnumber =& $xls->addFormat(array('Size' => 8));
    $colnumber->setNumFormat("#,##0.00");
    $colnumber->setBorder(1);
    $colnumber->setAlign("center");

    $colOffice =& $xls->addFormat(array('Size' => 10));
    // $colOffice->setBorder(2);
    $colOffice->setBold();
    $colOffice->setAlign("center");
    $colOffice->setFgColor('yellow');
    $colOffice->setColor('black');
    $colOffice->setLocked();
 
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
    
    $type  =  isset($birthdayInfo) ? $birthdayInfo : '';
    $month    =  isset($empBirthdayMonth) ? $empBirthdayMonth : '';
    $isactive    = isset($isactive) ? $isactive : '';

    if ($type == "Age" && $month == "All") {
        $report = "AGE REPORT";
        $result  = $this->reports->loadempbirthdayreportage($isactive);   
    }else if($type == "Month" && $month == "All"){
        $report = "ALL MONTH";
        $result  = $this->reports->loadempbirthdayreportall($isactive); 
    }else{
        $dateObj   = DateTime::createFromFormat('!m', $month);
        $report = $dateObj->format('F');
        $result  = $this->reports->loadempbirthdayreportmonth($month, $isactive); 
    }

    $cdata = $result;

    $i = 0;

    $rowspan = 1;
// echo "<pre>".print_r($cdata);die;

    $fields = array();
    array_push($fields, array("# ",1,5,1 ,));
    array_push($fields, array("EMPLOYEE ID ",1,25,1 ,));
    array_push($fields, array("EMPLOYEE NAME",1,25,1));
    array_push($fields, array("OFFICE",1,25,1));
    array_push($fields, array("BIRTHDAY",1,25,1));
    array_push($fields, array("AGE",1,25,1));

    $subfields = array();
    $i=0;
    $merge = array();

    $numfield = count( $subfields )-1;

    if($numfield < 5) {
        $numfield = 1;
        $offset = 0;
        $hr = 10;   
    }else{
        $offset = intval(($numfield - 2) / 2);
        $hr = 0;
    }

    $sheet = &$xls->addWorksheet("Sheet 1");
   
    $sheet->setMerge(0, 0, 0, $numfield);
    $sheet->setMerge(1, 0, 1, $numfield);
    $sheet->setMerge(2, 0, 2, $numfield);
    $sheet->setMerge(3, 0, 3, $numfield);
    $sheet->setMerge(4, 0, 4, $numfield);
    
    foreach($merge as $m)
    {
        $sheet->setMerge(6, $m, 7, $m);
    }
    // $sheet->setMerge(5, 0, 5, $numfield);
    // $sheet->setMerge(6, 0, 6, $numfield);

    $c = 0;$r = 0;
    $bitmap = "images/school_logo.bmp";
    
    $sheet->insertBitmap( $r , 1  + $offset , $bitmap , $hr , 8 , .10 ,.20 );
    $r++;$c++;
    $sheet->write(0,3,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(1,3,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(2,3,"EMPLOYEE'S BIRTHDAY BY ".strtoupper($report),$bigboldcenter);
    $r++;
    $sheet->write(3,3,"As of ".date("F Y"),$normalcenter);

    $r = 6;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$coltitle);

    $r = 7;
    $empcount = 1;
    foreach ($cdata as $emp) {
        $sched="";
        $sheet->write($r,$c,$empcount,$normalcenter);
        $c++;
        $sheet->write($r,$c,$emp->employeeid,$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $emp->fullname),$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE",$this->extras->getDeptDesc($emp->deptid)),$normalcenter);
        $c++;
        if($emp->bdate == "0000-00-00" || $emp->bdate == "1970-01-01" || $emp->bdate == null){
            $sheet->write($r,$c,'',$normalcenter);
        }else{
            $sheet->write($r,$c,date('F  d,  Y', strtotime($emp->bdate)),$normalcenter);
        }
        
        $c++;
        if (date_diff(date_create($emp->bdate), date_create('now'))->y == '2019') {
            $sheet->write($r,$c,"",$normalcenter);
        }else{
            $bday = new DateTime($emp->bdate);
            $today = new Datetime(date('m.d.y'));
            $diff = $today->diff($bday);
            if($emp->bdate == "0000-00-00" || $emp->bdate == "1970-01-01" || $emp->bdate == null){
                $sheet->write($r,$c,'',$normalcenter);
            }
            else{
                $sheet->write($r,$c,$diff->y,$normalcenter);
            }
            
        }
        $c++;
        $r++;
        $empcount++;
        $c=0;
    } 

    $empcount = $empcount - 1;
    for ($i=0; $i < 6; $i++) { 
        if($i == 4){
            $sheet->write($r,$i,"TOTAL:",$boldcenter);
            $sheet->write($r,$i+1,$empcount,$boldcenter);
        }else{
            $sheet->write($r,$i,' ',$boldcenter);
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