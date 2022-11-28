<?php
 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Longevity.xls");

    $year = date('Y');
    $year = date('Y',strtotime($year."- 1 year"));
    $campus = (isset($_GET['campus']) ? $_GET['campus'] : $_POST['campus']);
    $empid = (isset($_GET['empid']) ? $_GET['empid'] : isset($_POST['empid']));
    // echo $empids;die;
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalleft =& $xls->addFormat(array('Size' => 10));
    $normalleft->setAlign("left");
    $normalleft->setBold();
    // $normalcenter->setColumn(0,40,1);
    $normalcenter->setLocked();
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();


    $normalright =& $xls->addFormat(array('Size' => 10));
    $normalright->setHAlign("right");
    // $normalcenter->setColumn(0,40,1);
    $normalright->setLocked();

    
    
    $tardycenter =& $xls->addFormat(array('Size' => 10));
    $tardycenter->setAlign("center");
    $tardycenter->setColor("red");
    $tardycenter->setLocked();
    
    $grayBgCenter =& $xls->addFormat(array('Size' => 10));
    $grayBgCenter->setBorder(1);
    $grayBgCenter->setAlign("center");
    $grayBgCenter->setBold();
    $xls->setCustomColor(12, 192, 192, 192);
    $grayBgCenter->setBgColor(12);
    $grayBgCenter->setFgColor(12);
    $grayBgCenter->setLocked();
   

    $blueBgnormal =& $xls->addFormat(array('Size' => 10));
    $blueBgnormal->setBorder(1);
    $blueBgnormal->setHAlign('left');
    $blueBgnormal->setBold();
    $xls->setCustomColor(13, 51, 102, 255);
    $xls->setCustomColor(1, 255, 255, 255);
    $blueBgnormal->setColor(1);
    $blueBgnormal->setBgColor(13);
    $blueBgnormal->setFgColor(13);
    $blueBgnormal->setLocked();

    $blueBgnormalCenter =& $xls->addFormat(array('Size' => 10));
    $blueBgnormalCenter->setBorder(1);
    $blueBgnormalCenter->setHAlign('center');
    $blueBgnormalCenter->setBold();
    $xls->setCustomColor(13, 51, 102, 255);
    $xls->setCustomColor(1, 255, 255, 255);
    $blueBgnormalCenter->setColor(1);
    $blueBgnormalCenter->setBgColor(13);
    $blueBgnormalCenter->setFgColor(13);
    $blueBgnormalCenter->setLocked();
    
    $halfcenter =& $xls->addFormat(array('Size' => 10));
    $halfcenter->setAlign("center");
    $grayBgCenter->setBgColor("yellow");
    $halfcenter->setColor("red");
    $halfcenter->setLocked();        
    
    $tits =& $xls->addFormat(array('Size' => 10));
    $tits->setBold();
    $tits->setAlign("center");
    $tits->setLocked();
    
    $titsnormal =& $xls->addFormat(array('Size' => 10));
    $titsnormal->setAlign("center");
    $titsnormal->setLocked();
    
    $coltitle =& $xls->addFormat(array('Size' => 8));
    $coltitle->setBorder(2);
    $coltitle->setAlign("center");
    $coltitle->setBgColor(11);
    $coltitle->setFgColor(11);
    $coltitle->setLocked();
    
    $colnumber =& $xls->addFormat(array('Size' => 8));
    $colnumber->setNumFormat("#,##0.00");
    $colnumber->setBorder(1);
    $colnumber->setAlign("center");
    $coltitle->setLocked();
    
    $messbord =& $xls->addFormat(array('Size' => 8));
    $messbord->setBorder(1);
    $messbord->setAlign("center");
    $messbord->setLocked();
    
    $messbordpink =& $xls->addFormat(array('Size' => 8));
    $messbordpink->setBorder(1);
    $messbordpink->setBgColor(12);
    $messbordpink->setFgColor(12);
    $messbordpink->setAlign("center");
    $messbordpink->setLocked();
    
    $big =& $xls->addFormat(array('Size' => 12));
    $big->setLocked();
    
    $bigbold =& $xls->addFormat(array('Size' => 11));
    $bigbold->setBold();
    $bigbold->setLocked();
    
    $bigboldcenter =& $xls->addFormat(array('Size' => 12));
    $bigboldcenter->setBold();
    $bigboldcenter->setAlign("center");
    $bigboldcenter->setLocked();
    
    $bold =& $xls->addFormat(array('Size' => 8));
    $bold->setBold();
    $bold->setLocked();
    
    $boldcenter =& $xls->addFormat(array('Size' => 8));
    $boldcenter->setAlign("center");
    $boldcenter->setBold();
    $boldcenter->setLocked();
    
    $amount =& $xls->addFormat(array('Size' => 8));
    $amount->setNumFormat("#,##0.00");
    $amount->setLocked();
    
    $amountbold =& $xls->addFormat(array('Size' => 8));
    $amountbold->setNumFormat("#,##0.00_);\(#,##0.00\)");
    $amountbold->setAlign("center");
    $amountbold->setBold();
    $amountbold->setLocked();
    
    $number =& $xls->addFormat(array('Size' => 8));
    $number->setNumFormat("#,##0");
    $number->setLocked();
    
    $numberbold =& $xls->addFormat(array('Size' => 8));
    $numberbold->setNumFormat("#,##0");
    $numberbold->setBold();
    $numberbold->setLocked();
    
    $dateform =& $xls->addFormat(array('Size' => 8));
    $dateform->setNumFormat("D-MMM-YYYY");
    $dateform->setLocked();
    
    $timeform =& $xls->addFormat(array('Size' => 8));
    $timeform->setNumFormat("h:mm:ss AM/PM");
    $timeform->setLocked();
    /* END */


    
  
   

    


    $sheet = &$xls->addWorksheet("Sheet 1");
   
  
    
    $c = 0;$r = 0;
   
    $sheet->setColumn(0,0,15);
    $sheet->setColumn(0,1,30);
    $sheet->setColumn(0,2,30);
    $sheet->setColumn(0,3,30);
    $sheet->setColumn(0,4,40);
    $sheet->setColumn(0,5,30);
    $sheet->setColumn(0,6,30);
    $sheet->setColumn(0,7,35);
    $sheet->setColumn(0,8,35);
    $sheet->setColumn(0,9,30);

    $sheet->write(0,0,"Employee ID",$grayBgCenter);
    $r++;
    $sheet->write(0,1,"Employee Name",$grayBgCenter);
    $r++;
    $sheet->write(0,2,"Date Hired",$grayBgCenter);
    $r++;
    $sheet->write(0,3,"Date of Regular Appointment",$grayBgCenter);
    $r++;
    $sheet->write(0,4,"# of Credited Yrs. of Service as Regular",$grayBgCenter);
    $r++;
    $sheet->write(0,5,"Previous Basic Pay ".date("Y",strtotime("01-01-".$year."- 2 year"))." - ".date("Y",strtotime("01-01-".$year."- 1 year")),$grayBgCenter);
    $r++;
    $sheet->write(0,6,"Present Basic Pay ".date("Y",strtotime("01-01-".$year."- 1 year"))." - ".date("Y",strtotime("01-01-".$year)),$grayBgCenter);
    $r++;
    $sheet->write(0,7,"Longevity Pay Per Month ".date("Y",strtotime("01-01-".$year."- 4 year"))." - ".date("Y",strtotime($year."- 1 year")),$grayBgCenter);
    $r++;
    $sheet->write(0,8,"Longevity Pay Per Month ".date("Y",strtotime("01-01-".$year."- 1 year"))." - ".date("Y",strtotime("01-01-".$year)),$grayBgCenter);
    $r++;
    $sheet->write(0,9,"Proposed Increase Per Month",$grayBgCenter);
    $r++;


    $r = 1;
    $c = 0;

 $longevityList = $this->employee->showLongevity($year,$campus);

 $dept =$a ="";
 foreach($longevityList->result() as $row)
    {

        $id = $row->employeeid;
        $regyear = $this->employee->EmpRegularDate($id);
        $noCreditYears = $year - date("Y",strtotime($regyear));
        if ($noCreditYears == 5)
        $a = 1;
        elseif ($noCreditYears == 6) 
        $a = 2;
        elseif ($noCreditYears == 7) 
        $a = 3;
        elseif ($noCreditYears == 8) 
        $a = 4;
        elseif ($noCreditYears == 9) 
        $a = 5;
        elseif ($noCreditYears == 10) 
        $a = 6;
        elseif ($noCreditYears == 11) 
        $a = 7;
        elseif ($noCreditYears == 12) 
        $a = 8;
        elseif ($noCreditYears == 13) 
        $a = 9;
        elseif ($noCreditYears == 14) 
        $a = 10;
        elseif ($noCreditYears == 15) 
        $a = 11;
        elseif ($noCreditYears == 16) 
        $a = 12;
        elseif ($noCreditYears == 17) 
        $a = 13;
        elseif ($noCreditYears == 18) 
        $a = 14;
        elseif ($noCreditYears == 19) 
        $a = 15;
        elseif ($noCreditYears == 20) 
        $a = 16;
        elseif ($noCreditYears == 21) 
        $a = 17;
        elseif ($noCreditYears == 22) 
        $a = 18;
        elseif ($noCreditYears >= 23) 
        $a = 19;
        if ($empid == "") {
            $pcpay= round(((($this->employee->GetBasicPreviousPay($id) + $this->employee->GetBasicCurrentPay($id))/ 2)/12),2); 
            $totallongevity = round(((($pcpay * 3)*$a)/26),2);
                if($dept != $row->deptid && $noCreditYears > 5)
                {
                    $sheet->write($r,$c,$this->extras->getDeptDesc($row->deptid),$normalleft);
                    $r++;
                    $dept = $row->deptid;
                }
                if ($noCreditYears > 5 ) 
                {

                    $sheet->writeString($r,$c,$row->employeeid,$normal);
                    $c++;
                    $sheet->write($r,$c,$row->fullname,$normal);
                    $c++;
                    $sheet->write($r,$c,(date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))=='01-01-1970'?'':date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))),$normal);
                    $c++;
                    $sheet->write($r,$c,(date("m-d-Y",strtotime($this->employee->EmpRegularDate($id)))),$normal);
                    $c++;
                    $sheet->write($r,$c,($noCreditYears>=5?$noCreditYears:''),$normal);
                    $c++;
                    $sheet->write($r,$c,$this->employee->GetBasicPreviousPay($id),$normal);
                    $c++;
                    $sheet->write($r,$c,$this->employee->GetBasicCurrentPay($id),$normal);
                    $c++;
                    $sheet->write($r,$c,'',$normal);
                    $c++;
                    $sheet->write($r,$c,$totallongevity,$normal);
                    $c++;
                    $sheet->write($r,$c,$totallongevity,$normal);
                    $r++;
                    $c=0;   
                   
                }
        }
        else
        {
                $eid = explode(',', $empid);

                foreach ($eid as $key) {
                    if ($row->employeeid == $key) {
                            //COMPUTATION FOR GETTING LONGEVITY
                            $pcpay= round(((($this->employee->GetBasicPreviousPay($id) + $this->employee->GetBasicCurrentPay($id))/ 2)/12),2); 
                            $totallongevity = round(((($pcpay * 3)*$a)/26),2);
                                if($dept != $row->deptid && $noCreditYears > 5)
                                {
                                    // $datas .='<tr id="tbl"><td colspan='.$colspan.' >'.$this->extras->getDeptDesc($row->deptid).'</td></tr>';
                                    // $sheet->write($r+1,$c+1,$this->extras->getDeptDesc($row->deptid),$normal);
                                    $sheet->write($r,$c,$this->extras->getDeptDesc($row->deptid),$normalleft);
                                    $r++;
                                    $dept = $row->deptid;
                                }
                                if ($noCreditYears > 5 ) 
                                {

                                    $sheet->writeString($r,$c,$row->employeeid,$normal);
                                    $c++;
                                    $sheet->write($r,$c,$row->fullname,$normal);
                                    $c++;
                                    $sheet->write($r,$c,(date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))=='01-01-1970'?'':date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))),$normal);
                                    $c++;
                                    $sheet->write($r,$c,(date("m-d-Y",strtotime($this->employee->EmpRegularDate($id)))),$normal);
                                    $c++;
                                    $sheet->write($r,$c,($noCreditYears>=5?$noCreditYears:''),$normal);
                                    $c++;
                                    $sheet->write($r,$c,$this->employee->GetBasicPreviousPay($id),$normal);
                                    $c++;
                                    $sheet->write($r,$c,$this->employee->GetBasicCurrentPay($id),$normal);
                                    $c++;
                                    $sheet->write($r,$c,'',$normal);
                                    $c++;
                                    $sheet->write($r,$c,$totallongevity,$normal);
                                    $c++;
                                    $sheet->write($r,$c,$totallongevity,$normal);
                                    $r++;
                                    $c=0;   
                                }
                        }
                }
                
            
        }
    }
	    
          
    
	$r1 = 0;
	$r2 = 1;
	$r3 = 0;

	
    
    $xls->close();


    function displaytablefields($sheet,$r,$c,$fields,$coltitle=''){
        global $coltitles;   
        foreach($fields as $colinfo){ 
        list($caption,$span,$width,$extra) = $colinfo;  
        if($span > 1) $sheet->setMerge($r, $c, $r, (($c-1) + $span)); 
        $sheet->write($r,$c,$caption,$coltitle);
        if(is_array($extra)){
            $xr = $r + 1;
            displaytablefields($sheet,$xr,$c,$extra,$coltitles);  
        }else{
            $sheet->setColumn($c,$c,$width);  
        }
        $c += $span;
        }
    }
	
	function displaytablefieldssubfields($sheet,$r,$c,$subfields,$coltitle=''){
        global $coltitles;   
        foreach($subfields as $colinfo){ 
        list($caption,$span,$width,$extra) = $colinfo;  
        if($span > 1) $sheet->setMerge($r, $c, $r, (($c-1) + $span)); 
        $sheet->write($r,$c,$caption,$coltitle);
        if(is_array($extra)){
            $xr = $r + 1;
            displaytablefields($sheet,$xr,$c,$extra,$coltitles);  
        }else{
            $sheet->setColumn($c,$c,$width);  
        }
        $c += $span;
        }
    }
?>