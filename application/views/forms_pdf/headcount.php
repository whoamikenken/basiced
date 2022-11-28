<?php

/**
 * @author Glen Mark
 * @copyright 2017
 */
require_once(APPPATH."constants.php");

$from_date  = isset($dfrom) ? $dfrom : '';
$educlevel  =  isset($educlevel) ? $educlevel : '';
$to_date    = isset($dto) ? $dto : '';
$dept       = isset($deptid) ? $deptid : '';
$office       = isset($officeid) ? $officeid : '';
$tnt        = isset($tnt) ? $tnt : '';
$estatus    =  isset($estatus) ? $estatus : '';
$campus     =  isset($campus) ? $campus : '';
$isactive    =  isset($isactive) ? $isactive : '';
$compcampus    =  isset($company_campus) ? $company_campus : '';
$empstatus    =  isset($empstatus) ? $empstatus : '';
$status    =  isset($status) ? $status : '';
$datenow = date("Y-m-d");
$wc = $where = '';
if($office) $wc .= " AND code = '$office'";
if($dept)  $wc .= " AND department_id = '$dept'";
if($tnt) $where .= " AND teachingtype='$tnt'";
if($isactive != "all"){
  if($isactive=="1"){
    $where .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
  }
  if($isactive=="0"){
    $where .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
  }
  if(is_null($isactive)) $where .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
}


$educationallevel = array();
$query = $this->db->query("SELECT code,description FROM code_educational_level");
if ($query->num_rows() > 0) {
    foreach ($query->result() as $key => $value) {
        $educationallevel[$value->code] = $value->description;
    }
}

$arr_list = array();
$divisions = $this->extras->showManagement(); 
unset($divisions['']);
// $employeeoffices = $this->db->query("SELECT a.office FROM employee a UNION SELECT CODE FROM code_office");

foreach ($divisions as $mgmtid => $desc) {

    $deptlist = array();
    $res = $this->db->query("SELECT code, description FROM code_office WHERE managementid='$mgmtid'"); //AND description NOT LIKE '%Head%' AND description NOT LIKE '%Director%' AND description != 'Registrar'
    if($res->num_rows() > 0){
        foreach ($res->result() as $row) {
            $deptlist[$row->code] = $row->description;
        }
    }
    $arr_list[$mgmtid] = array('mgmtdesc'=>$desc, 'deptlist'=>$deptlist);
}
unset($arr_list['']);
$deptlist = array();
$res = $this->db->query("SELECT code, description FROM code_office WHERE IFNULL(managementid,'')='' "); //AND description NOT LIKE '%Head%' AND description NOT LIKE '%Director%' AND description != 'Registrar'
if($res->num_rows() > 0){
    foreach ($res->result() as $row) {
        $deptlist[$row->code] = $row->description;
    }
}
$arr_list['OTHER'] = array('mgmtdesc'=>'OTHER', 'deptlist'=>$deptlist);


$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,8,8,9,2);
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
                
                th{
                    color: yellow;
                }
                .tbl tr:nth-child(even)
                {
                  background-color:#C8C8C8;
                }
                  .tbls tr:nth-child(even)
                {
                  background-color:#C8C8C8;
                }
                .footer{
                    width: 100%;
                    text-align: right;
                }
                </style>";
$info .= "
<htmlpageheader name='Header'>
 <div>
        <table width='60%'  >
            <tr>
                <td rowspan='5' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='50%'><span style='font-size: 12px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 10px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>".strtoupper($reportTitle)." REPORT </strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>As of ".date("F Y")."</span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 10px;' width='55%'><strong>".$officeHeader."</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>
<div class='content'>
    <div class='content-header'>";

$arr_list = array();
$divisions = $this->extras->showManagement(); 
$code_status = $this->extras->getEmploymentCodeStatus();
$educlevel = $this->extras->showreportseduclevel('','eb');
$colspan = count($code_status) + 4;
unset($divisions['']);
unset($educlevel['']);
$colspan2 = count($educlevel) + 3;
$divisionCount = array();
$grandtotal = 0;
$nostat = 0;
// echo "<pre>"; print_r($divisions); die;
foreach ($divisions as $mgmtid => $desc) {
    $deptlist = array();
    $divisionCount[$mgmtid] = 0;
    $res = $this->db->query("SELECT code, description FROM code_office WHERE managementid='$mgmtid' "); //AND description NOT LIKE '%Head%' AND description NOT LIKE '%Director%' AND description != 'Registrar'
    if($res->num_rows() > 0){
        foreach ($res->result() as $row) {
            $deptlist[$row->code] = $row->description;
        }
    }
    $arr_list[$mgmtid] = array('mgmtdesc'=>$desc, 'deptlist'=>$deptlist);
}

$deptlist = array();
//$res = $this->db->query("SELECT code, description FROM code_office WHERE IFNULL(managementid,'')='' $wc"); // AND description NOT LIKE '%Head%' AND description NOT LIKE '%Director%' AND description != 'Registrar' AND description != ''
$res = $this->db->query("SELECT office FROM employee WHERE office IN (SELECT code from code_office WHERE 1 $wc) $where");
// echo "<pre>"; print_r($this->db->last_query()); echo "</pre>"; die;
if($res->num_rows() > 0){
    foreach ($res->result() as $key) {
        $deptlist[$key->office] = $this->extensions->getOfficeDesc($key->office);
    }
}
asort($deptlist);

$empcount = 1;
if ($status == "bystat") {
$headCount = array();
// $colspan -= 1;
$info .= "
         <table border='1' width='100%' class='tbl'>
         <tr >
         <td colspan='".$colspan."' align='center' ><h2><b>EMPLOYMENT STATUS</b></h2></td>
         </tr>
         <tr style='background-color: black'>
            <th align='center' width='5%'>#</th>
            <th align='center'>OFFICE</th>
             ";
             foreach ($code_status as $key => $value) {
                 $info .= "<th align='center'>".$value->description."</th>";
                 $headCount[$key] = 0;
             }
             $info .= "
             <th align='center'>NO STATUS</th>
             <th align='center'>TOTAL</th>
         </tr>
        ";

$headCount['head'] = 0;

foreach($deptlist as $keyy =>$data)
    {
    // $total = $this->reports->countHeads($key);
    $total = 0;
    foreach ($code_status as $k => $val) {
        $total = $total + $this->reports->countDeptESTAT($keyy,date("Y-m",strtotime($from_date)),$val->code,$campus,$isactive,$tnt, $deptid);
        // echo "<pre>"; print_r($this->db->last_query()); die;
    }
    $countnoofficeandnoemploymentstat = $this->reports->countnoofficeandnoemploymentstat($keyy,$isactive,$tnt);

    // echo "<pre>"; print_r($countnoofficeandnoemploymentstat); die;
    $nostat = $nostat + $countnoofficeandnoemploymentstat;
$info .="<tr>
        <td>".$empcount."</td>
        <td>".$data."</td>";
        foreach ($code_status as $k => $val) {
            $totals = $totals + $this->reports->countDeptESTAT($keyy,date("Y-m",strtotime($from_date)),$val->code,$campus, $isactive,$tnt, $dept);
            if($this->reports->countDeptESTAT($keyy,date("Y-m",strtotime($from_date)),$val->code,$campus,$isactive,$tnt, $dept)){
                $headCount[$k] = $headCount[$k] + $this->reports->countDeptESTAT($keyy,date("Y-m",strtotime($from_date)),$val->code,$campus, $isactive,$tnt, $dept);
                $info .="<td  class='datas' align='center'>".$this->reports->countDeptESTAT($keyy,date("Y-m",strtotime($from_date)),$val->code,$campus,$isactive,$tnt, $dept)."</td>";
                // echo "<pre>"; print_r($this->db->last_query()); die;
            }else{
                $info .="<td class='datas' align='center' >0</td>";
            } 
        }
        $total += $countnoofficeandnoemploymentstat;
$info .="<td align='center' class='datas'>".$countnoofficeandnoemploymentstat."</td>
        <td align='center' class='datas'>".$total."</td></tr>";
$empcount++;
    }
    $empcount = $empcount - 1;
    $colspan = $colspan - 1;

    $info .="  <tr>
            <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  colspan='2'><b>Total:</b></td>
            ";
            $grandtotal = $grandtotal + $headCount['head'];
            foreach ($code_status as $k => $val) {
                $info .="<td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$headCount[$k]."</b></td>";
                $grandtotal = $grandtotal + $headCount[$k];
            }
            $grandtotal += $nostat;
            $info .=" <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$nostat."</b></td>
            <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$grandtotal."</b></td>
        </tr>";

$info .=" </table>";
}
else
{
$empcount = 1;
$educCount = array();
    $info .="<table border='1' width='100%' class='tbls'>
         <tr>
         <td colspan='".$colspan2."' align='center' ><h2><b>EDUCATIONAL BACKGROUND</b></h2></td>
         </tr>
         <tr style='background-color: black'>
            <th align='center' width='5%'>#</th>
             <th  class='datas'>DEPARTMENTS</th>";
             foreach ($educlevel as $k => $v) {
                 $info .="<th align='center' class='datas'>".$v."</th>";
                 $educCount[$k] = 0;
             }
             $info .="
            <th align='center' class='datas'>TOTAL</th>
         </tr>";
    if(!$office || !$deptid){
        foreach ($divisions as $key => $value){
            $info .="<tr>
            <td  class='datas'>".$empcount."</td>
            <td  class='datas'>".$value."</td>";
            foreach ($educlevel as $k => $v) {
                     $info .="<td align='center' class='datas'>0</td>";
                 }
            $info .="
            <td align='center' class='datas'>0</td>
            </tr>";
            $empcount++;
        }
    }
        
    foreach($deptlist as $keyy =>$data)
        {
            // $total = $this->reports->countHeadByEducBackground($keyy,$campus, '', $isactive, $dept);
            $total = 0;
            foreach ($educlevel as $key => $value) {
                $total = $total + $this->reports->countHeadByEducBackground($keyy,$campus,$value, $isactive, $dept);
            }
            
        $info .="<tr>
        <td >".$empcount."</td>
        <td >".$data."</td>";
        foreach ($educlevel as $key => $value) {
            if ($this->reports->countHeadByEducBackground($keyy,$campus,$value, $isactive, $dept)) {
                $info .="<td align='center'>".$this->reports->countHeadByEducBackground($keyy,$campus,$value, $isactive, $dept)."</td>";
                $educCount[$key] = $educCount[$key] + $this->reports->countHeadByEducBackground($keyy,$campus,$value, $isactive, $dept);
            }
            else{
                $info .="<td align='center'>0</td>";   
            }
        }

        $info .="<td  align='center'>".$total."</td></tr>";
        $empcount++;
        }
        $empcount = $empcount - 1;
        $colspan2 = $colspan2 - 1;
         $info .="  <tr>
            <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  colspan='2'><b>Total:</b></td>
            ";
            foreach ($educlevel as $key => $value) {
                $info .="<td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$educCount[$key]."</b></td>";
                $grandtotal = $grandtotal + $educCount[$key];
            }

            $info .=" 
            <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$grandtotal."</b></td>
        </tr>";
        $info .="</table></div></div>";

}
     
     
$info .= "
    <htmlpagefooter name='Footer'>
        <br>
        <div class='footer'>
            Page : {PAGENO} of {nb}
        </div>
    </htmlpagefooter>
";

$pdf->WriteHTML($info);
$pdf->Output();