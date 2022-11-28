<?php
 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Reglamantory Deduction.xls");

 
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();

    $normalcenter2 =& $xls->addFormat(array('Size' => 10));
    $normalcenter2->setAlign("center");
    $normalcenter2->setNumFormat("#");
    $normalcenter2->setLocked();

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
    $amountbold->setAlign("right");
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
    
      $division  = ($_GET['division'] ? $_GET['division'] : $_POST['division']);
      $department  = ($_GET['department'] ? $_GET['department'] : $_POST['department']);
      $cutoff  = ($_GET['cutoff'] ? $_GET['cutoff'] : $_POST['cutoff']);
      $deduction  = ($_GET['deduction'] ? $_GET['deduction'] : $_POST['deduction']);
      $type = "";

      $result = $this->reports->rdc($division,$department,$cutoff,$deduction);

      if($deduction == "PAGIBIG") $type = "PAG-IBIG";
      else $type = $deduction;

       $fields = array(
                        array("Employee ID",1,13,1),
                        array("EMPLOYEE NAME",1,30,1),
                        array("SSS NUMBER",1,15,1),
                        array("EE",1,9,1),
                        array("EC",1,9,1),
                        array("ER",1,9,1),
                        array("TOTAL",1,9,1)
                  );
       

       $numfield = count( $fields )-1;

       $sheet = &$xls->addWorksheet("Sheet 1");
      
       $sheet->setMerge(0, 0, 0, $numfield);
       $sheet->setMerge(1, 0, 1, $numfield);
       $sheet->setMerge(2, 0, 2, $numfield);
       $sheet->setMerge(3, 0, 3, $numfield);
       $sheet->setMerge(4, 0, 4, $numfield);
       $sheet->setMerge(5, 0, 5, $numfield);
       $sheet->setMerge(6, 0, 6, $numfield);

       $c = 0;$r = 0;
       $bitmap = "images/school_logo_bm.bmp";
       $sheet->insertBitmap( 0 , 1 , $bitmap , 0 , 3 , .15 ,.40 );
       $sheet->write(1,0,$SCHOOL_NAME,$bigboldcenter);
       $sheet->write(2,0,$SCHOOL_CAPTION,$normalcenter);
       $sheet->write(4,0,$type." Contribution",$boldcenter);
       $sheet->write(5,0,"For the month of ".$cutoff,$boldcenter);

       $r = 7;
       $c = 0;
       displaytablefields($sheet,$r,$c,$fields,$boldcenter);
       $r+=2;

       $page_ee = 0;
       $page_ec= 0;
       $page_er = 0;
       $page_total = 0;


       if(sizeof($result) > 0){
            foreach ($result as $key => $row) {
              $t = "";
              if($type == "SSS") $t = $row->emp_sss;
              else if($type == "PHILHEALTH") $t = $row->emp_philhealth;
              else if($type == "PAGIBIG") $t = $row->emp_pagibig;

              $sheet->writeString($r,$c,$row->employeeid,$normalcenter);
              $c++;
              $sheet->write($r,$c,$row->fullname,$normal);
              $c++;
              $sheet->write($r,$c,$t,$normalcenter2);
              $c++;
              $sheet->write($r,$c,$row->amount,$amount);
              $c++;
              $sheet->write($r,$c,$row->ec,$amount);
              $c++;
              $sheet->write($r,$c,$row->amounter,$amount);
              $c++;
              $sheet->write($r,$c,$row->amounttotal,$amount);
              $c=0;$r++;

              $page_ee += $row->amount;
              $page_ec += $row->ec;
              $page_er += $row->amounter;
              $page_total += $row->amounttotal;
            }
            $c=2;$r++;
            $sheet->write($r,$c,"TOTAL",$boldcenter);
            $c++;
            $sheet->write($r,$c,$page_ee,$amountbold);
            $c++;
            $sheet->write($r,$c,$page_ec,$amountbold);
            $c++;
            $sheet->write($r,$c,$page_er,$amountbold);
            $c++;
            $sheet->write($r,$c,$page_total,$amountbold);

       }

       // $r+=2;
       // $sheet->write($r, $c, date('m-d-y H:i'), $normal);


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