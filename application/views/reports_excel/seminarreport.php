<?php
//KENNEDY 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");

    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Seminar Report.xls");
    $CI =& get_instance();
$CI->load->model('leave_application');
 
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

    $cdata = $this->reports->getSeminarData($scheddeptid, $dateFrom, $dateTo, $sortby, $officeid, $employee, $isactive, $seminar_type);  
    $officeHeader = ($officeid) ? $this->extensions->getOfficeDescriptionReport($officeid) : '';
    $grandtotal = array();
    $grandName = array();
    $rowspan = array();
    // echo "<pre>"; print_r($cdata); die;
    foreach($cdata as $emp){
        if(array_key_exists($emp['employeeid'], $grandtotal)){
            $grandtotal[$emp['employeeid']] = $grandtotal[$emp['employeeid']]+0;
            $rowspan[$emp['employeeid']] = $rowspan[$emp['employeeid']] + 1;
        }else{
            $grandtotal[$emp['employeeid']] = 0;
            $rowspan[$emp['employeeid']] = 1;
        }
    }

    $semiTitle = '';
    if($seminar_type == 'employee_pts') $semiTitle = 'T/A PTI SPIRITUALITY';
else if($seminar_type == 'employee_pts_pdp1') $semiTitle = 'PROFESSIONAL DEVELOPMENT PROGRAM';
else if($seminar_type == 'employee_pts_pdp2') $semiTitle = 'PEP DEVELOPMENT PROGRAM';
else if($seminar_type == 'employee_pts_pdp3') $semiTitle = 'PSYCHOSOCIAL - CULTURAL';
else $semiTitle = "SEMINAR";

    $i = 0;
    $officeL = 9;
    $officeDescStart = 4;
    $fields = array();
    array_push($fields, array("#",1,8,1));
    array_push($fields, array("EMPLOYEE ID",1,20,1));
    array_push($fields, array("FIRST NAME",1,20,1));
    array_push($fields, array("MIDLE NAME",1,20,1));
    array_push($fields, array("LAST NAME",1,20,1));
    array_push($fields, array("DATE",1,30,1));
    array_push($fields, array("PLACE",1,20,1));
    array_push($fields, array("ORGANIZER",1,20,1));
    array_push($fields, array("TITLE",1,40,1));
        $officeL = 15;
        $officeDescStart = 0;
        array_push($fields, array("REGISTRATION FEE",1,20,1));
        array_push($fields, array("TRANSPORTATION",1,20,1));
        array_push($fields, array("ACCOMMODATION",1,20,1));
        array_push($fields, array("OTHER",1,20,1));
        array_push($fields, array("TOTAL",1,20,1));
        array_push($fields, array("GRAND TOTAL",1,30,1));
    $subfields = array();
    $i=0;
    $merge = array();

    $numfield = count( $subfields )-1;

    if($numfield < 5) {
        $numfield = 1;
        $offset = 0;
        $hr = 10;   
    }else{
        $offset = intval(($numfield - 14) / 14);
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
    
    $sheet->insertBitmap( $r , 4  + $offset , $bitmap , $hr , 5 , .09 ,.20 );
    $r++;$c++;
    $sheet->write(0,6,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(1,6,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(2,6,$semiTitle." REPORT",$bigboldcenter);
    $r++;
    $sheet->write(3,6,"Date Range:  ".date("F d, Y", strtotime($dateFrom))." - ".date("F d, Y", strtotime($dateTo)),$normalcenter);
    $r++;
    $sheet->write(4,6,$officeHeader,$normalcenter);

    $r = 6;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$coltitle);

    $r = 7;
     //echo "<pre>".print_r($cdata);die;
    $CS="";
    $display= "";
    $empid2 = '';
    $ofc = '1';
    $row = 0;
    $totalEmpcounter = $counter = 0;
    foreach ($cdata as $emp) 
    {
        $lname = $emp['lname'];
        $fname = $emp['fname'];
        $mname = $emp['mname'];
        $empid = $emp['employeeid'];
        $counter++;
        if($empid2 == $empid){
            $lname = $fname = $mname = $grandtotals = $empid = '';
            $row = 0;
        }else{
            // $grandtotals = number_format($grandtotal[$emp['employeeid']], 2);
            $row = 1;
            $totalEmpcounter++;
        }

        if($sortby=='office' && !$officeid){
            if($ofc == $emp['office']){
                #do nothing..

            }else{
                for ($i=0; $i < $officeL; $i++) { 
                    if($i == $officeDescStart){
                        $sheet->setMerge($r, $i, $r, $i+2);
                        $sheet->write($r,$i,$this->extensions->getOfficeDescriptionReport($emp['office']),$colOffice);
                    }else{
                        $sheet->write($r,$i,' ',$colOffice);
                    }
                }
                $r++;
                $c=0;
            }
        }

        if(isset($emp['other_title'])){
            $emp['seminar_title'] = $emp['seminar_title'] == 'others' ? $emp['other_title'] : $emp['seminar_title'];
        }
        
        $tilRow = ($rowspan[$emp['employeeid']]-1) + $r;
        $sheet->write($r,$c,$counter,$normalcenter);
        $c++;
        $sheet->write($r,$c,$empid,$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $fname),$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $mname),$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $lname),$normalcenter);
        $c++;
        $sheet->write($r,$c,$emp['daterange'],$normalcenter);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE",$emp['location']),$normal);
        $c++;
        $organizer = iconv("UTF-8", "ISO-8859-1//TRANSLIT",$emp['organizer']);
        $sheet->write($r,$c,$organizer,$normal);
        $c++;
        $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//TRANSLIT",$emp['seminar_title']),$normal);
        $c++;
        $sheet->write($r,$c,"PHP ".number_format($emp['regfee'], 2),$normalcenter);
        $c++;
        $sheet->write($r,$c,"PHP ".number_format($emp['transfee'], 2),$normalcenter);
        $c++;
        $sheet->write($r,$c,"PHP ".number_format($emp['accfee'], 2),$normalcenter);
        $c++;
         $sheet->write($r,$c,"PHP ".number_format($emp['otherfee'], 2),$normalcenter);
        $c++;
        $sheet->write($r,$c,"PHP ".number_format($emp['total'], 2),$normalcenter);
        $c++;
        if($row == 1){
            $remAllowance = $CI->leave_application->getRemAllowance($empid);
            $sheet->write($r,$c,"PHP ".number_format($remAllowance, 2),$format_justify_center);
            $sheet->setMerge($r, $c, $tilRow, $c);
        }
        $r++;
        $c=0;
        $tilRow = 0;
        $empid2 = $emp['employeeid'];
        $ofc = $emp['office'];

    } 

    for ($i=0; $i < $officeL; $i++) { 
        if($i == 13){
            $sheet->write($r,$i,"TOTAL EMPLOYEE:",$boldcenter);
            $sheet->write($r,$i+1,$totalEmpcounter,$boldcenter);
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