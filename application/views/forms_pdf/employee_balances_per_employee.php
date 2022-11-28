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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>EMPLOYEE BALANCE(Per Employee) REPORT </strong></span></td>
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
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>".$tag_."</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>AMOUNT</th>
            ";
if ($tag != 'DEDUCTION') {
    $info .= "<th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>BALANCE</th>";
}
$info .= "</thead>";
$info .= "<tbody>";

$grand_total = $grand_bal = 0;
foreach ($data_list as $sort_key => $employee_list) {
    $count = 1;
    $department_total = $department_bal = 0;
    
    if($sort_key == "ACAD"){ /// <<< for ACAD only
        $employee_list_acad = array();

        foreach ($campus as $campusid => $description) { /// <<< set campus group
            foreach ($employee_list as $employeeid => $infos) {
                if($campusid == $infos["campusid"]) $employee_list_acad[$campusid][$employeeid] = $infos;
            }
        }

        foreach ($campus as $campusid => $description) {
            if(count($employee_list_acad[$campusid]) > 0) $info .= "<tr>".$department[$sort_key] ." - ". $description."</tr>";//$row = writeContent($sheet, $department[$sort_key] ." - ". $description, $bold, false, $row, $col, 0, $end_col);

            if(count($employee_list_acad[$campusid]) > 0){
                foreach ($employee_list_acad[$campusid] as $employeeid => $infos) {
                    $is_first = true;
                    $sub_total = $sub_bal = 0;

                    foreach ($infos["loan_deduc_list"] as $code => $ld_info) {
                        $idx   = ($is_first) ? $count : "";
                        $empid = ($is_first) ? $employeeid : "";
                        $name  = ($is_first) ? strtoupper($infos["name"]) : "";

                        // $table_content = array(
                        //     array($idx, $normalcenter, false, 20),
                        //     array($empid, $normalcenter, true, 30),
                        //     array($name, $normal, false, 50),
                        //     array($config[$code], $normal, false, 30),
                        //     array(number_format($ld_info["amount"], 2), $normalright, true, 25),
                        // );
                        
                        // if($tag != "DEDUCTION")  $table_content[] = array(number_format($ld_info["balance"], 2), $normalright, true, 25);
                        // $row = writeTable($sheet, $table_content, $row, $col);
                        $info .= "<tr>
                                    <td>".$idx."</td>
                                    <td>".$empid."</td>
                                    <td>".$name."</td>
                                    <td>".$config[$code]."</td>
                                    <td>".$ld_info["amount"]."</td>";

                        if($tag != 'DEDUCTION'){$info .= "<td>".$ld_info["balance"]."</td>";}

                        $info .= "</tr>";

                        $sub_total          += $ld_info["amount"];  
                        $grand_total        += $ld_info["amount"];
                        $department_total   += $ld_info["amount"];

                        $sub_bal            += $ld_info["balance"]; 
                        $grand_bal          += $ld_info["balance"];
                        $department_bal     += $ld_info["balance"];
                        $is_first = false;  
                    }

                    if(!$display_subtotal){
                        // writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 3);
                        // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);

                    }

                    // if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 5);
                    $count += 1;
                }
            }
        }
    }else{
        if($sort_by == "department") $info .= "<tr><td colspan='5' style='text-align: left!important; font-weight: bold;'>".$department[$sort_key]."</td></tr>";//$row = writeContent($sheet, $department[$sort_key], $bold, false, $row, $col, 0, $end_col);

        foreach ($employee_list as $employeeid => $infos) {
            $is_first = true;
            $sub_total = $sub_bal = 0;

            foreach ($infos["loan_deduc_list"] as $code => $ld_info) {
                $idx   = ($is_first) ? $count : "";
                $empid = ($is_first) ? $employeeid : "";
                $name  = ($is_first) ? strtoupper($infos["name"]) : "";

                // $table_content = array(
                //     array($idx, $normalcenter, false, 20),
                //     array($empid, $normalcenter, true, 30),
                //     array($name, $normal, false, 50),
                //     array($config[$code], $normal, false, 30),
                //     array(number_format($ld_info["amount"], 2), $normalright, true, 25)
                // );
                
                // if($tag != "DEDUCTION")  $table_content[] = array(number_format($ld_info["balance"], 2), $normalright, true, 25);
                // $row = writeTable($sheet, $table_content, $row, $col);

                $info .= "<tr>
                            <td>".$idx."</td>
                            <td>".$empid."</td>
                            <td>".$name."</td>
                            <td>".$config[$code]."</td>
                            <td>".$ld_info["amount"]."</td>";

                if($tag != 'DEDUCTION'){$info .= "<td>".$ld_info["balance"]."</td>";}

                $info .= "</tr>";

                $sub_total          += $ld_info["amount"];  
                $grand_total        += $ld_info["amount"];
                $department_total   += $ld_info["amount"];

                $sub_bal            += $ld_info["balance"]; 
                $grand_bal          += $ld_info["balance"];
                $department_bal     += $ld_info["balance"];
                $is_first = false;  
            }
            
            if(!$display_subtotal){
                // writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 3);
                // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);
            }

            // if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 5);

            $count += 1;
        }
    }

    if($sort_by == "department"){
               // writeContent($sheet, "Department Total : ", $normalBold, false, $row, $col, 0, 3);
        // $row = writeContent($sheet, number_format($department_total, 2), $normalBold, true, $row, 4);
        // if($tag != "DEDUCTION") writeContent($sheet, number_format($department_bal, 2), $normalBold, true, ($row - 1), 5);
    }
}

// $row += 1;
       // writeContent($sheet, "Grand Total : ", $normalBold, false, $row, $col, 0, 3);
// $row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 4);
// if($tag != "DEDUCTION") writeContent($sheet, number_format($grand_bal, 2), $normalBold, true, ($row - 1), 5);

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



