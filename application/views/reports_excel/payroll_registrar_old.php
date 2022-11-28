<?php 
    /**
    * @author justin (with e)
    * @copyright 2018
    */
// echo "<pre>"; print_r($inc_income); 
// echo "<pre>"; print_r($emp_list); die;
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Payroll Register Report.xls");

    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();

    $normalLeftBorder =& $xls->addFormat(array('Size' => 8));
    $normalLeftBorder->setAlign("left");
    $normalLeftBorder->setBorder(1);
    $normalLeftBorder->setLocked();

    $normalCenterBorder =& $xls->addFormat(array('Size' => 8));
    $normalCenterBorder->setAlign("center");
    $normalCenterBorder->setBorder(1);
    $normalCenterBorder->setLocked();
    
    $normalRightBorder =& $xls->addFormat(array('Size' => 8));
    $normalRightBorder->setNumFormat("#,##0.00");
    $normalRightBorder->setBorder(1);
    $normalRightBorder->setAlign("right");
    $normalRightBorder->setLocked();

    $normalcenter2 =& $xls->addFormat(array('Size' => 10));
    $normalcenter2->setAlign("center");
    $normalcenter2->setNumFormat("#");
    $normalcenter2->setLocked();

    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();  
    
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
    
    $boldCenterBorder =& $xls->addFormat(array('Size' => 8));
    $boldCenterBorder->setAlign("center");
    $boldCenterBorder->setBold();
    $boldCenterBorder->setBorder(1);
    $boldCenterBorder->setTextWrap();
    $boldCenterBorder->setLocked();

    $coltitle =& $xls->addFormat(array('Size' => 10));
    $coltitle->setBorder(2);
    $coltitle->setBold();
    $coltitle->setAlign("center");
    $coltitle->setFgColor('black');
    $coltitle->setColor('yellow');
    $coltitle->setLocked();
    
    $boldLeftBorder =& $xls->addFormat(array('Size' => 8));
    $boldLeftBorder->setAlign("left");
    $boldLeftBorder->setBold();
    $boldLeftBorder->setBorder(1);
    $boldLeftBorder->setTextWrap();
    $boldLeftBorder->setLocked();
    
    $boldRightBorder =& $xls->addFormat(array('Size' => 8));
    $boldRightBorder->setAlign("right");
    $boldRightBorder->setNumFormat("#,##0.00_);\(#,##0.00\)");
    $boldRightBorder->setBold();
    $boldRightBorder->setBorder(1);
    $boldRightBorder->setTextWrap();
    $boldRightBorder->setLocked();
    
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

    $sheet = &$xls->addWorksheet("Payroll Registrar");

    function writeText(&$sheet,$text,&$font,$row1, $col1, $row2 = '', $col2 = ''){
    
        if($row2 != '' || $col2 != ''){
            $sheet->setMerge($row1,$col1,$row1 + $row2, $col1 + $col2);
        }

        $sheet->write($row1,$col1,$text,$font);

        return ($row1 + 1);
    }

    function writeBorder(&$sheet, $count_col, $start_col, $row, &$font){
        for($col = $start_col; $col < $start_col + ($count_col); $col++) { 
            $sheet->writeBlank($row, $col, $font);
        }
    }

    function writeTable(&$sheet, $arr_content, $start_col, $start_row){
        $row = $start_row;

        foreach ($arr_content as $content) {
            $col = $start_col;

            foreach ($content as $key => $info) {
                list($caption, $size, $fonts) = $info;

                $sheet->setColumn($col, $col, $size);

                if($col == 1) $sheet->writeString($row, $col, $caption, $fonts);
                else          $sheet->write($row, $col, $caption, $fonts);

                $col += 1;
            }

            $row += 1;
        }

        return $row;
    }

    
    # ======================================================== DETAILED  ========================================================
    if($sd_filter == "detailed"):
        // header
        $row = $col = 0;
        
        $count_income_col = 6 + count($inc_income['deminimissList']) + count($inc_income['noDeminimissList']) + count($inc_adjustment) + 7;
        $count_deduction_col = 3 + count((isset($grand_total["deduction"]["fixed_deduc_list"])) ? $grand_total["deduction"]["fixed_deduc_list"] : '0') + count((isset($grand_total["deduction"]["deduc_list"])) ? $grand_total["deduction"]["deduc_list"]: '0') + count((isset($grand_total["deduction"]["loan_list"])) ? $grand_total["deduction"]["loan_list"] : '0');

        $end_col = ((($count_income_col > $count_deduction_col) ? $count_income_col : $count_deduction_col) + 3) - 1;

        $row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, $SCHOOL_NAME, $bigboldcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, $SCHOOL_CAPTION, $normalcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, "PAYROLL SHEET FOR SALARY SCHEDULE : ". $sched_display, $bigboldcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, "", $normalcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
        
        $bitmap = "images/school_logo.bmp";
        $sheet->insertBitmap( 0 , 4 , $bitmap , 80 , 0 , .15 ,.20 );

        // income
        $income_header = array();
        $income_header[] = array("#", 10, $coltitle);
        $income_header[] = array("EMPLOYEE ID", 30, $coltitle);
        $income_header[] = array("EMPLOYEE NAME", 50, $coltitle);
        $income_header[] = array("SALARY", 20, $coltitle);
        $income_header[] = array("TARDY", 20, $coltitle);
        $income_header[] = array("ABSENT", 20, $coltitle);
        $income_header[] = array("BASIC PAY", 20, $coltitle);

        foreach ($inc_income['deminimissList'] as $key => $value) {
            $income_header[] = array($config["income"][$key], 20, $coltitle);
        }
        $income_header[] = array("OTHER DEMINIMISS", 20, $coltitle);

        foreach ($inc_income['noDeminimissList'] as $key => $value) {
            $income_header[] = array($config["income"][$key], 20, $coltitle);
        }
        $income_header[] = array("OTHER INCOME", 20, $coltitle);

        foreach ($inc_adjustment as $key => $value) {
            $income_header[] = array($config["income"][$key]." ADJ", 20, $coltitle);
        }
        $income_header[] = array("OTHER ADJUSTMENT", 20, $coltitle);

        $income_header[] = array("OVERTIME", 20, $coltitle);
        $income_header[] = array("GROSS PAY", 20, $coltitle);

        $income_header[] = array("WITH HOLDING TAX", 20, $coltitle);
        
        foreach ($inc_fixed_deduc as $key => $value) {
            $income_header[] = array($key, 20, $coltitle);
        }
        
        foreach ($inc_deduction as $key => $value) {
            $income_header[] = array($config["deduction"][$key], 20, $coltitle);
        }
        
        foreach ($inc_loan as $key => $value) {
            $income_header[] = array($config["loan"][$key], 20, $coltitle);
        }

        $income_header[] = array("OTHER DEDUCTION", 20, $coltitle);
        $income_header[] = array("WITH HOLDING TAX", 20, $coltitle);
        $income_header[] = array("TOTAL DEDUCTION", 20, $coltitle);
        $income_header[] = array("Net", 20, $coltitle);

        writeBorder($sheet, count($income_header + $income_header), 0, $row, $boldCenterBorder);
        $row = writeText($sheet, "  INCOME AND DEDUCTION", $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
        $row = writeTable($sheet, array($income_header), 0, $row);

        // ksort($emp_list);
        $idx = 0;
        $old_campusid = '';
        $old_deptid = '';
        $employee_count = 0;
        if(isset($teaching)){
            foreach ($teaching['emp_list'] as $sort_key => $employees) {
                if($sort == "department" && $sort_key == "ACAD"){
                    if($sort_key != "name"){
                        writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
                        $row = writeText($sheet, " ". $config[$sort][$sort_key] , $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
                    }

                    foreach ($employees as $key => $info) {
                        if($sort == "department" && $sort_key == "ACAD"){
                            if($info['campusid'] != $old_campusid){
                                if($employee_count){
                                    $content_table = array();
                                    $content_table[] = array("", 10, $boldRightBorder);
                                    $content_table[] = array("", 30, $boldRightBorder);
                                    $content_table[] = array("Sub Total : ", 50, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["salary"], 20, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["tardy"], 20, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["absent"], 20, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["basic_pay"], 20, $boldRightBorder);

                                    foreach ($inc_income['deminimissList'] as $key => $value) {
                                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                                    }
                                    $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["totalOtherDeminimissToDisplay"])) ? $teaching['summary'][$sort_key][$old_campusid]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

                                    foreach ($inc_income['noDeminimissList'] as $key => $value) {
                                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                                    }
                                    $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["totalOtherIncomeToDisplay"])) ? $teaching['summary'][$sort_key][$old_campusid]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

                                    foreach ($inc_adjustment as $key => $value) {
                                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
                                    }
                                    $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["totalOtherAdjustmentToDisplay"])) ? $teaching['summary'][$sort_key][$old_campusid]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);

                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["overtime"], 20, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["gross"], 20, $boldRightBorder);

                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["with_holding_tax"], 20, $boldRightBorder);

                                    foreach ($inc_fixed_deduc as $key => $value) {
                                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
                                    }

                                    foreach ($inc_deduction as $key => $value) {
                                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key] : 0, 20, $boldRightBorder);
                                    }

                                    foreach ($inc_loan as $key => $value) {
                                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key] : 0, 20, $boldRightBorder);
                                    }

                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["totalOtherDeductionToDisplay"], 20, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["with_holding_tax"], 20, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["total_deduction"], 20, $boldRightBorder);
                                    $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["net"], 20, $boldRightBorder);

                                    $row = writeTable($sheet, array($content_table), 0, $row);

                                    $employee_count = 0;
                                }
                                writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
                                $row = writeText($sheet, " ". $info['campusid'], $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
                                $employee_count++;
                            }
                        }

                        $content_table = array();
                        $content_table[] = array(($idx += 1), 10, $normalCenterBorder);
                        $content_table[] = array($info["employeeid"], 30, $normalCenterBorder);
                        $content_table[] = array($info["name"], 50, $normalCenterBorder);
                        $content_table[] = array($info["income"]["salary"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["tardy"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["absent"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["basic_pay"], 20, $normalRightBorder);

                        foreach ($inc_income['deminimissList'] as $key => $value) {
                            $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherDeminimissToDisplay"])) ? $info["income"]["totalOtherDeminimissToDisplay"] : 0, 20, $normalRightBorder);

                        foreach ($inc_income['noDeminimissList'] as $key => $value) {
                            $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherIncomeToDisplay"])) ? $info["income"]["totalOtherIncomeToDisplay"] : 0, 20, $normalRightBorder);

                        foreach ($inc_adjustment as $key => $value) {
                            $content_table[] = array((isset($info["income"]["adjustment_list"][$key])) ? $info["income"]["adjustment_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherAdjustmentToDisplay"])) ? $info["income"]["totalOtherAdjustmentToDisplay"] : 0, 20, $normalRightBorder);

                        $content_table[] = array($info["income"]["overtime"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["gross"], 20, $normalRightBorder);

                        $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);

                        foreach ($inc_fixed_deduc as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["fixed_deduc_list"][$key])) ? $info["deduction"]["fixed_deduc_list"][$key] : 0, 20, $normalRightBorder);
                        }

                        foreach ($inc_deduction as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["deduc_list"][$key])) ? $info["deduction"]["deduc_list"][$key] : 0, 20, $normalRightBorder);
                        }

                        foreach ($inc_loan as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["loan_list"][$key])) ? $info["deduction"]["loan_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array($info["deduction"]["totalOtherDeductionToDisplay"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["total_deduction"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["net"], 20, $normalRightBorder);

                        $row = writeTable($sheet, array($content_table), 0, $row);

                        $old_campusid = $info['campusid'];

                    }
                
                    if($sort_key != "name"){
                        $content_table = array();
                        $content_table[] = array("", 10, $boldRightBorder);
                        $content_table[] = array("", 30, $boldRightBorder);
                        $content_table[] = array("Sub Total : ", 50, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["salary"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["tardy"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["absent"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["basic_pay"], 20, $boldRightBorder);

                        foreach ($inc_income['deminimissList'] as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["totalOtherDeminimissToDisplay"])) ? $teaching['summary'][$sort_key][$old_campusid]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_income['noDeminimissList'] as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["totalOtherIncomeToDisplay"])) ? $teaching['summary'][$sort_key][$old_campusid]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_adjustment as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["totalOtherAdjustmentToDisplay"])) ? $teaching['summary'][$sort_key][$old_campusid]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);

                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["overtime"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["gross"], 20, $boldRightBorder);

                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["with_holding_tax"], 20, $boldRightBorder);

                        foreach ($inc_fixed_deduc as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_deduction as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_loan as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? $teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["totalOtherDeductionToDisplay"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["with_holding_tax"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["total_deduction"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key][$old_campusid]["net"], 20, $boldRightBorder);

                        $row = writeTable($sheet, array($content_table), 0, $row);
                    }
                }else if($sort == "department" && $sort_key != "ACAD"){

                    if($sort_key != "name"){
                        writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
                        $row = writeText($sheet, " ". $config['campus'][$sort_key] , $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
                    }

                    foreach ($employees as $key => $info) {
                        if($sort == "department" && $sort_key != "ACAD"){
                            $content_table = array();
                            $content_table[] = array(($idx += 1), 10, $normalCenterBorder);
                            $content_table[] = array($info["employeeid"], 30, $normalCenterBorder);
                            $content_table[] = array(iconv("UTF-8", "ISO-8859-1//IGNORE",$info["name"]), 50, $normalCenterBorder);
                            $content_table[] = array($info["income"]["salary"], 20, $normalRightBorder);
                            $content_table[] = array($info["income"]["tardy"], 20, $normalRightBorder);
                            $content_table[] = array($info["income"]["absent"], 20, $normalRightBorder);
                            $content_table[] = array($info["income"]["basic_pay"], 20, $normalRightBorder);

                            foreach ($inc_income['deminimissList'] as $key => $value) {
                                $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                            }
                            $content_table[] = array((isset($info["income"]["totalOtherDeminimissToDisplay"])) ? $info["income"]["totalOtherDeminimissToDisplay"] : 0, 20, $normalRightBorder);

                            foreach ($inc_income['noDeminimissList'] as $key => $value) {
                                $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                            }
                            $content_table[] = array((isset($info["income"]["totalOtherIncomeToDisplay"])) ? $info["income"]["totalOtherIncomeToDisplay"] : 0, 20, $normalRightBorder);

                            foreach ($inc_adjustment as $key => $value) {
                                $content_table[] = array((isset($info["income"]["adjustment_list"][$key])) ? $info["income"]["adjustment_list"][$key] : 0, 20, $normalRightBorder);
                            }
                            $content_table[] = array((isset($info["income"]["totalOtherAdjustmentToDisplay"])) ? $info["income"]["totalOtherAdjustmentToDisplay"] : 0, 20, $normalRightBorder);

                            $content_table[] = array($info["income"]["overtime"], 20, $normalRightBorder);
                            $content_table[] = array($info["income"]["gross"], 20, $normalRightBorder);

                            $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);

                            foreach ($inc_fixed_deduc as $key => $value) {
                                $content_table[] = array((isset($info["deduction"]["fixed_deduc_list"][$key])) ? $info["deduction"]["fixed_deduc_list"][$key] : 0, 20, $normalRightBorder);
                            }

                            foreach ($inc_deduction as $key => $value) {
                                $content_table[] = array((isset($info["deduction"]["deduc_list"][$key])) ? $info["deduction"]["deduc_list"][$key] : 0, 20, $normalRightBorder);
                            }

                            foreach ($inc_loan as $key => $value) {
                                $content_table[] = array((isset($info["deduction"]["loan_list"][$key])) ? $info["deduction"]["loan_list"][$key] : 0, 20, $normalRightBorder);
                            }
                            $content_table[] = array($info["deduction"]["totalOtherDeductionToDisplay"], 20, $normalRightBorder);
                            $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);
                            $content_table[] = array($info["deduction"]["total_deduction"], 20, $normalRightBorder);
                            $content_table[] = array($info["deduction"]["net"], 20, $normalRightBorder);

                            $row = writeTable($sheet, array($content_table), 0, $row);

                            $old_campusid = $info['campusid'];
                        }
                    }
                    if($sort_key != "name"){
                        $content_table = array();
                        $content_table[] = array("", 10, $boldRightBorder);
                        $content_table[] = array("", 30, $boldRightBorder);
                        $content_table[] = array("Sub Total : ", 50, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['income']["salary"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['income']["tardy"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['income']["absent"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['income']["basic_pay"], 20, $boldRightBorder);

                        foreach ($inc_income['deminimissList'] as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key]['income']["income_list"][$key])) ? $teaching['summary'][$sort_key]['income']["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($teaching['summary'][$sort_key]['income']["totalOtherDeminimissToDisplay"])) ? $teaching['summary'][$sort_key]['income']["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_income['noDeminimissList'] as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key]['income']["income_list"][$key])) ? $teaching['summary'][$sort_key]['income']["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($teaching['summary'][$sort_key]['income']["totalOtherIncomeToDisplay"])) ? $teaching['summary'][$sort_key]['income']["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_adjustment as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key]['income']["adjustment_list"][$key])) ? $teaching['summary'][$sort_key]['income']["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($teaching['summary'][$sort_key]['income']["totalOtherAdjustmentToDisplay"])) ? $teaching['summary'][$sort_key]['income']["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);

                        $content_table[] = array($teaching['summary'][$sort_key]['income']["overtime"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['income']["gross"], 20, $boldRightBorder);

                        $content_table[] = array($teaching['summary'][$sort_key]['deduction']["with_holding_tax"], 20, $boldRightBorder);

                        foreach ($inc_fixed_deduc as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key]['deduction']["fixed_deduc_list"][$key])) ? $teaching['summary'][$sort_key]['deduction']["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_deduction as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key]['deduction']["deduc_list"][$key])) ? $teaching['summary'][$sort_key]['deduction']["deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_loan as $key => $value) {
                            $content_table[] = array((isset($teaching['summary'][$sort_key]['deduction']["loan_list"][$key])) ? $teaching['summary'][$sort_key]['deduction']["loan_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        $content_table[] = array($teaching['summary'][$sort_key]['deduction']["totalOtherDeductionToDisplay"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['deduction']["with_holding_tax"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['deduction']["total_deduction"], 20, $boldRightBorder);
                        $content_table[] = array($teaching['summary'][$sort_key]['deduction']["net"], 20, $boldRightBorder);

                        $row = writeTable($sheet, array($content_table), 0, $row);
                    }
                }
            }
        }
        // grand total income
        // grand total income
        $content_table = array();
        $content_table[] = array("", 10, $boldRightBorder);
        $content_table[] = array("", 30, $boldRightBorder);
        $content_table[] = array("Grand Total : ", 50, $boldRightBorder);
        $content_table[] = array((isset($teaching['grand_total']["income"]["salary"])) ? $teaching['grand_total']["income"]["salary"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($teaching['grand_total']["income"]["tardy"])) ? $teaching['grand_total']["income"]["tardy"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($teaching['grand_total']["income"]["absent"])) ? $teaching['grand_total']["income"]["absent"]: '0', 20, $boldRightBorder);
        $content_table[] = array((isset($teaching['grand_total']["income"]["basic_pay"])) ? $teaching['grand_total']["income"]["basic_pay"] : '0', 20, $boldRightBorder);

        foreach ($inc_income['deminimissList'] as $key => $value) {
            $content_table[] = array((isset($teaching['grand_total']["income"]["income_list"][$key])) ? $teaching['grand_total']["income"]["income_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($teaching['grand_total']["income"]["totalOtherDeminimissToDisplay"])) ? $teaching['grand_total']["income"]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

        foreach ($inc_income['noDeminimissList'] as $key => $value) {
            $content_table[] = array((isset($teaching['grand_total']["income"]["income_list"][$key])) ? $teaching['grand_total']["income"]["income_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($teaching['grand_total']["income"]["totalOtherIncomeToDisplay"])) ? $teaching['grand_total']["income"]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

        foreach ($inc_adjustment as $key => $value) {
            $content_table[] = array((isset($teaching['grand_total']["income"]["adjustment_list"][$key])) ? $teaching['grand_total']["income"]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($teaching['grand_total']["income"]["totalOtherAdjustmentToDisplay"])) ? $teaching['grand_total']["income"]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);   

        $content_table[] = array((isset($teaching['grand_total']["income"]["overtime"])) ? $teaching['grand_total']["income"]["overtime"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($teaching['grand_total']["income"]["gross"])) ? $teaching['grand_total']["income"]["gross"] : '0', 20, $boldRightBorder);

        $content_table[] = array((isset($teaching['grand_total']["deduction"]["with_holding_tax"])) ? $teaching['grand_total']["deduction"]["with_holding_tax"]: '0', 20, $boldRightBorder);

        foreach ($inc_fixed_deduc as $key => $value) {
            $content_table[] = array((isset($teaching['grand_total']["deduction"]["fixed_deduc_list"][$key])) ? $teaching['grand_total']["deduction"]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_deduction as $key => $value) {
            $content_table[] = array((isset($teaching['grand_total']["deduction"]["deduc_list"][$key])) ? $teaching['grand_total']["deduction"]["deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_loan as $key => $value) {
            $content_table[] = array((isset($teaching['grand_total']["deduction"]["loan_list"][$key])) ? $teaching['grand_total']["deduction"]["loan_list"][$key] : 0, 20, $boldRightBorder);
        }

        $content_table[] = array((isset($teaching['grand_total']["deduction"]["total_deduction"])) ? $teaching['grand_total']["deduction"]["total_deduction"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($teaching['grand_total']["deduction"]["net"])) ? $teaching['grand_total']["deduction"]["net"] : '0', 20, $boldRightBorder);

        $row = writeTable($sheet, array($content_table), 0, $row);

   
        $count_income_col = 6 + count($inc_income['deminimissList']) + count($inc_income['noDeminimissList']) + count($inc_adjustment) + 7;
        $count_deduction_col = 3 + count((isset($grand_total["deduction"]["fixed_deduc_list"])) ? $grand_total["deduction"]["fixed_deduc_list"] : '0') + count((isset($grand_total["deduction"]["deduc_list"])) ? $grand_total["deduction"]["deduc_list"]: '0') + count((isset($grand_total["deduction"]["loan_list"])) ? $grand_total["deduction"]["loan_list"] : '0');

        $end_col = ((($count_income_col > $count_deduction_col) ? $count_income_col : $count_deduction_col) + 3) - 1;

        // income
        $income_header = array();
        $income_header[] = array("#", 10, $boldCenterBorder);
        $income_header[] = array("EMPLOYEE ID", 30, $boldCenterBorder);
        $income_header[] = array("EMPLOYEE NAME", 50, $boldCenterBorder);
        $income_header[] = array("SALARY", 20, $boldCenterBorder);
        $income_header[] = array("TARDY", 20, $boldCenterBorder);
        $income_header[] = array("ABSENT", 20, $boldCenterBorder);
        $income_header[] = array("BASIC PAY", 20, $boldCenterBorder);

        foreach ($inc_income['deminimissList'] as $key => $value) {
            $income_header[] = array($config["income"][$key], 20, $boldCenterBorder);
        }
        $income_header[] = array("OTHER DEMINIMISS", 20, $boldCenterBorder);

        foreach ($inc_income['noDeminimissList'] as $key => $value) {
            $income_header[] = array($config["income"][$key], 20, $boldCenterBorder);
        }
        $income_header[] = array("OTHER INCOME", 20, $boldCenterBorder);

        foreach ($inc_adjustment as $key => $value) {
            $income_header[] = array($config["income"][$key]." ADJ", 20, $boldCenterBorder);
        }
        $income_header[] = array("OTHER ADJUSTMENT", 20, $boldCenterBorder);

        $income_header[] = array("OVERTIME", 20, $boldCenterBorder);
        $income_header[] = array("GROSS PAY", 20, $boldCenterBorder);

        $income_header[] = array("WITH HOLDING TAX", 20, $boldCenterBorder);
        
        foreach ($inc_fixed_deduc as $key => $value) {
            $income_header[] = array($key, 20, $boldCenterBorder);
        }
        
        foreach ($inc_deduction as $key => $value) {
            $income_header[] = array($config["deduction"][$key], 20, $boldCenterBorder);
        }
        
        foreach ($inc_loan as $key => $value) {
            $income_header[] = array($config["loan"][$key], 20, $boldCenterBorder);
        }

        $income_header[] = array("OTHER DEDUCTION", 20, $boldCenterBorder);
        $income_header[] = array("WITH HOLDING TAX", 20, $boldCenterBorder);
        $income_header[] = array("TOTAL DEDUCTION", 20, $boldCenterBorder);
        $income_header[] = array("Net", 20, $boldCenterBorder);

        writeBorder($sheet, count($income_header + $income_header), 0, $row, $boldCenterBorder);
        $row = writeText($sheet, "  INCOME AND DEDUCTION", $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
        $row = writeTable($sheet, array($income_header), 0, $row);

        // ksort($emp_list);
        $idx = 0;
        $old_campusid = '';
        $employee_count = 0;
        if(isset($nonteaching)){
            foreach ($nonteaching['emp_list'] as $sort_key => $employees) {
                if($sort == "department" && $sort_key == "ACAD"){
                    if($sort_key != "name"){
                        writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
                        $row = writeText($sheet, " ". $config[$sort][$sort_key] , $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
                    }

                    foreach ($employees as $key => $info) {
                        if($sort == "department" && $sort_key == "ACAD"){
                            if($info['campusid'] != $old_campusid){
                                if($employee_count){
                                    $content_table = array();
                                    $content_table[] = array("", 10, $boldRightBorder);
                                    $content_table[] = array("", 30, $boldRightBorder);
                                    $content_table[] = array("Total : ", 50, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["salary"], 20, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["tardy"], 20, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["absent"], 20, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["basic_pay"], 20, $boldRightBorder);

                                    foreach ($inc_income['deminimissList'] as $key => $value) {
                                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                                    }
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["totalOtherDeminimissToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_campusid]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

                                    foreach ($inc_income['noDeminimissList'] as $key => $value) {
                                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                                    }
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["totalOtherIncomeToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_campusid]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

                                    foreach ($inc_adjustment as $key => $value) {
                                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
                                    }
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["totalOtherAdjustmentToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_campusid]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);

                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["overtime"], 20, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["gross"], 20, $boldRightBorder);

                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["with_holding_tax"], 20, $boldRightBorder);

                                    foreach ($inc_fixed_deduc as $key => $value) {
                                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
                                    }

                                    foreach ($inc_deduction as $key => $value) {
                                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key] : 0, 20, $boldRightBorder);
                                    }

                                    foreach ($inc_loan as $key => $value) {
                                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key] : 0, 20, $boldRightBorder);
                                    }

                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["totalOtherDeductionToDisplay"], 20, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["with_holding_tax"], 20, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["total_deduction"], 20, $boldRightBorder);
                                    $content_table[] = array($nonteaching['summary'][$sort_key][$old_campusid]["net"], 20, $boldRightBorder);

                                    $row = writeTable($sheet, array($content_table), 0, $row);

                                    $employee_count = 0;
                                }
                                writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
                                $row = writeText($sheet, " ". $info['campusid'], $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
                                $employee_count++;
                            }
                        }

                        $content_table = array();
                        $content_table[] = array(($idx += 1), 10, $normalCenterBorder);
                        $content_table[] = array($info["employeeid"], 30, $normalCenterBorder);
                        $content_table[] = array(iconv("UTF-8", "ISO-8859-1//IGNORE",$info["name"]), 50, $normalCenterBorder);
                        $content_table[] = array($info["income"]["salary"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["tardy"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["absent"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["basic_pay"], 20, $normalRightBorder);

                        foreach ($inc_income['deminimissList'] as $key => $value) {
                            $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherDeminimissToDisplay"])) ? $info["income"]["totalOtherDeminimissToDisplay"] : 0, 20, $normalRightBorder);

                        foreach ($inc_income['noDeminimissList'] as $key => $value) {
                            $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherIncomeToDisplay"])) ? $info["income"]["totalOtherIncomeToDisplay"] : 0, 20, $normalRightBorder);

                        foreach ($inc_adjustment as $key => $value) {
                            $content_table[] = array((isset($info["income"]["adjustment_list"][$key])) ? $info["income"]["adjustment_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherAdjustmentToDisplay"])) ? $info["income"]["totalOtherAdjustmentToDisplay"] : 0, 20, $normalRightBorder);

                        $content_table[] = array($info["income"]["overtime"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["gross"], 20, $normalRightBorder);

                        $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);

                        foreach ($inc_fixed_deduc as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["fixed_deduc_list"][$key])) ? $info["deduction"]["fixed_deduc_list"][$key] : 0, 20, $normalRightBorder);
                        }

                        foreach ($inc_deduction as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["deduc_list"][$key])) ? $info["deduction"]["deduc_list"][$key] : 0, 20, $normalRightBorder);
                        }

                        foreach ($inc_loan as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["loan_list"][$key])) ? $info["deduction"]["loan_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array($info["deduction"]["totalOtherDeductionToDisplay"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["total_deduction"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["net"], 20, $normalRightBorder);

                        $row = writeTable($sheet, array($content_table), 0, $row);

                        $old_campusid = $info['campusid'];

                    }
                }else if($sort == "department" && $sort_key != "ACAD"){
                    foreach ($employees as $key => $info) {
                        if($info['campusid'] != $old_campusid){
                            writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
                            $row = writeText($sheet, " ". $config['campus'][$sort_key] , $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
                        }
                        if($info["deptid"] != $old_deptid){
                            $content_table = array();
                            if($employee_count){
                                $content_table[] = array("", 10, $boldRightBorder);
                                $content_table[] = array("", 30, $boldRightBorder);
                                $content_table[] = array("Sub Total : ", 50, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["salary"], 20, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["tardy"], 20, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["absent"], 20, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["basic_pay"], 20, $boldRightBorder);

                                foreach ($inc_income['deminimissList'] as $key => $value) {
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                                }
                                $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherDeminimissToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_deptid]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

                                foreach ($inc_income['noDeminimissList'] as $key => $value) {
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                                }
                                $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherIncomeToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_deptid]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

                                foreach ($inc_adjustment as $key => $value) {
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["adjustment_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
                                }
                                $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherAdjustmentToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_deptid]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);

                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["overtime"], 20, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["gross"], 20, $boldRightBorder);

                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["with_holding_tax"], 20, $boldRightBorder);

                                foreach ($inc_fixed_deduc as $key => $value) {
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["fixed_deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
                                }

                                foreach ($inc_deduction as $key => $value) {
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["deduc_list"][$key] : 0, 20, $boldRightBorder);
                                }

                                foreach ($inc_loan as $key => $value) {
                                    $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["loan_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["loan_list"][$key] : 0, 20, $boldRightBorder);
                                }

                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherDeductionToDisplay"], 20, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["with_holding_tax"], 20, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["total_deduction"], 20, $boldRightBorder);
                                $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["net"], 20, $boldRightBorder);
                                $row = writeTable($sheet, array($content_table), 0, $row);
                                $employee_count = 0;
                            }
                            writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
                            $row = writeText($sheet, " ". $this->extensions->getDepartmentDescription($info['deptid']), $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
                            $employee_count++;
                        }

                        $content_table = array();
                        $content_table[] = array(($idx += 1), 10, $normalCenterBorder);
                        $content_table[] = array($info["employeeid"], 30, $normalCenterBorder);
                        $content_table[] = array(iconv("UTF-8", "ISO-8859-1//IGNORE",$info["name"]), 50, $normalCenterBorder);
                        $content_table[] = array($info["income"]["salary"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["tardy"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["absent"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["basic_pay"], 20, $normalRightBorder);

                        foreach ($inc_income['deminimissList'] as $key => $value) {
                            $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherDeminimissToDisplay"])) ? $info["income"]["totalOtherDeminimissToDisplay"] : 0, 20, $normalRightBorder);

                        foreach ($inc_income['noDeminimissList'] as $key => $value) {
                            $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherIncomeToDisplay"])) ? $info["income"]["totalOtherIncomeToDisplay"] : 0, 20, $normalRightBorder);

                        foreach ($inc_adjustment as $key => $value) {
                            $content_table[] = array((isset($info["income"]["adjustment_list"][$key])) ? $info["income"]["adjustment_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array((isset($info["income"]["totalOtherAdjustmentToDisplay"])) ? $info["income"]["totalOtherAdjustmentToDisplay"] : 0, 20, $normalRightBorder);

                        $content_table[] = array($info["income"]["overtime"], 20, $normalRightBorder);
                        $content_table[] = array($info["income"]["gross"], 20, $normalRightBorder);

                        $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);

                        foreach ($inc_fixed_deduc as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["fixed_deduc_list"][$key])) ? $info["deduction"]["fixed_deduc_list"][$key] : 0, 20, $normalRightBorder);
                        }

                        foreach ($inc_deduction as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["deduc_list"][$key])) ? $info["deduction"]["deduc_list"][$key] : 0, 20, $normalRightBorder);
                        }

                        foreach ($inc_loan as $key => $value) {
                            $content_table[] = array((isset($info["deduction"]["loan_list"][$key])) ? $info["deduction"]["loan_list"][$key] : 0, 20, $normalRightBorder);
                        }
                        $content_table[] = array($info["deduction"]["totalOtherDeductionToDisplay"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["total_deduction"], 20, $normalRightBorder);
                        $content_table[] = array($info["deduction"]["net"], 20, $normalRightBorder);

                        $row = writeTable($sheet, array($content_table), 0, $row);

                        $old_deptid = $info['deptid'];
                        $old_campusid = $info['campusid'];
                    }
                    $content_table = array();
                    if($sort_key != "name"){
                        $content_table[] = array("", 10, $boldRightBorder);
                        $content_table[] = array("", 30, $boldRightBorder);
                        $content_table[] = array("Sub Total : ", 50, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["salary"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["tardy"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["absent"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["basic_pay"], 20, $boldRightBorder);

                        foreach ($inc_income['deminimissList'] as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherDeminimissToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_deptid]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_income['noDeminimissList'] as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherIncomeToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_deptid]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_adjustment as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["adjustment_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherAdjustmentToDisplay"])) ? $nonteaching['summary'][$sort_key][$old_deptid]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);

                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["overtime"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["gross"], 20, $boldRightBorder);

                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["with_holding_tax"], 20, $boldRightBorder);

                        foreach ($inc_fixed_deduc as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["fixed_deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_deduction as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_loan as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$old_deptid]["loan_list"][$key])) ? $nonteaching['summary'][$sort_key][$old_deptid]["loan_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["totalOtherDeductionToDisplay"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["with_holding_tax"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["total_deduction"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$old_deptid]["net"], 20, $boldRightBorder);
                        $row = writeTable($sheet, array($content_table), 0, $row);
                        $employee_count=0;
                    }

                    $content_table = array();
                    if($sort_key != "name"){
                        $content_table[] = array("", 10, $boldRightBorder);
                        $content_table[] = array("", 30, $boldRightBorder);
                        $content_table[] = array("Total : ", 50, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["salary"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["tardy"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["absent"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["basic_pay"], 20, $boldRightBorder);

                        foreach ($inc_income['deminimissList'] as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["totalOtherDeminimissToDisplay"])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_income['noDeminimissList'] as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["totalOtherIncomeToDisplay"])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

                        foreach ($inc_adjustment as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["adjustment_list"][$key])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
                        }
                        $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["totalOtherAdjustmentToDisplay"])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);

                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["overtime"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["gross"], 20, $boldRightBorder);

                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["with_holding_tax"], 20, $boldRightBorder);

                        foreach ($inc_fixed_deduc as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["fixed_deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_deduction as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["deduc_list"][$key])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["deduc_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        foreach ($inc_loan as $key => $value) {
                            $content_table[] = array((isset($nonteaching['summary'][$sort_key][$info['campusid']]["loan_list"][$key])) ? $nonteaching['summary'][$sort_key][$info['campusid']]["loan_list"][$key] : 0, 20, $boldRightBorder);
                        }

                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["totalOtherDeductionToDisplay"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["with_holding_tax"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["total_deduction"], 20, $boldRightBorder);
                        $content_table[] = array($nonteaching['summary'][$sort_key][$info['campusid']]["net"], 20, $boldRightBorder);
                        $row = writeTable($sheet, array($content_table), 0, $row);
                        $employee_count=0;
                    }
                }
            }
        }
        // grand total income
        $content_table = array();
        $content_table[] = array("", 10, $boldRightBorder);
        $content_table[] = array("", 30, $boldRightBorder);
        $content_table[] = array("Grand Total : ", 50, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["salary"])) ? $nonteaching['grand_total']['nonteaching']["income"]["salary"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["tardy"])) ? $nonteaching['grand_total']['nonteaching']["income"]["tardy"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["absent"])) ? $nonteaching['grand_total']['nonteaching']["income"]["absent"]: '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["basic_pay"])) ? $nonteaching['grand_total']['nonteaching']["income"]["basic_pay"] : '0', 20, $boldRightBorder);

        foreach ($inc_income['deminimissList'] as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key])) ? $nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["totalOtherDeminimissToDisplay"])) ? $nonteaching['grand_total']['nonteaching']["income"]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

        foreach ($inc_income['noDeminimissList'] as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key])) ? $nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["totalOtherIncomeToDisplay"])) ? $nonteaching['grand_total']['nonteaching']["income"]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

        foreach ($inc_adjustment as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["adjustment_list"][$key])) ? $nonteaching['grand_total']['nonteaching']["income"]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["totalOtherAdjustmentToDisplay"])) ? $nonteaching['grand_total']['nonteaching']["income"]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);   

        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["overtime"])) ? $nonteaching['grand_total']['nonteaching']["income"]["overtime"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["income"]["gross"])) ? $nonteaching['grand_total']['nonteaching']["income"]["gross"] : '0', 20, $boldRightBorder);

        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["deduction"]["with_holding_tax"])) ? $nonteaching['grand_total']['nonteaching']["deduction"]["with_holding_tax"]: '0', 20, $boldRightBorder);

        foreach ($inc_fixed_deduc as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["deduction"]["fixed_deduc_list"][$key])) ? $nonteaching['grand_total']['nonteaching']["deduction"]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_deduction as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["deduction"]["deduc_list"][$key])) ? $nonteaching['grand_total']['nonteaching']["deduction"]["deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_loan as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["deduction"]["loan_list"][$key])) ? $nonteaching['grand_total']['nonteaching']["deduction"]["loan_list"][$key] : 0, 20, $boldRightBorder);
        }

        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["deduction"]["total_deduction"])) ? $nonteaching['grand_total']['nonteaching']["deduction"]["total_deduction"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']['nonteaching']["deduction"]["net"])) ? $nonteaching['grand_total']['nonteaching']["deduction"]["net"] : '0', 20, $boldRightBorder);

        $row = writeTable($sheet, array($content_table), 0, $row);


        // grand total income
        $content_table = array();
        $content_table[] = array("", 10, $boldRightBorder);
        $content_table[] = array("", 30, $boldRightBorder);
        $content_table[] = array("Payroll Register Total : ", 50, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']["income"]["salary"])) ? $nonteaching['grand_total']["income"]["salary"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']["income"]["tardy"])) ? $nonteaching['grand_total']["income"]["tardy"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']["income"]["absent"])) ? $nonteaching['grand_total']["income"]["absent"]: '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']["income"]["basic_pay"])) ? $nonteaching['grand_total']["income"]["basic_pay"] : '0', 20, $boldRightBorder);

        foreach ($inc_income['deminimissList'] as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']["income"]["income_list"][$key])) ? $nonteaching['grand_total']["income"]["income_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($nonteaching['grand_total']["income"]["totalOtherDeminimissToDisplay"])) ? $nonteaching['grand_total']["income"]["totalOtherDeminimissToDisplay"] : 0, 20, $boldRightBorder);

        foreach ($inc_income['noDeminimissList'] as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']["income"]["income_list"][$key])) ? $nonteaching['grand_total']["income"]["income_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($nonteaching['grand_total']["income"]["totalOtherIncomeToDisplay"])) ? $nonteaching['grand_total']["income"]["totalOtherIncomeToDisplay"] : 0, 20, $boldRightBorder);

        foreach ($inc_adjustment as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']["income"]["adjustment_list"][$key])) ? $nonteaching['grand_total']["income"]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
        }
            $content_table[] = array((isset($nonteaching['grand_total']["income"]["totalOtherAdjustmentToDisplay"])) ? $nonteaching['grand_total']["income"]["totalOtherAdjustmentToDisplay"] : 0, 20, $boldRightBorder);   

        $content_table[] = array((isset($nonteaching['grand_total']["income"]["overtime"])) ? $nonteaching['grand_total']["income"]["overtime"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']["income"]["gross"])) ? $nonteaching['grand_total']["income"]["gross"] : '0', 20, $boldRightBorder);

        $content_table[] = array((isset($nonteaching['grand_total']["deduction"]["with_holding_tax"])) ? $nonteaching['grand_total']["deduction"]["with_holding_tax"]: '0', 20, $boldRightBorder);

        foreach ($inc_fixed_deduc as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']["deduction"]["fixed_deduc_list"][$key])) ? $nonteaching['grand_total']["deduction"]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_deduction as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']["deduction"]["deduc_list"][$key])) ? $nonteaching['grand_total']["deduction"]["deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_loan as $key => $value) {
            $content_table[] = array((isset($nonteaching['grand_total']["deduction"]["loan_list"][$key])) ? $nonteaching['grand_total']["deduction"]["loan_list"][$key] : 0, 20, $boldRightBorder);
        }

        $content_table[] = array((isset($nonteaching['grand_total']["deduction"]["total_deduction"])) ? $nonteaching['grand_total']["deduction"]["total_deduction"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($nonteaching['grand_total']["deduction"]["net"])) ? $nonteaching['grand_total']["deduction"]["net"] : '0', 20, $boldRightBorder);

        $row = writeTable($sheet, array($content_table), 0, $row);

    # ======================================================== SUMMARY  ========================================================
    else:
        // header
        $row = $col = 0;
        
        $count_income_col = 5 + count($inc_income) + count($inc_adjustment);
        $count_deduction_col = 3 + count((isset($grand_total["deduction"]["fixed_deduc_list"])) ? $grand_total["deduction"]["fixed_deduc_list"] : '0') + count((isset($grand_total["deduction"]["deduc_list"])) ? $grand_total["deduction"]["deduc_list"]: '0') + count((isset($grand_total["deduction"]["loan_list"])) ? $grand_total["deduction"]["loan_list"] : '0');

        $end_col = ((($count_income_col > $count_deduction_col) ? $count_income_col : $count_deduction_col) + 2) - 1;

        $row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, $SCHOOL_NAME, $bigboldcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, $SCHOOL_CAPTION, $normalcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, "PAYROLL SHEET FOR SALARY SCHEDULE : ". $sched_display, $bigboldcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, "", $normalcenter, $row, $col, 0, $end_col);
        $row = writeText($sheet, "", $bigboldcenter, $row, $col, 0, $end_col);
        
        $bitmap = "images/school_logo_bm.bmp";
        $sheet->insertBitmap( 0 , 1 , $bitmap , 0 , 3 , .15 ,.45 );

        // income
        $income_header = array();
        $income_header[] = array("#", 10, $boldCenterBorder);
        $income_header[] = array(($sort == "name") ? "EMPLOYEE" : strtoupper($sort), 50, $boldCenterBorder);
        $income_header[] = array("SALARY", 20, $boldCenterBorder);
        $income_header[] = array("TARDY", 20, $boldCenterBorder);
        $income_header[] = array("ABSENT", 20, $boldCenterBorder);
        $income_header[] = array("BASIC PAY", 20, $boldCenterBorder);

        foreach ($inc_income as $key => $value) {
            $income_header[] = array($config["income"][$key], 20, $boldCenterBorder);
        }

        foreach ($inc_adjustment as $key => $value) {
            $income_header[] = array($config["income"][$key]." ADJ", 20, $boldCenterBorder);
        }

        $income_header[] = array("OVERTIME", 20, $boldCenterBorder);
        $income_header[] = array("GROSS PAY", 20, $boldCenterBorder);

        writeBorder($sheet, count($income_header), 0, $row, $boldCenterBorder);
        $row = writeText($sheet, "  INCOME AND DEDUCTION", $boldLeftBorder, $row, $col, 0, (count($income_header) - 1));
        $row = writeTable($sheet, array($income_header), 0, $row);

        ksort($summary);
        $idx = 1;
        foreach ($summary as $sort_key => $info) {
            $content_table = array();
            $content_table[] = array($idx, 10, $normalCenterBorder);
            $content_table[] = array($config[$sort][$sort_key], 50, $normalCenterBorder);
            $content_table[] = array($info["income"]["salary"], 20, $normalRightBorder);
            $content_table[] = array($info["income"]["tardy"], 20, $normalRightBorder);
            $content_table[] = array($info["income"]["absent"], 20, $normalRightBorder);
            $content_table[] = array($info["income"]["basic_pay"], 20, $normalRightBorder);

            foreach ($inc_income as $key => $value) {
                $content_table[] = array((isset($info["income"]["income_list"][$key])) ? $info["income"]["income_list"][$key] : 0, 20, $normalRightBorder);
            }

            foreach ($inc_adjustment as $key => $value) {
                $content_table[] = array((isset($info["income"]["adjustment_list"][$key])) ? $info["income"]["adjustment_list"][$key] : 0, 20, $normalRightBorder);
            }

            $content_table[] = array($info["income"]["overtime"], 20, $normalRightBorder);
            $content_table[] = array($info["income"]["gross"], 20, $normalRightBorder);

            $row = writeTable($sheet, array($content_table), 0, $row);
            $idx += 1;
        }

        // grand total income
        $content_table = array();
        $content_table[] = array("", 10, $boldRightBorder);
        $content_table[] = array("Grand Total : ", 50, $boldRightBorder);
        $content_table[] = array((isset($grand_total["income"]["salary"])) ? $grand_total["income"]["salary"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($grand_total["income"]["tardy"])) ? $grand_total["income"]["tardy"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($grand_total["income"]["absent"])) ? $grand_total["income"]["absent"]: '0', 20, $boldRightBorder);
        $content_table[] = array((isset($grand_total["income"]["basic_pay"])) ? $grand_total["income"]["basic_pay"] : '0', 20, $boldRightBorder);

        foreach ($inc_income as $key => $value) {
            $content_table[] = array((isset($grand_total["income"]["income_list"][$key])) ? $grand_total["income"]["income_list"][$key] : 0, 20, $boldRightBorder);                    
        }

        foreach ($inc_adjustment as $key => $value) {
            $content_table[] = array((isset($grand_total["income"]["adjustment_list"][$key])) ? $grand_total["income"]["adjustment_list"][$key] : 0, 20, $boldRightBorder);                    
        }

        $content_table[] = array((isset($grand_total["income"]["overtime"])) ? $grand_total["income"]["overtime"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($grand_total["income"]["gross"])) ? $grand_total["income"]["gross"] : '0', 20, $boldRightBorder);

        $row = writeTable($sheet, array($content_table), 0, $row);

        // deduction header
        $row += 2;
        $deduction_header = array();
        $deduction_header[] = array("#", 10, $boldCenterBorder);
        $deduction_header[] = array(($sort == "name") ? "EMPLOYEE" : strtoupper($sort), 50, $boldCenterBorder);
        $deduction_header[] = array("WITH HOLDING TAX", 20, $boldCenterBorder);
        
        foreach ($inc_fixed_deduc as $key => $value) {
            $deduction_header[] = array($key, 20, $boldCenterBorder);
        }
        
        foreach ($inc_deduction as $key => $value) {
            $deduction_header[] = array($config["deduction"][$key], 20, $boldCenterBorder);
        }
        
        foreach ($inc_loan as $key => $value) {
            $deduction_header[] = array($config["loan"][$key], 20, $boldCenterBorder);
        }

        $deduction_header[] = array("Total Deduction", 20, $boldCenterBorder);
        $deduction_header[] = array("Net", 20, $boldCenterBorder);

        writeBorder($sheet, count($deduction_header), 0, $row, $boldCenterBorder);
        $row = writeText($sheet, "  Deduction", $boldLeftBorder, $row, $col, 0, (count($deduction_header) - 1));
        $row = writeTable($sheet, array($deduction_header), 0, $row);

        $idx = 1;
        foreach ($summary as $sort_key => $info) {
            $content_table = array();
            $content_table[] = array($idx, 10, $normalCenterBorder);
            $content_table[] = array($config[$sort][$sort_key], 50, $normalCenterBorder);
            $content_table[] = array($info["deduction"]["with_holding_tax"], 20, $normalRightBorder);

            foreach ($inc_fixed_deduc as $key => $value) {
                $content_table[] = array((isset($info["deduction"]["fixed_deduc_list"][$key])) ? $info["deduction"]["fixed_deduc_list"][$key] : 0, 20, $normalRightBorder);
            }

            foreach ($inc_deduction as $key => $value) {
                $content_table[] = array((isset($info["deduction"]["deduc_list"][$key])) ? $info["deduction"]["deduc_list"][$key] : 0, 20, $normalRightBorder);
            }

            foreach ($inc_loan as $key => $value) {
                $content_table[] = array((isset($info["deduction"]["loan_list"][$key])) ? $info["deduction"]["loan_list"][$key] : 0, 20, $normalRightBorder);
            }

            $content_table[] = array($info["deduction"]["total_deduction"], 20, $normalRightBorder);
            $content_table[] = array($info["deduction"]["net"], 20, $normalRightBorder);

            $row = writeTable($sheet, array($content_table), 0, $row);
            $idx += 1;
        }

        // grand total for deduction
        $content_table = array();
        $content_table[] = array("", 10, $boldRightBorder);
        $content_table[] = array("Grand Total : ", 50, $boldRightBorder);
        $content_table[] = array((isset($grand_total["deduction"]["with_holding_tax"])) ? $grand_total["deduction"]["with_holding_tax"]: '0', 20, $boldRightBorder);

        foreach ($inc_fixed_deduc as $key => $value) {
            $content_table[] = array((isset($grand_total["deduction"]["fixed_deduc_list"][$key])) ? $grand_total["deduction"]["fixed_deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_deduction as $key => $value) {
            $content_table[] = array((isset($grand_total["deduction"]["deduc_list"][$key])) ? $grand_total["deduction"]["deduc_list"][$key] : 0, 20, $boldRightBorder);
        }

        foreach ($inc_loan as $key => $value) {
            $content_table[] = array((isset($grand_total["deduction"]["loan_list"][$key])) ? $grand_total["deduction"]["loan_list"][$key] : 0, 20, $boldRightBorder);
        }

        $content_table[] = array((isset($grand_total["deduction"]["total_deduction"])) ? $grand_total["deduction"]["total_deduction"] : '0', 20, $boldRightBorder);
        $content_table[] = array((isset($grand_total["deduction"]["net"])) ? $grand_total["deduction"]["net"] : '0', 20, $boldRightBorder);

        $row = writeTable($sheet, array($content_table), 0, $row);
    endif;

    $xls->close();
?>

