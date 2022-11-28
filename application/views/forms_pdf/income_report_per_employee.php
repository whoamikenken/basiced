<?php
// Kennedy

require_once(APPPATH."constants.php");

$cdata = $result;

$extracol = "";
$isactive    = isset($isactive) ? $isactive : '';
// $cdata = $this->reports->allEmpByPosition($isactive); 
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>INCOME REPORT(Per Employee)</strong></span></td>
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
            <th style='padding: 5px;text-align: center;font-size: 12px;font-weight: bold;'>#</th>
            <th style='padding: 5px;text-align: center;font-size: 12px;font-weight: bold;'>EMPLOYEE ID</th>
            <th style='padding: 5px;text-align: center;font-size: 12px;font-weight: bold;'>EMPLOYEE NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 12px;font-weight: bold;'>INCOME</th>
            <th style='padding: 5px;text-align: center;font-size: 12px;font-weight: bold;'>AMOUNT</th>
            <th style='padding: 5px;text-align: center;font-size: 12px;font-weight: bold;'>CUT-OFF</th>
            <th style='padding: 5px;text-align: center;font-size: 12px;font-weight: bold;'>STATUS</th>
            </tr>";
$info .= "</thead>";
$info .= "<tbody>";

$grand_total = 0;

foreach ($data_list as $sort_key => $employee_list) {
    $count = 1;
    $department_total = 0;
    
    if($sort_key == "ACAD"){ /// <<< for ACAD only
        $employee_list_acad = array();

        foreach ($campus as $campusid => $description) { /// <<< set campus group
            foreach ($employee_list as $employeeid => $infos) {
                if($campusid == $infos["campusid"]) $employee_list_acad[$campusid][$employeeid] = $infos;
            }
        }

        foreach ($campus as $campusid => $description) {
            // if(count($employee_list_acad[$campusid]) > 0) $row = writeContent($sheet, $department[$sort_key] ." - ". $description, $boldColorBlue, false, $row, $col, 0, $end_col);

            if(count($employee_list_acad[$campusid]) > 0){
                foreach ($employee_list_acad[$campusid] as $employeeid => $infos) {
                    $is_first = true;
                    $sub_total = 0;

                    foreach ($infos["income"] as $code => $amount) {
                        $idx   = ($is_first) ? $count : "";
                        $empid = ($is_first) ? $employeeid : "";
                        $name  = ($is_first) ? strtoupper($infos["name"]) : "";

                        // $table_content = array(
                        //     array($idx, $normalcenter, false, 20),
                        //     array($empid, $normalcenter, true, 30),
                        //     array($name, $normal, false, 50),
                        //     array($income_list[$code]["description"], $normal, false, 30),
                        //     array(number_format($amount, 2), $normalright, true, 25)
                        // );
                        // $info .= "<tr>
                        //                 <td>".$idx."</td>
                        //                 <td>".$empid."</td>
                        //                 <td>".$name."</td>
                        //                 <td>".$income_list[$code]["description"]."</td>
                        //                 <td>".$amount."</td>
                        //         </tr>";

                            $info .= "<tr>
                                <td>".$idx."</td>
                                <td>".$empid."</td>
                                <td>".$name."</td>
                                <td>".$income_list[$code]["description"]."</td>
                                <td>".$amount."</td>
                                <td>".date('M d-',strtotime($cutoff_start)) . date('d, Y',strtotime($cutoff_end))."</td>
                                <td>".$infos["status"]."</td>
                        </tr>";
                        
                        // $row = writeTable($sheet, $table_content, $row, $col);

                        $sub_total          += $amount; 
                        $grand_total        += $amount;
                        $department_total   += $amount;
                        $is_first = false;  
                    }

                    if($is_select_all){
                        //        writeContent($sheet, "Sub Total : ", $normalBold, false, $row, $col, 0, 3);
                        // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 4);
                    }

                    $count += 1;
                }
            }
        }
    }else{

        if($sort_by == "department"){
            $info .= "<tr>
                                    <td colspan='7'>".$department[$sort_key]."</td>
                            </tr>";
        }
            
        foreach ($employee_list as $employeeid => $infos) {
            $is_first = true;
            $sub_total = 0;
            // $table_content = array(
            //         array($count, $normalcenter, false, 20),
            //         array($employeeid, $normalcenter, true, 30),
            //         array(strtoupper(utf8_decode($infos["name"])), $normal, false, 50)
            // );
            // $row = writeTable($sheet, $table_content, $row, $col);

            foreach ($infos["income"] as $code => $amount) {
                $idx   = ($is_first) ? $count : "";
                $empid = ($is_first) ? $employeeid : "";
                $name  = ($is_first) ? strtoupper($infos["name"]) : "";

                // $table_content = array(
                //     array($empid, $normalcenter, true, 30),
                //     array($name, $normal, false, 50),
                //     array($income_list[$code]["description"], $normal, false, 30),
                //     array(number_format($amount, 2), $normalright, true, 25),
                //     array(date('M d-',strtotime($cutoff_start)) . date('d, Y',strtotime($cutoff_end)), $normalright, true, 25),
                //     array($info["status"], $normalright, true, 25)
                // );
                $info .= "<tr>
                                <td>".$idx."</td>
                                <td>".$empid."</td>
                                <td>".$name."</td>
                                <td>".$income_list[$code]["description"]."</td>
                                <td>".$amount."</td>
                                <td>".date('M d-',strtotime($cutoff_start)) . date('d, Y',strtotime($cutoff_end))."</td>
                                <td>".$infos["status"]."</td>
                        </tr>";
                
                // $row = writeTable($sheet, $table_content, $row, $col);

                $sub_total          += $amount; 
                $grand_total        += $amount;
                $department_total   += $amount;
                $is_first = false;  
            }
            
            if($is_select_all){
                //        writeContent($sheet, "TOTAL : ", $normalBold, false, $row, $col, 0, 2);
                // $row = writeContent($sheet, number_format($sub_total, 2), $normalBold, true, $row, 3);
                $info .= "<tr>
                                    <td colspan='4' style='text-align:right; '><b>TOTAL :</b></td>
                                    <td ><b>".number_format($sub_total, 2)."</b></td>
                                    <td colspan='2'></td>
                            </tr>";
            }

            $count += 1;
        }
    }

    if($sort_by == "department"){
        //        writeContent($sheet, "DEPARTMENT TOTAL : ", $normalBold, false, $row, $col, 0, 2);
        // $row = writeContent($sheet, number_format($department_total, 2), $normalBold, true, $row, 3);
        $info .= "<tr>
                                    <td colspan='4' style='text-align:right; '><b>DEPARTMENT TOTAL :</b></td>
                                    <td ><b>".number_format($department_total, 2)."</b></td>
                                    <td colspan='2'></td>
                            </tr>";
    }
}

$row += 1;
//        writeContent($sheet, "GRAND TOTAL : ", $normalBold, false, $row, $col, 0, 2);
// $row = writeContent($sheet, number_format($grand_total, 2), $normalBold, true, $row, 3);
$info .= "<tr>
                                    <td colspan='4' style='text-align:right; '><b>GRAND TOTAL :</b></td>
                                    <td ><b>".number_format($grand_total, 2)."</b></td>
                                    <td colspan='2'></td>
                            </tr>";


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



