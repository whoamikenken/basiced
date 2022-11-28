<?php 
/**
* @author justin (with e)
* @copyright 2018
*/
$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
require_once(APPPATH."constants.php");

$xls = New Spreadsheet_Excel_Writer();
$xls->send("OB Absent Correction Report.xls");

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

$sheet = &$xls->addWorksheet("OB Absent Correction Report");

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
$bitmap = "images/school_logo.bmp";
$row = $col = 0;
$end_col = 6;
$sheet->insertBitmap( $row , $col  + 1 , $bitmap , 10 , 8 , .10 ,.20 );
$row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, $SCHOOL_NAME, $boldcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, $SCHOOL_CAPTION, $boldcenter, $row, $col, 0, $end_col);
// $row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, "OB/Excuse Slip Report", $bigboldcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, date('F j, Y', strtotime($dfrom))." - ".date('F j, Y', strtotime($dto)), $normalcenter, $row, $col, 0, $end_col);
$row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);

    
    
$table_header = array(
	array("EMPLOYEE NAME", 40, $coltitle),
	array("TYPE", 20, $coltitle),
	array("POSITION", 30, $coltitle),
	array("DEPARTMENT", 20, $coltitle),
	array("INCLUSIVE DATE", 30, $coltitle),
	array("DATE APPLIED", 20, $coltitle),
	array("REASON", 30, $coltitle)
);

$row = writeTable($sheet, array($table_header), $row, $col);

foreach ($emp_list as $info) {
	$table_content = array(
		array(utf8_decode($info["fullname"]), 40, $normal),
		array($info["type"], 20, $normalcenter),
		array($info["position"], 30, $normal),
		array($info["department"], 20, $normalcenter),
		array($info["date_exclusive"], 30, $normalcenter),
		array($info["time_requested"], 20, $normalcenter),
		array($info["reason"], 30, $normal)
	);

	$row = writeTable($sheet, array($table_content), $row, $col);
}

$xls->close();