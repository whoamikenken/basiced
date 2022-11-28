<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menus extends CI_Model {
  function loadmenus($root="",$userid="",$utype="",$foremplist=''){
     $return = array();
     $wC = $emod = " AND emod = 0";
     if($foremplist) $emod = "";
    
     if($utype){
         if(!in_array($utype,array("PAYROLL","ADMIN"))){
            if($userid)   $wC = " AND (!FIND_IN_SET(a.menu_id,'50') OR FIND_IN_SET(a.root,'50'))";
         }else{
            #$wC = " AND (FIND_IN_SET(a.menu_id,'2,3,50') OR FIND_IN_SET(a.root,'50'))";
            $wC = "";
         }
     }
     if($utype == "SUPER ADMIN") $wC = "";
      
     if($userid) $q = $this->db->query("select a.menu_id,a.root,a.link,a.title,a.status,a.arranged,a.icon,a.comments,a.description 
                                        from menus a 
                                        inner join user_access b on b.menu_id=a.menu_id
                                        WHERE".($root ? " a.root='{$root}'" : " ifnull(a.root,'')=''")." and a.status='SHOW' and b.userid='{$userid}' and IFNULL(b.read,'')='YES' $emod $wC order by a.arranged");

     else $q = $this->db->query("select menu_id,root,link,title,status,arranged,icon,comments,description from menus WHERE".($root ? " root='{$root}'" : " ifnull(root,'')=''")." and status='SHOW' $emod order by arranged");

     for($t=0;$t<$q->num_rows();$t++){
         $row = $q->row($t);
         array_push($return,array($row->menu_id,$row->root,$row->link,$row->title,$row->status,$row->arranged,$row->icon,$row->comments,$row->description));
     }
     return $return;
  }
  function loadempmenus($root="",$userid="",$utype=""){
     $return = array();
     $wC = "";
     if($root)  $wC = " AND a.root=$root";
     else       $wC = " AND (a.root IS NULL OR a.root = '')";
	 
	 if($userid)  $wC .= " AND b.userid = $userid";
	 
     // $q = $this->db->query("select menu_id,root,link,title,status,arranged,icon,comments from menus WHERE status='SHOW' $wC AND emod = 1 order by arranged");  
     $q = $this->db->query("SELECT a.menu_id,a.root,a.link,a.title,a.status,a.arranged,a.icon,a.comments
	 FROM menus a
	 LEFT JOIN user_access b ON a.`menu_id` = b.`menu_id`
	 WHERE a.status='SHOW' AND a.emod=1 $wC AND b.`read` = 'YES' ORDER BY a.arranged");  
     if($q->num_rows() != 0)
	 {
		for($t=0;$t<$q->num_rows();$t++){
			$row = $q->row($t);
			array_push($return,array($row->menu_id,$row->root,$row->link,$row->title,$row->status,$row->arranged,$row->icon,$row->comments));
		}
	 }
	 else
	 {
		if($root)  $wC = " AND root=$root";
		else       $wC = " AND (root IS NULL OR root = '')";
		 
		 $q2 = $this->db->query("SELECT menu_id,root,link,title,status,arranged,icon,comments
		 FROM menus
		 WHERE status='SHOW' AND emod=1 $wC ORDER BY arranged"); 
		 
		 for($t=0;$t<$q2->num_rows();$t++){
			$row = $q2->row($t);
			array_push($return,array($row->menu_id,$row->root,$row->link,$row->title,$row->status,$row->arranged,$row->icon,$row->comments));
		}
	 }
     return $return;
  }
  function login_trail($userid=''){
    if(!empty($userid))
        $this->db->query("INSERT INTO login_trail (userid) VALUES ('$userid');");
  }

  function validateWriteAccess($userid, $menuid){
    $isExist = $this->db->query("SELECT * FROM user_access WHERE userid = '$userid'")->num_rows();
    if($isExist > 0) return $this->db->query("SELECT * FROM user_access a WHERE a.userid = '$userid' AND a.write = 'YES' AND menu_id = '$menuid' ")->num_rows();
    else return 1;
    
  }

}

/* End of file menus.php */
/* Location: ./application/models/menus.php */