<?php
//KENNEDY 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");

    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Employees With Sync Logs Report.xls");
 
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

    $alllogsdata = $this->reports->allLogsData($deptid, $dateFrom, $dateTo); 

    $i = 0;

    $fields = array();
    array_push($fields, array("#",1,5,1));
    array_push($fields, array("EMPLOYEE ID",1,20,1));
    array_push($fields, array("EMPLOYEE NAME",1,30,1));
    array_push($fields, array("DEPARTMENT",1,30,1));
    array_push($fields, array("POSITION",1,30,1));
    array_push($fields, array("PROCESSED BY",1,20,1));
    array_push($fields, array("DATE PROCESSED",1,20,1));

    $subfields = array();
    $i=0;
    $merge = array();

    $numfield = count( $subfields )-1;

    if($numfield < 5) {
        $numfield = 1;
        $offset = 0;
        $hr = 10;   
    }else{
        $offset = intval(($numfield - 13) / 13);
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
    
    $sheet->insertBitmap( $r , 2  + $offset , $bitmap , $hr , 10 , .10 ,.20 );
    $r++;$c++;
    $sheet->write(1,3,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(2,3,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(3,3,"Employees With Sync Logs Report",$bigboldcenter);
    $r++;
    $sheet->write(4,3,"As of ".date("F Y"),$normalcenter);
    $r++;


    $r = 7;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$coltitle);

    $r = 8;
     // echo "<pre>"; print_r($cdata);die;
    $CS="";
    $display= "";
    $name = '';
    $ofc = '1';
    $row = $counter = 0;
    $empcount = 1;
    foreach ($alllogsdata as $emp) 
    {

        $sheet->write($r,$c,$empcount,$normalcenter);
        $c++;
        $sheet->write($r,$c,$emp->IDNumber,$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $emp->FirstName." ".$emp->MiddleName." ".$emp->LastName),$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE",$emp->DepartmentName),$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE",$emp->PositionName),$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE",$this->extensions->getEmployeeName($emp->processed_by)),$normalcenter);
        $c++;
        $sheet->write($r,$c,date("F d, Y", strtotime($emp->timestamp)),$normalcenter);
        $r++;
        $c=0;
        $empcount++;


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