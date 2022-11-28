<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class reportsitem extends CI_Model {

	function saveEducBackground($ID,$reportcode="", $description="", $educlevel="", $educpoints=""){
		$query="";
        $points = '';
        if($ID){
            if($educpoints) $points .= ", points='$educpoints'";
            $level = $this->db->query("SELECT level from reports_item where ID = '$ID'")->row()->level;
            $query   = $this->db->query("
                            UPDATE reports_item SET level='$educlevel' $points WHERE ID='$ID';
                            ");
            if($query){
                $tbl = '';
                foreach(Globals::reportsItemTableList() as $categoryCode => $categoryTable){
                    if($categoryCode == $reportcode) $tbl = $categoryTable;
                }
                if($tbl == "employee_pts" || $tbl == "employee_pts_pdp1" || $tbl == "employee_pts_pdp2" || $tbl == "employee_pts_pdp3"){
                    $this->db->query("UPDATE $tbl SET venue = '$educlevel' WHERE venue = '$level'");
                }else if($tbl == "employee_pgd"){
                    $this->db->query("UPDATE $tbl SET publication = '$educlevel' WHERE publication = '$level'");
                }else if($tbl == "employee_awardsrecog"){
                    $this->db->query("UPDATE $tbl SET award = '$educlevel' WHERE award = '$level'");
                }else if($tbl == "employee_scholarship"){
                   $this->db->query("UPDATE $tbl SET type_of_scho = '$educlevel' WHERE type_of_scho = '$level'");
                }else if($tbl == "employee_education"){
                    $this->db->query("UPDATE $tbl SET educ_level = '$educlevel' WHERE educ_level = '$level'");
                }else if($tbl == "employee_eligibilities"){
                    $this->db->query("UPDATE $tbl SET description = '$educlevel' WHERE description = '$level'");
                }else if($tbl == "employee_emergencyContact"){
                    $this->db->query("UPDATE $tbl SET type = '$educlevel' WHERE type = '$level'");
                }else{

                }
            }
        }else{
            $query   = $this->db->query("
                            INSERT INTO reports_item (reportcode,Description,level,points)
                                VALUES ('$reportcode','$description','$educlevel','$educpoints')
                            ");
        }
		return $query;
	   
	}

    function saveSubjCompetentToTeach($ID="",$subj_code="", $description="", $remarks=""){
        $query="";
        // $exists = $this->checkSubjCodeExists($subj_code);
            if($ID){
                    $level = $this->db->query("SELECT level from reports_item where ID = '$ID'")->row()->level;
                    $query   = $this->db->query("
                                    UPDATE code_subj_competent_to_teach SET subj_code='$subj_code', description='$description', remarks='$remarks' WHERE id='$ID';
                                    ");
            }
            else{
                $query   = $this->db->query("
                                INSERT INTO code_subj_competent_to_teach (subj_code,description,remarks)
                                    VALUES ('$subj_code','$description','$remarks')
                                ");
            }
        return $query;
    }

    function checkSubjCodeExists($subj_code=""){
        $exists = $this->db->query("SELECT id FROM code_subj_competent_to_teach where subj_code='$subj_code'");
        return $exists;
    }

	function getReportList($code){
        if($code == "SCTT"){
            $query = $this->db->query("
            				SELECT DISTINCT * FROM code_subj_competent_to_teach WHERE status='1';
            			")->result();
        }else{
            $query = $this->db->query("
                            SELECT DISTINCT * FROM reports_item WHERE reportcode='$code';
                        ")->result();
        }
        return $query;
    }

    function getSchoolYearData($id=''){
        $wC = '';
        if($id) $wC .= " AND id = '$id'";
        return $this->db->query("SELECT * FROM school_year WHERE 1 $wC")->result();
    }

    function getExistingSchoolYear(){
        return $this->db->query("SELECT  sy from school_year")->result_array();
    }

    function saveSchoolYear($data, $id=''){
        if($id != ''){
            $query = $this->db->update('school_year', $data, "id = '$id'");
        }else{
            $query = $this->db->insert('school_year', $data);    
        }
        return $query;
    }

    function deleteSchoolYear($id){
        $query = $this->db->delete('school_year', array('id' => $id)); 
    }

    function getReportData($id){
        
        $query = $this->db->query("
        				SELECT DISTINCT ID,`level`,points FROM reports_item WHERE ID='$id';
        			")->result();
        return $query;
    }

    function getSCTTData($id){
        
        $query = $this->db->query("
                        SELECT DISTINCT id,subj_code,description,remarks FROM code_subj_competent_to_teach WHERE status='1' AND id='$id'
                    ")->result();
        return $query;
    }

    function deleteReportData($id){
        
        $query = $this->db->query("
                        UPDATE code_subj_competent_to_teach SET status='0' WHERE id='$id'; 
                    ");
        return $query;
    }

     function deleteSCTTData($id){
        
        $query = $this->db->query("
                        DELETE FROM reports_item WHERE ID='$id' ");
                    #    SELECT DISTINCT ID,`level`,points FROM reports_item WHERE ID='$id';
                   
        return $query;
    }

    function checkReportsItemCategory($tbl="", $id=""){
        $count = 0;
        if($tbl != "employee_subj_competent_to_teach"){
            $level = $this->db->query("SELECT level from reports_item where ID = '$id'")->row()->level;
            if($tbl == "employee_pts" || $tbl == "employee_pts_pdp1" || $tbl == "employee_pts_pdp2" || $tbl == "employee_pts_pdp3"){
                $count = $this->db->query("SELECT DISTINCT * FROM $tbl a INNER JOIN reports_item b ON b.level = a.venue WHERE a.venue ='$level'")->num_rows();
            }else if($tbl == "employee_pgd"){
                $count =  $this->db->query("SELECT DISTINCT * FROM employee_pgd e INNER JOIN reports_item r ON e.publication = r.level WHERE e.publication = '$level'")->num_rows();
            }else if($tbl == "employee_awardsrecog"){
                $count =  $this->db->query("SELECT DISTINCT * FROM employee_awardsrecog e INNER JOIN reports_item r ON e.award = r.level WHERE e.award = '$level'")->num_rows();
            }else if($tbl == "employee_scholarship"){
                $count =  $this->db->query("SELECT DISTINCT * FROM employee_scholarship e INNER JOIN reports_item r ON e.type_of_scho = r.level WHERE e.type_of_scho = '$level'")->num_rows();
            }else if($tbl == "employee_education"){
                $count =  $this->db->query("SELECT DISTINCT * FROM employee_education e INNER JOIN reports_item r ON e.educ_level = r.level  WHERE e.educ_level = '$level'")->num_rows();
            }else if($tbl == "employee_eligibilities"){
                $count =  $this->db->query("SELECT DISTINCT * FROM employee_eligibilities e INNER JOIN reports_item r ON e.description = r.level  WHERE e.description = '$level'")->num_rows();
                // echo "<pre>"; print_r($this->db->last_query());
            }else if($tbl == "employee_emergencyContact"){
                $count =  $this->db->query("SELECT DISTINCT * FROM employee_emergencyContact e INNER JOIN reports_item r ON e.type = r.level  WHERE e.description = '$level'")->num_rows();
            }else{
                $count = 0;
            }
        }
        else{
            $count =  $this->db->query("SELECT * FROM employee_subj_competent_to_teach a INNER JOIN code_subj_competent_to_teach b ON a.subj_id=b.id WHERE a.subj_id='$id'")->num_rows();
        }
        return $count;
        
    }

	function getHistory($user=""){ 
        $wC = "";
        if($user)   $wC = " WHERE `user`='$user'";
        $query = $this->db->query("
        				SELECT id,GROUP_CONCAT(deptid) as deptids,datefrom,dateto,timefrom,timeto,`event`,venue,posted_until,`user`,date_created
        				FROM announcements
        				$wC
        				GROUP BY datefrom,dateto,timefrom,timeto,`event`,venue,posted_until,`user`,date_created
        			")->result();
        return $query;
    }

	function getAnnouncements($deptid="", $userid=""){
		// if(!$userid) $userid = $this->session->userdata('username');

		// $dept_res = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$userid'")->result();
		// $deptid = $dept_res->num_rows() > 0 ? $dept_res->row(0)->deptid : "";

		$wC = "";
        if($deptid) $wC = " AND deptid='$deptid'";

        $query = $this->db->query("
        				SELECT * FROM announcements
        				WHERE SUBSTR(datefrom,1,7) = SUBSTR(CURRENT_DATE,1,7) $wC
        				ORDER BY datefrom
        		")->result();
        return $query;
    }  

}