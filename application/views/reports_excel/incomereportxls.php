<?php
 #modified by Argyron Naces
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    
    $xls->send("Income Report.xls");

 
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();
    $normalright =& $xls->addFormat(array('Size' => 10));
    $normalright->setAlign("right");
    $normalright->setLocked();
    $normalBold =& $xls->addFormat(array('Size' => 10));
    $normalBold->setBold();
    $normalBold->setAlign("right");
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

    function formatAmount($amount=''){
     $return ="";
     if($amount)
     {
      $amount = floatval($amount);
     $return = number_format($amount,2);
     }
     else
     {
     $return = '0.00';
     }
     return $return;
    }
    
       $tnt         = ($_GET['tnt'] ? $_GET['tnt'] : $_POST['tnt']);
       $cutoff      = ($_GET['cutoff'] ? $_GET['cutoff'] : $_POST['cutoff']);
       $income      = ($_GET['income'] ? $_GET['income'] : $_POST['income']);
       $sort      = ($_GET['sort'] ? $_GET['sort'] : $_POST['sort']);
       if(in_array("selectAll", $income)) $income = array("selectAll");

       $cutoffdisplay = "";

       $cutoff_arr      = array();
       $cutoffstart    = "";
       $cutoffend      = "";

       if($cutoff){
        $cutoff_arr = explode(",", $cutoff);
        $cutoffstart = $cutoff_arr[0];
        $cutoffend = $cutoff_arr[1];
        $cutoffdisplay = date_format(date_create($cutoffstart),"F d, Y") . " - " . date_format(date_create($cutoffend),"F d, Y");
       }

       $payrollcutoff = $this->extras->getPayrollCutoff($cutoffstart, $cutoffend);
       foreach($payrollcutoff as $cutoff_data){
          $cutoffstart = $cutoff_data['startdate'];
          $cutoffend = $cutoff_data['enddate'];
       }

       $fields = array(
                        array("#",1,15,1),
                        array("Employee ID",1,20,1),
                        array("EMPLOYEE NAME",1,30,1),
                        array("AMOUNT",1,20,1),
                  );
       

       $numfield = count( $fields )-1;

       $sheet = &$xls->addWorksheet("Sheet 1");
      
       $sheet->setMerge(0, 1, 0, $numfield);
       $sheet->setMerge(1, 1, 1, $numfield);
       $sheet->setMerge(2, 1, 2, $numfield);
       $sheet->setMerge(3, 1, 3, $numfield);
       $sheet->setMerge(4, 1, 4, $numfield);


       $c = 0;$r = 0;
       $bitmap = "images/school_logo.bmp";
       $sheet->insertBitmap( $r , $c , $bitmap , 15 , 0 , .25 ,.40 );
       $r++;$c++;
       $sheet->write(1,1,$SCHOOL_NAME,$bigboldcenter);
       $r++;
       $sheet->write(2,1,"A prime institution in the field of medical science",$normalcenter);


       $r = 6;
       $c = 0;

       $grandtotalIncome = 0;
       if(sizeof($income) > 0){
            $sheet->write($r,$c,"OTHER INCOME TRANSACTION REPORT",$bold);
            $r++;
            $sheet->write($r,$c,"Cut off:",$bold);
            $c++;
            $sheet->write($r,$c,$cutoffdisplay,$normal);
            $r++;$r++;$c=0;

            
            foreach ($income as $key => $value) {
                $desc = $this->reports->getPayrollIncomeConfig("payroll_income_config",$value,$sort);
       
                foreach ($desc as $k => $v) {
                        
                  $emplist = $this->reports->getEmployeeListByIncome($v['id'], $cutoffstart, $cutoffend, $tnt, "", $sort);
                  $r_count=1;
                  $subtotalincome = 0;
                        
                  $content = array();
                  foreach ($emplist as $key => $emp) {
                    $eincome = explode("/",$emp['income']);
                            
                    for($x=0;$x < count($eincome);$x++){
                      $eeincome = explode("=",$eincome[$x]);
                      
                      if($eeincome[0] == $v['id']){
                        $grandtotalIncome += $eeincome[1];
                        $subtotalincome += $eeincome[1];

                        $content[] = array(
                          $r_count,
                          $emp['employeeid'],
                          $emp['fullname'],
                          formatAmount($eeincome[1])
                        );

                        $r_count++;
                      }
                    }         
                  }
                        
                

                  if($subtotalincome){
                    $r++; $c=0;
                    $sheet->write($r,$c,"Description:",$bold); $c++;                    
                    $sheet->write($r,$c,$v['description'],$normal);

                    $r++;$c=0;
                    $sheet->write($r,$c,"Taxable:",$bold);$c++;
                    $sheet->write($r,$c,$v['taxable'],$normal);
                    
                    // table  header
                    $r++;$c=0;
                    displaytablefields($sheet,$r,$c,$fields,$boldcenter);$r++;

                    // table content
                    foreach ($content as $info) {
                      $c=0;
                      $sheet->write($r, $c, $info[0], $normalcenter); $c++;
                      $sheet->write($r, $c, $info[1], $normalcenter); $c++;
                      $sheet->write($r, $c, $info[2], $normal); $c++;
                      $sheet->write($r, $c, $info[3], $normalright); $c++;
                      $r++;
                    }

                    $r++; $c=0;
                    $sheet->write($r,$c,"Sub Total: ",$normalBold);   
                    $sheet->write($r,3,formatAmount($subtotalincome),$normalBold);
                    $r++;
                  }
                }
            } 
            //GRAND TOTAL..
           
            $sheet->write($r,$c,"Grand Total: ",$normalBold);   
            $sheet->write($r,3,formatAmount($grandtotalIncome),$normalBold);
            $r++;
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