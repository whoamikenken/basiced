<?php

$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
$xls = New Spreadsheet_Excel_Writer();
$xls->send("Absence Report.xls");

// importing the fonts configuration file
require 'excel_fonts.php';    

$dateRange = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;
$pass = $this->extras->setPass();

$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);



$subHeaders = array(
    array(
        'headerVal' => "EMPLOYEE ID",
        'headerFont' => $fntColT,
        'doMerge' => false,
        'mergeCol' => 0,
        'mergeRow' => 0           
    ),
    array(
        'headerVal' => "EMPLOYEE NAME",
        'headerFont' => $fntColT,
        'doMerge' => false,
        'mergeCol' => 0,
        'mergeRow' => 0           
    ),
    array(
        'headerVal' => "WHOLE DAY",
        'headerFont' => $fntColT,
        'doMerge' => false,
        'mergeCol' => 0,
        'mergeRow' => 0           
    ),
    /*array(
        'headerVal' => "HALF DAY",
        'headerFont' => $fntColT,
        'doMerge' => false,
        'mergeCol' => 0,
        'mergeRow' => 0           
    ),*/
    array(
        'headerVal' => "TOTAL",
        'headerFont' => $fntColT,
        'doMerge' => false,
        'mergeCol' => 0,
        'mergeRow' => 0           
    ),
);

$subheadCount = count($subHeaders) - 1;

$mainHeaders = array(
    array(
        'headerVal' => "Saint Jude Catholic School",
        'headerFont' => $fntBigBoldCenter,
        'doMerge' => true,
        'mergeCol' => $subheadCount,
        'mergeRow' => 0
    ),
    array(
        'headerVal' => "372 Ycaza Street, San Miguel, Manila",
        'headerFont' => $fntNormalCenter,
        'doMerge' => true,
        'mergeCol' => $subheadCount,
        'mergeRow' => 0
    ),
    array(
        'headerVal' => "ABSENCES REPORT",
        'headerFont' => $fntBoldCenter,
        'doMerge' => true,
        'mergeCol' => $subheadCount,
        'mergeRow' => 0
    ),
    array(
        'headerVal' => "FOR THE PERIOD OF " . strtoupper($dateRange),
        'headerFont' => $fntNormalCenter,
        'doMerge' => true,
        'mergeCol' => $subheadCount,
        'mergeRow' => 0,
    ),
); 

$deptDisplay = "";


if (count($result) > 0) {
    $sheet = &$xls->addWorksheet("Sheet 1");
    $format =& $xls->addFormat();
    
    $format->setLocked(1);
    $sheet->protect($pass);
    $r = $c = 0;


    foreach ($mainHeaders as $key => $mainhead) {
        $c = 0;
        $sheet->write($r,$c, $mainhead["headerVal"], $mainhead["headerFont"]);
        if ($mainhead["doMerge"]) {
            if ($mainhead["mergeCol"] > 0) {
                $colToMerge = $mainhead["mergeCol"];
                $c+= $colToMerge;
                $sheet->write($r, $c, "", $mainhead["headerFont"]);
                $sheet->mergeCells($r,$c-$colToMerge,$r,$c);
            }//end if
        }// end if
        $r++;
    }// end foreach
    $c = 0;
    foreach ($subHeaders as $key => $subhead) {
        $sheet->write($r,$c, $subhead["headerVal"] , $subhead["headerFont"]);
        $c++;
    }// end foreach


    $arrDeptTotalHalf = array();
    $arrDeptTotalWhole = array();
    $r++;   // next row
    foreach ($result as $key => $datum) {
        $c = 0;
        $theId = $datum["qEmpId"];
        $theName = $datum["qFullname"];
        $arrAbsenceWhole = array();
        $arrAbsenceHalf = array();

        //writing employee department
        if ($deptDisplay != $datum["qDepartment"]) {
            if ($deptDisplay != "") {
                $c++;
                $sheet->write($r,$c, $deptDisplay . " TOTAL:", $fntBoldRight);
                $c++;
                $sheet->write($r,$c, (array_sum($arrDeptTotalWhole)), $fntBoldCenter);
                $c++;
                #$sheet->write($r,$c, (array_sum($arrDeptTotalHalf)), $fntBoldCenter);
                #$c++;
                $sheet->write($r,$c, (array_sum($arrDeptTotalHalf) + array_sum($arrDeptTotalWhole)), $fntBoldCenter);
                $r++;
            }
            $c = 0;
            $sheet->write($r,$c, $datum["qDepartment"], $fntBold);
            $r++;
            $arrDeptTotalHalf = array();
            $arrDeptTotalWhole = array();
        }

        // write employee id and fullname
        $sheet->write($r,$c, $theId, $fntNormal);
        // $sheet->write(5,0, $r . "<>" .$c, $fntNormal);

        $c++;
        $sheet->write($r,$c, $theName, $fntNormal);
        $c++;

        // get individual summary
        $empSumm = $this->attendance->giveIndividualSummary($from_date, $to_date, $theId);

        foreach ($empSumm as $key => $summ) {
            if ($summ["queTotalAbsentToday"] == .5) {
                array_push($arrAbsenceHalf, $summ["queTotalAbsentToday"]);
            }
            if ($summ["queTotalAbsentToday"] > .5) {
                array_push($arrAbsenceWhole, $summ["queTotalAbsentToday"]);
            }
        }//end foreach

        $sumAbsenceHalf = array_sum($arrAbsenceHalf);
        $sumAbsenceWhole = array_sum($arrAbsenceWhole);

        if ($sumAbsenceHalf > 0) {
            array_push($arrDeptTotalHalf, $sumAbsenceHalf);
        }

        if ($sumAbsenceWhole > 0) {
            array_push($arrDeptTotalWhole, $sumAbsenceWhole);
        }

        $sheet->write($r,$c, ($sumAbsenceWhole), $fntNormalCenter);
        $c++;
        #$sheet->write($r,$c, (count($arrAbsenceHalf)), $fntNormalCenter);
        #$c++;
        $sheet->write($r,$c, ($sumAbsenceWhole + $sumAbsenceHalf), $fntNormalCenter);
        $r++;
        $deptDisplay = $datum["qDepartment"];
    }

}// end if
$xls->close();