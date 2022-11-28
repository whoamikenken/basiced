<?php
ini_set('max_execution_time', 0);
ini_set("memory_limit", "2G");


if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test_ extends CI_Controller{

    function getMySQLVersion() { 
      $output = shell_exec('mysql -V'); 
      preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
      echo $version[0]; 
    }

    public function getPHPversion(){
        phpinfo();
    }

    function backup_tables() {
        $this->load->helper('file');
        $this->load->library('zip');

        $dbDatabase = $this->db->database;
        // Get Refresg Token
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://oauth2.googleapis.com/token',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'client_id=653027973997-gnjpa9ifvq7bknhlga6mi8o08da9j00f.apps.googleusercontent.com&client_secret=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&refresh_token=1%2F%2F0ezlE27ID4BqvCgYIARAAGA4SNwF-L9IrTACH_ARpfExE2Nl-HMaB9x4i9bjksfAP9ivfnIexNGtC4gMExecuMDKHzCDpaa1amFM&grant_type=refresh_token',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
          ),
        ));

        $respond = curl_exec($curl);

        curl_close($curl);

        $getRefreshToken = json_decode($respond);
        $token = $getRefreshToken->access_token;

        $folderID = $this->createFolder("Poveda-".date("Y-m-d"), $token);
        // echo "folderID";
        // echo "<pre>";print_r(dirname($_SERVER['PHP_SELF']) . '/../files/');die;
        $link = mysqli_connect($this->db->hostname,$this->db->username,$this->db->password, $this->db->database);
        $tables = "*";
        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit;
        }
        
        mysqli_query($link, "SET NAMES 'utf8'");

        //get all of the tables
        if($tables == '*')
        {

            $tables = array();
            $result = mysqli_query($link, 'SHOW TABLES');

            while($row = mysqli_fetch_row($result))
            {
                $tables[] = $row[0];
            }
        }
        else
        {
            $tables = is_array($tables) ? $tables : explode(',',$tables);
        }

        // echo "<pre>";print_r($tables);die;
        //cycle through
        foreach($tables as $table)
        {
            $return = "";
            $this->zip->clear_data();

            $result = mysqli_query($link, 'SELECT * FROM `'.$table.'`');
            $num_fields = mysqli_num_fields($result);
            $num_rows = mysqli_num_rows($result);

            $return.= 'DROP TABLE IF EXISTS `'.$table.'`;';
            $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE `'.$table.'`'));
            $return.= "\n\n".$row2[1].";\n\n";
            $counter = 1;

            //Over tables
            for ($i = 0; $i < $num_fields; $i++) 
            {   //Over rows
                while($row = mysqli_fetch_row($result))
                {   
                    if($counter == 1){
                        $return.= 'INSERT INTO '.$table.' VALUES(';
                    } else{
                        $return.= '(';
                    }

                    //Over fields
                    for($j=0; $j<$num_fields; $j++) 
                    {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n","\\n",$row[$j]);
                        if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                        if ($j<($num_fields-1)) { $return.= ','; }
                    }

                    if($num_rows == $counter){
                        $return.= ");\n";
                    } else{
                        $return.= "),\n";
                    }
                    ++$counter;
                }
            }
            $return.="\n\n\n";

            $fileDirectory = APPPATH.'uploads/'.$table.'.zip';
            $filename = $table.'.sql';
            $filenameZip = $table.'.zip';

            $this->zip->add_data($filename, $return);

            $this->zip->archive($fileDirectory);

            $this->uploadTableDump($fileDirectory, $token, $filenameZip, $folderID);

        }
    }

    public function uploadTableDump($fileDirectory='', $token, $filenameZip, $folderID)
    {
        $this->load->helper('file');
        $url = "https://www.googleapis.com/upload/drive/v3/files";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "Authorization: Bearer ". $token,
           "Accept: application/json",
           "Content-Type: application/json",
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        

        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($fileDirectory));

        //for debug only!
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
             echo "cURL Error #:" . $resp;
            $return = json_decode($resp);
            
            $fileID = $return->id;
            // sleep(5);
            
            $url = "https://www.googleapis.com/drive/v3/files/".$fileID."?addParents=".$folderID;
            // var_dump($url);
            // die;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
               "Authorization: Bearer ".$token,
               "Accept: application/json",
               "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = '{"name":"'.$filenameZip.'"}';

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);
            // var_dump($resp);

            unlink($fileDirectory);
        }
    }

    public function createFolder($fileName = "test", $token){

        $gdriveFolderParent = $this->db->GDriveFolder;

        $url = "https://www.googleapis.com/drive/v3/files";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "Authorization: Bearer ".$token,
           "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = '{"name": "'.$fileName.'", "mimeType": "application/vnd.google-apps.folder"}';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);

        $return = json_decode($resp);
        // echo print_r($return);die;
        $fileID = $return->id;

        $url = "https://www.googleapis.com/drive/v3/files/".$fileID."?addParents=".$gdriveFolderParent;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
           "Authorization: Bearer ".$token,
           "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $data = '{"name": "'.$fileName.'", "mimeType": "application/vnd.google-apps.folder"}';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        curl_close($curl);

        $ret = json_decode($response);
        return $ret->id;
    }

    public function index(){
        // $zip = new ZipArchive();
        // $zip_status = $zip->open("application/uploads/code_type_history.zip");
        // $file = file_get_contents(APPPATH.'uploads/Capture2.PNG');

         // echo system('zip -P pass application/uploads/dwawa.zip');
        echo $this->extensions->getDeparmentDescriptionReport("GS");
    }


    public function revertLeaveCreditsFromCodeLeaveSetup(){
        $setupData = $this->db->query("SELECT * FROM code_leave_setup WHERE dfrom = '2022-07-01' AND dto = '2023-06-30' ")->result_array();
        $processType = 0;
        $processEmployeeData = 0;
        foreach ($setupData as $row => $val) {

            $processType++;
            $empStat = "";
            foreach (explode(',', $val['employmentStatus']) as $rw => $values) {
                $empStat .= "'".$values."',";
            }
            $empStat = rtrim($empStat, ",");

            $leavetype = $val['leavetype'];
            $record = $this->db->query("SELECT * FROM employee_leave_credit_history WHERE dfrom = '2021-06-15' AND dto = '2022-06-30' AND employmentstat IN ($empStat) AND leavetype = '$leavetype' ")->result_array();
            foreach ($record as $key => $value) {
                $processEmployeeData++;
                $id = $value['employeeid'];
                $oldIDRecord = $value['id'];
                // Old Data
                $oldLeave = array();
                $oldLeave = $value;
                unset($oldLeave['id']);

                // delete current leave setup

                $this->db->query("DELETE FROM employee_leave_credit WHERE employeeid = '$id' AND dfrom = '2022-07-01' AND dto = '2023-06-30' AND employmentstat IN ($empStat) AND leavetype = '$leavetype'");

                // INSERT OLD employee credits
                $this->db->insert("employee_leave_credit", $oldLeave);

                $this->db->query("DELETE FROM employee_leave_credit_history WHERE id = '$oldIDRecord'");

            }
        }

        $this->db->query("DELETE FROM code_leave_setup WHERE dfrom = '2022-07-01' AND dto = '2023-06-30'");

        echo "Process leave type:".$processType;
        echo "Process Employee data:".$processEmployeeData;
    }

    public function testFacial(){
        $curl = curl_init();
        $data = $this->input->post();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://200.1.0.98:8090/findRecords?pass=12345678&personId=-1&length=-1&index=0&startTime=2021-03-04%2001:00:00&endTime=2022-07-06%2011:59:00&model=0',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

    function updateSupid(){
        $result = $this->db->query("SELECT * FROM `Athena_HSCI2`.SUPPLIER ")->result();
        $start = 99;
        $Starter = "S".date("Y");
        foreach ($result as $key => $value) {
            $updated = array();
            $randomizedCode = $Starter."-".$start;
            $updated['SUPPID'] = $randomizedCode;
            echo "<pre>";print_r($updated);
            $start++;
        }

        die;
    }
    
    public function generateRamdomCode($Starter = '', $table = ""){
        $start = 99;
        for ($i=0; $i < 1000 ; $i++) { 
          if ($Starter) {
            $start++;
            $randomizedCode = $Starter."-".$start;
          }
          $query = $this->db->query("SELECT * FROM $table WHERE code = '$randomizedCode'");
          if($query->num_rows() == 0) return $randomizedCode;
        }
    }
}