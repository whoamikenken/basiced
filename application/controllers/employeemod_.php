<?php
/**
 * @author Justin
 * @copyright 2016
 */
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employeemod_ extends CI_Controller {

  function __construct(){
      parent::__construct();
      if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
  }

	public function index(){
        echo "Oooppss.! This page found some problem..";
	}

    /*
     * Load Views
     */
    function fileconfig(){
      $data     = $this->input->post();
      $folder   = isset($data["toks"]) ? $this->gibberish->decrypt($this->input->post("folder"), $data["toks"]) : $this->input->post("folder");
      $view   = isset($data["toks"]) ? $this->gibberish->decrypt($this->input->post("view"), $data["toks"]) : $this->input->post("view");
      // echo "<pre>";print_r($view);die;
      $this->load->view("$folder/$view",$data);
    }


    /*
     *  Load Model Function
     */
    function loadmodelfunc(){
        $toks = $this->input->post("toks");
        $data   = $this->input->post();
        if($toks){
          unset($data["toks"]);
          foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
          }
        }
        $model  = $toks ?  $this->gibberish->decrypt($this->input->post('model'), $toks) : $this->input->post("model");
        echo $this->employeemod->$model($data);
        // echo "<pre>"; print_r($this->employeemod->$model($data)); die;
    }

    function applicantlist(){
      $data['applicantlist'] = $this->employeemod->applicantlist();
      // echo "<pre>"; print_r($this->db->last_query()); die;
      echo $this->load->view("employeemod/applicant_list", $data);
    }

    function applicantlisthistory(){
      $data['applicantlist'] = $this->employeemod->applicantlisthistory();
      // echo '<pre>'; print_r($this->db->last_query());die;
      echo $this->load->view("employeemod/applicant_list_history", $data);
    }

    function applicantlistforviewing(){
      $data['applicantlist'] = $this->employeemod->applicantlistforviewing();
      echo $this->load->view("employeemod/applicant_list_for_viewing", $data);
    }


    ///< @Angelica 100417 - binago ko na to (process_\saveEmployeeAttendanceSummary)
    ///< Payroll report - Computation for employee attendance summary per cutoff.

    /*
    * Title   : New function for payroll report
    * Author  : Justin (with e)
    * Date    : 09/04/2017
    */
    function displayedPayrollReport(){
      $dex = explode(",",$this->input->post("cdate"));
      $from_date = $dex[0];
      $to_date = $dex[1];
      $tnt = $this->input->post("tnt");
      $job = $this->input->post("job");

      // employee who confirmed, but need to unconfirmed.
      if($job == "unconfirmed"){
        $result = $this->attendance->emp_confirmed($from_date, $to_date, $tnt);
        if (count($result) > 0) {

          foreach ($result as $key => $r) {
            $data = array(
                            'empid' => $r["qEmpId"],
                            'dfrom' => $from_date,
                            'dto'   => $to_date,
                            'tnt'   => $tnt
                         );
            $this->employeemod->hrunconfirmatt($data);
          } // end of foreach
        } // end if
        $return = array();

        $result = $this->attendance->emp_not_yet_confirmed($from_date, $to_date, $tnt);
        foreach ($result as $key => $r) {
          $return[1] = isset($return[1])?$return[1].", ".$r["qEmpId"]:$r["qEmpId"];
        }
        $return[0] =  "No. of employee who need to confirmed 0/".count($result);
        echo json_encode($return);
      }
      // end of employee who confirmed, but need to unconfirmed.

      // employee who unconfirmed, but need to confirmed
      if($job == "confirmed"){
        $data = array(
                      'empid' => $this->input->post("id"),
                      'dfrom' => $from_date,
                      'dto'   => $to_date,
                      'tnt'   => $tnt
                    );
        $this->employeemod->hrconfirmatt($data);
        echo "OK";
      }
      // end of employee who unconfirmed, but need to confirmed
    }
    /*
    * End of new function for payroll report
    */


    // for saving Official Business Apply
    // author : justin (with e)
    function saveOffBusinessApply(){
      $data   = $this->input->post();
      $model  = $this->input->post("model");
      $ret = $this->employeemod->$model($data);
      echo json_encode($ret);
    }
    // end of saving official  business apply


    #added functions @author Angelica

    /**
    *  Save seminar application details and upload invitation if any.
    *
    * @return string
    */

    function saveSeminarApply(){
        $data   = $this->input->post("form_data");
        $post_data = array();

        foreach (json_decode($data) as $key) {
          $post_data[$key->name] = $key->value;
        }
        // var_dump($_FILES['filess']['type']);die;

        $model  = $this->input->post("model");
        $model = $post_data['model'];
        $model = "applySeminarWithSequenceNew";
        $save_ret =  $this->employeemod->$model($post_data); ///< Save application details

        if(isset($save_ret)){
          if($save_ret['err_code'] == 0 && isset($_FILES['filess'])){
            $fileName = $_FILES['filess']['name'];
            $tmpName  = $_FILES['filess']['tmp_name'];
            $fileSize = $_FILES['filess']['size'];
            $fileType = $_FILES['filess']['type'];
            if(isset($save_ret['base_id']) && $_FILES['filess']['error'] == 0 && $fileSize > 0 && $fileSize <= 100000){
                  #image/jpeg, image/png, application/pdf
                  if(in_array($fileType, array("image/jpeg","image/png","application/pdf"))) {
                     $fp      = fopen($tmpName, 'r');
                                  $content = fread($fp, filesize($tmpName));
                                  $content = addslashes($content);
                                  fclose($fp);
                    $this->db->query("INSERT into seminar_app_attach_invitation (base_id,file_name,content,file_type) values ('{$save_ret['base_id']}','$fileName','$content','$fileType')"); ///< upload invitation
                  }
            }
          }
        }

        echo $save_ret["msg"];
    }



	   //Added 6-8-17 VERIFY PASSWORD FOR PAYSLIP
    function verifyPayslipPassword(){
      $toks = $this->input->post("toks");
      $username     = $this->session->userdata('username');
      $password     = $toks ? $this->gibberish->decrypt($this->input->post('password'), $toks) : $this->input->post('password');
      echo $this->employeemod->verifyPayslipPassword($username,$password);

    }

    function checkWebSetupStatus($id){
        $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$id' AND STATUS = 'active'");
        if($weblogin->num_rows() > 0) return $weblogin->row()->status;
        else return false;
    }

    // for time in & out
    // justin (with e)
    function displayedTITO(){
      # for ica-hyperion 21194 & 21196
      # by justin (with e)
      # > para sa admin management
      $toks = $this->input->post("toks");
      $user_id = ($this->input->post("empID") != "") ? $this->gibberish->decrypt($this->input->post("empID"), $toks) : $this->session->userdata("username");
      # end for ica-hyperion 21194 & 21196

      $checker = $this->checkWebSetupStatus($user_id);

      $ldate = ($toks) ? $this->gibberish->decrypt($this->input->post("ldate"), $toks) : $this->input->post("ldate");
      $ltype = ($toks) ? $this->gibberish->decrypt($this->input->post("ltype"), $toks) : $this->input->post("ltype");
      $wc = '';
      if($ltype == 'Correction') $wc = " AND userid NOT IN (SELECT b.employeeid FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE ('$ldate' BETWEEN a.datefrom AND a.dateto) AND a.isHalfDay = '1')";
      $results = array();
      $result1 = $this->db->query("SELECT * FROM timesheet WHERE userid='{$user_id}' AND (timein LIKE '%{$ldate}%' OR timeout LIKE '%{$ldate}%') $wc ")->result();
      // $result2 = $this->db->query("SELECT '' AS timeid, timein, '0000-00-00 00:00:00' AS timeout FROM timesheet_history WHERE userid='{$user_id}' AND timein LIKE '%{$ldate}%'")->result();
      #echo "<pre>". "SELECT '' AS timeid, localtimein AS timein, '0000-00-00 00:00:00' AS timeout FROM timesheet_trail WHERE userid='{$user_id}' AND localtimein LIKE '%{$ldate}%'";
      $result2 = $this->db->query("SELECT '' AS timeid, localtimein AS timein, '0000-00-00 00:00:00' AS timeout FROM timesheet_trail WHERE userid='{$user_id}' AND localtimein LIKE '%{$ldate}%' $wc")->result();
      if (count($result2) == 0) {
        $result2 = $this->db->query("SELECT '' AS timeid, localtimein AS timein, '0000-00-00 00:00:00' AS timeout FROM webcheckin_history WHERE userid='{$user_id}' AND localtimein LIKE '%{$ldate}%'")->result();
      }
      
      // echo "<pre>";print_r($this->db->last_query());die;
      if(count($result1)){
         $results = array_merge($results, $result1);
      }else{
        if(count($result2)){
           $results = array_merge($results, $result2);
        }
      }

      # for ica-hyperion 21194 & 21196
      # by justin (with e)
      # > minodified ko muna ito, para sa timesheet_trail.. wala kasing row id ng time record..
      if(count($results) > 0){
        # > para sa row id...
        $i = 1;

        foreach ($results as $res) {
            # > papalitan ko lahat ng $res->timeid sa $rowId. may condition na kapag walang time id, mag gegenerate ng row id..
            $rowId = ($res->timeid != "") ? $res->timeid : "TT-".$i;

            echo "<tr class=\"remove\" id=\"TR-".$rowId."\">";
            $from_time = strtotime($res->timein);
            $to_time = strtotime($res->timeout);
            echo "<td id=\"AT-".$rowId."\" style=\"text-align: center\">".(strtoupper(date("h:i a",strtotime($res->timein)))=="12:00 AM"?"--:-- --":strtoupper(date("h:i a",strtotime($res->timein))))." - ".(strtoupper(date("h:i a",strtotime($res->timeout)))=="12:00 AM"?"--:-- --":strtoupper(date("h:i a",strtotime($res->timeout))))."</td>";
            echo "<td id=\"RT-".$rowId."\" style=\"text-align: center\"></td>";
            echo "<td id=\"ST-".$rowId."\" style=\"text-align: center\"></td>";
            #echo "<td id=\"id-".$res->timeid."\" style=\"text-align: center\" hidden>".$res->timeid."</td>";
            $hidden = round(abs($to_time - $from_time) / 60,2);
            echo "<td style=\"text-align: center\">";
            // if($hidden <= 1 || $from_time > $to_time || strtoupper(date("h:i a",strtotime($res->timein)))=="12:00 AM"){
              echo "<a class=\"btn blue\" id='edit'  code=\"".$rowId."\"><i class=\"icon glyphicon glyphicon-edit\"></i></a>";
              echo "<a class=\"btn blue\" id='remove'  code=\"".$rowId."\"><i class=\"icon glyphicon glyphicon-trash\"></i></a>";
            // }
            echo "</td>";
            echo "</tr>";

            # incremental, kung sa kaling meron pang galing sa timesheet trail
            $i += 1;
        }
      }else{
        echo "<tr class='remove'><td class=\"input-small align_center\"  colspan=\"4\">No result found.</td></tr>";
      }
      # end for ica-hyperion 21194 & 21196
    }

    // new function added for #ica-21090 by justin (with e)
    function showTimeRecord(){
      $toks = $this->input->post("toks");
      $data = array(
                      'empid' => $this->session->userdata('username'),
                      'cdate' => $this->gibberish->decrypt($this->input->post('cdate'), $toks)
                   );

      $getTimeRecord = $this->employeemod->findTimeRecord($data);
      if(count($getTimeRecord) > 0){
        foreach ($getTimeRecord as $row) {
          $diff = 0;
          echo "<tr id=\"tr-".$row->timeid."\">";
          echo "<td style=\"text-align: center;\" id=\"ti-".$row->timeid."\">". strtoupper(date('h:i a',strtotime($row->timein))) ."</td>";
          echo "<td style=\"text-align: center;\" id=\"to-".$row->timeid."\">". strtoupper(date('h:i a',strtotime($row->timeout))) ."</td>";
          if(strtoupper(date('h:i a',strtotime($row->timein))) != "12:00 AM" || strtoupper(date('h:i a',strtotime($row->timeout))) != "12:00 AM") $diff = round(abs(strtotime($row->timeout) - strtotime($row->timein)) / 60,0). " minute";
          echo "<td style=\"text-align: center;\"";
          if($diff == 0 || $diff == 1 || strtoupper(date('h:i a',strtotime($row->timein))) == "12:00 AM" || strtoupper(date('h:i a',strtotime($row->timeout))) == "12:00 AM"){
            echo " code='change'>";
            echo "<a class=\"btn blue\" code=\"".$row->timeid."\" id=\"edit\"><i class=\"glyphicon glyphicon-edit\"></i></a>";
            echo "<a class=\"btn blue\" code=\"".$row->timeid."\" id=\"remove\"><i class=\"glyphicon glyphicon-remove-sign\"></i></a>";
          }else{
            echo " code='not'>";
          }
          echo "</td>";
          echo "</tr>";
        }
      }else{
        echo "<tr><td colspan=\"3\" style=\"text-align: center;\">No Data Available..</td></tr>";
      }
    }

    function saveTimeRecord(){
      $toks = $this->input->post("toks");
      $aids = ($toks) ? $this->gibberish->decrypt($this->input->post("aid"), $toks) : $this->input->post("aid");
      # for ica-hyperion 21194 & 21196
      # by justin (with e)
      # > check kung admin yung user..

      $isAdmin = $this->extras->findIfAdmin($this->session->userdata("username"));
      $time_record = ($toks) ? $this->gibberish->decrypt($this->input->post("time_record"), $toks) : $this->input->post("time_record");
      $cdate = ($toks) ? $this->gibberish->decrypt($this->input->post("cdate"), $toks) : $this->input->post("cdate");
      $counter = 0;
      foreach (explode("|", $time_record) as $key) {
        $tr = explode("~u~", $key);
        $status = $tr[3];
        if($status != "REMOVED") $counter++;
      }
      # > hanapin dito
      $count = count(explode("|", $aids));
      if($counter > 0){
        foreach (explode("|", $aids) as $aid) {
          $q_status = $this->db->query("SELECT b.`status` FROM ob_app a INNER JOIN ob_app_emplist b ON b.`base_id`= a.id WHERE a.id='{$aid}' AND b.`status`='APPROVED'")->num_rows();


          $this->db->query("DELETE FROM leave_app_ti_to WHERE aid='".$aid."'");
          $td = "";
          $msg = $count." employee(s) successfully applied";
          foreach (explode("|", $time_record) as $key) {
              # updated by justin (with e) for mcu-hyperion 21113
              $tr = explode("~u~", $key);
              $tid = $tr[0];
              $actual = $tr[1];
              $request = $tr[2];
              $status = $tr[3];
              // save here
              $data = array(
                              'aid' => $aid,
                              'tid' => $tid,
                              'cdate' => $cdate,
                              'actual' => $actual,
                              'request' => $request,
                              'status' => $status
                            );
              $this->employeemod->saveTimeRecordModel($data);
              # end of updated for mcu-hyperion 21113
          }
          //echo "<pre>". print_r($td)/*(explode("|", $time_record))*/;die;
          $last_timeid = $this->db->query("SELECT MAX(timeid) AS id FROM timesheet")->row()->id;
          // $this->db->query("DELETE FROM timesheet WHERE timeid={$last_timeid}");
          # > kpag may result, Direct approved
          if($q_status > 0 && $isAdmin){
            # > find muna sa leave_app_ti_to
            $q_timerecord = $this->db->query("SELECT * FROM leave_app_ti_to WHERE aid='{$aid}';")->result();
            foreach ($q_timerecord as $qt) {
              # data
              $data = array(
                  'aid' => $qt->aid,
                  'tid' => $qt->tid,
                  'cdate' => $qt->cdate,
                  'actual' => $qt->actual_time,
                  'request' => $qt->request_time,
                  'status' => $qt->status
              );

              # > save direct approved
              if($qt->status) $this->employeemod->saveTimeRecordInTimesheet($data);
            }
          }
        }
      }else{
        $msg = "No records saved";
        foreach (explode("|", $aids) as $aid) {
          $this->db->query("DELETE FROM ob_app WHERE id = '$aid'");
          $this->db->query("DELETE FROM ob_app_emplist WHERE base_id = '$aid'");
        }
      }
      # end for ica-hyperion 21194 & 21196
      echo $msg;
    }

    function saveApprovedTimeRecord(){
      $aid = $this->input->post('aid');
      $cdate = $this->input->post('cdate');
      $timerecord = $this->input->post('timerecord');

      foreach (explode("|", $timerecord) as $key) {
        $tr = explode("~u~", $key);
        $time = explode(" - ", $tr[1]);
        $data = array(
                        "aid"     => $aid,
                        "cdate"   => $cdate,
                        "tid"     => $tr[0],
                        "timein"  => date('H:i:s', strtotime($time[0])),
                        "timeout" => date('H:i:s', strtotime($time[1])),
                        "status"  => $tr[2]
                     );

        // status
        if($tr[2] == "disapproved"){
          $ret = $this->employeemod->saveFinalTimeRecord($data, 1);
          //echo $ret."\n";
        }

        if($tr[2] == "change"){
          $ret = $this->employeemod->saveFinalTimeRecord($data, 0);
          //echo $ret."\n";
        }
        // end of status

        $result = $this->db->query("SELECT * FROM leave_app_tito WHERE aid='$aid' AND tid='{$tr[0]}'")->result();
        if(count($result) == 0){
          $sql = "INSERT INTO leave_app_tito (`aid`,`tid`,`cdate`,`actual_timein`,`actual_timeout`,`status`) VALUES ('$aid','{$tr[0]}','$cdate','".date('H:i:s', strtotime($time[0]))."','".date('H:i:s', strtotime($time[1]))."','{$tr[2]}')";
          $this->db->query($sql);
          //echo $sql."\n";
        }else{
          $this->employeemod->saveFinalTimeRecord($data, 1);
        }
        //echo "SELECT * FROM leave_app_tito WHERE aid='$aid' AND tid='{$tr[0]}'\n";
      }
      echo "save";
      //echo "aid = ". $aid ."\ncdate = ". $cdate ."\ntimerecord = ". $timerecord;
    }
    // end of new condition added for #ica-21090 by justin (with e)

    function verifyStatus(){
        $this->load->model('my_payroll');
        $empid = $this->session->userdata('username');
        $data  = $this->input->post();
        $cutoff = $data['cutoff'];
        $category = $data['category'];
        $result = $this->my_payroll->verifyIfPending($empid,$cutoff,$category);
        echo $result;
    }

    function loadSeminar(){
      $data['seminarInfo'] = $this->employeemod->inhouseSeminar();
      // echo $data; die;
      $this->load->view('config/inhouseseminar', $data);
    }

    function manageInhouseSeminar(){
      $toks = $this->input->post("toks");
      $data["datetoday"] = date("Y-m-d");
      $tbl_id = $toks ? $this->gibberish->decrypt( $this->input->post("tbl_id"), $toks ) : $this->input->post('tbl_id');
      $data['existingData'] = $this->employeemod->getInhouseData($tbl_id);
      $data["workshop_select"] = $this->setup->workshopSelection();
      $data["venLevel"] = $this->extras->showreportseduclevel(' - Select Venue - ','PTS');
      $this->load->view('config/addinhouseseminar', $data);
    }

    function logInSeminarGate(){
      $this->load->view('config/loginSeminarGate');
    }

    function loadWorkshop(){
      $category = $this->input->post('category');
      $workshopid = $this->input->post('workshopid');
      $return = '';
      $workshop = $this->employeemod->getWorkShop($category);
      foreach ($workshop as $value) {
        if($workshopid == $value['ID']){
          $return .= '<option value='.$value['ID'].' selected>'.$value['level'].'</option>';
        }else{
          $return .= '<option value='.$value['ID'].'>'.$value['level'].'</option>';
        }
      }
      echo $return;
    }

    public function getReminder(){
      $cutoff = explode(",", $this->input->post('cutoff'));
      $getremind = $this->employeemod->attendance_confirmation($cutoff[0],$cutoff[1]);
      print_r($getremind[0]);
    }

    public function getConfirmButton(){
      $cutoff = explode(",", $this->input->post('cutoff'));
      echo $this->employeemod->getConfirmButton($cutoff[0],$cutoff[1]);
    }
}
