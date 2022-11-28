<?php
/**
* @author justin (with e)
* @copyright 2018
*/

$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
require_once(APPPATH."constants.php");
$xls = New Spreadsheet_Excel_Writer();

$xls->send("Income Report (Per Employee).xls");

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

$sheet = &$xls->addWorksheet("Income Report (per employee)");

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

$end_col = 4;
$row = $col = 0;

$bitmap = "images/school_logo.bmp";
$sheet->insertBitmap( $row , $col , $bitmap , 50 , 0 , .13 ,.25 );

$row = writeContent($sheet, "", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, $SCHOOL_NAME, $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, $SCHOOL_CAPTION, $boldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "INCOME TRANSACTION", $bigboldcenter, false, $row, $col, 0, $end_col);
$row = writeContent($sheet, "Cut-Off : ". date("F d, Y", strtotime($cutoff_start)) ." - ". date("F d, Y", strtotime($cutoff_end)), $normalcenter, false, $row, $col, 0, $end_col);


$header_table = array(
	array("#", $coltitle, false, 10),
	array("EMPLOYEE ID", $coltitle, false, 30),
	array("EMPLOYEE NAME", $coltitle, false, 30),
	array("INCOME", $coltitle, false, 30),
	array("AMOUNT", $coltitle, false, 25),
	array("CUT-OFF", $coltitle, false, 25),
	array("STATUS", $coltitle, false, 25)
);
$row += 1;
$row = writeTable($sheet, $header_table, $row, $col);

$grand_total = 0;
foreach ($data_list as $sort_key => $employee_list) {
	$count = 1;
	$department_total = 0;
	
	if($sort_key == "ACAD"){ /// <<< for ACAD only
		$employee_list_acad = array();

		foreach ($campus as $campusid => $description) { /// <<< set campus group
			foreach ($employee_list as $employeeid => $info) {
				if($campusid == $info["campusid"]) $employee_list_acad[$campusid][$employeeid] = $info;
			}
		}

		foreach ($campus as $campusid => $description) {
			if(count($employee_list_acad[$campusid]) > 0) $row = writeContent($sheet, $department[$sort_key] ." - ". $description, $boldColorBlue, false, $row, $col, 0, $end_col);

			if(count($employee_list_acad[$campusid]) > 0){
				foreach ($employee_list_acad[$campusid] as $employeeid => $info) {
					$is_first = true;
					$sub_total = 0;

					foreach ($info["income"] as $code => $amount) {
						$idx   = ($is_first) ? $count : "";
						$empid = ($is_first) ? $employeeid : "";
						$name  = ($is_first) ? strtoupper(utf8_decode($info["name"])) : "";

						$table_content = array(
							array($idx, $normalcenter, false, 20),
							array($empid, $normalcenter, true, 30),
							array($name, $normal, false, 50),
							array($income_list[$code]["description"], $normal, false, 30),
							array(number_format($amount, 2), $normalright, true, 25),
							array($infos["status"], $normalright, true, 25)
						);
						
						$row = writeTable($sheet, $table_content, $row, $col);

						$sub_total 	 		+= $amount;	
						$grand_total 		+= $amount;
						$department_total 	+= $amount;
						$is_first = false;	
					}

					if($is_select_all){
							   writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 3);
						$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);
					}

					$count += 1;
				}
			}
		}
	}else{
		if($sort_by == "department") $row = writeContent($sheet, $department[$sort_key], $bold, false, $row, 1, 0, $end_col);

		foreach ($employee_list as $employeeid => $info) {
			$is_first = false;
			$sub_total = 0;
			$table_content = array(
					array($count, $normalcenter, false, 20),
					array($employeeid, $normalcenter, true, 30),
					array(strtoupper(utf8_decode($info["name"])), $normal, false, 50)
			);
			$row = writeTable($sheet, $table_content, $row, $col);

			foreach ($info["income"] as $code => $amount) {
				$idx   = ($is_first) ? $count : "";
				$empid = ($is_first) ? $employeeid : "";
				$name  = ($is_first) ? strtoupper(utf8_decode($info["name"])) : "";

				$table_content = array(
					array($idx, $normalcenter, true, 30),
					array($empid, $normalcenter, true, 30),
					array($name, $normal, false, 50),
					array($income_list[$code]["description"], $normal, false, 30),
					array(number_format($amount, 2), $normalright, true, 25),
					array(date('M d-',strtotime($cutoff_start)) . date('d, Y',strtotime($cutoff_end)), $normalright, true, 25),
					array($info["status"], $normalright, true, 25)
				);
				
				$row = writeTable($sheet, $table_content, $row, $col);

				$sub_total 	 		+= $amount;	
				$grand_total 		+= $amount;
				$department_total 	+= $amount;
				$is_first = false;	
			}
			
			if($is_select_all){
					   writeContent($sheet, "TOTAL : ", $normalBold, false, $row, $col, 0, 3);
				$row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);
			}

			$count += 1;
		}
	}

	if($sort_by == "department"){
			   writeContent($sheet, "DEPARTMENT TOTAL : ", $normalBold, false, $row, $col, 0, 3);
		$row = writeContent($sheet, number_format($department_total, 2), $normalBold, true, $row, 4);
	}
}

$row += 1;
	   writeContent($sheet, "GRAND TOTAL : ", $normalBold, false, $row, $col, 0, 3);
$row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 4);
$xls->close();