<?php
// Kennedy

require_once(APPPATH."constants.php");

$cdata = $result;

$extracol = "";
$isactive    = isset($isactive) ? $isactive : '';
$cdata = $this->reports->allEmpByPosition($isactive); 
// echo "<pre>"; print_r($cdata); die;

$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,8,8,9,2);
$info  = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3.15cm;
                    odd-header-name: html_Header;
                    odd-footer-name: html_Footer;
                }
                th{
                	color: yellow;
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

			    .footer{
			    	width: 100%;
			    	text-align: right;
			    }
                td{
                    text-align: center;
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>EMPLOYEE BALANCE(Per Deduction) REPORT </strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>As of ".date("F Y")."</span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 10px;' width='55%'><strong></strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";

$tag_ = ($tag == 'DEDUCTION') ? 'Deduction' : 'Loan';
$info .= "

<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
            <tr style='background-color: #000000;'>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>#</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>EMPLOYEE ID</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>AMOUNT</th>
            ";
if ($tag != 'DEDUCTION') {
    $info .= "<th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>BALANCE</th>";
}
$info .= "</thead>";
$info .= "<tbody>";


$grand_total = $grand_bal = 0;
foreach ($data_list as $code => $list) { // <<< displayed content
    //        writeContent($sheet, "Description : ", $bold, false, $row, $col);
    // $row = writeContent($sheet, $config[$code], $bold, false, $row, 1, 0, $end_col - 1);

    // $row = writeTable($sheet, $header_table, $row, $col);

    ksort($list);
    $count = 1;
    foreach ($list as $sort_key => $employee_list) {
        $sub_total = $sub_bal = 0;

        if($sort_key == "ACAD" && $sort_by == "department"){ // <<< ACAD ONLY
            $employee_list_acad = array();
            foreach ($campus as $c_code => $c_desc) {
                foreach ($employee_list as $employeeid => $infos) {
                    if($infos["campusid"] == $c_code) $employee_list_acad[$c_code][$employeeid] = $infos;
                }
            }

            foreach ($campus as $c_code => $c_desc) {
                if(isset($employee_list_acad[$c_code])){
                    $sub_total = 0;
                    // $row = writeContent($sheet, $department[$sort_key] ." - ". $c_desc, $bold, false, $row, $col, 0, $end_col);
                    $info .= "<tr><td colspan='4' style='text-align: left!important; font-weight: bold;'>".$department[$sort_key] ." - ". $c_desc."</tr>";

                    foreach ($employee_list_acad[$c_code] as $employeeid => $infos) {
                        // $table_content = array(
                        //     array($count, $normalcenter, false, 20),
                        //     array($employeeid, $normalcenter, true, 30),
                        //     array(strtoupper(utf8_decode($infos["name"])), $normal, false, 50),
                        //     array(number_format($infos["amount"], 2), $normalright, true, 25),
                        // );
                        // if($tag != "DEDUCTION")  $table_content[] = array(number_format($infos["balance"], 2), $normalright, true, 25);
                        $info .= "<tr>
                                    <td>".$count."</td>
                                    <td>".$employeeid."</td>
                                    <td>".$infos["name"]."</td>
                                    <td>".$infos["amount"]."</td>";

                        if($tag != 'DEDUCTION'){$info .= "<td>".$infos["balance"]."</td>";}

                        $info .= "</tr>";

                        $count += 1;
                        $sub_total += $infos["amount"];
                        $grand_total += $infos["amount"];
                        $sub_bal += $infos["balance"];
                        $grand_bal += $infos["balance"];
                        // $row = writeTable($sheet, $table_content, $row, $col);
                    }
                    
                           // writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
                    // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
                    // if($tag != "DEDUCTION") writeContent($sheet, number_format($sub_bal, 2), $normalBold, true, ($row - 1), 4);
                }
            }
        }else{
            if($department[$sort_key] != ''){
                if($sort_by == "department"){
                    $info .= "<tr><td colspan='4' style='text-align: left!important; font-weight: bold;'>".$department[$sort_key]."</tr>";
                }
            }//$row = writeContent($sheet, $department[$sort_key], $bold, false, $row, $col, 0, $end_col);

            foreach ($employee_list as $employeeid => $infos) {
                // $table_content = array(
                //     array($count, $normalcenter, false, 20),
                //     array($employeeid, $normalcenter, true, 30),
                //     array(strtoupper(utf8_decode($infos["name"])), $normal, false, 50),
                //     array(number_format($infos["amount"], 2), $normalright, true, 25),
                // );
                // if($tag != "DEDUCTION")  $table_content[] = array(number_format($infos["balance"], 2), $normalright, true, 25);

                $info .= "<tr>
                    <td>".$count."</td>
                    <td>".$employeeid."</td>
                    <td>".$infos["name"]."</td>
                    <td>".$infos["amount"]."</td>";

                if($tag != 'DEDUCTION'){$info .= "<td>".$infos["balance"]."</td>";}

                $info .= "</tr>";

                $count += 1;
                $sub_total += $infos["amount"];
                $grand_total += $infos["amount"];
                $sub_bal += $infos["balance"];
                $grand_bal += $infos["balance"];
                // $row = writeTable($sheet, $table_content, $row, $col);
            }

            //        writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
            // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
            // if($tag != "DEDUCTION") writeContent($sheet, number_format($sub_bal, 2), $normalBold, true, ($row - 1), 4);
        }
        
    }

           
    $row += 1; // <<< space 
}

if($grand_total){
    //        writeContent($sheet, "Grand Total : ", $normalBold, false, $row, $col, 0, 2);
    // $row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 3);
    // if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 4);   
}




$info .= "      
            </tbody>
        </table>
    </div>
</div>";
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
?>



