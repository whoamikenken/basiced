<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_ extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -  
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */

    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
        $this->load->library('form_data_encryption');
    }
    
    public function index()
    {
        # nothing
    }
    function reportloader(){
        $this->load->model("attendance");
        $formdata = $this->input->post("formdata");
        
        $formdata = base64_decode(urldecode($formdata));
        $data = Globals::convertFormDataToArray($formdata);
        $toks = $data['toks'];
        if($toks){
            foreach ($data as $key => $value) {
                if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
            }
        }
        // echo "<pre>";print_r($view);die;
        $view = $toks ? $data['view'] : $this->input->post("view");
        ## employeeid
         // echo "<pre>";print_r($view);die;
        $this->load->view($view, $data);
    }
    /*
     * Load File PDF Folder 
     */
    function reportconfig(){
        $data = $this->input->post();  
        
        if(isset($data["toks"])){
            $toks = $data["toks"];
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
        $report = $data["report"];  
        $data['monthlist'] = $this->payrolloptions->monthname();
        $data['yearlist']  = $this->payrolloptions->periodyear();

        $this->load->view("forms_pdf/$report",$data); 
    }
    
    function loadHRReport(){
        if($this->input->get()) exit('No direct script access allowed');
        $this->load->model('service_credit');
        $this->load->model('ob_application');
        $data = array();
        $this->load->model('leave_application');
        $leavetype = '';
        $reportname = $this->input->get('form');
        $reportformat = $this->input->get('reportformat');
        #echo "<pre>"; print_r($this->input->get()); echo "</pre>"; die;
        if($reportname == 'leavereport'){

            $dfrom      = $this->input->get('dfrom');
            $dto        = $this->input->get('dto');
            $type       = $this->input->get('type');
            if($this->input->get('type')){
                $leavetype = implode(',', $type);
            }

            ///< get necessary data here
            $data['leave']['list'] = $this->leave_application->getLeaveHistory(false,$leavetype,'',$dfrom,$dto,'','',"ORDER BY lname,a.leavetype,fromdate",true);
            $data['service_credit']['list'] = $this->service_credit->getServiceCreditHistory($dfrom,$dto);
            $data['ob_app']['list'] = $this->ob_application->getObAppHistory($dfrom,$dto);
            // echo "<pre>"; print_r($data); echo "</pre>"; die;

            if(!in_array("SC", $type)) $data['service_credit']['list'] = array();
            if(!in_array("DA", $type)) $data['ob_app']['list'] = array();
            if($reportformat == 'xls'){
                $this->load->view('reports_excel/leavereport',$data);
            }else{
                $this->load->view('forms_pdf/leavereport',$data);
            }
        }

    }

    function loadPayrollReportSetup(){
        $data = array();
        $toks = $this->input->post("toks");
        $data['reportname'] = ($toks) ? $this->gibberish->decrypt($this->input->post('reportname'), $toks) : $this->input->post('reportname');
        $data['title'] = ($toks) ? $this->gibberish->decrypt($this->input->post('title'), $toks) : $this->input->post('title');
        $data['reports_wd_format_filter'] = array('netPayHistory');
        

        switch ($data['reportname']) {
            case 'netPayHistory': 
            case 'philhealthFileGenerator':
                $data['monthlist'] = $this->payrolloptions->monthname();
                $data['yearlist']  = $this->payrolloptions->periodyear();
                $this->load->view('payroll/setup/report_setup',$data);
                break;
            case 'pagibigFileWriter':
                #echo "<strong>OOPS!.. <br> This page is still working.. Press [Esc] to return..</strong>"; return;
                $data['cutofflist'] = $this->payrolloptions->displaypayrollcutoff();
                $data['banklist'] = $this->payrolloptions->getBankListSelect();
                $this->load->view('payroll/setup/report_setup',$data);
                break;
            default:
                # code...
                break;
        }
    }

    function loadPayrollReport(){
        $data = array();
        $this->load->model("payrollreport");

        $reportname = ($this->input->get('reportname')) ? $this->input->get('reportname') : $this->input->post('reportname');
        $reportformat = ($this->input->get('reportformat')) ? $this->input->get('reportformat') : $this->input->post('reportformat');

        switch ($reportname) {
            case 'netPayHistory':

                $month      = $this->input->post('period');
                $pyear      = $this->input->post('pyear');
                $status     = $this->input->post('status');
                $sort       = $this->input->post('sort');

                ///< get necessary data here
                $this->load->model('utils');
                $data = $this->payrollreport->getNetPayHistory($month,$pyear,$status);
                $data["emplist"] = $this->payrollreport->getNetPayHistoryNew($month,$pyear,$status,$sort);
                $data['deptlist'] = $this->utils->getDepartments();
                $data["sort"] = $sort;
                ksort($data['deptlist']);
                $data['employer_info'] = $this->utils->getEmployerInfo(array('SCHOOL_NAME','SCHOOL_CAPTION'));
                $data['month'] = $month;
                $data['pyear'] = $pyear;
                if($reportformat == 'xls') $this->load->view('reports_excel/netpay_history',$data);
                else                       $this->load->view('forms_pdf/netpay_history',$data);

                break;

            case 'pagibigFileWriter':

                $cutoff         = $this->input->get('cutoff');
                $bank           = $this->input->get('bank');

                $dates = explode(' ',$cutoff);
                if(isset($dates[0]) && isset($dates[1])){
                    $sdate = $dates[0];
                    $edate = $dates[1];
                }else{
                    echo 'Invalid Cutoff';
                    return;
                }

                $this->load->model('utils');

                $data['contri_list'] = $this->payrollreport->getEmployeeFixedContriHistory('PAGIBIG',$sdate,$edate,$bank);
                $data['employer_info'] = $this->utils->getEmployerInfo(array('SCHOOL_NAME','ADDRESS','ZIP_CODE','EMPLOYER_TYPE','CONTACT_NO','BR_CODE','PAGIBIG_ID'));
                $data['sdate'] = $sdate;
                $data['edate'] = $edate;
                $data['bank'] = $bank;
                $data['code_deduction'] = 'PAGIBIG';

                $this->load->view('reports_excel/pagibig_file_writer',$data);

                break;

            case 'philhealthFileGenerator':

                $month      = $this->input->get('month');
                $pyear      = $this->input->get('pyear');

                $this->load->model('payrollprocess');

                $data['contriList'] = $this->payrollreport->getEmployeePhilhealthContri($month,$pyear);
                
                $this->load->view('reports_excel/philhealth_file_generator',$data);
                
                break;  
            
            default:
                # code...
                break;
        }

        
    }

    # for ica-hyperion 21655
    # by justin (with e)
    function loadRDCForms(){
        if($this->input->get()) exit("No direct script access allowed");
        extract($this->input->post());
        $division="";
        $sd_filter="detailed";
        $exp_cutoff     = explode("~~", $cutoff);
        $month          = date("m", strtotime($exp_cutoff[0]));
        $year           = $exp_cutoff[1];
        $data = $emp_list = $cutoff_list = $key_list = $inc_campus = array();

        $q_emplist = $this->reports->getRDCEmpList($division, $department, $year ."-". $month, $status, $office, $tnt);
 
        foreach ($q_emplist as $row) {
            $sort_key = "name";
            if($sort == "department"){
                if($row->dept_code == "ACAD") $sort_key = ($row->dept_desc) ? $row->dept_code : "";
                else $sort_key = ($row->campus_desc) ? $row->campus_code : "";
            }
            if($sort == "campus")     $sort_key = ($row->campus_desc) ? $row->campus_code : "";

            if($sort == "name") $key["name"] = "ALL EMPLOYEE";
            if($sort == "department"){
                $key_list[$row->dept_code] = ($row->dept_code) ? $row->dept_desc : "-NO DEPARTMENT-";
                // $key_list[$row->campus_code] = ($row->campus_code) ? $row->campus_desc : "-NO CAMPUS-";
            }
            if($sort == "campus") $key_list[$row->campus_code] = ($row->campus_code) ? $row->campus_desc : "-NO CAMPUS-";

            $tin = "";
            $gross_basic_amount = ($row->gross) ? $row->gross : 0;
            switch ($deduction) {
                case 'PHILHEALTH':
                    $gross_basic_amount = ($row->salary) ? $row->salary : 0;
                    $tin = $row->emp_philhealth;
                    break;

                case 'PAGIBIG':
                    $tin = $row->emp_pagibig;
                    break;

                case 'PERAA':
                    $gross_basic_amount = ($row->netbasicpay) ? $row->netbasicpay : 0;
                    $tin = $row->emp_peraa;
                    break;
                
                default:
                    $tin = $row->emp_sss;
                    break;
            }

            $ee = $er = $ec = $provident_er = $total_fixed_deduction = 0;
            $q_ee_er = $this->reports->findRDCEEER($row->id, $deduction);
            foreach ($q_ee_er as $row_ee_er) {
                $ee = $row_ee_er->EE;
                if($deduction != "PERAA") $er = $row_ee_er->ER;
                else $er = $row_ee_er->EE;
                $ec = $row_ee_er->EC;
                $provident_er = $row_ee_er->provident_er;
                $total_fixed_deduction = $ee + $er + $ec;
            }

            $gb_key = date("d", strtotime($row->cutoffstart)) ."-". date("d", strtotime($row->cutoffend));

            if(!in_array($gb_key, $cutoff_list)) $cutoff_list[] = $gb_key; 
            $emp_list[$sort_key][$row->employeeid]["name"] = $row->fullname;
            $emp_list[$sort_key][$row->employeeid]["campus"] = $row->campus_code;
            $emp_list[$sort_key][$row->employeeid]["deptid"] = $row->dept_code;
            $emp_list[$sort_key][$row->employeeid]["tin_num"] = $tin;
            $emp_list[$sort_key][$row->employeeid]["gb_amount"][$gb_key] = $gross_basic_amount;

            $emp_list[$sort_key][$row->employeeid]["gb_total"] = 0;
            foreach ($emp_list[$sort_key][$row->employeeid]["gb_amount"] as $key => $value) $emp_list[$sort_key][$row->employeeid]["gb_total"] += $value;

            $emp_list[$sort_key][$row->employeeid]["ee"][$gb_key] = $ee;
            $emp_list[$sort_key][$row->employeeid]["er"][$gb_key] = $er + $provident_er;
            $emp_list[$sort_key][$row->employeeid]["ec"][$gb_key] = $ec;
            $emp_list[$sort_key][$row->employeeid]["total_fixed_deduction"][$gb_key] = $total_fixed_deduction;

            $inc_campus[$row->campus_code] = ($row->campus_code) ? $row->campus_desc : "-NO CAMPUS-";
        }

        asort($inc_campus);
        $data["include_campus"] = $inc_campus;
        $data["cutoff"]         = $cutoff;  
        $data["deduction"]      = $deduction;
        $data["cutoff_list"]    = $cutoff_list;
        $data["sort"]           = $sort;
        $data["sd_filter"]      = $sd_filter;
        $data["gb_display"]     = ($deduction != "PHILHEALTH") ? "Basic Pay" : "Basic Pay";
        $data["key_list"]       = $key_list;
        $data["emp_list"]       = $emp_list;
        $data["summary"]        = $this->rdcFormSummary($emp_list);
        $data["perdept_summary"]        = $this->rdcFormPerdeptSummary($emp_list);

        $this->load->library('PdfCreator_mpdf');
        $this->load->library('lib_includer');
        $this->lib_includer->load("excel/Writer");

        // echo "<pre>"; print_r($data); die;
        if($format == 'PDF') $this->load->view('forms_pdf/rdc_form',$data);
        else                 $this->load->view('reports_excel/rdc_form',$data);
    }

    function rdcFormSummary($emp_list){
        $data = array();
        $ee_ec_er = 0;
        foreach ($emp_list as $key => $employee) {
            
            $page_gb = $page_ee = $page_ec = $page_er = $page_fixed_deduction = 0;
            foreach ($employee as $empid => $info) {
                foreach ($info['ee'] as $m_key => $amount) $ee_ec_er += $amount;
                foreach ($info['ec'] as $m_key => $amount) $ee_ec_er += $amount;
                foreach ($info['er'] as $m_key => $amount) $ee_ec_er += $amount;
                if($ee_ec_er){
                    $page_gb += $info['gb_total'];

                    foreach ($info['ee'] as $m_key => $amount) $page_ee += $amount;
                    foreach ($info['ec'] as $m_key => $amount) $page_ec += $amount;
                    foreach ($info['er'] as $m_key => $amount) $page_er += $amount;
                    foreach ($info['total_fixed_deduction'] as $m_key => $amount) $page_fixed_deduction += $amount;
                }

                $ee_ec_er = 0;
            }

            $data[$key] = array(
                "description"   => ($key == "name") ? "ALL EMPLOYEE" : $key,
                "gb_amount"     => $page_gb,
                "ee_amount"     => $page_ee,
                "ec_amount"     => $page_ec,
                "er_amount"     => $page_er,
                "total_fixed_deduction"     => $page_fixed_deduction
            );
        }

        return $data;
    }
    # end for ica-hyperion 21655

    function rdcFormPerdeptSummary($emp_list){
        $data = array();
        $converted_emplist = array();
        $old_campus = '';
        foreach($emp_list as $dept => $emp_data){
            foreach($emp_data as $empid => $emp_info){
                if($emp_info['campus'] != $old_campus){
                    $converted_emplist[$emp_info['campus']][$empid] = $emp_info;
                }else{
                    $converted_emplist[$emp_info['campus']][$empid] = $emp_info;
                }

                $old_campus = $emp_info['campus'];
            }
        }


        foreach ($converted_emplist as $key => $employee) {
            
            $page_gb = $page_ee = $page_ec = $page_er = $page_fixed_deduction = 0;
            foreach ($employee as $empid => $info) {
                
                $page_gb += $info['gb_total'];

                foreach ($info['ee'] as $m_key => $amount) $page_ee += $amount;
                foreach ($info['ec'] as $m_key => $amount) $page_ec += $amount;
                foreach ($info['er'] as $m_key => $amount) $page_er += $amount;
                foreach ($info['total_fixed_deduction'] as $m_key => $amount) $page_fixed_deduction += $amount;
            }

            $data[$key] = array(
                "description"   => ($key == "name") ? "ALL EMPLOYEE" : $key,
                "gb_amount"     => $page_gb,
                "ee_amount"     => $page_ee,
                "ec_amount"     => $page_ec,
                "er_amount"     => $page_er,
                "total_fixed_deduction"     => $page_fixed_deduction
            );
        }
        
        return $data;
    }


    # for ica-hyperion 21671
    function loadOtherReport(){
        $toks = $this->input->post("toks");
        $data = $this->input->post();
        foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
        $this->load->model("extras");
        $this->load->model("employee");
        $this->load->model("payrolloptions");

        $this->load->view("payroll/other_report", $data);
    }

    function loadEmployee(){
        $toks = $this->input->post("toks");
       $data['deptid'] = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) : $this->input->post("deptid");
       $data['estatus'] = $toks ? $this->gibberish->decrypt( $this->input->post("estatus"), $toks ) : $this->input->post("estatus");
       $data['etype'] = $toks ? $this->gibberish->decrypt( $this->input->post("etype"), $toks ) : $this->input->post("etype");
       $data['campusid'] = $toks ? $this->gibberish->decrypt( $this->input->post("campusid"), $toks ) : $this->input->post("campusid");
       $data['officeid'] = $toks ? $this->gibberish->decrypt( $this->input->post("officeid"), $toks ) : $this->input->post("officeid");
       $data['isactive'] = $toks ? $this->gibberish->decrypt( $this->input->post("isactive"), $toks ) : $this->input->post("isactive");
       echo $this->employee->showempnores($data['deptid'],$data['estatus'],$data['etype'],$data['campusid'],$data['officeid'],$data['isactive'] );
    }

    function displayPayrollRegistrarReportTesting(){
        $data = $this->input->get();
        $selected_income = $selected_adjustment = $selected_deduction = array();
        if(isset($data['income'])) $selected_income = array_flip($data['income']);
        if(isset($data['adjustment'])) $selected_adjustment = array_flip($data['adjustment']);
        if(isset($data['deduction'])) $selected_deduction = array_flip($data['deduction']);

        $selected_deminimiss = array_intersect_key($selected_income, $this->extensions->getDeminimissIncomeKeys());
        $notselected_deminimiss = array_diff_key($this->extensions->getDeminimissIncomeKeys(), $selected_income);

        $selected_nondeminimiss = array_intersect_key($selected_income, $this->extensions->getNonDeminimissIncomeKeys());
        $notselected_nondeminimiss = array_diff_key($this->extensions->getNonDeminimissIncomeKeys(), $selected_income);

        if(isset($data['deduction'])) if(in_array("selectalldeduction", $data['deduction'])) $selected_deduction = $this->extensions->getDeductioConfignKeys();

        /*for filter history*/
        $save_adjustment_history = $this->reports->save_payrollregister_filter($selected_adjustment, "adjustment");
        $save_adjustment_history = $this->reports->save_payrollregister_filter($selected_deduction, "deduction");
        $save_history = $this->reports->save_payrollregister_filter($data['selectIncome'], $data['demchoices']);
        /*end*/
        
    }

    function displayPayrollRegistrarReport(){
       $this->load->model("payroll");
       $this->load->model("reports");
        $data = $this->input->post();
        $deminimissIncome = array();
        $notDeminimissIncome = array();
        $adjustmentIncome = array();
        $deductionCategory = array();
        $incomeToDisplay = array();
        $emp_deduction = array();
        $deductionToDisplay = array();
        $adjustmentToDisplay = array();
        $allIncome = array();
        $deminimissList = array();
        $noDeminimissList = array();
        $data['selectIncome'] = array();
        $totalOtherDeminimissToDisplay = '';
        $isAllDeminimiss = $data['demchoices'];
        $income_category = $this->reports->getIncomeDeminimiss();
        $notDeminimissToDisplay = array();
        $deminimissToDisplay = array();
        if(array_key_exists("income", $data)){
            if(in_array("selectalldeminimis", $data['income'])){
                foreach($income_category as $value){
                    $deminimissIncome[$value->id] = $value->description;
                }
            }else{
                foreach($income_category as $value){
                    $deminimissIncome[$value->id] = $value->description;
                }
            }
            /*remove selectalldeminimiss value in array*/
            $key = array_search("selectalldeminimis", $data['income']);

            if($key){
                unset($data['income'][$key]);
            }

            /*all selected income of user*/
            $data['income'] = array_flip($data['income']);    
            if($isAllDeminimiss == "yes"){
                $data['selectIncome'] = $data['income'] + $deminimissIncome;
            }else{
                $data['selectIncome'] = $data['income'];
            }
             /*get not deminimiss income*/
            $other_income_category['other_income_category'] = $this->reports->getOtherIncome();
            foreach($other_income_category['other_income_category'] as $value){
                $notDeminimissIncome[$value->id] = $value->description;
            }

             /*get all income*/
            $all_income_category['all_income_category'] = $this->reports->getIncome();
            foreach($all_income_category['all_income_category'] as $value){
                $allIncome[$value->id] = $value->description;
            }

        }
        else{ /*if no selected income*/
        /*get not deminimiss income*/
            $income_category['income_category'] = $this->reports->getIncomeDeminimiss();
            foreach($income_category['income_category'] as $value){
                $deminimissIncome[$value->id] = $value->description;
            }

             /*get not deminimiss income*/
            $other_income_category['other_income_category'] = $this->reports->getOtherIncome();
            foreach($other_income_category['other_income_category'] as $value){
                $notDeminimissIncome[$value->id] = $value->description;
            }

             /*get all income*/
            $all_income_category['all_income_category'] = $this->reports->getIncome();
            foreach($all_income_category['all_income_category'] as $value){
                $allIncome[$value->id] = $value->description;
            }
        }


        /*============== for deduction ==================*/
        if(array_key_exists("deduction", $data)){
            if(in_array("selectalldeduction", $data['deduction'])){
                $deduction_category['deduction_category'] = $this->reports->getDeductionConfig();
                foreach($deduction_category['deduction_category'] as $value){
                    $deductionCategory[$value->id] = $value->description;
                }
            }else{
                $deductionCategory = array_flip($data['deduction']);
            }
        }
            
        /*============== for adjustment ==================*/
        if(array_key_exists("adjustment", $data)){
            $adjustmentIncome = array_flip($data['adjustment']);
        }


        /*for filter history*/
        $save_adjustment_history = $this->reports->save_payrollregister_filter($adjustmentIncome, "adjustment");
        $save_adjustment_history = $this->reports->save_payrollregister_filter($deductionCategory, "deduction");
        $save_history = $this->reports->save_payrollregister_filter($data['selectIncome'], $data['demchoices']);
        /*end*/
        $count = 0;
        $employeeid = $data["employeeid"];
        $deptid = $data['deptid'];
        $teachingtype = $data['teachingtype'];
        list($date_from, $date_to, $quarter) = explode(" ", $data["payrollcutoff"]);
        $status = $data["payroll_status"];
        $bank = isset($data["bank"]) ? $data['bank'] : "";
        $inc_income = $inc_adjustment = $inc_loan = $inc_fixed_deduc = $inc_deduction = $inc_loan = $grand_total = $summary = array();

        $q_emp_list = $this->payroll->getPayrollRegistrarEmpList($employeeid, $deptid, $date_from, $date_to, $quarter, $status, $bank, $data["sort"],$teachingtype);
        $campus_list = $this->extensions->getCampusId();
        $campus_list[""] = "";
        $emp_list = $grand_total["income"] = $grand_total["deduction"] = array();
        if($data['sort'] == "name"){
            foreach ($q_emp_list as $row) {
                $income_arr = $inc_income = $adjustmentToDisplay = array();
                $sort_key = "name";
                if($data["sort"] == "department") $sort_key = $row->dept_code; 
                if($data["sort"] == "campus") $sort_key = $row->campus_code;

                $income_arr = $this->setListTagToArray($row->income);
                $inc_income = $this->setIncludeIncomeLoanToTotal($income_arr, $inc_income);
                // echo "<pre>"; print_r($row->employeeid); echo "<pre>"; print_r($income_arr); 
                $adjustment_arr = $this->setListTagToArray($row->income_adj);
                $inc_adjustment = $this->setIncludeIncomeLoanToTotal($adjustment_arr, $inc_adjustment);
 

    /*======================== ADJUSTMENT ========================*/


     /* get adjustment total */

                  /*get the adjustment that will be displayed*/
                 $adjustmentList = array_intersect_key($adjustment_arr,$adjustmentIncome);
                foreach($adjustmentList as $key => $value){
                    if(array_key_exists($key, $adjustmentToDisplay)){
                        $adjustmentToDisplay[$key] = $key;
                    }
                }

                $totalSelectedAdjustedIncome = array_sum($adjustmentToDisplay);

                // GET THE adjustment Income from not selected income and get total
                $notSelectedAdjustment = array_intersect_key($adjustment_arr, $allIncome);
                $totalOtherAdjustmentIncome = array_sum($notSelectedAdjustment);

                //total of adjustment income to be display
                $totalOtherAdjustmentToDisplay = $totalOtherAdjustmentIncome - $totalSelectedAdjustedIncome;
                // $adjustmentToDisplay

    /*======================== DEMINIMISS ========================*/

                /*get the income that will be displayed*/
                $incomeToDisplay = array_intersect_key($inc_income,$data['selectIncome']);
                // GET THE deminimiss Income from selected income and get total
                $selectedDeminimiss = array_intersect_key($incomeToDisplay, $deminimissIncome);
                $totalDeminimissIncome = array_sum($selectedDeminimiss);

                // GET THE deminimiss Income from not selected income and get total
                $notSelectedDeminimissRoot = array_intersect_key($income_arr, $deminimissIncome);

                $notSelectedDeminimiss = array_diff_key($notSelectedDeminimissRoot, $selectedDeminimiss);
                $totalOtherDeminimissIncome = array_sum($notSelectedDeminimiss);
                if(count($notSelectedDeminimiss) > 0) $totalOtherDeminimissToDisplay = $totalOtherDeminimissIncome - $totalDeminimissIncome;
                else $totalOtherDeminimissToDisplay = 0;
    /*======================== NOT DEMINIMISS ========================*/


                // GET THE other Income from selected income and get total
                $selectedNotDeminimiss = array_intersect_key($incomeToDisplay, $notDeminimissIncome);
                $totalNotDeminimissIncome = array_sum($selectedNotDeminimiss);

                // GET THE other Income from not selected income and get total
                $notSelectedOtherIncome = array_intersect_key($income_arr, $notDeminimissIncome);
                $totalOtherIncome = array_sum($notSelectedOtherIncome);

                //total of other income to be display
                $totalOtherIncomeToDisplay = 0;
                $totalOtherIncomeToDisplay = $totalOtherIncome - $totalNotDeminimissIncome;

        /*======================== DEMINISS TO DISPLAY ========================*/
                $deminimiss_inc_income = array_intersect_key($inc_income, $deminimissIncome);
                $deminimissList = array_intersect_key($incomeToDisplay, $deminimissIncome);
                foreach($deminimissList as $key => $value){
                    if(!array_key_exists($key, $deminimissToDisplay)){
                        $deminimissToDisplay[$key] = $key;
                    }
                }

        /*======================== NOT DEMINISS TO DISPLAY ========================*/
                $not_deminimiss_inc_income = array_intersect_key($inc_income, $notDeminimissIncome);
                $noDeminimissList = array_intersect_key($incomeToDisplay, $notDeminimissIncome);
                foreach($noDeminimissList as $key => $value){
                    if(!array_key_exists($key, $notDeminimissToDisplay)){
                        $notDeminimissToDisplay[$key] = $key;
                    }
                }
                // echo "<pre>"; print_r($totalOtherIncome); 




                $income_list = array(
                    "salary"            => $row->salary,
                    "tardy"             => $row->tardy,
                    "absent"            => $row->absents,
                    "basic_pay"         => $row->netbasicpay,
                    "income_list"       => $income_arr,
                    "adjustment_list"   => $adjustment_arr,
                    "overtime"          => $row->overtime,
                    "gross"             => $row->gross,
                    "totalOtherDeminimissToDisplay" => $totalOtherDeminimissToDisplay,
                    "totalOtherAdjustmentToDisplay" => $totalOtherAdjustmentToDisplay,
                    "totalOtherIncomeToDisplay" => $totalOtherIncomeToDisplay
                );

                $fixed_deduc_arr = $this->setListTagToArray($row->fixeddeduc);
                $inc_fixed_deduc = $this->setIncludeIncomeLoanToTotal($fixed_deduc_arr, $inc_fixed_deduc);
                $deduc_arr = $this->setListTagToArray($row->otherdeduc);
                $inc_deduction = $this->setIncludeIncomeLoanToTotal($deduc_arr, $inc_deduction);
                $loan_arr = $this->setListTagToArray($row->loan);
                $inc_loan = $this->setIncludeIncomeLoanToTotal($loan_arr, $inc_loan);

                 /*get the deduction that will be displayed*/
                $emp_deduction = array_intersect_key($deduc_arr,$deductionCategory);

                /*get sum of total deduction and selected deduction*/
                $totalDeduction = array_sum($deduc_arr);
                $totalSelectedDeduction = array_sum($emp_deduction);

                // $emp_deduction = array_keys($emp_deduction);
                foreach($emp_deduction as $key => $value){
                    if(!in_array($value, $deductionToDisplay)){
                        $deductionToDisplay[$key] = $value;
                    }
                }

                /*subtract selected deduction and total deduction*/
                /*if(count($notSelectedDeminimiss) == 0) */$totalOtherDeductionToDisplay = $totalDeduction - $totalSelectedDeduction;
               /* else $totalOtherDeductionToDisplay = 0;*/

                $deduction_list = array(
                    "with_holding_tax"      => $row->withholdingtax,
                    "fixed_deduc_list"      => $fixed_deduc_arr,
                    "deduc_list"            => $deduc_arr,
                    "loan_list"             => $loan_arr,
                    "total_deduction"       => $this->getTotalDeduction($row->withholdingtax, $fixed_deduc_arr, $deduc_arr, $loan_arr),
                    "net"                   => $row->net,
                    "totalOtherDeductionToDisplay" => $totalOtherDeductionToDisplay
                );

                $grand_total["income"] = $this->setGrandTotalInArray($income_list, $grand_total["income"]);
                $grand_total["deduction"] = $this->setGrandTotalInArray($deduction_list, $grand_total["deduction"]);

                if($row->deptid == "ACAD"){
                    $emp_list[$sort_key][$row->employeeid] = array(
                        "employeeid"        => $row->employeeid,
                        "name"              => $row->fullname,
                        "income"            => $income_list,
                        "campusid"            => $row->campus_desc,
                        "deptid"            => $row->deptid,
                        "deduction"         => $deduction_list
                    );

                    if(!array_key_exists($sort_key, $summary)){
                        $summary[$sort_key] = array(
                            "income" => array(),
                            "deduction" => array()
                        );
                    } 
                    if(!isset($summary[$sort_key][$row->campus_desc])){
                        $summary[$sort_key][$row->campus_desc] = array();
                        $summary[$sort_key][$row->campus_desc]['count'] = 0;
                    }
                    if($row->deptid == "ACAD" && in_array($row->campus_code, $campus_list)){
                        $summary[$sort_key][$row->campus_desc] = $this->setGrandTotalInArray($income_list, $summary[$sort_key][$row->campus_desc]);
                        $summary[$sort_key][$row->campus_desc] = $this->setGrandTotalInArray($deduction_list, $summary[$sort_key][$row->campus_desc]);
                        $summary[$sort_key][$row->campus_desc]['count']++;
                    }

                    $summary[$sort_key]["income"] = $this->setGrandTotalInArray($income_list, $summary[$sort_key]["income"]);
                    $summary[$sort_key]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$sort_key]["deduction"]);
                }else{
                    $emp_list[$row->campus_code][$row->employeeid] = array(
                        "employeeid"        => $row->employeeid,
                        "name"              => $row->fullname,
                        "income"            => $income_list,
                        "campusid"            => $row->campus_desc,
                        "deptid"            => $row->deptid,
                        "deduction"         => $deduction_list
                    );
                    if(!array_key_exists($row->campus_code, $summary)){
                        $summary[$row->campus_code] = array(
                            "income" => array(),
                            "deduction" => array()
                        );
                    } 
                    if(!isset($summary[$row->campus_code][$row->campus_desc])){
                        $summary[$row->campus_code][$row->campus_desc] = array();
                        $summary[$row->campus_code][$row->campus_desc]['count'] = 0;
                    }
                    if($row->deptid == "ACAD" && in_array($row->campus_code, $campus_list)){
                        $summary[$row->campus_code][$row->campus_desc] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->campus_desc]);
                        $summary[$row->campus_code][$row->campus_desc] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->campus_desc]);
                        $summary[$row->campus_code][$row->campus_desc]['count']++;
                    }

                    $summary[$row->campus_code]["income"] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code]["income"]);
                    $summary[$row->campus_code]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code]["deduction"]);

                    /*CAMPUS PERDEPT*/
                    if($row->deptid != "ACAD"){
                        if(!isset($row->campus_code[$row->deptid])){
                            $summary[$row->campus_code][$row->deptid] = array(
                                "income" => array(),
                                "deduction" => array()
                            );
                        } 
                        if(!isset($summary[$row->campus_code][$row->deptid])){
                            $summary[$row->campus_code][$row->deptid] = array();
                            $summary[$row->campus_code][$row->deptid]['count'] = 0;
                        }
                        if($row->deptid != "ACAD"){
                            $summary[$row->campus_code][$row->deptid] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->deptid]);
                            $summary[$row->campus_code][$row->deptid] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->deptid]);
                            $summary[$row->campus_code][$row->deptid]['count']++;

                        }
                            if(isset($summary[$row->campus_code][$row->deptid]["income"])) $summary[$row->campus_code][$row->deptid]["income"] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->deptid]["income"]);
                            if(isset($summary[$row->campus_code][$row->deptid]["deduction"])) $summary[$row->campus_code][$row->deptid]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->deptid]["deduction"]);
                    }
                }


            }
            // echo "<pre>"; print_r($summary); die;
            // $deductionToDisplay = array_flip($deductionToDisplay);
            
            $data["sched_display"] = date("F", strtotime($date_to)) ." ". date("d", strtotime($date_from)) ."-". date("d", strtotime($date_to)) ." ". date("Y", strtotime($date_to));
            $data["config"] = $this->getArrayConfig();
            ksort($inc_adjustment);
            $data["inc_adjustment"] = $adjustmentToDisplay;
            ksort($inc_income);
            $data["inc_income"] = array("deminimissList" => $deminimissToDisplay,"noDeminimissList" => $notDeminimissToDisplay);
            ksort($inc_fixed_deduc);
            $data["inc_fixed_deduc"] = $inc_fixed_deduc;
            ksort($deductionToDisplay);
            $data["inc_deduction"] = $deductionToDisplay;
            ksort($inc_loan);
            $data["inc_loan"] = $inc_loan;
            $data["grand_total"] = $grand_total;
            $data["summary"] = $summary;
            $data["emp_list"] = $emp_list;
            /* ksort($data['emp_list']);
            if(!isset($emp_list['name']) && isset($emp_list['ACAD'])){
                $popped_department = $emp_list['ACAD'];
                unset($data["emp_list"]['ACAD']);
                $data['emp_list']['ACAD'] = $popped_department;
            }*/

            // $data['emp_list'] = ($data["sort"] == "department") ? $this->constructEmployeeListforPayrollRegistrarReport($data['emp_list']) : $data["emp_list"] ;

            // echo "<pre>"; print_r($data); die;
        }else{
                $emp_list = array();
                $q_emp_list = $this->payroll->getPayrollRegistrarEmpList($employeeid, $deptid, $date_from, $date_to, $quarter, $status, $bank, $data["sort"],"teaching");
                foreach ($q_emp_list as $row) {
                    $income_arr = $inc_income = $adjustmentToDisplay = array();
                    $sort_key = "name";
                    if($data["sort"] == "department") $sort_key = $row->dept_code; 
                    if($data["sort"] == "campus") $sort_key = $row->campus_code;

                    $income_arr = $this->setListTagToArray($row->income);
                    $inc_income = $this->setIncludeIncomeLoanToTotal($income_arr, $inc_income);
                    // echo "<pre>"; print_r($row->employeeid); echo "<pre>"; print_r($income_arr); 
                    $adjustment_arr = $this->setListTagToArray($row->income_adj);
                    $inc_adjustment = $this->setIncludeIncomeLoanToTotal($adjustment_arr, $inc_adjustment);



        /*======================== ADJUSTMENT ========================*/


         /* get adjustment total */

                      /*get the adjustment that will be displayed*/
                    $adjustmentList = array_intersect_key($adjustment_arr,$adjustmentIncome);
                    foreach($adjustmentList as $key => $value){
                        if(array_key_exists($key, $adjustmentToDisplay)){
                            $adjustmentToDisplay[$key] = $key;
                        }
                    }

                   $totalSelectedAdjustedIncome = array_sum($adjustmentToDisplay);

                    // GET THE adjustment Income from not selected income and get total
                    $notSelectedAdjustmentRoot = array_intersect_key($adjustment_arr, $allIncome);
                    $notSelectedAdjustment = array_diff_key($notSelectedAdjustmentRoot, $adjustmentToDisplay);

                    $totalOtherAdjustmentIncome = array_sum($notSelectedAdjustment);

                    //total of adjustment income to be display
                    if(count($notSelectedAdjustment) > 0) $totalOtherAdjustmentToDisplay = $totalOtherAdjustmentIncome - $totalSelectedAdjustedIncome;
                    else $totalOtherAdjustmentToDisplay = 0;
                    // $adjustmentToDisplay

        /*======================== DEMINIMISS ========================*/

                    /*get the income that will be displayed*/
                    $incomeToDisplay = array_intersect_key($inc_income,$data['selectIncome']);
                    // GET THE deminimiss Income from selected income and get total
                    $selectedDeminimiss = array_intersect_key($incomeToDisplay, $deminimissIncome);
                    $totalDeminimissIncome = array_sum($selectedDeminimiss);

                    // GET THE deminimiss Income from not selected income and get total
                    $notSelectedDeminimissRoot = array_intersect_key($income_arr, $deminimissIncome);

                    $notSelectedDeminimiss = array_diff_key($notSelectedDeminimissRoot, $selectedDeminimiss);
                    $totalOtherDeminimissIncome = array_sum($notSelectedDeminimiss);
                    if(count($notSelectedDeminimiss) > 0) $totalOtherDeminimissToDisplay = $totalOtherDeminimissIncome - $totalDeminimissIncome;
                    else $totalOtherDeminimissToDisplay = 0;
        /*======================== NOT DEMINIMISS ========================*/


                    // GET THE other Income from selected income and get total
                    $selectedNotDeminimiss = array_intersect_key($incomeToDisplay, $notDeminimissIncome);
                    $totalNotDeminimissIncome = array_sum($selectedNotDeminimiss);

                    // GET THE other Income from not selected income and get total
                    $notSelectedOtherIncome = array_intersect_key($income_arr, $notDeminimissIncome);
                    $totalOtherIncome = array_sum($notSelectedOtherIncome);

                    //total of other income to be display
                    $totalOtherIncomeToDisplay = 0;
                    $totalOtherIncomeToDisplay = $totalOtherIncome - $totalNotDeminimissIncome;

            /*======================== DEMINISS TO DISPLAY ========================*/
                    $deminimiss_inc_income = array_intersect_key($inc_income, $deminimissIncome);
                    $deminimissList = array_intersect_key($incomeToDisplay, $deminimissIncome);
                    foreach($deminimissList as $key => $value){
                        if(!array_key_exists($key, $deminimissToDisplay)){
                            $deminimissToDisplay[$key] = $key;
                        }
                    }

            /*======================== NOT DEMINISS TO DISPLAY ========================*/
                    $not_deminimiss_inc_income = array_intersect_key($inc_income, $notDeminimissIncome);
                    $noDeminimissList = array_intersect_key($incomeToDisplay, $notDeminimissIncome);
                    foreach($noDeminimissList as $key => $value){
                        if(!array_key_exists($key, $notDeminimissToDisplay)){
                            $notDeminimissToDisplay[$key] = $key;
                        }
                    }
                    // echo "<pre>"; print_r($totalOtherIncome); 




                    $income_list = array(
                        "salary"            => $row->salary,
                        "tardy"             => $row->tardy,
                        "absent"            => $row->absents,
                        "basic_pay"         => $row->netbasicpay,
                        "income_list"       => $income_arr,
                        "adjustment_list"   => $adjustment_arr,
                        "overtime"          => $row->overtime,
                        "gross"             => $row->gross,
                        "totalOtherDeminimissToDisplay" => $totalOtherDeminimissToDisplay,
                        "totalOtherAdjustmentToDisplay" => $totalOtherAdjustmentToDisplay,
                        "totalOtherIncomeToDisplay" => $totalOtherIncomeToDisplay
                    );

                    $fixed_deduc_arr = $this->setListTagToArray($row->fixeddeduc);
                    $inc_fixed_deduc = $this->setIncludeIncomeLoanToTotal($fixed_deduc_arr, $inc_fixed_deduc);
                    $deduc_arr = $this->setListTagToArray($row->otherdeduc);
                    $inc_deduction = $this->setIncludeIncomeLoanToTotal($deduc_arr, $inc_deduction);
                    $loan_arr = $this->setListTagToArray($row->loan);
                    $inc_loan = $this->setIncludeIncomeLoanToTotal($loan_arr, $inc_loan);

                     /*get the deduction that will be displayed*/
                    $emp_deduction = array_intersect_key($deduc_arr,$deductionCategory);

                    /*get sum of total deduction and selected deduction*/
                    $totalDeduction = array_sum($deduc_arr);
                    $totalSelectedDeduction = array_sum($emp_deduction);

                    // $emp_deduction = array_keys($emp_deduction);
                    foreach($emp_deduction as $key => $value){
                        if(!in_array($value, $deductionToDisplay)){
                            $deductionToDisplay[$key] = $value;
                        }
                    }

                    /*subtract selected deduction and total deduction*/
                    /*if(count($notSelectedDeminimiss) == 0)*/ $totalOtherDeductionToDisplay = $totalDeduction - $totalSelectedDeduction;
                   /* else $totalOtherDeductionToDisplay = 0;*/

                    $deduction_list = array(
                        "with_holding_tax"      => $row->withholdingtax,
                        "fixed_deduc_list"      => $fixed_deduc_arr,
                        "deduc_list"            => $deduc_arr,
                        "loan_list"             => $loan_arr,
                        "total_deduction"       => $this->getTotalDeduction($row->withholdingtax, $fixed_deduc_arr, $deduc_arr, $loan_arr),
                        "net"                   => $row->net,
                        "totalOtherDeductionToDisplay" => $totalOtherDeductionToDisplay
                    );

                    $grand_total["income"] = $this->setGrandTotalInArray($income_list, $grand_total["income"]);
                    $grand_total["deduction"] = $this->setGrandTotalInArray($deduction_list, $grand_total["deduction"]);

                    if($row->deptid == "ACAD"){
                        $emp_list[$sort_key][$row->employeeid] = array(
                            "employeeid"        => $row->employeeid,
                            "name"              => $row->fullname,
                            "income"            => $income_list,
                            "campusid"            => $row->campus_desc,
                            "deptid"            => $row->deptid,
                            "deduction"         => $deduction_list
                        );

                        if(!array_key_exists($sort_key, $summary)){
                            $summary[$sort_key] = array(
                                "income" => array(),
                                "deduction" => array()
                            );
                        } 
                        if(!isset($summary[$sort_key][$row->campus_desc])){
                            $summary[$sort_key][$row->campus_desc] = array();
                            $summary[$sort_key][$row->campus_desc]['count'] = 0;
                        }
                        if($row->deptid == "ACAD" && in_array($row->campus_code, $campus_list)){
                            $summary[$sort_key][$row->campus_desc] = $this->setGrandTotalInArray($income_list, $summary[$sort_key][$row->campus_desc]);
                            $summary[$sort_key][$row->campus_desc] = $this->setGrandTotalInArray($deduction_list, $summary[$sort_key][$row->campus_desc]);
                            $summary[$sort_key][$row->campus_desc]['count']++;
                        }

                        $summary[$sort_key]["income"] = $this->setGrandTotalInArray($income_list, $summary[$sort_key]["income"]);
                        $summary[$sort_key]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$sort_key]["deduction"]);
                    }else{
                        $emp_list[$row->campus_code][$row->employeeid] = array(
                            "employeeid"        => $row->employeeid,
                            "name"              => $row->fullname,
                            "income"            => $income_list,
                            "campusid"            => $row->campus_desc,
                            "deptid"            => $row->deptid,
                            "deduction"         => $deduction_list
                        );
                        if(!array_key_exists($row->campus_code, $summary)){
                            $summary[$row->campus_code] = array(
                                "income" => array(),
                                "deduction" => array()
                            );
                        } 
                        if(!isset($summary[$row->campus_code][$row->campus_desc])){
                            $summary[$row->campus_code][$row->campus_desc] = array();
                            $summary[$row->campus_code][$row->campus_desc]['count'] = 0;
                        }
                        if($row->deptid == "ACAD" && in_array($row->campus_code, $campus_list)){
                            $summary[$row->campus_code][$row->campus_desc] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->campus_desc]);
                            $summary[$row->campus_code][$row->campus_desc] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->campus_desc]);
                            $summary[$row->campus_code][$row->campus_desc]['count']++;
                        }

                        $summary[$row->campus_code]["income"] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code]["income"]);
                        $summary[$row->campus_code]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code]["deduction"]);

                        /*CAMPUS PERDEPT*/
                        if($row->deptid != "ACAD"){
                            if(!isset($row->campus_code[$row->deptid])){
                                $summary[$row->campus_code][$row->deptid] = array(
                                    "income" => array(),
                                    "deduction" => array()
                                );
                            } 
                            if(!isset($summary[$row->campus_code][$row->deptid])){
                                $summary[$row->campus_code][$row->deptid] = array();
                                $summary[$row->campus_code][$row->deptid]['count'] = 0;
                            }
                            if($row->deptid != "ACAD"){
                                $summary[$row->campus_code][$row->deptid] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->deptid]);
                                $summary[$row->campus_code][$row->deptid] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->deptid]);
                                $summary[$row->campus_code][$row->deptid]['count']++;

                            }
                                if(isset($summary[$row->campus_code][$row->deptid]["income"])) $summary[$row->campus_code][$row->deptid]["income"] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->deptid]["income"]);
                                if(isset($summary[$row->campus_code][$row->deptid]["deduction"])) $summary[$row->campus_code][$row->deptid]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->deptid]["deduction"]);
                        }
                    }


                }
                // echo "<pre>"; print_r($summary); die;
                // $deductionToDisplay = array_flip($deductionToDisplay);
                
                $data["sched_display"] = date("F", strtotime($date_to)) ." ". date("d", strtotime($date_from)) ."-". date("d", strtotime($date_to)) ." ". date("Y", strtotime($date_to));
                $data["config"] = $this->getArrayConfig();
                ksort($inc_adjustment);
                $data["inc_adjustment"] = $adjustmentToDisplay;
                ksort($inc_income);
                $data["inc_income"] = array("deminimissList" => $deminimissToDisplay,"noDeminimissList" => $notDeminimissToDisplay);
                ksort($inc_fixed_deduc);
                $data["inc_fixed_deduc"] = $inc_fixed_deduc;
                ksort($deductionToDisplay);
                $data["inc_deduction"] = $deductionToDisplay;
                ksort($inc_loan);
                $data["inc_loan"] = $inc_loan;
                $data['teaching']["grand_total"] = $grand_total;
                $data['teaching']["summary"] = $summary;
                $data['teaching']["emp_list"] = $emp_list;
    
                $emp_list = array();
                $grand_total['nonteaching']["income"] = $grand_total['nonteaching']["deduction"] = array();
                $q_emp_list = $this->payroll->getPayrollRegistrarEmpList($employeeid, $deptid, $date_from, $date_to, $quarter, $status, $bank, $data["sort"],"nonteaching");
                foreach ($q_emp_list as $row) {
                    $income_arr = $inc_income = $adjustmentToDisplay = array();
                    $sort_key = "name";
                    if($data["sort"] == "department") $sort_key = $row->dept_code; 
                    if($data["sort"] == "campus") $sort_key = $row->campus_code;

                    $income_arr = $this->setListTagToArray($row->income);
                    $inc_income = $this->setIncludeIncomeLoanToTotal($income_arr, $inc_income);
                    // echo "<pre>"; print_r($row->employeeid); echo "<pre>"; print_r($income_arr); 
                    $adjustment_arr = $this->setListTagToArray($row->income_adj);
                    $inc_adjustment = $this->setIncludeIncomeLoanToTotal($adjustment_arr, $inc_adjustment);



        /*======================== ADJUSTMENT ========================*/


         /* get adjustment total */

                      /*get the adjustment that will be displayed*/
                    $adjustmentList = array_intersect_key($adjustment_arr,$adjustmentIncome);
                    foreach($adjustmentList as $key => $value){
                        if(array_key_exists($key, $adjustmentToDisplay)){
                            $adjustmentToDisplay[$key] = $key;
                        }
                    }

                    $totalSelectedAdjustedIncome = array_sum($adjustmentToDisplay);

                    // GET THE adjustment Income from not selected income and get total
                    $notSelectedAdjustmentRoot = array_intersect_key($adjustment_arr, $allIncome);
                    $notSelectedAdjustment = array_diff_key($notSelectedAdjustmentRoot, $adjustmentToDisplay);

                    $totalOtherAdjustmentIncome = array_sum($notSelectedAdjustment);

                    //total of adjustment income to be display
                    if(count($notSelectedAdjustment) > 0) $totalOtherAdjustmentToDisplay = $totalOtherAdjustmentIncome - $totalSelectedAdjustedIncome;
                    else $totalOtherAdjustmentToDisplay = 0;
                    // $adjustmentToDisplay

        /*======================== DEMINIMISS ========================*/

                    /*get the income that will be displayed*/
                    $incomeToDisplay = array_intersect_key($inc_income,$data['selectIncome']);
                    // GET THE deminimiss Income from selected income and get total
                    $selectedDeminimiss = array_intersect_key($incomeToDisplay, $deminimissIncome);
                    $totalDeminimissIncome = array_sum($selectedDeminimiss);

                    // GET THE deminimiss Income from not selected income and get total
                    $notSelectedDeminimissRoot = array_intersect_key($income_arr, $deminimissIncome);

                    $notSelectedDeminimiss = array_diff_key($notSelectedDeminimissRoot, $selectedDeminimiss);
                    $totalOtherDeminimissIncome = array_sum($notSelectedDeminimiss);
                    if(count($notSelectedDeminimiss) > 0) $totalOtherDeminimissToDisplay = $totalOtherDeminimissIncome - $totalDeminimissIncome;
                    else $totalOtherDeminimissToDisplay = 0;
        /*======================== NOT DEMINIMISS ========================*/


                    // GET THE other Income from selected income and get total
                    $selectedNotDeminimiss = array_intersect_key($incomeToDisplay, $notDeminimissIncome);
                    $totalNotDeminimissIncome = array_sum($selectedNotDeminimiss);

                    // GET THE other Income from not selected income and get total
                    $notSelectedOtherIncome = array_intersect_key($income_arr, $notDeminimissIncome);
                    $totalOtherIncome = array_sum($notSelectedOtherIncome);

                    //total of other income to be display
                    $totalOtherIncomeToDisplay = 0;
                    $totalOtherIncomeToDisplay = $totalOtherIncome - $totalNotDeminimissIncome;

            /*======================== DEMINISS TO DISPLAY ========================*/
                    $deminimiss_inc_income = array_intersect_key($inc_income, $deminimissIncome);
                    $deminimissList = array_intersect_key($incomeToDisplay, $deminimissIncome);
                    foreach($deminimissList as $key => $value){
                        if(!array_key_exists($key, $deminimissToDisplay)){
                            $deminimissToDisplay[$key] = $key;
                        }
                    }

            /*======================== NOT DEMINISS TO DISPLAY ========================*/
                    $not_deminimiss_inc_income = array_intersect_key($inc_income, $notDeminimissIncome);
                    $noDeminimissList = array_intersect_key($incomeToDisplay, $notDeminimissIncome);
                    foreach($noDeminimissList as $key => $value){
                        if(!array_key_exists($key, $notDeminimissToDisplay)){
                            $notDeminimissToDisplay[$key] = $key;
                        }
                    }
                    // echo "<pre>"; print_r($totalOtherIncome); 




                    $income_list = array(
                        "salary"            => $row->salary,
                        "tardy"             => $row->tardy,
                        "absent"            => $row->absents,
                        "basic_pay"         => $row->netbasicpay,
                        "income_list"       => $income_arr,
                        "adjustment_list"   => $adjustment_arr,
                        "overtime"          => $row->overtime,
                        "gross"             => $row->gross,
                        "totalOtherDeminimissToDisplay" => $totalOtherDeminimissToDisplay,
                        "totalOtherAdjustmentToDisplay" => $totalOtherAdjustmentToDisplay,
                        "totalOtherIncomeToDisplay" => $totalOtherIncomeToDisplay
                    );

                    $fixed_deduc_arr = $this->setListTagToArray($row->fixeddeduc);
                    $inc_fixed_deduc = $this->setIncludeIncomeLoanToTotal($fixed_deduc_arr, $inc_fixed_deduc);
                    $deduc_arr = $this->setListTagToArray($row->otherdeduc);
                    $inc_deduction = $this->setIncludeIncomeLoanToTotal($deduc_arr, $inc_deduction);
                    $loan_arr = $this->setListTagToArray($row->loan);
                    $inc_loan = $this->setIncludeIncomeLoanToTotal($loan_arr, $inc_loan);

                     /*get the deduction that will be displayed*/
                    $emp_deduction = array_intersect_key($deduc_arr,$deductionCategory);

                    /*get sum of total deduction and selected deduction*/
                    $totalDeduction = array_sum($deduc_arr);
                    $totalSelectedDeduction = array_sum($emp_deduction);

                    // $emp_deduction = array_keys($emp_deduction);
                    foreach($emp_deduction as $key => $value){
                        if(!in_array($value, $deductionToDisplay)){
                            $deductionToDisplay[$key] = $value;
                        }
                    }

                    /*subtract selected deduction and total deduction*/
                    /*if(count($notSelectedDeminimiss) == 0) */$totalOtherDeductionToDisplay = $totalDeduction - $totalSelectedDeduction;
                    /*else $totalOtherDeductionToDisplay = 0;*/

                    $deduction_list = array(
                        "with_holding_tax"      => $row->withholdingtax,
                        "fixed_deduc_list"      => $fixed_deduc_arr,
                        "deduc_list"            => $deduc_arr,
                        "loan_list"             => $loan_arr,
                        "total_deduction"       => $this->getTotalDeduction($row->withholdingtax, $fixed_deduc_arr, $deduc_arr, $loan_arr),
                        "net"                   => $row->net,
                        "totalOtherDeductionToDisplay" => $totalOtherDeductionToDisplay
                    );

                    $grand_total["income"] = $this->setGrandTotalInArray($income_list, $grand_total["income"]);
                    $grand_total["deduction"] = $this->setGrandTotalInArray($deduction_list, $grand_total["deduction"]);

                    $grand_total['nonteaching']["income"] = $this->setGrandTotalInArray($income_list, $grand_total['nonteaching']["income"]);
                    $grand_total['nonteaching']["deduction"] = $this->setGrandTotalInArray($deduction_list, $grand_total['nonteaching']["deduction"]);

                    if($row->deptid == "ACAD"){
                        $emp_list[$sort_key][$row->employeeid] = array(
                            "employeeid"        => $row->employeeid,
                            "name"              => $row->fullname,
                            "income"            => $income_list,
                            "campusid"            => $row->campus_desc,
                            "deptid"            => $row->deptid,
                            "deduction"         => $deduction_list
                        );

                        if(!array_key_exists($sort_key, $summary)){
                            $summary[$sort_key] = array(
                                "income" => array(),
                                "deduction" => array()
                            );
                        } 
                        if(!isset($summary[$sort_key][$row->campus_desc])){
                            $summary[$sort_key][$row->campus_desc] = array();
                            $summary[$sort_key][$row->campus_desc]['count'] = 0;
                        }
                        if($row->deptid == "ACAD" && in_array($row->campus_code, $campus_list)){
                            $summary[$sort_key][$row->campus_desc] = $this->setGrandTotalInArray($income_list, $summary[$sort_key][$row->campus_desc]);
                            $summary[$sort_key][$row->campus_desc] = $this->setGrandTotalInArray($deduction_list, $summary[$sort_key][$row->campus_desc]);
                            $summary[$sort_key][$row->campus_desc]['count']++;
                        }

                        $summary[$sort_key]["income"] = $this->setGrandTotalInArray($income_list, $summary[$sort_key]["income"]);
                        $summary[$sort_key]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$sort_key]["deduction"]);
                    }else{
                        $emp_list[$row->campus_code][$row->employeeid] = array(
                            "employeeid"        => $row->employeeid,
                            "name"              => $row->fullname,
                            "income"            => $income_list,
                            "campusid"            => $row->campus_desc,
                            "deptid"            => $row->deptid,
                            "deduction"         => $deduction_list
                        );
                        if(!array_key_exists($row->campus_code, $summary)){
                            $summary[$row->campus_code] = array(
                                "income" => array(),
                                "deduction" => array()
                            );
                        } 
                        if(!isset($summary[$row->campus_code][$row->campus_desc])){
                            $summary[$row->campus_code][$row->campus_desc] = array();
                            $summary[$row->campus_code][$row->campus_desc]['count'] = 0;
                        }
                        // if($row->deptid == "ACAD" && in_array($row->campus_code, $campus_list)){
                            $summary[$row->campus_code][$row->campus_desc] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->campus_desc]);
                            $summary[$row->campus_code][$row->campus_desc] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->campus_desc]);
                            $summary[$row->campus_code][$row->campus_desc]['count']++;
                        // }

                        $summary[$row->campus_code]["income"] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code]["income"]);
                        $summary[$row->campus_code]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code]["deduction"]);

                        /*CAMPUS PERDEPT*/
                        if($row->deptid != "ACAD"){
                            if(!isset($row->campus_code[$row->deptid])){
                                $summary[$row->campus_code][$row->deptid] = array(
                                    "income" => array(),
                                    "deduction" => array()
                                );
                            } 
                            if(!isset($summary[$row->campus_code][$row->deptid])){
                                $summary[$row->campus_code][$row->deptid] = array();
                                $summary[$row->campus_code][$row->deptid]['count'] = 0;
                            }
                            if($row->deptid != "ACAD"){
                                $summary[$row->campus_code][$row->deptid] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->deptid]);
                                $summary[$row->campus_code][$row->deptid] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->deptid]);
                                $summary[$row->campus_code][$row->deptid]['count']++;

                            }
                                if(isset($summary[$row->campus_code][$row->deptid]["income"])) $summary[$row->campus_code][$row->deptid]["income"] = $this->setGrandTotalInArray($income_list, $summary[$row->campus_code][$row->deptid]["income"]);
                                if(isset($summary[$row->campus_code][$row->deptid]["deduction"])) $summary[$row->campus_code][$row->deptid]["deduction"] = $this->setGrandTotalInArray($deduction_list, $summary[$row->campus_code][$row->deptid]["deduction"]);
                        }
                    }


                }
                // echo "<pre>"; print_r($summary); die;
                // $deductionToDisplay = array_flip($deductionToDisplay);
                
                $data["sched_display"] = date("F", strtotime($date_to)) ." ". date("d", strtotime($date_from)) ."-". date("d", strtotime($date_to)) ." ". date("Y", strtotime($date_to));
                $data["config"] = $this->getArrayConfig();
                ksort($inc_adjustment);
                $data["inc_adjustment"] = $adjustmentToDisplay;
                ksort($inc_income);
                $data["inc_income"] = array("deminimissList" => $deminimissToDisplay,"noDeminimissList" => $notDeminimissToDisplay);
                ksort($inc_fixed_deduc);
                $data["inc_fixed_deduc"] = $inc_fixed_deduc;
                ksort($deductionToDisplay);
                $data["inc_deduction"] = $deductionToDisplay;
                ksort($inc_loan);
                $data["inc_loan"] = $inc_loan;
                $data['nonteaching']["grand_total"] = $grand_total;
                $data['nonteaching']["summary"] = $summary;
                $data['nonteaching']["emp_list"] = $emp_list;

        }
        // echo "<pre>"; print_r($data); die;
        if($data["format"] == "PDF") $this->load->view("forms_pdf/payroll_registrar", $data);
        else                         $this->load->view("reports_excel/payroll_registrar", $data);
    }

    function constructEmployeeListforPayrollRegistrarReport($emplist){
        $data = $data_tmp = array();
        $acad_tmp_arr = array();

        foreach ($emplist as $deptid => $employee) {
            foreach ($employee as $empid => $info) {
                $data_tmp[$deptid][$info["name"]][$empid] = $info;
            }   
        }
        
        foreach ($data_tmp as $deptid => $employee) {
            ksort($employee);
            if($deptid != "ACAD"){
                foreach ($employee as $name => $emp_info){
                    foreach ($emp_info as $empid => $info) $data[$deptid][$empid] = $info;
                }
            }else{
                foreach ($employee as $name => $emp_info){
                    foreach ($emp_info as $empid => $info) $acad_tmp_arr[$info["campusid"]][$empid] = $info;
                }
            }
        }

        if(isset($emplist["ACAD"])){
            
            $acad_arr = array();
            ksort($acad_tmp_arr);
            foreach ($acad_tmp_arr as $campus => $employees) {
                foreach ($employees as $empid => $info)  $acad_arr[$empid] = $info;
            }

            $data["ACAD"] = $acad_arr;
        }
        
        return $data;
    }

    function getArrayConfig(){
        $this->load->model("payrollconfig");
        $this->load->model("extras");
        $data = array();

        $q_income = $this->payrollconfig->getAllIncomeConfig();
        foreach ($q_income as $row) $data["income"][$row->id] = $row->description;

        $q_deduction = $this->payrollconfig->getDeductionConfig();
        foreach ($q_deduction as $row) $data["deduction"][$row->id] = $row->description;

        $q_loan = $this->payrollconfig->getLoanConfig();
        foreach ($q_loan as $row) $data["loan"][$row->id] = $row->description;

        $data["name"]["name"] = "ALL EMPLOYEE";

        $data["department"] = $this->extras->showdepartment("NO DEPARTMENT");

        $data["campus"] = $this->extras->showcampus("NO CAMPUS");

        return $data;
    }

    function setGrandTotalInArray($list, $grand_total){

        foreach ($list as $key => $l_value) {
            
            if(is_array($l_value)){
                if(!array_key_exists($key, $grand_total)) $grand_total[$key] = array();

                foreach ($l_value as $sub_key => $sub_val) {
                    if(!array_key_exists($sub_key, $grand_total[$key])) $grand_total[$key][$sub_key] = 0;

                    $grand_total[$key][$sub_key] += $sub_val;
                }
            }else{
                if(!array_key_exists($key, $grand_total)) $grand_total[$key] = 0;

                $grand_total[$key] += $l_value;
            }
        }

        return $grand_total;
    }

    function getTotalDeduction($with_holding_tax, $fixed_deduc, $other_deduc, $loan){
        $total = 0;

        $total += $with_holding_tax;
        $total += $this->getTotalAmountInArray($fixed_deduc);
        $total += $this->getTotalAmountInArray($other_deduc);
        $total += $this->getTotalAmountInArray($loan);

        return $total;
    }

    function getTotalAmountInArray($array){
        $amount = 0;

        foreach ($array as $key => $value) $amount += $value;

        return $amount;
    }

    function setListTagToArray($list_tag){
        $data = array();

        if($list_tag){
            foreach (explode("/", $list_tag) as $tag_info) {
                list($key, $amount) = explode("=", $tag_info);

                $data[$key] = round($amount,2);      
            }
        }

        return $data;
    }

    function setIncludeIncomeLoanToTotal($list_tag, $data){

        foreach ($list_tag as $key => $amount) {
            if(!array_key_exists($key, $data)) $data[$key] = 0;

            $data[$key] += $amount;
        }

        return $data;
    }
    # end for ica-hyperion 21671

    function constructEncryptedFormData(){
        $formdata = $this->input->post('params');
        echo $this->form_data_encryption->encryptString($formdata);
        // echo $formdata;
    }

    function viewPayslipReport(){
        $formdata = $this->input->get('data');
        $formdata = $this->form_data_encryption->decryptString($formdata);
        $formdata = explode("&", $formdata);

        $data = array();
        foreach($formdata as $formvalue){
            list($key, $value) = explode("=", $formvalue);
            $data[$key] = $value;
        }

        $this->load->model('extras');
        $this->load->library('PdfCreator_mpdf');

        $this->load->view('forms_pdf/'. $data['form'],$data);
    }

    function getIncomeOptions(){
        $filter_history_deminimiss = $this->reports->getFilterHistory("yes");
        $filter_history_ntdeminimiss = $this->reports->getFilterHistory("no");
        $filter_code1 = explode(",", $filter_history_deminimiss);
        $filter_code2 = explode(",", $filter_history_ntdeminimiss);
        $payroll_income_config = array();
        $getIncome = $this->reports->getIncome();
        foreach($getIncome as $income_data){
            $payroll_income_config[$income_data->id] = $income_data->description;
        }


        $data = $result_arr = array();
        $toks = $this->input->post("toks");
        $is_deminimis = ($toks) ? $this->gibberish->decrypt($this->input->post("is_deminimis"), $toks) : $this->input->post("is_deminimis");

        if($is_deminimis == "yes"){
            $result_arr = $this->reports->getPayrollIncomeConfigNoDeminimis("payroll_income_config","selectAll");
            if($filter_history_deminimiss){
                foreach($filter_code1 as $code_val){
                    if($code_val != ""){
                        $data[] = array(
                            "value"     => $code_val,
                            "caption"   => $payroll_income_config[$code_val],
                            "is_select" => true
                        );
                    }
                }
            }else{
                    $data[] = array(
                        "value"     => "selectalltdeminimis",
                        "caption"   => "Select All",
                        "is_select" => true
                    );
            }
        }else{
            foreach($filter_code2 as $code_val){
                $result_arr = $this->reports->getPayrollIncomeConfigNoDeminimis("payroll_income_config","selectAll");
                if($code_val != ""){
                    $data[] = array(
                        "value"     => $code_val,
                        "caption"   => $payroll_income_config[$code_val],
                        "is_select" => true
                    );
                }
            }
        }

        foreach ($result_arr as $row) {
            $data[] = array(
                "value"     => $row["id"],
                "caption"   => $row["description"],
                "is_select" => false
            );
        }

        echo json_encode($data);
    }

    function checkIfhasData(){
        $cutoffstart = $cutoffend = '';
        $data = $this->input->post('cutoff');
        $data = explode(",", $data);
        $cutoffstart = $data[0];
        $cutoffend = $data[1];
        $query = $this->reports->loademployeeDeductionForVerify($cutoffstart, $cutoffend);
        if(count($query) > 0) echo TRUE;
        else echo FALSE;
    }

    function showSummaryOfAbsencesNoLeaveFiled(){
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $this->load->model("attendance");
        $data = array();
        $data = $this->input->post();

        $date_list = $this->reports->getDateIncluded($data["dfrom"], $data["dto"]);
        $data["month_list"] = $this->reports->getMonthList($date_list);
        $q_emplist = $this->reports->getEmployeeList("", $data["campus"], $data["deptid"], $data["tnt"])->result();

        $data["list"] = array();
        foreach ($q_emplist as $row) {
            if($row->employeeid){
                $data["list"][$row->employeeid]["name"] = strtoupper($row->fullname);
                $data["list"][$row->employeeid]["tnt"]  = $row->teachingtype;
                $data["list"][$row->employeeid]["absences_list"] = array();
                foreach ($date_list as $date) {
                    $computed_absent = 0;
                    
                    if($row->teachingtype == "teaching"){
                        list($tlec,$tlab,$tadmin,$utlec,$utlab,$utadmin,$tabsent,$totr,$totrest,$tothol,$tel,$tvl,$tsl,$tol,$tdlec,$tdlab,$tdadmin,$deducperday,$holiday,$hasSched,$hasLog,$twork_lec,$twork_lab,$twork_admin,$ot_list) = $this->attendance->computeEmployeeAttendanceSummaryTeaching($date, $date, $row->employeeid);

                        $computed_absent = $this->reports->convertTimeToNumber($deducperday);
                    }

                    if($row->teachingtype == "nonteaching"){
                        list($tabsent,$tlec,$tutlec,$totr,$totrest,$tothol,$tel,$tvl,$tsl,$tsl,$tol,$tholiday,$holiday,$hasSched,$hasLog,$workdays,$ot_list) = $this->attendance->computeEmployeeAttendanceSummaryNonTeaching($date, $date, $row->employeeid);

                        $tabsent = $this->reports->convertTimeToNumber(($tabsent) ? $tabsent : "00:00");
                        $tlec    = $this->reports->convertTimeToNumber(($tlec) ? $tlec : "00:00");
                        $tutlec  = $this->reports->convertTimeToNumber(($tutlec) ? $tutlec : "00:00");

                        $computed_absent = $tabsent + $tlec + $tutlec;
                    }

                    $computed_absent = ($computed_absent > 8) ? (int) $computed_absent : $computed_absent;
                    $data["list"][$row->employeeid]["absences_list"][$date] = $computed_absent;
                }
            }
        }
        #echo "<pre>"; print_r($data); die;
        $this->load->view("forms_pdf/summary_absences_no_leave_filed", $data);
    }

    function showAttendanceReport(){
        $data = array();
        $data = $this->input->post();
        if(isset($data["toks"])){
            $toks = $data["toks"];
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }

        $this->load->view("process/attendance_reports_filter", $data);
    }

    function showAbsentOBCorrectionReport(){
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $data = array();
        $data = $this->input->post();
        $emp_list = $this->reports->getAbsentOBCorrectionReport($data["dfrom"], $data["dto"], $data["type"], $data["isactive"]);
        $data["emp_list"] = array();
        $count = 0;
        foreach ($emp_list as $type => $list) {
            $type = ($type == "DIRECT") ? "OFFICIAL BUSINESS" : $type;
            foreach ($list as $row) {
                $count++;
                $data["emp_list"][$count]= array(
                    "employeeid" => $row["employeeid"],
                    "fullname" => $row["fullname"],
                    "type" => $type,
                    "position" => $row["position_desc"],
                    "department" =>  $this->extensions->getDeparmentDescriptionReport($row["deptid"]), 
                    "date_exclusive" => date("M d, Y", strtotime($row["fromdate"])) ." - ". date("M d, Y", strtotime($row["todate"])),
                    "time_requested" => date("M d, Y", strtotime($row["dateapplied"])),
                    "reason" => $row["remarks"]
                );
            }
        }
        ksort($data["emp_list"]);
        $view = "";
        
        if($data["format"] == "PDF") $view = "forms_pdf/ob_absent_correction_report";
        else                         $view = "reports_excel/ob_absent_correction_report";

        $this->load->view($view, $data);
    }

    function showDetailedAttendanceSetup(){
        $data = $this->input->post();
        if(isset($data["toks"])){
            $toks = $data["toks"];
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
        $data['datetoday'] = date('Y-m-d');
        $this->load->view('process/detailed_attendance',$data);
    }

    function showDetailedAttendanceReport(){
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $datesetfrom = $datesetto = $fv = $category = $ispdf = '';
        $campus_desc = $dept_desc = '';
        if($this->input->post()){
            $toks = $this->input->post("toks");
            $datesetfrom = $toks ? $this->gibberish->decrypt($this->input->post('datesetfrom'), $toks) : $this->input->post('datesetfrom');
            $datesetto = $toks ? $this->gibberish->decrypt($this->input->post('datesetto'), $toks) : $this->input->post('datesetto');
            $office = $toks ? $this->gibberish->decrypt($this->input->post('office'), $toks) : $this->input->post('office');
            $deptid = $toks ? $this->gibberish->decrypt($this->input->post('deptid'), $toks) : $this->input->post('deptid');
            $fv =$toks ? $this->gibberish->decrypt($this->input->post('fv'), $toks) : $this->input->post('fv');
            $category = $toks ? $this->gibberish->decrypt($this->input->post('category_selected'), $toks) : $this->input->post('category_selected');
            $terminal = $toks ? $this->gibberish->decrypt($this->input->post('terminal'), $toks) : $this->input->post('terminal');
            $logs = $toks ? $this->gibberish->decrypt($this->input->post('logs'), $toks) : $this->input->post('logs');
            $gate = $toks ? $this->gibberish->decrypt($this->input->post('gate'), $toks) : $this->input->post('gate');
        }else{
            $ispdf = TRUE;
            $formdata = $this->input->get("form_data");
            $formdata = base64_decode(urldecode($formdata));
            $data = Globals::convertFormDataToArray($formdata);
            $toks = $this->input->get("toks");
            if($toks){
                foreach ($data as $key => $value) {
                    if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
                }
            }
            $datesetfrom = isset($data['datesetfrom']) ? $data['datesetfrom'] : $this->input->get('datesetfrom');
            $datesetto = isset($data['datesetto']) ? $data['datesetto'] : $this->input->get('datesetto');
            $fv = isset($data['fv']) ? $data['fv'] : $this->input->get('fv');
            $category = isset($data['category_selected']) ? $data['category_selected'] : $this->input->get('category_selected');
            $terminal = isset($data['terminal']) ? $data['terminal'] : $this->input->get('terminal');
            $logs = isset($data['logs']) ? $data['logs'] : $this->input->get('logs');
            $gate = isset($data['gate']) ? $data['gate'] : $this->input->get('gate');
        }
        // echo "<pre>"; print_r($data); die;
        $data['records'] = $this->reports->showDetailedAttendance($fv, $datesetfrom, $datesetto, $category, $office, $deptid);
        // echo "<pre>";print_r($this->db->last_query());die;
        $data['from_date'] = $datesetfrom;
        $data['to_date'] = $datesetto;
        $data['category'] = $category;

        if($category == "att_adj"){
            $data['att_adj'] = $this->extensions->getAttendanceAdjustmentRecords($fv, $datesetfrom, $datesetto, $office);
        }

        if($category == "att_terminal"){
            $where_clause = "";
            if($fv) $where_clause .= " AND user_id = '$fv' ";
            $data["trail_records"] = $this->timesheet->getTimesheetTrail($datesetfrom, $datesetto, $where_clause, $terminal, $gate, $logs, $fv); 
            $data['logs'] = $logs;  
            if($ispdf) $this->load->view('forms_pdf/detailed_attendance_trail', $data);
            else $this->load->view('process/detailed_attendance_trail', $data);
            return;
        }
        
        $old_campus = $old_dept = '';
        if($ispdf){ 
            if($category != "att_adj" && $category != "absents"){
                $emplist = $summary = array();
                foreach($data['records'] as $key => $emp_info){
                    #echo "<pre>"; print_r($emp_info);
                    $campus_desc = $this->extensions->getCampusDescription($emp_info['campusid']);
                    $dept_desc = $this->extensions->getDepartmentDescription($emp_info['deptid']);
                    if($emp_info['deptid'] == "ACAD"){
                        if($category == "overtime") $emplist['ACADEMIC DEPARTMENT'][$campus_desc][$emp_info['employeeid']][] = $emp_info;
                        else                        $emplist['ACADEMIC DEPARTMENT'][$campus_desc][$emp_info['employeeid']] = $emp_info;
                        
                        if($category == "overtime"){
                            if($campus_desc != $old_campus) $summary[$campus_desc] = $emp_info['ot_amount']; 
                            else $summary[$campus_desc] += $emp_info['ot_amount']; 
                        }
                    }else{
                        if($category == "overtime") $emplist[$campus_desc][$dept_desc][$emp_info['employeeid']][] = $emp_info;
                        else                        $emplist[$campus_desc][$dept_desc][$emp_info['employeeid']] = $emp_info;
                        
                        if($category == "overtime"){
                            if($dept_desc != $old_dept) $summary[$dept_desc] = $emp_info['ot_amount']; 
                            else $summary[$dept_desc] += $emp_info['ot_amount']; 
                        }
                    }
                    $old_campus = $campus_desc;
                    $old_dept = $dept_desc;

                    $summary['grand_total'] += $emp_info['ot_amount'];
                }

                $data['summary'] = $summary;
                $data['records'] = $emplist;
                
            }
            
            $this->load->view('forms_pdf/detailed_attendance_table', $data);
        }

        else $this->load->view('process/detailed_attendance_table', $data);
 
    }

    function showOTReport(){
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $data = array();
        $data = $this->input->post();
        list($data["from_date"], $data["to_date"]) = explode(",", $data["cutoff"]);
        $data["campus"] = "";
        $data["deptid"] = "";
        $data["estatus"] = "";

        $this->load->view("forms_pdf/otreport", $data);
    }

    function showIndividualAttendanceReport(){
        $this->load->model('utils');
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $data = array();
        $data = $this->input->post();
        list($deptid, $campusid) = $this->utils->getEmpDept($data['employeeid']);
        $empid = $data['employeeid'];
        list($from_date, $to_date) = explode(",", $data["cutoff"]);

        $info = array();

        $dfrom  = $from_date;
        $dto    = $to_date;
        $empid_post = $empid;
        $edata  = "NEW";
        $tnt    = $this->extras->getEmployeeTeachingType($empid);
        $deptid_post = $deptid;
        $campus = $campusid;
        $reportType = "";


        $this->load->model('utils');
        $arr_empids = $this->utils->getEmpIDs($deptid_post,'',$tnt,$empid_post,' ORDER BY lname,fname', $campus);
        $date_range = $this->utils->getDatesFromRange($dfrom, $dto);

        foreach ($arr_empids as $empid) {
            $info['emplist_detail'][$empid]['fullname'] = $this->utils->getFullName($empid); 
            if(!$tnt) $tnt = $this->employee->getempdatacol('teachingtype',$empid);
            $deptid = $this->employee->getempdatacol('deptid',$empid);

            $fixedday = $this->attcompute->isFixedDay($empid);

            $hasLog = false;
            $firstDate = true;

            foreach ($date_range as $date) {
                $holiday        = $this->attcompute->isHolidayNew($empid,$date,$deptid ); 

                $holidayInfo    = $this->attcompute->holidayInfo($date);
                if(isset($holidayInfo["withPay"])) if($holidayInfo["withPay"]=='NO') $holiday = '';
                

                $isSuspension   = $this->isSuspension($holiday,$holidayInfo);

                if($firstDate && $holiday){
                    if($tnt=='teaching'){
                       $hasLog = $this->attendance->checkPreviousSchedAttendanceTeaching($date,$empid);
                    }else{
                        $hasLog = $this->attendance->checkPreviousSchedAttendanceNonTeaching($date,$empid);
                    }
                }

                list($info['attendance_list'][$deptid][$empid][$date]['detail'],$hasLog) = $this->getDailyAttendanceDetails($empid,$date,$edata,$hasLog,$isSuspension,$holiday,$tnt,$fixedday);
                $info['attendance_list'][$deptid][$empid][$date]['holidayinfo'] = $holidayInfo;
                $info['attendance_list'][$deptid][$empid][$date]['isHoliday'] = $holiday;



            } ///< end loop dates
        } ///< end loop empids

        // echo '<pre>';print_r($info['attendance_list']);
        // die;

        $info['datedisplay'] = $this->time->createRangeToDisplay($dfrom,$dto);
        $info['empcount'] = sizeof($arr_empids);

        if($tnt=='teaching'){
          $this->load->view('process/reports_pdf/attendance_detailed',$info);
        }else{
          $this->load->view('process/reports_pdf/attendance_detailed_NT',$info);
        }
    }

    function getDailyAttendanceDetails($empid='', $date='',$edata='',$hasLog='',$isSuspension='',$holiday='',$tnt='teaching',$fixedday=TRUE){
        $perday_info = array();
        $sched = $this->attcompute->displaySched($empid,$date);
        $sched_count = $sched->num_rows();

        $isValidSchedule = $this->isValidSchedule($sched);

        if($isValidSchedule){
            $haswholedayleave = false;
            $sched_seq = $hasleavecount = 0;
            $hasLogprev = $hasLog;
            $hasLog = false;

            $isCreditedHoliday = $this->isCreditedHoliday($hasLogprev,$isSuspension);

            foreach($sched->result() as $rsched){
                list($persched_info,$hasLog) = $this->getPerSchedAtendanceDetails($empid,$date,$edata,$rsched,$sched_seq,$isCreditedHoliday,$holiday,$tnt,$fixedday,$hasLog);
                array_push($perday_info, $persched_info);
            }

        }else{

            $persched_info = $this->getNoSchedAtendanceDetails($empid,$date,$edata);
            array_push($perday_info, $persched_info);

        }

        return array($perday_info,$hasLog);
    }

    function isSuspension($holiday='',$holidayInfo=''){
        $isSuspension = false;
        if($holiday){
            if($holidayInfo["holiday_type"]=="SUS") $isSuspension = true;
        }
        return $isSuspension;
    }

    function isValidSchedule($sched=''){
        $isValidSchedule = false;
        if($sched->num_rows() > 0){
            if($sched->row(0)->starttime == "00:00:00" && $sched->row(0)->endtime == "00:00:00") $isValidSchedule = false;
            else $isValidSchedule = true;
        }
        return $isValidSchedule;
    }

    function isCreditedHoliday($hasLogprev='',$isSuspension=''){
        $isCreditedHoliday = false;
        if($hasLogprev || $isSuspension)    $isCreditedHoliday = true;
        return $isCreditedHoliday;
    }

    function getPerSchedAtendanceDetails($empid='',$date='',$edata='',$rsched='',$sched_seq=0,$isCreditedHoliday=false,$holiday='',$tnt='teaching',$fixedday=TRUE,$hasLog=false){
        $persched_info = array();
        $sched_seq++;
        $hasleavecount = 0;
        $haswholedayleave = false;

        $sched_start    = $rsched->starttime;
        $sched_end      = $rsched->endtime; 
        $sched_type     = $rsched->leclab;
        $tardy_start    = $rsched->tardy_start;
        $absent_start   = $rsched->absent_start;
        $early_d        = $rsched->early_dismissal;


        list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$date,true);

        $service_credit = $this->attcompute->displayServiceCredit($empid,$sched_start,$sched_end,$date);

        $cs_app = $this->attcompute->displayChangeSchedApp($empid,$date);

        $pending = $this->attcompute->displayPendingApp($empid,$date);

        if($rsched->flexible != "YES"){
            
            list($login,$logout,$log_for) = $this->attcompute->displayLogTime($empid,$date,$sched_start,$sched_end,$edata,$sched_seq,$absent_start,$early_d);

            list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count)     = $this->attcompute->displayLeave($empid,$date,"",$sched_start,$sched_end);

            $absent = $this->attcompute->displayAbsent($sched_start,$sched_end,$login,$logout,$empid,$date,$early_d);

            if($holiday && $isCreditedHoliday) $absent = "";
            if ($vl >= 1 || $el >= 1 || $sl >= 1 || $ob >= 1 || $ol >= 1 || $service_credit >= 1){
                $absent = "";
                $haswholedayleave = true;
            }
            if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $ol >= 1 || $service_credit > 0){
                $absent = "";
                $hasleavecount++;
            }
            if($abs_count >= 1) $haswholedayleave = true;

            if($tnt=='teaching'){
                list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($sched_start,$sched_end,$tardy_start,$login,$logout,$sched_type,$absent);
                list($utlec,$utlab,$utadmin) = $this->attcompute->computeUndertime($sched_start,$sched_end,$tardy_start,$login,$logout,$sched_type,$absent);
            }else{
                $lateutlab = $lateutadmin = $utlab = $utadmin = $tschedlec = $tschedlab = $tschedadmin = '';
                $lateutlec = $this->attcompute->displayLateUTNT($sched_start,$sched_end,$login,$logout,$absent,'',$tardy_start);
                $utlec  = $this->attcompute->computeUndertimeNT($sched_start,$sched_end,$login,$logout,$absent,'',$tardy_start);
            }

            if ($this->attcompute->exp_time($utadmin) >= 14400) $utadmin = "4:00";
            elseif ($this->attcompute->exp_time($utlec) >= 14400) $utlec = "4:00";
            elseif ($this->attcompute->exp_time($utlab) >= 14400) $utlab = "4:00";

            if ($this->attcompute->exp_time($lateutlec) >= 14400) $lateutlec = "4:00";
            elseif ($this->attcompute->exp_time($lateutlab) >= 14400) $lateutlab = "4:00";
            elseif ($this->attcompute->exp_time($lateutadmin) >= 14400) $lateutadmin = "4:00";

            if($el || $vl || $sl || $ob || $service_credit || ($holiday && $isCreditedHoliday)){
                 $lateutlec = $lateutlab = $lateutadmin = $tschedlec = $tschedlab = $tschedadmin = "";
                 $utlec = $utlab = $utadmin = "";
            }

            if($ol && $ol != "CORRECTION" ) $login = $logout = "";

            if(($lateutlec || $lateutlab || $lateutadmin) && date("H:i",strtotime($sched_start)) < date("H:i",strtotime($login)) ) $hasLate = 1;
            else    $hasLate = 0;

            if(($utlec || $utlab || $utadmin) && date("H:i",strtotime($logout)) < date("H:i",strtotime($sched_end)) ) $hasUT = 1;
            else    $hasUT = 0;

            if(!$fixedday && $ol) $absent = $tschedlec = $tschedlab = $tschedadmin = '';

            if($tschedlec || $tschedlab || $tschedadmin || $absent) $isAbsent = 1;
            else $isAbsent = 0;

            $persched_info = array(
                    'sched_start'   => $sched_start,
                    'sched_end'     => $sched_end,
                    'sched_type'    => $sched_type,
                    'absent_start'  => $absent_start,
                    'early_d'       => $early_d,
                    'flexi'         => $rsched->flexible,
                    'login'         => $login,
                    'logout'        => $logout,
                    'lateut_lec'    => $lateutlec,
                    'lateut_lab'    => $lateutlab,
                    'lateut_admin'  => $lateutadmin,
                    'ut_lec'        => $utlec,
                    'ut_lab'        => $utlab,
                    'ut_admin'      => $utadmin,
                    'absent'        => $absent,
                    'deduc_lec'     => $tschedlec,
                    'deduc_lab'     => $tschedlab,
                    'deduc_admin'   => $tschedadmin,
                    'otreg'         => $otreg,
                    'otrest'        => $otrest,
                    'othol'         => $othol,
                    'vl'            => $vl,
                    'el'            => $el,
                    'sl'            => $sl,
                    'other'         => $ol,
                    'ob'        => $ob,
                    'service_credit'=> $service_credit,
                    'cs_app'        => $cs_app,
                    'pending'       => $pending,
                    'ol'            => $ol,
                    'oltype'            => $oltype,
                    'hasleavecount' => $hasleavecount ,
                    'haswholedayleave' => $haswholedayleave,
                    'hasLate'       => $hasLate,
                    'hasUT'         => $hasUT,
                    'isAbsent'      => $isAbsent

                );




        }else{

            $getLog = $this->attcompute->getLogsPerDay($empid,$date,$edata,true);
            $log = array();
            if(count($getLog) > 1) $log[] = $getLog[0];
            else                   $log = $getLog; 

            list($el,$vl,$sl,$ol,$oltype,$ob)             = $this->attcompute->displayLeave($empid,$date);

            $count_leave = $vl > 0 ? $vl : ( $el > 0 ? $el : ( $sl > 0 ? $sl : ( $ob > 0 ? $ob : ( $service_credit > 0 ? $service_credit : 0 ) ) ) ) ;

            $absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$date,$sched_type,$rsched->breaktime, $count_leave);


            if($holiday && $isCreditedHoliday) $absent = "";

            if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $ol >= 1 || $service_credit > 0){
                $absent = "";
            }

            if($tnt=='teaching'){
                $lateutlec = $lateutlab = $lateutadmin = '';
                list($utlec,$utlab,$utadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUTFlexi($log,$rsched->hours,$rsched->mode,$sched_type,$absent,$rsched->breaktime, $count_leave);
            }else{
                $lateutlec = $lateutlab = $lateutadmin = $utlab = $utadmin = $tschedlec = $tschedlab = $tschedadmin = '';
                $utlec = $this->attcompute->displayLateUTNTFlexi($log,$rsched->hours,$rsched->mode,$absent,$rsched->breaktime, $count_leave);
            }

            if($el >= 1 || $vl >= 1 || $sl >= 1 || $ob >= 1 || $ol >= 1 || $service_credit >= 1 || ($holiday && $isCreditedHoliday)){
                 $utlec = $utlab = $utadmin = $tschedlec = $tschedlab = $tschedadmin = "";
            }

            $login = $logout = "";
            $arr_logs = array();
            if(count($log) > 0){
                for($i = 0;$i < count($log);$i++){
                    $login = $log[$i][0];
                    $logout = $log[$i][1];

                    if($login=='0000-00-00 00:00:00') $login = "";
                    if($logout=='0000-00-00 00:00:00') $logout = "";

                    array_push($arr_logs, array('login'=>$login,'logout'=>$logout));
                }
            }

            if(($lateutlec || $lateutlab || $lateutadmin) && date("H:i",strtotime($sched_start)) < date("H:i",strtotime($login)) ) $hasLate = 1;
            else    $hasLate = 0;

            if(($utlec || $utlab || $utadmin) && date("H:i",strtotime($logout)) < date("H:i",strtotime($sched_end)) ) $hasUT = 1;
            else    $hasUT = 0;

            if(!$fixedday && !$ol) $absent = $tschedlec = $tschedlab = $tschedadmin = '';

            if($tschedlec || $tschedlab || $tschedadmin || $absent) $isAbsent = 1;
            else $isAbsent = 0;


            $persched_info = array(
                    'sched_start'   => $sched_start,
                    'sched_end'     => $sched_end,
                    'sched_type'    => $sched_type,
                    'absent_start'  => $absent_start,
                    'early_d'       => $early_d,
                    'flexi'         => $rsched->flexible,
                    'logs'          => $arr_logs,
                    'lateut_lec'    => $lateutlec,
                    'lateut_lab'    => $lateutlab,
                    'lateut_admin'  => $lateutadmin,
                    'ut_lec'        => $utlec,
                    'ut_lab'        => $utlab,
                    'ut_admin'      => $utadmin,
                    'absent'        => $absent,
                    'deduc_lec'     => $tschedlec,
                    'deduc_lab'     => $tschedlab,
                    'deduc_admin'   => $tschedadmin,
                    'otreg'         => $otreg,
                    'otrest'        => $otrest,
                    'othol'         => $othol,
                    'vl'            => $vl,
                    'el'            => $el,
                    'sl'            => $sl,
                    'other'         => $ol,
                    'ob'        => $ob,
                    'service_credit'=> $service_credit,
                    'cs_app'        => $cs_app,
                    'pending'       => $pending,
                    'ol'            => $ol,
                    'oltype'            => $oltype,
                    'hasleavecount' => $hasleavecount ,
                    'haswholedayleave' => $haswholedayleave,
                    'hasLate'       => $hasLate,
                    'hasUT'         => $hasUT,
                    'isAbsent'      => $isAbsent

                );
        }

        if(!$hasLog){
            $hasOL = $ol ? ($ol != 'CORRECTION' ? true : false) : false; 
            if((!$tschedadmin && !$absent) || $hasOL) $hasLog = true;
        }

        return array($persched_info,$hasLog);
    }

    function getNoSchedAtendanceDetails($empid='',$date='',$edata=''){
        $persched_info = array();

        $log = $this->attcompute->getLogsPerDay($empid,$date,$edata);

        list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$date,false);

        $pending = $this->attcompute->displayPendingApp($empid,$date);

        $cs_app = $this->attcompute->displayChangeSchedApp($empid,$date);

        list($el,$vl,$sl,$ol,$oltype,$ob)     = $this->attcompute->displayLeave($empid,$date);

        $service_credit = $this->attcompute->displayServiceCredit($empid,'','',$date);

        $login = $logout = "";
        $arr_logs = array();
        if(count($log) > 0){
            for($i = 0;$i < count($log);$i++){
                $login = $log[$i][0];
                $logout = $log[$i][1];

                if($login=='0000-00-00 00:00:00') $login = "";
                if($logout=='0000-00-00 00:00:00') $logout = "";

                array_push($arr_logs, array('login'=>$login,'logout'=>$logout));
            }
        }

        $persched_info = array( 
                    'nosched'       => 1,
                    'logs'          => $arr_logs,
                    'otreg'         => $otreg,
                    'otrest'        => $otrest,
                    'othol'         => $othol,
                    'vl'            => $vl,
                    'sl'            => $sl,
                    'other'         => $el,
                    'service_credit'=> $service_credit,
                    'cs_app'        => $cs_app,
                    'pending'       => $pending,
                    'ol'            => $ol,
                    'hasLate'       => 0,
                    'isAbsent'      => 0

                );

        return $persched_info;

    }

    public function generateYearEndReport(){
        $data = $this->input->post();
        $data["emplist"] = array();
        if($data["employeeid"]){
            foreach($data["employeeid"] as $empid){
                $salary = $this->extensions->getEmployeeLatestSalary($empid);
                if(isset($salary[0])){
                    $basic_pay = $salary[0];
                    $data["emplist"][$empid]["fullname"] = $this->extensions->getEmployeeName($empid);
                    $data["emplist"][$empid]["basic_pay"] = $basic_pay;
                    $_13th_month = $this->payroll->getEmployeeIncome($empid, "25", "semimonthly", $data["rep_quarter"]);
                    $_14th_month = $this->payroll->getEmployeeIncome($empid, "25", "semimonthly", $data["rep_quarter"]);
                    $year_end = $this->payroll->getEmployeeIncome($empid, "5", "semimonthly", $data["rep_quarter"]);
                    $data["emplist"][$empid]['incomes'][26] = ($_13th_month) ? $_13th_month->row(0)->amount : 0;
                    $data["emplist"][$empid]['incomes'][25] = ($_14th_month) ? $_14th_month->row(0)->amount : 0;
                    $data["emplist"][$empid]['incomes'][5] = ($year_end) ? $year_end->row(0)->amount : 0;
                }

            }
        }
        // echo "<pre>"; print_r($data); die;
        $this->load->view('reports_excel/yearEndReport',$data);
    }
}

/* End of file employee_.php */
/* Location: ./application/controllers/reports_.php */
