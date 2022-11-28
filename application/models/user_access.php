<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_access extends CI_Model {

    function loaduseraccess($data){
        $q = $this->db->query("SELECT * FROM user_access where userid='".$this->session->userdata('userid')."'");
        $faccess_detailed = array();
        $faccess = "";
        if($q->num_rows()){
          $row = $q->row(0);
          $ftmp = explode(",",$row->access);
          $faccess_detailed = $ftmp;
          $faccess = $row->access;
          #foreach($ftmp as $dt){
          #  list($id,$r,$w) = explode(":",$dt);
          #  if($r==1 || $w==1){
          #     $faccess .= empty($faccess) ? "" : ",";
          #     $faccess .= $id;   
          #  } 
          #}
        }
        $data['accessdetailed'] = $faccess_detailed;
        $data['useraccess'] = $faccess;
    }
    function loaduseraccessbyuserid($userid){
        $return = array();
        $q = $this->db->query("SELECT * FROM user_access where userid='{$userid}'");
        if($q->num_rows()){
          $row = $q->row(0);
          if($row->access) $return = explode(",",$row->access);  
        }
        return $return;
    }
}

/* End of file user_access.php */
/* Location: ./application/models/user_access.php */