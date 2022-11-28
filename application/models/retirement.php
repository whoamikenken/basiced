<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retirement extends CI_Model {

    public function employeeRetiree($department='', $office='', $month='', $status='1')
    {	
        $where = "";
        $datenow = date("Y-m-d");
        if($department) $where .= " AND deptid = '$department'";
        if($office) $where .= " AND office = '$office'";
        if($status != "all"){
          if($status=="1"){
            $where .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1') AND employmentstat != 'R'";
          }
          if($status=="0"){
            $where .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0') OR employmentstat = 'R'";
          }
        }
        if($month) $where .= " AND DATE_FORMAT(bdate, '%m') = '$month' ";
        $query = $this->db->query("SELECT employeeid, CONCAT(lname,', ',fname,' ',mname) AS fullname, bdate, deptid, office, age, dateposition, employmentstat, positionid FROM employee WHERE age >= '59' AND (age != 'NaN' AND age != '') $where");
        return $query;
    }

    public function saveRetirement($empid, $dateresigned, $reason){
        return $this->db->query("UPDATE employee SET employmentstat = 'R', dateresigned2 = '$dateresigned', dateresigned='$dateresigned', resigned_reason='$reason', isactive = '0' WHERE employeeid = '$empid'");
    }
}
/* End of file employee.php */
/* Location: ./application/models/employee.php */
