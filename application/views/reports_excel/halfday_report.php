<?php

$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
$xls = New Spreadsheet_Excel_Writer();
$xls->send("Halfday Report.xls");

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


    $leftSubHeaders = array(
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
    );

    $rightSubHeaders = array(
        array(
            'headerVal' => "Frequency",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
        array(
            'headerVal' => "Total",
            'headerFont' => $fntColT,
            'doMerge' => false,
            'mergeCol' => 0,
            'mergeRow' => 0           
        ),
    );
    $numdays = $this->time->giveNumDaysCovered($from_date, $to_date);
    $colsMerge = ($numdays - 1) +  
        count($leftSubHeaders) + count($rightSubHeaders);

    $mainHeaders = array(
        array(
            'headerVal' => "Saint Jude Catholic School",
            'headerFont' => $fntBigBoldCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "327 Ycaza Street, San Miguel Manila",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "HALFDAY REPORT",
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
        array(
            'headerVal' => "FOR THE PERIO OF " . $dateRange,
            'headerFont' => $fntNormalCenter,
            'doMerge' => true,
            'mergeCol' => $colsMerge,
            'mergeRow' => 0
        ),
    );

$isDateHeadersCreated = false;
$dateHeaders = array();
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
            if ($mainhead["mergeCol"]>0) {
                $colToMerge = $mainhead["mergeCol"];
                $c+= $colToMerge;
                $sheet->write($r, $c, "", $mainhead["headerFont"]);
                $sheet->mergeCells($r,$c-$colToMerge,$r,$c);
            }// end if
        }// end if
        $r++;
    }
    $c = 0;

    foreach ($leftSubHeaders as $key => $leftsub) {
        $sheet->write($r,$c, $leftsub["headerVal"], $leftsub["headerFont"]);
        $c++;
    }
    $colDateHeadStart = $c;
    $rowDateHead = $r;

    $c += $numdays;
    foreach ($rightSubHeaders as $key => $rightsub) {
        $sheet->write($r,$c, $rightsub["headerVal"], $rightsub["headerFont"]);
        $c++;
    }
    $r++;
    $sheet->freezePanes(array($r, 0, $r, 0)); 

    // write information to excel sheet
    foreach ($result as $key => $datum) {
        $c = 0;
        $theId = $datum["qEmpId"];
        $theName = $datum["qFullname"];
        $arrHalfday = array();

        //writing employee department
        if ($deptDisplay != $datum["qDepartment"]) {
            $sheet->write($r,$c, $datum["qDepartment"], $fntBold);
            $r++;
        }

        // write employee id and fullname
        $sheet->write($r,$c, $theId, $fntNormal);
        $c++;
        $sheet->write($r,$c, $theName, $fntNormal);
        $c++;

        // get individual summary
        $empSumm = $this->attendance->giveIndividualSummary($from_date, $to_date, $theId);

        // create date headers
        if (!$isDateHeadersCreated) {
            foreach ($empSumm as $key => $summ) {
                if (!in_array($summ["queLogDate"], $dateHeaders)) {
                    $dte = array(
                        'headerVal' => date("d (l)",strtotime($summ["queLogDate"])),
                        'headerFont' => $fntColT,
                        'doMerge' => false,
                        'mergeRow' => 0,
                        'mergeCol' => 0
                    );
                    array_push($dateHeaders, $dte);
                }// end if
            }// end foreach
            $isDateHeadersCreated = true;
        }// end if

        // parse through the individual summary
        foreach ($empSumm as $key => $summ2) {
            $half = ($summ2["queHalfDayToday"] > 0) ? "X" : "";

            if ($half != "") {
                array_push($arrHalfday, $summ2["queHalfDayToday"]);
            }

            $fntToWrite = ($half != "") ? $fntTardyCenter : $fntNormalCenter;

            // write half ("X" or "") to document
            $sheet->write($r,$c, $half, $fntToWrite);
            $c++;
        }

        // write totals
        $sheet->write($r,$c, count($arrHalfday), $fntNormalCenter);
        $c++;
        $sheet->write($r,$c, (array_sum($arrHalfday)), $fntNormalCenter); 
        $c++;

        $deptDisplay = $datum["qDepartment"];   //set current department
        $r++; // next row
    }// end foreach

    // write dateheaders
    $col = $colDateHeadStart;
    $row = $rowDateHead;
    foreach ($dateHeaders as $key => $datehead) {
        $sheet->write($row,$col, $datehead["headerVal"] , $datehead["headerFont"]);
        $col++;
    }
}// end if
$xls->close(); // closes spreadsheet object