<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Approval_ extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model("approval");
    }

    public function index()
    {
        # do nothing
    }

    function loadRequestApproval(){
        $toks = $this->input->post("toks");
        $data['tbl'] = $toks ? $this->gibberish->decrypt( $this->input->post("tbl"), $toks ) : $this->input->post("tbl");
        $data['title'] = $toks ? $this->gibberish->decrypt( $this->input->post("title"), $toks ) : $this->input->post("title");
        $data['department'] = $toks ? $this->gibberish->decrypt( $this->input->post("department"), $toks ) : $this->input->post("department");
        $data['office'] = $toks ? $this->gibberish->decrypt( $this->input->post("office"), $toks ) : $this->input->post("office");
        $data['employeeid'] = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
        if($data['employeeid'] && $data['employeeid'] !== 'null'){
            $employeeid = explode(',', $data['employeeid']);
            unset($data['employeeid']);
            foreach ($employeeid as $key => $value){
                $value = str_replace("'", "",$value);
                if($key == 0) $data['employeeid'] = "'".$value."'";
                else $data['employeeid'] .= ",'".$value."'";
            }
        }
        // $data['tblData'] = $this->approval->selectAllEmpTable($data['tbl'], $data['department'], $data['office'], $data['employeeid']);
        // if($data['tblData']) $data['tblData'] = Globals::resultarray_XHEP($data['tblData']);
        $this->load->view("data_approval/approval_".$data['tbl'], $data);
    }

    function loadRequestApprovalSSP(){
        
        $toks = $this->input->post("toks");
        $tbl = $toks ? $this->gibberish->decrypt( $this->input->post("tbl"), $toks ) : $this->input->post("tbl");
        $department = $toks ? $this->gibberish->decrypt( $this->input->post("department"), $toks ) : $this->input->post("department");
        $office = $toks ? $this->gibberish->decrypt( $this->input->post("office"), $toks ) : $this->input->post("office");
        $employeeid = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
        if($employeeid && $employeeid !== 'null'){
            $employeeid = explode(',', $employeeid);
            foreach ($employeeid as $key => $value){
                $value = str_replace("'", "",$value);
                if($key == 0) $employeeid = "'".$value."'";
                else $employeeid .= ",'".$value."'";
            }
        }
        $output = $this->$tbl($tbl, $department, $office, $employeeid);
        

        echo json_encode($output);
        // if($data['tblData']) $data['tblData'] = Globals::resultarray_XHEP($data['tblData']);
        // $this->load->view("data_approval/approval_".$data['tbl'], $data);
    }

    function countPendingRequest(){
        echo $this->approval->ifHasPendingRequest();
    }

    function employee_eligibilities($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // $content = $value['content'];
            // $mime = $value['mime'];
            // $filename = $value['filename'];
            // // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            // if($value['content'] == '' && $value['mime'] == ''){
            //     $value['content'] = $content;
            //     $value['mime'] = $mime;
            //     $value['filename'] = $filename;
            // }
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_eligibility' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '".$fullname."'  employeeid='".$value['employeeid']."' lnumber='".$value['license_number']."' date_issued = '".$value['date_issued']."' date_expired='".$value['date_expired']."' description='".$value['description']."' remarks='".$value['remarks']."' mime='".$value['mime']."' content='".$value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname, $value['description'], $value['license_number'], $value['date_issued'], $value['date_expired'], $value['remarks'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_pts_pdp2($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_pts_pdp2' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '".$fullname."'  employeeid='".$value['employeeid']."' title_id='".$value['title_id']."' datef='".$value['datef']."' datet='".$value['datet']."' organizer='".$value['organizer']."' venue='".$value['venue']."' location='".$value['location']."' mime='".$value['mime']."' content='".$value['content']."' other_title='". $value['other_title']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $value['title_id'] = ($value['title_id'] == 'others' ? $value['other_title'] : $value['title_id']);
            $data[] = array($checkbox, $fullname, $value['title_id'], $value['datef'], $value['organizer'], $value['location'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }


    function employee_family($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_family' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '".$fullname."'  employeeid='".$value['employeeid']."' dob = '".$value['bdate']."' reldata='".$value['relation']."' relative = '".$value['name']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname, $value['name'], $this->extras->getrelation($value['relation']), "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_emergencyContact($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_emergency_contact' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '".$fullname."'  employeeid='".$value['employeeid']."' relation='".$value['relation']."' relative ='".$value['name']."' mobile='".$value['mobile']."' homeNo = '".$value['homeNo']."' officeNo='".$value['officeNo']."' type='".$value['type']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname, $value['name'],$this->extras->getrelation($value['relation']),$value['mobile'],$value['homeNo'],$value['officeNo'],$value['type'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_education($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            $value['filename'] = $value['content'] = $value['mime'] = '';
            // // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_education' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '".$fullname."'  employeeid='".$value['employeeid']."' schooldesc = '".$value['schoolid'] ."' educ_level='".$value['educ_level'] ."' course='".$value['course'] ."' units='".$value['units'] ."' date_graduated='".$value['date_graduated'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname, $value['schoolDesc'], $value['educ_level'], $value['course'], ($value['units'] != 0 ? $value['units'] : ''),$value['date_graduated'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_subj_competent_to_teach($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = " <div style='float: right'>
                            <a class='btn btn-primary edit_sctt' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '".$fullname."'  employeeid='".$value['employeeid']."' subj_id='".$value['subj_id'] ."' remarks='".$value['remarks'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname, $this->extras->getComTeachDesc($value['subj_id']), $value['remarks'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_work_history_related($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_workhistoryrelated' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' position='".$value['position'] ."' company='".$value['company'] ."' remarks='".$value['remarks'] ."' salary='".$value['salary'] ."' reason='".$value['reason'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['position'], $value['company'], $value['remarks'],(is_numeric($value['salary']) ? number_format($value['salary'], 2) : $value['salary']), $value['reason'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_pts($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_pts' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' title='".$value['title_id'] ."' datef='".$value['datef'] ."' datet='".$value['datet'] ."' organizer='".$value['organizer'] ."' venue='".$value['venue'] ."' location='".$value['location'] ."' mime='". $value['mime']."' content='". $value['content']."' other_title='". $value['other_title']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $value['title_id'] = ($value['title_id'] == 'others' ? $value['other_title'] : $value['title_id']);
            $data[] = array($checkbox, $fullname,$value['title_id'], $value['datef'], $value['organizer'], $value['location'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_pts_pdp1($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_pts_pdp1' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' seminar_title='".$value['seminar_title'] ."' datef='".$value['datef'] ."' datet='".$value['datet'] ."' organizer='".$value['organizer'] ."' venue='".$value['venue'] ."' location='".$value['location'] ."' regfee='".$value['regfee'] ."' accfee='".$value['accfee'] ."' transfee='".$value['transfee'] ."' total='".$value['total'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['seminar_title'], $value['datef'].'-'.$value['datet'], $value['organizer'], $value['location'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_pts_pdp3($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_pts_pdp3' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' title_id='".$value['title_id'] ."' datef='".$value['datef'] ."' datet='".$value['datet'] ."' organizer='".$value['organizer'] ."' venue='".$value['venue'] ."' mime='". $value['mime']."' content='". $value['content']."' other_title='". $value['other_title']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $value['title_id'] = ($value['title_id'] == 'others' ? $value['other_title'] : $value['title_id']);
            $data[] = array($checkbox, $fullname,$value['title_id'],$value['datef'], $value['organizer'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_pgd($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_pgd' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' publication='".$value['publication'] ."' title='".$value['titles'] ."' publisher='".$value['publisher'] ."' type='".$value['type'] ."' datef='".$value['datef'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['publication'],$value['titles'], $value['publisher'],$value['type'], $value['datef'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_awardsrecog($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_ar' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' award='".$value['award'] ."' institution='".$value['institution'] ."' address='".$value['address'] ."'  datef='".$value['datef'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['award'],$value['institution'], $value['address'], $value['datef'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_scholarship($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_sho' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' type_of_scho='".$value['type_of_scho'] ."' gr_agency='".$value['gr_agency'] ."' prog_study='".$value['prog_study'] ."'  ins_scho='".$value['ins_scho'] ."' datef='".$value['datef'] ."' dateto='".$value['dateto'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['type_of_scho'],$value['gr_agency'], $value['prog_study'], $value['ins_scho'], $value['datef'].'-'.$value['dateto'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_resource($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_resource' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' topic='".$value['topic'] ."' organizer='".$value['organizer'] ."' venue='".$value['venue'] ."'  datef='".$value['datef'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['topic'],$value['organizer'], $value['venue'], $value['datef'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_proorg($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_org' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' name_org='".$value['name_org'] ."' position='".$value['position'] ."' datef='".$value['datef'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['name_org'],$value['position'], $value['datef'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_community($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_community' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' school='".$value['school'] ."' honor='".$value['honor'] ."' year_grad='".$value['year_grad'] ."'  mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['school'],$value['honor'], $value['year_grad'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function employee_administrative($tbl, $department, $office, $employeeid){
        $data = array();
        $tableData = $this->approval->selectAllEmpTable($tbl, $department, $office, $employeeid);
        $pendingTotal = $this->approval->selectAllEmpTable($tbl);
        foreach($tableData as $value){
            // list($value['filename'], $value['content'], $value['mime']) = $this->extensions->getEmployee201Files($tbl, $value['id']);
            $value['filename'] = $value['content'] = $value['mime'] = '';
            $fullname = $value['lname'].', '.$value['fname'].' '.$value['mname'];
            $checkbox = "<input type='checkbox' name='empCheck' id='empCheck' class='double-sized-cb empCheck' employeeid='".$value['employeeid']."' trid='".$value['id']."'>";
            $actions = "<div style='float: right'>
                            <a class='btn btn-primary edit_administrative' href='#modal-view' data-toggle='modal' dra_remarks = '".$value['dra_remarks']."'  tbl_id = '".$value['id']."'  empname = '". $value['lname'].', '.$value['fname'].' '.$value['mname'] ."'  employeeid='".$value['employeeid'] ."' positionf='".$value['positionf'] ."' department='".$value['department'] ."' datef='".$value['datef'] ."' mime='". $value['mime']."' content='". $value['content']."'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                            <a class='btn btn-warning delete_family tr_".$value['id']."_delete' tbl_id = '".$value['id']."' table='".$tbl."' employeeid='".$value['employeeid']."'><i class='glyphicon glyphicon-trash'></i></a>
                        </div>";
            $data[] = array($checkbox, $fullname,$value['positionf'],$value['department'], $value['datef'], "<a class='btn btn-danger update_status tr_".$value['id']."'>".$value['status']."</a>",$value['dra_remarks'], $actions, $value['id']);

        }

        $output = array(
            "recordsTotal" => count($pendingTotal),
            "recordsFiltered" => count($tableData),
            "data" => $data,
        );

        return $output;
    }

    function updateTableStatusBatch(){
        $toks = $this->input->post("toks");
        $table = $toks ? $this->gibberish->decrypt( $this->input->post("table"), $toks ) : $this->input->post("table");
        $idlist = $toks ? $this->gibberish->decrypt( $this->input->post("idlist"), $toks ) : $this->input->post("idlist");
        $approver = $toks ? $this->gibberish->decrypt( $this->input->post("approverid"), $toks ) : $this->input->post("approverid");
        $idlist = explode('~', $idlist);
        // echo "<pre>"; print_r($idlist); die;
        foreach ($idlist as $key => $id) {
            $res = $this->employee->updateTableStatus($table, $id, $approver);
        }
        echo $res;
    }

    function deleteTableStatusBatch(){
        $toks = $this->input->post("toks");
        $table = $toks ? $this->gibberish->decrypt( $this->input->post("table"), $toks ) : $this->input->post("table");
        $idlist = $toks ? $this->gibberish->decrypt( $this->input->post("idlist"), $toks ) : $this->input->post("idlist");
        $idlist = explode('~', $idlist);
        foreach ($idlist as $key => $id) {
            $res = $this->employee->deleteData($id, $table);
        }
        echo $res;
    }

    function loadFile(){
        $toks = $this->input->post("toks");
        $table = $toks ? $this->gibberish->decrypt( $this->input->post("table"), $toks ) : $this->input->post("table");
        $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
        $employeeid = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
        $loadedFile = $this->extensions->getEmployee201Files($table, $id, $employeeid);
        echo json_encode($loadedFile);
    }

    function checkAdminRemarks(){
        $toks = $this->input->post("toks");
        $counter = $tableHeaderCount = 0;
        $userid = $toks ? $this->gibberish->decrypt( $this->input->post("userid"), $toks ) : $this->input->post("userid");
        $table = '';
        foreach (Globals::dataRequestApprovalList() as $key => $value) {
            $tableData = $this->approval->selectAllEmpTable($key, '','', "'$userid'");
            $tableColumn = $this->columnTitle($key);
            if(count($tableData) > 0){
                $tableHeaderCount = 0;
                foreach ($tableData as $k => $v) {
                    if($v['dra_remarks'] != '' && $v['dra_remarks'] != null){
                        if($tableHeaderCount == 0){
                            $table .= '<br>';
                            $table .= ' <table class="table table-bordered table-hover">
                                            <thead>                      
                                                <tr style="background-color: #0072c6;">
                                                    <th><b>Category</b></th>';
                                                    foreach ($tableColumn as $columnHead => $ColumnValue) {
                                                        $table .= '<th ><b>'.$columnHead.'</b></th>';
                                                    }
                                                    $table .= ' 
                                                    <th><b>Admin Remark</b></th>
                                                </tr>
                                            </thead>
                                        <tbody>';
                        }
                        $tableHeaderCount++;
                        $counter++;
                        $table .= ' <tr class="testtooltip">
                                        <td>'.$value.'</td>
                                        ';
                                        foreach ($tableColumn as $columnHead => $ColumnValue) {
                                            if($ColumnValue == 'relation'){
                                                $table .= '<td> '.$this->extras->getrelation($v[$ColumnValue]).'</td>';
                                            }else if($ColumnValue == 'title_id' && $v[$ColumnValue] == 'other'){
                                                $table .= '<td> '.$v['other_title'].'</td>';
                                            }else{
                                                $table .= '<td> '.$v[$ColumnValue].'</td>';
                                            }
                                        }
                                    $table .='
                                        <td>'.$v['dra_remarks'].'</td>
                                    </tr>';
                    }
                }
                $table .= '</tbody></table>';
            }
        }
        
        if($counter == 0) $table = '';
        echo $table;
    }

    function columnTitle($table){
        $data['employee_family'] = array("Name" => "name", "Relation" => "relation", "Date of Birth" => "bdate");
        $data['employee_emergencyContact'] = array("Name" => "name", "Relation" => "relation", "Mobile #" => "mobile", "Home #" => "homeNo", "Office #" => "officeNo", "Type #" => "type");
        $data['employee_education'] = array("Name of School" => "schoolDesc", "Educational Level" => "educ_level", "Course" => "course", "Units" => "units", "Inclusive Years" => "date_graduated");
        $data['employee_eligibilities'] = array("Government Examination/ Professional Exam Taken" => "description", "License No." => "license_number", "Issued Date" => "date_issued", "Expiry Date" => "date_expired", "Remarks" => "remarks");
        $data['employee_subj_competent_to_teach'] = array("Subject Code" => "subj_code", "Remarks" => "remarks");
        $data['employee_work_history_related'] = array("Position Held" => "position", "Employer" => "company", "Inclusive Years" => "remarks" , "Reason For Leaving" => "reason");
        $data['employee_pts'] = array("Title" => "title_id", "Date" => "datef", "Organizer" => "organizer" , "Location" => "location");
        $data['employee_pts_pdp1'] = array("Seminar Title" => "seminar_title", "Location" => "location", "Date From" => "datef" , "Date To" => "datet", "Organizer" => "organizer");
        $data['employee_pts_pdp2'] = array("Title" => "title_id", "Date" => "datef", "Organizer" => "organizer" , "Location" => "location");
        $data['employee_pts_pdp3'] = array("Title" => "title_id", "Date" => "datef", "Organizer" => "organizer");
        $data['employee_pgd'] = array("Type of Publication" => "publication", "Title" => "title", "Publisher" => "publisher", "Date Published" => "datef", "Date Type of Authorship" => "type");
        $data['employee_awardsrecog'] = array("Type of Award" => "award", "Granting Agency / Org" => "institution", "Place" => "address", "Date Given" => "datef");
        $data['employee_scholarship'] = array("Type of Scholarship" => "type_of_scho", "Granting Agency" => "gr_agency", "Program of Study" => "prog_study", "Institution" => "ins_scho", "Date From" => "datef", "Date To" => "dateto");
        $data['employee_resource'] = array("Date" => "datef", "Topic" => "topic", "Organizer" => "organizer", "Venue" => "venue");
        $data['employee_proorg'] = array("Name of Organization" => "name_org", "Date" => "datef", "Position" => "position");
        $data['employee_community'] = array("Name of Organization" => "school", "Date" => "year_grad", "Nature of Involvement" => "honor");
        $data['employee_administrative'] = array("Position" => "positionf", "Department" => "department", "Inclusive Date" => "datef");

        return $data[$table];
    }
}

/* End of file approval_.php */
