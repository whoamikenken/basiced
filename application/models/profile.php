<?php 
/**
 * @author Max Consul
 * @copyright 2018
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Profile extends CI_Model {

	/**
	* Query for processing employee data
	*
	* @return query result
	*/

	public function saveEmployeeDeduction($employeeid, $deduction, $memberid, $remarks="", $dsetfrom, $dsetto, $amount, $nocutoff, $schedule, $period){
		$this->db->query("CALL prc_employee_deduction_set('$employeeid', '$deduction', '$memberid', '$remarks', '$dsetfrom', '$dsetto', '$amount', '$nocutoff', '$schedule', '$period')");
	}

	public function saveEmployeeIncome($employeeid, $income, $income_remarks, $dsetfrom, $dsetto, $amount, $nocutoff, $incomebase, $schedule, $period){
		$this->db->query("CALL prc_employee_income_set('$employeeid', '$income', '$income_remarks', '$dsetfrom', '$dsetto', '$amount', '$nocutoff', '$incomebase', '$schedule', '$period')");
	}

	public function saveEmployeeIncomeAdj($employeeid, $income, $income_remarks, $dsetfrom, $dsetto, $amount, $nocutoff, $incomebase, $schedule, $period, $deduct, $taxable){
		$this->db->query("CALL prc_employee_income_adj_set('$employeeid', '$income', '$income_remarks', '$dsetfrom', '$dsetto', '$amount', '$nocutoff', '$incomebase', '$schedule', '$period', '$deduct', '$taxable')");
	}

	public function saveEmployeeLoanHistory($employeeid, $loan, $dsetfrom, $dsetto, $currentamount, $amount, $currentamount, $schedule, $period, $status, $user, $startingamount, $remarks, $datefrom, $famount, $nocutoff, $basedon){
		$status = $message = "";
		$query = $this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user,currentBalance)
									VALUES('$employeeid', '$loan', '$dsetfrom', '$dsetto', '$currentamount', '$amount', '$currentamount', '$schedule', '$period', '$status', '$user', '$startingamount')");
        if($query){
            $update = $this->db->query("UPDATE employee_loan SET remarks = '$remarks',datefrom='$datefrom',amount='$amount',startingamount='$currentamount',famount='$famount',nocutoff='$nocutoff',loan_base='$basedon',schedule='$schedule',cutoff_period='$period',currentamount='$startingamount' WHERE id='$id'");
            if($update){
                $status = 1;
                $message = "Successfully Updated!";
            }
            else{
                $status = 1;
                $message = "Failed to Update!"; 
            }
        }

        return array($status, $message);
	}

	public function saveEmployeeLoanHistoryPRC($id,$employeeid,$loan,$remarks,$dsetfrom,$dsetto,$amount,$startingamount, $famount,$nocutoff,$basedon,$schedule,$period, $currentamount){
		$query = $this->db->query("CALL prc_employee_loan_set('$id','$employeeid','$loan','$remarks','$dsetfrom','$dsetto','$amount','$startingamount', '$famount','$nocutoff','$basedon','$schedule','$period','$currentamount')");
            $status = 1;
            $message = "Successfully Added!";
            return array("1", "Successfully Added!");
	}

	public function checkEmployeeLoan($loan, $employeeid){
		return $this->db->query("SELECT * FROM employee_loan WHERE code_loan = '$loan' AND employeeid='$employeeid' ");
	}

	public function saveEmployeeOtherIncome($employeeid, $income, $amount, $pos){
        $this->db->query("CALL prc_employee_income_oth_set('$employeeid', '$income', '$amount', '$pos')");
	}

	public function saveEmployeeSchedule($employeeid,$start_time,$end_time,$dw,$dow,$tstart,$astart,$tsecond,$asecond,$nosched,$halfsched,$early_d, $user, $date_active,$types,$flexible,$flexi_hours,$flexi_breaktime,$day,$course,$section,$subject,$aims, $weekly_sched){
        $this->db->query("INSERT INTO employee_schedule(employeeid, starttime,endtime,`dayofweek` ,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,editedby, dateedit,leclab,flexible,`hours`,breaktime,`mode`,`course`,`section`,`subject`,`aimsdept`, `weekly_sched`) 
                        VALUES('$employeeid','$start_time','$end_time','$dw','$dow','$tstart','$astart','$tsecond','$asecond','$nosched','$halfsched','$early_d', '$user', '$date_active','$types','$flexible','$flexi_hours','$flexi_breaktime','$day','$course','$section','$subject','$aims','$weekly_sched') ");
	}

	public function saveEmployeeScheduleHistory($employeeid){
		$this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,changeby,dateactive,leclab,flexible,`hours`,breaktime,`mode`,`course`,`section`,`subject`,`aimsdept`, `weekly_sched`) 
                                                  (SELECT employeeid,starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,editedby,dateedit,leclab,flexible,`hours`,breaktime,`mode`,`course`,`section`,`subject`,`aimsdept`,`weekly_sched`
                                                  FROM employee_schedule WHERE employeeid='$employeeid') ");
	}

	public function saveEmployeeSalary($salary_type, $tax_status, $employeeid, $mon_basic, $mon_lec, $mon_lab, $daily_basic, $daliy_lec, $daliy_lab, $hr_basic, $hr_lec, $hr_lab, $min_basic, $min_lec, $min_lab){
		    $this->db->query("update employee set income_base='{$salary_type}',tax_status='{$tax_status}' where employeeid='$employeeid'");
            $this->db->query("replace into employee_salary (employeeid,rate_type,salary_type,amount) 
								values('$employeeid','MONTHLY','BASIC RATE','{$mon_basic}'),
										('$employeeid','MONTHLY','LECTURE','{$mon_lec}'),
										('$employeeid','MONTHLY','LAB','{$mon_lab}'),
										('$employeeid','DAILY','BASIC RATE','{$daily_basic}'),
										('$employeeid','DAILY','LECTURE','{$daliy_lec}'),
										('$employeeid','DAILY','LAB','{$daliy_lab}'),
										('$employeeid','HOURLY','BASIC RATE','{$hr_basic}'),
										('$employeeid','HOURLY','LECTURE','{$hr_lec}'),
										('$employeeid','HOURLY','LAB','{$hr_lab}'),
										('$employeeid','MINUTELY','BASIC RATE','{$min_basic}'),
										('$employeeid','MINUTELY','LECTURE','{$min_lec}'),
										('$employeeid','MINUTELY','LAB','{$min_lab}');");
	}
}