<?php 
/**
 * @author Max Consul
 * @copyright 2019
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Machine extends CI_Model {

	/**
	* Query for machine db data
	*
	* @return query result
	*/

	public function getTerminalList($id = ""){
		$where_clause = "";
		if($id) $where_clause .= " WHERE id = '$id' ";
		return $this->db->query("SELECT * FROM terminal $where_clause ")->result_array();
	}

	public function getGateHistoryList(){
		return $this->db->query("SELECT * FROM user_gate_history a INNER JOIN terminal b ON b.username = a.username ")->result_array();
	}

	public function insertMachineAccount($insert_data){
		return $this->db->insert("terminal", $insert_data);
	}

	public function updateMachineAccount($update_data, $where_clause){
		$this->db->where($where_clause);
		return $this->db->update("terminal", $update_data);
	}

	public function deleteTerminal($where_clause){
		$this->db->where($where_clause);
		return $this->db->delete("terminal");
	}

	public function getActiveEmployee(){
		return $this->db->query("SELECT CONCAT(lname, ' ,', fname , ' ,', mname) AS fullname, employeecode, employeeid FROM employee WHERE isactive = 1 AND employeecode != '' ")->result_array();
	}

	public function get_terminal($id = ""){
		$whereid = "";
		if($id) $whereid .=" WHERE a.id = '$id'";
        $query = $this->db->query("SELECT a.id, c.id AS online_id, a.username , terminal_name , campus , building , floor , password , rt_password, a.`template`  FROM terminal a  LEFT JOIN user_gate_history c ON a.username = c.username $whereid  GROUP BY a.id ")->result();
        return $query;
    }

	public function isUsernameExist($username){
		return $this->db->query("SELECT * FROM terminal WHERE username = '$username' ")->num_rows();
	}

}