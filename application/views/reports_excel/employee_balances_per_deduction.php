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

$coltitle =& $xls->addFormat(array('Size' => 10));
$coltitle->setBorder(2);
$coltitle->setBold();
$coltitle->setAlign("center");
$coltitle->setFgColor('black');
$coltitle->setColor('yellow');
$coltitle->setLocked();


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

$header_table = array(
	array("#", $coltitle, false, 20),
	array("Employee ID", $coltitle, false, 30),
	array("Employee Name", $coltitle, false, 50),
	array("Amount", $coltitle, false, 25),
);
if($tag != "DEDUCTION")  $header_table[] = array("Balances", $coltitle, false, 25);

$end_col = ($tag == "DEDUCTION") ? 3 : 4;
$row = $col = 0;

$bitmap = "images/school_logo.bmp";
$sheet->insertBitmap( 1 , 1 , $bitmap , 10 , 0 , .10 ,.20 );

$row = writeContent($sheet, "", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, $SCHOOL_NAME, $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, $SCHOOL_CAPTION, $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "                                                                                                                              Employee Balances", $bold, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "                                                                                                         Cut-Off : ". date("F d, Y", strtotime($cutoff_start)) ." - ". date("F d, Y", strtotime($cutoff_end)), $bold, false, $row, $col, 0, $end_col);


$row += 1;
$grand_total = $grand_bal = 0;
foreach ($data_list as $code => $list) { // <<< displayed content
		   writeContent($sheet, "Description : ", $bold, false, $row, $col);
	$row = writeContent($sheet, $config[$code], $bold, false, $row, 1, 0, $end_col - 1);

	$row = writeTable($sheet, $header_table, $row, $col);

	ksort($list);
	$count = 1;
	foreach ($list as $sort_key => $employee_list) {
		$sub_total = $sub_bal = 0;

		if($sort_key == "ACAD" && $sort_by == "department"){ // <<< ACAD ONLY
			$employee_list_acad = array();
			foreach ($campus as $c_code => $c_desc) {
				foreach ($employee_list as $employeeid => $info) {
					if($info["campusid"] == $c_code) $employee_list_acad[$c_code][$employeeid] = $info;
				}
			}

			foreach ($campus as $c_code => $c_desc) {
				if(isset($employee_list_acad[$c_code])){
					$sub_total = 0;
					$row = writeContent($sheet, $department[$sort_key] ." - ". $c_desc, $bold, false, $row, $col, 0, $end_col);

					foreach ($employee_list_acad[$c_code] as $employeeid => $info) {
						$table_content = array(
							array($count, $normalcenter, false, 20),
							array($employeeid, $normalcenter, true, 30),
							array(strtoupper(utf8_decode($info["name"])), $normal, false, 50),
							array(number_format($info["amount"], 2), $normalright, true, 25),
						);
						if($tag != "DEDUCTION")  $table_content[] = array(number_format($info["balance"], 2), $normalright, true, 25);

						$count += 1;
						$sub_total += $info["amount"];
						$grand_total += $info["amount"];
						$sub_bal += $info["balance"];
						$grand_bal += $info["balance"];
						$row = writeTable($sheet, $table_content, $row, $col);
					}
					
						   writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
					$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
					if($tag != "DEDUCTION") writeContent($sheet, number_format($sub_bal, 2), $normalBold, true, ($row - 1), 4);
				}
			}
		}else{
			if($sort_by == "department") $row = writeContent($sheet, $department[$sort_key], $bold, false, $row, $col, 0, $end_col);

			foreach ($employee_list as $employeeid => $info) {
				$table_content = array(
					array($count, $normalcenter, false, 20),
					array($employeeid, $normalcenter, true, 30),
					array(strtoupper(utf8_decode($info["name"])), $normal, false, 50),
					array(number_format($info["amount"], 2), $normalright, true, 25),
				);
				if($tag != "DEDUCTION")  $table_content[] = array(number_format($info["balance"], 2), $normalright, true, 25);

				$count += 1;
				$sub_total += $info["amount"];
				$grand_total += $info["amount"];
				$sub_bal += $info["balance"];
				$grand_bal += $info["balance"];
				$row = writeTable($sheet, $table_content, $row, $col);
			}

				   writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
			$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
			if($tag != "DEDUCTION") writeContent($sheet, number_format($sub_bal, 2), $normalBold, true, ($row - 1), 4);
		}
		
	}

		   
	$row += 1; // <<< space	
}

if($grand_total){
		   writeContent($sheet, "Grand Total : ", $normalBold, false, $row, $col, 0, 2);
	$row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 3);
	if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 4);	
}

$xls->close();