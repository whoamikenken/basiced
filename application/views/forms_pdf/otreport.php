<?php

/**
 * @author Justin
 * @copyright 2016
 */

/*$from_date  = ($_GET['dfrom'] ? $_GET['dfrom'] : $_POST['dfrom']);
$to_date    = ($_GET['dto'] ? $_GET['dto'] : $_POST['dto']);
$dept       = ($_GET['deptid'] ? $_GET['deptid'] : $_POST['deptid']);
$tnt        = ($_GET['tnt'] ? $_GET['tnt'] : $_POST['tnt']);
$estatus    = ($_GET['estatus'] ? $_GET['estatus'] : $_POST['estatus']);
$campus     = ($_GET['campus'] ? $_GET['campus'] : $_POST['campus']);*/

$dateDisplay = '';
if(date('F',strtotime($from_date)) != date('F',strtotime($to_date))){
    $dateDisplay = date('F',strtotime($from_date))."-". date('F Y',strtotime($to_date));
}else{
    $dateDisplay = date('F Y',strtotime($from_date));
}

$edata      = "NEW";
$departments = $this->extras->showdepartment();
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $employeeid, $dept, $tnt, $estatus,$campus);
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
                  <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>OVERTIME REPORT</strong></span></td>
              </tr>
              <tr>
                  <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>As of ".$dateDisplay."</strong></span></td>
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
            <tr  style='background-color: #0099FF'>
                <td align='center' style='width: 10%;'>".date('F',strtotime($from_date))." - OVERTIME</td>";
                $x = 2;
            foreach ($qdate as $rdate) {
                $x++;
$info .= "      <td align='center' style='width: 2.5%;background: ".(in_array(date("D",strtotime($rdate->dte)),array("Sat")) ? "#C8C8C8" : (in_array(date("D",strtotime($rdate->dte)),array("Sun")) ? "#C8C8C8" : "")).";'>".date("D",strtotime($rdate->dte))."</td>
                ";
            }
$info .= "      
                <td align='center' style='border-bottom: 0;width: 5%;'>Total Late</td>
            </tr>
            <tr>      
                <td>&nbsp;</td>
         ";
            foreach ($qdate as $rdate) {
$info .= "      <td align='center' style='background: ".(in_array(date("D",strtotime($rdate->dte)),array("Sat")) ? "#C8C8C8" : (in_array(date("D",strtotime($rdate->dte)),array("Sun")) ? "#C8C8C8" : "")).";'>".date("d",strtotime($rdate->dte))."</td>";
            }
$info .= "      
                <td align='center' style='border-top: 0;'>Undertime</td>
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
                
            $tot = "";
            foreach ($qdate as $rdate) {
            $totr = "";
            $sched = $this->attcompute->displaySched($empid,$rdate->dte);
            $countrow = $sched->num_rows();
            if($countrow){
            foreach($sched->result() as $rsched)    
                list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
            }else{
                list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);    
            }
            /* Overtime */
            // total regular
            if($totr){
                $secs  = strtotime($otreg)-strtotime("00:00:00");
                if($secs>0) $totr = date("H:i",strtotime($totr)+$secs);
            }else
                $totr    = $otreg;
            if($totr){
                $secs  = strtotime($otsat)-strtotime("00:00:00");
                if($secs>0) $totr = date("H:i",strtotime($totr)+$secs);
            }else
                $totr    = $otsat;
            if($totr){
                $secs  = strtotime($otsun)-strtotime("00:00:00");
                if($secs>0) $totr = date("H:i",strtotime($totr)+$secs);
            }else
                $totr    = $otsun;
            if($totr){
                $secs  = strtotime($othol)-strtotime("00:00:00");
                if($secs>0) $totr = date("H:i",strtotime($totr)+$secs);
            }else
                $totr    = $othol;
                
            // Total
            if($tot){
                $secs  = strtotime($totr)-strtotime("00:00:00");
                if($secs>0) $tot = date("H:i",strtotime($tot)+$secs);
            }else
                $tot    = $totr;
            
            $info .= "      <td align='center' style='background: ".(in_array(date("D",strtotime($rdate->dte)),array("Sat")) ? "#C8C8C8" : (in_array(date("D",strtotime($rdate->dte)),array("Sun")) ? "#C8C8C8" : "")).";'>$totr</td>";
            }

$info .= "      <td align='center'>$tot</td>
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



