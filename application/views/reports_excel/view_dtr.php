<?php

/**
 * @author Stanley
 * @copyright 2013
 */
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("view dtr.xls");
    
    function displaytablefields(&$sheet,$r,$c,$fields){
        global $coltitle,$boldcenter;	 
        foreach($fields as $key=>$con){ 
            $ainfo=array();
            foreach ($con as $sub=>$li) {
                switch ($sub) {
                    case 0: $ainfo["caption"]=$li; break;
                    case 1: $ainfo["span"]=$li; break;
                    case 2: $ainfo["width"]=$li; break;
                    case 3: $ainfo["extra"]=$li; break;
                }
            }
            if($ainfo["span"]>1)$sheet->setMerge($r,$c,$r,(($c-1)+$ainfo["span"]));	
            $sheet->write($r,$c,$ainfo["caption"],$boldcenter);
            if(is_array($ainfo["extra"])){
                $xr=$r+1;
                displaytablefields($sheet,$xr,$c,$ainfo["extra"]);	
            }else{
                $sheet->setColumn($c,$c,$ainfo["width"]);	
            }
            $c+=$ainfo["span"];
        }
    }
    
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();
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
    $bigbold =& $xls->addFormat(array('Size' => 12));
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
    
    /** End of Font Format */
    
    $info=array();
    foreach ($_GET as $key=>$value) $info[htmlspecialchars($key)]=htmlspecialchars($value); 
    
    $info["title"]="DAILY TIME RECORD REPORT";
    $info["field"]=array(
        array("Date",1,10,""),
        array("Day",1,10,""),
        array("Schedule",1,20,""),
        array("Time In",1,10,""),
        array("Time Out",1,10,""),
        array("Tardy",1,10,""),
        array("Under Time",1,10,""),
        array("Absent",1,10,""),
        array("Regular",1,10,""),
        array("Legal Hol",1,10,""),
        array("Special Hol",1,10,""),
        array("Rest",1,10,""),
        array("Rest Legal",1,10,""),
        array("Rest Special",1,10,""),
        array("OT Reg",1,10,""),
        array("OT Legal",1,10,""),
        array("OT Special",1,10,""),
        array("OT Rest",1,10,""),
        array("OT Rest Hol",1,10,""),
        array("Night Prem",1,10,""),
        array("Type",1,15,"")
    );
    
    $info["ctr"]=count($info["field"])-1;
    $sheet = &$xls->addWorksheet("Sheet 1");
    $sheet->setMerge(0, 0, 0, $info["ctr"]);
    $sheet->setMerge(1, 0, 1, $info["ctr"]);
    $sheet->setMerge(3, 0, 3, $info["ctr"]);
    $sheet->setMerge(4, 0, 4, $info["ctr"]);
    /** Title */
    $sheet->write(0,0,"DAVAO DOCTOR'S COLLEGE, INC",$tits);
    $sheet->write(1,0,"GENERAL MALVAR STREET, DAVAO CITY, DAVAO DEL SUR",$normalcenter);
    $sheet->write(3,0,$info["title"],$tits);
    $sheet->write(4,0,"",$normalcenter);
    $info["r"]=5;
    $info["c"]=0;
    
    $funame="";
    $emp=$this->db->query("select concat(trim(lname),', ',trim(fname),' ',trim(mname)) as funame from employee where employeeid='".$info["employeeid"]."'");
    if ($emp->num_rows()>0) {
        $funame=$emp->row(0)->funame;
    }
    
    $sheet->setMerge($info["r"],$info["c"],$info["r"],($info["c"]+1));
    $sheet->write($info["r"],$info["c"],"Employee ID",$normal);
    $info["c"]+=2;
    $sheet->setMerge($info["r"],$info["c"],$info["r"],$info["ctr"]);
    $sheet->write($info["r"],$info["c"],(" ".$info["employeeid"]),$normal); 
    $info["r"]++;
    $info["c"]=0;
    $sheet->setMerge($info["r"],$info["c"],$info["r"],($info["c"]+1));
    $sheet->write($info["r"],$info["c"],"Employee Name",$normal);
    $info["c"]+=2;
    $sheet->setMerge($info["r"],$info["c"],$info["r"],$info["ctr"]);
    $sheet->write($info["r"],$info["c"],$funame,$normal);
    
    $info["r"]+=2;
    $info["c"]=0;
    displaytablefields($sheet,$info["r"],$info["c"],$info["field"]);
    
    $dtr=$this->db->query("select * from employee_dtr where cutoffid='".$info["cutoffid"]."' AND employeeid='".$info["employeeid"]."' order by cdate,starttime");
    $info["r"]++;
    if ($dtr->num_rows()>0) {
        $inf=array();
        $inf["tdy"]=0; 
        $inf["und"]=0;
        $inf["abs"]=0;
        $inf["reg"]=0;       
        
        for($i=0;$i<$dtr->num_rows();$i++){
            $mrow = $dtr->row($i);   
            /**
            $inf["tdy"]+=(date("H",strtotime($mrow->tardy))!="00"?$mrow->tardy:0); 
            $inf["und"]+=(date("H",strtotime($mrow->undertime))!="00"?$mrow->undertime:0); 
            $inf["abs"]+=(date("H",strtotime($mrow->absent))!="00"?$mrow->absent:0); 
            $inf["reg"]+=(date("H",strtotime($mrow->regular))!="00"?$mrow->regular:0);
            */
            $info["r"]++;
            $info["c"]=0;
            $sheet->write($info["r"],$info["c"],date("m/d/Y",strtotime($mrow->cdate)),$normal);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],$mrow->dayofweek_,$normal);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],$mrow->schedules_,$normal);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],($mrow->timein ? date("h:iA",strtotime($mrow->timein)) : ""),$timeform);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],($mrow->timein ? (date("mdy",strtotime($mrow->timein))!=date("mdy",strtotime($mrow->timeout)) ? date("m/d/Y h:iA",strtotime($mrow->timeout)) : date("h:iA",strtotime($mrow->timeout))) : ""),$timeform);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],(date("H",strtotime($mrow->tardy))!="00" ? date("g",strtotime($mrow->tardy)) . "h" : "")." ".(date("i",strtotime($mrow->tardy))!="00" ? date("i",strtotime($mrow->tardy))."m" : ""),$normal);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],(date("H",strtotime($mrow->undertime))!="00" ? date("g",strtotime($mrow->undertime)) . "h" : "")." ".(date("i",strtotime($mrow->undertime))!="00" ? date("i",strtotime($mrow->undertime))."m" : ""),$normal);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],(date("H",strtotime($mrow->absent))!="00" ? date("g",strtotime($mrow->absent)) . "h" : "")." ".(date("i",strtotime($mrow->absent))!="00" ? date("i",strtotime($mrow->absent))."m" : ""),$normal);
            $info["c"]++;
            $sheet->write($info["r"],$info["c"],(date("H",strtotime($mrow->regular))!="00" ? date("g",strtotime($mrow->regular)) . "h" : "")." ".(date("i",strtotime($mrow->regular))!="00" ? date("i",strtotime($mrow->regular))."m" : ""),$normal);
            $info["c"]+=12;
            $sheet->write($info["r"],$info["c"],($mrow->type_ ? $mrow->type_ : "ADMIN"),$normal);
        }
        
        /**
        $info["r"]+=2;
        $info["c"]=0;
        $sheet->setMerge($info["r"],$info["c"],$info["r"],($info["c"]+4));
        $sheet->write($info["r"],$info["c"],"TOTAL",$bold);
        $info["c"]+=5;
        $sheet->write($info["r"],$info["c"],(date("H",strtotime($inf["tdy"]))!="00" ? date("g",strtotime($inf["tdy"])) . "h" : "")." ".(date("i",strtotime($inf["tdy"]))!="00" ? date("i",strtotime($inf["tdy"]))."m" : ""),$bold);
        $info["c"]++;
        $sheet->write($info["r"],$info["c"],(date("H",strtotime($inf["und"]))!="00" ? date("g",strtotime($inf["und"])) . "h" : "")." ".(date("i",strtotime($inf["und"]))!="00" ? date("i",strtotime($inf["und"]))."m" : ""),$bold);
        $info["c"]++;
        $sheet->write($info["r"],$info["c"],(date("H",strtotime($inf["abs"]))!="00" ? date("g",strtotime($inf["abs"])) . "h" : "")." ".(date("i",strtotime($inf["abs"]))!="00" ? date("i",strtotime($inf["abs"]))."m" : ""),$bold);
        $info["c"]++;
        $sheet->write($info["r"],$info["c"],(date("H",strtotime($inf["reg"]))!="00" ? date("g",strtotime($inf["reg"])) . "h" : "")." ".(date("i",strtotime($inf["reg"]))!="00" ? date("i",strtotime($inf["reg"]))."m" : ""),$bold);
        */
    }
    
    


    $xls->close();
?>