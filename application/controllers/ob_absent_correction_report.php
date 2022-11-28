<?php 
/**
* @author justin (with e)
* @copyright 2018
*/

require_once(APPPATH."constants.php");
$xls = New Spreadsheet_Excel_Writer();
$xls->send("Reglamantory Deduction.xls");

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

$sheet = &$xls->addWorksheet("Reglamantory Report");

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
$row = $col = 0;
$end_col = ($sd_filter == "detailed") ? (count($cutoff_list) + 8) - 1 : (6 - 1);

$row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, $SCHOOL_NAME, $bigboldcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, $SCHOOL_CAPTION, $normalcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, "Absent/OB/Excuse Slip Report", $normalcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, "For the month of ". $month, $normalcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);

$bitmap = "images/school_logo_bm.bmp";


$xls->close();