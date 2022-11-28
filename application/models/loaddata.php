<?php 
/**
 * @author Justin
 * @copyright 2015
 * 
 * This file will hold all functions to retrieve data on database.
 *  
 */
 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loaddata extends CI_Model {
    
    function loadeprofileconfig(){
        $query = $this->db->query("SELECT IF(a.employeeid='','All Employee',a.employeeid) AS employeeid, datefrom, dateto FROM employee_restriction a
                                     INNER JOIN employee b ON a.employeeid = b.employeeid 
                                     WHERE CURRENT_DATE <= datefrom AND dateto");
        return $query;
    }
    
}

?>