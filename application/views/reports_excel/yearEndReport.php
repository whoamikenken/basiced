<?php
/**
* @author Max Consul
* @copyright 2018
*/
// echo "<pre>"; print_r($attendance_list); die;
$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
$xls = New Spreadsheet_Excel_Writer();
require_once(APPPATH."constants.php");
require 'excel_fonts.php';

$sheet = &$xls->addWorksheet("Sheet 1");
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

    $coltitle =& $xls->addFormat(array('Size' => 9));
    $coltitle->setBorder(1);
    $coltitle->setAlign("center");
    $coltitle->setFontFamily("Century Gothic");
    $coltitle->setLocked();

    $coldata =& $xls->addFormat(array('Size' => 8));
    $coldata ->setAlign("center");
    $coldata->setBorder(1);
    $coldata->setFontFamily("Century Gothic");
    $coldata ->setLocked();

    $coldataName =& $xls->addFormat(array('Size' => 8));
    $coldataName->setBorder(1);
    $coldataName->setFontFamily("Century Gothic");
    $coldataName ->setLocked();

    /** end Fonts Format */

    /*add school logo and caption*/
    $sheet->write(1,4,$SCHOOL_NAME,$newWaveL);
    $sheet->write(2,4,$SCHOOL_CAPTION,$newWaveLCaption);
    /*end of school logo and caption*/

    $bitmap = "images/school_logo.bmp";
    $sheet->setMerge(0, 0, 1, 0);
    $sheet->insertBitmap( 0 , 3 , $bitmap , 5 , 10 , .15 ,.15 );

    /*report title*/
    $sheet->write(5,4,"13th Month, 14th Month, Year End Income Report",$newWave);
    /*end of report title*/

    /*table content*/
    $sheet->setColumn(2,2,12);
    $sheet->write(7,2,"Employee ID",$coltitle);
    $sheet->setColumn(3,3,25);
    $sheet->write(7,3,"Fullname",$coltitle);
    $sheet->setColumn(4,4,15);
    $sheet->write(7,4,"Basic Pay",$coltitle);
    $sheet->setColumn(5,5,25);
    $sheet->write(7,5,"Income Type",$coltitle);
    $sheet->setColumn(6,6,15);
    $sheet->write(7,6,"Amount",$coltitle);
    $sheet->setColumn(7,7,15);
    $sheet->write(7,7,"Percentage",$coltitle);

    $row = 8;
    foreach($emplist as $empid => $info){
        $sheet->write($row,2,$empid,$coldata);
        $sheet->write($row,3,$info['fullname'],$coldataName);
        $sheet->write($row,4,$info['basic_pay'],$coldata);
        foreach($info["incomes"] as $inc_key => $amount){
            $sheet->write($row,5,$this->payroll->getIncomeDescription($inc_key),$coldata);
            $sheet->write($row,6,number_format($amount, 2),$coldata);
            $sheet->write($row,7,number_format($amount/$info["basic_pay"]*100, 2),$coldata);

            $row++;
        }
            

        $row++;
    } 

// end of table content
$xls->send("Income Report.xls");
$xls->close();
?>