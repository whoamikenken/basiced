<?php
/**
 * @author Justin
 * @copyright 2016
 */

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);

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
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>Summary of Absences (No leave filed)</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>As of ".date('F Y')."</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";
/*
if($tnt == "teaching"){ // Teaching
}else{  // Non Teaching
*/
$info .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
            <tr style='background-color: #0099FF'>
                <td align='center' style='width: 20%;' rowspan=3>NAME</td>";
            $temp = "";
            foreach ($qdate as $rdate) {
                if($temp != date("F' Y",strtotime($rdate->dte)))
$info .= "      <td align='center' style='width: 5%;border-bottom: 0;background: #0099FF;'>".date("F' Y",strtotime($rdate->dte))."</td>
                <td align='center' style='width: 5%;background: #0099FF;' rowspan=3>NLF Total</td>
         ";
            $temp = date("F' Y",strtotime($rdate->dte));
            }
$info .= "      
            </tr>
         ";
         $temp = "";
$info .= "<tr>";
            foreach ($qdate as $rdate) {
                if($temp != date("F' Y",strtotime($rdate->dte)))
$info .= "      <td align='center' style='width: 5%;border-top: 0;border-bottom: 0;background: #0099FF;'>ABSENCES</td>
         ";
            $temp = date("F' Y",strtotime($rdate->dte));
            }
$info .= "</tr>";
          $temp = "";
$info .= "<tr>";
            foreach ($qdate as $rdate) {
                if($temp != date("F' Y",strtotime($rdate->dte)))
$info .= "      <td align='center' style='width: 5%;border-top: 0;background: #0099FF;'>No Leave Filed</td>
         ";
            $temp = date("F' Y",strtotime($rdate->dte));
            }
$info .= "</tr>
            </thead>";
            foreach ($result as $key => $data) {
                $empid = $data['qEmpId'];
                $name = $data["qFullname"];
                $info .= "
            <tr>
                <td>$name</td>
                ";
            $arr = array();
            foreach ($qdate as $rdate) {
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
                    
                    // Absent
                    if(!$el && !$vl && !$sl && !$ol && !$holiday){
                    $absent += $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);           // total absent
                    
                    if($this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd))
                    $arr[$rdate->dte] += $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd); // store date with absences in array.
                    }
                }
                
                if(date("t",strtotime($rdate->dte)) == date("d",strtotime($rdate->dte))){
                    foreach($arr as $key=>$val) $incleave .= date("d",strtotime($key))." $val; ";
                    $info .= "      <td align='center' style='color: red;'>$incleave</td>";
                    $info .= "      <td align='center'>".($absent ? $absent : 0)."  </td>";
                    $absent = 0;
                    $arr = array();
                    $incleave = "";
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
#}
     

$pdf->WriteHTML($info);

$pdf->Output();
?>



