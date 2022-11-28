<?php
/**
* @author Max Consul
* @copyright 2018
*/
$this->load->library('lib_includer');
$this->lib_includer->load("excel/Writer");
$xls = New Spreadsheet_Excel_Writer();
require_once(APPPATH."constants.php");

require 'excel_fonts.php';

$sheet = &$xls->addWorksheet("Sheet 1");
    /** Fonts Format */
    $coltitle =& $xls->addFormat(array('Size' => 12));
    $coltitle->setBorder(1);
    $coltitle->setAlign("center");
    $coltitle ->setBold();
    $coltitle->setBgColor("black");
    $coltitle->setColor("yellow");
    $coltitle->setLocked();

    $newWave =& $xls->addFormat(array('Size' => 15));
    $newWave ->setBold();
    $newWave ->setAlign("center");
    $newWave ->setLocked();

    $coldata =& $xls->addFormat(array('Size' => 10));
    $coldata ->setAlign("center");
    $coldata ->setLocked();

    $newWaveL =& $xls->addFormat(array('Size' => 15));
    $newWaveL ->setBold();
    $newWaveL ->setAlign("left");
    $newWaveL ->setLocked();

    $newWaveLCaption =& $xls->addFormat(array('Size' => 13));
    $newWaveLCaption ->setAlign("left");
    $newWaveLCaption ->setLocked();

    $newWaveLCaptionDate =& $xls->addFormat(array('Size' => 13));
    $newWaveLCaptionDate ->setAlign("center");
    $newWaveLCaptionDate ->setLocked();

    $newWaveLDepartment =& $xls->addFormat(array('Size' => 11));
    $newWaveLDepartment ->setAlign("left");
    $newWaveLDepartment ->setLocked();
    $newWaveLDepartment ->setBold();
    /** end Fonts Format */

    /*add school logo and caption*/
    $sheet->write(1,1,$SCHOOL_NAME,$newWaveL);
    $sheet->write(2,1,$SCHOOL_CAPTION,$newWaveLCaption);
    /*end of school logo and caption*/

    $bitmap = "images/school_logo.bmp";
    $sheet->setMerge(0, 0, 1, 0);
    $sheet->insertBitmap( 0 , 0 , $bitmap , 5 , 10 , .15 ,.15 );

    /*report title*/
    $sheet->write(4,5,"ATTENDANCE CONFIRMED",$newWave);
    $sheet->write(5,5,$dateRange, $newWaveLCaptionDate );
    /*end of report title*/

    /*table header*/
    $sheet->setMerge(6, 0, 7, 0);
    $sheet->write(6,0,"Employee ID",$coltitle);
    $sheet->write(7,0,"Employee ID",$coltitle);
    $sheet->setMerge(6, 1, 7, 1);
    $sheet->write(6,1,"Name",$coltitle);
    $sheet->write(7,1,"",$coltitle);
  
    $sheet->write(6,2,"Overtime (hr:min)",$coltitle);
    $sheet->write(6,3,"",$coltitle);
    $sheet->write(6,4,"",$coltitle);
    $sheet->setMerge(6, 2, 6, 4);
    $sheet->write(7,2,"Regular",$coltitle);
    $sheet->write(7,3,"Rest Day",$coltitle);
    $sheet->write(7,4,"Holiday",$coltitle);

    $sheet->write(6,5,"Late/Undertime",$coltitle);
    $sheet->write(7,5,"Hr:min",$coltitle);

    $sheet->setMerge(6, 6, 7, 6);
    $sheet->write(6,6,"Absent",$coltitle);
    $sheet->write(7,6,"",$coltitle);

    $sheet->write(6,7,"Leaves",$coltitle);
    $sheet->write(7,7,"",$coltitle);
    $sheet->write(6,8,"",$coltitle);
    $sheet->write(6,9,"",$coltitle);
    $sheet->setMerge(6, 7, 6, 9);
    $sheet->write(7,7,"VL",$coltitle);
    $sheet->write(7,8,"SL",$coltitle);
    $sheet->write(7,9,"Other",$coltitle);

    $sheet->setMerge(6, 10, 7, 10);
    $sheet->write(6,10,"No. of Days",$coltitle);
    $sheet->write(7,10,"",$coltitle);

    $sheet->setMerge(6, 11, 7, 11);
    $sheet->write(6,11,"Holiday",$coltitle);
    $sheet->write(7,11,"",$coltitle);


    $row = 8;
    foreach($attendance_list as $deptid => $dept_det){
        if(isset($office_list[$deptid])){
            $sheet->write($row,0,$office_list[$deptid],$newWaveLDepartment);
            $row+=1;
        }else{
            $sheet->write($row,0,$deptid,$newWaveLDepartment);
            $row+=1;
        }

        foreach ($dept_det as $employeeid => $emp_det) { 
            $sheet->write($row,0,$employeeid,$coldata);
            $sheet->write($row,1,$emp_det['fullname'],$coldata);
            $sheet->write($row,2,$emp_det['otreg'],$coldata);
            $sheet->write($row,3,$emp_det['otrest'],$coldata);
            $sheet->write($row,4,$emp_det['othol'],$coldata);
            $sheet->write($row,5,$emp_det['lateut'],$coldata);
            $sheet->write($row,6,$emp_det['absent'],$coldata);
            $sheet->write($row,7,$emp_det['vleave'],$coldata);
            $sheet->write($row,8,$emp_det['sleave'],$coldata);
            $sheet->write($row,9,$emp_det['oleave'],$coldata);
            $sheet->write($row,10,$emp_det['workdays'],$coldata);
            $sheet->write($row,11,$emp_det['isholiday'],$coldata);
            $sheet->write($row,6,$emp_det['day_absent'],$coldata);
            $sheet->write($row,7,$emp_det['hold_status_change'],$coldata);
        
            $row++;
        // echo "<pre>"; print_r($office_list);die;
        }

    }

$sheet->setColumn(0, 15, 25);
$xls->send("Attendance Summary.xls");
$xls->close();
?>