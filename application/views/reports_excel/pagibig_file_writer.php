<?php

    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    $xls = New Spreadsheet_Excel_Writer();
    
    $xls->send("PAGIBIG File Writer.xls");

 
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();
    $normalBold =& $xls->addFormat(array('Size' => 10));
    $normalBold->setBold();
    $normalBold->setAlign("right");
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();
 
    
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
    
    
    $big =& $xls->addFormat(array('Size' => 12));
    $big->setLocked();
    
    $bigbold =& $xls->addFormat(array('Size' => 11));
    $bigbold->setBold();
    $bigbold->setColor("blue");
    $bigbold->setLocked();
    
    $bigboldcenter =& $xls->addFormat(array('Size' => 12));
    $bigboldcenter->setBold();
    $bigboldcenter->setAlign("center");
    $bigboldcenter->setColor("blue");
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
 
    /* END */
    
     
       $fields = array(
                        array("Pag-IBIG ID",1,20,1),
                        array("Employee ID",1,20,1),
                        array("Last Name",1,20,1),
                        array("First Name",1,20,1),
                        array("Middle Name",1,20,1),
                        array("Employee Contribution",1,20,1),
                        array("Employer Contribution",1,20,1),
                        array("TIN",1,20,1),
                        array("Birth Date (YYYYMMDD)",1,20,1),
                  );

       
       $sheet = &$xls->addWorksheet("Sheet 1");
      
       $bitmap = "images/school_logo_bm.bmp";
       $sheet->insertBitmap( 0 , 3 , $bitmap , 15 , 0 , .25 ,.40 );
       $sheet->write(1,4,strtoupper("                                                  ".$employer_info['SCHOOL_NAME']),$bigboldcenter);
       $sheet->write(2,4,utf8_decode("                         Dasmariñas Cavite"),$bigbold);
       $sheet->write(3,4,"               As of ". date('F d Y',strtotime($sdate)).' - '.date('F d Y',strtotime($edate)) ,$normal);

       $c = 0;$r = 0;
       $sheet->write(7,0,"Employer's Name:",$bold);
       $sheet->write(7,1,$employer_info['SCHOOL_NAME'],$bold);
       $sheet->write(8,0,"Address:",$bold);
       $sheet->write(8,1,$employer_info['ADDRESS'],$bold);
       $sheet->write(9,0,"Zip Code:",$bold);
       $sheet->writeString(9,1,$employer_info['ZIP_CODE'],$bold);
       $sheet->write(10,0,"Employer Type:",$bold);
       $sheet->write(10,1,$employer_info['EMPLOYER_TYPE'],$bold);

       $sheet->write(7,5,"Contact Number:",$bold);
       $sheet->writeString(7,6,$employer_info['CONTACT_NO'],$bold);
       $sheet->write(8,5,"BR Code:",$bold);
       $sheet->writeString(8,6,$employer_info['BR_CODE'],$bold);
       $sheet->write(9,5,"SSS/GSIS/Pag-IBIG ID:",$bold);
       $sheet->writeString(9,6,$employer_info['PAGIBIG_ID'],$bold);
       $sheet->write(10,5,"Type of Payment:",$bold);
       $sheet->write(10,6,"MC - Members contribution",$bold);

       $sheet->write(12,0,"BANK",$bold);
       $sheet->write(12,1,$bank,$bold);
       $sheet->write(13,0,"CUTOFF",$bold);
       $sheet->write(13,1,$sdate . ' to ' . $edate,$bold);


       $r = 15;
       $c = 0;

       displaytablefields($sheet,$r,$c,$fields,$boldcenter);
       $r++;
       foreach ($contri_list as $deptid  => $emplist) {
          foreach ($emplist as $employeeid => $empdetail) {
            $sheet->writeString($r,$c,$empdetail['emp_pagibig'],$normal);
            $c++;
            $sheet->writeString($r,$c,$employeeid,$normal);
            $c++;
            $sheet->write($r,$c,$empdetail['lname'],$normal);
            $c++;
            $sheet->write($r,$c,$empdetail['fname'],$normal);
            $c++;
            $sheet->write($r,$c,$empdetail['mname'],$normal);
            $c++;
            $sheet->write($r,$c,isset($empdetail[$code_deduction]['EE'])?$empdetail[$code_deduction]['EE']:0,$amount);
            $c++;
            $sheet->write($r,$c,isset($empdetail[$code_deduction]['ER'])?$empdetail[$code_deduction]['ER']:0,$amount);
            $c++;
            $sheet->write($r,$c,$empdetail['emp_tin'],$normal);
            $c++;
            $sheet->writeString($r,$c,date('Ymd',strtotime($empdetail['bdate'])),$normal);
            $c=0;$r++;
          }
       }


       $xls->close();


       function displaytablefields(&$sheet,$r,$c,$fields,$coltitle=''){
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