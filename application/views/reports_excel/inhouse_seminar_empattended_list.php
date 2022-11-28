<?php
//KENNEDY 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");

    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Seminar Report.xls");
 
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

    $inhouse_title = "Seminar Attendees";

    $fields = array();
    array_push($fields, array("EMPLOYEE ID",1,20,1));
    array_push($fields, array("FULLNAME",1,30,1));
    array_push($fields, array("LOGIN",1,30,1));
    array_push($fields, array("LOGOUT",1,30,1));

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
   

    $c = 0;$r = 0;
    $bitmap = "images/school_logo.bmp";
    $sheet->insertBitmap( $r , 1  + $offset , $bitmap , $hr , 5 , .10 ,.20 );
    $r++;$c++;
    $sheet->write(0,2,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(1,2,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(2,2,"SEMINAR ATTENDEES REPORT",$bigboldcenter);
    $r++;
    $sheet->write(3,2,$inhouse_title,$normalcenter);
    $r++;
    $sheet->write(4,2,$inhouseAttendance,$normalcenter);
        
    $r = 6;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$coltitle);

    $r = 7;
    foreach ($attendedEmployee as $seminarid => $userid){
        foreach ($userid as $k => $v){
            $login = $logout = '';
            foreach ($v as $value){
                $fullname = Globals::_e($value['fullname']);
                $timein = date("h:i:s A", strtotime($value['localtimein']));
                if($value['log_type'] == 'IN') $login = $timein;
                else $logout = $timein;
            }
            $sheet->write($r,$c,$k,$normalcenter);
            $c++;
            $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $fullname),$normalcenter);
            $c++;
            $sheet->write($r,$c,$login,$normalcenter);
            $c++;
            $sheet->write($r,$c,$logout,$normalcenter);
            $c++;
           
            $r++;
            $c=0;
            $tilRow = 0;
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