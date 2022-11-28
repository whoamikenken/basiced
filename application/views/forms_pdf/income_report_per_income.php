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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>INCOME REPORT(Per Income)</strong></span></td>
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
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>CUT-OFF</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>STATUS</th>
            ";
$info .= "</thead>";
$info .= "<tbody>";

$grand_total = 0;
foreach ($data_list as $code => $list) { // <<< displayed content
    // $row = writeContent($sheet, $income_list[$code]["description"], $bold, false, $row, 1, 0, 2);

    ksort($list);
    $count = 1;
    foreach ($list as $sort_key => $employee_list) {
        $sub_total = 0;

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

                    foreach ($employee_list_acad[$c_code] as $employeeid => $infos) {
                        $info .= "<tr>
                                        <td>".$count."</td>
                                        <td>".$employeeid."</td>
                                        <td>".strtoupper($infos["name"])."</td>
                                        <td>".$infos["amount"]."</td>
                                        <td>".date('M d-',strtotime($cutoff_start)) . date('d, Y',strtotime($cutoff_end))."</td>
                                        <td>".$infos["status"]."</td>
                                </tr>";

                        $count += 1;
                        $sub_total += $infos["amount"];
                        $grand_total += $infos["amount"];
                    }
                    
                    //        writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
                    // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
                    // $info .= "<tr>
                    //                     <td>".$count."</td>
                    //                     <td>".$employeeid."</td>
                    //             </tr>";
                }
            }
        }else{
            // if($sort_by == "department") $row = writeContent($sheet, $department[$sort_key], $bold, true, $row, 1, 0); 

            foreach ($employee_list as $employeeid => $infos) {
                $info .= "<tr>
                                        <td>".$count."</td>
                                        <td>".$employeeid."</td>
                                        <td>".strtoupper($infos["name"])."</td>
                                        <td>".$infos["amount"]."</td>
                                        <td>".date('M d-',strtotime($cutoff_start)) . date('d, Y',strtotime($cutoff_end))."</td>
                                        <td>".$infos["status"]."</td>
                                </tr>";

                $count += 1;
                $sub_total += $infos["amount"];
                $grand_total += $infos["amount"];
                // $row = writeTable($sheet, $table_content, $row, $col);
            }

            //        writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 2);
            // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
        }
        
    }

           
    $row += 1; // <<< space 
}

if($grand_total){
    //        writeContent($sheet, "Grand Total : ", $normalBold, false, $row, $col, 0, 2);
    // $row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 3);    
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



