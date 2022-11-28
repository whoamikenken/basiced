<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Studentt extends CI_Model {

	public function getStudent(){
		$list = $this->db->query("SELECT studentid, CONCAT (lname, ',', fname,' ',lname) AS fullname FROM student ORDER BY studentid ASC")->result();
		return $list;
	}

	public function getDataForPrinting(){
		$list = $this->db->query("SELECT NAME, title, content FROM elfinder_file WHERE parent_id = '5' ORDER BY NAME ASC")->result();
		return $list;
	}

	public function getPreviewData($id){
		$list = $this->db->query("SELECT name, title, content FROM elfinder_file WHERE title = '$id'")->result();
		return $list;
	}
	public function getDataStd($id){
		$list = $this->db->query("SELECT a.`studentid`, a.`sy`, a.`lname`, a.`fname`, a.`mname`, a.`yearlevel`, a.`section`, b.`content` FROM elfinder_file b INNER JOIN student a ON a.`studentid` = b.`title` WHERE b.`title` = '$id'")->result();
		return $list;
	}

	public function getDataEmp($id){
		$list = $this->db->query("SELECT a.`employeeid`, a.`campusid`, a.`lname`, a.`fname`, a.`mname`, a.`dateemployed`, c.`description`, b.`content` FROM elfinder_file b INNER JOIN employee a INNER JOIN code_position c ON a.`employeeid` = b.`title` AND a.`positionid` = c.`positionid` WHERE b.`title` = '$id'")->result();
		return $list;
	}

	public function saveStudentImage($image,$name,$id){
		if($this->db->query("SELECT * FROM elfinder_file WHERE title = '$id'")->num_rows() == 0){
			$list = $this->db->query("INSERT INTO `elfinder_file`(`name`,`title`,`content`,`parent_id`,`mime`) VALUES ('$name','$id','$image','5','image/jpeg')");
		}else{
			$list = $this->db->query("UPDATE `elfinder_file` SET `content` = '$image' WHERE `title` = '$id';");
		}
		return $list;
	}
} 