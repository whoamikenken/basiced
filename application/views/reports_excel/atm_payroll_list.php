<?php
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    // require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    
    $xls->send("ATM Payroll List.xls");

 
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

    
    $colnumber =& $xls->addFormat(array('Size' => 8));
    $colnumber->setNumFormat("#,##0.00");
    $colnumber->setBorder(1);
    $colnumber->setAlign("center");
    $colnumber->setLocked();

    $coltitle =& $xls->addFormat(array('Size' => 10));
    $coltitle->setBorder(2);
    $coltitle->setBold();
    $coltitle->setAlign("center");
    $coltitle->setFgColor('black');
    $coltitle->setColor('yellow');
    $coltitle->setLocked();
    
        
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

    $colOffice =& $xls->addFormat(array('Size' => 10));
    // $colOffice->setBorder(2);
    $colOffice->setBold();
    $colOffice->setAlign("center");
    $colOffice->setFgColor('yellow');
    $colOffice->setColor('black');
    $colOffice->setLocked();
   
    /* END */
    
     

       $fields = array();
        array_push($fields, array("#",1,5,1));
        array_push($fields, array("ACCOUNT NUMBER",1,20,1));
        array_push($fields, array("NET SALARY",1,30,1));
        array_push($fields, array("EMPLOYEE NAME",1,20,1));
        array_push($fields, array("BANK",1,20,1));

       $numfield = count( $fields )-1;

       $sheet = &$xls->addWorksheet("Sheet 1");
      
       $sheet->setMerge(0, 1, 0, 5);
       $sheet->setMerge(1, 1, 1, 5);
       $sheet->setMerge(2, 1, 2, 5);
       $sheet->setMerge(3, 1, 3, 5);
       $sheet->setMerge(4, 1, 4, 5);

       $c = 0;$r = 0;

    $bitmap = "images/school_logo.bmp";
    
     $sheet->insertBitmap( $r , $c , $bitmap , 50 , 0 , .15 ,.20 );
    $r++;$c++;
    $sheet->write(0,1,"Pinnacle Technologies Inc.",$boldcenter);
    $r++;
    $sheet->write(1,1,"D`Great",$boldcenter);
    $r++;
    $sheet->write(2,1,"ATM Payroll List",$bigboldcenter);
    $r++;
    $sheet->write(3,1,"As of ".date("F Y"),$normalcenter);
    $r++;

       $c = 0;$r = 0;

       $r = 6;
       $c = 0;

       // displaytablefields($sheet,$r,$c,$fields,$coltitle);
        $count = 1;
        $total = 0;
  //       if(isset($list)){
    //  foreach($list as $employeeid => $emp_info){
    //    $net_salary = (float) $emp_info['net_salary'];
    //    $sheet->write($r,$c,$count,$normalcenter);
   //           $c++;
   //           $sheet->write($r,$c,$emp_info['account_num'],$normalcenter);
   //           $c++;
   //           $sheet->write($r,$c,trim("  ".number_format($net_salary, 2, '.', '')),$normalcenter);
   //           $c++;
   //           $sheet->write($r,$c,utf8_encode(strtoupper($emp_info['fullname'])),$normalcenter);
   //           $c++;
   //           $sheet->write($r,$c,$bank_name,$normalcenter);
    //    $count++;
    //    $r++;
  //            $c=0;
  //            $total += $emp_info['net_salary'];
    //  }
    // }

  //      for ($i=0; $i < 5; $i++) { 
  //           if($i == 3){
  //               $sheet->write($r,$i,"TOTAL:",$boldcenter);
  //           }else if($i == 4){
  //               $sheet->write($r,$i,number_format($total, 2),$boldcenter);
  //           }else{
  //               $sheet->write($r,$i,' ',$bold);
  //           }
  //       }
       


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