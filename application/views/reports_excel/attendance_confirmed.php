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
    $sheet->write(5,5,"ATTENDANCE CONFIRMED",$newWave);
    $sheet->write(6,5,$dateRange, $newWaveLCaptionDate );
    /*end of report title*/

    /*table header*/
    $sheet->setMerge(7, 0, 8, 0);
    $sheet->write(7,0,"Employee ID",$coltitle);
    $sheet->write(8,0,"Employee ID",$coltitle);
    $sheet->setMerge(7, 1, 8, 1);
    $sheet->write(7,1,"Name",$coltitle);
    $sheet->write(8,1,"",$coltitle);
    $sheet->setMerge(7, 2, 8, 2);
    $sheet->write(7,2,"Department",$coltitle);
    $sheet->write(8,2,"",$coltitle);
  
    $sheet->write(7,3,"Total Hours",$coltitle);
    $sheet->write(7,4,"",$coltitle);
    $sheet->write(7,5,"",$coltitle);
    $sheet->setMerge(7, 3, 7, 5);
    $sheet->write(8,3,"Lec",$coltitle);
    $sheet->write(8,4,"Lab",$coltitle);
    $sheet->write(8,5,"Admin",$coltitle);

    $sheet->write(7,6,"No. of late/UT (hr:min)",$coltitle);
    $sheet->write(7,7,"",$coltitle);
    $sheet->write(7,8,"",$coltitle);
    $sheet->setMerge(7, 6, 7, 8);
    $sheet->write(8,6,"Lec",$coltitle);
    $sheet->write(8,7,"Lab",$coltitle);
    $sheet->write(8,8,"Admin",$coltitle);

    $sheet->write(7,9,"Absent",$coltitle);
    $sheet->write(8,9,"Subject",$coltitle);
    $sheet->write(7,10,"Leaves",$coltitle);
    $sheet->write(7,11,"",$coltitle);
    $sheet->write(7,12,"",$coltitle);
    $sheet->setMerge(7, 10, 7, 12);
    $sheet->write(8,10,"Emergency",$coltitle);
    $sheet->write(8,11,"Vacation",$coltitle);
    $sheet->write(8,12,"Sick",$coltitle);

    $sheet->write(7,13,"Total Deduction",$coltitle);
    $sheet->write(7,14,"",$coltitle);
    $sheet->write(7,15,"",$coltitle);
    $sheet->setMerge(7, 13, 7, 15);
    $sheet->write(8,13,"Lec",$coltitle);
    $sheet->write(8,14,"Lab",$coltitle);
    $sheet->write(8,15,"Admin",$coltitle);

    $sheet->setMerge(7, 16, 8, 16);
    $sheet->write(7,16,"Date of Absent",$coltitle);
    $sheet->write(8,16,"",$coltitle);
    $sheet->setMerge(7, 17, 8, 17);
    $sheet->write(7,17,"Hold Status",$coltitle);
    $sheet->write(8,17,"",$coltitle);

    /*end of header*/
    $row = 9;
    foreach ($attendance_list as $deptid => $dept_det) {
        if(isset($office_list[$deptid])){
            $sheet->write($row,0,$office_list[$deptid],$newWaveLDepartment);
            $row+=1;
        }else{
            $sheet->write($row,0,"",$newWaveLDepartment);
            $row+=1;
        }
        foreach ($dept_det as $employeeid => $emp_det) { 
            $perdept_count = sizeof($emp_det['perdept_arr']) > 1 ? sizeof($emp_det['perdept_arr']) : 1;
            $sheet->write($row,0,$employeeid,$coldata);
            $sheet->write($row,1,$emp_det['fullname'],$coldata);
            $sheet->write($row,9,$emp_det['absent'],$coldata);
            $sheet->write($row,10,$emp_det['eleave'],$coldata);
            $sheet->write($row,11,$emp_det['vleave'],$coldata);
            $sheet->write($row,12,$emp_det['sleave'],$coldata);
            $sheet->write($row,16,isset($emp_det['day_absent']) ? $emp_det['day_absent'] : 0,$coldata);
            $sheet->write($row,16,isset($emp_det['hold_status_change']) ? $emp_det['hold_status_change'] : 0,$coldata);
            if(sizeof($emp_det['perdept_arr']) > 0){
                foreach ($emp_det['perdept_arr'] as $aimsdept => $perdept_det) { 
                    $sheet->write($row,0,"",$coldata);
                    $sheet->write($row,1,"",$coldata);
                    $sheet->write($row,2,isset($office_list[$aimsdept]) ? $office_list[$aimsdept] : 0,$coldata);
                    $sheet->write($row,3,isset($perdept_det['LEC']) ? $perdept_det['LEC']['work_hours'] : 0,$coldata);
                    $sheet->write($row,4,isset($perdept_det['LAB']) ? $perdept_det['LAB']['work_hours'] : 0,$coldata);
                    $sheet->write($row,5,isset($perdept_det['ADMIN']) ? $perdept_det['ADMIN']['work_hours'] : 0,$coldata);
                    $sheet->write($row,6,isset($perdept_det['LEC']) ? $perdept_det['LEC']['late_hours'] : 0,$coldata);
                    $sheet->write($row,7,isset($perdept_det['LAB']) ? $perdept_det['LAB']['late_hours'] : 0,$coldata);
                    $sheet->write($row,8,isset($perdept_det['ADMIN']) ? $perdept_det['ADMIN']['late_hours'] : 0,$coldata);
                    $sheet->write($row,9,"",$coldata);
                    $sheet->write($row,10,"",$coldata);
                    $sheet->write($row,11,"",$coldata);
                    $sheet->write($row,12,"",$coldata);
                    $sheet->write($row,13,isset($perdept_det['LEC']) ? $perdept_det['LEC']['deduc_hours'] : 0,$coldata);
                    $sheet->write($row,14,isset($perdept_det['LAB']) ? $perdept_det['LAB']['deduc_hours'] : 0,$coldata);
                    $sheet->write($row,15,isset($perdept_det['ADMIN']) ? $perdept_det['ADMIN']['deduc_hours'] : 0,$coldata);
                    $sheet->write($row,16,"",$coldata);
                    $sheet->write($row,17,"",$coldata);
                    $row++;
                }

            }else{
                    $sheet->write($row,0,"",$coldata);
                    $sheet->write($row,1,"",$coldata);
                    $sheet->write($row,2,"",$coldata);
                    $sheet->write($row,3,"",$coldata);
                    $sheet->write($row,4,"",$coldata);
                    $sheet->write($row,5,"",$coldata);
                    $sheet->write($row,6,"",$coldata);
                    $sheet->write($row,7,"",$coldata);
                    $sheet->write($row,8,"",$coldata);
                    $sheet->write($row,9,"",$coldata);
                    $sheet->write($row,10,"",$coldata);
                    $sheet->write($row,11,"",$coldata);
                    $sheet->write($row,12,"",$coldata);
                    $sheet->write($row,13,"",$coldata);
                    $sheet->write($row,14,"",$coldata);
                    $sheet->write($row,15,"",$coldata);
                    $sheet->write($row,16,"",$coldata);
                    $sheet->write($row,17,"",$coldata);
            }
       
            $row++;

        }
        $row++;

    }

$sheet->setColumn(0, 15, 25);
// end of table content
$xls->send("Attendance Summary.xls");
$xls->close();
?>