<?php
 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send(($isMRRReport) ? "MONTHLY REMITTANCE RETURN OF INCOME TAX W/HELD (1601-C).xls" : "Reglamantory Deduction.xls");

    
    function changeenye($enye = "") {
        $return = "";
        $return = str_replace("Ã‘","Ñ",$enye);
        return utf8_decode($return);
    }

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
    $amount->setAlign("right");
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

      $isRDCForm = ($isMRRReport) ? false : true;

      $result = $this->reports->rdc($division,$department,$cutoff,$deduction, $isRDCForm);


      if($deduction == "PAGIBIG") $type = "PAG-IBIG";
      else $type = $deduction;

      
      $cutoff_name = '';
      $arr_cutoff = array();

      if($isRDCForm){
        $arr_cutoff = $this->employeemod->getCutoff($cutoff);            
        $exp_co = explode("~~", $cutoff); 
        $cutoff_name = $exp_co[0];
      }else{
        $cutoff_name = $cutoff;
       
        list($start_date, $end_date) = explode(",",$cutoff);
        $arr_cutoff[] = array(
          'start_date' => $start_date,
          'end_date' => $end_date
        );

       }

       $fields = array(
                        array("Employee ID",1,13,1),
                        array("EMPLOYEE NAME",1,40,1),
                        array("$type NUMBER",1,15,1),
                        array((($type != "PHILHEALTH") ? "Gross" : "Basic") ." Pay",1,30,1),
                        array("EE",1,15,1),
                        array("EC",1,15,1),
                        array("ER",1,15,1),
                        array("TOTAL",1,15,1)
                  );
       

       $numfield = (count( $fields ) + count( $arr_cutoff ))-1;

       $sheet = &$xls->addWorksheet("Sheet 1");
      
       $sheet->setMerge(0, 0, 0, $numfield);
       $sheet->setMerge(1, 0, 1, $numfield);
       $sheet->setMerge(2, 0, 2, $numfield);
       $sheet->setMerge(3, 0, 3, $numfield);
       $sheet->setMerge(4, 0, 4, $numfield);
       $sheet->setMerge(5, 0, 5, $numfield);
       $sheet->setMerge(6, 0, 6, $numfield);


       $c = 0;$r = 0;
       $bitmap = "images/school_logo.bmp";
       $sheet->insertBitmap( 0 , 1 , $bitmap , 0 , 3 , .15 ,.45 );
       $sheet->write(1,0,$SCHOOL_NAME,$bigboldcenter);
       $sheet->write(2,0,$SCHOOL_CAPTION,$normalcenter);
       $sheet->write(4,0,$type." Contribution",$boldcenter);
       $sheet->write(5,0,"For the month of ".$cutoff_name,$boldcenter);

       $r = 7;
       $c = 0;
      
     
       foreach ($fields as $key => $h_info) {
     
         $sheet->write($r, $c, $h_info[0], $boldcenter);
         $sheet->setColumn($c,$c, $h_info[2]);
         
         
         if($key == 3){
          $sheet->setMerge($r, $c , $r , $c + count( $arr_cutoff ));
          $c += (1 + count( $arr_cutoff ));
         }else{
          $sheet->setMerge($r, $c , $r + 1, $c);
          $c++;
         }
       }
       $r++;

       $c = 3;
       foreach ($arr_cutoff as $key => $l_cutoff) {
          extract($l_cutoff);
          $date = date("M", strtotime($start_date)).' '.date("d",strtotime($start_date)).'-'.date("d",strtotime($end_date)).', '. date("Y", strtotime($start_date));
          $size_col  = 30 / count($arr_cutoff) + 1;
          $sheet->write($r, $c, $date, $boldcenter);
          $sheet->setColumn($c,$c, $size_col);
          $c++;
       }

        $size_col  = 30 / count($arr_cutoff) + 1;
        $sheet->write($r, $c, "Total", $boldcenter);
        $sheet->setColumn($c,$c, $size_col);
        $c++;

       $r+=1;

       $page_ee = 0;
       $page_ec= 0;
       $page_er = 0;
       $page_total = 0;


       if(sizeof($result) > 0){
            $page_gob = 0;
            foreach ($result as $key => $row) {
              

              $fixeddeduc = $row->fixeddeduc;
              if($fixeddeduc != "" || $fixeddeduc != NULL){
                $efixeddeduc = explode("/", $fixeddeduc);
                for($x=0;$x < count($efixeddeduc);$x++){
                  $eefixeddeduc = explode("=",$efixeddeduc[$x]);
                  if($eefixeddeduc[0] == $deduction) $ee = $eefixeddeduc[1];
                }
                
              }
              if($deduction == "PAGIBIG") $table = 'hdmf_deduction';  
              else $table = strtolower($type).'_deduction';

              $sqlDed = $this->db->query("SELECT * FROM $table WHERE emp_ee ='$ee'")->result_array();


              $deduc = $deduction;

              $t = "";
              $t = "";
                if($deduc == "SSS"){
                $ec = $sqlDed[0]['emp_con'];
                $er = $sqlDed[0]['emp_er'];
                $t = $row->emp_sss;
                $totalCont = $sqlDed[0]['total_contribution'];
                }else if($deduc == "PHILHEALTH"){
                $er = $ee;
                $totalCont = $ee + $er;
                $t = $row->emp_philhealth;
                }else if($deduc == 'PAGIBIG'){
                $er = $ee;
                $totalCont = $ee + $er;
                $t = $row->emp_pagibig;
                }

              if($ee > 0){
                $c = 0;
                $sheet->writeString($r,$c,$row->employeeid,$normalcenter);
                $c++;
                $sheet->write($r,$c,changeenye($row->fullname),$normal);
                $c++;
                $sheet->write($r,$c,$t,$normalcenter2);
                $c++;

                $gob = 0;
                $column = ($type != "PHILHEALTH") ? "gross" : "salary";
                $col_val = "";
                                
                $td_val = '';
                $row_val = 0;
                foreach ($arr_cutoff as $key => $l_cutoff) {
                  extract($l_cutoff);
                  $q_amount = $this->db->query("SELECT $column AS amount FROM payroll_computed_table WHERE employeeid='{$row->employeeid}' AND cutoffstart='{$start_date}' AND cutoffend='{$end_date}' AND bank <> '';")->result();
                  $amount_gob = 0;
                  foreach ($q_amount as $res) {
                    $amount_gob += floatval($res->amount);
                    $row_val += floatval($res->amount);
                    $gob += $res->amount;
                  }

                  if(count($q_amount) == 0 ){
                    $sheet->write($r,$c,$row_val,$amount);
                  }else{
                    $sheet->write($r,$c,$amount_gob,$amount);
                  }
                  $c++;
                }
                $sheet->write($r,$c,$row_val,$amount);

                if($type == "PAG-IBIG" && $row_val < 1500){
                  $ee = $row_val * (0.01);
                  $er  = $ee * 2; 
                }
                
                $c++;
                $sheet->write($r,$c,number_format($ee,2),$amount);
                $c++;
                $sheet->write($r,$c,number_format($ec,2),$amount);
                $c++;
                $sheet->write($r,$c,number_format($er,2),$amount);
                $c++;
                $sheet->write($r,$c,$totalCont,$amount);
                $c=0;$r++;

                $page_gob += $gob;
              }

              $page_ee += $ee;
              $page_ec += $ec;
              $page_er += $er;
              $page_total += $totalCont;
            }
            $c=2 + count($arr_cutoff);$r++;
            $sheet->write($r,$c,"TOTAL",$boldcenter);
            $c++;
            $sheet->write($r,$c,$page_gob,$amountbold);
            $c++;
            $sheet->write($r,$c,$page_ee,$amountbold);
            $c++;
            $sheet->write($r,$c,$page_ec,$amountbold);
            $c++;
            $sheet->write($r,$c,$page_er,$amountbold);
            $c++;
            $sheet->write($r,$c,$page_total,$amountbold);

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