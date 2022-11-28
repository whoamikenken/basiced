<?php
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    
    $xls->send("Net Pay History.xls");

 
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
        array_push($fields, array("EMPLOYEE ID",1,20,1));
        array_push($fields, array("NAME",1,30,1));
        array_push($fields, array("AMOUNT",1,20,1));
        array_push($fields, array("CUT-OFF",1,20,1));
        array_push($fields, array("STATUS",1,20,1));

       $numfield = count( $fields )-1;

       $sheet = &$xls->addWorksheet("Sheet 1");
      
       $sheet->setMerge(0, 1, 0, 5);
       $sheet->setMerge(1, 1, 1, 5);
       $sheet->setMerge(2, 1, 2, 5);
       $sheet->setMerge(3, 1, 3, 5);
       $sheet->setMerge(4, 1, 4, 5);

       $c = 0;$r = 0;
       $montlist = Globals::monthList();
      $month = $montlist[$month];

    $bitmap = "images/school_logo.bmp";
    
     $sheet->insertBitmap( $r+1 , $c , $bitmap , 50 , 0 , .15 ,.20 );
    $r++;$c++;
    $sheet->write(0,1,$SCHOOL_NAME,$boldcenter);
    $r++;
    $sheet->write(1,1,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(2,1,"NET PAY HISTORY",$bigboldcenter);
    $r++;
    $sheet->write(3,1,"As of ".date("F Y"),$normalcenter);
    $r++;
    $sheet->write(4,1,"Period Cover ".$month." ".$pyear,$normalcenter);
    $r++;

       $c = 0;$r = 0;

       $r = 7;
       $c = 0;

       displaytablefields($sheet,$r,$c,$fields,$coltitle);
        $ofc = $officeDesc = 'sometext';
        $count = 1;
        $total = 0;
        $officeL = 6;
        $officeDescStart = 1;
        $totalStart = 2;
        $grandtotalStart = 3;
        foreach ($emplist as $key  => $val) {
          if($sort=='office'){
              if($ofc !== $val->office){
                  if($officeDesc != $this->extensions->getOfficeDescriptionReport($val->office)){
                      for ($i=0; $i < $officeL; $i++) { 
                          if($i == $totalStart){
                              $sheet->write($r,$i,"                                  TOTAL:",$boldcenter);
                          }else if($i == $grandtotalStart){
                              $sheet->write($r,$i,number_format($total, 2),$boldcenter);
                          }else{
                              $sheet->write($r,$i,' ',$bold);
                          }
                      }
                      $r++;
                      $c=0;
                      $total = 0;
                      for ($i=0; $i < $officeL; $i++) { 
                          if($i == $officeDescStart){
                              $sheet->write($r,$i,$this->extensions->getOfficeDescriptionReport($val->office),$boldcenter);
                          }else{
                              $sheet->write($r,$i,' ',$boldcenter);
                          }
                      }
                      $r++;
                      $c=0;
                  }
              }
          }

          $officeDesc = $this->extensions->getOfficeDescriptionReport($val->office);
          $ofc = $val->office;

          $sheet->write($r,$c,$count,$normalcenter);
          $c++;
          $sheet->write($r,$c,$val->employeeid,$normalcenter);
          $c++;
          $sheet->write($r,$c,iconv("UTF-8", "ISO-8859-1//IGNORE", $val->fullname),$normalcenter);
          $c++;
          $sheet->write($r,$c,number_format($val->net, 2),$normalcenter);
          $c++;
          $sheet->write($r,$c,date('M d-',strtotime($val->cutoffstart)) . date('d, Y',strtotime($val->cutoffend)),$normalcenter);
          $c++;
          $sheet->write($r,$c,$val->status,$normalcenter);
          $c++;
          $r++;
          $c=0;
          $count++;
          $total = $total + $val->net;
       }

       for ($i=0; $i < $officeL; $i++) { 
            if($i == $totalStart){
                $sheet->write($r,$i,"                                  TOTAL:",$boldcenter);
            }else if($i == $grandtotalStart){
                $sheet->write($r,$i,number_format($total, 2),$boldcenter);
            }else{
                $sheet->write($r,$i,' ',$bold);
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