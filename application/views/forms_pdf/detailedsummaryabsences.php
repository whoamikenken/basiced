<?php

/**
 * @author Justin
 * @copyright 2016
 */

$from_date  = ($_GET['dfrom'] ? $_GET['dfrom'] : $_POST['dfrom']);
$to_date    = ($_GET['dto'] ? $_GET['dto'] : $_POST['dto']);
$dept       = ($_GET['deptid'] ? $_GET['deptid'] : $_POST['deptid']);
$tnt        = ($_GET['tnt'] ? $_GET['tnt'] : $_POST['tnt']);
$estatus    = ($_GET['estatus'] ? $_GET['estatus'] : $_POST['estatus']);
$campus     = ($_GET['campus'] ? $_GET['campus'] : $_POST['campus']);
$edata      = "NEW";
$departments = $this->extras->showdepartment();
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, "", $dept, $tnt, $estatus,$campus);
// echo $this->db->last_query();die;
$qdate = $this->attcompute->displayDateRange($from_date, $to_date);    
$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,5,5);
$info  = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3.15cm;
                    odd-header-name: html_Header;
                    odd-footer-name: html_Footer;
                }  
                .content{
                    height: 100%;
                    margin-top: 15px;
                }
                table{
                    border-collapse: collapse;
                    font-size: 12px;
                    border-spacing: 5px;
                }
                .content-header{
                    text-align: center;
                    font-size: 12px;
                }
                .content-body{
                    border: 1px solid black;
                    padding-top: 8px;
                    padding-bottom: 8px;
                    padding-left: 8px;
                }
                #datas tr:nth-child(odd)
                {
                    background-color:#C8C8C8;
                }
            </style>";
$info .= "
<htmlpageheader name='Header'>
   <div>
          <table width='60%'  >
              <tr>
                  <td rowspan='3' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                  <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
                 <!-- 
                  <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
              </tr>
              <tr>
                  <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>DETAILED SUMMARY OF ABSENCES</strong></span></td>
              </tr>
              <tr>
                  <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>As of ".date('F Y')."</strong></span></td>
              </tr>
          </table>
      </div>
</htmlpageheader>";

if($tnt == "teaching"){ // Teaching


}else{  // Non Teaching

$info .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
            <tr style='background-color: #0099FF'>
                <td align='center' style='width: 10%;'>".date('F',strtotime($from_date))." - ABSENCES</td>";
                $x = 2;
            foreach ($qdate as $rdate) {
                $x++;
$info .= "      <td align='center' style='width: 2.5%;background: ".(in_array(date("D",strtotime($rdate->dte)),array("Sat")) ? "#C8C8C8" : (in_array(date("D",strtotime($rdate->dte)),array("Sun")) ? "#C8C8C8" : "")).";'>".date("D",strtotime($rdate->dte))."</td>
                ";
            }
$info .= "      
                <td align='center' style='border-bottom: 0;width: 5%;'>Total</td>
            </tr>
            <tr>      
                <td>&nbsp;</td>
         ";
            foreach ($qdate as $rdate) {
$info .= "      <td align='center' style='background: ".(in_array(date("D",strtotime($rdate->dte)),array("Sat")) ? "#C8C8C8" : (in_array(date("D",strtotime($rdate->dte)),array("Sun")) ? "#C8C8C8" : "")).";'>".date("d",strtotime($rdate->dte))."</td>";
            }
$info .= "      
                <td align='center' style='border-top: 0;'>Absences</td>
            </tr>
            <tr>
                <td colspan='$x'>Name of Employee</td>
            </tr>
            </thead>  
         ";
            foreach ($result as $key => $data) {
                $empid = $data['qEmpId'];
                $name = $data["qFullname"];
                $info .= "
            <tr>
                <td>$name</td>
                ";
            $gtabsent = "";
            foreach ($qdate as $rdate) {
            $tabsent = "";
            $sched = $this->attcompute->displaySched($empid,$rdate->dte);
            foreach($sched->result() as $rsched){
            $stime  = $rsched->starttime;
            $etime  = $rsched->endtime; 
            $earlyd = $rsched->early_dismissal;
            
            // Holiday
            $holiday = $this->attcompute->isHoliday($rdate->dte); 
            
            // logtime
            list($login,$logout,$q)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
            
            // Overtime
            list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                    
            // Leave
            list($el,$vl,$sl,$ol)             = $this->attcompute->displayLeave($empid,$rdate->dte);
            
            // Absent
            $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
            if($el || $vl || $sl || $ol || $holiday) $absent = "";
            $tabsent += $absent;
            }
            $gtabsent += $tabsent;
            $info .= "<td align='center' style='background: ".(in_array(date("D",strtotime($rdate->dte)),array("Sat")) ? "#C8C8C8" : (in_array(date("D",strtotime($rdate->dte)),array("Sun")) ? "#C8C8C8" : "")).";'>$tabsent</td>";
            }

$info .= "      <td align='center'>$gtabsent</td>
            </tr>
";
}
$info .= "      
            </table>
    	</div>
     </div>";
}
     

$pdf->WriteHTML($info);

$pdf->Output();
?>



