<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Approval extends CI_Model {

    public function selectAllEmpTable($tbl='', $department='', $office='', $employeeid='')
    {	
    	$Bwc = $Cwc = '';

    	if($department){
    		$Bwc .= " AND b.deptid = '$department'";
    		$Cwc .= " AND c.deptid = '$department'";
    	}

    	if($office){
    		$Bwc .= " AND b.office = '$office'";
    		$Cwc .= " AND c.office = '$office'";
    	}

    	if($employeeid && $employeeid !== 'null'){
    		$Bwc .= " AND b.employeeid IN ($employeeid)";
    		$Cwc .= " AND c.employeeid IN ($employeeid)";
    	}
    	if($tbl == "employee_work_history_related" ||  $tbl == "employee_eligibilities" || $tbl == "employee_subj_competent_to_teach"){
    		return $this->db->query("SELECT DISTINCT *, a.remarks as remarks FROM $tbl a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE  status = 'PENDING'  $Bwc ORDER BY b.lname")->result_array();
    	}else if($tbl == "employee_pts"){
    		return $this->db->query("SELECT DISTINCT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.datet, a.organizer, a.status, a.location, a.dra_remarks, a.other_title, c.* FROM employee_pts a LEFT JOIN reports_item b ON b.level = a.venue INNER JOIN employee c ON a.employeeid = c.employeeid WHERE a.status ='PENDING'  $Cwc")->result_array();
    	}else if($tbl == "employee_pts_pdp1"){
    		return $this->db->query("SELECT DISTINCT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.datet, a.seminar_title, a.location, a.regfee, a.transfee, a.accfee, a.total, a.dra_remarks, c.* FROM employee_pts_pdp1 a LEFT JOIN reports_item b ON b.level = a.venue INNER JOIN employee c ON a.employeeid = c.employeeid WHERE a.status = 'PENDING' AND a.is201 = 'YES' $Cwc")->result_array();

    	}else if($tbl == "employee_pts_pdp2"){
    		return $this->db->query("SELECT DISTINCT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.datet, a.organizer, a.status, a.location, a.dra_remarks, a.other_title, c.* FROM employee_pts_pdp2 a LEFT JOIN reports_item b ON b.level = a.venue INNER JOIN employee c ON a.employeeid = c.employeeid WHERE a.status = 'PENDING'  $Cwc")->result_array();

    	}else if($tbl == "employee_pts_pdp3"){
    		return $this->db->query("SELECT DISTINCT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.datet, a.organizer, a.status, a.location, a.dra_remarks, a.other_title, c.* FROM employee_pts_pdp3 a LEFT JOIN reports_item b ON b.level = a.venue  INNER JOIN employee c ON a.employeeid = c.employeeid WHERE a.status = 'PENDING'  $Cwc")->result_array();

    	}else if($tbl == "employee_pgd"){
    		return $this->db->query("SELECT DISTINCT e.publication,e.title as titles,e.publisher,e.datef,e.type, e.id as id, r.level as level, e.status, e.dra_remarks, c.* from employee_pgd e LEFT JOIN reports_item r ON e.publication = r.ID INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $Cwc")->result_array();

    	}else if($tbl == "employee_education"){
            return $this->db->query("SELECT DISTINCT school,course,units,year_graduated,date_graduated,datefrom,dateto,e.educ_level,r.level , e.id, e.status, a.description as schoolDesc, a.schoolid, e.dra_remarks, c.* from employee_education e INNER JOIN reports_item r ON e.educ_level = r.level INNER JOIN code_school a ON e.schoolid = a.schoolid   INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $Cwc")->result_array();

        }else if($tbl == "employee_emergencyContact"){
            return $this->db->query("SELECT DISTINCT *, a.mobile as mobile FROM $tbl a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE  status = 'PENDING'  $Bwc ORDER BY b.lname")->result_array();

        }else{
    		return $this->db->query("SELECT DISTINCT * FROM $tbl a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE  status = 'PENDING'  $Bwc ORDER BY b.lname")->result_array();
    	}
        
    }

    public function ifHasPendingRequest(){
        $total = 0;
        $utwc = '';
        $utdept = $this->session->userdata("department");
        $utoffice = $this->session->userdata("office");
        if($this->session->userdata("usertype") == "ADMIN"){
          if($utdept && $utdept != 'all') $utwc .= " AND  FIND_IN_SET (c.deptid, '$utdept')";
          if($utoffice && $utoffice != 'all') $utwc .= " AND  FIND_IN_SET (c.office, '$utoffice')";
          if(($utdept && $utdept != 'all') && ($utoffice && $utoffice != 'all')) $utwc = " AND  (FIND_IN_SET (c.deptid, '$utdept') OR FIND_IN_SET (c.office, '$utoffice'))";
          if(!$utdept && !$utoffice) $utwc =  " AND c.employeeid = 'nosresult'";
          // $usercampus =  $this->extras->getCampusUser();
          // if($usercampus) $utwc .= " AND FIND_IN_SET (c.campusid,'$usercampus') ";
        }
        foreach (Globals::dataRequestApprovalList() as $tbl => $value){
            if($tbl == "employee_eligibilities"){
                $query = $this->db->query("SELECT COUNT(e.id) AS c_row from employee_eligibilities e INNER JOIN reports_item r on e.description = r.id INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $utwc")->row()->c_row;
            }else if($tbl == "employee_pgd"){
                $query = $this->db->query("SELECT COUNT(e.id) AS c_row FROM employee_pgd e LEFT JOIN reports_item r ON e.publication = r.ID INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $utwc")->row()->c_row;
            }else if($tbl == "employee_language"){
                $query =  $this->db->query("SELECT COUNT(e.id) AS c_row FROM employee_language e LEFT JOIN code_language b ON e.languageid=b.id INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $utwc")->row()->c_row;
            }else if($tbl == "employee_education"){
                $query =  $this->db->query("SELECT COUNT(e.id) AS c_row from employee_education e INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $utwc")->row()->c_row;
            }else if($tbl == "employee_emergencyContact"){
                $query =  $this->db->query("SELECT COUNT(e.id) AS c_row from employee_emergencyContact e INNER JOIN reports_item r on e.type = r.id INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $utwc")->row()->c_row;
            }else if($tbl == "employee_scholarship"){
                $query =  $this->db->query("SELECT COUNT(e.id) AS c_row from employee_scholarship e INNER JOIN reports_item r on e.type_of_scho = r.ID INNER JOIN employee c ON e.employeeid = c.employeeid WHERE e.status = 'PENDING' $utwc")->row()->c_row;
            }else{
                $query = $this->db->query("SELECT COUNT(a.id) AS c_row FROM $tbl a INNER JOIN employee c ON a.employeeid = c.employeeid WHERE a.status = 'PENDING' $utwc")->row()->c_row;
            }
            // echo "<pre>"; print_r($this->db->last_query());
            // if($query > 0){
            //     echo "<pre>"; print_r($this->db->last_query()); 
            // }
            $total = $total + $query;
        }
        return $total;
    }

   

}
/* End of file employee.php */
/* Location: ./application/models/employee.php */
