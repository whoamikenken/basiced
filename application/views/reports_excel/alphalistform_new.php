<?php  

  	/**
  	* @author Max Consul
  	* @copyright 2018
  	*/

  	$this->load->library('lib_includer');
  	$this->lib_includer->load("excel/Writer");
  	require_once(APPPATH."constants.php");
  	$xls = New Spreadsheet_Excel_Writer();


    /** Fonts Format */


    $newWave =& $xls->addFormat(array('Size' => 12));
    $newWave ->setAlign("center");
    $newWave->setFontFamily("Century Gothic");
    $newWave ->setBold();
    $newWave ->setLocked();

    $newWaveL =& $xls->addFormat(array('Size' => 14));
    $newWaveL ->setBold();
    $newWaveL ->setAlign("left");
    $newWaveL->setFontFamily("Century Gothic");
    $newWaveL ->setLocked();

    $newWaveLCaption =& $xls->addFormat(array('Size' => 12));
    $newWaveLCaption ->setAlign("left");
    $newWaveLCaption->setFontFamily("Century Gothic");
    $newWaveLCaption ->setLocked();

    $coltitle =& $xls->addFormat(array('Size' => 8));
    $coltitle->setBorder(1);
    $coltitle->setVAlign('vjustify');
    $coltitle->setVAlign('vcenter');
    $coltitle->setAlign("center");
    $coltitle->setTextWrap();
    $coltitle->setLocked();

    $newWave =& $xls->addFormat(array('Size' => 12));
    $newWave ->setBold();
    $newWave ->setAlign("center");
    $newWave ->setLocked();

    $scheduleTitle =& $xls->addFormat(array('Size' => 8));
    $scheduleTitle->setFontFamily("Century Gothic");
    $scheduleTitle ->setBold();
    $scheduleTitle->setLocked();
    /** end Fonts Format */
    
    $sheet =& $xls->addWorksheet("Alphalist");

    /*add school logo and caption*/
    $sheet->write(1,4,$SCHOOL_NAME,$newWaveL);
    $sheet->write(2,4,$SCHOOL_CAPTION,$newWaveLCaption);
    /*end of school logo and caption*/

    $bitmap = "images/school_logo.bmp";
    $sheet->setMerge(0, 0, 1, 0);
    $sheet->insertBitmap( 0 , 3 , $bitmap , 5 , 10 , .15 ,.15 );

    /*report title*/
    $sheet->write(5,5,"Alphalist",$newWave);
    /*end of report title*/
    if($schedule=="7.3" || $schedule=="all"){
      $sheet->write(7,0,"SCHEDULE 7.3",$scheduleTitle);
      $sheet->setMerge(10, 0, 12, 0);
      $sheet->setColumn(0,0,10);
      $sheet->setRow(12, 50);
      $sheet->write(10,0,"Sequential Number",$coltitle);
      $sheet->write(11,0,"",$coltitle);
      $sheet->write(12,0,"",$coltitle);

      $sheet->setMerge(10, 1, 12, 1);
      $sheet->setColumn(1,1,25);
      $sheet->setRow(12, 50);
      $sheet->write(10,1,"Taxpayer Identification Number",$coltitle);
      $sheet->write(11,1,"",$coltitle);
      $sheet->write(12,1,"",$coltitle);

      $sheet->setMerge(10, 2, 12, 2);
      $sheet->setColumn(2,2,20);
      $sheet->setRow(12, 50);
      $sheet->writeString(10,2,"Name of Employees \n Last        First        Middle",$coltitle);
      $sheet->writeString(11,2,"",$coltitle);
      $sheet->writeString(12,2,"",$coltitle);

      $sheet->setMerge(8, 3, 8, 7);
      $sheet->writeString(8,3,"GROSS COMPENSATION INCOME",$coltitle);
      $sheet->writeString(8,4,"",$coltitle);
      $sheet->writeString(8,5,"",$coltitle);
      $sheet->writeString(8,6,"",$coltitle);
      $sheet->writeString(8,7,"",$coltitle);

      $sheet->setMerge(9, 3, 9, 7);
      $sheet->writeString(9,3,"PREVIOUS EMPLOYER",$coltitle);
      $sheet->writeString(9,4,"",$coltitle);
      $sheet->writeString(9,5,"",$coltitle);
      $sheet->writeString(9,6,"",$coltitle);
      $sheet->writeString(9,7,"",$coltitle);

      $sheet->setMerge(10, 3, 10, 7);
      $sheet->writeString(10,3,"PRESENT EMPLOYER",$coltitle);
      $sheet->writeString(10,4,"",$coltitle);
      $sheet->writeString(10,5,"",$coltitle);
      $sheet->writeString(10,6,"",$coltitle);
      $sheet->writeString(10,7,"",$coltitle);

      $sheet->setMerge(11, 3, 11, 5);
      $sheet->writeString(11,3,"NON TAXABLE",$coltitle);
      $sheet->writeString(11,4,"",$coltitle);
      $sheet->writeString(11,5,"",$coltitle);

      $sheet->setMerge(11, 6, 11, 7);
      $sheet->writeString(11,6,"TAXABLE",$coltitle);
      $sheet->writeString(11,7,"",$coltitle);
     
      $sheet->setColumn(3,3,15);
      $sheet->writeString(12,3,"13th Month Pay & Other Benefits",$coltitle);
      $sheet->setColumn(4,4,15);
      $sheet->writeString(12,4,"SSS, Philhealth, Pag-ibig & Other Contributions",$coltitle);
      $sheet->setColumn(5,5,15);
      $sheet->writeString(12,5,"Salaries & Other Forms of Compensation",$coltitle);

      $sheet->setColumn(6,6,15);
      $sheet->writeString(12,6,"13th Month Pay & Other Benefits",$coltitle);
      $sheet->setColumn(7,7,15);
      $sheet->writeString(12,7,"Salaries & Other Forms of Compensation",$coltitle);

      $sheet->setMerge(10, 8, 12, 8);
      $sheet->setColumn(8,8,13);
      $sheet->setRow(12, 50);
      $sheet->writeString(10,8,"Amount \n Exception",$coltitle);
      $sheet->writeString(11,8,"",$coltitle);
      $sheet->writeString(12,8,"",$coltitle);

      $sheet->setMerge(10, 9, 12, 9);
      $sheet->setColumn(9,9,13);
      $sheet->setRow(12, 50);
      $sheet->writeString(10,9,"Tax Due \n (Jan. - Dec.)",$coltitle);
      $sheet->writeString(11,9,"",$coltitle);
      $sheet->writeString(12,9,"",$coltitle);

      $sheet->setMerge(10, 10, 11, 11);
      $sheet->setRow(12, 40);
      $sheet->writeString(10,10,"Tax Withheld (Jan. - Nov.)",$coltitle);
      $sheet->writeString(10,11,"",$coltitle);
      $sheet->writeString(11,11,"",$coltitle);

      $sheet->setColumn(10,10,15);
      $sheet->setRow(12, 30);
      $sheet->writeString(12,10,"Previous Employer",$coltitle);

      $sheet->setColumn(11,11,15);
      $sheet->setRow(12, 30);
      $sheet->writeString(12,11,"Present Employer",$coltitle);

      $sheet->setMerge(10, 12, 11, 13);
      $sheet->setColumn(10,10,30);
      $sheet->setRow(12, 40);
      $sheet->writeString(10,12,"Year-End Adjustment",$coltitle);
      $sheet->writeString(10,13,"",$coltitle);
      $sheet->writeString(11,13,"",$coltitle);

      $sheet->setColumn(12,12,20);
      $sheet->setRow(12, 30);
      $sheet->writeString(12,12,"Amount Withheld & Paid for in Dec.",$coltitle);

      $sheet->setColumn(13,13,20);
      $sheet->setRow(12, 30);
      $sheet->writeString(12,13,"Over Withheld Tax Refunded to Employee",$coltitle);

      $sheet->setMerge(10, 14, 12, 14);
      $sheet->setColumn(14,14,20);
      $sheet->setRow(12, 50);
      $sheet->writeString(10,14,"Amount of Tax Withheld as Adjusted",$coltitle);
      $sheet->writeString(11,14,"",$coltitle);
      $sheet->writeString(12,14,"",$coltitle);

      $row = 14;
      $rowcount = 1;
      foreach($emp_list_total as $empid => $info){
        $total_reg = $info["fixeddeduc"]["SSS"] + $info["fixeddeduc"]["PHILHEALTH"] + $info["fixeddeduc"]["PAGIBIG"] + $info["fixeddeduc"]["PERAA"];
        $sheet->write($row,0,$rowcount,$coltitle);
        $sheet->write($row,1,$this->extensions->getEmployeeTin($empid),$coltitle);
        $sheet->write($row,2,$this->extensions->getEmployeeName($empid),$coltitle);
        $sheet->write($row,3,$info["income_ntax"]+$info["_13thmonth_nt"],$coltitle);
        $sheet->write($row,4,$total_reg,$coltitle);
        $sheet->write($row,5,"",$coltitle);
        $sheet->write($row,6,$info["income_wtax"]+$info["_13thmonth_wt"],$coltitle);
        $sheet->write($row,7,$info["gross"],$coltitle);
        $sheet->write($row,8,$info["income_ntax"],$coltitle);
        $sheet->write($row,9,"",$coltitle);
        $sheet->write($row,10,"",$coltitle);
        $sheet->write($row,11,"",$coltitle);
        $sheet->write($row,12,"",$coltitle);
        $sheet->write($row,13,"",$coltitle);
        $sheet->write($row,14,"",$coltitle);
        $rowcount++;
        $row++;
      }

    }

    /*end schedule 7.3*/
    if($schedule=="7.1" || $schedule=="all"){
      if($schedule=="7.1") $row = 8;
      $row+=2;
      $sheet->write($row,0,"SCHEDULE 7.1",$scheduleTitle);
      $row+=1;

      $sheet->setMerge($row, 0, $row+2, 0);
      $sheet->setColumn(0,0,10);
      $sheet->setRow($row+2, 50);
      $sheet->write($row,0,"Sequential Number",$coltitle);
      $sheet->write($row+1,0,"",$coltitle);
      $sheet->write($row+2,0,"",$coltitle);

      $sheet->setMerge($row, 1, $row+2, 1);
      $sheet->setColumn(1,1,25);
      $sheet->setRow($row+2, 50);
      $sheet->write($row,1,"Taxpayer Identification Number",$coltitle);
      $sheet->write($row+1,1,"",$coltitle);
      $sheet->write($row+2,1,"",$coltitle);

      $sheet->setMerge($row, 2, $row+2, 2);
      $sheet->setColumn(2,2,20);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,2,"Name of Employees \n Last        First        Middle",$coltitle);
      $sheet->writeString($row+1,2,"",$coltitle);
      $sheet->writeString($row+2,2,"",$coltitle);

      $sheet->setMerge($row, 3, $row+2, 3);
      $sheet->setColumn(3,3,20);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,3,"Period of Employment",$coltitle);
      $sheet->writeString($row+1,3,"",$coltitle);
      $sheet->writeString($row+2,3,"",$coltitle);

      $sheet->setMerge($row, 4, $row, 8);
      $sheet->writeString($row,4,"GROSS COMPENSATION INCOME",$coltitle);
      $sheet->writeString($row,5,"",$coltitle);
      $sheet->writeString($row,6,"",$coltitle);
      $sheet->writeString($row,7,"",$coltitle);
      $sheet->writeString($row,8,"",$coltitle);

      $sheet->setMerge($row+1, 4, $row+1, 6);
      $sheet->writeString($row+1,4,"NON TAXABLE",$coltitle);
      $sheet->writeString($row+1,5,"",$coltitle);
      $sheet->writeString($row+1,6,"",$coltitle);

      $sheet->setMerge($row+1, 7, $row+1, 8);
      $sheet->writeString($row+1,7,"TAXABLE",$coltitle);
      $sheet->writeString($row+1,8,"",$coltitle);
     
      $sheet->setColumn(4,4,15);
      $sheet->writeString($row+2,4,"13th Month Pay & Other Benefits",$coltitle);
      $sheet->setColumn(5,5,15);
      $sheet->writeString($row+2,5,"SSS, Philhealth, Pag-ibig & Other Contributions",$coltitle);
      $sheet->setColumn(6,6,15);
      $sheet->writeString($row+2,6,"Salaries & Other Forms of Compensation",$coltitle);

      $sheet->setColumn(7,7,15);
      $sheet->writeString($row+2,7,"13th Month Pay & Other Benefits",$coltitle);
      $sheet->setColumn(8,8,15);
      $sheet->writeString($row+2,8,"Salaries & Other Forms of Compensation",$coltitle);

      $sheet->setMerge($row, 9, $row+2, 9);
      $sheet->setColumn(9,9,13);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,9,"Amount \n Exception",$coltitle);
      $sheet->writeString($row+1,9,"",$coltitle);
      $sheet->writeString($row+2,9,"",$coltitle);

      $sheet->setMerge($row, 10, $row+2, 10);
      $sheet->setColumn(10,10,13);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,10,"Tax Due \n (Jan. - Dec.)",$coltitle);
      $sheet->writeString($row+1,10,"",$coltitle);
      $sheet->writeString($row+2,10,"",$coltitle);

      $sheet->setMerge($row, 11, $row+2, 11);
      $sheet->setColumn(11,11,13);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,11,"Tax Withheld \n (Jan. - Nov.)",$coltitle);
      $sheet->writeString($row+1,11,"",$coltitle);
      $sheet->writeString($row+2,11,"",$coltitle);


      $sheet->setMerge($row, 12, $row+1, 13);
      $sheet->setColumn(12,12,30);
      $sheet->writeString($row,12,"Year-End Adjustment",$coltitle);
      $sheet->writeString($row,13,"",$coltitle);
      $sheet->writeString($row+1,13,"",$coltitle);
      $sheet->writeString($row+1,12,"",$coltitle);

      $sheet->setColumn(12,12,20);
      $sheet->setRow($row+2, 30);
      $sheet->writeString($row+2,12,"Amount Withheld & Paid for in Dec.",$coltitle);

      $sheet->setColumn(13,13,20);
      $sheet->setRow($row+2, 30);
      $sheet->writeString($row+2,13,"Over Withheld Tax Refunded to Employee",$coltitle);

      $sheet->setMerge($row, 14, $row+2, 14);
      $sheet->setColumn(14,14,20);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,14,"Amount of Tax Withheld as Adjusted",$coltitle);
      $sheet->writeString($row+1,14,"",$coltitle);
      $sheet->writeString($row+2,14,"",$coltitle);
      $rowcount = 1;
      $row+=3;
      foreach($emp_list_total as $empid => $info){
        $total_reg = $info["fixeddeduc"]["SSS"] + $info["fixeddeduc"]["PHILHEALTH"] + $info["fixeddeduc"]["PAGIBIG"] + $info["fixeddeduc"]["PERAA"];
        $sheet->write($row,0,$rowcount,$coltitle);
        $sheet->write($row,1,$this->extensions->getEmployeeTin($empid),$coltitle);
        $sheet->write($row,2,$this->extensions->getEmployeeName($empid),$coltitle);
        $sheet->write($row,3,"",$coltitle);
        $sheet->write($row,4,$info["income_ntax"]+$info["_13thmonth_nt"],$coltitle);
        $sheet->write($row,5,$total_reg,$coltitle);
        $sheet->write($row,6,"",$coltitle);
        $sheet->write($row,7,$info["income_wtax"]+$info["_13thmonth_wt"],$coltitle);
        $sheet->write($row,8,$info["gross"],$coltitle);
        $sheet->write($row,9,$info["income_ntax"],$coltitle);
        $sheet->write($row,10,"",$coltitle);
        $sheet->write($row,11,"",$coltitle);
        $sheet->write($row,12,"",$coltitle);
        $sheet->write($row,13,"",$coltitle);
        $sheet->write($row,14,"",$coltitle);
        $rowcount++;
        $row++;
      }
    }
    // echo $row; die;
    /*end schedule 7.3*/
    if($schedule=="7.4" || $schedule=="all"){
      if($schedule=="7.4") $row=8;
      $row+=2;
      $sheet->write($row,0,"SCHEDULE 7.4",$scheduleTitle);
      $row+=1;

      $sheet->setMerge($row, 0, $row+2, 0);
      $sheet->setColumn(0,0,10);
      $sheet->setRow($row+2, 50);
      $sheet->write($row,0,"Sequential Number",$coltitle);
      $sheet->write($row+1,0,"",$coltitle);
      $sheet->write($row+2,0,"",$coltitle);

      $sheet->setMerge($row, 1, $row+2, 1);
      $sheet->setColumn(1,1,25);
      $sheet->setRow($row+2, 50);
      $sheet->write($row,1,"Taxpayer Identification Number",$coltitle);
      $sheet->write($row+1,1,"",$coltitle);
      $sheet->write($row+2,1,"",$coltitle);

      $sheet->setMerge($row, 2, $row+2, 2);
      $sheet->setColumn(2,2,20);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,2,"Name of Employees \n Last        First        Middle",$coltitle);
      $sheet->writeString($row+1,2,"",$coltitle);
      $sheet->writeString($row+2,2,"",$coltitle);

      $sheet->setMerge($row, 3, $row, 7);
      $sheet->writeString($row,3,"GROSS COMPENSATION INCOME",$coltitle);
      $sheet->writeString($row,4,"",$coltitle);
      $sheet->writeString($row,5,"",$coltitle);
      $sheet->writeString($row,6,"",$coltitle);
      $sheet->writeString($row,7,"",$coltitle);

      $sheet->setMerge($row+1, 3, $row+1, 5);
      $sheet->writeString($row+1,3,"NON TAXABLE",$coltitle);
      $sheet->writeString($row+1,4,"",$coltitle);
      $sheet->writeString($row+1,5,"",$coltitle);

      $sheet->setMerge($row+1, 6, $row+1, 7);
      $sheet->writeString($row+1,6,"TAXABLE",$coltitle);
      $sheet->writeString($row+1,7,"",$coltitle);
     
      $sheet->setColumn(3,3,15);
      $sheet->writeString($row+2,3,"13th Month Pay & Other Benefits",$coltitle);
      $sheet->setColumn(4,4,15);
      $sheet->writeString($row+2,4,"SSS, Philhealth, Pag-ibig & Other Contributions",$coltitle);
      $sheet->setColumn(5,5,15);
      $sheet->writeString($row+2,5,"Salaries & Other Forms of Compensation",$coltitle);

      $sheet->setColumn(6,6,15);
      $sheet->writeString($row+2,6,"13th Month Pay & Other Benefits",$coltitle);
      $sheet->setColumn(7,7,15);
      $sheet->writeString($row+2,7,"Salaries & Other Forms of Compensation",$coltitle);

      $sheet->setMerge($row, 8, $row+2, 8);
      $sheet->setColumn(8,8,13);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,8,"Amount \n Exception",$coltitle);
      $sheet->writeString($row+1,8,"",$coltitle);
      $sheet->writeString($row+2,8,"",$coltitle);

      $sheet->setMerge($row, 9, $row+2, 9);
      $sheet->setColumn(9,9,13);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,9,"Tax Due \n (Jan. - Dec.)",$coltitle);
      $sheet->writeString($row+1,9,"",$coltitle);
      $sheet->writeString($row+2,9,"",$coltitle);

      $sheet->setMerge($row, 10, $row+2, 10);
      $sheet->setColumn(10,10,13);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,10,"Tax Withheld \n (Jan. - Nov.)",$coltitle);
      $sheet->writeString($row+1,10,"",$coltitle);
      $sheet->writeString($row+2,10,"",$coltitle);


      $sheet->setMerge($row, 11, $row+1, 12);
      $sheet->setColumn(11,11,30);
      $sheet->writeString($row,11,"Year-End Adjustment",$coltitle);
      $sheet->writeString($row,12,"",$coltitle);
      $sheet->writeString($row+1,12,"",$coltitle);
      $sheet->writeString($row+1,12,"",$coltitle);

      $sheet->setColumn(11,11,20);
      $sheet->setRow($row+2, 30);
      $sheet->writeString($row+2,11,"Amount Withheld & Paid for in Dec.",$coltitle);

      $sheet->setColumn(12,12,20);
      $sheet->setRow($row+2, 30);
      $sheet->writeString($row+2,12,"Over Withheld Tax Refunded to Employee",$coltitle);

      $sheet->setMerge($row, 13, $row+2, 13);
      $sheet->setColumn(13,13,20);
      $sheet->setRow($row+2, 50);
      $sheet->writeString($row,13,"Amount of Tax Withheld as Adjusted",$coltitle);
      $sheet->writeString($row+1,13,"",$coltitle);
      $sheet->writeString($row+2,13,"",$coltitle);

      $rowcount = 1;
      $row+=3;
      foreach($emp_list_total as $empid => $info){
        $total_reg = $info["fixeddeduc"]["SSS"] + $info["fixeddeduc"]["PHILHEALTH"] + $info["fixeddeduc"]["PAGIBIG"] + $info["fixeddeduc"]["PERAA"];
        $sheet->write($row,0,$rowcount,$coltitle);
        $sheet->write($row,1,$this->extensions->getEmployeeTin($empid),$coltitle);
        $sheet->write($row,2,$this->extensions->getEmployeeName($empid),$coltitle);
        $sheet->write($row,3,$info["income_ntax"]+$info["_13thmonth_nt"],$coltitle);
        $sheet->write($row,4,$total_reg,$coltitle);
        $sheet->write($row,5,"",$coltitle);
        $sheet->write($row,6,$info["income_wtax"]+$info["_13thmonth_wt"],$coltitle);
        $sheet->write($row,7,$info["gross"],$coltitle);
        $sheet->write($row,8,$info["income_ntax"],$coltitle);
        $sheet->write($row,9,"",$coltitle);
        $sheet->write($row,10,"",$coltitle);
        $sheet->write($row,11,"",$coltitle);
        $sheet->write($row,12,"",$coltitle);
        $sheet->write($row,13,"",$coltitle);
        $rowcount++;
        $row++;
      }
    }
    /*end schedule 7.4*/
   	$xls->send("Alphalist.xls");
  	$xls->close();

?>