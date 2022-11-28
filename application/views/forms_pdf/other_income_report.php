<?php
/**
* @author justin (with e)
* @copyright 2018
*/
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);

require_once(APPPATH."constants.php");
$pdf = new mpdf('P', 'A4','','UTF-8',5,5,8,5);

$show_content  = "
<style>
    @page{            
        margin-top: 4cm;
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
    
    /*#datas tr:nth-child(odd){
        background-color:#C8C8C8;
    }*/
</style>
";

$show_content .= "
<htmlpageheader name='Header'  style='margin-top: -50px;'>
     <div>
        <table width='10%' >
            <tr>
                <td rowspan='3' style='text-align: right;' width='60%'><img src='". $imgurl ."images/school_logo.jpg' style='width: 60px;text-align: left;' /></td>
                <td align='left' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 20px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='". $imgurl ."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='left' style='padding: 0;text-align: left;'><span style='font-size: 18px;' width='45%'>D`Great</span></td>
            </tr>
           <!--  <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>As of ".date('F Y')."</strong></span></td>
            </tr> -->
        </table>
        <table width='100%' >
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='100%'>&nbsp;</td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='100%'><strong><span style='font-size: 17px;' width='100%'>OTHER INCOME REPORT</span></strong></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='100%'>As of ".date('F Y')."</span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>
";


$show_content .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
                <tr style='background-color: #000'>
                    <td align='center' style='width: 05%; color: #0072c6; font-weight: bold'>#</td>
                    <td align='center' style='width: 25%; color: #0072c6; font-weight: bold'>NAME</td>
                    <td align='center' style='width: 15%; color: #0072c6; font-weight: bold'>INCOME</td>
                    <td align='center' style='width: 15%; color: #0072c6; font-weight: bold'>MONTHLY</td>
                    <td align='center' style='width: 15%; color: #0072c6; font-weight: bold'>DAILY</td>
                    <td align='center' style='width: 15%; color: #0072c6; font-weight: bold'>HOURLY</td>
                    <td align='center' style='width: 15%; color: #0072c6; font-weight: bold'>DATE EFFECTIVE</td>
                </tr>
            </thead>
            <tbody>
";

$total = array(
    "monthly" => 0,
    "daily"   => 0,
    "hourly"  => 0
);
$pop_salary = array();
$counter = 1;
foreach ($emplist as $employeeid => $info) {
    if($isdetailed == "yes"){
        ksort($info['income_list']);
    }
    else{
        ksort($info['income_list']);
        if(isset($info['income_list']['salary'])){
            $pop_salary = $info['income_list']['salary'];
            unset($info['income_list']['salary']);
            array_unshift($info['income_list'], $pop_salary);
        }
    }
    $is_first_item = true;

    foreach ($info["income_list"] as $code => $income) { 
        if($code == 0) $code = 'salary';
        if($code != "deminimiss"){
            $name_td = "";

            if($is_first_item){
                $name_td .= "
                    <td align='right' style='padding-right: 5px;' rowspan='". count($info["income_list"]) ."'>". $counter ."</td>
                    <td style='padding-left: 8px;' rowspan='". count($info["income_list"]) ."'>". $info["fullname"] ."</td>
                ";
            }

            $show_content .= "
                <tr>
                    ". $name_td ."
                    <td align='center'>". $income["income_desc"] ."</td>
                    <td align='right' style='padding-right: 5px;'>". number_format($income["monthly"], 2) ."</td>
                    <td align='right' style='padding-right: 5px;'>". number_format($income["daily"], 2) ."</td>
                    <td align='right' style='padding-right: 5px;'>". number_format($income["hourly"], 2) ."</td>
                    <td align='center'>". $income["date_effective"] ."</td>
                </tr>
            ";

            $total["monthly"]   += $income["monthly"];
            $total["daily"]     += $income["daily"];
            $total["hourly"]    += $income["hourly"];

            $is_first_item = false;
        }
    }
    
    if(isset($info["income_list"]["deminimiss"])){
        if($is_first_item){
                $name_td .= "
                    <td align='right' style='padding-right: 5px;' rowspan='". count($info["income_list"]) ."'>". $counter ."</td>
                    <td style='padding-left: 8px;' rowspan='". count($info["income_list"]) ."'>". $info["fullname"] ."</td>
                ";
        }

        $show_content .= "
                <tr>
                    ". $name_td ."
                    <td align='center'>". $info["income_list"]["deminimiss"]["income_desc"] ."</td>
                    <td align='right' style='padding-right: 5px;'>". number_format($info["income_list"]["deminimiss"]["monthly"], 2) ."</td>
                    <td align='right' style='padding-right: 5px;'>". number_format($info["income_list"]["deminimiss"]["daily"], 2) ."</td>
                    <td align='right' style='padding-right: 5px;'>". number_format($info["income_list"]["deminimiss"]["hourly"], 2) ."</td>
                    <td align='center'>". $income["date_effective"] ."</td>
                </tr>
        ";

        $total["monthly"]   += $info["income_list"]["deminimiss"]["monthly"];
        $total["daily"]     += $info["income_list"]["deminimiss"]["daily"];
        $total["hourly"]    += $info["income_list"]["deminimiss"]["hourly"];
    }

    $counter += 1;
}

$show_content .= "
                <tr>
                    <td align='right' style='padding-right: 5px;' colspan='3'><strong>Total</strong></td>
                    <td align='right' style='padding-right: 5px;'><strong>". number_format($total["monthly"], 2) ."</strong></td>
                    <td align='right' style='padding-right: 5px;'><strong>". number_format($total["daily"], 2) ."</strong></td>
                    <td align='right' style='padding-right: 5px;'><strong>". number_format($total["hourly"], 2) ."</strong></td>
                </tr>
";

$show_content .= "
            </tbody>
        </table>
    </div>
</div>
";
$pdf->WriteHTML($show_content);
$pdf->Output();