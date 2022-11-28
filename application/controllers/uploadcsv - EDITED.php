<?php 

/**
 * @author Justin
 * @copyright 2016
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Uploadcsv extends CI_Controller {

    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
    }

    public function import()
        { 
          if(isset($_POST["Importsched"]))
            {
                  $filename=$_FILES["file"]["tmp_name"];
                  if($_FILES["file"]["size"] > 0)
                      {
                        $file = fopen($filename, "r");
                        $x = $ins = 0;
                         while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
                         {
                            $x++;
                            if($x != 1){
                                $data = array(
                                    'employeeid' => str_replace("`","",$emapData[0]),
                                    'dayofweek' => $this->timesheet->dow($emapData[1]),
                                    'idx' => $this->timesheet->dow($emapData[1],true),
                                    'starttime' => $emapData[2],
                                    'endtime' => $emapData[3],
                                    'leclab' => $emapData[4],
                                    'dateactive' => date("Y-m-d H:i:s")                      
                                    );
                            $inserted = $this->timesheet->csvsched($data);
                            
                                $sdata = array(
                                    'employeeid' => str_replace("`","",$emapData[0]),
                                    'dayofweek' => $this->timesheet->dow($emapData[1]),
                                    'idx' => $this->timesheet->dow($emapData[1],true),
                                    'starttime' => $emapData[2],
                                    'endtime' => $emapData[3],
                                    'leclab' => $emapData[4]               
                                    );
                                $this->timesheet->csvsched($sdata,true);
                            
                            $ins += $inserted;
                            }
                         }
                        fclose($file);
                        $data["message"] = "Your file was successfully uploaded!. <br />$ins Data Inserted!.";
                        $this->load->view('process/csvuploaded',$data);
                      }
             }
          // if(isset($_POST['Import']))
             // {
                // $filename=$_FILES["file"]["tmp_name"];
                    // if($_FILES["file"]["size"] > 0)
                      // {
                        // $file = fopen($filename, "r");
                        // $x = $ins = 0;
                        
                         // while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
                         // {
                            // $x++;
                            // if($this->timesheet->checkemp($emapData[0])){
                                // if($x != 1){
                                // $timein  = ($emapData[1] ? date("Y-m-d",strtotime($emapData[1]))." ".date("H:i:s",strtotime($emapData[2])) : "");
                                // $timeout = ($emapData[1] ? date("Y-m-d",strtotime($emapData[1]))." ".date("H:i:s",strtotime($emapData[3])) : "");
                                    // $data = array(
                                        // 'userid' => $emapData[0],
                                        // 'timein' => $timein,
                                        // 'timeout' => $timeout
                                        // );
                                // $inserted = $this->timesheet->csvatt($data);
                                // $ins += $inserted;
                                // }
                            // }                            
                         // }
                        // fclose($file);
                        // $data["message"] = "Your file was successfully uploaded!. <br />$ins Data Inserted!.";
                        // $this->load->view('process/csvuploaded',$data);
                      // }
             // }
			 
			if(isset($_POST['Import']))
            {
				$filename=$_FILES["file"]["tmp_name"];
                if($_FILES["file"]["size"] > 0)
				{
					$file = fopen($filename, "r");
                    $x = $ins = 0;
					$empID = "";
					$user = $this->session->userdata("username");
                    while (($emapData = fgetcsv($file, 10000, ",")) !== FALSE)
                    {
						$x++;
						if($x != 1)
						{
							if($emapData[1])
							{
								$empID = $emapData[1];
								$date = $emapData[2];
							}
							else
							{
								if(!$emapData[4])
								{
									$empID = "";
									$date = "";
								}
							}
							
							if($this->timesheet->checkemp($empID))
							{
								$timein  = ($date ? date("Y-m-d",strtotime($date))." ".date("H:i:s",strtotime($emapData[4])) : "");
                                $timeout = ($date ? date("Y-m-d",strtotime($date))." ".date("H:i:s",strtotime($emapData[6])) : "");
                                $data = array(
                                        'userid' => $empID,
                                        'timein' => $timein,
                                        'timeout' => $timeout
                                        );
                                $inserted = $this->timesheet->csvatt($data);
                                $ins += $inserted;
								
								$newData = array(
										'userid' => $empID,
										'timein' => $timein,
										'timein_machine' => $emapData[5],
										'timeout' => $timeout,
										'timeout_machine' => $emapData[7],
										'addedby' => $user
										);
								$this->timesheet->csvUploaded($newData);
							}
						}
					}	
					fclose($file);
                    $data["message"] = "Your file was successfully uploaded!. <br />$ins Data Inserted!.";
                    $this->load->view('process/csvuploaded',$data);
				}
			}
        }


}