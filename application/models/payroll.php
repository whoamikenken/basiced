<?php 
/**
 * @author Justin
 * @copyright 2015
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll extends CI_Model {
    
    /*
     * Payroll Deduction
     */
    function sssdeduc($salary = ""){
        $sss = $philhealth = $pagibig = 0;
       #$salary = preg_replace('/\D/', '', $salary);
        $query = $this->db->query("SELECT IFNULL((SELECT emp_ee AS sss FROM sss_deduction WHERE '$salary' BETWEEN compensationfrom AND compensationto LIMIT 1),0) AS sss, 
                                    IFNULL((SELECT emp_ee AS philhealth FROM philhealth_deduction WHERE '$salary' BETWEEN compensationfrom AND compensationto LIMIT 1),0) AS philhealth,
                                    IFNULL((SELECT emp_ee AS pagibig FROM hdmf_deduction WHERE '$salary' BETWEEN compensationfrom AND compensationto LIMIT 1),0) AS pagibig
                                  ")->result();
        foreach($query as $row){
            $sss        =   $row->sss;
            $philhealth =   $row->philhealth;
            $pagibig    =   $row->pagibig; 
        }
        return "$sss*$philhealth*$pagibig";
    }

    function ranksaving($eid, $type){
    return $this->db->query("UPDATE employee SET rank = '$type' WHERE employeeid = '$eid' ");
  }
    
    /*
     * Payroll Config 
     * Adding / Editing / Deleting
     */
     
     // Salary
    function esalary($data){
        $msg='';
        $data["monthly"] = str_replace(",", "", $data["monthly"]);
        $data["semimonthly"] = str_replace(",", "", $data["semimonthly"]);
        $data["daily"] = str_replace(",", "", $data["daily"]);
        $data["hourly"] = str_replace(",", "", $data["hourly"]);
        $data["minutely"] = str_replace(",", "", $data["minutely"]);
        $date_effective = $data['date_effective'];
        $this->deleteSameDateSalary($date_effective, $data['eid']);
        $user = $this->session->userdata("username");
        $hasProcessedPayroll = $this->checkProcessedPayroll($data['eid'],$data['date_effective']);

        if(!$hasProcessedPayroll){
            $query = $this->db->query("SELECT * FROM payroll_employee_salary WHERE employeeid='{$data['eid']}'");
            
            $base_id = $base_id_history = '';

            if($query->num_rows() == 0){
                $result = $this->insertEmpSalaryBase($data,$user);
                if(!$result) $msg .= 'Failed to save salary.'; 
            }else{
                $result = $this->updateEmpSalaryBase($data,$query->row(0)->monthly,$user,true);
                if(!$result) $msg .= 'Failed to update salary.';
            }
           
            $data['sss'] = $data['sss'] == '' ? 'NULL' : $data['sss'];
            $data['philhealth'] = $data['philhealth'] == '' ? 'NULL' : $data['philhealth'];
            $data['pagibig'] = $data['pagibig'] == '' ? 'NULL' : $data['pagibig'];

            $deducarray = array(
                                array($data['eid'],$data['ssdesc'],$data['sssid'],$data['sss'],$data['sched'],(isset($data['sssq']) ? $data['sssq'] : 0),'HIDDEN'),
                                array($data['eid'],'SSS_ER',$data['sssid'],$data['sss_er'],$data['sched'],(isset($data['sssq']) ? $data['sssq'] : 0),'HIDDEN'),
                                array($data['eid'],$data['phdesc'],$data['philhealthid'],$data['philhealth'],$data['sched'],(isset($data['philhealthq']) ? $data['philhealthq'] : 0),'HIDDEN'),
                                array($data['eid'],'PHILHEALTH_ER',$data['philhealthid'],$data['philhealth_er'],$data['sched'],(isset($data['philhealthq']) ? $data['philhealthq'] : 0),'HIDDEN'),
                                array($data['eid'],$data['pagibigdesc'],$data['pagibigid'],$data['pagibig'],$data['sched'],(isset($data['pagibigq']) ? $data['pagibigq'] : 0),'HIDDEN'),
                                array($data['eid'],'PAGIBIG_ER',$data['pagibigid'],$data['pagibig_er'],$data['sched'],(isset($data['pagibigq']) ? $data['pagibigq'] : 0),'HIDDEN')
                               );

           

            $query = $this->db->query("SELECT * FROM employee_deduction WHERE visibility='HIDDEN' AND employeeid='{$data['eid']}'");
            // if($query->num_rows() == 0){
            //     foreach($deducarray as $key=>$row){
            //         $this->db->query("INSERT INTO employee_deduction (employeeid,code_deduction,memberid,amount,schedule,cutoff_period,visibility) 
            //                           VALUES 
            //                          ('{$row[0]}' , '".strtoupper($row[1])."' , '{$row[2]}' , {$row[3]} , '{$row[4]}' , '{$row[5]}' , '{$row[6]}') ");       
            //     }
            // }else{
            //     foreach($deducarray as $key=>$row){
            //         $this->db->query("UPDATE employee_deduction SET  memberid='{$row[2]}', amount={$row[3]}, schedule='{$row[4]}', cutoff_period='{$row[5]}' WHERE employeeid='{$row[0]}' AND code_deduction='".strtoupper($row[1])."' AND visibility='HIDDEN'");
            //     }
            // }

            $query = $this->db->query("SELECT * FROM employee_deduction WHERE visibility='HIDDEN' AND employeeid='{$data['eid']}'");
            if($query->num_rows() == 0){
                foreach($deducarray as $key=>$row){
                    $this->db->query("INSERT INTO employee_deduction (employeeid,code_deduction,memberid,amount,schedule,cutoff_period,visibility) 
                                      VALUES 
                                     ('{$row[0]}' , '".strtoupper($row[1])."' , '{$row[2]}' , {$row[3]} , '{$row[4]}' , '{$row[5]}' , '{$row[6]}') ");       
                }
            }else{
                foreach($deducarray as $key=>$row){
                    $q_deduc = $this->db->query("SELECT * FROM employee_deduction WHERE visibility='HIDDEN' AND employeeid='{$data['eid']}' AND code_deduction='".strtoupper($row[1])."'");
                    if($q_deduc->num_rows() > 0) $this->db->query("UPDATE employee_deduction SET  memberid='{$row[2]}', amount={$row[3]}, schedule='{$row[4]}', cutoff_period='{$row[5]}' WHERE employeeid='{$row[0]}' AND code_deduction='".strtoupper($row[1])."' AND visibility='HIDDEN'");
                    else $this->db->query("INSERT INTO employee_deduction (employeeid,code_deduction,memberid,amount,schedule,cutoff_period,visibility) 
                                      VALUES 
                                     ('{$row[0]}' , '".strtoupper($row[1])."' , '{$row[2]}' , {$row[3]} , '{$row[4]}' , '{$row[5]}' , '{$row[6]}') ");    
                }
            }
        }else{
          $msg .= 'Failed to save. Payroll is already processed for the given date effective.';
        }

        if(!$msg) $msg = "Successfully saved.";
        return $msg;
    }

    function deleteDataDeduction($row_id, $code_deduc){
        $res = $this->db->query("DELETE FROM employee_deduction WHERE employeeid='$row_id' AND code_deduction = '$code_deduc'");
        return $res;
    }

    function saveEmployeeSalary($data=array()){
        $user = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM payroll_employee_salary WHERE employeeid='{$data['eid']}'");
          
        $base_id = $base_id_history = '';
        $data["isFixed"] = 1;
        if($query->num_rows() == 0) $res = $this->insertEmpSalaryBase($data,$user); 
        else                        $res = $this->updateEmpSalaryBase($data,$query->row(0)->monthly,$user,true);

        return $res;
    }

    

    function insertEmpSalaryBase($data=array(),$user=''){
        $workdays = (isset($data['workingdays']))       ? $data['workingdays'] : 0;
        $isFixed  = (isset($data['isFixed']))           ? $data['isFixed'] : 0; 
        $workexemp = (!empty($data['workhoursexemp']))  ? $data['workhoursexemp'] : 0;
        $data['workhours']      = isset($data['workhours'])? $data['workhours'] : 0; 
        $data['biweekly']       = isset($data['biweekly'])? $data['biweekly'] : 0; 
        $data['weekly']         = isset($data['weekly'])? $data['weekly'] : 0; 
        $data['lechour']        = isset($data['lechour'])? $data['lechour'] : 0; 
        $data['labhour']        = isset($data['labhour'])? $data['labhour'] : 0; 
        $data['honorarium']     = isset($data['honorarium'])? $data['honorarium'] : 0; 
        $data['sched']          = isset($data['sched'])? $data['sched'] : 'semimonthly'; 
        $data['tax_status']     = isset($data['tax_status'])? $data['tax_status'] : ''; 
        $data['type']     = isset($data['type'])? $data['type'] : ''; 

        $base_id = '';

        $res = $this->db->query("INSERT INTO payroll_employee_salary (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,schedule,dependents,addedby,lechour,labhour,honorarium,type) 
                        VALUES
                        ('{$data['eid']}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
                          , '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}' , '{$data['sched']}' , '{$data['tax_status']}','$user','{$data['lechour']}','{$data['labhour']}','{$data['honorarium']}','{$data['type']}')  ");

        if($res) $base_id = $this->db->insert_id();
        if($base_id) $this->saveEmpSalaryPerDepartment($base_id,$data['eid'],json_decode($data['leclab_arr']),$user);
      
        if($res) $this->saveEmpSalaryHistoryBase($data);

        if($res) return true;
        else return false;
    }

    function updateEmpSalaryBase($data=array(),$prev_salary=0,$user='',$updateHistory=false){
        $base_id = '';

        $workdays = (isset($data['workingdays']))       ? $data['workingdays'] : 0;
        $isFixed  = (isset($data['isFixed']))           ? $data['isFixed'] : 0; 
        $workexemp = (!empty($data['workhoursexemp']))  ? $data['workhoursexemp'] : 0;

        $data['workhours']  = isset($data['workhours'])? $data['workhours'] : 0; 
        $data['biweekly']   = isset($data['biweekly'])? $data['biweekly'] : 0; 
        $data['weekly']     = isset($data['weekly'])? $data['weekly'] : 0; 
        $data['lechour']     = isset($data['lechour'])? $data['lechour'] : 0; 
        $data['labhour']     = isset($data['labhour'])? $data['labhour'] : 0; 
        $data['honorarium']     = isset($data['honorarium'])? $data['honorarium'] : 0; 
        $type     = isset($data['type'])? $data['type'] : ''; 

        $update_str = '';
        if(isset($data['sched'])) $update_str .= " schedule='{$data['sched']}', ";
        if(isset($data['tax_status'])) $update_str .= " dependents='{$data['tax_status']}', ";

        $res = $this->db->query("UPDATE payroll_employee_salary SET 
                                      workdays='$workdays', 
                                      type='$type', 
                                      fixedday='$isFixed', 
                                      workhours='{$data['workhours']}', 
                                      workhoursexemp='$workexemp', 
                                      monthly='{$data['monthly']}', semimonthly='{$data['semimonthly']}', 
                                      biweekly='{$data['biweekly']}', weekly='{$data['weekly']}', 
                                      daily='{$data['daily']}', hourly='{$data['hourly']}', minutely='{$data['minutely']}' , 
                                      
                                      $update_str

                                      addedby='$user',
                                      lechour='{$data['lechour']}',labhour='{$data['labhour']}',
                                      honorarium='{$data['honorarium']}'
                          WHERE employeeid='{$data['eid']}'");

        if($res){
            $id_q = $this->db->query("SELECT id FROM payroll_employee_salary WHERE employeeid='{$data['eid']}'");
            if($id_q->num_rows() > 0) $base_id = $id_q->row(0)->id;
        }

        if($base_id) $this->saveEmpSalaryPerDepartment($base_id,$data['eid'],json_decode($data['leclab_arr']),$user);

        
        if(($prev_salary <> $data['monthly']) || $updateHistory){
            if($res) $this->saveEmpSalaryHistoryBase($data,$user);
        }

        if($res) return true;
        else return false;

    }

     ///< @Angelica added separate function for saving (old : models/backup/payroll.php : esalary ftn)
    function saveEmpSalaryHistoryBase($data=array(),$user=''){
        $base_id_history = '';

        $workdays = (isset($data['workingdays']))       ? $data['workingdays'] : 0;
        $isFixed  = (isset($data['isFixed']))           ? $data['isFixed'] : 0; 
        $workexemp = (!empty($data['workhoursexemp']))  ? $data['workhoursexemp'] : 0;

        $data['workhours']  = isset($data['workhours'])? $data['workhours'] : 0; 
        $data['biweekly']   = isset($data['biweekly'])? $data['biweekly'] : 0; 
        $data['weekly']     = isset($data['weekly'])? $data['weekly'] : 0; 
        $data['lechour']     = isset($data['lechour'])? $data['lechour'] : 0; 
        $data['labhour']     = isset($data['labhour'])? $data['labhour'] : 0; 
        $data['honorarium']     = isset($data['honorarium'])? $data['honorarium'] : 0; 
        $data['sched']     = isset($data['sched'])? $data['sched'] : 'semimonthly'; 
        $data['tax_status']     = isset($data['tax_status'])? $data['tax_status'] : ''; 
        $data['date_effective']     = isset($data['date_effective'])? $data['date_effective'] : ''; 
        $data['type']     = isset($data['type'])? $data['type'] : ''; 

        $res_history = $this->db->query("INSERT INTO payroll_employee_salary_history (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,schedule,dependents,addedby,lechour,labhour,honorarium,date_effective,type) 
                          VALUES
                          ('{$data['eid']}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
                            , '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}' , '{$data['sched']}' , '{$data['tax_status']}' , '$user','{$data['lechour']}','{$data['labhour']}','{$data['honorarium']}','{$data['date_effective']}','{$data['type']}')  ");

        if($res_history) $base_id_history = $this->db->insert_id();
        if($base_id_history) $this->saveEmpSalaryPerDepartment($base_id_history,$data['eid'],json_decode($data['leclab_arr']),$user,TRUE);
    }

    function saveEmpSalaryPerDepartment($base_id='',$employeeid='',$data=array(),$user='',$ishistory=false){
        $tbl = 'payroll_emp_salary_perdept';
        if($ishistory){
            $tbl = 'payroll_emp_salary_perdept_history';
        }else{
            $this->db->query("DELETE FROM payroll_emp_salary_perdept WHERE employeeid='$employeeid'");
        }
        if($data){
            foreach ($data as $key => $row) {
                $this->db->query("INSERT INTO $tbl (base_id,employeeid,aimsdept,lechour,labhour,editedby) VALUES ('$base_id','$employeeid','$row->aimsdept','$row->lechour','$row->labhour','$user')");
            }
        }
    }

    function batchencode($data){
		$return = "";
        $user = $this->session->userdata("username");
        $dept = $data['dept'];
        $tnt  = $data['tnt'];
        $estat= $data['estat'];
        $eid  = $data['eid'];
        $cat  = $data['cat'];
        
        
        $workdays = (isset($data['workingdays']))       ? $data['workingdays'] : 0;
        $isFixed  = (isset($data['isFixed']))           ? $data['isFixed'] : 0; 
        $workexemp = (!empty($data['workhoursexemp']))  ? $data['workhoursexemp'] : 0;
        
        $param = "";
        if($dept)   $param .= " AND deptid='$dept'";
        if($eid)    $param .= " AND FIND_IN_SET(employeeid,'$eid')";
        if($estat)  $param .= " AND employmentstat='$estat'";
        if($tnt)    $param .= " AND teachingtype='$tnt'";
        $query = $this->db->query("SELECT * FROM employee WHERE (dateresigned IS NULL OR dateresigned = '0000-00-00' OR dateresigned = '1970-01-01') $param");
        if($query->num_rows() > 0){
            foreach($query->result() as $res){
                $emp = $res->employeeid; 
                if($cat == 1){
					$query2 = $this->db->query("SELECT * FROM payroll_employee_salary WHERE employeeid='$emp'");
					if($query2->num_rows() > 0){
						/*if($cat == 1){
							$this->db->query("UPDATE payroll_employee_salary SET monthly='{$data['monthly']}', semimonthly='{$data['semimonthly']}', biweekly='{$data['biweekly']}', weekly='{$data['weekly']}', daily='{$data['daily']}', hourly='{$data['hourly']}', minutely='{$data['minutely']}' WHERE employeeid='$emp'");
						}else if($cat == 2){
							$this->db->query("UPDATE payroll_employee_salary SET dependents='{$data['tax_status']}' WHERE employeeid='$emp'");
						}else if($cat == 3){
							$this->db->query("UPDATE payroll_employee_salary SET schedule='{$data['sched']}' WHERE employeeid='$emp'");
						}else if($cat == 4){
							$this->db->query("UPDATE payroll_employee_salary SET whtax='{$data['whtax']}' WHERE employeeid='$emp'");
						}else if($cat == 5){
							$this->db->query("UPDATE payroll_employee_salary SET absent='{$data['absents']}', absentbalance='{$data['balance']}' WHERE employeeid='$emp'");
						}*/
						if($cat == 1){
							$this->db->query("UPDATE payroll_employee_salary SET monthly='{$data['monthly']}', semimonthly='{$data['semimonthly']}', biweekly='{$data['biweekly']}', weekly='{$data['weekly']}', daily='{$data['daily']}', hourly='{$data['hourly']}', minutely='{$data['minutely']}', dependents='{$data['tax_status']}', schedule='{$data['sched']}', whtax='{$data['whtax']}', absent='{$data['absents']}', absentbalance='{$data['balance']}' WHERE employeeid='$emp'");
						}
					}else{
						/*if($cat == 1){
							$this->db->query("INSERT INTO payroll_employee_salary (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,addedby) 
										  VALUES
										  ('{$emp}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
											, '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}', '$user')  ");
							$this->db->query("INSERT INTO payroll_employee_salary_history (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,addedby) 
										  VALUES
										  ('{$emp}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
											, '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}', '$user')  ");
						}else if($cat == 2){
							$this->db->query("INSERT INTO payroll_employee_salary (employeeid,dependents,addedby) VALUES ('{$emp}','{$data['tax_status']}','$user') ");
						}else if($cat == 3){
							$this->db->query("INSERT INTO payroll_employee_salary (employeeid,schedule,addedby) VALUES ('{$emp}','{$data['sched']}','$user') ");
						}else if($cat == 4){
							$this->db->query("INSERT INTO payroll_employee_salary (employeeid,whtax,addedby) VALUES ('{$emp}','{$data['whtax']}','$user') ");
						}else if($cat == 5){
							$this->db->query("INSERT INTO payroll_employee_salary (employeeid,absent,absentbalance,addedby) VALUES ('{$emp}','{$data['absents']}','{$data['balance']}','$user') ");
						}*/

						if($cat == 1){
							$this->db->query("INSERT INTO payroll_employee_salary (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,dependents,schedule,whtax,absent,absentbalance, addedby) 
										  VALUES
										  ('{$emp}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
											, '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}', '{$data['tax_status']}', '{$data['sched']}', '{$data['whtax']}', '{$data['absents']}',, '{$data['balance']}', '$user')  ");
							$this->db->query("INSERT INTO payroll_employee_salary_history (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,dependents,schedule,whtax,absent,absentbalance,addedby) 
										  VALUES
										  ('{$emp}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
											, '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}', '{$data['tax_status']}', '{$data['sched']}', '{$data['whtax']}', '{$data['absents']}',, '{$data['balance']}', '$user')  ");
						}
						
					}
				}

                
                if($cat == 1){

                        $data['sss'] = $data['sss'] == '' ? 'NULL' : $data['sss'];
                        $data['philhealth'] = $data['philhealth'] == '' ? 'NULL' : $data['philhealth'];
                        $data['pagibig'] = $data['pagibig'] == '' ? 'NULL' : $data['pagibig'];
                        $data['peraa'] = $data['peraa'] == '' ? 'NULL' : $data['peraa'];
                        
                        $deducarray = array(
                                            array($data['eid'],$data['ssdesc'],$data['sssid'],$data['sss'],$data['sched'],(isset($data['sssq']) ? $data['sssq'] : 0),'HIDDEN'),
                                            array($data['eid'],$data['phdesc'],$data['philhealthid'],$data['philhealth'],$data['sched'],(isset($data['philhealthq']) ? $data['philhealthq'] : 0),'HIDDEN'),
                                            array($data['eid'],$data['pagibigdesc'],$data['pagibigid'],$data['pagibig'],$data['sched'],(isset($data['pagibigq']) ? $data['pagibigq'] : 0),'HIDDEN'),
                                            array($data['eid'],$data['peraadesc'],$data['peraaid'],$data['peraa'],$data['sched'],(isset($data['peraaq']) ? $data['peraaq'] : 0),'HIDDEN')
                                           );

                        foreach($deducarray as $key=>$row){

                            $query = $this->db->query("SELECT * FROM employee_deduction WHERE visibility='HIDDEN' AND employeeid='{$data['eid']}' AND code_deduction='".strtoupper($row[1])."'");
                            if($query->num_rows() == 0){
                                $res_deduc = $this->db->query("INSERT INTO employee_deduction (employeeid,code_deduction,memberid,amount,schedule,cutoff_period,visibility) 
                                                  VALUES 
                                                 ('{$row[0]}' , '".strtoupper($row[1])."' , '{$row[2]}' , {$row[3]} , '{$row[4]}' , '{$row[5]}' , '{$row[6]}') ");  
                            }else{
                                $res_deduc = $this->db->query("UPDATE employee_deduction SET  memberid='{$row[2]}', amount={$row[3]}, schedule='{$row[4]}', cutoff_period='{$row[5]}' WHERE employeeid='{$row[0]}' AND code_deduction='".strtoupper($row[1])."' AND visibility='HIDDEN'");
                            }


                        }
                        $return = "Successfully Saved!.";
                    }

			
				if($cat == 6){
					if($eid){
						$teid = explode(",",$eid);
						foreach($teid as $key=>$val){
						$deduction = $data["deduction_drop"];
						$memberid = $data["memberid"];
						$schedule = $data["schedule"];
						$period = $data["period_drop"];
							
						$amount = $data["amountdeduct"];
						$nocutoff = $data["nocutoff"];
						$datefrom = $data["datefrom"];
						// $dateto = $data["dateto"]; 
						$dateto = '';              
						  
						$dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
						$dsetto = $dateto ? date("Y-m-d",strtotime($dateto)) : "0000-00-00";
						$this->db->query("CALL prc_employee_deduction_set('{$val}',
																	  '{$deduction}',
																	  '{$memberid}',
																	  '',
																	  '{$dsetfrom}',
																	  '{$dsetto}',
																	  '{$amount}',
																	  '{$nocutoff}',
																	  '{$schedule}',
																	  '{$period}')");
						}
						break;
					}
					else
					{
						$deduction = $data["deduction_drop"];
						$memberid = $data["memberid"];
						$schedule = $data["schedule"];
						$period = $data["period_drop"];
							
						$amount = $data["amountdeduct"];
						$nocutoff = $data["nocutoff"];
						$datefrom = $data["datefrom"];
						// $dateto = $data["dateto"]; 
						$dateto = '';              
						  
						$dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
						$dsetto = $dateto ? date("Y-m-d",strtotime($dateto)) : "0000-00-00";
						$this->db->query("CALL prc_employee_deduction_set('{$emp}',
																	  '{$deduction}',
																	  '{$memberid}',
																	  '',
																	  '{$dsetfrom}',
																	  '{$dsetto}',
																	  '{$amount}',
																	  '{$nocutoff}',
																	  '{$schedule}',
																	  '{$period}')");
					}
                    $return = "Successfully Saved!.";
				}
			
                if($cat == 7){
                    if($eid){
                        $teid = explode(",",$eid);
                        foreach($teid as $key=>$val){
                        $income     = $data["income_drop"];
                        $schedule   = $data["schedule"];
                        $period     = $data["period_drop"];
                        $amount     = $data["amountincome"];
                        $nocutoff   = $data["nocutoff"];
                        $datefrom   = $data["datefrom"];     
                        $dsetfrom   = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                        $this->db->query("CALL prc_employee_income_set('{$val}',
                                                                          '{$income}',
                                                                          '{$income['remarks']}',
                                                                          '{$dsetfrom}',
                                                                          '',
                                                                          '{$amount}',
                                                                          '{$nocutoff}',
                                                                          '',
                                                                          '{$schedule}',
                                                                          '{$period}')");
                        }
						break;
                    }
					else{
                        $income     = $data["income_drop"];
                        $schedule   = $data["schedule"];
                        $period     = $data["period_drop"];
                        $amount     = $data["amountincome"];
                        $nocutoff   = $data["nocutoff"];
                        $datefrom   = $data["datefrom"];     
                        $dsetfrom   = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                        $this->db->query("CALL prc_employee_income_set('','{$emp}',
                                                                          '{$income}',
                                                                          '{$income['remarks']}',
                                                                          '{$dsetfrom}',
                                                                          '',
                                                                          '{$amount}',
                                                                          '{$nocutoff}',
                                                                          '',
                                                                          '{$schedule}',
                                                                          '{$period}')");
					}
                    $return = "Successfully Saved!.";
                }
				
                if($cat == 10){
                    if($eid){
                        $teid = explode(",",$eid);
                        foreach($teid as $key=>$val){
                        $income     = $data["income_drop"];
                        $schedule   = $data["schedule"];
                        $period     = $data["period_drop"];
                        $amount     = $data["amountincome"];
                        $nocutoff   = $data["nocutoff"];
                        $deduct     = $data["deduct"];
                        $datefrom   = $data["datefrom"];     
                        $dsetfrom   = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                        $this->db->query("CALL prc_employee_income_adj_set('{$val}',
                                                                          '{$income}',
                                                                          '{$income['remarks']}',
                                                                          '{$dsetfrom}',
                                                                          '',
                                                                          '{$amount}',
                                                                          '{$nocutoff}',
                                                                          '',
                                                                          '{$schedule}',
                                                                          '{$period}',
                                                                          '{$deduct}',
                                                                          '0')");
                        }
                        break;
                    }
                    else{
                        $income     = $data["income_drop"];
                        $schedule   = $data["schedule"];
                        $period     = $data["period_drop"];
                        $amount     = $data["amountincome"];
                        $nocutoff   = $data["nocutoff"];
                        $deduct     = $data["deduct"];
                        $datefrom   = $data["datefrom"];     
                        $dsetfrom   = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                        $this->db->query("CALL prc_employee_income_adj_set('','{$emp}',
                                                                          '{$income}',
                                                                          '{$income['remarks']}',
                                                                          '{$dsetfrom}',
                                                                          '',
                                                                          '{$amount}',
                                                                          '{$nocutoff}',
                                                                          '',
                                                                          '{$schedule}',
                                                                          '{$period}',
                                                                          '{$deduct}',
                                                                          '0')");
                    }
                    $return = "Successfully Saved!.";
                }

                if($cat == 8){
                    if($eid){
                        $countFailed = $countSaved= 0;
                        $teid = explode(",",$eid);
                        foreach($teid as $key=>$val){
                            // $loan       = $data["dloan_drop"];
                            // $schedule   = $data["dschedule"];
                            // $period     = $data["dperiod_drop"];
                            // $startingamount = $data["dstartingamount"];
                            // $amount     = $data["damountloan"];
                            // $famount    = $data["dfamount"];
                            // $nocutoff   = $data["dnocutoff"];
                            // $datefrom   = $data["ddatefrom"];          
                            $loan       = $data["dloan_drop"];
                            $schedule   = $data["dschedule"];
                            $period     = $data["dperiod_drop"];
                            $startingamount = $data["startingamountloan"];
                            $currentamount = $data['currentamount'];
                            $amount     = $data["amountloans"];
                            $famount    = isset($data["dfamount"])?$data["dfamount"]:"";
                            $nocutoff   = $data["nocutoff"];
                            $datefrom   = $data["ddatefrom"];    
                            $dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                          

                      $remainingBalance = 0; $status = 1;
                      $query = $this->db->query("SELECT * FROM employee_loan WHERE code_loan ='{$loan}' AND employeeid='{$val}'");

                      if ($query->num_rows() > 0) {
                          $remainingBalance = ($query->row(0)->amount * ($query->row(0)->nocutoff -1)) + $query->row(0)->famount;
                      }

                          if ($remainingBalance == 0 || $remainingBalance == "" || $remainingBalance == NULL)
                           { 
                            $this->db->query("CALL prc_employee_loan_set('','{$val}',
                                                                          '{$loan}',
                                                                          '',
                                                                          '{$dsetfrom}',
                                                                          '',
                                                                          '{$amount}',
                                                                          '{$startingamount}',                                                                  
                                                                          '{$famount}',
                                                                          '{$nocutoff}',
                                                                          '',
                                                                          '{$schedule}',
                                                                          '{$period}','{$currentamount}')");
							
                            $user = $this->session->userdata('username');
							
                            // $this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user)
                            //      VALUES('".$val."','".$loan."','$dsetfrom','',".$startingamount.",".$amount.",".$startingamount.",'".$schedule."','".$period."','UPDATE','".$user."')");
                            $this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user,currentBalance)
                              VALUES('".$val."','".$loan."','$dsetfrom','',".$currentamount.",".$amount.",".$currentamount.",'".$schedule."','".$period."','UPDATE','".$user."','".$startingamount."')");
                            $countSaved ++;
                                
                            }
                            else
                            {
                            $countFailed ++;
                            }
                             $return = "Successfully Saved : ".$countSaved."\nFailed to Save Due to have Remaining Balance: ".$countFailed;
						}

						break;
                    }
					else{

                            $loan       = $data["dloan_drop"];
                            $schedule   = $data["dschedule"];
                            $period     = $data["dperiod_drop"];
                            $startingamount = $data["startingamountloan"];
                            $currentamount = $data['currentamount'];
                            $amount     = $data["amountloans"];
                            $famount    = isset($data["dfamount"])?$data["dfamount"]:"";
                            $nocutoff   = $data["nocutoff"];
                            $datefrom   = $data["ddatefrom"];          
                          
                            $dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                            
                            $remainingBalance = 0; $status = 1;
                            $query = $this->db->query("SELECT * FROM employee_loan WHERE code_loan ='{$loan}' AND employeeid='{$emp}'");
                            if ($query->num_rows() > 0) {
                                $remainingBalance = ($query->row(0)->amount * ($query->row(0)->nocutoff -1)) + $query->row(0)->famount;
                            }

                            

                        if ($remainingBalance == 0 || $remainingBalance == "" || $remainingBalance == NULL)
                         {

                                $this->db->query("CALL prc_employee_loan_set('','{$emp}',
                                                                                  '{$loan}',
                                                                                  '',
                                                                                  '{$dsetfrom}',
                                                                                  '',
                                                                                  '{$amount}',
                                                                                  '{$startingamount}',                                                                  
                                                                                  '{$famount}',
                                                                                  '{$nocutoff}',
                                                                                  '',
                                                                                  '{$schedule}',
                                                                                  '{$period}','{$currentamount}')");
    							
    							$user           = $this->session->userdata('username');
    							// $this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user)
    							// 		VALUES('".$emp."','".$loan."','$dsetfrom','',".$startingamount.",".$amount.",".$startingamount.",'".$schedule."','".$period."','UPDATE','".$user."')");
                                $this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user,currentBalance)
                                  VALUES('".$emp."','".$loan."','$dsetfrom','',".$currentamount.",".$amount.",".$currentamount.",'".$schedule."','".$period."','UPDATE','".$user."','".$startingamount."')");

                                $return = "Successfully Saved!";
                        }
                        
                        else
                        {

                                $return = "Failed to Saved! Due to have remaining balance!";

                        }

                    }

                }
				
                if($cat == 9){
                    if($eid){
                        $teid = explode(",",$eid);
                        foreach($teid as $key=>$val){
                            $income = $data["othincome_drop"];
                            $amount = $data["othamountincome"];      
                            $pos    = $data["othpos"];      
                            $this->db->query("CALL prc_employee_income_oth_set('{$val}',
                                                                              '{$income}',
                                                                              '{$amount}',
                                                                              '{$pos}')");
                        }
						break;
                    }
					else{
                            $income = $data["othincome_drop"];
                            $amount = $data["othamountincome"];      
                            $pos    = $data["othpos"];      
                            $this->db->query("CALL prc_employee_income_oth_set('{$emp}',
                                                                              '{$income}',
                                                                              '{$amount}',
                                                                              '{$pos}')");
                    }
                   $return = "Successfully Saved!.";
                }
            }
        
            if(!$return) $return .= "Successfully saved.";    
        }
		else
		{
			$return = "This employee already resigned!";
		}
        #JUSTIN DITO
        /*
        $query = $this->db->query("SELECT * FROM payroll_employee_salary WHERE employeeid='{$data['eid']}'");
        $salary = $query->row(0)->monthly;
        if($query->num_rows() == 0){
            $this->db->query("INSERT INTO payroll_employee_salary (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,schedule,dependents,whtax,absent,absentbalance,addedby) 
                              VALUES
                              ('{$data['eid']}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
                                , '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}' , '{$data['sched']}' , '{$data['tax_status']}', '{$data['whtax']}', '{$data['absents']}', '{$data['balance']}' , '$user')  ");
            $this->db->query("INSERT INTO payroll_employee_salary_history (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,schedule,dependents,whtax,absent,absentbalance,addedby) 
                              VALUES
                              ('{$data['eid']}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
                                , '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}' , '{$data['sched']}' , '{$data['tax_status']}', '{$data['whtax']}', '{$data['absents']}', '{$data['balance']}' , '$user')  ");
            $return = "Successfully Saved!.";
        }else{
            $this->db->query("UPDATE payroll_employee_salary SET workdays='$workdays', fixedday='$isFixed', workhours='{$data['workhours']}', workhoursexemp='$workexemp', 
                                                              monthly='{$data['monthly']}', semimonthly='{$data['semimonthly']}', biweekly='{$data['biweekly']}', weekly='{$data['weekly']}', 
                                                              daily='{$data['daily']}', hourly='{$data['hourly']}', minutely='{$data['minutely']}' , schedule='{$data['sched']}', dependents='{$data['tax_status']}', whtax='{$data['whtax']}', absent='{$data['absents']}', absentbalance='{$data['balance']}'  , addedby='$user' 
                                WHERE employeeid='{$data['eid']}'");
            if($salary <> $data['monthly']){
                $this->db->query("INSERT INTO payroll_employee_salary_history (employeeid,workdays,fixedday,workhours,workhoursexemp,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,schedule,dependents,whtax,absent,absentbalance,addedby) 
                                  VALUES
                                  ('{$data['eid']}', '$workdays', '$isFixed', '{$data['workhours']}', '$workexemp', '{$data['monthly']}', '{$data['semimonthly']}', '{$data['biweekly']}' 
                                    , '{$data['weekly']}' , '{$data['daily']}' , '{$data['hourly']}' , '{$data['minutely']}' , '{$data['sched']}' , '{$data['tax_status']}', '{$data['whtax']}', '{$data['absents']}', '{$data['balance']}' , '$user')  ");
            }
            $return = "Record Updated!.";
        }
        
        $deducarray = array(
                            array($data['eid'],$data['ssdesc'],$data['sssid'],$data['sss'],$data['sched'],(isset($data['sssq']) ? $data['sssq'] : 0),'HIDDEN'),
                            array($data['eid'],$data['phdesc'],$data['philhealthid'],$data['philhealth'],$data['sched'],(isset($data['philhealthq']) ? $data['philhealthq'] : 0),'HIDDEN'),
                            array($data['eid'],$data['pagibigdesc'],$data['pagibigid'],$data['pagibig'],$data['sched'],(isset($data['pagibigq']) ? $data['pagibigq'] : 0),'HIDDEN')
                           );
        $query = $this->db->query("SELECT * FROM employee_deduction WHERE visibility='HIDDEN' AND employeeid='{$data['eid']}'");
        if($query->num_rows() == 0){
            foreach($deducarray as $key=>$row){
                $this->db->query("INSERT INTO employee_deduction (employeeid,code_deduction,memberid,amount,schedule,cutoff_period,visibility) 
                                  VALUES 
                                 ('{$row[0]}' , '".strtoupper($row[1])."' , '{$row[2]}' , '{$row[3]}' , '{$row[4]}' , '{$row[5]}' , '{$row[6]}') ");       
            }
            $return = "Successfully Saved!.";
        }else{
            foreach($deducarray as $key=>$row){
                $this->db->query("UPDATE employee_deduction SET amount='{$row[3]}', schedule='{$row[4]}', cutoff_period='{$row[5]}' WHERE employeeid='{$row[0]}' AND code_deduction='".strtoupper($row[1])."' AND visibility='HIDDEN'");
            }
                $return = "Record Updated!.";
        }
        */
        return $return;
    }
     
     // WithHolding Tax
    function WHTax($empid = "",$schedule = "",$quarter = "",$sdate = "",$edate = "",$salary = "",$dependents = ""){
        $WHTax = 0;
        $totalincome = "";
        $netpay = "";
        $deductions = $income = $taxrange = $taxbase = $percentage = 0;
        
        $querywh = $this->db->query("SELECT whtax FROM payroll_employee_salary WHERE employeeid='$empid'");
        $wholdingtax = $querywh->row(0)->whtax; 
        
        /*  
         *  Deductions  /  INCOME
         */ 
         $query = $this->db->query("SELECT (A.deductamt+B.loanamt) as deductions,C.incomeamt,D.otamt FROM
                                      (SELECT SUM(amount) AS deductamt FROM employee_deduction WHERE employeeid='$empid' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')) AS A, 
                                      (SELECT SUM(amount) AS loanamt FROM employee_loan WHERE employeeid='$empid' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')) AS B, 
                                      (SELECT SUM(amount) AS incomeamt FROM employee_income a INNER JOIN payroll_income_config b ON a.code_income=b.description WHERE employeeid='$empid' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3') AND taxable='withtax') as C,
                                      (SELECT SUM((ottime*minutely)) AS otamt FROM payroll_employee_deductions a LEFT JOIN payroll_employee_salary b ON a.employeeid = b.employeeid WHERE a.employeeid='$empid' AND a.schedule='$schedule' AND FIND_IN_SET(quarter,'$quarter,3') AND deductcutoffstart='$sdate' AND deductcutoffend='$edate') as D
                                   ");
 
            if($query->num_rows() > 0){
                $deductions      = ($query->row(0)->deductions+$this->payroll->tardydeduct($empid,$schedule,$quarter,$sdate,$edate)+$this->payroll->absentdeduct($empid,$schedule,$quarter,$sdate,$edate)); 
                $income          = ($query->row(0)->incomeamt)+$salary+($query->row(0)->otamt);  
            }    
            $totalpay = $income-$deductions;
            $query = $this->db->query("SELECT tax_range,basic_tax, percent  FROM code_tax WHERE tax_type='$schedule' AND status_='$dependents' AND tax_range<='$totalpay' ORDER BY tax_range DESC LIMIT 1");
            if($query->num_rows() > 0){
                $taxrange   = $query->row(0)->tax_range;
                $taxbase    = $query->row(0)->basic_tax;
                $percentage = $query->row(0)->percent;
            } 
            $WHTax = ($totalpay - $taxrange);
            $WHTax = $WHTax*$percentage;
            $WHTax = $WHTax+$taxbase;
         
         if($wholdingtax != 0)  $WHTax = $wholdingtax;
        
        return number_format($WHTax,2,'.',''); 
    }
    // Overtime
    function ottime($empid = "",$schedule = "",$quarter = "",$sdate = "",$edate = ""){
        $amount = 0;
        $query = $this->db->query("SELECT a.minutely, b.teachingtype FROM payroll_employee_salary a 
                                    INNER JOIN employee b ON a.employeeid = b.employeeid
                                    WHERE a.employeeid='$empid' AND a.schedule='$schedule'");
        if($query->num_rows() > 0){
            $minutely   = $query->row(0)->minutely;
            $ttype = $query->row(0)->teachingtype;
            $ottime = $this->getTardyAbsentOT($empid,$sdate,$edate,"ottime",$ttype);
            $ottime = $this->time->hoursToMinutes($ottime);
            $amount     = number_format($ottime * $minutely,2,'.', '');
        }
        return $amount;
    }
    
    ///< @Angelica - functions revised: refer to models\attendance.php (computeEmployeeAttendanceSummaryTeaching, computeEmployeeAttendanceSummaryNonTeaching)

    function getTardyAbsentOT($empid = "",$sdate = "",$edate = "",$type = "",$etype = ""){
        $lateutlec = $tlec = $absent = $tabsent = $totr = $totsat = $totsun = $tothol = $tlec = $tlab = $tdlec = $tdlab = "";
        $edata = "NEW";
        $qdate = $this->attcompute->displayDateRange($sdate, $edate);
        if($etype == "nonteaching"){
            foreach ($qdate as $rdate) {
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                if($countrow > 0){
                    foreach($sched->result() as $rsched){
                        $stime  = $rsched->starttime;
                        $etime  = $rsched->endtime; 
                        $earlyd = $rsched->early_dismissal;
                        
                        // Holiday
                        $holiday = $this->attcompute->isHoliday($rdate->dte); 
                        
                        // logtime
                        list($login,$logout,$q)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        
                        // Overtime
                        list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                                
                        // Leave
                        list($el,$vl,$sl,$ol)             = $this->attcompute->displayLeave($empid,$rdate->dte);
                        
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
                        if($el || $vl || $sl || $ol || $holiday) $absent = "";
                        
                        // Late / Undertime
                        $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent);
                        // Absent
                        $tabsent  += $absent;
                        
                        // Late / UT
                        if($lateutlec)  $tlec += $this->attcompute->exp_time($lateutlec);                    
                    }
                        /* Overtime */
                        if($otreg)  $totr += $this->attcompute->exp_time($otreg);
                        if($otrest)  $totrest += $this->attcompute->exp_time($otrest);
                        if($othol)  $tothol += $this->attcompute->exp_time($othol);
                }else{
                    /* Overtime */
                    list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                    if($otreg)  $totr += $this->attcompute->exp_time($otreg);
                    if($otrest)  $totrest += $this->attcompute->exp_time($otrest);
                    if($othol)  $tothol += $this->attcompute->exp_time($othol);
                }
            }
             $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");
             $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
             $otrest = ($otrest ? $this->attcompute->sec_to_hm($otrest) : ""); 
             $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");
            if($type == "late")         return $tlec;
            else if($type == "ottime")  return $tothol;
            else if($type == "absent")  return $tabsent;
        }else{
            $tlate = $tdeduct = 0;
            foreach ($qdate as $rdate) {
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                if($countrow > 0){
                    foreach($sched->result() as $rsched){
                        
                        $stime = $rsched->starttime;
                        $etime = $rsched->endtime; 
                        $rtype  = $rsched->leclab;
                        
                        // Holiday
                        $holiday = $this->attcompute->isHoliday($rdate->dte);
                        
                        // logtime
                        list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        
                        // Leave
                        list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte);
                        if($el || $vl || $sl || $ol || $holiday) $absent = "";
                        
                        // Late / Undertime
                        list($lateutlec,$lateutlab,$tschedlec,$tschedlab) = $this->attcompute->displayLateUT($stime,$etime,$login,$logout,$rtype,$absent);
                        if($el || $vl || $sl || $ol || $holiday){
                            $lateutlec = $lateutlab = $tschedlec = $tschedlab = "";
                        }
                        
                        /*
                         * Total
                         */ 
                        
                        // Absent
                        $tabsent  += $absent;
                        // Late / UT
                        if($lateutlec)  $tlec += $this->attcompute->exp_time($lateutlec);
                        if($lateutlab)  $tlab += $this->attcompute->exp_time($lateutlab);
                    #    echo $lateutlab." - ".$tlab."<br />";
                        
                        // Deductions
                        if($tschedlec)  $tdlec += $this->attcompute->exp_time($tschedlec);
                        if($tschedlab)  $tdlab += $this->attcompute->exp_time($tschedlab);
                    }
                }
            }
            $tlate    = $this->attcompute->sec_to_hm($tlec+$tlab);
            $tdeduct  = $this->attcompute->sec_to_hm($tdlec+$tdlab);
            
            if($type == "late")         return $tlate;
            else if($type == "ottime")  return 0;
            else if($type == "absent")  return $tdeduct;
        }
    }

    ///< @Angelica - functions revised: refer to models\payrollcomputation.php (getTardyAbsentSummary)
        
    // Tardy Deduction
    function tardydeduct($empid = "",$schedule = "",$quarter = "",$sdate = "",$edate = ""){
        $amount = 0;
        $query = $this->db->query("SELECT a.minutely, b.teachingtype FROM payroll_employee_salary a 
                                    INNER JOIN employee b ON a.employeeid = b.employeeid
                                    WHERE a.employeeid='$empid' AND a.schedule='$schedule'");
        if($query->num_rows() > 0){    
            $minutely   = $query->row(0)->minutely;
            $ttype      = $query->row(0)->teachingtype;
            $tardy      = $this->getTardyAbsentOT($empid,$sdate,$edate,"late",$ttype);
            $tardy      = $this->time->hoursToMinutes($tardy);
            $amount     = number_format($tardy * $minutely,2,'.', '');
        }
        return $amount;
    }

    ///< @Angelica - functions revised: refer to models\payrollcomputation.php (getTardyAbsentSummary)
    
    // Absent Deduction
    function absentdeduct($empid = "",$schedule = "",$quarter = "",$sdate = "",$edate = ""){
        $amount = 0;
        $query = $this->db->query("SELECT a.daily, b.teachingtype FROM payroll_employee_salary a 
                                    INNER JOIN employee b ON a.employeeid = b.employeeid
                                    WHERE a.employeeid='$empid' AND a.schedule='$schedule'");
        if($query->num_rows() > 0){
            $daily   = $query->row(0)->daily;
            $ttype = $query->row(0)->teachingtype;
            $absences = $this->getTardyAbsentOT($empid,$sdate,$edate,"absent",$ttype);
            $amount  = number_format($absences * $daily,2,'.', '');
        }else{
            $query = $this->db->query("SELECT daily, absent FROM payroll_employee_salary WHERE employeeid='$empid'");    
            if($query->num_rows() > 0){
                $absent     = $query->row(0)->absent;    
                $daily      = $query->row(0)->daily;
                $amount     = number_format($absent * $daily,2,'.', '');    
            }
        }
        return $amount;
    }
     
     // Deduction
    function newDeduction($data){
        $return =   "";
        $desc   =   strtoupper($data['desc']);
        $id     =   $data['id'];
        $arith  =   $data['arithmetic'];
        $tax    =   $data['taxable'];
        $gross   =   $data['grossinc'];
        $loanaccount   =   $data['loan_acc'];
        $user   =   $this->session->userdata('username');
        
        /** ADDED NEW ALTER loanaccount **/
        /** ADDING "L" IN VALUE OF loanaccount **/
        /** ADDED BY PAULO 04-02-2018 ICA - HYPERION 21517 **/
        if($id){
            $query  =  $this->db->query("UPDATE payroll_deduction_config SET description='$desc', arithmetic='$arith',taxable='$tax',grossinc='$gross',loanaccount='L$loanaccount', addedby = '$user' WHERE id='$id'");
            $return = "Record Updated!.";
        }else{
            $query  =   $this->db->query("SELECT * FROM payroll_deduction_config where description='$desc'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO payroll_deduction_config (description,arithmetic,addedby,taxable,grossinc,loanaccount) VALUES ('$desc','$arith','$user','$tax','$gross','L$loanaccount')");
                $return = "Successfully Saved!.";    
            }else
                $return = "Deduction Already Exists!.";  
        }
        return $return;
    }

    function delDeduction($data){
       $return = "";
       $id = $data['id'];
       if($this->db->query("SELECT * FROM employee_deduction WHERE code_deduction = '$id' ")->num_rows() == 0){
           $query = $this->db->query("DELETE FROM payroll_deduction_config WHERE id='$id'");
           if($query)   $return = "Deduction successfully deleted.";
           else         $return = "Failed to delete data!.";
       }else{
        $return = "Failed to delete data. Deduction already used.";
       }
       return $return;
    }

    function deleteSalaryHistory($data){
       $return = "";
       $id = $data['base_id'];
       $perdept = $this->db->query("DELETE FROM payroll_emp_salary_perdept_history WHERE base_id='$id'");
       if($perdept) $salary = $this->db->query("DELETE FROM payroll_employee_salary_history WHERE id='$id'");
       if($salary)  $return = "Data Successfully Deleted!";
       else         $return = "Failed to delete data!";
       return $return;
    }
    // end
    
    // Income
    function newIncome($data){
        $return =   "";
        $desc   =   strtoupper($data['desc']);
        $id     =   $data['id'];
        $wtax   =   $data['taxable'];
        $gross  =   isset($data['grossinc']) ? $data['grossinc'] : "";
        $mainaccount = $data['mainaccount'];
        $deductedby   =   $data['deductedby'];
        $incomeType = $data['incomeType'];
        $isIncluded = isset($data['isIncluded']) ? $data['isIncluded'] : 0;
        $grosspayNotIncluded = isset($data['grosspayNotIncluded']) ? $data['grosspayNotIncluded'] : 0 ;
        $grosspayNotIncludedPhil = isset($data['grosspayNotIncludedPhil']) ? $data['grosspayNotIncludedPhil'] : 0 ;
        $grosspayNotIncludedPag = isset($data['grosspayNotIncludedPag']) ? $data['grosspayNotIncludedPag'] : 0 ;

        $ismainaccount = isset($data['ismainaccount']) ? $data['ismainaccount'] : 0;
        if($ismainaccount) $mainaccount = '';

        $user   =   $this->session->userdata('username');
        
        # > updated by justin (with e) for mcu-hyperion 21518
        if($id){
            $query  =  $this->db->query("UPDATE payroll_income_config SET description=".$this->db->escape($desc).", incomeType='$incomeType', taxable='$wtax', grossinc='$gross',
                                              ismainaccount='$ismainaccount', mainaccount='$mainaccount',deductedby='$deductedby',isIncluded = '$isIncluded', grosspayNotIncluded='$grosspayNotIncluded', grosspayNotIncludedPhil='$grosspayNotIncludedPhil', grosspayNotIncludedPag='$grosspayNotIncludedPag' WHERE id='$id'");
            $return = "Record Updated!.";
        }else{
            $query  =   $this->db->query("SELECT * FROM payroll_income_config where description='$desc'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO payroll_income_config (description,incomeType,taxable,grossinc,addedby,ismainaccount, mainaccount,deductedby,isIncluded,grosspayNotIncluded,grosspayNotIncludedPhil,grosspayNotIncludedPag) 
                                        VALUES (".$this->db->escape($desc).",'$incomeType','$wtax','$gross','$user','$ismainaccount','$mainaccount','$deductedby','$isIncluded','$grosspayNotIncluded','$grosspayNotIncludedPhil','$grosspayNotIncludedPag')");
                $return = "Successfully Saved!.";    
            }else
                $return = "Deduction Already Exists!.";  
        }
        return $return;
    }

    function delIncome($data){
       $return = "";
       $id = $data['id'];
       if($this->db->query("SELECT * FROM employee_income WHERE code_income = '$id' ")->num_rows() == 0){
           $query = $this->db->query("DELETE FROM payroll_income_config WHERE id='$id'");
           if($query)   $return = "Income successfully deleted.";
           else         $return = "Failed to delete data!.";
       }else{
        $return = "Failed to delete data. Income already used.";
       }
       return $return;
    }
    // end
    
    // Loan
    function newLoan($data){
        $return =   "";
        $desc   =   strtoupper($data['desc']);
        $id     =   $data['id'];
        $tax    =   $data['taxable'];
        $gross  =   $data['grossinc'];
        $user   =   $this->session->userdata('username');
        
        if($id){
            $query  =  $this->db->query("UPDATE payroll_loan_config SET description='$desc',taxable='$tax',grossinc='$gross' WHERE id='$id'");
            $return = "Record Updated!.";
        }else{
            $query  =   $this->db->query("SELECT * FROM payroll_loan_config where description='$desc'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO payroll_loan_config (description,addedby,taxable,grossinc) VALUES ('$desc','$user','$tax','$gross')");
                $return = "Successfully Saved!.";    
            }else
                $return = "Deduction Already Exists!.";  
        }
        return $return;
    }
    function delLoan($data){
       $return = "";
       $id = $data['id'];
       if($this->db->query("SELECT * FROM employee_loan WHERE code_loan = '$id' ")->num_rows() == 0){
           $query = $this->db->query("DELETE FROM payroll_loan_config WHERE id='$id'");
           if($query)   $return = "Loan successfully deleted.";
           else         $return = "Failed to delete data.";
       }else{
        $return = "Failed to delete data. Loan already used.";
       }
       return $return;
    }
    // end
    
    // Other Income
    function newIncomeOth($data){
        $return =   "";
        $desc   =   strtoupper($data['desc']);
        $id     =   $data['id'];
        $tax    =   $data['taxable'];   
        $gross  =   $data['grossinc'];
        $user   =   $this->session->userdata('username');
        
        if($id){
            $query  =  $this->db->query("UPDATE payroll_income_oth_config SET description='$desc',taxable='$tax',grossinc='gross' WHERE id='$id'");
            $return = "Record Updated!.";
        }else{
            $query  =   $this->db->query("SELECT * FROM payroll_income_oth_config where description='$desc'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO payroll_income_oth_config (description,addedby,taxable,grossinc) VALUES ('$desc','$user','$tax','$gross')");
                $return = "Successfully Saved!.";    
            }else
                $return = "Deduction Already Exists!.";  
        }
        return $return;
    }
    function delIncomeOth($data){
       $return = "";
       $id = $data['id'];
       $query = $this->db->query("DELETE FROM payroll_income_oth_config WHERE id='$id'");
       if($query)   $return = "Data Successfully Deleted.!";
       else         $return = "Failed to delete data!.";
       return $return;
    }
    // end
    
    // Payroll Cut-Off Date
    function newCutoff($data){
        $return   =   "";
        $id       =   $data['id'];
        $schedule =   $data['schedule'];
        $quarter  =   $data['quarter'];
        $dfrom    =   $data['dfrom'];
        $dto      =   $data['dto'];
        $user     =   $this->session->userdata('username');
        
        if($id){
            $query  =  $this->db->query("UPDATE payroll_cutoff_config SET schedule='$schedule', quarter='$quarter', startdate='$dfrom', enddate='$dto', lastupdate='$user' WHERE id='$id'");
            $return = "Record Updated!.";
        }else{
            $query  =   $this->db->query("SELECT * FROM payroll_cutoff_config where ('$dfrom' BETWEEN startdate AND enddate OR '$dto' BETWEEN startdate and enddate) AND schedule='$schedule'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO payroll_cutoff_config (schedule,quarter,startdate,enddate,addedby) VALUES ('$schedule','$quarter','$dfrom','$dto','$user')");
                $return = "Successfully Saved!.";    
            }else
                $return = ucwords($schedule)." Cut-off Already Exists!.";  
        }
        return $return;
    }
    // end

    function newBank($data){
        $return =   "";
        $job                =   $data['job'];
        $code               =   strtoupper($data['code']);
        $account_number     =   $data['account_number'];
        $bank_name          =   $data['bank_name'];
        $branch             =   $data['branch'];
        
        if($job == 'edit'){
            $query  =  $this->db->query("UPDATE code_bank_account SET account_number='$account_number', bank_name='$bank_name', branch='$branch' WHERE code='$code'");
            $return = "Record Updated!.";
        }else{
            $query  =   $this->db->query("SELECT * FROM code_bank_account WHERE code='$code'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO code_bank_account (code,account_number,bank_name,branch) VALUES ('$code','$account_number','$bank_name','$branch')");
                $return = "Successfully Saved!.";    
            }else
                $return = "Bank Code Already Exists!.";  
        }
        return $return;
    }

    function delBank($data){
       $return = "";
       $id = $data['id'];
       $date = date('Y-m-d');
       $bank = array();
       $query = $this->db->query("SELECT DISTINCT bank FROM payroll_computed_table WHERE ('$date' BETWEEN cutoffstart AND cutoffend)")->result_array();
       // $query2 = $this->db->query("SELECT emp_bank FROM payroll_employee_salary WHERE ''")
       // print_r($query[0]['CutoffFrom']);
       // print_r($query);
       foreach ($query as $key) {
           array_push($bank, $key['bank']);
       }
       if (in_array($id, $bank)){
            return '1';
       }
       else{
            $query = $this->db->query("DELETE FROM code_bank_account WHERE code='$id'");
            if($query)   $return = "3";
            else         $return = "2"; //Failed to delete data!.
            return $return;
       }
       
    }
    
    // Payroll Attendance
    function payrollattcutoffsave($data){
        $ins     = "";
        $count = $recomputed_emp = $success_count = $failed_count = 0;
        $key = '';
        $cstart  = $data['cutoffstart'];
        $cend    = $data['cutoffend'];
        $tnt     = $data['type'];
        $sched   = $data['schedule'];
        $quarter = $data['quarter'];
        $qdate   = explode(' ',$data['pcutoffdate']);
        $psdate  = $qdate[0];
        $pedate  = $qdate[1];
        $final_pay = isset($data['finalpay_arr']) ? $data['finalpay_arr'] : array();
        $where_clause = '';
        if($final_pay){
            $where_clause = " AND employeeid = '$key' ";
        }
        
        $user    =   $this->session->userdata('username');
        if($final_pay){
            foreach($final_pay as $key => $count){
                // $key = $this->gibberish->decrypt($key, $data["toks"]);
                if($tnt == "teaching"){
                    $cquery = $this->db->query("SELECT * FROM attendance_confirmed WHERE payroll_cutoffstart='$psdate' AND payroll_cutoffend='$pedate' AND quarter='$quarter'");
                    if($cquery->num_rows() > 0){
                        if(!$count[0]['isFinalPay']) $this->removeFinalBenefits($key);
                        $ins    = $this->db->query("UPDATE attendance_confirmed SET forcutoff=1, payroll_cutoffstart='$psdate', payroll_cutoffend='$pedate', quarter='$quarter', status = 'PROCESSED', isFinal='{$count[0]['isFinalPay']}', hold_status='{$count[0]['isOnhold']}' WHERE cutoffstart='$cstart' AND cutoffend='$cend' AND `status` != 'PENDING' AND employeeid = '$key' ");

                        $this->db->query("DELETE FROM processed_employee WHERE cutoffstart = '$psdate' AND cutoffend = '$pedate' AND employeeid = '$key' ");
                        $this->db->query("INSERT INTO processed_employee (employeeid,cutoffstart,cutoffend,status,remaining_cutoff) VALUES ('$key', '$psdate', '$pedate', 'PROCESSED', '') ");

                        /*check if status is onhold*/
                        if($count[0]['isOnhold']){
                            $this->db->query("UPDATE attendance_confirmed SET forcutoff=1, payroll_cutoffstart='$psdate', payroll_cutoffend='$pedate', quarter='$quarter', hold_status='{$count[0]['isOnhold']}', status = 'SUBMITTED' WHERE cutoffstart='$cstart' AND cutoffend='$cend' AND `status` != 'PENDING' AND employeeid = '$key' ");
                            $this->db->query("DELETE FROM processed_employee WHERE cutoffstart = '$psdate' AND cutoffend = '$pedate' AND employeeid = '$key' ");
                            $this->db->query("DELETE FROM payroll_computed_table WHERE cutoffstart = '$psdate' AND cutoffend = '$pedate' AND employeeid = '$key' AND status != 'PROCESSED' ");
                        }

                        /*end*/

                    }else{
                        $return = "Failed to Saved!. Cut-Off Already Exists!.";
                        $failed_count+=1;
                    }
                }else{
                    $cquery = $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE payroll_cutoffstart='$psdate' AND payroll_cutoffend='$pedate' AND quarter='$quarter'");
                    if($cquery->num_rows() > 0){
                        if(!$count[0]['isFinalPay']) $this->removeFinalBenefits($key);
                        $ins    = $this->db->query("UPDATE attendance_confirmed_nt SET forcutoff=1, payroll_cutoffstart='$psdate', payroll_cutoffend='$pedate', status = 'PROCESSED', quarter='$quarter', isFinal='{$count[0]['isFinalPay']}', hold_status='{$count[0]['isOnhold']}' WHERE cutoffstart='$cstart' AND cutoffend='$cend' AND `status` != 'PENDING' AND employeeid = '$key' "); 
                        $this->db->query("DELETE FROM processed_employee WHERE cutoffstart = '$psdate' AND cutoffend = '$pedate' AND employeeid = '$key' ");
                        $this->db->query("INSERT INTO processed_employee (employeeid,cutoffstart,cutoffend,status,remaining_cutoff) VALUES ('$key', '$psdate', '$pedate', 'PROCESSED', '') ");

                        /*check if status is onhold*/
                        if($count[0]['isOnhold']){
                            $this->db->query("UPDATE attendance_confirmed_nt SET forcutoff=1, payroll_cutoffstart='$psdate', payroll_cutoffend='$pedate', quarter='$quarter', hold_status='{$count[0]['isOnhold']}', status = 'SUBMITTED' WHERE cutoffstart='$cstart' AND cutoffend='$cend' AND `status` != 'PENDING' AND employeeid = '$key' ");
                            $this->db->query("DELETE FROM processed_employee WHERE cutoffstart = '$psdate' AND cutoffend = '$pedate' AND employeeid = '$key' ");
                            $this->db->query("DELETE FROM payroll_computed_table WHERE cutoffstart = '$psdate' AND cutoffend = '$pedate' AND employeeid = '$key' AND status != 'PROCESSED' ");
                        }
                        $success_count+=1;
                        /*end*/

                    }else
                        $failed_count+=1;
                        $return = "Failed to Saved!. Cut-Off Already Exists!.";
                }

                
                $recomputed_emp += 1;
                $emplist_total = count($final_pay);

                $this->db->query("UPDATE recomputing_percentage SET emp_count = '$recomputed_emp', emp_total = '$emplist_total', success = '$success_count', failed = '$failed_count' WHERE teachingtype = '$tnt' "); 
            }
        }

        if($tnt == "teaching") $this->db->query("UPDATE recomputing_percentage SET emp_count = '0', emp_total = '0', success = '0', failed = '0' WHERE teachingtype = 'teaching' ");
        else $this->db->query("UPDATE recomputing_percentage SET emp_count = '0', emp_total = '0', success = '0', failed = '0' WHERE teachingtype = 'nonteaching' ");

        if($ins)                $return = "Cut-Off Successfully Saved.";
        if(!$return && !$ins)   $return = "Failed to Saved!. Please Check your connection.";
        return $return;
    }

    
    // Payroll Cut-Off Deduction
    function payrolldeductcutoffsave($data){
        $sched   = $data['schedule'];
        $quarter = $data['quarter'];
        $qdate   = explode(' ',$data['cutoffdate']);
        $sdate   = $qdate[0];
        $edate   = $qdate[1];
        $user     =   $this->session->userdata('username');
        
        $cquery = $this->db->query("SELECT * FROM payroll_employee_deductions WHERE startdate='{$data['deductionsd']}' AND enddate='{$data['deductioned']}' AND schedule='$sched'");
        if($cquery->num_rows() == 0){
            $query = $this->db->query(" INSERT INTO  payroll_employee_deductions (employeeid,startdate,enddate,schedule,quarter,deductcutoffstart,deductcutoffend,latefreq,thlatefreq,minslate,earlydismissal,halfday,absences,failuretolog,attbonusbal,ottime,user)
                                        (SELECT employeeid,startdate,enddate,'$sched','$quarter','$sdate','$edate',latefreq,thlatefreq,minslate,earlydismissal,halfday,absences,failuretolog,attbonusbal,ottime,'$user' 
                                        FROM  payroll_employee_dtr_deductions 
                                        WHERE startdate='{$data['deductionsd']}' AND enddate='{$data['deductioned']}') 
                                      ");
            if($query)  $return = "Cut-Off Successfully Saved!.";
            else        $return = "Failed to Saved!. Please Check your connection..";
        }else{
            $return = "Failed to Saved!. Cut-Off Already Exists!.";
        }
        return $return;
    }
     
    
    /*
     * Payroll Config Content 
     */
     // salary
     function displaySalary($id = ""){
        $whereClause = "";        
        if($id) $whereClause = " WHERE a.employeeid='$id'";
        $query = $this->db->query("SELECT a.*,
                                        (SELECT schedule FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='SSS') as sched,
                                        (SELECT memberid FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='SSS') as sssid,
                                        (SELECT amount FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='SSS') as sssamount,
                                        (SELECT amount FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='SSS_ER') as sssamount_er,
                                        (SELECT cutoff_period FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='SSS') as sssquarter,
                                        
                                        (SELECT memberid FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PAGIBIG') as pagibigid,
                                        (SELECT amount FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PAGIBIG') as pagibigamount,
                                        (SELECT amount FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PAGIBIG_ER') as pagibigamount_er,
                                        (SELECT cutoff_period FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PAGIBIG') as pagibigquarter,
                                        
                                        (SELECT memberid FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PHILHEALTH') as philhealthid,
                                        (SELECT amount FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PHILHEALTH') as philhealthamount,
                                        (SELECT amount FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PHILHEALTH_ER') as philhealthamount_er,
                                        (SELECT cutoff_period FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PHILHEALTH') as philhealthquarter,

                                        (SELECT memberid FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PERAA') as peraaid,
                                        (SELECT amount FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PERAA') as peraaamount,
                                        (SELECT cutoff_period FROM employee_deduction d WHERE d.employeeid = a.employeeid AND code_deduction='PERAA') as peraaquarter
                                        FROM payroll_employee_salary a $whereClause")->result();
        return $query;
     } 
     //loan
     function getEmployeeLoan($employeeid='',$employeeloan='',$schedule='',$quarter='')
     {
        $wC = '';
        if ($employeeloan) { $wC .= " AND a.code_loan='$employeeloan'";}
        if ($schedule) { $wC .= " AND a.schedule='$schedule'";}
        if ($quarter) { $wC .= " AND a.cutoff_period='$quarter'";}
        
        $query = $this->db->query("SELECT * FROM employee_loan a WHERE a.employeeid='$employeeid' $wC");
        return $query;

     }
    //salary
    function getEmployeeSalary($employeeid=''){
        $wC = '';
        if($employeeid) $wC .= " WHERE employeeid='$employeeid'";
        $res = $this->db->query("SELECT * FROM payroll_employee_salary $wC");
        return $res;
    }
     
     // deduction
    function displayDeduction($id = ""){
        $whereClause = "";        
        if($id) $whereClause = " WHERE id='$id'";
        $query = $this->db->query("SELECT id,description,arithmetic,addedby,taxable,grossinc,loanaccount FROM  payroll_deduction_config $whereClause ORDER BY timestamp DESC");
        return $query;
    }
    // loan
    function displayLoan($id = ""){
        $whereClause = "";        
        if($id) $whereClause = " WHERE id='$id'";
        $query = $this->db->query("SELECT id,description,addedby,taxable,grossinc FROM payroll_loan_config $whereClause");
        return $query;
    }
    // income
    function displayIncome($id = ""){
        $whereClause = "";        
        if($id) $whereClause = " WHERE id='$id'";
        $query = $this->db->query("SELECT id,description,taxable,incomeType,grossinc,ismainaccount,mainaccount,deductedby,isIncluded,grosspayNotIncluded,grosspayNotIncludedPhil,grosspayNotIncludedPag,addedby FROM payroll_income_config $whereClause");
        return $query;
    }
    // income
    function displayIncomeOth($id = ""){
        $whereClause = "";        
        if($id) $whereClause = " WHERE id='$id'";
        $query = $this->db->query("SELECT id,description,addedby,taxable,grossinc FROM payroll_income_oth_config $whereClause");
        return $query;
    }
    // payroll cut-off
    function displayCutoff($id = ""){
        $whereClause = "";        
        if($id) $whereClause = " WHERE id='$id'";
        $query = $this->db->query("SELECT id,schedule,quarter,startdate,enddate,addedby,lastupdate FROM payroll_cutoff_config $whereClause");
        return $query;
    }

    
    function displayBankList($code = ""){
        $whereClause = "";        
        if($code) $whereClause = " WHERE code='$code'";
        $query = $this->db->query("SELECT code,account_number,bank_name,branch,`timestamp` FROM code_bank_account $whereClause");
        return $query;
    }

    // payroll data
    function processeddata($id = ""){
        $query = $this->db->query("SELECT * FROM payroll_computed_table WHERE id='$id'");
        return $query;
    }
    // cutoff finalized list
    function cutofffinalizedlist($sdate = "",$edate = "",$type = ""){ 
        if($type == "teaching")
            $query = $this->db->query("SELECT * FROM attendance_confirmed WHERE cutoffstart='$sdate' AND cutoffend='$edate' AND `status`='SUBMITTED'");
        else
            $query = $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE cutoffstart='$sdate' AND cutoffend='$edate' AND `status`='SUBMITTED'");
        return $query;
    }
    // cutoffdeductlist
    function cutoffdeductlist($sdate = "",$edate = ""){
        $whereClause = "";        
        if($sdate) $whereClause = " WHERE startdate='$sdate' AND enddate='$edate'";
        $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname FROM payroll_employee_dtr_deductions a INNER JOIN employee b ON a.employeeid = b.employeeid $whereClause");
        return $query;
    }
    // headcashier data
    function displayHeadCashier(){
        $return = "";
        $query = $this->db->query("SELECT headcashier FROM config_cashier");
        if($query->num_rows() > 0)  $return = $query->row(0)->headcashier;
        return $return;
    }
    
    /*
     *  Cut-Off Saving
     */
    // Cut-off for deduction
    function deduccutoffsaving($data = ""){
        $user = $this->session->userdata("username");
        $queue = $this->db->query("SELECT * FROM payroll_employee_dtr_deductions WHERE employeeid='".$data['eid']."' AND ('".$data['dfrom']."' BETWEEN startdate AND enddate OR '".$data['dto']."' BETWEEN startdate and enddate)");
        if($queue->num_rows() == 0){
        $query = $this->db->query("INSERT INTO payroll_employee_dtr_deductions  
                                    (employeeid,startdate,enddate,latefreq,thlatefreq,minslate,earlydismissal,halfday,absences,failuretolog,attbonusbal,ottime,user) 
                                    VALUES 
                                    ('".$data['eid']."','".$data['dfrom']."','".$data['dto']."','".$data['latefreq']."','".$data['thlatefreq']."','".$data['minslate']."','".$data['earlyd']."','".$data['halfd']."','".$data['tabsences']."','".$data['failtolog']."','".$data['attbonus']."','".$data['ottime']."','$user')
                                  ");
        if($query)  return  "Cut-off for employee no. ".$data['eid']." is Successfully Saved!.";
        else        return  "Cut-off for employee no. ".$data['eid']." is Failed to Saved!.";
        }else       return  "Cut-off for employee no. ".$data['eid']." is already exists or maybe your connection is lost..";
    }
    
   /*
    * Load All Employee Data for Payroll
    */
   function loadAllEmpbyDept($dept = "", $office = "", $eid = "", $sched = "",$campus="", $sdate = "", $edate = "", $forpayroll=false){
        // get schedule of payroll cutoff
        $q_sched = $this->db->query("SELECT schedule FROM payroll_cutoff_config WHERE startdate = '$sdate' AND enddate = '$edate'");
        /*comment for now*/
        if($q_sched->num_rows() > 0) $sched = $q_sched->row()->schedule;
        // $sched = "semimonthly";

        $date = date('Y-m-d');
        $whereClause = "";
        if($dept)   $whereClause .= " AND b.deptid='$dept'";
        if($office)   $whereClause .= " AND b.office='$office'";
        if($eid)    $whereClause .= " AND a.employeeid='$eid'";
        if($campus)    $whereClause .= " AND b.campusid='$campus'";
        if($sdate && $edate) $whereClause .= " AND c.cutoffstart = '$sdate' AND c.cutoffend = '$edate' AND c.`status` = 'PROCESSED' ";
        if($forpayroll) $whereClause .= " AND a.employeeid NOT IN (SELECT employeeid FROM payroll_computed_table WHERE (status='SAVED' OR status='PROCESSED') AND cutoffstart = '$sdate' AND cutoffend = '$edate' AND b.employeeid = '$eid' )";
        // $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname,a.$sched as regpay, b.teachingtype, b.employmentstat
        //                              FROM payroll_employee_salary a 
        //                              INNER JOIN employee b ON b.employeeid = a.employeeid
        //                              INNER JOIN processed_employee c ON c.`employeeid` = b.`employeeid`
        //                              WHERE (b.dateresigned2 = '1970-01-01' OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 IS NULL OR b.dateresigned2 >= '$date' OR b.dateresigned = '1970-01-01' OR b.dateresigned = '0000-00-00' OR b.dateresigned IS NULL OR b.dateresigned >= '$date') AND a.schedule='$sched' $whereClause GROUP BY employeeid")->result();
        $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname,a.$sched as regpay, b.teachingtype, b.employmentstat
                                     FROM payroll_employee_salary a 
                                     INNER JOIN employee b ON b.employeeid = a.employeeid
                                     INNER JOIN processed_employee c ON c.`employeeid` = b.`employeeid`
                                     WHERE ('$date' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive = '1' /*AND a.schedule='$sched'*/ $whereClause GROUP BY employeeid")->result();
        // die();
        return $query;
   } 

    function loadAllEmpbyDeptSample($dept = "", $office="", $eid = "", $sched = "",$campus="", $sdate, $edate){
        $q_sched = $this->db->query("SELECT schedule FROM payroll_cutoff_config WHERE startdate = '$sdate' AND enddate = '$edate'");
        /*comment for now*/
        if($q_sched->num_rows() > 0) $sched = $q_sched->row()->schedule;
        // $sched = "semimonthly";
        
        $date = date('Y-m-d');
        $whereClause = "";
        if($dept)   $whereClause .= " AND b.deptid='$dept'";
        if($office)   $whereClause .= " AND b.office='$office'";
        if($eid)    $whereClause .= " AND a.employeeid='$eid'";
        if($campus)    $whereClause .= " AND b.campusid='$campus'";
        if($sdate && $edate) $whereClause .= " AND c.cutoffstart = '$sdate' AND c.cutoffend = '$edate' AND c.`status` = 'PROCESSED' ";
        // $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname,a.$sched as regpay, b.teachingtype, b.employmentstat
        //                              FROM payroll_employee_salary a 
        //                              INNER JOIN employee b ON b.employeeid = a.employeeid
        //                              INNER JOIN processed_employee c ON c.`employeeid` = b.`employeeid`
        //                              WHERE (b.dateresigned2 = '1970-01-01' OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 IS NULL OR b.dateresigned2 >= '$date' OR b.dateresigned = '1970-01-01' OR b.dateresigned = '0000-00-00' OR b.dateresigned IS NULL OR b.dateresigned >= '$date') AND a.schedule='$sched' $whereClause GROUP BY employeeid")->result();
        $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname,a.$sched as regpay, b.teachingtype, b.employmentstat
                                     FROM payroll_employee_salary a 
                                     INNER JOIN employee b ON b.employeeid = a.employeeid
                                     INNER JOIN processed_employee c ON c.`employeeid` = b.`employeeid`
                                     WHERE ('$date' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive = '1'/* AND a.schedule='$sched'*/ $whereClause GROUP BY employeeid")->result();
        // die();
        return $query;
      
    } 

      /*employeelist for processed payroll*/
    function loadAllEmpbyDeptForProcessed($dept = "", $office = "", $eid = "", $sched = "",$campus="",$edate="",$sdate=""){
        // get schedule of payroll cutoff
        $q_sched = $this->db->query("SELECT schedule FROM payroll_cutoff_config WHERE startdate = '$sdate' AND enddate = '$edate'");
        /*comment for now*/
        if($q_sched->num_rows() > 0) $sched = $q_sched->row()->schedule;
        // $sched = "semimonthly";

        $date = date('Y-m-d');
        $whereClause = "";
        if($dept)   $whereClause .= " AND b.deptid='$dept'";
        if($office)   $whereClause .= " AND b.office='$office'";
        if($eid)    $whereClause .= " AND a.employeeid='$eid'";
        if($campus)    $whereClause .= " AND b.campusid='$campus'";
        if($sdate && $edate) $whereClause .= " AND c.cutoffstart = '$sdate' AND c.cutoffend = '$edate' AND c.`status` = 'PROCESSED' ";
        
        $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname,a.$sched as regpay, b.teachingtype, b.employmentstat
                                     FROM payroll_employee_salary a 
                                     INNER JOIN employee b ON b.employeeid = a.employeeid
                                     INNER JOIN processed_employee c ON c.`employeeid` = b.`employeeid`
                                     WHERE ('$date' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive = '1' /*AND a.schedule='$sched'*/ $whereClause GROUP BY employeeid")->result();
        return $query;
    } 

   /*end*/


   function loadAllEmpbyDeptProcessed($dept = "", $eid = "", $sched = "",$sdate = "",$edate = "",$campus){
        $whereClause = "";
        if($dept)   $whereClause .= " AND b.deptid='$dept'";
        if($eid)    $whereClause .= " AND a.employeeid='$eid'";
        if($sdate)  $whereClause .= " AND a.cutoffstart='$sdate'";
        if($edate)  $whereClause .= " AND a.cutoffend='$edate'";
        if($campus)  $whereClause .= " AND b.campusid='$campus'";
        $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname,a.salary as regpay, d.editedby,c.dependents
                                     FROM payroll_computed_table a 
                                     INNER JOIN employee b ON b.employeeid = a.employeeid
                                      INNER JOIN payroll_employee_salary c ON c.employeeid = a.employeeid
                                     LEFT JOIN payroll_computed_table_adjustment d ON a.employeeid = d.employeeid AND a.cutoffstart = d.cutoffstart AND a.cutoffend = d.cutoffend
                                     WHERE (b.dateresigned = '1970-01-01' OR b.dateresigned = '0000-00-00' OR b.dateresigned IS NULL) AND a.schedule='$sched' $whereClause GROUP BY employeeid ")->result();
        return $query;
   }
   
   /*
    * Computed Employee Payroll Saving
    */
   function computedpayroll($data){
        $sdate          = $data['dfrom'];
        $edate          = $data['dto'];
        $eid            = $data['eid'];  
        $schedule       = $data['schedule'];
        $quarter        = $data['quarter'];
        $regularpay     = $data['regularpay'];
        $income         = $data['income'];
        $ottime         = $data['ottime'];
        $withholding    = $data['withholding'];
        $deductfixed    = $data['deductfixed'];
        $loans          = $data['loans'];
        $deductothers   = $data['deductothers'];
        $tardy          = $data['tardy'];
        $absents        = $data['absents'];
        $user           = $this->session->userdata('username');
        
        $uptloan      =   explode("/",$loans);
        $uptincome    =   explode("/",$income);
        $uptcontri    =   explode("/",$deductfixed);
        $uptothded    =   explode("/",$deductothers);
        
        $cquery = $this->db->query("SELECT * FROM payroll_computed_table WHERE cutoffstart='$sdate' AND cutoffend='$edate' AND employeeid='$eid' AND quarter='$quarter' AND schedule='$schedule'");
        if($cquery->num_rows() == 0){
            $query = $this->db->query("INSERT INTO payroll_computed_table 
                                                (employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,overtime,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,addedby) 
                                        VALUES  ('$eid','$sdate','$edate','$schedule','$quarter','$regularpay','$income','$ottime','$withholding','$deductfixed','$deductothers','$loans','$tardy','$absents','$user')");
            
            $query = $this->db->query("INSERT INTO payroll_computed_table_history 
                                                (employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,overtime,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,addedby) 
                                        VALUES  ('$eid','$sdate','$edate','$schedule','$quarter','$regularpay','$income','$ottime','$withholding','$deductfixed','$deductothers','$loans','$tardy','$absents','$user')");
            
            # Saving of Loan
            if(count($uptloan) > 0 && !empty($loans)){
                for($x = 0; $x<count($uptloan); $x++){
                    $code = explode("=",$uptloan[$x]);
                    $qloan = $this->db->query("SELECT nocutoff,amount,famount FROM employee_loan WHERE employeeid='$eid' AND code_loan='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                    if($qloan->num_rows() > 0){
                        $nocutoff = $qloan->row(0)->nocutoff-1; 
                        $amount = $qloan->row(0)->amount; 
                        $famount = $qloan->row(0)->famount; 
                        if($nocutoff >= 0){
                            $qloan = $this->db->query("UPDATE employee_loan SET nocutoff='$nocutoff' WHERE employeeid='$eid' AND code_loan='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                            $ploan = $this->db->query("INSERT INTO payroll_process_loan 
                                                                (employeeid,code_loan,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
                                                        VALUES  ('$eid','".$code[0]."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
                                                        ");
                        
							$hloan = $this->db->query("SELECT * FROM employee_loan_history WHERE employeeid = '".$eid."' AND code_loan = '".$code[0]."' AND schedule='$schedule' ORDER BY cutoffstart DESC LIMIT 1");
							if($hloan->num_rows() > 0){
								if($nocutoff != 0){ 
									$balance = $hloan->row(0)->remainingBalance - $amount;
									$this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user)
									VALUES('".$eid."','".$code[0]."','$sdate','$edate',".$hloan->row(0)->remainingBalance.",".$amount.",".$balance.",'".$schedule."','".$quarter."','CUTOFF','".$user."')");
								}
								else {
									$balance = $hloan->row(0)->remainingBalance - $famount;
									$this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user)
									VALUES('".$eid."','".$code[0]."','$sdate','$edate',".$hloan->row(0)->remainingBalance.",".$famount.",".$balance.",'".$schedule."','".$quarter."','CUTOFF','".$user."')");
								}
							}
						}						
                    }
                }
            }
            # Saving of Income
            if(count($uptincome) > 0 && !empty($income)){
                for($x = 0; $x<count($uptincome); $x++){
                    $code = explode("=",$uptincome[$x]);
                    $qincome = $this->db->query("SELECT nocutoff FROM employee_income WHERE employeeid='$eid' AND code_income='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                    if($qincome->num_rows() > 0){
                        $nocutoff = $qincome->row(0)->nocutoff-1; 
                        if($nocutoff >= 0){
                            $qincome = $this->db->query("UPDATE employee_income SET nocutoff='$nocutoff' WHERE employeeid='$eid' AND code_income='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                            $pincome = $this->db->query("INSERT INTO payroll_process_income 
                                                                (employeeid,code_income,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
                                                        VALUES  ('$eid','".$code[0]."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
                                                        ");
                        } 
                    }
                }
            }
            # Saving of Fixed Deductions
            if(count($uptcontri) > 0 && !empty($deductfixed)){
                for($x = 0; $x<count($uptcontri); $x++){
                    $code = explode("=",$uptcontri[$x]);
                    list($tcontri,$er,$ec)   =  $this->payroll_collection_contribution($code[1]);
                        $pcontri = $this->db->query("INSERT INTO payroll_process_contribution 
                                                                (employeeid,code_deduct,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
                                                        VALUES  ('$eid','".strtoupper($code[0])."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
                                                    "); 
                                   $this->db->query("INSERT INTO payroll_process_contribution_collection 
                                                                (employeeid,code_deduct,cutoffstart,cutoffend,amount,schedule,cutoff_period,user,ec,amounter,amounttotal) 
                                                        VALUES  ('$eid','".strtoupper($code[0])."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user','$ec','$er','$tcontri')
                                                    "); 
                                                    
                }
            }
            # Saving of Other Deductions
            if(count($uptothded) > 0 && !empty($deductothers)){
                for($x = 0; $x<count($uptothded); $x++){
                    $code = explode("=",$uptothded[$x]);
                    $qincome = $this->db->query("SELECT nocutoff FROM employee_deduction WHERE employeeid='$eid' AND code_deduction='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                    if($qincome->num_rows() > 0){
                    $nocutoff = $qincome->row(0)->nocutoff-1; 
                        if($nocutoff >= 0){
                            $qincome = $this->db->query("UPDATE employee_deduction SET nocutoff='$nocutoff' WHERE employeeid='$eid' AND code_deduction='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");                                        
                            $pcontri = $this->db->query("INSERT INTO payroll_process_otherdeduct 
                                                                    (employeeid,code_deduct,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
                                                            VALUES  ('$eid','".strtoupper($code[0])."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
                                                        "); 
                        }             
                    }                                                                                           
                }
            }
            
            if($query)  $return = "Cut-Off for Employee No. ($eid) is Successfully Saved.!";
        }else{
            $return = "Cut-Off for Employee No. ($eid) is already exists..";
        }
        return $return; 
   }
   function payroll_collection_contribution($val=""){
    $arr = array("","","");
    $query = $this->db->query("SELECT * FROM sss_deduction WHERE emp_ee='$val'");
    if($query->num_rows() > 0){
        $arr = array($query->row(0)->total_contribution,$query->row(0)->emp_er,$query->row(0)->emp_con);
    }
    return $arr;
   }
   function modPayroll($data){
    $id             = $data['id'];
    $empid          = $data['empid'];
    $schedule       = $data['schedule'];
    $quarter        = $data['quarter'];
    $sdate          = $data['cutoffstart'];
    $edate          = $data['cutoffend'];
    $salary         = $data['salary'];
    $income         = "";
    $fixeddedduc    = "";
    $otherdeduc     = "";
    $loan           = "";
    $whtax          = $data['whtax'];
    $tardy          = $data['tardy'];
    $absents        = $data['absents'];
    $user           = $this->session->userdata('username');
    // Income
    $x = 0;
    if($this->payrolloptions->incometitlep('',$schedule,$quarter,$sdate,$edate)){
        foreach($this->payrolloptions->incometitlep('',$schedule,$quarter,$sdate,$edate) as $row){
            $x++;
            if(!empty($income)) $income .= "/";
            $income .= $row."=".$data['income'.$x];
        }
    }
    // Fixed Deduction
    $x = 0;
    if($this->payrolloptions->deducttitlep('',$schedule,$quarter,$sdate,$edate)){
        foreach($this->payrolloptions->deducttitlep('',$schedule,$quarter,$sdate,$edate) as $row){
            $x++;
            if(!empty($fixeddedduc)) $fixeddedduc .= "/";
            $fixeddedduc .= $row."=".$data['fixd'.$x];
        }
    }
    // Other Deduction
    $x = 0;
    if($this->payrolloptions->deducttitleothp('',$schedule,$quarter,$sdate,$edate)){
        foreach($this->payrolloptions->deducttitleothp('',$schedule,$quarter,$sdate,$edate) as $row){
            $x++;
            if(!empty($otherdeduc)) $otherdeduc .= "/";
            $otherdeduc .= $row."=".$data['otd'.$x];
        }
    }
    // Loan
    $x = 0;
    if($this->payrolloptions->loantitlep('',$schedule,$quarter,$sdate,$edate)){
        foreach($this->payrolloptions->loantitlep('',$schedule,$quarter,$sdate,$edate) as $row){
            $x++;
            if(!empty($loan)) $loan .= "/";
            $loan .= $row."=".$data['loans'.$x];
        }
    }
    
    $query = $this->db->query("UPDATE payroll_computed_table SET income='$income', withholdingtax='$whtax', fixeddeduc='$fixeddedduc', otherdeduc='$otherdeduc', loan='$loan', tardy='$tardy', absents='$absents', editedby='$user'  WHERE id='$id'");
    $query = $this->db->query("INSERT INTO payroll_computed_table_adjustment 
                                        (employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,editedby) 
                                VALUES  ('$empid','$sdate','$edate','$schedule','$quarter','$salary','$income','$whtax','$fixeddedduc','$otherdeduc','$loan','$tardy','$absents','$user')");
    if($query)  return "Data Successfully Saved!.";
    else        return "Failed to Saved.. Please check your connection..";
   }
   
   /*
    * OVERTIME ACCEPTANCE AND CHECKING
    */
   // Accept Overtime
   function acceptOT($data){
    $ottime = $data['ot'];
    $eid    = $data['eid'];
    $otdate = $data['otdate'];
    $user   = $this->session->userdata("username");
    $msg    = ""; 
    $query = $this->db->query("SELECT * FROM payroll_emp_otaccepted WHERE employeeid='$eid' AND otdate='$otdate'");
    if($query->num_rows() > 0){
        $msg = "Overtime for ".date('F d, Y',strtotime($otdate))." is already exists..";
    }else{
        $query = $this->db->query("INSERT INTO payroll_emp_otaccepted (employeeid,otdate,overtime,user) VALUES ('$eid','$otdate','$ottime','$user')");
        if($query) $msg = "Overtime for ".date('F d, Y',strtotime($otdate))." is successfully saved!.";  
    }
    return $msg;
   }
   // Check Accepted Overtime
   function otchecking($eid = "",$otdate = ""){
    $query = $this->db->query("SELECT * FROM payroll_emp_otaccepted WHERE employeeid='$eid' AND otdate='$otdate'");
    if($query->num_rows() > 0)  return true;
    else                        return false;    
   }
   

   /*
    Employee Ledger Functions
   */
    function EmpLedgerData($eid,$empstat,$deptid,$year)
    {
        $where = "";
        if ($eid) $where .= "AND b.employeeid='$eid'";
        if ($empstat) $where .= "AND b.employmentstat='$empstat'";
        if ($deptid) $where .= "AND b.deptid='$deptid'";
        $query = $this->db->query("SELECT a.*,b.emp_sss,b.emp_tin,b.deptid,b.employmentstat,CONCAT(lname,',',fname,' ',mname) as fullname FROM payroll_computed_table a INNER JOIN employee b ON (b.employeeid = a.employeeid) WHERE YEAR(cutoffstart) = '$year' $where ")->result();
        return $query; 
    }
    function getEmploymentStatus($stat)
    {
        $description = "";
        $query =$this->db->query("SELECT description FROM code_status WHERE code='$stat'");
        if ($query->num_rows() > 0) {
            $description = $query->row(0)->description;            
        }
        return $description;
    }
    
   /*
    * Payslip FUNCTIONS 
    */
    //SlipRecord LIMIT 2 by naces
   function SlipRecord($eid = "",$sched = "",$quarter = "",$sdate = "",$edate = "",$dept = "",$office = "",$campus="",$sort = "",$status=""){
        $whereClause = "";
        if($eid)    $whereClause .= " AND a.employeeid='$eid'";
        if($dept)   $whereClause .= " AND b.deptid='$dept'";
        if($office)   $whereClause .= " AND b.office='$office'";
        if($campus)   $whereClause .= " AND b.campusid='$campus'";
        if($status)   $whereClause .= " AND a.`status`='$status'";
        if($sort == "department") $whereClause .= " ORDER BY b.office";
        else $whereClause .= " ORDER BY fullname"; 
        // $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname, b.emp_accno FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE schedule='$sched' AND quarter='$quarter' AND cutoffstart='$sdate' AND cutoffend='$edate' $whereClause LIMIT 2")->result();
        $query = $this->db->query("SELECT DISTINCT b.campusid,a.employeeid,d.description,c.daily,c.hourly,a.*, CONCAT(lname,', ',fname,' ',mname) as fullname, b.emp_accno FROM payroll_computed_table a 
            INNER JOIN employee b ON a.employeeid = b.employeeid 
            INNER JOIN payroll_employee_salary c ON c.employeeid = a.employeeid
            LEFT JOIN code_department d ON d.code = b.deptid
            WHERE ( b.dateresigned = '1970-01-01' OR b.dateresigned = '0000-00-00' OR b.dateresigned IS NULL)
            AND a.schedule='$sched' AND quarter='$quarter' AND cutoffstart='$sdate' AND cutoffend='$edate' $whereClause ")->result();
        // echo "<pre>"; print_r($this->db->last_query()); die;
        return $query; 
    } 
   
    function PayrollRegistrar($eid = "",$sched = "",$quarter = "",$sdate = "",$edate = "",$dept = "",$office = "",$campus="",$sort = "",$status=""){
        $whereClause = "";
        if($eid)    $whereClause .= " AND a.employeeid='$eid'";
        if($dept)   $whereClause .= " AND b.deptid='$dept'";
        if($office)   $whereClause .= " AND b.office='$office'";
        if($campus)   $whereClause .= " AND b.campusid='$campus'";
        if($status)   $whereClause .= " AND a.`status`='$status'";
        if($sort)   $whereClause .= " ORDER BY a.employeeid";
        else        $whereClause .= " ORDER BY b.deptid,a.employeeid";
        // $query = $this->db->query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname, b.emp_accno FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE schedule='$sched' AND quarter='$quarter' AND cutoffstart='$sdate' AND cutoffend='$edate' $whereClause LIMIT 2")->result();
        $query = $this->db->query("SELECT DISTINCT b.campusid,a.employeeid,b.deptid,c.daily,c.hourly,a.*, CONCAT(lname,', ',fname,' ',mname) as fullname, b.emp_accno FROM payroll_computed_table a 
            INNER JOIN employee b ON a.employeeid = b.employeeid 
            INNER JOIN payroll_employee_salary c ON c.employeeid = a.employeeid
            WHERE ( b.dateresigned = '1970-01-01' OR b.dateresigned = '0000-00-00' OR b.dateresigned IS NULL)
            AND a.schedule='$sched' AND quarter='$quarter' AND cutoffstart='$sdate' AND cutoffend='$edate' $whereClause ")->result();
        // die();
        
        return $query; 
    } 
   function printpayslip($schedule,$sdate,$edate,$quarter){
        $whereClause = "";
        $query = $this->db->query("SELECT * FROM payroll_computed_table WHERE schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$sdate' AND cutoffend='$edate'");
        return $query->num_rows(); 
   }
   function getEmpDept($eid = ""){
        $query  = $this->db->query("SELECT b.description FROM employee a INNER JOIN code_office b ON a.deptid = b.code WHERE a.employeeid='$eid'")->result();
        return $query->row(0)->description;
   }
   
   /*
    * Payroll Reports Module   
    */
   function listEmployeeWithSalary($empid = "", $dept = ""){
    $wC = "";
    if($dept)   $wC = " AND deptid='$dept'";
    if($empid)  $wC = " AND a.employeeid='$empid'";
    $query = $this->db->query("SELECT a.employeeid,CONCAT(lname,', ',fname,' ',mname) as fullname,b.semimonthly,IFNULL(b.whtax,0) as whtax FROM employee a
                                LEFT JOIN payroll_employee_salary b ON a.employeeid = b.employeeid
                                WHERE a.dateresigned = '1970-01-01' $wC");
    return $query;
   }
   function listHeaders($tbl = "",$title = ""){
    $query = $this->db->query("SELECT $title FROM $tbl")->result();
    return $query;
   }
   function listhiddenHeaders($tbl = "",$title = ""){
    $query = $this->db->query("SELECT $title FROM $tbl WHERE visibility='HIDDEN' GROUP BY code_deduction")->result();
    return $query;
   }
   function listAmount($tbl = "",$amt = "",$eid = "",$code = "",$visible = false,$col = ""){
    $wC = "";
    if($eid)        $wC = " WHERE employeeid='$eid'";
    if($code)       $wC .= " AND $col='$code'";
    if($visible)    $wC .= " AND visibility='HIDDEN'";    
    $query = $this->db->query("SELECT $amt FROM $tbl $wC");
    if($query->num_rows() > 0)  return $query->row(0)->amount;
    else                        return 0;
   }
    
   /*
    * Head Cashier
    */ 
   function hcsave($data){
        $query = $this->db->query("SELECT * FROM config_cashier");
        if($query->num_rows() > 0){
            $query = $this->db->query("UPDATE config_cashier SET headcashier='".$data['headcashier']."', addedby='".$this->session->userdata("username")."'");
            if($query)  $return = "Record Updated!.";
        }else{
            $query = $this->db->query("INSERT INTO config_cashier(headcashier,addedby) VALUES ('".$data['headcashier']."','".$this->session->userdata("username")."')");
            if($query)  $return = "Successfully Saved!.";
            else        $return = "Failed to Saved!. Please check your connection..";
        }
        return $return;
   } 
   /*
    * Recompute Tax
    */
   function recomputetax($data){
       $return = ""; 
       $codate = $data['cutoffdate'];
       $codate = explode('*',$codate);
       $dfrom  = $codate[0];
       $dto    = $codate[1]; 
       $query = $this->db->query("SELECT employeeid, withholdingtax FROM payroll_computed_table WHERE cutoffstart='$dfrom' AND cutoffend='$dto'")->result();
       foreach($query as $row){
        $eid   = $row->employeeid;
        $whtax = $row->withholdingtax;
        $this->db->query("UPDATE payroll_employee_salary SET whtax='$whtax' WHERE employeeid='$eid'");
        
       }
       if($query)   $return = "Successfully Saved!.";
       else         $return = "Failed to Recompute.. Please Check your connection";
       return $return;
   }
   /*
    * Delete Tax
    */
   function deletetax($data){
       $return = ""; 
       $query = $this->db->query("UPDATE payroll_employee_salary SET whtax=''");
       if($query)   $return = "Successfully Saved!.";
       else         $return = "Failed to Recompute.. Please Check your connection";
       return $return;
   }

   /*
    * EXCEL PAG-IBIG,SSS glen,naces
    */
   function getpagibigLoanExcel($eid = "",$sched = "",$quarter = "",$sdate = "",$edate = "",$dept = "")
   {
        $wC = "";
         if($eid)    $wC .= " AND a.employeeid='$eid'";
         if($dept)   $wC .= " AND a.deptid='$dept'";
        $query = $this->db->query("SELECT a.emp_tin,a.emp_pagibig,a.employeeid,a.lname,a.fname,a.mname,a.bdate,b.fixeddeduc FROM employee a
        INNER JOIN payroll_computed_table b ON (a.`employeeid` = b.`employeeid`) WHERE b.schedule ='$sched' AND b.quarter='$quarter' AND 
        b.cutoffstart='$sdate' AND b.cutoffend='$edate' ");
        // die();

    return $query; 
   }


   function getSSSContributionExcel($eid = "",$sched = "",$quarter = "",$sdate = "",$edate = "",$dept = "")
   {
        $wC = "";
         if($eid)    $wC .= " AND a.employeeid='$eid'";
         if($dept)   $wC .= " AND a.deptid='$dept'";
        $query = $this->db->query("SELECT a.emp_sss,a.lname,a.fname,a.mname,a.bdate,a.dateemployed,b.`fixeddeduc` FROM employee a
        INNER JOIN payroll_computed_table b ON (a.`employeeid` = b.`employeeid`) WHERE b.schedule ='$sched' AND b.quarter='$quarter' AND 
        b.cutoffstart='$sdate' AND b.cutoffend='$edate' and b.status='PROCESSED' ");
        return $query; 
   }

   function getSSScontribution($amount,$type)
   {
        $return = "";
        if ($type == "EC") {
            $query = $this->db->query("SELECT emp_con FROM sss_deduction WHERE emp_ee='$amount'");
                    if ($query->num_rows() > 0) {
                        $return = $query->row()->emp_con;
                    }
                    else
                    {
                        $return = 0.00;
                    }
        }
        else
        {
            $query = $this->db->query("SELECT total_contribution FROM sss_deduction WHERE emp_ee='$amount'");
            if ($query->num_rows() > 0) {
                $return = $query->row()->total_contribution;
            }
            else
            {
                $return = 0.00;
            }    
        }
        
        return $return;
   }

  # for ica-hyperion 21503
  # by justin (with e)
  function getEmployeeIncome($employeeid, $code_income, $schedule, $quarter=""){
    $wc ="";
    if($schedule) $wc .= "AND schedule = '$schedule'";
    if($quarter) $wc .= "AND cutoff_period = '$quarter'";
    $q_emp_income = $this->db->query("SELECT * FROM employee_income WHERE employeeid='$employeeid' AND code_income='$code_income' AND visibility='SHOW' $wc;")->result();
    return $q_emp_income;
  }

  function findEmpIncomeHistory($employeeid, $code_income){
    $q_emp_income = $this->db->query("SELECT * FROM employee_income_history WHERE employeeid='$employeeid' AND code_income='$code_income' AND visibility='SHOW' ORDER BY datecreated DESC LIMIT 1;");
    return $q_emp_income;
  }

  function saveBEIncome($isUpdate, $updateClause, $tbl_fields, $valueClauses, $empid, $code_income, $sched){
    $query = "";
    if($isUpdate) $query = "UPDATE employee_income SET $updateClause WHERE employeeid='$empid' AND code_income='$code_income';";
    else          $query = "INSERT INTO employee_income (employeeid, code_income, $tbl_fields , `schedule`) VALUES ('$empid','$code_income', $valueClauses , '$sched');";

    $q_saveIncome = $this->db->query($query);

    return $q_saveIncome;
  }

  function saveBEIncomeHistory($tbl_fields, $valueClauses, $empid, $code_income){
    $userid = $this->session->userdata('username');

    $query = "INSERT INTO employee_income_history (employeeid, code_income, $tbl_fields , `schedule`, status, userid) VALUES ('$empid','$code_income', $valueClauses , 'semimonthly', 'SAVED', '$userid');";
    
    $q_saveIncomeHistory = $this->db->query($query);

    return $q_saveIncomeHistory;
  }
  # end for ica-hyperion 21503

  ///< @Angelica - copy for ICA-Hyperion21501
  # for mcu-hyperion 21478
  # by justin (with e)
  function getEmpReglamentoryStatusHistory($empid, $code_deduction){
    $q_latest_status = $this->db->query("SELECT status FROM employee_deduction_history WHERE employeeid='$empid' AND code_deduction='$code_deduction';")->result();

    return $q_latest_status;
  }

  function findEmpReglamentory($empid, $code_deduction){
    $q_reglamentory = $this->db->query("SELECT * FROM employee_deduction WHERE employeeid='$empid' AND code_deduction='$code_deduction';")->result();

    return $q_reglamentory;
  }

  function saveBEReglamentory($empid, $code_deduction, $fieldsClauses, $valueClauses, $updateClause, $isExist){
    $query = "";

    if($isExist)
      $query = "UPDATE employee_deduction SET $updateClause WHERE employeeid='$empid' AND code_deduction='$code_deduction';";
    else
      $query = "INSERT INTO employee_deduction (employeeid, code_deduction, $fieldsClauses ,visibility, schedule) VALUES ('$empid', '$code_deduction', $valueClauses ,'HIDDEN', 'semimonthly');";

    $q_saveReglamentory = $this->db->query($query);
    return $q_saveReglamentory;
  }

  function saveBEReglamentoryHistory($empid, $code_deduction, $fieldsClauses, $valueClauses){
    $userid = $this->session->userdata('username');

    $q_saveReglamentoryHistory = $this->db->query("INSERT INTO employee_deduction_history (employeeid, code_deduction, $fieldsClauses , visibility, userid, schedule) VALUES ('$empid', '$code_deduction', $valueClauses ,'HIDDEN', '$userid', 'semimonthly');");

    return $q_saveReglamentoryHistory;

  }
  # end for mcu-hyperion 21478

    function getIncomeDescription($id){
      $return = "";
      $query = $this->db->query("SELECT description FROM payroll_income_config WHERE id='$id'")->result();
      foreach ($query as $data) {
        $return = $data->description;
      }
      return $return;

    }
  
    # for ica-hyperion 21671
    function getPayrollRegistrarEmpList($emp_id, $deptid, $cutoffstart, $cutoffend, $quarter, $status='PENDING', $bank='', $sort = '', $teachingtype= ''){
        $whereClause = ($status != 'PENDING') ? "AND a.bank='$bank'" : "";
        $orderClause = '';
        if($emp_id) $whereClause .= " AND b.employeeid = '$emp_id'";
        if($deptid) $whereClause .= " AND b.deptid = '$deptid'";
        if($teachingtype) $whereClause .= " AND b.teachingtype = '$teachingtype'";
        if($sort == 'department') $orderClause = 'deptid ASC, fullname ASC';
        else $orderClause = 'fullname ASC';
        if($orderClause) $orderClause = " ORDER BY $orderClause ";
        $q_emp_list = $this->db->query("SELECT CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname, c.code AS dept_code, c.description AS dept_desc, d.code AS campus_code, d.description AS campus_desc,a.*, b.office as deptid, b.campusid 
                                        FROM payroll_computed_table a
                                        LEFT JOIN employee b ON b.employeeid = a.employeeid
                                        LEFT JOIN code_office c ON c.code = b.deptid
                                        LEFT JOIN code_campus d ON d.code = b.campusid
                                        WHERE a.cutoffstart='$cutoffstart' AND a.cutoffend='$cutoffend' AND a.quarter='$quarter' AND a.status='$status' $whereClause
                                       
                                        GROUP BY b.employeeid  $orderClause
                                      ")->result();
        // echo "<pre>"; print_r($this->db->last_query()); die;
        return $q_emp_list;
    }
    # end for ica-hyperion 21671

    function deleteBEIncome($empId,$code_income){
        $query = "";
        $query = "DELETE FROM employee_income WHERE employeeid='$empId' AND code_income='$code_income';";
        $q_saveIncome = $this->db->query($query);
        return $q_saveIncome;
    }


    function clearZeros($tbl){
        $query = "";
        if($tbl != "employee_loan") $query = "UPDATE $tbl SET nocutoff = null, datefrom = '', dateto = '', amount = '', cutoff_period='' where nocutoff = 0";
        else $query = "UPDATE $tbl SET nocutoff = null, datefrom = '', dateto = '', amount = '',startingamount='',currentamount='', cutoff_period='' where nocutoff = 0";
        $q_saveIncome = $this->db->query($query);
        return $q_saveIncome;
    }

   function deleteBEDeduction($empId,$code_deduc){
        $query = "";
        $query = "DELETE FROM employee_deduction WHERE employeeid='$empId' AND code_deduction='$code_deduc';";
        $q_saveDeduction = $this->db->query($query);
        return $q_saveDeduction;
    }

   function deleteEmployeeSalary($employeeid){

        $query = "";
        $query = "DELETE FROM payroll_employee_salary WHERE employeeid='{$employeeid['eid']}';";
        $q_saveSalary = $this->db->query($query);
        return $q_saveSalary;
    }

    function clearBEReglamentory($empId){
        $query = "";
        $query = "UPDATE employee_deduction SET amount='0', cutoff_period='4' WHERE employeeid='{$empId}';";
        $q_clearDeduction = $this->db->query($query);
        return $q_clearDeduction;
    }  

    function getEmployeeRateBased($employeeid){
        $query = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE employeeid = '$employeeid' ORDER BY TIMESTAMP DESC LIMIT 1");
        if($query->num_rows > 0) return $query->row()->ratebased;
        else return FALSE;
    }

    function removeFinalBenefits($key){
        $query = $this->db->query("DELETE FROM `employee_income` WHERE (code_income = '37' OR code_income = '5') AND employeeid = '$key' ");
    }

    function getPerDeptLecLabPay($employeeid=''){
        $data = array();
        $wC = '';
        if($employeeid) $wC .= "  WHERE employeeid='$employeeid'";

        $res = $this->db->query("SELECT * FROM payroll_emp_salary_perdept $wC");
        if($res->num_rows() > 0){
          foreach ($res->result() as $key => $row) {
            $data[$row->employeeid][$row->id] = array('aimsdept'=>$row->aimsdept,'lechour'=>$row->lechour,'labhour'=>$row->labhour);
          }
        }
        return $data;
     }

    function deleteSameDateSalary($date_effective, $eid){
        $this->db->query("DELETE FROM payroll_employee_salary_history WHERE DATE(date_effective) = '$date_effective' AND employeeid = '$eid' ");
    }

    function checkProcessedPayroll($employeeid='',$date_effective=''){
      $hasProcessedPayroll = false;
      $payroll_q = $this->db->query("SELECT id FROM payroll_computed_table WHERE `status`='PROCESSED' AND employeeid='$employeeid' AND cutoffstart = '$date_effective'");
      if($payroll_q->num_rows() > 0) $hasProcessedPayroll = true;
      return $hasProcessedPayroll;
    }

    function getEmployeeSalaryHistory($employeeid=''){
        $salary_list = array();
        $base_q = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE employeeid='$employeeid'");
        foreach ($base_q->result() as $key => $row) {
            $base_id = $row->id;

            $salary_list[$base_id] = array(
                                      'employeeid' => $row->employeeid,
                                      'fixedday' => $row->fixedday,
                                      'type' => $row->type,
                                      'monthly' => $row->monthly,
                                      'semimonthly' => $row->semimonthly,
                                      'daily' => $row->daily,
                                      'hourly' => $row->hourly,
                                      'minutely' => $row->minutely,
                                      'date_effective' => $row->date_effective,
                                      'timestamp' => $row->timestamp
                                    );
            
            $perdept_arr = array();
            if($base_id){
                $perdept_q = $this->db->query("SELECT * FROM payroll_emp_salary_perdept_history WHERE base_id='$base_id' AND employeeid='$employeeid'");
                foreach ($perdept_q->result() as $pd_row) {
                  $perdept_arr[$pd_row->aimsdept] = array('lechour'=>$pd_row->lechour,'labhour'=>$pd_row->labhour, 'aims'=>$pd_row->aimsdept);

                }
            }

            $salary_list[$base_id]['perdept_arr'] = $perdept_arr;
        }
        return $salary_list;
    }

    function getEmpSalaryPerDept($employeeid, $date, $aimsdept, $type){
        $col_selected = strtolower($type) ."hour";

        $q_emp_salary_perdept = $this->db->query("SELECT $col_selected AS rate FROM payroll_emp_salary_perdept_history WHERE employeeid='$employeeid' AND `timestamp` <= '$date' AND aimsdept='$aimsdept' ORDER BY `timestamp` DESC LIMIT 1;")->result();

        return $q_emp_salary_perdept;
    }

    function getRateHistory($employeeid = '',$deptid,$tnt,$erryear,$sort){
        $user_access = getPayrollAccess();
        $salary_list = array();
        // sort by employee
        if(!empty($employeeid)){
            $this->db->where('payroll_employee_salary_history.employeeid', $employeeid);
        }
        // sort by department
        if(!empty($deptid)){
            $this->db->where('employee.deptid', $deptid);
        }
        // sort by teaching type
        if(!empty($tnt)){
            $this->db->where('employee.teachingtype', $tnt);
        }
        // sort by year
        $this->db->where('payroll_employee_salary_history.timestamp <', $erryear."-12-31 24:00:00");
        $this->db->where('employee.positionid', $user_access);
        $this->db->from('payroll_employee_salary_history');
        $this->db->select('*');
        $this->db->join('employee', 'payroll_employee_salary_history.employeeid = employee.employeeid', 'left');
        $this->db->join('code_office', 'employee.deptid = code_office.code', 'left');
        $this->db->select("CONCAT(employee.lname,' ',employee.fname,' ',employee.mname) as name",FALSE);

        if(!empty($sort)){
            $this->db->order_by($sort, "asc"); 
        }
        $this->db->order_by('date_effective', "desc"); 
        $base_q = $this->db->get();
        $concat = $this->db->last_query();

        foreach ($base_q->result() as $key => $row) {
            $base_id = $row->id;
            $salary_list[$base_id] = array(
            'employeeid' => $row->employeeid,
            'base_id' => $row->id,
            'code' => $row->code,
            'positionid' => $row->positionid,
            'description' => $row->description,
            'name' => $row->name,
            'teachingtype' => $row->teachingtype,
            'fixedday' => $row->fixedday,
            'monthly' => $row->monthly,
            'daily' => $row->daily,
            'hourly' => $row->hourly,
            'timestamp' => $row->timestamp,
            'date_effective' => $row->date_effective
            );
            $perdept_arr = array();
            if($base_id){
                $this->db->where('base_id', $base_id);
                if(!empty($employeeid)){
                    $this->db->where('employeeid', $employeeid);
                }
                $this->db->from('payroll_emp_salary_perdept_history');
                $this->db->order_by('timestamp', "desc"); 
                $perdept_q = $this->db->get();
                foreach ($perdept_q->result() as $pd_row) {
                    $perdept_arr[$pd_row->aimsdept] = array('lechour'=>$pd_row->lechour,'labhour'=>$pd_row->labhour,'aims'=>$pd_row->aimsdept,'timestamp'=>$pd_row->timestamp);
                } 
            }
            $salary_list[$base_id]['perdept_arr'] = $perdept_arr;
        }

        return $salary_list;

    }

    public function getWorkHoursPerdept($employeeid, $dfrom, $dto){
        $q_workhours = $this->db->query("SELECT b.*, c.`DESCRIPTION` FROM payroll_computed_table a INNER JOIN payroll_computed_perdept_detail b ON a.id = b.`base_id` INNER JOIN tblCourseCategory c ON b.`aimsdept` = c.`GROUP_ID` WHERE cutoffstart = '$dfrom' AND cutoffend = '$dto' AND employeeid = '$employeeid' ORDER BY type ");
        if($q_workhours->num_rows() > 0) return $q_workhours->result();
        else return false;
    }

    public function insertPhilhealthShare($data){
        return $this->db->insert("philhealth_empshare", $data);
    }

    public function updatePhilhealthShare($data, $id){
        $this->db->where("id", $id);
        $this->db->set($data);
        return $this->db->update("philhealth_empshare");
    }

    public function deletePhilhealthShare($id){
        return $this->db->query("DELETE FROM philhealth_empshare WHERE id = '$id'");
    }

    public function employeeDropdown($office, $deptid){
        $wc = "";
        if($office) $wc = " AND office = '$office' ";
        if($deptid) $wc = " AND deptid = '$deptid' ";
        return $this->db->query("SELECT * FROM employee WHERE employeeid != '' $wc");
    }

    public function getLatestSalaryEffective($eid){
        $q_salary = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE employeeid = '$eid' ORDER BY date_effective DESC LIMIT 1");
        if($q_salary->num_rows()) return $q_salary->row()->date_effective;
        else return false;
    }

    public function displayEmployeeDeduction($id = ""){
        $deduc_arr = array();
        $query = $this->db->query("SELECT * FROM employee_deduction WHERE employeeid = '$id'");
        if($query->num_rows() > 0){
            foreach($query->result_array() as $row){
                if($row["code_deduction"] == "PAGIBIG"){
                    $deduc_arr["pagibigamount"] = $row["amount"];
                    $deduc_arr["sssid"] = $row["memberid"];
                    $deduc_arr["sssquarter"] = $row["schedule"];
                }elseif($row["code_deduction"] == "SSS"){
                    $deduc_arr["sssamount"] = $row["amount"];
                    $deduc_arr["sssamount"] = $row["memberid"];
                    $deduc_arr["sssamount"] = $row["schedule"];
                }elseif($row["code_deduction"] == "PHILHEALTH"){
                    $deduc_arr["philhealthamount"] = $row["amount"];
                    $deduc_arr["philhealthamount"] = $row["memberid"];
                    $deduc_arr["philhealthamount"] = $row["schedule"];
                }
            }
        }

        return $deduc_arr;
     } 

    public function isBankUsed($bank){
        return $this->db->query("SELECT DISTINCT bank FROM payroll_computed_table WHERE bank = '$bank'")->num_rows();
    }

    public function is_payroll_processed($empid, $sdate, $edate){
        return $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '$empid' AND cutoffstart = '$sdate' AND cutoffend = '$edate' AND STATUS IN ('SAVED', 'PROCESSED');")->num_rows();
    }

}
