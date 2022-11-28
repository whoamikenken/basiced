<?php

/**
 * @author Justin
 * @copyright 2014
 */

function head($dailydate){    
$html .= '<div class"img"><img src="images/CCA_logo.png" height="5%" width=50%></img></div>
<div class="header">
    
    <h2>DAILY ATTENDANCE MONITORING</h2>
    <h3>'.date('F d,Y',strtotime($dailydate)).'</h3>
</div>
<div class="body">
    <table>
        <tr>
            <th style="text-align: left;">Name</th>
            <th>ID No.</th>
            <th>Description</th>
        <!--    <td>Group</th> -->
            <th>Date</th>
            <th>IN</th>
        </tr>
    </table>
</div>';
return $html;
}

function content($dailydate){
    $html .= '
<div class="content">
';
$html .= '<table>';
$sqlcount = mysql_query("SELECT DISTINCT COUNT(userid) as total FROM timesheet_trail
                            WHERE SUBSTRING(logtime,1,10)='$dailydate'
                        ");
$countdata = mysql_fetch_array($sqlcount);                            

$sql = mysql_query("SELECT DISTINCT studentid as id,CONCAT(lname,', ',fname,' ',mname) as FULLNAME, coursecode as department FROM timesheet_trail a
                        INNER JOIN student b
                            on a.userid = b.studentid
                        WHERE SUBSTRING(logtime,1,10)='$dailydate'
                    UNION
                    SELECT DISTINCT employeeid as id,CONCAT(lname,', ',fname,' ',mname) as FULLNAME, deptid as department FROM timesheet_trail a
                        INNER JOIN employee b
                            on a.userid = b.employeeid
                        WHERE SUBSTRING(logtime,1,10)='$dailydate'");

while($data = mysql_fetch_array($sql)){

        $sql2 = mysql_query("SELECT DISTINCT COUNT(userid) as totalid FROM timesheet_trail WHERE userid='".$data['id']."' AND SUBSTR(logtime,1,10)='$dailydate'");
        $totalid = mysql_fetch_array($sql2);
        
    $html .= '<tr>';
    $html .= '<td style="text-align: left;">'.$data['FULLNAME'].'</td>';
    $html .= '<td>'.$data['id'].'</td>';
    $html .= '<td>'.$data['department'].'</td>';
    $html .= '<td>'.date('F d,Y',strtotime($dailydate)).'</td>';
    $html .= '<td>'.$totalid['totalid'].'</td>';
    $html .= '</tr>';
    
}
$html .= '<tr><td style="text-align: left;"><b>Total Count : '.$countdata['total'].'</b></td></tr>';
$html .= '</table>';
$html .= 
'</div></center></div>';

return $html;
}


?>