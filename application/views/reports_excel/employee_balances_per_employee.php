<?php
/**
* @author justin (with e)
* @copyright 2018
*/

$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
require_once(APPPATH."constants.php");
$xls = New Spreadsheet_Excel_Writer();

$xls->send("Employee Balances.xls");

$normal =& $xls->addFormat(array('Size' => 10));
$normal->setLocked();

$coltitle =& $xls->addFormat(array('Size' => 10));
$coltitle->setBorder(2);
$coltitle->setBold();
$coltitle->setAlign("center");
$coltitle->setFgColor('black');
$coltitle->setColor('yellow');
$coltitle->setLocked();

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

$sheet = &$xls->addWorksheet("Employee Balances");

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

$end_col = ($tag == "DEDUCTION") ? 4 : 5;
$row = $col = 0;

$bitmap = "images/school_logo.bmp";
$sheet->insertBitmap( $row , 1 , $bitmap , 10 , 0 , .15 ,.25 );

$row = writeContent($sheet, "", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, $SCHOOL_NAME, $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, $SCHOOL_CAPTION, $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "                                                                                                                                                                   Employee Balances", $bold, false, $row, $col, 0, $end_col);   
$row = writeContent($sheet, "                                                                                                                                            Cut-Off : ". date("F d, Y", strtotime($cutoff_start)) ." - ". date("F d, Y", strtotime($cutoff_end)), $bold, false, $row, $col, 0, $end_col);


$header_table = array(
	array("#", $coltitle, false, 20),
	array("Employee ID", $coltitle, false, 30),
	array("Employee Name", $coltitle, false, 50),
	array(($tag == "DEDUCTION") ? "Deduction" : "Loan", $coltitle, false, 30),
	array("Amount", $coltitle, false, 25),
);

if($tag != "DEDUCTION")  $header_table[] = array("Balances", $coltitle, false, 25);
$row += 1;
$row = writeTable($sheet, $header_table, $row, $col);

$grand_total = $grand_bal = 0;
foreach ($data_list as $sort_key => $employee_list) {
	$count = 1;
	$department_total = $department_bal = 0;
	
	if($sort_key == "ACAD"){ /// <<< for ACAD only
		$employee_list_acad = array();

		foreach ($campus as $campusid => $description) { /// <<< set campus group
			foreach ($employee_list as $employeeid => $info) {
				if($campusid == $info["campusid"]) $employee_list_acad[$campusid][$employeeid] = $info;
			}
		}

		foreach ($campus as $campusid => $description) {
			if(count($employee_list_acad[$campusid]) > 0) $row = writeContent($sheet, $department[$sort_key] ." - ". $description, $bold, false, $row, $col, 0, $end_col);

			if(count($employee_list_acad[$campusid]) > 0){
				foreach ($employee_list_acad[$campusid] as $employeeid => $info) {
					$is_first = true;
					$sub_total = $sub_bal = 0;

					foreach ($info["loan_deduc_list"] as $code => $ld_info) {
						$idx   = ($is_first) ? $count : "";
						$empid = ($is_first) ? $employeeid : "";
						$name  = ($is_first) ? strtoupper(utf8_decode($info["name"])) : "";

						$table_content = array(
							array($idx, $normalcenter, false, 20),
							array($empid, $normalcenter, true, 30),
							array($name, $normal, false, 50),
							array($config[$code], $normal, false, 30),
							array(number_format($ld_info["amount"], 2), $normalright, true, 25),
						);
						
						if($tag != "DEDUCTION")  $table_content[] = array(number_format($ld_info["balance"], 2), $normalright, true, 25);
						$row = writeTable($sheet, $table_content, $row, $col);

						$sub_total 	 		+= $ld_info["amount"];	
						$grand_total 		+= $ld_info["amount"];
						$department_total 	+= $ld_info["amount"];

						$sub_bal 	 		+= $ld_info["balance"];	
						$grand_bal 			+= $ld_info["balance"];
						$department_bal 	+= $ld_info["balance"];
						$is_first = false;	
					}

					if(!$display_subtotal){
						writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 3);
						$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);

					}

					if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 5);
					$count += 1;
				}
			}
		}
	}else{
		if($sort_by == "department") $row = writeContent($sheet, $department[$sort_key], $bold, false, $row, $col, 0, $end_col);

		foreach ($employee_list as $employeeid => $info) {
			$is_first = true;
			$sub_total = $sub_bal = 0;

			foreach ($info["loan_deduc_list"] as $code => $ld_info) {
				$idx   = ($is_first) ? $count : "";
				$empid = ($is_first) ? $employeeid : "";
				$name  = ($is_first) ? strtoupper(utf8_decode($info["name"])) : "";

				$table_content = array(
					array($idx, $normalcenter, false, 20),
					array($empid, $normalcenter, true, 30),
					array($name, $normal, false, 50),
					array($config[$code], $normal, false, 30),
					array(number_format($ld_info["amount"], 2), $normalright, true, 25)
				);
				
				if($tag != "DEDUCTION")  $table_content[] = array(number_format($ld_info["balance"], 2), $normalright, true, 25);
				$row = writeTable($sheet, $table_content, $row, $col);

				$sub_total 	 		+= $ld_info["amount"];	
				$grand_total 		+= $ld_info["amount"];
				$department_total 	+= $ld_info["amount"];

				$sub_bal 	 		+= $ld_info["balance"];	
				$grand_bal 			+= $ld_info["balance"];
				$department_bal 	+= $ld_info["balance"];
				$is_first = false;	
			}
			
			if(!$display_subtotal){
				writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 3);
				$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);
			}

			if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 5);

			$count += 1;
		}
	}

	if($sort_by == "department"){
			   writeContent($sheet, "Department Total : ", $normalBold, false, $row, $col, 0, 3);
		$row = writeContent($sheet, number_format($department_total, 2), $normalBold, true, $row, 4);
		if($tag != "DEDUCTION") writeContent($sheet, number_format($department_bal, 2), $normalBold, true, ($row - 1), 5);
	}
}

$row += 1;
	   writeContent($sheet, "Grand Total : ", $normalBold, false, $row, $col, 0, 3);
$row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 4);
if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 5);

$xls->close();