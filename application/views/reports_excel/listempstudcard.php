<?php
//KENNEDY 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");

    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("RFID Registration List.xls");
 
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
    /* END */
    
    $result = $this->reports->allEmpByPosition();   

    $cdata = $result;

    $i = 0;

    $rowspan = 1;


    $fields = array();
    array_push($fields, array("#",1,25,1));
    array_push($fields, array("EMPLOYEE ID ",1,25,1));
    array_push($fields, array("FULLNAME",1,55,1));
    array_push($fields, array("RFID ",1,25,1));

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
    $sheet->setMerge(5, 0, 5, $numfield);
    foreach($merge as $m)
    {
        $sheet->setMerge(10, $m, 7, $m);
    }

    $c = 1;$r = 0;
    $bitmap = "images/school_logo.bmp";
    
    $sheet->insertBitmap( $r , $c  + $offset , $bitmap , $hr , 8 , .10 ,.20 );
    $r++;$c++;
    $sheet->write(0,2,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(1,2,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(2,2,"RFID Registration List",$bigboldcenter);
    $r++;
    $sheet->write(3,2,"As of ".date("F Y"),$normalcenter);

    $r = 6;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$coltitle);

    $r = 8;
     //echo "<pre>".print_r($cdata);die;
        if($ltype == "E"){
        $num = 0;
        $wC = "WHERE 1";
        if(!empty($deptid)) $wC .= " AND a.deptid = \'{$deptid}\' ";
        if($status != "all"){
            if($status == "active") $wC .= " AND a.isactive = 1";
            else $wC .= " AND a.isactive = 0";
          }
        $query = "CALL prc_employee_card_get(@a,@b,'{$wC}')";
        $employee = $this->db->query($query)->result();
        foreach($employee as $row){
            $c = 0;
            $num++;
            $sheet->write($r,$c,$num,$normalcenter);$c++;
            $sheet->write($r,$c,$row->employeeid,$normalcenter);$c++;
            $sheet->write($r,$c,utf8_decode($row->fullname),$normal);$c++;
            $sheet->writeString($r,$c,$row->employeecode,$normalcenter);
        $r++;
        }
    }else{
        // Student
        $num = 0;
        $wC = "";
        if(!empty($sy))     $wC .= " AND B.SY=\'{$sy}\'";
        if(!empty($sem))    $wC .= " AND B.Sem=\'{$sem}\'";      
        if(!empty($yrlvl))  $wC .= " AND B.YearLevel=\'{$yrlvl}\'";
        if(!empty($sect))   $wC .= " AND a.SectCode=\'{$sect}\'";
        if(!empty($dept))   $wC .= " AND a.CourseCode=\'{$dept}\'";
        $student = $this->db->query("CALL prc_student_card_get(@a,@b,' $wC ')")->result();
        foreach($student as $row){
            $c = 0;
            $num++;
            $sheet->write($r,$c,$num,$normalcenter);$c++;
            $sheet->write($r,$c,$row->StudNo,$normalcenter);$c++;
            $sheet->write($r,$c,utf8_decode($row->fullname),$normal);$c++;
            $sheet->writeString($r,$c,$row->StudCardNo,$normalcenter);
        $r++;
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