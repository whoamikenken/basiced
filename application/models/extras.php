<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extras extends CI_Model {

    public function getclientipaddress(){
        $ipaddress = '';
        if(getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');#$_SERVER[''];
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');#$_SERVER[''];
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');#$_SERVER[''];
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');#$_SERVER[''];
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');#$_SERVER[''];
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');#$_SERVER[''];
        else $ipaddress = 'UNKNOWN';
        $remoteIp = $ipaddress;
        return $remoteIp;
    }

    function returnmacaddress($remoteIp="") {
        if($remoteIp==""){
           $remoteIp = $this->getclientipaddress();
        }
        // This code is under the GNU Public Licence
        // Written by michael_stankiewicz {don't spam} at yahoo {no spam} dot com
        // Tested only on linux, please report bugs
        
        // WARNinG: the commands 'which' and 'arp' should be executable
        // by the apache user; on most linux boxes the default configuration
        // should work fine
        
        // get the arp executable path
        
        $location = `which arp`;
        $location = rtrim($location);
        // Execute the arp command and store the output in $arpTable
        $arpTable = `$location -n`;
        # echo $arpTable;
        // Split the output so every line is an entry of the $arpSplitted array
        $arpSplitted = explode("\n", $arpTable);
        //echo $arpSplitted[6];
        // get the remote ip address (the ip address of the client, the browser)
        # $remoteIp = str_replace(".", "\\.", $remoteIp);
        //echo $remoteIp;
        // Cicle the array to find the match with the remote ip address
        foreach ($arpSplitted as $value) {
        // Split every arp line, this is done in case the format of the arp
        // command output is a bit different than expected
        $valueSplitted = explode(" ",$value);
        # echo $valueSplitted[0];
        $ipFound = false;
        foreach ($valueSplitted as $spLine) {
            # echo "/$remoteIp/ : $spLine";
            if (preg_match("/$remoteIp/",$spLine)) {
             $ipFound = true;
            }
            // The ip address has been found, now rescan all the string
            // to get the mac address
            if ($ipFound) {
            // Rescan all the string, in case the mac address, in the string
            // returned by arp, comes before the ip address
            // (you know, Murphy's laws)
            reset($valueSplitted);
            foreach ($valueSplitted as $spLine) {
                if (preg_match("/[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f]/i",$spLine)) {
                    return $spLine;
                    }
                    }
            }
            $ipFound = false;
            }
        }
        return false;
    }

    function getBankDesc($empid = ''){
        $query = $this->db->query("SELECT emp_bank from employee where employeeid = '$empid'")->result();
        return $query;
    }

    function showMonth($months=""){
        $result = '';
        $month = array('' => "Month",'01' => "January",'02' => "February",'03' => "March",'04' => "April",'05' => "May",'06' => "June",'07' => "July",'08' => "August",'09' => "September",'10' => "October",'11' => "November",'12' => "December" );
        foreach ($month as $key => $value) {
            if ($months == $key) {
                $sel = "selected";
            }
            else
            {
                $sel = "";
            }
            $result .= "<option value='$key' $sel>".$value."</option>";
        }

        return $result;
    }  
    
    function school_name(){
        return "Pinnacle Technologies Inc.";

    }

    function school_desc(){
        return "D`Great";
    }
    function enum_select( $table , $field ){
        $query = "SHOW COLUMNS FROM `$table` LIKE '$field'";
        $res = $this->db->query($query);
        #extract the values
        #the values are enclosed in single quotes
        #and separated by commas
        $regex = "/'(.*?)'/";
        preg_match_all( $regex , $res->row(0)->Type, $enum_array );
        $enum_fields = $enum_array[1];
        $return = array();
        foreach($enum_fields as $enumvalue){
          $return[$enumvalue] = $enumvalue;  
        }
        return $return;
    }
    
    /** This function will connect to AIMS */
    function showSchoolYear(){
        $returns = array(""=>"");
        $this->db->select("SY");
        $this->db->order_by("DDC.SYFORMAT(DDC.tblFacultyLoad.SY)","DESC");
        $q = $this->db->get("DDC.tblFacultyLoad"); 
        
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->SY] = $row->SY;
        }
        return $returns;
    }
    # by Naces 12-18-17
    function showStudentYL($selected,$dept){


         $caption = "-All Year Level-";

         
    if($dept == ''){
            $return = "<option value=''>{$caption}</option>";
            $sql = $this->db->query("SELECT DISTINCT YearLevel FROM Poveda.tblStudClassList ");
                foreach ($sql->result() as $value) {
                    $return .= "<option value='$value->YearLevel'>$value->YearLevel</option>";
                }

    }else{
        $return = "<option value=''>{$caption}</option>";
         if($selected == "" || $selected == null){
                $query = $this->db->query("SELECT a.`Years` FROM Poveda.tblCourses a WHERE a.`HSOrCollege` = '$dept' ");
                $years = $query->row(0)->Years;
                for($i=1; $i <= $years; $i++){
                    $return .= "<option value='$i'>$i</option>";    
                }
            }else{

                $query = $this->db->query("SELECT a.`Years` FROM Poveda.tblCourses a WHERE a.`HSOrCollege` = '$dept' ");
                $years = $query->row(0)->Years;
                for($i=1; $i <= $years; $i++){
                    ($selected == $i) ? $selecthis = "selected": $selecthis = "";
                    $return .= "<option value='$i' $selecthis>$i</option>";    
                }

            }
        
    }   
    
     return Globals::_e($return);

    }
    function showStudentSection($selected='',$dept='',$sy='',$sem='',$yl=''){

        $caption = "All Section";
        
        
        
        if($dept == ''){
            $return = "<option value=''>{$caption}</option>";
                    $sql = $this->db->query("SELECT DISTINCT SectCode FROM Poveda.tblStudClassList");
                    foreach ($sql->result() as $value) {
                        $return .= "<option value='$value->SectCode'>$value->SectCode</option>";
                    }

        }else{
            
            if($selected == "" || $selected == null){
                    
            $return = "<option value=''>{$caption}</option>";
                    $sql = $this->db->query("SELECT a.section FROM student AS a 
                                    WHERE a.SY = '$sy' 
                                      AND a.Sem = '$sem' 
                                      AND a.yearlevel = '$yl' 
                                      AND a.depttype = '$dept' 
                                    GROUP BY a.section ");
                    
                    foreach ($sql->result() as $value) {
                        $return .= "<option value='$value->section'>".$value->section."</option>";
                    }
                }else{
                    $return = "<option value=''>{$caption}</option>";
                    $sql = $this->db->query("SELECT a.section FROM student AS a 
                                WHERE a.SY = '$sy' 
                                  AND a.Sem = '$sem' 
                                  AND a.yearlevel = '$yl' 
                                  AND a.depttype = '$dept' 
                                GROUP BY a.section ");
                    foreach ($sql->result() as $value) {
                        ($selected == $value->section) ? $selecthis = "selected": $selecthis = "";
                        $return .= "<option value='$value->section' $selecthis>".$value->section."</option>";
                    }

                }

        }
        return Globals::_e($return);

    }
   
    
    function showStudentDepartmentType($depts,$selected) {

    $caption = "-All Department-";

    if($depts == ''){
    $return = "<option value=''>{$caption}</option>";

    if($selected == "" || $selected == null){

                $sql = $this->db->query("SELECT * FROM Poveda._depttypes ORDER BY description");
                foreach ($sql->result() as $key) {
                $return .= "<option value='$key->code'>$key->description</option>";
            }

    }else{
            
            $sql = $this->db->query("SELECT * FROM Poveda._depttypes ORDER BY description");
            foreach ($sql->result() as $key) {
                ($selected == $key->code) ? $selecthis = "selected": $selecthis = "";
                $return .= "<option value='$key->code' $selecthis>$key->description</option>";
            }
    }

    
    return Globals::_e($return);
    }else{
        $deptArray = explode(",", $depts);
        $count = count($deptArray);

        $descArray = "";
        for($i=0; $i < $count; $i++){
        $sql = $this->db->query("SELECT description FROM Poveda._depttypes WHERE code = '$deptArray[$i]' ORDER BY description");
        if ($sql->num_rows() > 0) {
            $descArray .= Globals::_e($sql->row(0)->description)."<br>";
        }
        }
        

        return $descArray;
    }
}
function showStudentSY() {
    $caption = "-All School Year-";

    $return = "<option value=''>{$caption}</option>";
    $sql = $this->db->query("(SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'' ORDER BY SY DESC)
                                    UNION ALL
                                   (SELECT DISTINCT SY FROM Poveda.tblConfig WHERE SY<>'' AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'' ORDER BY SY DESC) ORDER BY SY DESC)
                                    UNION ALL
                                   (SELECT DISTINCT SY FROM Poveda.tblSchedule WHERE SY<>''
                                                                        AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'')
                                                                        AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblConfig WHERE SY<>'' AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'')))
                                    ORDER BY SY DESC;");
    foreach ($sql->result() as $value) {
        $return .= "<option value='$value->SY'>$value->SY</option>";
    }
    return $return;
}

    function saveStudentSchedule($data){
        $sy = $data['sy'];
        $dept = $data['dept'];
        $yl = $data['yl'];
        $sect = $data['sect'];
        $aDate = $data['aDate'];

        $deptArray = implode(',', $dept);

        $timeStart = $data['timeStart'];
        $timeStart = date("Y-m-d H:i:s",strtotime($timeStart));

        $timeEnd = $data['timeEnd'];
        $timeEnd = date("Y-m-d H:i:s",strtotime($timeEnd));


        $tardyStart = $data['tardyStart'];
        $tardyStart = date("Y-m-d H:i:s",strtotime($tardyStart));

        $halfdayStart = $data['halfdayStart'];
        $halfdayStart = date("Y-m-d H:i:s",strtotime($halfdayStart));

        $absentStart = $data['absentStart'];
        $absentStart = date("Y-m-d H:i:s",strtotime($absentStart));

        $sql = $this->db->query("INSERT INTO student_schedule_batch (sy,department,yl,section,timeStart,timeEnd,tardyStart,halfdayStart,absentStart,applicableDate) VALUES('$sy','$deptArray','$yl','$sect','$timeStart','$timeEnd','$tardyStart','$halfdayStart','$absentStart','$aDate')");
        ($sql === true) ? $check = 'Successfully Saved' : $check = "Somethings Wrong...";
        echo $check;
        return;
    }

    #End by naces 12-18-17


     function showSemester(){
        $return = array(""=>"","A"=>"First Semester","B"=>"Second Semester","C"=>"Summer");
        return $return;
    }
    function showYearLevel($section=""){
        $return = array(""=>"All year level"); 
        #$q1 = "select distinct trim(yearlevel) as yearlevel from student where ifnull(trim(yearlevel),'')<>'' ".($section?" and section='{$section}'":"")."  ORDER BY trim(yearlevel)";
        $q1 = "select distinct trim(YearLevel) as yearlevel from StJude.tblPersonalData where ifnull(trim(YearLevel),'')<>'' ".($section?" and SectCode='{$section}'":"")."  ORDER BY trim(YearLevel)";
        $q = $this->db->query($q1)->result();
        $return = array(""=>"All year level");
        foreach($q as $oo){
          $return[$oo->yearlevel] = $oo->yearlevel;    
        }
        return $return;
    }
    function showSection($yearlevel="", $dept=""){
        $return = array(""=>"All section");
        #$que = "select distinct trim(section) as section from student where ifnull(trim(section),'')<>''".($yearlevel?" and (yearlevel='{$yearlevel}'":"")." ".($dept?" and coursecode='{$dept}' )":"")." ORDER BY trim(section)";
        $que = "select distinct trim(SectCode) as section from StJude.tblPersonalData where ifnull(trim(SectCode),'')<>''".($yearlevel?" and (YearLevel='{$yearlevel}'":"")." ".($dept?" and CourseCode='{$dept}' )":"")." ORDER BY trim(SectCode)";
        $q = $this->db->query($que)->result();
        foreach($q as $oo){
          $return[$oo->section] = $oo->section;
        }
        return $return;
    }
    
    function showLecLab(){
        $return = array("LEC"=>"LEC","LAB"=>"LAB");
        return $return;
    }
    function showMachineType(){
        $return = array("IN-OUT"=>"IN-OUT","IN"=>"IN","OUT"=>"OUT");
        return $return;
    }
    function showAllStatus(){
        $return = array("ACTIVE"=>"ACTIVE","INACTIVE"=>"INACTIVE");
        return $return;
    }
    function showLeaveStatus(){
        $return = array("PENDING"=>"PENDING","APPROVED"=>"APPROVED","DISAPPROVED"=>"DISAPPROVED");
        return $return;
    }
    function showCategory(){
        $return = array(""=>"- All Category - ", "PENDING"=>"PENDING", "APPROVED"=>"APPROVED", "DISAPPROVED"=>"DISAPPROVED");
        return $return;
    }
    function showCategoryopt($id = ""){
        $opt = "";
        $return = array(""=>"- All Category - ", "PENDING"=>"PENDING", "APPROVED"=>"APPROVED", "DISAPPROVED"=>"DISAPPROVED");
        foreach($return as $key=>$val){
            if($key == $id) $sel = " selected";
            else            $sel = "";
            $opt .= "<option value='$key' $sel>$val</option>";
        }
        return $opt;
    }
    function showLeavelist(){
        $return = array("VL"=>"Vacation Leave","SL"=>"Sick Leave","EL"=>"Emergency Leave","other"=>"Others");
        return $return;
    }
    function showUpdatedLeavelist(){
        $return = $this->db->query("SELECT * FROM code_request_form WHERE ismain = '1'")->result_array();
        return $return;
    }
    function showcstat(){
        $return = array(""=>"- All Status -", "PENDING"=>"PENDING", "DONE"=>"DONE");
        return $return;
    }
    function showLoanConfig()
    {
        $return = "";
        $query = $this->db->query("SELECT id,description FROM payroll_loan_config ORDER by ID");
        foreach ($query->result() as $key) {
            $return .= "<option value='$key->id'>".$key->description."</option>";
        }
        return $return;
    }

     function showDeductionsConfig()
    {
        $return = "";
        $query = $this->db->query("SELECT id,description FROM payroll_deduction_config ORDER by ID");
        foreach ($query->result() as $key) {
            $return .= "<option value='$key->id'>".$key->description."</option>";
        }
        return $return;
    }

    function showDeductionLoanConfig()
    {
        $return = "";
        $query = $this->db->query(" SELECT id,description FROM payroll_loan_config UNION ALL SELECT id,description FROM payroll_deduction_config ORDER BY ID");
        foreach ($query->result() as $key) {
            $return .= "<option value='$key->description'>".$key->description."</option>";
        }
        return $return;
    }
    function showLeaveType($ltype = "",$eid = ""){
        $wC     = "";
        $return = "<option value=''>- Leave Type -</option>";
        $qemp   = $this->db->query("SELECT leavetype FROM employee WHERE employeeid='$eid'");
        $eltype = $qemp->row(0)->leavetype;
        if($eltype ) $wC     = " AND leavetype='$eltype'";  
        $query  = $this->db->query("SELECT code_request,description,leavetype FROM code_request_form WHERE leavetype <> '' $wC")->result();  
        foreach($query as $val){
            $code = $val->code_request;
            $desc = $val->description;
            $type = $val->leavetype;
            if($ltype == $code)
                $return .= "<option value='$code' selected>$desc (".$type.")</option>";
            else
                $return .= "<option value='$code'>$desc (".$type.")</option>";
        }
        return $return;
    }

    function getemployeemlevel($emptype=""){
        $returns = "";
        $q = $this->db->query("select description from code_managementlevel WHERE managementid='$emptype'")->result();
        foreach($q as $row){
            $returns = $row->description;
        }
        
        return $returns;
    }
    /*

    */
    /*  
    * title   : new function added
    * author  : justin (with e)
    *
    */
    function saveDependent($data){
        $msg = '';
        if($data['job'] == "saveNewDependent")
            $query = $this->db->query("INSERT INTO code_tax_status VALUES('{$data['dep_code']}','{$data['stat_name']}','{$data['tax_exc']}')");
        else
            $query = $this->db->query("UPDATE code_tax_status SET status_desc='{$data['stat_name']}', status_exemption='{$data['tax_exc']}' WHERE status_code='{$data['dep_code']}'");

        // return after saving
        $msg = "Successfully Saved!.";
        return $msg;
    }

    function deleteDependent($dep_code){
        $msg ='';
        $query = $this->db->query("DELETE FROM code_tax_status WHERE status_code='{$dep_code}'");
        $msg = "Successfully Deleted!.";
        return $msg;
    }
     /**get all campuses**/

    function getCampuses($campusid = "") {
        $return = "<option value=''> All Campus </option>";
        $query = $this->db->query("SELECT code, description FROM code_campus ")->result();
        foreach ($query as $key) {
            $key->code = Globals::_e($key->code);
            $key->description = Globals::_e($key->description);
            if($campusid == $key->code) $return .= "<option value='$key->code' selected>$key->description</option>";
            else $return .= "<option value='$key->code'>$key->description</option>";
        }

        return $return;
    }

    /*
    * end of new function added
    */
    function showManagement(){
        $return = array(""=>"Choose a Division ...");
        $q = $this->db->query("select managementid,description from code_managementlevel order by description")->result();
        foreach($q as $oo){
          $return[$oo->managementid] = $oo->description;    
        }
        return $return;
    }

    function getEmploymentCodeStatus(){
        $q = $this->db->query("SELECT * FROM code_status");
        if($q->num_rows() > 0) return $q->result();
        else return "";
    }
	
	//Added 5-26-17
	function getManagementLevelDescription($mLevel=""){
        $return = "";
		$wC = "";
        if($mLevel) $wC .= " WHERE managementid='$mLevel'";
        $result = $this->db->query("SELECT managementid, description FROM code_managementlevel $wC")->result();
		foreach($result as $row)
		{
			$return = $row->description;
		}
        return $return;
    }
	
    function showPostion($positionid=""){
        $return = array();
        $wC = "";
        if($positionid) $wC = " WHERE positionid='$positionid'";
        else $return = array(""=>"Choose a position ...");
        $q = $this->db->query("SELECT positionid,description FROM code_position $wC order by description")->result();
        foreach($q as $oo){
          $return[Globals::_e($oo->positionid)] = Globals::_e($oo->description);    
        }
        return $return;
    }
    function showPosDesc($pos){
        $return = "";
        $q = $this->db->query("SELECT description FROM code_position WHERE positionid='{$pos}'")->result();
        foreach($q as $val){
            $return = Globals::_e($val->description);
        }
        return $return;
    }
    function showCitizenship(){
        $return = array(""=>"Choose a citizenship ...");
        $q = $this->db->query("SELECT citizenid,description FROM code_citizenship ORDER BY description;")->result();
        foreach($q as $oo){
          $return[Globals::_e($oo->citizenid)] = Globals::_e($oo->description);    
        }
        return $return;
    }
    function showReligion(){
        $return = array(""=>"Choose a religion ...");
        $q = $this->db->query("SELECT religionid,description FROM code_religion ORDER BY description")->result();
        foreach($q as $oo){
          $return[Globals::_e($oo->religionid)] = Globals::_e($oo->description);    
        }
        return $return;
    }
    function showNationality(){
        $return = array(""=>"Choose a nationality ...");
        $q = $this->db->query("SELECT nationalityid,description FROM code_nationality ORDER BY description")->result();
        foreach($q as $oo){
          $return[Globals::_e($oo->nationalityid)] = Globals::_e($oo->description);    
        }
        return $return;
    }
    function regionlist($regid=""){
        $return = array(""=>"Choose a region ...");
        $q = $this->db->query("SELECT regDesc,regCode FROM refregion ORDER BY regCode")->result();
        foreach($q as $oo){
          $return[Globals::_e($oo->regCode)] = Globals::_e($oo->regDesc);    
        }
        return $return;
    }
	
	function regiondesc($regid=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM refregion where regCode='$regid'");
        foreach($query->result() as $row){
            $return = $row->regDesc;
        }
        return $return;
    } 
	
    /*function regionlist($regid=""){
        $q = $this->db->query("SELECT region_name,region_code FROM regions ORDER BY region_code")->result();
        echo '<select class="chosen col-md-10" name="region">';
        
        echo "<option value=''>Choose a region ...</option>";
        foreach($q as $oo){
          $return[$oo->region_code] = $oo->region_name;    
          echo "<option value='{$oo->region_code'}">'{$oo->region_name}'</option>";
        }
        echo "</select>";
    }*/
    /*function provincelist($provid="",$regCode=""){
        $return = array(""=>"Choose a province ...");
        $q = $this->db->query("SELECT cpName,cpID FROM city_provinces where RegionCode = '$regCode' ORDER BY cpName")->result();
        foreach($q as $oo){
          $return[$oo->cpID] = $oo->cpName;    
        }
        return $return;
    }*/
    function provincelist($data){
        $provid=$data['provid'];
        $regCode=$data['regCode'];
        $q = $this->db->query("SELECT provDesc,provCode FROM refprovince where regCode = '$regCode' ORDER BY provDesc")->result();
            
        echo "<option value=''>Choose a province ...</option>";
        foreach($q as $oo){
            $val = Globals::_e($oo->provCode);
            $disp = Globals::_e($oo->provDesc);
          echo "<option value='$val' ". ($provid == $val? "selected":"")  .">$disp</option>";
        }
    }

	function provincedesc($prov=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM refprovince where provCode='$prov'");
        foreach($query->result() as $row){
            $return = Globals::_e($row->provDesc);
        }
        return $return;
    } 
	
    function municipalitylist($data){
        $munid=trim($data['munid']);
        $ProvID=$data['ProvID'];
        // $q = $this->db->query("SELECT REPLACE(REPLACE(DistMunName,'Ã‘','&#241;'),'ñ','&#241;') as DistMunName,dmunID FROM district_municipalities Where ProvID = '$ProvID'  ORDER BY DistMunName")->result();
        $q = $this->db->query("SELECT citymunDesc,citymunCode FROM refcitymun Where provCode = '$ProvID'  ORDER BY citymunDesc")->result();
        echo "<option value=''>Choose a municipality ... </option>";
        foreach($q as $oo){
            $val = $oo->citymunCode;
            $val = trim($val);
            $disp = html_entity_decode(htmlentities($oo->citymunDesc));
          echo "<option value='$val' ". ($munid == $val? "selected":"") .">$disp</option>";
        }
    }

    function barangaylist($data){
        $brgyid=trim($data['brgyid']);
        $munid=$data['munid'];
        // $q = $this->db->query("SELECT REPLACE(REPLACE(DistMunName,'Ã‘','&#241;'),'ñ','&#241;') as DistMunName,dmunID FROM district_municipalities Where ProvID = '$ProvID'  ORDER BY DistMunName")->result();
        $q = $this->db->query("SELECT brgyDesc,brgyCode FROM refbrgy Where citymunCode = $munid ORDER BY brgyDesc")->result();
        echo "<option value=''>Choose a barangay ...</option>";
        foreach($q as $oo){
            $val = $oo->brgyCode;
            $val = trim($val);
            $disp = html_entity_decode(htmlentities($oo->brgyDesc));
          echo "<option value='$val' ". ($brgyid == $val? "selected":"")  .">$disp</option>";
        }
    }
	
	function municipalitydesc($municipality=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM refcitymun where citymunCode='$municipality'");
        foreach($query->result() as $row){
            $return = Globals::_e($row->citymunDesc);
        }
        return $return;
    } 

    function barangaydesc($barangay=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM refbrgy where brgyCode='$barangay'");
        foreach($query->result() as $row){
            $return = $row->brgyDesc;
        }
        return $return;
    }

    function changeaddr($data){
        $employeeid = $data['employeeid'];
        if($data['changeaddr'] == 'regionaladdr'){
            $update = $this->db->query("UPDATE employee set provaddr = '', cityaddr = '', barangay='', zip_code = '' where employeeid ='$employeeid'");
        }else if($data['changeaddr'] == 'provaddr'){
            $update = $this->db->query("UPDATE employee set cityaddr = '', barangay='', zip_code = '' where employeeid ='$employeeid'");
        }else if($data['changeaddr'] == 'cityaddr'){
            $update = $this->db->query("UPDATE employee set barangay='', zip_code = '' where employeeid ='$employeeid'");
        }else if($data['changeaddr'] == 'permaRegion'){
            $update = $this->db->query("UPDATE employee set permaProvince = '', permaMunicipality = '', permaBarangay='', permaZipcode = '' where employeeid ='$employeeid'");
        }
        else if($data['changeaddr'] == 'permaProvince'){
            $update = $this->db->query("UPDATE employee set permaMunicipality = '', permaBarangay='', permaZipcode = '' where employeeid ='$employeeid'");
        }
        else if($data['changeaddr'] == 'permaMunicipality'){
            $update = $this->db->query("UPDATE employee set permaBarangay='', permaZipcode = '' where employeeid ='$employeeid'");
        }
    } 

    function changeaddrApplicant($data){
        $employeeid = $data['applicantId'];
        if($data['changeaddr'] == 'regionaladdr'){
            $update = $this->db->query("UPDATE applicant_info set provaddr = '', cityaddr = '', barangay='', zip_code = '' where baseId ='$employeeid'");
        }else if($data['changeaddr'] == 'provaddr'){
            $update = $this->db->query("UPDATE applicant_info set cityaddr = '', barangay='', zip_code = '' where baseId ='$employeeid'");
        }else if($data['changeaddr'] == 'cityaddr'){
            $update = $this->db->query("UPDATE applicant_info set barangay='', zip_code = '' where baseId ='$employeeid'");
        }else if($data['changeaddr'] == 'regionaladdr2'){
            $update = $this->db->query("UPDATE applicant_info set provaddr2 = '', cityaddr2 = '', barangay2='', zip_code2 = '' where baseId ='$employeeid'");
        }
        else if($data['changeaddr'] == 'permaProvince'){
            $update = $this->db->query("UPDATE applicant_info set cityaddr2 = '', barangay2='', zip_code2 = '' where baseId ='$employeeid'");
        }
        else if($data['changeaddr'] == 'permaMunicipality'){
            $update = $this->db->query("UPDATE applicant_info set barangay2='', zip_code2 = '' where baseId ='$employeeid'");
        }
    } 
	
    function showrequestform(){
        $returns = array(""=>"NA");
        $this->db->select("code_request,description");
        $this->db->order_by("is_leave","asc");
        $this->db->order_by("description","asc");
        $q = $this->db->get("code_request_form"); 
        
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->code_request] = $row->description;
        }
        return $returns;
    }
    
    function showStatus(){
        $query = $this->db->query("SELECT * FROM code_status");
        return $query;
    }
    
    function showdepartment($caption=''){
        $returns = array();
        if (isset($caption)) {
            $returns = array(""=>$caption);
        }
        $this->db->select("code,description");
        $this->db->order_by("description","asc");
        $q = $this->db->get("code_department"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[Globals::_e($row->code)] = Globals::_e($row->description);
        }
        return $returns;
    }

    public function getPayrollCutoffSelect($id){
        $result = $this->db->query("SELECT id,startdate,enddate FROM payroll_cutoff_config")->result();
        $return = "<option value=''></option>";
        foreach ($result as $value) {
            if($id === $value->id){
                $return .= "<option selected value='$value->id'>$value->startdate ~ $value->enddate</option>";
            }
            else{
                $return .= "<option value='$value->id'>$value->startdate ~ $value->enddate</option>";
            }
        }
        return $return;
    }

    public function getPayrollCutoffDescription($id){
        $result = $this->db->query("SELECT id,startdate,enddate FROM payroll_cutoff_config WHERE id='$id'")->result();
        $return = "";
        foreach ($result as $value) {
            $return = $value->startdate." ~ ".$value->enddate;
        }
        return $return;
    }

    function showoffice($caption=''){
        $returns = array();
        if (isset($caption)) {
            $returns = array(""=>$caption);
        }
        $this->db->select("code,description");
        $this->db->order_by("description","asc");
        $q = $this->db->get("code_office"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[Globals::_e($row->code)] = Globals::_e($row->description);
        }
        return $returns;
    }

    //SELECT code, description FROM code_office WHERE IFNULL(managementid,'')='' AND description NOT LIKE '%Head%' AND description NOT LIKE '%Director%' AND description != 'Registrar' AND description != ''


    function showofficeheadcount(){
        $returns = array();
        $sql = $this->db->query("SELECT code, description FROM code_office WHERE IFNULL(managementid,'')='' AND description NOT LIKE '%Head%' AND description NOT LIKE '%Director%' AND description != 'Registrar' AND description != ''")->result();
        foreach ($sql as $key => $row) {
             $return[$row->code] = $row->description;
        }
        return $return;
    }
    
    function showcampus($caption = "")
    {
        $return = "";

        $return[""] = $caption; // add by justin (with e) for ica-hyperion 21671
        $query = $this->db->query("SELECT code,description FROM code_campus ORDER by code")->result();
        foreach ($query as $key => $row) {
             $return[$row->code] = $row->description;
        }
       return $return;

    }
    
    function showstatusdescription()
    {
        $return = array();
        $query = $this->db->query("SELECT code,description FROM code_status ORDER BY code ASC")->result();
        foreach ($query as $key => $row) {
            $return[$row->code] = $row->description;
        }
        return $return;

    }

    // function showreportseduclevel($select,$code)
    // {
    //     $return = array();
    //     $query = $this->db->query("SELECT level,ID FROM reports_item Where reportcode='ECT' ORDER BY level")->result();
    //     foreach ($query as $key => $row) {
    //         $return[$row->level] = $row->level;
    //     }
    //     return $return;

    // }

    function showreportseligibilities($select,$code)
    {
        $return = array();
        $query = $this->db->query("SELECT level,ID FROM reports_item Where reportcode='$code' ORDER BY level")->result();
        foreach ($query as $key => $row) {
            $return[$row->level] = $row->level;
        }
        return $return;

    }

    function showreportseduclevelseminar($code)
    {
        $return = array();
        $query = $this->db->query("SELECT level,ID FROM reports_item Where reportcode='$code' ORDER BY level")->result();
        foreach ($query as $key => $row) {
            $return[$row->level] = $row->level;
        }
        return $return;

    }
    
    function showreportseduclevel($caption='',$code){
        $returns = array();
        if (isset($caption)) {
            $returns = array(""=>$caption);
        }
        $this->db->select("level,ID");
        $this->db->order_by("level");
        $this->db->where("reportcode",$code);
        $q = $this->db->get("reports_item"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[Globals::_e($row->level)] = Globals::_e($row->level);
        }
        $returns['others'] = 'OTHERS';
        return $returns;
    }

    function getSchoolName(){
        $return = array();
        $query = $this->db->query("SELECT * FROM code_school")->result();
        foreach ($query as $key => $row) {
            $row->description = str_replace("'S", "'s", $row->description);
            $return[Globals::_e($row->schoolid)] = Globals::_e($row->description);
        }
        return $return;
    }

    function showreportsvenue($caption=''){
        $returns = array();
        if (isset($caption)) {
            $returns = array(""=>$caption);
        }
        $this->db->select("venue,ID");
        $this->db->order_by("venue");
        $q = $this->db->get("reports_item"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->venue] = $row->venue;
        }
        return $returns;
    }

    function showCodeSctt($caption=''){
        $returns = array();
        if (isset($caption)) {
            $returns = array(""=>$caption);
        }
        $this->db->select("id,subj_code,description");
        $this->db->order_by("subj_code");
        $this->db->where("status","1");
        $q = $this->db->get("code_subj_competent_to_teach"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[Globals::_e($row->id)] = Globals::_e($row->subj_code);
        }
        return $returns;
    }

    function getComTeachDesc($id){
        return $this->db->query("SELECT description FROM code_subj_competent_to_teach WHERE id = '$id'")->row()->description;
    }
    
    function showdepartmentholiday($hol=""){
        $returns = array();
        $param = "";
        if($hol)    $param = " AND b.holi_cal_id='$hol'";
        $q = $this->db->query("SELECT a.code,a.description,b.* FROM code_office a LEFT JOIN holiday_inclusions b ON a.code = b.dept_included $param ORDER BY a.description"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->code] = $row->description."|".$row->permanent."|".$row->prob."|".$row->contractual;
        }
        return $returns;
    }

    function listDepartmentsAffectedByHoliday($holiday_id){
        $listAffecteds = array();
        $sql = "SELECT dept_included from holiday_inclusions WHERE holi_cal_id = '".$holiday_id."' ";
        $result = $this->db->query($sql)->result_array();
        foreach ($result as $key => $value) {
            $listAffecteds[$key] = $value["dept_included"];
        }
        return $listAffecteds;
    }

    function showcutofdatebyid($cid,$employeeid=""){
        $returns = array();
        $this->db->select("cdate");
        $this->db->where("id",$cid);
        $this->db->order_by("cdate","asc");
        $q = $this->db->get("cutoff_details"); 
        
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->cdate] = date("d M D",strtotime($row->cdate));
        }
        
        if($employeeid && $cid){
            $qe = $this->db->query("select DISTINCT cdate from employee_schedule_adjustment where cutoffid='{$cid}' and employeeid='{$employeeid}'");
            for($u=0;$u<$qe->num_rows();$u++){
              $row = $qe->row($u);  
              $returns[$row->cdate] = date("d M D",strtotime($row->cdate));  
            }
        }
        array_multisort($returns);
        return $returns;
    }
    function showincomebase(){
        $returns = array();
        $this->db->select("income_base,description");
        $this->db->order_by("description","asc");
        $q = $this->db->get("code_income_base"); 
        
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[Globals::_e($row->income_base)] = Globals::_e($row->description);
        }
        return $returns;
        
    }
    function getincomebase($incomebase){
        $returns = "";
        $this->db->select("income_base,description");
        $this->db->where("income_base",$incomebase);
        $this->db->order_by("description","asc");
        $q = $this->db->get("code_income_base"); 
        
        if($q->num_rows()>0){
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns = $row->description;
        }
        }
        return $returns;
    }
    function showtaxstatus($inp=''){
        $returns = array();
        $this->db->select("status_code,status_desc,status_exemption");
        $this->db->order_by("status_desc","asc");
        $q = $this->db->get("code_tax_status"); 
        
        if($inp==''){
            for($t=0;$t<$q->num_rows();$t++){
              $row = $q->row($t);
              $returns[$row->status_code] = $row->status_desc;
            }
            return $returns;
        }

        return $q;
    }
    function gettaxstatus($taxstatus=""){
        $returns = "";
        $this->db->select("status_code,status_desc");
        $this->db->where("status_code",$taxstatus);
        $this->db->order_by("status_desc","asc");
        $q = $this->db->get("code_tax_status"); 
        if($q->num_rows()>0){
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns .= $row->status_desc;
        }
        }
        return $returns;
    }
    
    function gettaxstatuscode($taxstatus=""){
        $returns = "";
        $q = $this->db->query("SELECT status_code,status_desc FROM code_tax_status WHERE status_code='$taxstatus'")->result();
        foreach($q as $row){
            $returns = $row->status_desc;
        }
        
        return $returns;
    }
    function getemployeetype($emptype=""){
        $returns = "";
        $q = $this->db->query("SELECT code,description FROM code_type WHERE code='$emptype'")->result();
        foreach($q as $row){
            $returns = $row->description;
        }
        
        return $returns;
    }
    function getrelation($emptype=""){
        $returns = "";
        $q = $this->db->query("SELECT relationshipid,description FROM code_relationship WHERE relationshipid='$emptype'")->result();
        foreach($q as $row){
            $returns = $row->description;
        }
        
        return $returns;
    }

    function getemployeestatus($empstatus=""){
        $returns = "";
        $q = $this->db->query("SELECT code,description FROM code_status WHERE code='$empstatus'")->result();
        foreach($q as $row){
            $returns = $row->description;
        }
        return $returns;
    }
    
    function getemployeecol($employeeid="",$col=""){
        $return = "";
        $query = $this->db->query("SELECT $col FROM employee WHERE employeeid='$employeeid'");
        if($query->num_rows() > 0)  $return = $query->row(0)->$col;
        return $return;
    }
    
    function getemployeedepartment($deptid=''){
        $mydept = "";
        $row = $this->db->query("SELECT description FROM code_department WHERE CODE ='{$deptid}' ")->result();
        foreach ($row as $key => $val) {
            $mydept = Globals::_e($val->description);
        }
        if($deptid == "ALL")    $mydept = " ALL Department";
        return $mydept;
    }

    function getHeadDepartment($empid=''){
        return $this->db->query("SELECT deptid from employee where employeeid = '$empid'")->row()->deptid;
    }

    function countDeoartment(){
        return $this->db->query("SELECT code from code_department")->num_rows();
    }

    function getHeadOffice($empid=''){
        return $this->db->query("SELECT office from employee where employeeid = '$empid'")->row()->office;
    }

    function getemployeeoffice($office=''){
        $myoffice = "";
        $row = $this->db->query("SELECT description FROM code_office WHERE CODE ='{$office}' ")->result();
        foreach ($row as $key => $val) {
            $myoffice = $val->description;
        }
        if($office == "ALL")    $mydept = " ALL Office";
        return $myoffice;
    }

    function getemployeeRank($code=''){
        $myoffice = "";
        $row = $this->db->query("SELECT description FROM rank_code_type WHERE id ='{$code}' ")->result();
        foreach ($row as $key => $val) {
            $myoffice = $val->description;
        }
        if($code == "ALL") $myoffice = " ";
        return $myoffice;
    }

    function getemployeeSchedule($code=''){
        $myoffice = "";
        $row = $this->db->query("SELECT description FROM code_schedule WHERE schedid ='{$code}' ")->result();
        foreach ($row as $key => $val) {
            $myoffice = $val->description;
        }
        if($code == "ALL") $myoffice = " ";
        return $myoffice;
    }
    
    function listEmpDept($deptid=''){
        $mydept = "<option value=''>- All Department -</option>";
        $row = $this->db->query("SELECT code,description FROM code_office")->result();
        foreach ($row as $val) {
            $mydept .= "<option value='{$val->code}'>{$val->description}</option>";
        }
        return $mydept;
    }

    function showshiftschedule(){
        $return = array(""=>" --  Select  --");
        
        $this->db->select("schedid,description,schedcode,tardy_start");
        $this->db->order_by("description","");
        $q = $this->db->get("code_schedule"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $return[Globals::_e($row->schedid)] = Globals::_e($row->description);
        }
        return $return;
        
    }
    function showholiday(){
        $return = array(""=>"regular day");
        
        $this->db->select("code,description,holiday_type");
        $this->db->order_by("description","asc");
        $q = $this->db->get("code_holidays"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $return[$row->code] = $row->description . " (".$row->holiday_type.")";
        }
        return $return;
        
    }
    function showincome(){
        $returns = array(""=>"");
        $this->db->select("code_income,description");
        $this->db->order_by("description","asc");
        $q = $this->db->get("incomes"); 
        if($q->num_rows()>0){
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->code_income] = $row->description;
        }
        }
        return $returns;
        
    }
    
    function showdeductions(){
        $returns = array(""=>"");
        $this->db->select("code_deduction,description");
        $this->db->order_by("description","asc");
        $q = $this->db->get("deductions"); 
        
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->code_deduction] = $row->description;
        }
        return $returns;
        
    }
    
    function reformstring($str="",$num=2,$fill="0",$isback=0){
        $tmp = $str;
        while(strlen($tmp)<$num){
           if($isback) $tmp .= $fill;
           else $tmp = $fill.$tmp;  
        }
        return $tmp;
    }
    function showhours($hr=""){
        $return = "<option value=''></option>";
        $u = 1;
        while($u<13){
          $return .= "<option".($hr==$u ? " selected" : "")." value='$u'>".$this->reformstring($u)."</option>";    
          $u++;  
        }
        return $return;
    }
    function showminutes($min="",$detailed = false){
        $return = "<option value=''></option>";
        $u = array(00,15,30,45);
        if($detailed){
            $u = array();
            for($t=0;$t<60;$t++){
               array_push($u,$this->reformstring($t)); 
            }
        }
        
        foreach($u as $mins){
          $return .= "<option".(($min==$mins && $min!="") ? " selected" : "")." value='$mins'>".$this->reformstring($mins)."</option>";      
        }
        return $return;
    }
    function showstat($stats=""){
        $return = "<option value=''></option>";
        $u = array("AM","PM");
        foreach($u as $stat){
          $return .= "<option".($stat==$stats ? " selected" : "")." value='$stat'>$stat</option>";      
        }
        return $return;
    }
    function sreformstring($str="",$ende=0){
        $return = $str;
        $equi = array("~"=>":curl:","#"=>":num:","@"=>":at:","$"=>":dollar:","%"=>":percent:","^"=>":roof:","&"=>":amp:","*"=>":ast:","("=>":opar:",")"=>":cpar:","_"=>":uscore:","+"=>":plus:","-"=>":minus:","/"=>":fslash:","="=>":equal:");
        foreach($equi as $key=>$value){
          $s = $ende==1 ? $key : $value;
          $t = $ende==1 ? $value : $key;
          $return = str_replace($s,$t,$return);  
        }
        return $return;
    }
    function showgender(){
        # return array("MALE"=>"MALE","FEMALE"=>"FEMALE");
        $return = array(""=>"Choose a gender ...");
        $q = $this->db->query("SELECT genderid,description FROM code_gender order by description")->result();
        foreach($q as $oo){
          $return[Globals::_e($oo->genderid)] = Globals::_e($oo->description);    
        }
        return $return;
    }
    
    function showemployeetype(){
        //return array("TEACHING LOAD"=>"TEACHING LOAD","NON-TEACHING LOAD"=>"NON-TEACHING LOAD");
        $return = array(""=>"Select Batch Scheduling");
        $q = $this->db->query("SELECT code,description,schedid FROM code_type  WHERE schedid <> '' order by description ")->result();
        foreach($q as $oo){
          $return[Globals::_e($oo->code)] = Globals::_e($oo->description);    
        }
        return $return;
    }
    //AIMS INTEGRATION
     function showAimsDepartment(){
         $return = array(""=>"Choose a Department ...");
         $q = $this->db->query("SELECT CODE AS codes,description FROM Poveda.`_depttypes` order by codes")->result();
         foreach($q as $oo){
           $return[Globals::_e($oo->codes)] = Globals::_e($oo->description);    
         }
         return $return;   
        // $return = "<option value=''>- Select Department-</option>";
        // $query = $this->db->query("SELECT CODE AS codes,description FROM Poveda.`_depttypes`");
        // foreach($query->result() as $row){
        //     if($row->codes)    $sel = " selected";
        //     else                            $sel = " ";
        //     $return .= "<option value='".$row->codes."' $sel>".$row->description."</option>";
        // }
        // return $return;
    }
    
    /* ADDED BY JUSTIN - 02/09/2015 */
    function showEmployee($emp=""){
        $return = "<option value=''>- Select Employee-</option>";
        $query = $this->db->query("SELECT *,CONCAT(lname,', ',fname,' ',mname) as fullname FROM employee");
        foreach($query->result() as $row){
            if($row->employeeid == $emp)    $sel = " selected";
            else                            $sel = " ";
            $return .= "<option value='".$row->employeeid."' $sel>".$row->fullname."</option>";
        }
        return $return;
    }
            
    function showDay(){
        $strday = array(""=>"-Select Day-", "M"=>"Monday", "T"=>"Tuesday", "W"=>"Wednesday", "TH"=>"Thursday", "F"=>"Friday", "S"=>"Saturday", "SUN"=>"Sunday");
        $return = "";
        foreach($strday as $key=>$val){
            $return .= "<option value=$key>$val</option>";
        }
        return $return;
    }
    
    function showShift($code){
        $return = "";
        $sql = $this->db->query("SELECT description FROM code_type where code='$code'");
        foreach($sql->result() as $row){
            $return = $row->description;
        }
        return $return;
    }
    function showOfficialSchedHistory($id){
        $sql = $this->db->query("SELECT * FROM employee_official_schedule_history WHERE employeeid='$id' ORDER BY timestamp DESC");
        return $sql->result();
    }
    /* END */
    
    function idxval($dayofweek=''){
        $return = "";
        switch($dayofweek){
            case "M" : $return = "1";break;
            case "T" : $return = "2";break;
            case "W" : $return = "3";break;
            case "TH" : $return = "4";break;
            case "F" : $return = "5";break;
            case "S" : $return = "6";break;
            case "SUN" : $return = "0";break;
            default:
                $return = "";break;
        }
        return $return;
    }
    /* END */
    
    function showemployeestatus($content=""){
        //return array("FULL-TIME"=>"FULL-TIME","PART-TIME"=>"PART-TIME");
		if($content) $return = array(""=>$content);
        else $return = array(""=>"Choose a status ...");
        $q = $this->db->query("SELECT code,description FROM code_status order by description")->result();
        foreach($q as $oo){
          $return[$oo->code] = $oo->description;    
        }
        return $return;
    }
    function showcivilstatus(){
        return array("SINGLE"=>"SINGLE","MARRIED"=>"MARRIED","WIDOW"=>"WIDOW/WIDOWER");
    }

    function listCivilStatus(){
        $arrStatus = array("" => "Choose a status ...");
        $res = $this->db->query("SELECT code, description FROM code_civil_status ORDER BY code")->result();
        foreach ($res as $key) {
            $arrStatus[Globals::_e($key->code)] = Globals::_e($key->description);
        }
        return $arrStatus;
    }

    function listRankType(){
        $arrRankType = array("" => "Choose Rank Type ...");
        $q = $this->db->query("SELECT id, description FROM rank_code_type ORDER BY id")->result();
        foreach($q as $er){
            $arrRankType[Globals::_e($er->id)] = Globals::_e($er->description);
        }
        
        return $arrRankType;
    }

    function listRank(){
        $arrRank = array("" => "Choose Rank ...");
        $q = $this->db->query("SELECT id, description FROM rank_code ORDER BY id")->result();
        foreach($q as $er){
            $arrRank[Globals::_e($er->id)] = Globals::_e($er->description);
        }
        
        return $arrRank;
    }

    function listRankSet(){
        $arrRankSet = array("" => "Choose Rank ...");
        $q = $this->db->query("SELECT id, description FROM rank_code ORDER BY id")->result();
        foreach($q as $er){
            $arrRankSet[Globals::_e($er->id)] = Globals::_e($er->description);
        }
        
        return $arrRankSet;
    }

    function listRelation($gender='', $civil_status=''){
        $arrRelation = array("" => "Choose a relation ...");
        $wc = $code = "";
        if($civil_status == 2){
            if($gender == 'M'){
               $wc .= " AND relationshipid <> 'H'"; 
               $code = "W";
            } 
            else{
                $wc .= " AND relationshipid <> 'W'";
                $code = "H";
            } 
        }
        $q = $this->db->query("SELECT relationshipid,description FROM code_relationship WHERE 1 $wc ORDER BY relationshipid")->result();
        $icounter = count($q);
        if($code != ""){
            for ($i=0; $i < $icounter; $i++) { 
                if($q[$i]->relationshipid == $code){
                    $arrRelation[$q[$i]->relationshipid] = $q[$i]->description;
                    unset($q[$i]);
                }
            }
        }
        foreach($q as $er){
            $arrRelation[Globals::_e($er->relationshipid)] = Globals::_e($er->description);
        }
        
        return $arrRelation;
    }
    
    function civilstatusdesc($stat=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM code_civil_status where code='$stat'");
        foreach($query->result() as $row){
            $return = $row->description;
        }
        return $return;
    }
    
    function citizenshipdesc($stat=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM code_citizenship where citizenid='$stat'");
        foreach($query->result() as $row){
            $return = $row->description;
        }
        return $return;
    }  
    
    function religiondesc($stat=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM code_religion where religionid='$stat'");
        foreach($query->result() as $row){
            $return = $row->description;
        }
        return $return;
    }
    
    function nationalitydesc($stat=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM code_nationality where nationalityid='$stat'");
        foreach($query->result() as $row){
            $return = $row->description;
        }
        return $return;
    }
    
    function genderdesc($stat=""){
        $return = "";
        $query = $this->db->query("SELECT * FROM code_gender where genderid='$stat'");
        foreach($query->result() as $row){
            $return = $row->description;
        }
        return $return;
    }    

    function listSchoolYears(){
        $sqlsy = "SELECT DISTINCT A.SY FROM Poveda.tblStatusHistory AS A WHERE A.SY <> '' AND A.SY IS NOT NULL ORDER BY A.SY DESC";
        return $this->db->query($sqlsy)->result_array();
    }

    function listHolidayFreqs(){
        $arrFreq = array(0 => "Select frequency");
        $res = $this->db->query("SELECT freq_id, freq_description FROM code_holiday_freq")->result();
        foreach ($res as $key) {
            $arrFreq[$key->freq_id] = $key->freq_description;
        }
        return $arrFreq;
    }

    function showadjustment_code($issched=false){
        $return = array();
        $q = $this->db->query("SELECT `code`,`description`,rate,is_sched,salary_type FROM code_adjustment".($issched ? " where is_sched='1'" : ""));
        for($i=0;$i<$q->num_rows();$i++){
           $mrow = $q->row($i); 
           $return[$mrow->code] = $mrow->description;  
        }
        return $return; 
    }
    function counthours($ft,$et){
        $q = $this->db->query("SELECT TIMEDIFF('$et','$ft') as totdif;");
        $row = $q->row(0);
        list($h,$m,$s) = explode(":",$row->totdif);
        $timetot = substr($h,0,1)=="0" ? substr($h,1,1) : $h;
        $mins = substr($m,0,1)=="0" ? substr($m,1,1) : $m;
        $timetot += $mins/60;
        return $timetot;
    }
    function displaytablefields(&$sheet,$r,$c,$coltitle,$fields){
        foreach($fields as $colinfo){ 
         list($caption,$span,$width,$extra) = $colinfo;	
         if($span > 1) $sheet->setMerge($r, $c, $r, (($c-1) + $span));	
         $sheet->write($r,$c,$caption,$coltitle);
         if($extra){
           $sheet->writeNote($r,$c,$extra);	 
         }
         $sheet->setColumn($c,$c,$width);	
         $c += $span;
        }
    } 
    function changeenye($enye = ""){
    	$return = $enye;
    	$return = str_replace("Ãƒâ€˜","Ã‘",$return);
        $return = str_replace("Ã‘","Ñ",$return);
        $return = str_replace("Ã±","ñ",$return);
    	return $return;
    }
    function htmlchangeenye($enye = ""){
	       $return = $enye;
	       $return = str_replace("Ñ","&Ntilde;",$return);
           $return = str_replace("Ãƒâ€˜","Ã‘",$return);
           $return = str_replace("Ã‘","&Ntilde;",$return);
           $return = str_replace("??","&Eacute;",$return);
	       return $return;
   }
    function getdays(){
        $return = array();
    	for($t=1;$t<=31;$t++){
    	   $return[$this->reformstring($t)] = $this->reformstring($t);
    	}
    	return $return;
    }
    function getmonths(){
        $return = array();
    	for($t=1;$t<13;$t++){
    	   $return[$this->reformstring($t)] = date("M",strtotime("2001-".$this->reformstring($t)."-01"));
    	}
    	return $return;
    }
    function getyears($f="",$limit=10,$i = false){
        $return = array();
        $f = $f ? $f : date("Y");
        for($y=$f;($i ? ($y<=($f+$limit)) : ($y>=($f-$limit)));($i ? $y++ : $y--)){
          $return[$y] = date("Y",strtotime("$y-01-01"));  
        }
    	return $return;
    }
    function getperiodbycutoff($cutoffid=""){
        $sql = $this->db->query("select cutoff_period from cutoff_summary where id='{$cutoffid}'");
        if($sql->num_rows()>0){
          $return = $sql->row($o)->cutoff_period;      
        }
    	return $return;
    }
    function excel_column($col_number,$row_number) {
        if( ($col_number < 0) || ($col_number > 701)) die('Column must be between 0(A) and 701(ZZ)');
        if($col_number < 26) {
        return(chr(ord('A') + $col_number) . ($row_number+1));
        } else {
        $remainder = floor($col_number / 26) - 1;
        return(chr(ord('A') + $remainder) . excel_column($col_number % 26) . ($row_number+1));
        }
    }
    function showcutoffprocessed(){
        $return = array(""=>"DISPLAY ALL");
        $q = $this->db->query("SELECT a.`id`,CONCAT(b.description,' - ',DATE_FORMAT(datefrom,'%M %d, %Y'),' - ',DATE_FORMAT(dateto,'%M %d, %Y')) as `description` FROM cutoff_summary a inner join code_income_base b on b.income_base=a.cutoff_type where a.is_process='1' ORDER BY a.id DESC");
        for($i=0;$i<$q->num_rows();$i++){
           $mrow = $q->row($i); 
           $return[$mrow->id] = $mrow->description;  
        }
        return $return; 
    }
    function showusertype($utset){
        $return = "<option value=''></option>";
        $utype = $this->enum_select("user_info","type");
        foreach($utype as $ut){
            if($ut!="SUPER ADMIN") $return .= "<option".($utset==$ut?" selected":"")." value='{$ut}'>{$ut}</option>";
        }
        return $return;
    }
    function showrequesttype($rtypeid){
        $return = "";
        $utype = $this->db->query("select id,request_code,description from code_request_type")->result();
        foreach($utype as $ut){
            $ut->description = Globals::_e($ut->description);
            $return .= "<option".($rtypeid==$ut->request_code?" selected":"")." value='{$ut->request_code}'>{$ut->description}</option>";
        }
        return $return;
    }
    function showrelation($rtypeid){
        $return = "<option value=''></option>";
        $utype = $this->db->query("select relationshipid,description from code_relationship")->result();
        foreach($utype as $ut){
            $return .= "<option".($rtypeid==$ut->relationshipid?" selected":"")." value='{$ut->relationshipid}'>{$ut->description}</option>";
        }
        return $return;
    }

    function setHolidayAffectedDepartments($hol_id,$depts,$permanent,$prob,$contractual){
        $temp1 = $temp2 = $temp3 = "";
        $queDel = "DELETE FROM holiday_inclusions WHERE holi_cal_id = {$hol_id}";
        $this->db->query($queDel);
        if (!empty($depts)) {
            foreach ($depts as $key => $deptvalue) {
                foreach($permanent as $pkey=>$pval){
                    $pvale = explode("~",$pval);
                    if($pvale[0] == $deptvalue){  
                        $temp1 = $pval;break;
                    }else
                        $temp1 = "";
                }
                foreach($prob as $pkey=>$pval){
                    $prval = explode("~",$pval);
                    if($prval[0] == $deptvalue){ 
                        $temp2 = $pval;break;
                    }else
                        $temp2 = "";
                }
                foreach($contractual as $pkey=>$pval){
                    $cont = explode("~",$pval);
                    if($cont[0] == $deptvalue){  
                        $temp3 = $pval;break;
                    }else
                        $temp3 = "";
                }
                $queAdd = "INSERT INTO holiday_inclusions VALUES({$hol_id},'{$deptvalue}','$temp1','$temp2','$temp3')";
                $this->db->query($queAdd);
            }// end foreach
        }// end if
    }// end function

    function checkIfInList($needle, $listVar){
        $inList = false;
        foreach ($listVar as $key => $listItem) {
            if ($needle == $listItem) {
                $inList = true;
                break;
            }
        }
        return $inList;
    }
    
    function setPass(){
        return "327ycaza";
    }
    
    function messages($cat="",$param = "",$dfrom = "",$dto = ""){
    $query = "";        
    $whereClause = "";
   # if($this->session->userdata("userid") != 2){
    if($param) $whereClause .= " AND a.id='$param'";
    if($cat)   $whereClause .= " AND a.status='$cat'";
    if(!empty($dfrom) && !empty($dto)) $whereClause .= " AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto'";
    
    $query = $this->db->query("SELECT a.id, a.receiver, a.date, a.description, a.sender, a.status, a.timestamp 
                                FROM messages a 
                                LEFT JOIN user_info b ON a.receiver = b.id 
                                WHERE (FIND_IN_SET('".$this->session->userdata("userid")."',receiver) OR receiver='0') $whereClause ORDER BY timestamp DESC")
                                ->result();
  #  }
    return $query;
    }
    
    function viewCutOff($dfrom='',$dto=''){
        $wC = "";
        if(!empty($dfrom) && !empty($dto))  $wC = " WHERE CutoffFrom = '$dfrom' AND CutoffTo = '$dto'";
        $query = $this->db->query("SELECT a.`ID`,a.`CutoffFrom`,a.`CutoffTo`,a.`TPostedDate`,a.`NTPostedDate`,b.`schedule`,b.`quarter`,b.`startdate`,b.`enddate`,a.`ConfirmFrom`,a.`ConfirmTo` FROM cutoff AS a LEFT JOIN payroll_cutoff_config b ON(a.`ID` = b.`baseid`) $wC  ORDER BY a.`CutOffFrom` DESC")->result();
        return $query;
    }
    
    function viewCutOffConfirmed($dfrom='',$dto='',$dept=''){
        $wC = "";
        if(!empty($dfrom) && !empty($dto))  $wC = " WHERE CutOffFrom = '$dfrom' AND CutOffTo = '$dto' AND dateresigned = '1970-01-01'";
        if(!empty($dept))                   $wC .= " AND deptid='$dept'";
        $query = $this->db->query("SELECT * FROM cutoff_confirmed INNER JOIN employee USING (employeeid) $wC");
        return $query;
    }
    
    function viewCutOffNoConfirmed($dfrom='',$dto='',$dept=''){
        $wC = "";$param = "";
        if(!empty($dfrom) && !empty($dto))  $wC = " WHERE CutOffFrom = '$dfrom' AND CutOffTo = '$dto'";
        if(!empty($dept))                   $param .= " AND deptid='$dept' AND dateresigned = '1970-01-01'";
        $query = $this->db->query("SELECT employeeid FROM employee WHERE employeeid NOT IN (SELECT employeeid FROM cutoff_confirmed $wC) $param");
        return $query;
    }
    
    function viewcutoffdate(){
    $cutoffbox = "<option value=''>- Cut-Off Date -</option>";
    $cutoffq = $this->db->query("SELECT CutoffFrom, CutoffTo FROM cutoff ORDER BY CutoffFrom DESC");
    foreach($cutoffq->result() as $qrow){
        $cutoffbox .= "<option value='".$qrow->CutoffFrom."|".$qrow->CutoffTo."'>".date('F d, Y',strtotime($qrow->CutoffFrom))." to ".date('F d, Y',strtotime($qrow->CutoffTo))." </option>";
    }
    return $cutoffbox; 
    }
    
    function editCutoff($key = ''){
        $query = $this->db->query("SELECT a.`ID`,a.`CutoffFrom`,a.`CutoffTo`,a.`TPostedDate`,a.`NTPostedDate`,b.`schedule`,b.`quarter`,b.`startdate`,b.`enddate`,a.`ConfirmFrom`,a.`ConfirmTo`,a.`TimeFrom`,a.`TimeTo`,b.nodtr FROM cutoff AS a LEFT JOIN payroll_cutoff_config b ON(a.`ID` = b.`baseid`)WHERE a.`ID`='$key' ORDER BY a.`Timestamp` ");
        return $query->result();
    }
    
    function removeID($ltype = ''){
        $msg = "";
        if($ltype == "E"){
            $query = $this->db->query("UPDATE employee SET employeecode = ''");
        }else{
            $query = $this->db->query("UPDATE StJude.tblPersonalData SET StudCardNo = ''");
            $query = $this->db->query("UPDATE student SET studentcode = ''");
        }
        if($query)  $msg = "All ID No. is Successfully Deleted..";
        return $msg;
    }
    
    function hrDocx(){
        $id = "";
        $cquery = $this->db->query("SELECT id FROM elfinder_file WHERE NAME='HR FORMS'")->result();
        
        foreach($cquery as $row){
            $id = $row->id;
        }
        $query = $this->db->query("SELECT title FROM elfinder_file WHERE parent_id='$id'")->result();
        return $query;
    }
    
    function opengate(){
        $opengate = `nohup php /var/www/incl/id_server.php &`;
    }
    
    function leavedatevalidity($data){
        $toks = $data['toks'];
        $startdate = $this->gibberish->decrypt($data['dfrom'], $toks);
        $enddate   = $this->gibberish->decrypt($data['dto'], $toks);
        $ltype     = $this->gibberish->decrypt($data['ltype'], $toks);        
        $query = $this->db->query("SELECT * FROM code_request_form WHERE leavetype='$ltype' AND (('$startdate' BETWEEN startdate AND enddate) OR ('$enddate' BETWEEN startdate AND enddate))");
        return $query->num_rows();
    }
    
    function leavetype($ltype = ""){
        $return = "";
        $arr = array("OLD"=>"OLD","NEW"=>"NEW","MID"=>"NEW-MID","NEW9"=>"NEW9","NEW8"=>"NEW8","NEW7"=>"NEW7","NEW6"=>"NEW6");
        foreach($arr as $key=>$val){
            if($ltype == $key)
                $return .= "<option value='$key' selected>$val</option>";
            else
                $return .= "<option value='$key'>$val</option>";
        }
        return $return;
    }
    
    function ftwodigits(){
        $return = "<option value=''>All ID</option>";
        $query = $this->db->query("SELECT A.employeeid FROM (SELECT SUBSTR(employeeid,1,2) AS employeeid FROM employee) AS A WHERE A.employeeid REGEXP '^[0-9]+$' GROUP BY A.employeeid;");
        foreach($query->result() as $row){
            $return .= "<option value='".$row->employeeid."'>".$row->employeeid."</option>";
        }   
        return $return; 
    }
    function save_terminal(){
         $query   = $this->db->query("
                            INSERT INTO code_terminal (id,terminal_name,campus,building,`floor`,`password`,rt_password)
                                VALUES ('{$id}','{$terminal_name}','{$campus}','{$building}','{$floor}','{$password}','{$rt_password}')
                            ");
        return $query;
    }
    
    function   saveltype($data){
        $dept  = $data['deptid'];
        $eid   = $data['employeeid'];
        $ltype = $data['ltype'];
        $twod  = $data['eidtwo'];
        $wC    = "";
        
        if($dept)   $wC = " AND deptid='$dept'";
        if($eid)    $wC = " AND employeeid='$eid'";
        if($twod)   $wC = " AND SUBSTR(employeeid,1,2)='$twod'";
        $query = $this->db->query("UPDATE employee SET leavetype='$ltype' WHERE dateresigned='1970-01-01' $wC");
        if($query)  return "Successfully Saved!.";
        else        return "Failed to Saved!. Please check your connection..";
    }
    
    function updateltype($data){
        $series = $data['eid'];
        $type   = $data['type'];
        $query = $this->db->query("INSERT INTO leavetype_trail (seriesno,type,user) VALUES ('$series','$type','".$this->session->userdata("username")."')");
        $query = $this->db->query("UPDATE employee SET leavetype='$type' WHERE SUBSTR(employeeid,1,2) = '$series'");
        if($query)  return "Update Successfully!.";
        else        return "Failed to Update!. Please Check your connection..";
    }
    
    function showLeaveTrail(){
        $return = "";
        $query = $this->db->query("SELECT * FROM leavetype_trail");
        foreach($query->result() as $row){
            $return .=  "<tr>
                            <td>".$row->seriesno."</td>
                            <td>".$row->type."</td>
                            <td>".$row->user."</td>
                            <td>".date('F d, Y h:i:s',strtotime($row->timestamp))."</td>
                        </tr>";
        }
        return $return;
    }
    
    function OtTime($eid = "", $dfrom = "",$dto = ""){
        $query = $this->db->query("SELECT SUM(overtime) as ttime FROM payroll_emp_otaccepted WHERE employeeid='$eid' AND otdate BETWEEN '$dfrom' AND '$dto'");
        return $query->row(0)->ttime;
    }
    
    function showtimedtr($eid='', $date=''){
        $timein = $timeout = "";
        $query = $this->db->query("SELECT TIME_FORMAT(timein,'%h:%i %p') AS tin, TIME_FORMAT(timeout,'%h:%i %p') AS tout FROM timesheet WHERE DATE(timein)='$date' AND userid='$eid' LIMIT 1");
        if($query->num_rows() > 0){
            $timein  = $query->row(0)->tin;
            $timeout = $query->row(0)->tout;
        }else{
            $query = $this->db->query("SELECT TIME_FORMAT(starttime,'%h:%i %p') AS tin, TIME_FORMAT(endtime,'%h:%i %p') AS tout FROM employee_schedule_adjustment WHERE DATE(cdate)='$date' AND employeeid='$eid' ORDER BY id DESC LIMIT 1");
            $timein  = $query->row(0)->tin;
            $timeout = $query->row(0)->tout;
        }
        
        if(empty($timein)){
            $query = $this->db->query("SELECT TIME_FORMAT(logtime,'%h:%i %p') AS tin FROM timesheet_trail WHERE DATE(logtime)='$date' AND userid='$eid' AND log_type='IN' LIMIT 1");
            if($query->num_rows() > 0)  $timein  = $query->row(0)->tin;
        }
        
        if(empty($timeout)){
            $query = $this->db->query("SELECT TIME_FORMAT(logtime,'%h:%i %p') AS tin FROM timesheet_trail WHERE DATE(logtime)='$date' AND userid='$eid' AND log_type='OUT' ORDER BY logtime DESC LIMIT 1");
            if($query->num_rows() > 0)  $timeout  = $query->row(0)->tin;
        }
        
        return array($timein,$timeout);
    }
    
    function showAccessmsg($user = ""){
        $return = false;
        $query = $this->db->query("SELECT * FROM user_info WHERE id='$user' AND msgaccess=1");
        if($query->num_rows() > 0)  $return = true;
        return $return;
    }
    
    function getTimeIn(){
        $return = array("","");
        $islate = false;
        $empid = $this->session->userdata("username");
        $sched = $this->attcompute->displaySched($empid,date("Y-m-d"));
        foreach($sched->result() as $rsched){
            $stime  = $rsched->starttime;
            $etime  = $rsched->endtime; 
            list($login,$logout,$q)           = $this->attcompute->displayLogTime($empid,date("Y-m-d"),$stime,$etime,"NEW");
            $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,"");
            $return = array($login,$islate);
        }
        return $return;
    }

    function getTimeInEmployeeRemarks(){
        $return = array("","");
        $islate = false;
        $empid = $this->session->userdata("username");
        $sched = $this->attcompute->displaySched($empid,date("Y-m-d"))->result();
            // echo "<pre>";print_r($this->db->last_query());die;
        if (empty($sched)) {
            $remarks = "noSched";
            $time = "Please add a schedule.";
        }else{
            $tardy =  date("H:i:s",strtotime($sched[0]->tardy_start));
            $absent =  date("H:i:s",strtotime($sched[0]->absent_start));
            list($login,$logout,$q)            = $this->attcompute->displayLogTime($empid,date("Y-m-d"),$sched[0]->starttime,$sched[0]->endtime,"NEW");
            if ($login != "") {
               $login =  date("H:i:s",strtotime($login));
            }

            if($login == "" && date("H:i s",strtotime("now")) > $tardy) {
                $remarks = "absent";
                $time = date("h:i A",strtotime($absent));
            }elseif($login == "") {
                $remarks = "notlog";
                $time = date("h:i A",strtotime($sched[0]->starttime));
            }elseif($login >= $tardy) {
                $remarks = "LateIn";
                $time = date("h:i A",strtotime($login));
            }elseif($login <= $tardy) {
                $remarks = "On Time";
                $time = date("h:i A",strtotime($login));
            }
        }
            
            
            $return = array($remarks,$time);
        return $return;
    }
            
    function showHol($month = "", $year = ""){
        $return = "";
        $query = $this->db->query("SELECT date_from,date_to,hdescription,c.description FROM code_holiday_calendar a
                                    INNER JOIN code_holidays b ON a.holiday_id = b.holiday_id
                                    INNER JOIN code_holiday_type c ON b.holiday_type = c.holiday_type
                                    WHERE SUBSTR(date_from,1,7) = '$year-$month'  ORDER BY date_from")->result();
        return $query;
    } 

    function showHolPast($month = "", $year = ""){
        $return = "";
        $query = $this->db->query("SELECT date_from,date_to,hdescription,c.description FROM code_holiday_calendar a
                                    INNER JOIN code_holidays b ON a.holiday_id = b.holiday_id
                                    INNER JOIN code_holiday_type c ON b.holiday_type = c.holiday_type
                                    WHERE SUBSTR(date_from,1,7) = '$year-$month' AND DATE(NOW()) > a.`date_to` ORDER BY date_from")->result();
        return $query;
    }       
    
    function holDate($dfrom="",$dto=""){
        $query = "";
        $query = $this->db->query("SELECT DATE('{$dfrom}') + INTERVAL A + B + C DAY dte FROM
                                    (SELECT 0 A UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 ) d,
                                    (SELECT 0 B UNION SELECT 10 UNION SELECT 20 UNION SELECT 30 UNION SELECT 40 UNION SELECT 60 UNION SELECT 70 UNION SELECT 80 UNION SELECT 90) m , 
                                    (SELECT 0 C UNION SELECT 100 UNION SELECT 200 UNION SELECT 300 UNION SELECT 400 UNION SELECT 600 UNION SELECT 700 UNION SELECT 800 UNION SELECT 900) Y
                                    WHERE DATE('{$dfrom}') + INTERVAL A + B + C DAY  <=  DATE('{$dto}') ORDER BY A + B + C;")->result();
        return $query;
    }
    
    function imgExists(){
        $query = $this->db->query("SELECT * FROM elfinder_file WHERE title='".$this->session->userdata("username")."'");
        return $query->num_rows();
    }

    function userPhoto(){
        return $this->db->query("SELECT * FROM employee_photo WHERE employeeid='".$this->session->userdata("username")."'"); 
    }
    
    function infoEditRestriction(){
        $query = $this->db->query("SELECT * FROM ");
    }
    
    /*
     * Others
     */
    function clean($string) {
       return preg_replace('/[^A-Za-z0-9\- ]/', '', $string); // Removes special chars.
    }
    function computeAge($age=""){
      //date in mm/dd/yyyy format; or it can be in other formats as well
      $birthDate = date("m/d/Y",strtotime($age));
      //explode the date to get month, day and year
      $birthDate = explode("/", $birthDate);
      //get age from date or birthdate
      $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
      return $age;
    }
	
	//Addedd 5-19-17
	function getDeptDesc($deptid="", $isDepartment=""){
		$return = ($isDepartment) ? "No Department" : "No Office";
		$query = $this->db->query("SELECT * FROM code_office WHERE department_id = '{$deptid}'")->result();
		foreach($query as $row)
		{
			$return = Globals::_e($row->description);
		}
		return $return;
		
	}

    public function getCampus($campus=""){
        $result = $this->db->query("SELECT code,description FROM code_campus GROUP BY description ASC")->result();
        // $return = "<option value='All'>All Campus</option>";
        foreach ($result as $value) {
            if ($value->code == $campus) {
                $return .= "<option value='$value->code' selected >$value->description</option>";
            }else $return .= "<option value='$value->code'>$value->description</option>";
        }

        return $return;
    }
	
	function getEmpSchedule($empid=""){
		$query = $this->db->query("SELECT * FROM employee_schedule WHERE employeeid = '{$empid}' ORDER BY idx ASC")->result();
		return $query;
		
	}

    function validateBatchDtr($data){
        foreach($data as $row){
            if(!$row) return false;
        }
    }

    ##Modified by Glen Mark
	//Batchapproval for Manage DTR
    function batchApprovalDTR($data="", $toks='')
    // function batchapprovalDTR($data="")
    {

        $dow = array("SUN","M","T","W","TH","F","SAT");
        $user = $this->session->userdata('username');

        $result = "";
        $msg = "";
        $count = $countInsert = "";
        $queryInsert ="";
        $datas = explode("|", $data);
        $prev_eid = $baseid = $tID = '';
            # code...
            foreach ($datas as $value) {
                list($eid,$timein,$timeout,$date,$timestamp, $remarks) = explode("~u~", $value);
                $eid = $this->gibberish->decrypt( $eid, $toks );
                $timein = $this->gibberish->decrypt( $timein, $toks );
                $timeout = $this->gibberish->decrypt( $timeout, $toks );
                $date = $this->gibberish->decrypt( $date, $toks );
                $timestamp = $this->gibberish->decrypt( $timestamp, $toks );
                $remarks = $this->gibberish->decrypt( $remarks, $toks );
                $timestamp = $timestamp?$timestamp:"";
                if($timein != '') $finaltimein = date("Y-m-d H:i:s",strtotime("$date $timein"));
                else $finaltimein = '0000-00-00 00:00:00';
                
                if($timeout != '') $finaltimeout = date("Y-m-d H:i:s",strtotime("$date $timeout"));
                else $finaltimeout = '0000-00-00 00:00:00';

                $actual_timein = $actual_timeout = '';

                //< insert to adjustment
                
                $idx = date("w",strtotime($date));
                $status = '';

                if($eid != $prev_eid){

                    $select = $this->db->query("SELECT * FROM employee_schedule_adjustment WHERE cdate ='$date' AND dayofweek='{$dow[$idx]}' AND idx='{$idx}' AND employeeid='{$eid}'");
                    if ($select) $status = 'UPDATED';
                    $insert_adj = $this->db->query("INSERT INTO employee_schedule_adjustment (employeeid, cdate, dayofweek, idx, remarks, editedby,status) VALUES ('$eid','$date','{$dow[$idx]}','{$idx}','{$remarks}','{$user}','UPDATED') ");

                    if($insert_adj) $baseid = $this->db->insert_id();

                }



                if ($timeout != "" && $timein !=""){

                    $query = $this->db->query("SELECT * FROM timesheet WHERE date(timein) ='$date' AND date(timeout) ='$date' AND userid='$eid' AND timeid='$timestamp'");

                    if ($query->num_rows() > 0){

                        $actual_timein = date('h:i a',strtotime($query->row(0)->timein));
                        $actual_timeout = date('h:i a',strtotime($query->row(0)->timeout));
                        $tID = $query->row(0)->timeid;

                       $queryInsert = $this->db->query("UPDATE timesheet SET timein = '$finaltimein', timeout ='$finaltimeout' WHERE userid='$eid' AND date(timein) = '$date' AND date(timeout) = '$date' AND timeid='$timestamp'");
                       if ($queryInsert){
                            $countInsert ++;
                        }
                    
                    }else{

                        $queryInsert = $this->db->query("INSERT INTO timesheet(userid,timein,timeout)VALUES('$eid','$finaltimein','$finaltimeout')");
                        if ($queryInsert){
                            // $insert = $this->db->query("INSERT INTO employee_schedule_adjustment(employeeid,cdate)VALUES('$eid','$date')");
                            $countInsert++;
                        }
                    }
                }
                else if ($timein != "" && $timeout == "") 
                {
                   $query = $this->db->query("SELECT * FROM timesheet_trail WHERE userid='$eid' AND date(logtime) ='$date'");
                    if ($query->num_rows() > 0) 
                    {
                      $actual_timein = date('h:i a',strtotime($query->row(0)->logtime));

                      $queryInsert = $this->db->query("UPDATE timesheet_trail SET logtime = '$finaltimein' WHERE userid='$eid' AND date(logtime) = '$date'");
                        if ($queryInsert){
                            $countInsert ++;
                        }
                    }else{

                        $queryInsert = $this->db->query("INSERT INTO timesheet_trail(userid,logtime) VALUES('$eid','$finaltimein')");
                        if ($queryInsert){
                            $countInsert ++;
                        }
                    }
                   
                }


                $actual_time = ($actual_timein || $actual_timeout) ? $actual_timein . " - " . $actual_timeout : '';

                $final_time = (($timein != '') ? date('Y-m-d h:i A',strtotime($finaltimein)) : '0000-00-00 00:00:00') . ' - ' . (($timeout != '') ? date('Y-m-d h:i A',strtotime($finaltimeout)) : '0000-00-00 00:00:00' );

                $findExistBIdAndTId = $this->db->query("SELECT * FROM employee_schedule_adjustment_ext WHERE baseID={$baseid} AND tID='{$tID}'")->result();
                if(count($findExistBIdAndTId) && $prev_eid != $eid){
                    foreach ($findExistBIdAndTId as $febat) {
                        $id = $febat->id;
                        $this->db->query("UPDATE employee_schedule_adjustment_ext SET actual_time='{$actual_time}', final_time='{$final_time}' WHERE id={$id}");
                        // echo "<pre>"; print_r($this->db->last_query());
                    }
                }else{
                    $this->db->query("INSERT INTO employee_schedule_adjustment_ext (baseID, tID, actual_time,final_time) VALUES ('{$baseid}','{$tID}','{$actual_time}','{$final_time}')");
                }

                $prev_eid = $eid;
                $tID = '';
            }
            

        if ($queryInsert) {
            return "Successfully Saved!";
        }
        else
        {
            return "Failed to saved data!";

        }
        
    }

    // for holiday 
    // author : justin (with e)
    function getCodeStatus(){
        $ret = $this->db->query("SELECT * FROM code_status ORDER BY seqno");
        return $ret;
    }
    function saveHolidayInclusion($hol_id,$dept,$stat){
        // save holiday inclusion
        $this->db->query("INSERT INTO holiday_inclusions (holi_cal_id, dept_included, status_included) VALUES ({$hol_id},'{$dept}','{$stat}')");
    }
    function findStatusIncluded($hol_id=0,$dept){
        $query = $this->db->query("SELECT * FROM holiday_inclusions WHERE holi_cal_id='{$hol_id}' AND dept_included='{$dept}'");
        return $query;
    }
	// end for holiday 
	
    // for manage dtr
    // justin (with e)
    function findTimeRecordModel($eid, $cdate){
        $wc = $starttime = $endtime = '';
        $facialLogs = $facial_log = array();
        // $wc = " AND userid NOT IN (SELECT b.employeeid FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE ('$cdate' BETWEEN a.datefrom AND a.dateto) AND a.isHalfDay = '1')";
        $query = $this->db->query("SELECT * FROM timesheet WHERE userid='{$eid}' AND (timein LIKE '%{$cdate}%' OR timeout LIKE '%{$cdate}%')  $wc");
        if($query->num_rows() == 0){
            $query = $this->db->query("SELECT DISTINCT logtime as timein, '' as timeout, '' as type, '' as timeid, userid FROM timesheet_trail WHERE userid='{$eid}' AND logtime LIKE '%{$cdate}%'");
            if($query->num_rows() == 0){
                $query = $this->db->query("SELECT DISTINCT localtimein as timein, '' as timeout, '' as type, '' as timeid, userid FROM webcheckin_history WHERE userid='{$eid}' AND localtimein LIKE '%{$cdate}%'");
                if($query->num_rows() == 0){
                    $query = $this->db->query("SELECT DISTINCT logtime as timein, '' as timeout, '' as type, '' as timeid, userid FROM timesheet_noout WHERE userid='{$eid}' AND logtime LIKE '%{$cdate}%'");
                    if($query->num_rows() == 0){
                        $query = $this->db->query("SELECT a.time, b.employeeid FROM facial_Log as a INNER JOIN facial_person as b ON (a.personId = b.personId) WHERE b.employeeid='{$eid}' AND DATE(FROM_UNIXTIME(FLOOR(a.time/1000))) LIKE '%{$cdate}%' GROUP BY a.id, a.time");
                        if($query->num_rows() > 0){
        

                            foreach ($query->result() as $key => $value) {
                                if(isset($facialLogs[date("Y-m-d", substr($value->time, 0, 10))])){
                                    if($facialLogs[date("Y-m-d", substr($value->time, 0, 10))]['timein'] < date("Y-m-d g:i:s A", substr($value->time, 0, 10))){
                                        $facialLogs[date("Y-m-d", substr($value->time, 0, 10))]['timein'] = date("Y-m-d g:i:s A", substr($value->time, 0, 10));
                                    }else{
                                        $facialLogs[date("Y-m-d", substr($value->time, 0, 10))]['timeout'] = date("Y-m-d g:i:s A", substr($value->time, 0, 10));
                                    }
                                }else{
                                    $facialLogs[date("Y-m-d", substr($value->time, 0, 10))] = array(
                                        "timein" => date("Y-m-d g:i:s A", substr($value->time, 0, 10)),
                                        "timeout" => '',
                                        "type" => '',
                                        "timeid" => '',
                                        "userid" => $value->employeeid
                                    );
                                }
                                // echo "<pre>"; print_r(date("Y-m-d g:i:s A", substr($value->time, 0, 10))); 
                            }
                            // die;
                            $facialLogs = json_encode($facialLogs);
                            $facialLogs = json_decode($facialLogs, FALSE);
                            return $facialLogs;
                            die;
                        }
                    }
                }
            }
        }else{
            $query = $this->db->query("SELECT * FROM timesheet WHERE userid='{$eid}' AND (timein LIKE '%{$cdate}%' OR timeout LIKE '%{$cdate}%')  $wc GROUP BY TIME(timein), TIME(timeout)");
            $timesheetdata = $query->result();

            $queries = $this->db->query("SELECT DISTINCT logtime as timein, '' as timeout, '' as type, '' as timeid, userid FROM timesheet_trail WHERE userid='{$eid}' AND logtime LIKE '%{$cdate}%'");
            if($queries->num_rows() == 0){
                $query = $this->db->query("SELECT DISTINCT localtimein as timein, '' as timeout, '' as type, '' as timeid, userid FROM webcheckin_history WHERE userid='{$eid}' AND localtimein LIKE '%{$cdate}%'");
            }
            $timesheetdata = array_merge($queries->result(), $timesheetdata);
            $q = $this->db->query("SELECT a.sched_affected FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE ('$cdate' BETWEEN a.datefrom AND a.dateto) AND a.isHalfDay = '1' AND b.employeeid = '$eid'");
            if($q->num_rows() > 0){
                foreach ($q->result() as $value) {
                    list($starttime, $endtime) = explode("|", $value->sched_affected);
                    $starttime = date("H:i",strtotime($starttime));
                    $endtime = date("H:i",strtotime($endtime));
                    foreach ($timesheetdata as $k => $v) {
                        if((date("H:i",strtotime($v->timein)) <= $starttime && date("H:i",strtotime($v->timeout)) >= $endtime)){
                            /*commented for now as of ms hazel*/
                            // unset($timesheetdata[$k]);
                        } 
                    }
                }
            }
            return $timesheetdata;
            die;

        }

        return $query->result();
    }

    function checkWebSetupStatus($id){
        $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$id' AND STATUS = 'active'");
        if($weblogin->num_rows() > 0) return $weblogin->row()->status;
        else return false;
    }

    function getOfficeDescription()
    {
        $return = array();
        $query = $this->db->query("SELECT code,description FROM code_department ORDER BY code");
        foreach ($query->result() as $row) {
            $return[$row->code] = $row->description;
        }
        return $return;
    }

    function getDepartmentDescription()
    {
        $return = array();
        $query = $this->db->query("SELECT code,description FROM code_office ORDER BY code");
        foreach ($query->result() as $row) {
            $return[$row->code] = $row->description;
        }
        return $return;
    }

    function getOffice($officeid = "", $department = "") {
        $return = "<option value=''>  All Office  </option>";
        $wc = "";
        if ($department) {
            $wc = "AND department_id = '$department'";
        }
        $query = $this->db->query("SELECT code, description FROM code_office WHERE 1 $wc")->result();
        foreach ($query as $key) {
            $key->code = Globals::_e($key->code);
            $key->description = Globals::_e($key->description);
            if($key->code != ''){
                if($officeid == $key->code) $return .= "<option value='$key->code' selected>$key->description</option>";
                else $return .= "<option value='$key->code'>$key->description</option>";
            }
                
        }

        return $return;
    }

    function getSurveyCategory($id = "") {
        $return = "<option value=''>  All Category  </option>";
        $query = $this->db->query("SELECT id, name FROM survey_category ")->result();
        foreach ($query as $key) {
            if($id == $key->id) $return .= "<option value='$key->id' selected>$key->name</option>";
            else $return .= "<option value='$key->id'>$key->name</option>";
        }

        return $return;
    }

    function getSurveyDescription($description = "") {
        $return = "<option value=''>  All Survey  </option>";
        $query = $this->db->query("SELECT description, description FROM survey_items ")->result();
        foreach ($query as $key) {
            if($description == $key->description) $return .= "<option value='$key->description' selected>$key->description</option>";
            else $return .= "<option value='$key->description'>$key->description</option>";
        }

        return $return;
    }

    function getDeptpartment($deptid = "") {
        $return = "<option value=''>  All Department  </option>";
        $query = $this->db->query("SELECT code, description FROM code_department ")->result();
        foreach ($query as $key) {
            $key->code = Globals::_e($key->code);
            $key->description = Globals::_e($key->description);
            if($deptid == $key->code) $return .= "<option value='$key->code' selected>$key->description</option>";
            else $return .= "<option value='$key->code'>$key->description</option>";
        }

        return $return;
    }

    function getUnderDeptEmployee($category='', $office='', $deptid='', $username='', $employeeid=''){
         $wc = '';
            if($office && ($office != 'All' && $office != 'all')) $wc .= " AND a.office = '$office' ";
            if($deptid && ($deptid != 'All' && $deptid != 'all')) $wc .= " AND a.deptid = '$deptid' ";
            if($employeeid) $wc .= " AND a.employeeid = '$employeeid' ";
            if($category != 'all') $wc .= " AND b.is_completed = '$category' ";
            return $this->db->query("SELECT b.id AS empdef_id, c.description AS defdesc,d.description AS deptdesc, a.office as empoffice, b.* FROM employee a LEFT JOIN employee_deficiency b ON a.employeeid = b.employeeid INNER JOIN code_deficiency c on b.def_id=c.id LEFT JOIN code_office d ON d.code=b.concerned_dept WHERE b.lookfor = '$username' $wc ORDER BY employeeid ")->result();
    }

    function getDevices($serial_number = "") {
        $return = "<option value=''>  All Devices  </option>";
        $query = $this->db->query("SELECT deviceKey, deviceName FROM facial_heartbeat")->result();
        foreach ($query as $key) {
            $key->deviceKey = Globals::_e($key->deviceKey);
            $key->deviceName = Globals::_e($key->deviceName);
            if($serial_number == $key->deviceKey) $return .= "<option value='$key->deviceKey' selected>$key->deviceName</option>";
            else $return .= "<option value='$key->deviceKey'>$key->deviceName</option>";
        }

        return $return;
    }

    function saveManageDTRModel($data, $idx, $editBy, $toks = ""){
        $dow = array("SUN","M","T","W","TH","F","SAT");
        if($toks){
            foreach ($data as $key => $value) {
                if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
            }
        }
    
        $select = $this->db->query("SELECT * FROM employee_schedule_adjustment WHERE cdate ='{$data['cdate']}' AND dayofweek='{$dow[$idx]}' AND idx='{$idx}' AND employeeid='{$data['eid']}'");
        if ($select) {
           $query = $this->db->query("INSERT INTO employee_schedule_adjustment (employeeid, cdate, dayofweek, idx, remarks, editedby,status) VALUES ('{$data['eid']}','{$data['cdate']}','{$dow[$idx]}','{$idx}','{$data['remarks']}','{$editBy}','UPDATED')");
        }
        else
        {
            $query = $this->db->query("INSERT INTO employee_schedule_adjustment (employeeid, cdate, dayofweek, idx, remarks, editedby) VALUES ('{$data['eid']}','{$data['cdate']}','{$dow[$idx]}','{$idx}','{$data['remarks']}','{$editBy}')");
        }
        return $query = $this->db->query("SELECT id FROM employee_schedule_adjustment WHERE id=(SELECT MAX(id) FROM employee_schedule_adjustment)")->row()->id;
    }

    function saveManageDTRAndTimesheet($eid,$bID, $tID,$fTime,$timein,$timeout){
        
        // save to employee_schedule_adjustment_ext
        $findExistBIdAndTId = $this->db->query("SELECT * FROM employee_schedule_adjustment_ext WHERE baseID={$bID} AND tID='{$tID}'")->result();
        if(count($findExistBIdAndTId)){
            foreach ($findExistBIdAndTId as $febat) {
                $id = $febat->id;
                $this->db->query("UPDATE employee_schedule_adjustment_ext SET final_time='{$fTime}' WHERE id={$id}");
            }
        }else{
            $this->db->query("INSERT INTO employee_schedule_adjustment_ext (baseID, tID, final_time) VALUES ({$bID},'{$tID}','{$fTime}')");
        }
        // end of saving to employee_schedule_adjustment_ext

        // save to timesheet
        $this->db->query("INSERT INTO timesheet (userid, timein, timeout) VALUES ('{$eid}','{$timein}','{$timeout}')");
        // end saving to timesheet
    }
    function findRemarks($id=0){
        $result = $this->db->query("SELECT description FROM code_request_type WHERE request_code=".$id);
        if($result->num_rows() > 0) return $result->row()->description;
        else return false;
    }
    // end for manage dtr
     function leaveSetup($job, $data){
        if($job == 0){
            $sql = "DELETE FROM code_request_form WHERE id=".$data['id'];
            $this->db->query($sql);
            return $sql;
        }else{
            $sql = "INSERT INTO code_request_form
                              (code_request,description,details,dhseq,hhseq,chseq,cpseq,upseq,boseq,fdseq,pseq,budgetoff,univphy,univphyt,financedir,president,is_leave, ismain) 
                              VALUES 
                              ('{$data['code']}','{$data['description']}','{$data['details']}','{$data['dhseq']}','{$data['hhseq']}','{$data['chseq']}','{$data['cpseq']}','{$data['upseq']}','{$data['boseq']}','{$data['fdseq']}','{$data['pseq']}','{$data['bo']}','{$data['up']}','{$data['upt']}','{$data['fd']}','{$data['pres']}',{$data['mngt']},{$data['mngt']})";
            $this->db->query($sql);
            return $sql;
        }
    }

    # for ica-hyperion 21152
    # by : justin (with e) 
    function getAllEmployee($empID){
        $data = array();
        $getCode = $this->db->query("SELECT DISTINCT code FROM code_office WHERE head='$empID' OR divisionhead='$empID'")->row()->code;
        $getEmployee = $this->db->query("SELECT employeeid AS empID, CONCAT(lname, ', ', fname, ' ', mname) AS fullname FROM employee WHERE deptid='$getCode'")->result();
        foreach ($getEmployee as $key) {
            $data[$key->empID] = $key->empID ." - ". $key->fullname;
        }

        # if head is not included on the list
        if(!(array_key_exists($empID, $data))){
            $getInfo = $this->db->query("SELECT employeeid AS empID, CONCAT(lname, ', ', fname, ' ', mname) AS fullname FROM employee WHERE employeeid='$empID'");
            $key = $getInfo->row()->empID;
            $val = $getInfo->row()->fullname;
            $data[$key] = $key ." - ".$val;
        }
        
        return $data;
    }
    # end for ica-hyperion 21152

    # for ica-hyperion 21194
    # justin (with e)
    function findIfAdmin($empid){
        #return  "SELECT * FROM user_info WHERE username='$empid' AND `type` LIKE '%admin%';"; die;
        $query = $this->db->query("SELECT * FROM user_info WHERE username='$empid' AND `type` LIKE '%admin%';")->result();
        if(count($query) > 0)
            return true;
        else 
            return false;
    }
    # justin (with e)

    function getAdminInfo($username){
        $query = $this->db->query("SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) AS fullname FROM user_info WHERE username='$username'; ");
        if ($query->num_rows > 0) return $query->row()->fullname;
        else return false;
    }

    #get all clustertype
    function loadclustertype()
    {
        $return = '';
        $query = $this->db->query("SELECT code,description FROM code_type")->result();
            foreach ($query as $key) {
                $return .= "<option value='$key->code'>".$key->description."</option>";
            }
        return $return;
    }
    #Added by Glen Mark
    //get campus description 
    function getCampusDescription()
    {
        $return = array();
        $query = $this->db->query("SELECT code,description FROM code_campus ORDER BY code");
        foreach ($query->result() as $row) {
            $return[Globals::_e($row->code)] = Globals::_e($row->description);
        }
        return $return;
    }
    #Added by Glen Mark
     //get department description 
    function getDeptDescription()
    {
        $return = array();
        $query = $this->db->query("SELECT code,description FROM code_office ORDER BY code");
        foreach ($query->result() as $row) {
            $return[$row->code] = $row->description;
        }
        return $return;
    }
     #Added by Glen Mark
     //get EmployeeType description 
    function getEmpTypeDescription()
    {
        $return = array();
        $query = $this->db->query("SELECT code,description FROM code_campus ORDER BY code");
        foreach ($query->result() as $row) {
            $return[$row->code] = $row->description;
        }
        return $return;
    }

    function getPayrollCutoff($cutoffstart, $cutoffto){
        $cutoffid = $this->db->query("SELECT ID FROM cutoff WHERE CutoffFrom = '$cutoffstart' AND CutoffTo = '$cutoffto' ")->row()->ID;
        $query = $this->db->query("SELECT * FROM payroll_cutoff_config WHERE baseid = '$cutoffid' ")->result_array();
        return $query;
    }

    function getEmployeeTeachingType($employeeid){
        $type = "teaching";
        $query = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ");
        if($query->num_rows() > 0) return $query = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ")->row()->teachingtype;
        return $type;
    }

    function getAllEmployeeList(){
        $query = $this->db->query("SELECT * FROM employee");
        return $query->result_array();
    }

    function getAMSLink($menu_id='150'){
        $link = "";

        $q_link = $this->db->query("SELECT link FROM menus WHERE menu_id='$menu_id'")->result();
        foreach ($q_link as $row) $link = $row->link;

        return $link;
    }

    function getEmploymentStatus($empid){
        $query = $this->db->query("SELECT employmentstat FROM employee WHERE employeeid = '$empid' ");
        if($query->num_rows > 0) return $query->row()->employmentstat;
        else return "REG";
    }

    function isConsecutive($date){
        $date = explode("/", $date);
        $each_date = array();
        foreach ($date as $key => $value) {
            $each_date[] = substr($value, 3);
        }
        $last_data = '';
        $sequence = '';
        $isconsec = '';

        foreach($each_date as $key => $row){
            if(!$last_data){
                $last_data = $row;
                $sequence +=1;
            }else{
                if($last_data + 1 == $row){
                   $sequence += 1;
                   if($last_data >= 3) $isconsec += 1; 
                   $last_data = $row;
                }
                else{
                   $sequence = 0;
                   $sequence += 1;
                   $last_data = $row;
                }
            }
        }   
        if($isconsec > 0) return true;  
    }

    function getFacial($facial = "") {
        $return = "<option value=''>Select Device</option>";
        $query = $this->db->query("SELECT * FROM facial_devices")->result();
        foreach ($query as $key) {
            if($facial == $key->serial_number) $return .= "<option value='$key->serial_number' selected>$key->name</option>";
            else $return .= "<option value='$key->serial_number'>$key->name</option>";
        }
        return $return;
    }

    function constructArrayListFromComputedTable($str=''){
        $arr = array();
        if($str){
            $str_arr = explode('/', $str);
            if(count($str_arr)){
                foreach ($str_arr as $i_temp) {
                    $str_arr_temp = explode('=', $i_temp);
                    if(isset($str_arr_temp[0]) && isset($str_arr_temp[1])){
                        $arr[$str_arr_temp[0]] = $str_arr_temp[1];
                    }
                }
            }
        }
        return $arr;
    }

    function getExistingData($userid="", $table="", $column=""){
        return $this->db->query("SELECT $column FROM $table WHERE employeeid = '$userid'")->result_array();
    }

    function getEmergencyType($tbl_id=''){
        return $this->db->query("SELECT * FROM applicant_emergencyContact where id='$tbl_id'")->row()->type;
    }

    function isComplete($tbl_id='', $tbl){
        $check = $this->db->query("SELECT * FROM $tbl where id='$tbl_id'");
        if($check->num_rows() > 0) return $check->row()->completed;
        else return 0;
    }

     function checkifCodeExist($code, $table, $holiday_type=''){
        $where = '';
        $num = ($holiday_type) ? 1 : 0; 
        if(in_array($table, array('code_type','code_holidays','code_status', 'code_disciplinary_action_sanction', 'code_disciplinary_action_offense_type'))) $where = 'code';
        else $where = 'holiday_code';
        $query = $this->db->query("SELECT * FROM $table WHERE $where = ".$this->db->escape($code)." ");
        if($query->num_rows() > $num) return 0;
        else return 1;
    }

    public function getEmployeeList(){
        $this->load->model('Setup');
        $code = $this->input->post('code');
        $yearlevel_records = $this->setup->getYearLevelList($code);
        $option = "<option value=''> - Select a year level - </option>";
        foreach($yearlevel_records as $value){
            $option .= "<option value='". $value['id'] ."'>". $value['description'] ."</option>";
        }

        echo $option;
    }

    public function loadChildrendata($employeeid, $table){
        return $this->db->query("SELECT * FROM $table WHERE employeeid = '$employeeid' ORDER BY birthdate ASC")->result();
    }

    public function getReason($id=''){
        $query = $this->db->query("SELECT reason from employee_employment_status_history where id ='$id'");
        if($query->num_rows() > 0) return Globals::_e($query->row()->reason);
        else return "No reason indicated.";
    }

    function updateLockedHistory($stat,$key, $username=""){
        if($username) return $this->db->query("UPDATE lock_account_history SET status='$stat' WHERE userid='$username' AND `status`='SENT'");
        else return $this->db->query("UPDATE lock_account_history SET status='$stat' WHERE key='$key'");
    }

    function readPastRequest($username){
        return $this->db->query("UPDATE lock_account_history SET status='READ' WHERE userid='$username' AND `status`='SENT'");
    }

    function updateUserLockedStat($username,$stat){
        return $this->db->query("UPDATE user_info SET locked='$stat' WHERE username='$username'");
    }

    function insertLoginTrails($data){
        return $this->db->insert("login_attempts_hris", $data);
    }

    function insertRequestTrails($data){
        return $this->db->insert("lock_account_history", $data);
    }

    function insertLockUnlockData($data){
        return $this->db->insert("lock_unlock_account", $data);
    }

    function getUnlockStatus($key=''){
        return $this->db->query("SELECT `status` FROM lock_account_history where `key` ='$key'")->row()->status;
    }

    function getUnlockUser($key=''){
        return $this->db->query("SELECT userid FROM lock_account_history where `key` ='$key'")->row()->userid;
    }

    function getUnlockTimeRequest($key=''){
        return $this->db->query("SELECT `timestamp` FROM lock_account_history where `key` ='$key'")->row()->timestamp;
    }

    function getAccountStatus($userid=''){
        return $this->db->query("SELECT locked FROM user_info where username = '$userid'")->row()->locked;
    }

    function getUserId($username){
        $query = $this->db->query("SELECT id FROM user_info WHERE username='$username'; ");
        return $query->row()->id;
    }

    function getUserLockedStat($username){
        $query = $this->db->query("SELECT locked FROM user_info WHERE username='$username'");
        if ($query->num_rows == 0) {
            $empID = "";
           $q_user = $this->db->query("SELECT employeeid FROM employee WHERE email = '$username' OR personal_email = '$username' ");
            if($q_user->num_rows > 0) $empID = $q_user->row()->employeeid;
            $query = $this->db->query("SELECT locked FROM user_info WHERE username='$empID'");
        }
        if ($query->num_rows == 0) return "";
        else return $query->row()->locked;     
    }

    function saveLeaveToHistory($data){
        $this->db->insert("employee_leave_credit_history", $data);
    }

    function insertLeaveData($data){
        $this->db->insert("employee_leave_credit", $data);
    }

    public function updateLeaveData($data, $ID){
        $this->db->where("`id = '$ID'");
        $q_save_civil_status = $this->db->update('employee_leave_credit', $data);
        return $q_save_civil_status;
    }

    function getEmployeeDataRecountLeave(){
        $query = $this->db->query("SELECT employeeid,dateemployed,teachingtype,employmentstat FROM employee WHERE employmentstat = 'PER'")->result();
        return $query;
    }

    function getLeaveCredit($employeeid,$leavetype){
        return $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid = '$employeeid' AND leavetype = '$leavetype'")->result();
    }

    function getEarnedServiceCredit($absent){
        $query = $this->db->query("SELECT earned FROM credits_earn_computation WHERE absent='$absent'");
        return $query->row()->earned;
    }

    public function recountAvailedLeave($empid, $leavetype, $dfrom, $dto){
        $q = $this->db->query("SELECT SUM(b.nodays) AS total FROM leave_app_emplist a LEFT JOIN leave_app_base b ON b.id = a.base_id WHERE a.employeeid = '$empid' AND b.type = '$leavetype' AND b.datefrom BETWEEN '$dfrom' AND '$dto' AND b.dateto BETWEEN '$dfrom' AND '$dto' AND a.`status` = 'APPROVED' AND paid = 'YES'");
        if($q->row(0)->total){
            return $q->row(0)->total;
        }
        else{
            return 0;
        }
    }


    public function getLeaveGiven($empid, $leavetype){
        $q = $this->db->query("SELECT TIMESTAMPDIFF(YEAR, dateemployed, CURDATE()) AS difference, teachingtype, employmentstat FROM employee WHERE employeeid = '$empid'");

        if($q->row(0)->teachingtype != "" && $q->row(0)->employmentstat != ""){
            // $yearEmployment = $q->row(0)->difference;
            $empStat = $q->row(0)->employmentstat;
            $teachingtype = $q->row(0)->teachingtype;
            $getElegibilityCount = $this->db->query("SELECT credit, credit_non FROM `code_request_eligibility_period` WHERE emp_status = '$empStat' AND code_request = '$leavetype'");
            if($getElegibilityCount->num_rows() > 0){
                if ($teachingtype == "teaching") {
                    return $getElegibilityCount->row(0)->credit;
                }else{
                    return $getElegibilityCount->row(0)->credit_non;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function duplicateCheckerSequence($code = "", $table = "", $column = "", $idUpdater = "", $idUpdateCollumn = ""){
        $isExist = false;
        $this->db->select('*');
        if ($code && $column) $this->db->where($column, $code);
        if($idUpdater && $idUpdateCollumn) $this->db->where($idUpdateCollumn." !=", $idUpdater);
        $q_checker = $this->db->get($table)->result_array();
        foreach ($q_checker as $row) $isExist = true;

        if ($isExist) return $q_checker;
        else return $isExist;
    }

    function updateTableData($data, $table, $id = "", $column = ""){
        if($id && $column) $this->db->where($column, $id);
        return $this->db->update($table, $data);
    }

    function saveDataTable($data, $table){
        return $this->db->insert($table, $data);
    }

    public function deleteData($table, $column, $id){
        $this->db->where($column, $id);
        return $this->db->delete($table);
    }

    function deleteTableData($table, $id, $column = ""){
        $this->db->where($column, $id);
        return $this->db->delete($table);
    }

    function getTableData($table, $id = "", $column = ""){
        if($id && $column) $this->db->where($column, $id);
        return $this->db->get($table)->result_array();
    }

    function getEmployeeDataRecountLeavePastYear($id = ""){
        $wh = "";
        if ($id) $wh = "AND employeeid = '$id'";
        $query = $this->db->query("SELECT employeeid, dateemployed, teachingtype, employmentstat, SUBSTR(dateemployed, 5, 8) AS startDate FROM employee WHERE isactive = 1 AND (dateresigned IS NULL OR dateresigned = '0000-00-00' OR dateresigned = '1970-01-01') $wh")->result();
        return $query;
    }

    function leaveCreditSetupData($code = "", $yearService = "", $teachingtype = "", $employmentstat = ""){
        $value = "";
        $type = "";
        $leaveType = $teachingtype."Type";
        $data = $this->db->query("SELECT credits, period_type FROM code_request_leave_setup WHERE $yearService BETWEEN `from` AND `to` AND `code` = '$code' AND emp_type = '$employmentstat' AND teaching_type = '$teachingtype'")->result_array();
        if (empty($data)) {
            $leaveGreater = $this->db->query("SELECT credits, period_type FROM code_request_leave_setup WHERE `code` = '$code' AND emp_type = '$employmentstat' AND teaching_type = '$teachingtype' order by `to` DESC limit 1")->result_array();
            if (empty($leaveGreater)) {
                $value = "NoSetup";
            }else{
                foreach ($leaveGreater as $row) {
                    $value = $row['credits'];
                    $type = $row['period_type'];
                }
            }
        }else{
            foreach ($data as $row){
                $value = $row['credits'];
                $type = $row['period_type'];
            } 
        }
        return $value."/".$type;
    }

    function deleteLeaveData($id = ""){
        $this->db->query("DELETE FROM employee_leave_credit WHERE id ='$id'");
    }

    function updateLeaveCredit($id, $table, $data){
        $this->db->where('id', $id);
        return $this->db->update($table, $data);
    }
    
}
 
/* End of file extras.php */
/* Location: ./application/models/extras.php */





