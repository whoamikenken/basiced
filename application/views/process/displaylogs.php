<?php

/**
 * @author Justin
 * @copyright 2015
 */

$condition = "";
$datedisplay = "";
$department_desc = $this->extras->showdepartment();

if($edata == "OLD") $tbl = "timesheet_bak"; 
else                $tbl = "timesheet";
/*
if($fv){
  $condition = " and (TRIM(a.employeeid)='{$fv}' or TRIM(a.employeecode)='{$fv}' or TRIM(CONCAT(a.LName,', ',a.FName,' ',a.MName))='{$fv}')";  
}
if($datesetfrom && $datesetto){
   $condition .=  " and (DATE(b.timein) BETWEEN '".date("Y-m-d",strtotime($datesetfrom))."' AND '".date("Y-m-d",strtotime($datesetto))."' OR DATE(b.timeout) BETWEEN '".date("Y-m-d",strtotime($datesetfrom))."' AND '".date("Y-m-d",strtotime($datesetto))."') ";
   if(date("Y-m",strtotime($datesetfrom))==date("Y-m",strtotime($datesetto))) $datedisplay = date("F d-",strtotime($datesetfrom)) . date("d Y",strtotime($datesetto));
   else if(date("Y-m-d",strtotime($datesetfrom))!=date("Y-m-d",strtotime($datesetto))) $datedisplay = date("F d, Y - ",strtotime($datesetfrom)) . date("F d, Y",strtotime($datesetto));
   else $datedisplay = date("F d, Y",strtotime($datesetfrom));
}else if(!$datesetfrom && $datesetto){
   $condition .=  " and (DATE(b.timein)='".date("Y-m-d",strtotime($datesetto))."' OR DATE(b.timeout)='".date("Y-m-d",strtotime($datesetto))."')"; 
   $datedisplay = date("F d, Y",strtotime($datesetto));
}else if($datesetfrom && !$datesetto){
   $condition .=  " and (DATE(b.logtime)='".date("Y-m-d",strtotime($datesetfrom))."' OR DATE(b.timeout)='".date("Y-m-d",strtotime($datesetfrom))."')"; 
   $datedisplay = date("F d, Y",strtotime($datesetfrom));
}
if($deptid){
   $condition .= " and a.deptid='{$deptid}'";   
}
$sql = $this->db->query("SELECT a.employeeid,CONCAT(a.lname,', ',a.fname,' ',a.mname) AS Fullname, a.deptid,c.description as department,
                            IF( 
                               IF(IFNULL(b.timein,'')='0000-00-00 00:00:00','',IFNULL(b.timein,''))='',
                               IF(IFNULL(b.timeout,'')='0000-00-00 00:00:00','',IFNULL(b.timeout,'')),
                               IF(IFNULL(b.timein,'')='0000-00-00 00:00:00','',IFNULL(b.timein,''))   
                            )
                            as logdate,                       
                            IFNULL(CONCAT(e.cdate,' ',e.starttime),IF(IFNULL(b.timein,'')='0000-00-00 00:00:00','',IFNULL(b.timein,''))) AS log_IN,
                            IFNULL(CONCAT(e.cdate,' ',e.endtime),IF(IFNULL(b.timeout,'')='0000-00-00 00:00:00','',IFNULL(b.timeout,''))) AS log_OUT
                         FROM (SELECT a.userid,a.timein,a.timeout,a.mac_add_in,a.mac_add_out FROM $tbl a 
                               UNION 
                               SELECT b.userid,b.timein,'' AS timeout,b.mac_add AS mac_add_in,'' AS mac_add_out FROM timesheet_history b) b
                               LEFT JOIN 
								(SELECT employeeid, cdate, remarks, starttime, endtime FROM employee_schedule_adjustment) AS e
                                ON b.userid = e.employeeid AND DATE(b.timein) = e.cdate
                         INNER JOIN employee a ON a.employeeid=b.userid
                         INNER JOIN code_office c ON c.code=a.deptid
                         WHERE b.userid<>'' AND IF(FIND_IN_SET(IFNULL(a.dateresigned,''),'1970-01-01,0000-00-00,'),(1=1),(a.dateresigned>b.timein)){$condition}
                         GROUP BY a.employeeid, DATE(log_IN) ORDER BY department,Fullname,log_IN,log_OUT")->result_array();
*/
if($fv){
      $condition = " and (TRIM(mtable.employeeid)='{$fv}' or TRIM(mtable.employeecode)='{$fv}' or TRIM(CONCAT(mtable.lname,', ',mtable.lname,' ',mtable.lname))='{$fv}')";  
    }
    if($datesetfrom && $datesetto){
       if(date("Y-m",strtotime($datesetfrom))==date("Y-m",strtotime($datesetto))) $datedisplay = date("F d-",strtotime($datesetfrom)) . date("d Y",strtotime($datesetto));
       else if(date("Y-m-d",strtotime($datesetfrom))!=date("Y-m-d",strtotime($datesetto))) $datedisplay = date("F d, Y - ",strtotime($datesetfrom)) . date("F d, Y",strtotime($datesetto));
       else $datedisplay = date("F d, Y",strtotime($datesetfrom));
    }else if(!$datesetfrom && $datesetto){
       $datedisplay = date("F d, Y",strtotime($datesetto));
    }else if($datesetfrom && !$datesetto){
       $datedisplay = date("F d, Y",strtotime($datesetfrom));
    }
    if($deptid){
       $condition .= " and mtable.deptid='{$deptid}'";   
    }
$sql = $this->db->query("SELECT mtable.employeeid,mtable.employeecode,CONCAT(mtable.lname,', ',mtable.fname,' ',mtable.mname) AS Fullname,mtable.deptid,c.description AS department,DATE(mtable.dte) AS logdate,
                            IF(IFNULL(d.timein,'')='0000-00-00 00:00:00','',IFNULL(d.timein,'')) AS log_IN,
                            IF(IFNULL(d.timeout,'')='0000-00-00 00:00:00','',IFNULL(d.timeout,'')) AS log_OUT
                            FROM 
                                (SELECT a.employeeid,a.employeecode,a.dateresigned,a.lname,a.fname,a.mname,a.deptid,e.dte
                                FROM employee a, 
                                (SELECT '".date("Y-m-d",strtotime($datesetfrom))."' + INTERVAL a + b + c DAY dte
                                FROM 
                                 (SELECT 0 a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 ) d, 
                                 (SELECT 0 b UNION SELECT 10 UNION SELECT 20 UNION SELECT 30 UNION SELECT 40 UNION SELECT 60 UNION SELECT 70 UNION SELECT 80 UNION SELECT 90) m , 
                                 (SELECT 0 c UNION SELECT 100 UNION SELECT 200 UNION SELECT 300 UNION SELECT 400 UNION SELECT 600 UNION SELECT 700 UNION SELECT 800 UNION SELECT 900) h 
                                WHERE ('".date("Y-m-d",strtotime($datesetfrom))."' + INTERVAL a + b + c DAY) <= '".date("Y-m-d",strtotime($datesetto))."' ORDER BY a + b + c) e) mtable
                                LEFT JOIN 
                                (SELECT a.userid,a.timein,a.timeout,a.mac_add_in,a.mac_add_out FROM $tbl a 
                                   UNION 
                                   SELECT b.userid,b.timein,'' AS timeout,b.mac_add AS mac_add_in,'' AS mac_add_out FROM timesheet_history b)
                                 d ON d.userid=mtable.employeeid AND (DATE(d.timein)=mtable.dte OR DATE(d.timeout)=mtable.dte) 
                            LEFT JOIN code_office c ON c.code=mtable.deptid
                            WHERE mtable.employeeid<>'' AND IF(FIND_IN_SET(IFNULL(mtable.dateresigned,''),'1970-01-01,0000-00-00,'),(1=1),(mtable.dateresigned>mtable.dte)){$condition}
                            GROUP BY mtable.employeeid,DATE(mtable.dte) ORDER BY department,Fullname,DATE(mtable.dte)")->result_array();
$count_result = count($sql);                          
if($count_result>0){
$noc = 2;
$maxe = 10;
$tmax = $maxe;
if($count_result>($maxe*$noc)){
  $tmax = floor($count_result/$noc) + (($count_result % $noc)>$noc ? round(($count_result % $noc)/$noc) : ($count_result % $noc));   
}

$cn = 1;
$gad_date = array();
foreach($sql as $mrow){
  $compdate = date("Y-m-d",strtotime($mrow['logdate']));  
  if(!in_array($compdate,$gad_date)) array_push($gad_date,$compdate);
}
array_multisort($gad_date);
?>
<div id="displaylogs" class="well-content" style='border: transparent !important;'>
<h2>Attendance Summary</h2>
<p><?=$datedisplay?></p>
<?if($deptid){?>
<p>Department : <?=$department_desc[$deptid]?></p>
<?}?>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
        <tr>
            <th rowspan="2" class="sorting_asc">Employee ID</th>
            <th rowspan="2">Name</th>
            <?foreach($gad_date as $date){?><th colspan="2"><?=date("d-M Y (l)",strtotime($date))?></th><?}?>
        </tr>
        <tr>
            <?foreach($gad_date as $date){?><th>IN</th><th>OUT</th><?}?>
        </tr>
    </thead>
    <tbody id="employeelist">
<?    
$cr = 0;$emp = "";$lt = 0;$lastdate = "";$dept_g = "";$col = 2;
foreach($sql as $mrow){
    if($emp!=$mrow['employeeid']){
        if($emp!=""){
            foreach($gad_date as $dateset){
               if($lastdate!="" && $lastdate<$dateset){
            ?>       
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
            <?    
               }else if($lastdate==""){
            ?>       
                   <td style="text-align: center;">&nbsp;</td>
                   <td style="text-align: center;">&nbsp;</td>
            <?    
               }
            }   
            $lastdate = "";    
            ?></tr><?
        }    
        
        /** display department */
        if($dept_g != $mrow['department']){
            foreach($gad_date as $date){ $col+=2;}
        ?>
          <tr>
            <td colspan="<?=$col?>"><b><?=$mrow['department']?></b></td>
          </tr>
        <?      
        }
        $cc = 0;
        ?>
            <tr>
               <td><?=$mrow['employeeid']?></td>  
               <td><?=$mrow['Fullname']?></td>
        <?
    }
    foreach($gad_date as $dateset){
       $compdate = date("Y-m-d",strtotime($mrow['logdate']));  
       if($dateset==$compdate){
          
       $time_in = $mrow['log_IN'];
       $time_out = $mrow['log_OUT'];   
       
       if($this->time->getLeave($dateset,$mrow['employeeid'],$arraytime)){
        list($time_in,$time_out,$isleave)=$arraytime;
       }
    ?>       
           <td style="text-align: center;"><?=($time_in!="" ? date("h:i A",strtotime($time_in)) : "---")?></td>
           <td style="text-align: center;"><?=($time_out!="" ? date("h:i A",strtotime($time_out)) : "---")?></td>
    <?
        $lastdate = $dateset;
       }else if($lastdate==""){
    ?>       
           <td style="text-align: center;">&nbsp;</td>
           <td style="text-align: center;">&nbsp;</td>
    <?    
       }else if($lastdate<$dateset && $dateset<$compdate){
    ?>       
           <td style="text-align: center;">&nbsp;</td>
           <td style="text-align: center;">&nbsp;</td>
    <?
        $lastdate = $dateset;
       }
    }
    $dept_g = $mrow['department'];
    $emp = $mrow['employeeid'];
    $lt = $lt==0?1:0;
    $cr++;    
}

/** Just end all td */
foreach($gad_date as $dateset){
   if($lastdate!="" && $lastdate<$dateset){ 
?>       
       <td style="text-align: center;">&nbsp;</td>
       <td style="text-align: center;">&nbsp;</td>
<?    
   }else if($lastdate==""){ 
?>       
       <td style="text-align: center;">&nbsp;</td>
       <td style="text-align: center;">&nbsp;</td>
<?    
   }
} 
?>
    </tr>
    </tbody>
</table>
</div>
<?
}else{
?>
NO RECORD FOUND ...
<?    
}                         
?>