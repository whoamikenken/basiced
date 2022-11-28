<?php
ini_set('max_execution_time', 0);
ini_set("memory_limit", "2G");


if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Backup_ extends CI_Controller{

    function backup_tables() {
        $this->load->helper('file');
        $this->load->library('zip');

        $dbDatabase = $this->db->database;
        $dbTableBackup = $this->db->tablebackup;
        $refreshToken = $this->db->refresh_token;
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
          CURLOPT_POSTFIELDS => 'client_id=123369233973-4u9u32pdnpfnl6fdgm89iudlqfgu1u7f.apps.googleusercontent.com&client_secret=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&refresh_token='.$refreshToken.'&grant_type=refresh_token',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
          ),
        ));

        $respond = curl_exec($curl);

        curl_close($curl);

        $getRefreshToken = json_decode($respond);
        $token = $getRefreshToken->access_token;
        $folderID = $this->createFolder($dbDatabase."-".date("Y-m-d"), $token);
        // echo "folderID";
        // echo "<pre>";print_r(dirname($_SERVER['PHP_SELF']) . '/../files/');die;
        $link = mysqli_connect($this->db->hostname,$this->db->username,$this->db->password, $this->db->database);
        $tables = $dbTableBackup;
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

            $fileDirectory = APPPATH.'uploads/db/'.$table.'.zip';
            $filename = $table.'.sql';
            $filenameZip = $table.'.zip';

            $this->zip->add_data($filename, $return);

            $this->zip->archive($fileDirectory);

            // echo system('zip -P pass '.$filenameZip.' '.$return.' ');
            // die;
            $this->uploadTableDump($fileDirectory, $token, $filenameZip, $folderID);
        }

        $this->reprocessFacialLogs();
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
             // echo "cURL Error #:" . $resp;
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

    public function reprocessFacialLogs(){
        $this->load->model("facial");
        $yesterdayDate = date('Y-m-d',strtotime("-1 days"));
        $process = $this->facial->reprocessFacialLogs($yesterdayDate, $yesterdayDate);
        // echo 'processed:'.$process;
    }

}