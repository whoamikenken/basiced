<?php
 
	$this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Alphalist.xls");

	//MODIFIED COLORS
	$xls->setCustomColor(12, 252, 228, 214); //	PEACH
	$xls->setCustomColor(13, 169, 208, 142); // GREEN
	$xls->setCustomColor(14, 244, 176, 132); // ORANGE
	$xls->setCustomColor(15, 91, 155, 213);  // BLUE
 
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
	
	$bigunderlined =& $xls->addFormat(array('Size' => 12));
    $bigunderlined->setBottom(1);
    $bigunderlined->setLocked();
    
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
	
	$tableHeader = & $xls->addFormat(array('Size' => 8));
	$tableHeader->setBorder(1);
	$tableHeader->setAlign("center");
    $tableHeader->setBold();
    $tableHeader->setTextWrap(1);
	$tableHeader->setFgColor(12);
	$tableHeader->setLocked();
	
	$tableNormal = & $xls->addFormat(array('Size' => 8));
	$tableNormal->setBorder(1);
	$tableNormal->setLocked();
	
	$tableNormalCenter = & $xls->addFormat(array('Size' => 8));
	$tableNormalCenter->setBorder(1);
	$tableNormalCenter->setAlign("center");
	$tableNormalCenter->setLocked();
	
	$tableCenterBold = & $xls->addFormat(array('Size' => 8));
	$tableCenterBold->setBorder(1);
	$tableCenterBold->setAlign("center");
    $tableCenterBold->setBold();
	$tableCenterBold->setLocked();
	
	$tableNormalCenterGreen = & $xls->addFormat(array('Size' => 8));
	$tableNormalCenterGreen->setBorder(1);
	$tableNormalCenterGreen->setAlign("center");
	$tableNormalCenterGreen->setFgColor(13);
	$tableNormalCenterGreen->setLocked();
	
	$tableCenterGreenBold = & $xls->addFormat(array('Size' => 8));
	$tableCenterGreenBold->setBorder(1);
	$tableCenterGreenBold->setAlign("center");
    $tableCenterGreenBold->setBold();
	$tableCenterGreenBold->setFgColor(13);
	$tableCenterGreenBold->setLocked();
	
	$tableNormalCenterOrange= & $xls->addFormat(array('Size' => 8));
	$tableNormalCenterOrange->setBorder(1);
	$tableNormalCenterOrange->setAlign("center");
	$tableNormalCenterOrange->setFgColor(14);
	$tableNormalCenterOrange->setLocked();
	
	$tableCenterOrangeBold = & $xls->addFormat(array('Size' => 8));
	$tableCenterOrangeBold->setBorder(1);
	$tableCenterOrangeBold->setAlign("center");
    $tableCenterOrangeBold->setBold();
	$tableCenterOrangeBold->setFgColor(14);
	$tableCenterOrangeBold->setLocked();
	
	$tableNormalCenterBlue= & $xls->addFormat(array('Size' => 8));
	$tableNormalCenterBlue->setBorder(1);
	$tableNormalCenterBlue->setAlign("center");
	$tableNormalCenterBlue->setFgColor(15);
	$tableNormalCenterBlue->setLocked();
	
	$tableCenterBlueBold = & $xls->addFormat(array('Size' => 8));
	$tableCenterBlueBold->setBorder(1);
	$tableCenterBlueBold->setAlign("center");
    $tableCenterBlueBold->setBold();
	$tableCenterBlueBold->setFgColor(15);
	$tableCenterBlueBold->setLocked();
	
	$tableNormalCenterEE= & $xls->addFormat(array('Size' => 8));
	$tableNormalCenterEE->setBorder(1);
	$tableNormalCenterEE->setAlign("center");
	$tableNormalCenterEE->setFgColor(12);
	$tableNormalCenterEE->setLocked();
	
	$tableCenterEEBold = & $xls->addFormat(array('Size' => 8));
	$tableCenterEEBold->setBorder(1);
	$tableCenterEEBold->setAlign("center");
    $tableCenterEEBold->setBold();
	$tableCenterEEBold->setFgColor(12);
	$tableCenterEEBold->setLocked();
	
    /* END */
    
function formatAmount($amount=''){
	$return ="";
	if($amount)
	{
	    $return = number_format( $amount, 2 );
	}
	else
	{
	    $return = '0.00';
	}
	return $return;
}
	$year  = ($_GET['year'] ? $_GET['year'] : $_POST['year']);
	
	$result = $this->reports->alphalistEmp($year);
	
	$fields = array(
                        array("No.",1,5,1),
                        array("Name",1,40,1),
                        array("SSS#",1,20,1),
                        array("TIN",1,20,1),
                        array("Philhealth No.",1,20,1),
                        array("Pag-Ibig",1,20,1),
                        array("B-DAY",1,20,1),
                        array("Period Covered",1,20,1),
                        array("Net Income",1,20,1),
                        array("COLA",1,20,1),
                        array("Gross Income",1,20,1),
                        array("Total SSS EE",1,20,1),
                        array("Total MCR EE",1,20,1),
                        array("Total P-ibig EE",1,20,1),
                        array("Total EE",1,20,1),
                        array("Net Pay w/o EE",1,20,1),
                        array("13th Mo. Pay",1,20,1),
                        array("Total Salary",1,20,1),
                        array("Status",1,20,1),
                        array("Personal Exemption",1,20,1),
                        array("Tax Due/",1,20,1),
                        array("Tax w/ Held",1,20,1),
                        array("Still Due",1,20,1),
                        array("Monthly Tax",1,20,1),
                        array("Still Due",1,20,1),
                        array("Monthly Taxes",1,20,1),
                  );
   
	$sheet = &$xls->addWorksheet("Sheet 1");
	$sheet->setRow(6,25);
	$c = 0;$r = 0;
	$sheet->write(0,0,"Payroll Sheet",$bold);
	$sheet->write(1,0,"for the year ".$year,$bold);
	
	$sheet->write(3,0,"Client:",$normal);
	$sheet->write(3,1,ucwords(strtolower($SCHOOL_NAME)),$bold);
	
	$sheet->write(4,0,"Address:   ".$SCHOOL_ADDRESS,$normal);
	
	$sheet->write(4,8,"SSS#:   ".$SCHOOL_SSS,$bold);
	
	$sheet->write(4,12,"PEN:   ".$SCHOOL_PEN,$bold);
   
	$sheet->write(4,16,"TIN:   ".$SCHOOL_TIN,$bold);
   
	$sheet->write(4,20,"PAGIBIG TRN:   ".$SCHOOL_PAGIBIG_TRN,$bold);
   
	$c = 0;$r = 6;
	
    displaytablefields($sheet,$r,$c,$fields,$tableHeader);
	$r +=1;
	$i = 0;
	if(sizeof($result) > 0){
		$netIncomeTotal = array();
		$COLATotal = array();
		$grossIncomeTotal = array();
		$sssTotal = array();
		$mcrTotal = array();
		$pagibigTotal = array();
		$eeTotal = array();
		$netPayWithoutEETotal = array();
		$month13PayTotal = array();
		$totalSalaryTotal = array();
		
		
		foreach ($result as $key => $row) {
			$c=0;
			$i++;
			$sheet->write($r,$c,$i,$tableNormalCenter);
			$c++;
			$sheet->write($r,$c,$row->fullname,$tableNormal);
			$c++;
			$sheet->write($r,$c,$row->emp_sss,$tableNormalCenter);
			$c++;
			$sheet->write($r,$c,$row->emp_tin,$tableNormalCenter);
			$c++;
			$sheet->write($r,$c,$row->philhealth,$tableNormalCenter);
			$c++;
			$sheet->write($r,$c,$row->pagibig,$tableNormalCenter);
			$c++;
			$sheet->write($r,$c,date("F d, Y",strtotime($row->bdate)),$tableNormalCenter);
			$c++;
			
			$query = $this->reports->alphalistData($row->employeeid,$year);
			$monthRange = array();
			$monthRange2 = array();
			$periodCover = $netIncome = $COLA = $grossIncome = $sssEE = $mcrEE = $pagibigEE = $totalEE = $netpayWithoutEE = $month13pay = $totalSalary = 0;
			$periodCover2 = $netIncome2 = $COLA2 = $grossIncome2 = $sssEE2 = $mcrEE2 = $pagibigEE2 = $totalEE2 = $netpayWithoutEE2 = $month13pay2 = $totalSalary2 = 0;
			foreach ($query as $k => $rs) {
				if(date("n",strtotime($rs->cutoffstart)) < 6)
				{
					//NET INCOME
					$netIncome += $rs->salary;
					
					//COLA
					foreach(explode("/",$rs->income) as $inc)
					{
						$in = explode("=",$inc);
						$COLA += $in[1];
					}
					
					//EE
					foreach(explode("/",$rs->fixeddeduc) as $deduc)
					{
						$d = explode("=",$deduc);
						if($d[0] == "PAGIBIG")
						{
							$pagibigEE += $d[1];
						}
						else if($d[0] == "PHILHEALTH")
						{
							$mcrEE += $d[1];
						}
						else if($d[0] == "SSS")
						{
							$sssEE += $d[1];
						}
					}
					
					//13TH MONTH PAY
					$month13pay = 0;
					
					$monthRange[] = date("n",strtotime($rs->cutoffstart));
				}
				else
				{
					//NET INCOME
					$netIncome2 += $rs->salary;
					
					//COLA
					foreach(explode("/",$rs->income) as $inc)
					{
						$in = explode("=",$inc);
						$COLA2 += $in[1];
					}
					
					//EE
					foreach(explode("/",$rs->fixeddeduc) as $deduc)
					{
						$d = explode("=",$deduc);
						if($d[0] == "PAGIBIG")
						{
							$pagibigEE2 += $d[1];
						}
						else if($d[0] == "PHILHEALTH")
						{
							$mcrEE2 += $d[1];
						}
						else if($d[0] == "SSS")
						{
							$sssEE2 += $d[1];
						}
					}
					
					//13TH MONTH PAY
					$month13pay2 = 0;
					
					$monthRange2[] = date("n",strtotime($rs->cutoffstart));
				}
			}
			//FIRST HALF
			if(count($monthRange) > 0)
			{
				if(min($monthRange) == max($monthRange))
				{
					$periodCover = max($monthRange);
				}
				else
				{
					$periodCover = min($monthRange)."-".max($monthRange);
				}

				$sheet->write($r,$c,$periodCover,$tableNormalCenter);
				$c++;
				
				$sheet->write($r,$c,formatAmount($netIncome),$tableNormalCenterGreen);
				$c++;
				
				$sheet->write($r,$c,formatAmount($COLA),$tableNormalCenterGreen);
				$c++;
				
				$grossIncome = $netIncome + $COLA;
				
				$sheet->write($r,$c,formatAmount($grossIncome),$tableNormalCenterGreen);
				$c++;
				
				$sheet->write($r,$c,formatAmount($sssEE),$tableNormalCenter);
				$c++;
				
				$sheet->write($r,$c,formatAmount($mcrEE),$tableNormalCenter);
				$c++;
				
				$sheet->write($r,$c,formatAmount($pagibigEE),$tableNormalCenter);
				$c++;
				
				$totalEE = $sssEE + $mcrEE + $pagibigEE;
				
				$sheet->write($r,$c,formatAmount($totalEE),$tableNormalCenterEE);
				$c++;
				
				$netpayWithoutEE = $grossIncome - $totalEE;
				
				$sheet->write($r,$c,formatAmount($netpayWithoutEE),$tableNormalCenterOrange);
				$c++;
				
				$sheet->write($r,$c,formatAmount($month13pay),$tableNormalCenter);
				$c++;
				
				$totalSalary = $netpayWithoutEE + $month13pay;
				$sheet->write($r,$c,formatAmount($totalSalary),$tableCenterBlueBold);
				
				$c++;
				$r++;
			}
			$c = 7;
			//SECOND HALF
			if(count($monthRange2) > 0)
			{
				if(min($monthRange2) == max($monthRange2))
				{
					$periodCover2 = max($monthRange2);
				}
				else
				{
					$periodCover2 = min($monthRange2)."-".max($monthRange2);
				}
				
				$sheet->write($r,$c,$periodCover2,$tableNormalCenter);
				$c++;
				
				$sheet->write($r,$c,formatAmount($netIncome2),$tableNormalCenterGreen);
				$c++;
				
				$sheet->write($r,$c,formatAmount($COLA2),$tableNormalCenterGreen);
				$c++;
				
				$grossIncome2 = $netIncome2 + $COLA2;
				
				$sheet->write($r,$c,formatAmount($grossIncome2),$tableNormalCenterGreen);
				$c++;
				
				$sheet->write($r,$c,formatAmount($sssEE2),$tableNormalCenter);
				$c++;
				
				$sheet->write($r,$c,formatAmount($mcrEE2),$tableNormalCenter);
				$c++;
				
				$sheet->write($r,$c,formatAmount($pagibigEE2),$tableNormalCenter);
				$c++;
				
				$totalEE2 = $sssEE2 + $mcrEE2 + $pagibigEE2;
				
				$sheet->write($r,$c,formatAmount($totalEE2),$tableNormalCenterEE);
				$c++;
				
				$netpayWithoutEE2 = $grossIncome2 - $totalEE2;
				
				$sheet->write($r,$c,formatAmount($netpayWithoutEE2),$tableNormalCenterOrange);
				$c++;
				
				$sheet->write($r,$c,formatAmount($month13pay2),$tableNormalCenter);
				$c++;
				
				$totalSalary2 = $netpayWithoutEE2 + $month13pay2;
				$sheet->write($r,$c,formatAmount($totalSalary2),$tableCenterBlueBold);
				$c++;
				$r++;
			}
			
			if(count($monthRange) > 0 && count($monthRange2) > 0)
			{
				$c = 8;
				$sheet->writeFormula($r,$c,"=SUM(I".($r-1)."+I".$r.")",$tableCenterGreenBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(J".($r-1)."+J".$r.")",$tableCenterGreenBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(K".($r-1)."+K".$r.")",$tableCenterGreenBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(L".($r-1)."+L".$r.")",$tableCenterBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(M".($r-1)."+M".$r.")",$tableCenterBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(N".($r-1)."+N".$r.")",$tableCenterBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(O".($r-1)."+O".$r.")",$tableCenterEEBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(P".($r-1)."+P".$r.")",$tableCenterOrangeBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(Q".($r-1)."+Q".$r.")",$tableCenterBold);
				$c++;
				
				$sheet->writeFormula($r,$c,"=SUM(R".($r-1)."+R".$r.")",$tableCenterBlueBold);
				$c++;

				$r++;
				
				$netIncomeTotal[] = $r;
				$COLATotal[] = $r;
				$grossIncomeTotal[] = $r;
				$sssTotal[] = $r;
				$mcrTotal[] = $r;
				$pagibigTotal[] = $r;
				$eeTotal[] = $r;
				$netPayWithoutEETotal[] = $r;
				$month13PayTotal[] = $r;
				$totalSalaryTotal[] = $r;
				
				
			}
			else
			{
				$netIncomeTotal[] = $r;
				$COLATotal[] = $r;
				$grossIncomeTotal[] = $r;
				$sssTotal[] = $r;
				$mcrTotal[] = $r;
				$pagibigTotal[] = $r;
				$eeTotal[] = $r;
				$netPayWithoutEETotal[] = $r;
				$month13PayTotal[] = $r;
				$totalSalaryTotal[] = $r;
			}
			
			
			
			$r++;
		}
		$c=0;
		$c=1;
		$sheet->write($r,$c,"TOTAL",$tableHeader);
		
		foreach($netIncomeTotal as $index => $value)
		{
			$I .= "I".$value."+";
		}
		foreach($COLATotal as $index => $value)
		{
			$J .= "J".$value."+";
		}
		foreach($grossIncomeTotal as $index => $value)
		{
			$K .= "K".$value."+";
		}
		foreach($sssTotal as $index => $value)
		{
			$L .= "L".$value."+";
		}
		foreach($mcrTotal as $index => $value)
		{
			$M .= "M".$value."+";
		}
		foreach($pagibigTotal as $index => $value)
		{
			$N .= "N".$value."+";
		}
		foreach($eeTotal as $index => $value)
		{
			$O .= "O".$value."+";
		}
		foreach($netPayWithoutEETotal as $index => $value)
		{
			$P .= "P".$value."+";
		}
		foreach($month13PayTotal as $index => $value)
		{
			$Q .= "Q".$value."+";
		}
		foreach($totalSalaryTotal as $index => $value)
		{
			$R .= "R".$value."+";
		}
		
		$I = rtrim($I,'+');
		$J = rtrim($J,'+');
		$K = rtrim($K,'+');
		$L = rtrim($L,'+');
		$M = rtrim($M,'+');
		$N = rtrim($N,'+');
		$O = rtrim($O,'+');
		$P = rtrim($P,'+');
		$Q = rtrim($Q,'+');
		$R = rtrim($R,'+');
		
		$c = 8;
		$sheet->writeFormula($r,$c,"=SUM(".$I.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$J.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$K.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$L.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$M.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$N.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$O.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$P.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$Q.")",$tableHeader);
		$c++;
				
		$sheet->writeFormula($r,$c,"=SUM(".$R.")",$tableHeader);
		$c++;
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