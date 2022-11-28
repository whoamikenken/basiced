<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expiration extends CI_Model {

    public function prcExpiryData(){
    	$notifStart = date('Y-m-d', strtotime(date('Y-m-d'). ' + 14 days'));
    	$datenow = date("Y-m-d");
        return $this->db->query("SELECT * FROM employee where prc_expiration <= '$notifStart' AND prc_expiration <> '' AND prc <> '' AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')");
    }

}
