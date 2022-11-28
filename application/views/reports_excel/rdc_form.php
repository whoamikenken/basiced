<?php 
	/**
	* @author justin (with e)
	* @copyright 2018
	*/

	require_once(APPPATH."constants.php");
	$xls = New Spreadsheet_Excel_Writer();
	$xls->send("Reglementary Deduction.xls");

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

    $sheet = &$xls->addWorksheet("Reglementary Report");

    function writeText(&$sheet,$text,&$font,$row1, $col1, $row2 = '', $col2 = ''){
    
        if($row2 != '' || $col2 != ''){
            $sheet->setMerge($row1,$col1,$row1 + $row2, $col1 + $col2);
        }

        $sheet->write($row1,$col1,$text,$font);

        return ($row1 + 1);
    }

    function writeTable(&$sheet, $arr_content, $start_row, $start_col){
    	$row = $start_row;

    	foreach ($arr_content as $content) {
    		$col = $start_col;

    		foreach ($content as $key => $info) {
    			list($caption, $size, $font) = $info;
    			
    			$sheet->setColumn($col, $col, $size);

    			if($key == 0) $sheet->writeString($row, $col, $caption, $font);
    			else 		  $sheet->write($row, $col, $caption, $font);

    			$col += 1;
    		}

    		$row += 1;
    	}

    	return $row;
    }

    // header
    $month = explode("~~", $cutoff);
    $month = $month[0];
    $row = $col = 0;
	$end_col = ($sd_filter == "detailed") ? (count($cutoff_list) + 8) - 1 : (6 - 1);

	$row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
	$row = writeText($sheet, $SCHOOL_NAME, $bigboldcenter, $row, $col, 0, $end_col);
	$row = writeText($sheet, $SCHOOL_CAPTION, $boldcenter, $row, $col, 0, $end_col);
	$row = writeText($sheet,"Reglementary Deduction Contributions", $boldcenter, $row, $col, 0, $end_col);
	$row = writeText($sheet, "For the month of ". $month, $boldcenter, $row, $col, 0, $end_col);
	$row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
    $row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
	
	$bitmap = "images/school_logo.bmp";

    if($sd_filter == "detailed"):
	   	$sheet->insertBitmap( 0 , 1 , $bitmap , 30 , 0 , .13 ,.25 );
    	// table header
    	writeText($sheet, "EMPLOYEE ID", $coltitle, $row, $col, 1, 0);
    	$col += 1;
    	writeText($sheet, "EMPLOYEE NAME", $coltitle, $row, $col, 1, 0);
    	$col += 1;
    	writeText($sheet, $deduction ." NUMBER", $coltitle, $row, $col, 1, 0);
    	$col += 1;
    	writeText($sheet, strtoupper($gb_display), $coltitle, $row, $col, 0, count($cutoff_list));
    	$col += count($cutoff_list) + 1;
    	writeText($sheet, "EE", $coltitle, $row, $col, 1, 0);
    	$col += 1;
    	writeText($sheet, "EC", $coltitle, $row, $col, 1, 0);
    	$col += 1;
    	writeText($sheet, "ER", $coltitle, $row, $col, 1, 0);
    	$col += 1;
    	$row = writeText($sheet, "TOTAL", $coltitle, $row, $col, 1, 0);
    	
    	$col = 3;
    	foreach ($cutoff_list as $co_date) {
	    	writeText($sheet, date("M", strtotime($month)) ." ". $co_date . ", ". $year, $coltitle, $row, $col, 0, 0);
    		$col += 1;
    	}
    	$row = writeText($sheet, "Total", $coltitle, $row, $col, 0, 0);
    	
    	// table content
    	ksort($emp_list);	
        $old_deptid = $old_campusid = '';
        $first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;
		foreach ($emp_list as $sort_key => $employees) {
			$col = 0;
	    	if($sort_key != "name") $row = writeText($sheet, $key_list[$sort_key], $bold, $row, $col, 0, $end_col);

	    	foreach ($employees as $empid => $e_info) {
                if(!$e_info['campus']) $e_info['campus'] = "No Campus";

                $ee = $ec = $er = $total_fixed_deduction = 0;
                foreach ($e_info['ee'] as $m_key => $m_amount) $ee += $m_amount;
                foreach ($e_info['ec'] as $m_key => $m_amount) $ec += $m_amount;
                foreach ($e_info['er'] as $m_key => $m_amount) $er += $m_amount;
                foreach ($e_info['total_fixed_deduction'] as $m_key => $m_amount) $total_fixed_deduction += $m_amount;

                if($ec || $er || $ee){
                    if($old_deptid != $e_info['deptid'] && $e_info['deptid'] != 'ACAD'){
                        if($old_deptid){
                            writeText($sheet, "Sub Total :", $amountbold, $row, 2); 
                            writeText($sheet, $first_cutoff, $amountbold, $row, 3); 
                            writeText($sheet, $second_cutoff, $amountbold, $row, 4); 
                            writeText($sheet, $total, $amountbold, $row, 5); 
                            writeText($sheet, $tot_ee, $amountbold, $row, 6); 
                            writeText($sheet, $tot_ec, $amountbold, $row, 7); 
                            writeText($sheet, $tot_er, $amountbold, $row, 8); 
                            writeText($sheet, $tot_totalfix, $amountbold, $row, 9); 
                            $row = writeText($sheet, "Total :", $amountbold, $row, 4); 
                            $first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;
                        }
                        $row = writeText($sheet, $this->extensions->getDeparmentDescriptionReport($e_info['deptid']), $bold, $row, $col, 0, $end_col);
                    }
                    if($old_campusid != $e_info['campus'] && $e_info['deptid'] == 'ACAD'){
                        if($old_campusid){
                            writeText($sheet, "Sub Total :", $amountbold, $row, 2); 
                            writeText($sheet, $first_cutoff, $amountbold, $row, 3); 
                            writeText($sheet, $second_cutoff, $amountbold, $row, 4); 
                            writeText($sheet, $total, $amountbold, $row, 5); 
                            writeText($sheet, $tot_ee, $amountbold, $row, 6); 
                            writeText($sheet, $tot_ec, $amountbold, $row, 7); 
                            writeText($sheet, $tot_er, $amountbold, $row, 8); 
                            writeText($sheet, $tot_totalfix, $amountbold, $row, 9); 
                            $row = writeText($sheet, "Total :", $amountbold, $row, 4); 
                            $first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;
                        }
                        $row = writeText($sheet, ($e_info['campus'] ? $this->extensions->getCampusDescription($e_info['campus']) : "No Campus"), $bold, $row, $col, 0, $end_col);
                    }

    	    		$arr_content = array();	

    	    		$arr_content[] = array($empid, 15, $normalcenter);
    	    		$arr_content[] = array(iconv("UTF-8", "ISO-8859-1//IGNORE", $e_info['name']), 40, $normal);
    	    		$arr_content[] = array($e_info['tin_num'], 20, $normalcenter);

    	    		foreach ($cutoff_list as $co_key){
                        if(!$cutoff_count) $first_cutoff +=  $e_info['gb_amount'][$co_key];
                        else $second_cutoff += $e_info['gb_amount'][$co_key];
                        $arr_content[] = array($e_info['gb_amount'][$co_key], 15, $amount);

                        $cutoff_count++;
                    }

    	    		$arr_content[] = array($e_info['gb_total'], 15, $amount);

    				$arr_content[] = array($ee, 15, $amount);
    				$arr_content[] = array($ec, 15, $amount);
    				$arr_content[] = array($er, 15, $amount); 
    				$arr_content[] = array($total_fixed_deduction, 15, $amount);
    				
    	    		$row = writeTable($sheet, array($arr_content), $row, $col);
                    $old_deptid = $e_info['deptid'];
    	    	    $old_campusid = $e_info['campus'];

                    $total += $e_info['gb_total'];
                    $tot_ee += $ee;
                    $tot_ec += $ec;
                    $tot_er += $er;
                    $tot_totalfix += $total_fixed_deduction;

                    $cutoff_count = 0;
                }
            }

	    	if($sort_key != "name"):
                writeText($sheet, "Sub Total :", $amountbold, $row, 2); 
                writeText($sheet, $first_cutoff, $amountbold, $row, 3); 
                writeText($sheet, $second_cutoff, $amountbold, $row, 4); 
                writeText($sheet, $total, $amountbold, $row, 5); 
                writeText($sheet, $tot_ee, $amountbold, $row, 6); 
                writeText($sheet, $tot_ec, $amountbold, $row, 7); 
                writeText($sheet, $tot_er, $amountbold, $row, 8); 
                writeText($sheet, $tot_totalfix, $amountbold, $row, 9);
                $first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;

	    		$col = $end_col - 5;

	    		writeText($sheet, "Total :", $amountbold, $row+1, $col, 0, 0); $col+=1;
	    		writeText($sheet, $summary[$sort_key]["gb_amount"], $amountbold, $row+1, $col, 0, 0); $col+=1;
	    		writeText($sheet, $summary[$sort_key]["ee_amount"], $amountbold, $row+1, $col, 0, 0); $col+=1;
	    		writeText($sheet, $summary[$sort_key]["ec_amount"], $amountbold, $row+1, $col, 0, 0); $col+=1;
	    		writeText($sheet, $summary[$sort_key]["er_amount"], $amountbold, $row+1, $col, 0, 0); $col+=1;
	    		$row = writeText($sheet, $summary[$sort_key]["total_fixed_deduction"], $amountbold, $row+1, $col, 0, 0);
                $row++;
			endif;
		    $old_deptid = '';
        }    		

    	// grand total
    	$grand_total = array();
		foreach ($summary as $s_key => $s_info) {
			foreach ($s_info as $key => $s_amount) {
				if(is_numeric($s_amount)):
					if(!array_key_exists($key, $grand_total)) $grand_total[$key] = 0;

					$grand_total[$key] += $s_amount;
				endif;
			}
		}

    	$col = $end_col - 5;

		writeText($sheet, "Grand Total :", $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["gb_amount"], $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["ee_amount"], $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["ec_amount"], $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["er_amount"], $amountbold, $row, $col, 0, 0); $col+=1; 
		$row = writeText($sheet, $grand_total["total_fixed_deduction"], $amountbold, $row, $col, 0, 0);
    
    else:

    	$sheet->insertBitmap( 0 , 0, $bitmap , 10, 3 , .15 ,.45 );
    	$col = 0;

    	$header = '&nbsp;';
		if($sort == "department") $header = 'DEPARTMENT';
		if($sort == "campus") $header = 'CAMPUS';

		$table_header = array(
			array($header, 40, $boldcenter),
			array(strtoupper($gb_display), 20, $boldcenter),
			array("EE", 20, $boldcenter),
			array("ER", 20, $boldcenter),
			array("EC", 20, $boldcenter),
			array("TOTAL", 20, $boldcenter)
		);
		$row = writeTable($sheet, array($table_header), $row, $col);

		foreach ($summary as $s_key => $s_info) {
			$arr_content = array(
				array((($s_key != 'name') ? $key_list[$s_key] : "ALL EMPLOYEE"), 40, $normal),
				array($s_info["gb_amount"], 20, $amount),
				array($s_info["ee_amount"], 20, $amount),
				array($s_info["ec_amount"], 20, $amount),
				array($s_info["er_amount"], 20, $amount),
				array($s_info["total_fixed_deduction"], 20, $amount)
			);

			$row = writeTable($sheet, array($arr_content), $row, $col);
		}

		// grand total
    	$grand_total = array();
		foreach ($summary as $s_key => $s_info) {
			foreach ($s_info as $key => $s_amount) {
				if(is_numeric($s_amount)):
					if(!array_key_exists($key, $grand_total)) $grand_total[$key] = 0;

					$grand_total[$key] += $s_amount;
				endif;
			}
		}

		writeText($sheet, "Grand Total :", $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["gb_amount"], $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["ee_amount"], $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["ec_amount"], $amountbold, $row, $col, 0, 0); $col+=1;
		writeText($sheet, $grand_total["er_amount"], $amountbold, $row, $col, 0, 0); $col+=1;
		$row = writeText($sheet, $grand_total["total_fixed_deduction"], $amountbold, $row, $col, 0, 0);

    endif;


    $xls->close();
?>

