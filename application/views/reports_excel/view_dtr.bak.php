<?php
$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");

$xls = New Spreadsheet_Excel_Writer();
$xls->send("ALLPAYOUTS.xls");

#$xls->setCustomColor(11, 216,216,216);
#$xls->setCustomColor(12, 255,199,206);
/** Fonts Format */
	$normal =& $xls->addFormat(array('Size' => 10));
	$normal->setLocked();
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
	$normalunderlined->setBottom(1);
    $normalunderlined->setLocked();
    $tits =& $xls->addFormat(array('Size' => 10));
	$tits->setBold();
	$tits->setAlign("center");
	$tits->setLocked();
	$titsnormal =& $xls->addFormat(array('Size' => 10));
    $titsnormal->setAlign("center");
	$titsnormal->setLocked();
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
    $messbord =& $xls->addFormat(array('Size' => 8));
	$messbord->setBorder(1);
    $messbord->setAlign("center");
	$messbord->setLocked();
    $messbordpink =& $xls->addFormat(array('Size' => 8));
	$messbordpink->setBorder(1);
    $messbordpink->setBgColor(12);
    $messbordpink->setFgColor(12);
    $messbordpink->setAlign("center");
	$messbordpink->setLocked();
	$big =& $xls->addFormat(array('Size' => 12));
	$big->setLocked();
	$bigbold =& $xls->addFormat(array('Size' => 12));
	$bigbold->setBold();
	$bigbold->setLocked();
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
    $amountbold->setAlign("center");
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
/** End of Font Format */

$sheet =& $xls->addWorksheet("Sheet 1");
/** Set column's  sizes */
$sheet->setColumn(0,0,20);
$sheet->setColumn(1,1,70);
$sheet->setColumn(2,2,30);
$sheet->setColumn(3,3,30);
$sheet->setColumn(4,4,30);
$r = 0;
/** Title */
$sheet->setMerge($r, 0, $r, 4);
$sheet->write($r,0,"",$tits);
$r++;
$sheet->write($r,0,"LIST OF ALL PAYOUTS",$tits);
$sheet->setMerge($r, 0, $r, 4);
$r++;
$sheet->write($r,0,"AS OF ".date("F d, Y"),$titsnormal);
$sheet->setMerge($r, 0, $r, 4);
$r++;
$r++;
$c = 0;
$sheet->write($r,$c,"ACCOUNT NO.",$coltitle);$c++;
$sheet->write($r,$c,"FULL NAME",$coltitle);$c++;
$sheet->write($r,$c,"DATE PROCESS",$coltitle);$c++;
$sheet->write($r,$c,"TOTAL DOWNLINES",$coltitle);$c++;
$sheet->write($r,$c,"TOTAL PAYOUT",$coltitle);$c++;
$r++;
$sheet->freezePanes(array($r, 0, $r, 0)); /** This freezes the row starting from the row 4 */

$r++; 
$r++; 
$c=3;
$sheet->write($r,$c,"GRAND TOTAL: ",$boldcenter);
$c++;
$sheet->write($r,$c,number_format($total,2),$boldcenter);

$xls->close();
?>