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
    $colnumber->setLocked();
    
    
    $big =& $xls->addFormat(array('Size' => 12));
    $big->setLocked();
    
    $bigbold =& $xls->addFormat(array('Size' => 11));
    $bigbold->setBold();
    $bigbold->setLocked();
    
    $bigboldcenter =& $xls->addFormat(array('Size' => 12));
    $bigboldcenter->setBold();
    $bigboldcenter->setAlign("center");
    $bigboldcenter->setLocked();
    
    $boldRcenter =& $xls->addFormat(array('Size' => 8));
    $boldRcenter->setBold();
    $boldRcenter->setAlign("right");
    $boldRcenter->setLocked();
    
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
    
      $division  =  isset($division) ? $division : '';
$department  =  isset($department) ? $department : '';
$cutoff  =  isset($cutoff) ? $cutoff : '';
$deduction  =  isset($deduction) ? $deduction : '';
$sort  =  isset($sort) ? $sort : '';
$type = "";

$isRDCForm = ($isMRRReport) ? false : true;



$result = $this->reports->rdc($division,$department,$cutoff,$deduction, $isRDCForm, $sort);
      $fields = array(
                        array("Employee ID",1,20,1),
                        array("EMPLOYEE NAME",1,50,1),
                        array("TIN NUMBER",1,20,1),
                        array("Taxable Amount",1,20,1),
                        array("Tax Withheld",1,20,1)/*,
                        array("TOTAL",1,20,1)*/
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
       $bitmap = "images/school_logo.bmp";
       $sheet->insertBitmap( 0 , 0 , $bitmap , 30 , 0 , .13 ,.25 );
       $sheet->write(1,0,$SCHOOL_NAME,$bigboldcenter);
       $sheet->write(2,0,$SCHOOL_CAPTION,$normalcenter);
       $sheet->write(4,0,"MONTHLY REMITTANCE RETURN OF INCOME TAX W/HELD (1601-C)",$boldcenter);
       $sheet->write(5,0,"For the month of ".$cutoff,$boldcenter);

       $r = 7;
       $c = 0;
       
       # > displayed header..
       foreach ($fields as $arr_header_info) {
         list($caption, $v1, $col_size, $v2) = $arr_header_info;
         $sheet->setColumn($c,$c, $col_size);
         $sheet->write($r,$c,$caption,$coltitle);
         $c++;
       }

       $r++;
       $c = 0;
       $ofc = $officeDesc = 'sometext';
        $count = 1;
        $total = 0;
        $officeL = 5;
        $officeDescStart = 1;

        $t_tax_amount = 0;
        $t_ee_amount = 0;
        $t_total_amount = 0;
        foreach($result->result() as $row)
        {
          $c  = 0;
          $ee_amount = 0;
          $total_per_emp = 0;
          $ee_amount = $row->withholdingtax;
          /*$fixeddeduc = $row->fixeddeduc;
          $exp_fd = explode("/", $fixeddeduc);
          foreach ($exp_fd as $value) {
            $exp_val = explode("=", $value);
            $ee_amount += $exp_val[1];
          }*/

          $tax_amount = $this->reports->getTaxableAmount($row->employeeid, $cutoff, true);
          $total_per_emp = $ee_amount + $tax_amount;
          
          if($ee_amount != 0){
              if($sort=='department'){
                if($ofc !== $row->office){
                    if($officeDesc != $this->extensions->getOfficeDescriptionReport($row->office)){
                        for ($i=0; $i < $officeL; $i++) { 
                            if($i == $officeDescStart){
                                $sheet->write($r,$i,$this->extensions->getOfficeDescriptionReport($row->office),$boldcenter);
                            }else{
                                $sheet->write($r,$i,' ',$boldcenter);
                            }
                        }
                        $r++;
                        $c=0;
                    }
                }
            }
            $officeDesc = $this->extensions->getOfficeDescriptionReport($row->office);
          $ofc = $row->office;
            # > displayed content..
            $sheet->writeString($r, $c, $row->employeeid, $normalcenter);
            $c++;
            $sheet->writeString($r, $c, iconv("UTF-8", "ISO-8859-1//IGNORE", $row->fullname), $normalcenter);
            $c++;
            $sheet->writeString($r, $c, $row->emp_tin, $normalcenter);
            $c++;
            $sheet->write($r, $c, $tax_amount, $amount);
            $c++;
            $sheet->write($r, $c, number_format($ee_amount,2), $amount);
            $c++;
            $t_tax_amount += $tax_amount;
            $t_ee_amount += $ee_amount;
            $t_total_amount += $total_per_emp;
            $r++;
          }
         /* $sheet->writeString($r, $c, number_format($total_per_emp,2), $amount);
          $c++;*/

        }

        // displayed total..
        $c = 0;
        $sheet->setMerge($r, 0, $r, 2);
        $sheet->writeString($r, $c, "Grand Total : ", $boldRcenter);
        $c+=3;
        $sheet->write($r, $c, $t_tax_amount, $amountbold);
        $c++;
        $sheet->write($r, $c, number_format($t_ee_amount,2), $amountbold);
        $c++;
        /*$sheet->writeString($r, $c, number_format($t_total_amount,2), $amountbold);
        $c++;*/
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