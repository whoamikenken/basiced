<?php

/**
 * @author Justin
 * @copyright 2016
 */

$from_date  = date("Y-m-",strtotime(($_GET['dfrom'] ? $_GET['dfrom'] : $_POST['dfrom'])))."01";
$to_date    = date("Y-m-",strtotime(($_GET['dto'] ? $_GET['dto'] : $_POST['dto']))).date("t",strtotime(($_GET['dto'] ? $_GET['dto'] : $_POST['dto'])));
$dept       = ($_GET['deptid'] ? $_GET['deptid'] : $_POST['deptid']);
$tnt        = ($_GET['tnt'] ? $_GET['tnt'] : $_POST['tnt']);
$estatus    = ($_GET['estatus'] ? $_GET['estatus'] : $_POST['estatus']);
$campus     = ($_GET['campus'] ? $_GET['campus'] : $_POST['campus']);
$edata      = "NEW";
$departments = $this->extras->showdepartment();
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, "", $dept, $tnt, $estatus,$campus);
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
                  <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>Summary of Absences w/ SL, VL and EL</strong></span></td>
              </tr>
              <tr>
                  <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>As of ".date('F Y')."</strong></span></td>
              </tr>
          </table>
      </div>
</htmlpageheader>";


$info .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
            <tr style='background-color: #0099FF';>
                <td align='center' style='width: 20%;' rowspan=3>NAME</td>
                <td align='center' style='width: 20%;' style='width: 5%;border-top: 0;border-bottom: 0;background: #0099FF;' >VL</td>
                <td align='center' style='width: 20%;' style='width: 5%;border-top: 0;border-bottom: 0;background: #0099FF;' >SL</td>";
            $temp = "";
            foreach ($qdate as $rdate) {
                if($temp != date("F' Y",strtotime($rdate->dte)))
$info .= "      <td align='center' style='width: 5%;border-bottom: 0;background: #0099FF;' colspan=5>".date("F' Y",strtotime($rdate->dte))."</td>
         ";
            $temp = date("F' Y",strtotime($rdate->dte));
            }
$info .= "      
            </tr>
         ";
         $temp = "";
$info .= "<tr>
                <td align='center' style='width: 5%;border-top: 0;border-bottom: 0;background: #0099FF;'>Credit</td>
                <td align='center' style='width: 5%;border-top: 0;border-bottom: 0;background: #0099FF;'>Credit</td>
    ";
            foreach ($qdate as $rdate) {
                if($temp != date("F' Y",strtotime($rdate->dte)))
$info .= "      
                <td align='center' style='width: 5%;border-top: 0;border-bottom: 0;background: #0099FF;' colspan=5>ABSENCES</td>
         ";
            $temp = date("F' Y",strtotime($rdate->dte));
            }
$info .= "</tr>";
          $temp = "";
$info .= "<tr>
                <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>Balance</td>
                <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>Balance</td>
";
            foreach ($qdate as $rdate) {
                if($temp != date("F' Y",strtotime($rdate->dte)))
$info .= "      
                <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>w/ EL Filed</td>
                <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>w/ VL Filed</td>
                <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>w/ SL Filed</td>
                <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>w/ VL-TOTAL</td>
                <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>w/ SL-TOTAL</td>
         ";
            $temp = date("F' Y",strtotime($rdate->dte));
            }
$info .= "</tr></thead>";
            foreach ($result as $key => $data) {
                $empid = $data['qEmpId'];
                $name = $data["qFullname"];
                
                $vebal = 0;
                $vlbal = $this->employeemod->displayleavetype($empid)->result();
                
                foreach($vlbal as $ldata){
                    if(in_array($ldata->code_request,array("VL","EL"))) $vebal += $ldata->balance;
                    else if($ldata->code_request == "SL")               $sbal   = $ldata->balance;
                }
                               
                $info .= "
            <tr>
                <td>$name</td>
                ";
            $info .= "      <td align='center'>$vebal</td>";
            $info .= "      <td align='center'>$sbal</td>";
            
            $arrel = $arrvl = $arrsl = array();
            foreach ($qdate as $rdate) {
                $el = $vl = $sl = $ol = 0;
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                foreach($sched->result() as $rsched){
                    $stime  = $rsched->starttime;
                    $etime  = $rsched->endtime; 
                    $earlyd = $rsched->early_dismissal;
                    // Holiday
                    $holiday = $this->attcompute->isHoliday($rdate->dte); 
                    
                    // logtime
                    list($login,$logout,$q)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                    
                    // Leave
                    list($el,$vl,$sl,$ol)             = $this->attcompute->displayLeave($empid,$rdate->dte);
                }
                if($el) $arrel[$rdate->dte] += $el; // store date with absences in array.
                if($vl) $arrvl[$rdate->dte] += $vl;
                if($sl) $arrsl[$rdate->dte] += $sl;
                $tel += $el;
                $tvl += $vl;
                $tsl += $sl;
                
                if(date("t",strtotime($rdate->dte)) == date("d",strtotime($rdate->dte))){
                    foreach($arrel as $key=>$val) $eleave .= date("d",strtotime($key))." $val; ";
                    foreach($arrvl as $key=>$val) $vleave .= date("d",strtotime($key))." $val; ";
                    foreach($arrsl as $key=>$val) $sleave .= date("d",strtotime($key))." $val; ";
                    $info .= "      <td align='center'>$eleave</td>";
                    $info .= "      <td align='center'>$vleave</td>";
                    $info .= "      <td align='center'>$sleave</td>";
                    $info .= "      <td align='center'>".($tel+$tvl)."</td>";
                    $info .= "      <td align='center'>$tsl</td>";                    
                    $tel = $tvl = $tsl = 0;
                    $eleave = $vleave = $sleave = "";
                }
            }

$info .= "      
            </tr>
";
}
$info .= "      
            </table>
    	</div>
     </div>";

     

$pdf->WriteHTML($info);

$pdf->Output();
?>



