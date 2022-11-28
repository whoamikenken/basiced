<?php

/**
 * @author Justin
 * @copyright 2016
 */

$from_date  = $_GET['datesetfrom'];
$to_date    = $_GET['datesetto'];
$dept = $_GET['deptid'];
$empid    = $_GET['fv'];
$tnt    = $_GET['tnt'];
$estatus= $_GET['estatus'];

$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept, $tnt, $estatus);

$pdf = new PdfCreator_mpdf();
$pdf->Bookmark('Start of the document');

$info = '
    <html>
        <head>
            <style>
            @page:first{
                header: html_header;
                footer: html_rfooter;
            }
            @page{
                header: html_othpage;
                footer: html_rfooter;
                margin: 4%;
                margin-top: 6.75cm; 
            }
            @page rotated { size: landscape; }
            @page noheader {
                odd-header-name: _blank;
                odd-footer-name: _blank;
                margin-top: 2%; 
            }
            div.noheader {
                page-break-before: always;
                page: noheader;
            }
            div.content{
                font-size: 12px;
            }
            .center{
                text-align: center;    
            }
            .theader{
                vertical-align: middle;
                font-family: Arial;
                font-size: 9pt;
                margin-top: 10px;
                border-collapse: collapse;
            }
            .rheader{
                vertical-align: middle;
                font-family: Arial;
                font-size: 9pt;
                margin-top: 10px;
                border-collapse: collapse;
            }
            .info{
                font-size: 11px;
            }
            .rheader tr:nth-child(even) td {background: #CCC}
            .rheader tr:nth-child(odd) td {background: #FFF}
            </style>
        </head>
        <body>
        
            <sethtmlpageheader name="header" value="on" show-this-page="1" />
            <sethtmlpageheader name="othpage" value="on" />
            <sethtmlpagefooter value="off" />
            
            <htmlpageheader name="header">
            	<table style="vertical-align: middle; font-family: serif; font-size: 9pt; color: #000088;" width="100%">
            		<tbody>
            			<tr>
            				<td width="10%" rowspan=3><img src="'.base_url().'/images/school_logo.jpg" height="85" width="85" /></td>
                            <td class="center"><h2>M A N I L A&nbsp;&nbsp;C E N T R A L&nbsp;&nbsp;U N I V E R S I T Y</h2></td>
                            <td width="12%" rowspan=3><img src="'.base_url().'/images/school_logo.jpg" height="85" width="85" /></td>
            			</tr>
                        <tr>
                            <td class="center"><h2 style="color: #C6BC00">Human Resources Department</h2></td>
            			</tr>
                        <tr>
                            <td class="center"><h2>Caloocan City</h2></td>
            			</tr>
            		</tbody>
            	</table>
                <table class="theader" width="100%" cellpadding=3>
                    <tr>
                        <td><h2>Payroll Report</h2></td>                
                    </tr>
                    <tr>
                        <th style="font-weight: normal;text-align: left;"><small>'.date("F d, Y",strtotime($from_date)).' to '.date("F d, Y",strtotime($to_date)).'</small></th>                
                    </tr>
                </table><br />
                <table class="theader" width="100%" cellpadding=3>
                        <tr>
                            <td>
                                <p>Prepared by: ______________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Approved by: ______________________________</p><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                JOIE S. CUYA
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                FLOR M. CASTILLO
                                </span>
                            </td> 
                        </tr>
                        <tr>
                            <td>
                                <span>                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                HR Assistant
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                HRD Director
                                </span>
                            </td> 
                        </tr>
                    </table>                
                </htmlpageheader>
                
                <htmlpageheader name="othpage" style="display:none">
                    <table style="vertical-align: middle; font-family: serif; font-size: 9pt; color: #000088;" width="100%">
            		<tbody>
            			<tr>
            				<td width="10%" rowspan=3><img src="'.base_url().'/images/school_logo.jpg" height="85" width="85" /></td>
                            <td class="center"><h2>M A N I L A&nbsp;&nbsp;C E N T R A L&nbsp;&nbsp;U N I V E R S I T Y</h2></td>
                            <td width="12%" rowspan=3><img src="'.base_url().'/images/school_logo.jpg" height="85" width="85" /></td>
            			</tr>
                        <tr>
                            <td class="center"><h2 style="color: #C6BC00">Human Resources Department</h2></td>
            			</tr>
                        <tr>
                            <td class="center"><h2>Caloocan City</h2></td>
            			</tr>
            		</tbody>
                	</table>
                    <table class="theader" width="100%" cellpadding=3>
                        <tr>
                            <td><h2>Payroll Report</h2></td>                
                        </tr>
                        <tr>
                            <th style="font-weight: normal;text-align: left;"><small>'.date("F d, Y",strtotime($from_date)).' to '.date("F d, Y",strtotime($to_date)).'</small></th>                
                        </tr>
                    </table>
                    <table class="rheader" width="100%" border=1 cellpadding=3>
                    <tr>
                        <th rowspan=3 width="25%">NAME OF FACULTY</th>
                        <th colspan=2>LATE/UT</th>
                        <th rowspan=3>ABSENCES</th>
                        <th rowspan=3>EMERGENCY LEAVE</th>
                        <th rowspan=3>VACATION LEAVE</th>
                        <th rowspan=3>SICK LEAVE</th>
                        <th rowspan=3>REMARKS</th>
                        <th colspan=2>TOTAL</th>                
                    </tr>
                    <tr>
                        <th>LEC</th>
                        <th>LAB</th>
                        <th colspan=2>DEDUCTION</td>                
                    </tr>
                    <tr>
                        <th>hr:min</th>
                        <th>hr:min</th>
                        <th>LEC</th>
                        <th>LAB</th>                
                    </tr>
                    </table>

                </htmlpageheader>
                
                <!--<pagefooter name="footer" content-left="{DATE j-m-Y}" content-center="{PAGENO}/{nbpg}" footer-style="font-size: 8pt;" />-->
                <htmlpagefooter name="rfooter">
                	<table class="theader" width="100%" cellpadding=3>
                        <tr>
                            <td>
                                <p>Prepared by: ______________________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Approved by: ______________________________</p><br />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span>                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                JOIE S. CUYA
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                FLOR M. CASTILLO
                                </span>
                            </td> 
                        </tr>
                        <tr>
                            <td>
                                <span>                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                HR Assistant
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                HRD Director
                                </span>
                            </td> 
                        </tr>
                    </table>
                </htmlpagefooter>
                ';

if($tnt == "teaching"){ // teaching
    $info .= '
            <div class="content">
            <table class="rheader" width="100%" border=1 cellpadding=3>
                    <tr>
                        <th rowspan=3 width="25%">NAME OF FACULTY</th>
                        <th colspan=2>LATE/UT</th>
                        <th rowspan=3>ABSENCES</th>
                        <th rowspan=3>EMERGENCY LEAVE</th>
                        <th rowspan=3>VACATION LEAVE</th>
                        <th rowspan=3>SICK LEAVE</th>
                        <th rowspan=3>REMARKS</th>
                        <th colspan=2>TOTAL</th>                
                        <th rowspan=3>Holiday</th>
                    </tr>
                    <tr>
                        <th>LEC</th>
                        <th>LAB</th>
                        <th colspan=2>DEDUCTION</td>                
                    </tr>
                    <tr>
                        <th>hr:min</th>
                        <th>hr:min</th>
                        <th>LEC</th>
                        <th>LAB</th>                
                    </tr>
                    ';
    if (count($result) > 0) {
    	$deptDisplay = "";
    	foreach ($result as $key => $data) {
    		$empid = $data["qEmpId"];
    		$empFullname = $data["qFullname"];
            
            $deptid = $this->employee->getindividualdept($empid);
            
            $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
                $x = $tlec = $tlab = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = ""; 
                foreach ($qdate as $rdate) {
                    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                    $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                    $countrow = $sched->num_rows();
                    
                    if($countrow > 0){
                        $tempsched = "";
                        foreach($sched->result() as $rsched){
                            if($tempsched == $dispLogDate)  $dispLogDate = "";
                            $stime = $rsched->starttime;
                            $etime = $rsched->endtime; 
                            $type  = $rsched->leclab;
                            
                            // Holiday
                            $holiday = $this->attcompute->isHoliday($rdate->dte); 
                            
                            // logtime
                            list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                            
                            // Leave
                            list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                            
                            // Absent
                            $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte);
                            if($el || $vl || $sl || $ol || $holiday) $absent = "";
                            
                            // Late / Undertime
                            list($lateutlec,$lateutlab,$tschedlec,$tschedlab) = $this->attcompute->displayLateUT($stime,$etime,$login,$logout,$type,$absent);
                    
                            /*
                             * Total
                             */ 
                            
                            // Absent
                            $tabsent  += $absent;
                            
                            // Late / UT
                            if($tlec){
                                $secs  = strtotime($lateutlec)-strtotime("00:00:00");
                                if($secs>0) $tlec = date("H:i",strtotime($tlec)+$secs);
                            }else
                                $tlec    = $lateutlec;
                                
                            if($tlab){
                                $secs  = strtotime($lateutlab)-strtotime("00:00:00");
                                if($secs>0) $tlab = date("H:i",strtotime($tlab)+$secs);
                            }else
                                $tlab    = $lateutlab;
                            
                            // Deductions
                            if($tschedlec){
                            $tdlec += $this->attcompute->exp_time($tschedlec);
                            }
                            if($tschedlab){
                                $tdlab += $this->attcompute->exp_time($tschedlab);
                            }
                            
                        }   // end foreach
                        
                        // total holiday
                        $tholiday += $holiday;
                        
                        // Leave
                        if($dispLogDate){
                            $tel      += $el;
                            $tvl      += $vl;
                            $tsl      += $sl;
                            $tol      += $ol;
                        } // end if
                        
                    } // end if
                    
                } // end foreach
            
    $tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
    $tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");

  		  if ($deptDisplay != $data["qDepartment"]) {		  
    $info .= '<tr style="background: #CCC;"><td colspan="11"><b><i>Department: '.$data["qDepartment"].'</i></b></td></tr>';
    		}
    $info .= "        
    	<tr class='idata'>
    		<td class='info'>$empFullname</td>
            <td class='info center'>$tlec</td>
            <td class='info center'>$tlab</td>
            <td class='info center'>$tabsent</td>
            <td class='info center'>$tel</td>
            <td class='info center'>$tvl</td>
            <td class='info center'>$tsl</td>
            <td class='info center'>$tol</td>
            <td class='info center'>$tdlec</td>
            <td class='info center'>$tdlab</td>
            <td class='info center'>$tholiday</td>
    	</tr>
        ";
     
        		$deptDisplay = $data["qDepartment"];
        	} // end foreach
        }
     $info .= '
                </table>
            </div>                                  
            <!--<div class="noheader">no header content</div>-->
        </body>
    </html>';
}else{  
    // Non Teaching
    $info .= '
            <div class="content">
            <table class="rheader" width="100%" border=1 cellpadding=3>
                    <tr>
                        <th rowspan=2 width="25%">NAME OF FACULTY</th>
                        <th colspan=4>Overtime (hr:min)</th>
                        <th class="align_center">Late/Undertime</th>
                        <th rowspan="2">Absent</th>
                        <th colspan=3>Leaves</th>
                        <th rowspan=2>Remarks/Others</th>
                        <th rowspan=2>Holiday</th>              
                    </tr>
                    <tr>
                        <th>Regular</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                        <th>Holiday</th>
                        <th>Hr:min</th>            
                        <th>Emergency</th>
                        <th>Vacation</th>
                        <th>Sick</th>
                    </tr>    
                    ';
    if (count($result) > 0) {
    	$deptDisplay = "";
    	foreach ($result as $key => $data) {
    		$empid = $data["qEmpId"];
    		$empFullname = $data["qFullname"];
            
            $deptid = $this->employee->getindividualdept($empid);
            
            $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
            
                $x = $totr = $totsat = $totsun = $tothol =$tlec = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = ""; 
                foreach ($qdate as $rdate) {
                $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                
                if($x%2 == 0)   $color = " style='background-color: white;'";
                else            $color = " style='background-color: #f2f2f2;'";
                $x++;
                
                if($countrow > 0){
                    $tempsched = "";
                    foreach($sched->result() as $rsched){
                        if($tempsched == $dispLogDate)  $dispLogDate = "";
                        $stime  = $rsched->starttime;
                        $etime  = $rsched->endtime; 
                        $type   = $rsched->leclab;
                        $earlyd = $rsched->early_dismissal;
                        
                        // Holiday
                        $holiday = $this->attcompute->isHoliday($rdate->dte); 
                        
                        // logtime
                        list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        
                        // Overtime
                        list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                                
                        // Leave
                        list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
                        if($el || $vl || $sl || $ol || $holiday) $absent = "";
                        
                        // Late / Undertime
                        $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent);
                        if($el || $vl || $sl || $ol || $holiday)    $lateutlec = "";
                        
                        /*
                         * Total
                         */ 
                        // Absent
                        $tabsent  += $absent;
                        // Late / UT
                        if($lateutlec)  $tlec += $this->attcompute->exp_time($lateutlec);
                    }   // end foreach
                    
                    // Leave
                    if($dispLogDate){
                        $tel      += $el;
                        $tvl      += $vl;
                        $tsl      += $sl;
                        $tol      += ($ol ? 1 : "");
                    }
                    
                    // total holiday
                    $tholiday += $holiday;
                    
                    /* Overtime */
                    // total regular
                    if($otreg){
                        $totr += $this->attcompute->exp_time($otreg);
                    }
                    // total saturday
                    if($otsat){
                        $totsat += $this->attcompute->exp_time($otsat);
                    }
                    // total sunday
                    if($otsun){
                        $totsun += $this->attcompute->exp_time($otsun);
                    }
                    // total holiday
                    if($othol){
                        $tothol += $this->attcompute->exp_time($othol);
                    }
                }else{
                    /* Overtime */
                    list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                    if($otreg)  $totr += $this->attcompute->exp_time($otreg);
                    if($otsat)  $totsat += $this->attcompute->exp_time($otsat);
                    if($otsun)  $totsun += $this->attcompute->exp_time($otsun);
                    if($othol)  $tothol += $this->attcompute->exp_time($othol);
                }
            } // end foreach
      $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");         
      $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
      $totsat = ($totsat ? $this->attcompute->sec_to_hm($totsat) : ""); 
      $totsun = ($totsun ? $this->attcompute->sec_to_hm($totsun) : ""); 
      $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");
            
    
    		if ($deptDisplay != $data["qDepartment"]) {		  
    $info .= '<tr><td colspan="12"  style="background: #FFF;"><b><i>Department: '.$data["qDepartment"].'</i></b></td></tr>';
    		}
    $info .= "        
    	<tr class='idata'>
    		<td class='info'>$empFullname</td>
            <td class='info center'>$totr</td>
            <td class='info center'>$totsat</td>
            <td class='info center'>$totsun</td>
            <td class='info center'>$tothol</td>
            <td class='info center'>$tlec</td>
            <td class='info center'>$tabsent</td>
            <td class='info center'>$tel</td>
            <td class='info center'>$tvl</td>
            <td class='info center'>$tsl</td>
            <td class='info center'>$tol</td>
            <td class='info center'>$tholiday</td>
    	</tr>
        ";
        		$deptDisplay = $data["qDepartment"];
        	} // end foreach
        }
     $info .= '
                </table>';
     $info .= '
            </div>
            <!--<div class="noheader">no header content</div>-->
        </body>
    </html>';
}

$pdf->WriteHTML($info);

$pdf->Output();
/*
<sethtmlpageheader name="Header" value="on" /> <!-- Activates <htmlpageheader name="Header"> on the NEXT PDF page -->
<sethtmlpageheader name="Header" show-this-page="1" value="on" /> <!-- Activates <htmlpageheader name="Header"> on the CURRENT PDF page -->
<sethtmlpageheader value="off" /> <!-- Disables the header on the NEXT PDF page -->

<sethtmlpagefooter name="Footer" value="on" /> <!-- Activates <htmlpagefooter name="Footer"> on the NEXT PDF page -->
<sethtmlpagefooter name="Footer" show-this-page="1" value="on" /> <!-- Activates <htmlpagefooter name="Footer"> on the CURRENT PDF page -->
<sethtmlpagefooter value="off" /> <!-- Disables the footer on the NEXT PDF page -->
*/
?>

