<?php
/**
* @author justin (with e)
* @copyright 2018
*/

$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
require_once(APPPATH."constants.php");
$xls = New Spreadsheet_Excel_Writer();

$xls->send($title .".xls");

$normal =& $xls->addFormat(array('Size' => 10));
$normal->setLocked();

$normalcenter =& $xls->addFormat(array('Size' => 10));
$normalcenter->setAlign("center");
$normalcenter->setLocked();

$normalright =& $xls->addFormat(array('Size' => 10));
$normalright->setNumFormat("#,##0.00");
$normalright->setAlign("right");
$normalright->setLocked();

$normalBold =& $xls->addFormat(array('Size' => 10));
$normalBold->setBold();
$normalBold->setAlign("right");

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

$xls->setCustomColor(12, 189, 215, 238);
$boldColorBlue =& $xls->addFormat(array('Size' => 8));
$boldColorBlue->setAlign("left");
$boldColorBlue->setFgColor(12);
$boldColorBlue->setBold();
$boldColorBlue->setLocked();


$sheet = &$xls->addWorksheet($title);

function writeContent(&$sheet, $caption, $fonts, $is_number=false, $row1, $col1, $row2=0, $col2=0, $width=15){
	$is_merge = ($row2 || $col2) ? true : false;
	$row2 = ($row2) ? $row1 + $row2 : $row1;
	$col2 = ($col2) ? $col1 + $col2 : $col1;

	if($is_merge) $sheet->setMerge($row1, $col1, $row2, $col2); // <<< merge cell
	else 		  $sheet->setColumn($col1, $col1, $width);

	if($is_number) $sheet->writeString($row1, $col1, $caption, $fonts);
	else 		   $sheet->write($row1, $col1, $caption, $fonts);

	return (int)($row2 + 1);
}

function writeTable(&$sheet, $content, $row, $col){

	foreach($content as $col_info){
		list($caption, $fonts, $is_number, $width) = $col_info;

		writeContent($sheet, $caption, $fonts, $is_number, $row, $col, 0, 0, $width);

		$col += 1;
	}

	return ($row + 1);
}

if($form == "incomeadj"){
	$header_table = array(
		array("#", $boldcenter, false, 20),
		array("Employee ID", $boldcenter, false, 30),
		array("Employee Name", $boldcenter, false, 50),
		array("Amount", $boldcenter, false, 25),
	);
}else{
	$header_table = array(
		array("#", $boldcenter, false, 20),
		array("Employee ID", $boldcenter, false, 30),
		array("Employee Name", $boldcenter, false, 50),
		array("Income", $boldcenter, false, 30),
		array("Amount", $boldcenter, false, 25),
	);	
}

$end_col = count($header_table) - 1;
$row = $col = 0;

$bitmap = "images/school_logo_bm.bmp";
$sheet->insertBitmap( $row , $col , $bitmap , 15 , 0 , .25 ,.40 );

$row = writeContent($sheet, "", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, $SCHOOL_NAME, $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, utf8_decode("Dasmariñas Cavite"), $normalcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "OTHER INCOME TRANSACTION REPORT", $bold, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "Cut-Off : ". date("F d, Y", strtotime($cutoff_start)) ." - ". date("F d, Y", strtotime($cutoff_end)), $bold, false, $row, $col, 0, $end_col);

if($form == "incomeadj"){
	// <<< income
	$grand_total = 0;
	foreach ($setup as $code => $income_info) {
		if(isset($list[$code])){
				   writeContent($sheet, "Description : ", $boldColorBlue, false, $row, $col);
			$row = writeContent($sheet, $income_info["description"], $boldColorBlue, false, $row, 1, 0, 2);

				   writeContent($sheet, "Taxable : ", $boldColorBlue, false, $row, $col);
			$row = writeContent($sheet, $income_info["taxable"], $boldColorBlue, false, $row, 1, 0, 2);

			$row = writeTable($sheet, $header_table, $row, $col);

			$count_emp = 1;
			// <<< ACAD first
			$not_include_department = array();
			if($sort_by == "department"){
				foreach ($campus as $campus_id => $campus_desc) {
					$dept_name = $department["ACAD"];
					$sort = "ACAD-". $campus_id;

					if(isset($list[$code][$sort])){
						$not_include_department[] = $sort;
						$sub_total = 0;
						$row = writeContent($sheet, $dept_name ." - ". $campus_desc, $bold, false, $row, $col, 0, $end_col);
						foreach ($list[$code][$sort] as $detail) {
							$table_content = array(
								array($count_emp, $normalcenter, false, 20),
								array($detail["employeeid"], $normalcenter, true, 30),
								array(strtoupper(utf8_decode($detail["name"])), $normal, false, 50),
								array(number_format($detail["amount"], 2), $normalright, true, 25),
							);

							$count_emp 	 += 1;
							$sub_total 	 += $detail["amount"];
							$grand_total += $detail["amount"];
							$row = writeTable($sheet, $table_content, $row, $col);
						}

							   writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
						$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
					}
				}
			}

			$sub_total = 0;
			foreach ($list[$code] as $sort => $employee_list) {
				if($sort_by == "department" && !in_array($sort, $not_include_department)) {
					$row = writeContent($sheet, $dept_name ." - ". $campus_desc, $bold, false, $row, $col, 0, $end_col);
					$sub_total = 0;
				}
					
				
				if(!in_array($sort, $not_include_department)){
					foreach ($employee_list as $detail) {
						$table_content = array(
							array($count_emp, $normalcenter, false, 20),
							array($detail["employeeid"], $normalcenter, true, 30),
							array(strtoupper(utf8_decode($detail["name"])), $normal, false, 50),
							array(number_format($detail["amount"], 2), $normalright, true, 25),
						);

						$count_emp 	 += 1;
						$sub_total 	 += $detail["amount"];
						$grand_total += $detail["amount"];
						$row = writeTable($sheet, $table_content, $row, $col);
					}

					if($sort_by == "department"){
							   writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
						$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
					}
				}
			}

			if($sort_by != "department"){
					   writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
				$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
			}
		}
	}

	$row += 1;
	if($grand_total){
			   writeContent($sheet, "Grand Total : ", $normalBold, false, $row, $col, 0, 2);
		$row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 3);	
	}
}else{
	// <<< employee
	$grand_total = 0;
	$row = writeTable($sheet, $header_table, $row, $col);
	foreach ($list as $sort => $employee_list) {
		$department_total = 0;
		
		if($sort_by == "department")
			$row = writeContent($sheet, $department[$sort], $boldColorBlue, false, $row, $col, 0, $end_col);

		$count_emp = 1;
		foreach ($employee_list as $employeeid => $detail) {
			$sub_total = 0;
			$is_first_row = true;

			foreach ($detail["income"] as $idx => $description) {
				$table_content = array(
					array(($is_first_row) ? $count_emp : "", $normalcenter, false, 20),
					array(($is_first_row) ? $employeeid : "", $normalcenter, true, 30),
					array(($is_first_row) ? $detail["name"] : "", $normal, false, 50),
					array($description, $normal, false, 30),
					array(number_format($detail["amount"][$idx], 2), $normalright, true, 25)
				);


				$sub_total 	 	  += $detail["amount"][$idx];
				$grand_total 	  += $detail["amount"][$idx];
				$department_total += $detail["amount"][$idx];
				$row = writeTable($sheet, $table_content, $row, $col);
				$is_first_row = false;
			}

			if(count($income) > 1 || in_array("selectAll", $income)){
					   writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 3);
				$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);
			}

			$count_emp 	 += 1;
		}
		


		if($sort_by == "department"){
				   writeContent($sheet, "Department Total : ", $normalBold, false, $row, $col, 0, 3);
			$row = writeContent($sheet, number_format($department_total, 2), $normalBold, true, $row, 4);
		}			
	}

	$row += 1;
	if($grand_total){
			   writeContent($sheet, "Grand Total : ", $normalBold, false, $row, $col, 0, 3);
		$row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 4);
	}
}
$xls->close();