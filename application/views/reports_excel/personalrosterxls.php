<?php
 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Personal Roster Report.xls");
 	
    /** Fonts Format */
    
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();

    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    $normalcenter->setLocked();

    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();
    $tardycenter =& $xls->addFormat(array('Size' => 10));
    $tardycenter->setAlign("center");
    $tardycenter->setColor("red");
    $tardycenter->setLocked();
    
    $failcenter =& $xls->addFormat(array('Size' => 10));
    $failcenter->setAlign("center");
    $failcenter->setBgColor("yellow");
    $failcenter->setFgColor("yellow");
    $failcenter->setLocked();
    
    $halfcenter =& $xls->addFormat(array('Size' => 10));
    $halfcenter->setAlign("center");
    $failcenter->setBgColor("yellow");
    $halfcenter->setColor("red");
    $halfcenter->setLocked();        
    
    $tits =& $xls->addFormat(array('Size' => 10));
    $tits->setBold();
    $tits->setAlign("center");
    $tits->setLocked();
    
    $titsnormal =& $xls->addFormat(array('Size' => 10));
    $titsnormal->setAlign("center");
    $titsnormal->setLocked();
    
   $coltitle =& $xls->addFormat(array('Size' => 10));
    $coltitle->setBorder(2);
    $coltitle->setBold();
    $coltitle->setAlign("center");
    $coltitle->setFgColor('black');
    $coltitle->setColor('yellow');
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
    
   
    $from_date  = isset($dfrom) ? $dfrom : '';
	$to_date    = isset($dto) ? $dto : '';
	$dept       =  isset($deptid) ? $deptid : '';
	$tnt        =  isset($tnt) ? $tnt : '';
	$estatus    = isset($estatus) ? $estatus : '';
	$edata      =  isset($edata) ? $edata : '';
	$division      =  isset($division) ? $division : '';
	$department    = isset($department) ? $department : '';
	$employee      = isset($employee) ? $employee : '';
	$campus 	 = isset($campus) ? $campus : '';
	$isactive 	 = isset($isactive) ? $isactive : '';
	// $result      = $this->reports->loadempdata($edata, $division, $department, $employee,$campus,$isactive); 
	$result = $this->reports->loadempdata($edata, $division, $department, $employee, $campus, $isactive);   

	$cdata = $empstathistoryquery = $familyquery = $childrenquery = $taxDependentsquery = "";

	$cdata = $result;

	$empstathistory = array("managementid2","deptid2","employmentstat2","positionid2","dateposition2");
	$empstathistorycol = "";
	$empstathistoryexist = 0;

	$family = array("fmname","fmrelation","fmdob");
	$familycol = "";
	$familyexist = 0;

	$children = array("childname","childbday","childage");
	$childrencol = "";
	$childrenexist = 0;

	$taxDependents = array (   "tdname","tdrelation","tdaddress","tdcontact","tdbdate","tdlegitimate");
	$taxDependentscol = "";
	$taxDependentsexist = 0;

	$father = array (   "father","fatheroccu","fatherstatus","fatheraddress","fathernumber");
	$fathercol = "";
	$fatherexist = 0;

	$mother =  array (   "mother","motheroccu","motherstatus","motheraddress","mothernumber");
	$mothercol = "";
	$motherexist = 0;

	$spouse =  array ( "spouse_name","occupation","spousestatus","spouseaddress","spousebaddress","spousenumber");
	$spousecol = "";
	$spouseexist = 0;

	$immigrationDetails =  array ( "passport","visa","icardnum","crnno");
	$immigrationDetailscol = "";
	$immigrationDetailsexist = 0;

	$i = 0;

	$rowspan = 1;
	foreach(explode(',',$edata) as $data){
		if(in_array($data,$empstathistory))
		{
			$empstathistorycol += 1;
			$rowspan = 2;
		}
		if(in_array($data,$family))
		{
			$familycol += 1;
			$rowspan = 2;
		}
		if(in_array($data,$children))
		{
			$childrencol += 1;
			$rowspan = 2;
		}
		if(in_array($data,$taxDependents))
		{
			$taxDependentscol += 1;
			$rowspan = 2;
		}
		if(in_array($data,$father))
		{
			$fathercol += 1;
			$rowspan = 2;
		}
		if(in_array($data,$mother))
		{
			$mothercol += 1;
			$rowspan = 2;
		}
		if(in_array($data,$spouse))
		{
			$spousecol += 1;
			$rowspan = 2;
		}
		if(in_array($data,$immigrationDetails))
		{
			$immigrationDetailscol += 1;
			$rowspan = 2;
		}
	}

    $fields = array();
    array_push($fields, array("",1,20,1));
    $subfields = array();
    array_push($subfields, array("#",1,20,1));
	$i=1;
	$merge = array();
    foreach(explode(',',$edata) as $data){
		if(in_array($data,$empstathistory))
		{
			if($empstathistoryexist == 0)
			{
				$desc =  "Employment Status History";
				array_push($fields, array($desc,$empstathistorycol,20,1));
				$empstathistorycount = $i;
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$empstathistoryexist += 1;
		}
		else if(in_array($data,$family))
		{
			if($familyexist == 0)
			{
				$desc =  "Number of Children";
				array_push($fields, array($desc,$familycol,20,1));
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$familyexist += 1;
		}
		else if(in_array($data,$children))
		{
			if($childrenexist == 0)
			{
				$desc =  "Number of Children";
				array_push($fields, array($desc,$childrencol,20,1));
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$childrenexist += 1;
		}
		else if(in_array($data,$taxDependents))
		{
			if($taxDependentsexist == 0)
			{
				$desc =  "Tax Dependents";
				array_push($fields, array($desc,$taxDependentscol,20,1));
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$taxDependentsexist += 1;
		}
		else if(in_array($data,$father))
		{
			if($fatherexist == 0)
			{
				$desc =  "Father";
				array_push($fields, array($desc,$fathercol,20,1));
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$fatherexist += 1;
		}
		else if(in_array($data,$mother))
		{
			if($motherexist == 0)
			{
				$desc =  "MOTHER";
				array_push($fields, array($desc,$mothercol,20,1));
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$motherexist += 1;
		}
		else if(in_array($data,$spouse))
		{
			if($spouseexist == 0)
			{
				$desc =  "Spouse";
				array_push($fields, array($desc,$spousecol,20,1));
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$spouseexist += 1;
		}
		else if(in_array($data,$immigrationDetails))
		{
			if($immigrationDetailsexist == 0)
			{
				$desc =  "Immigration Details";
				array_push($fields, array($desc,$immigrationDetailscol,20,1));
			}
			$desc =  $this->reports->showdesc($data);
			array_push($subfields, array($desc,1,20,1));
			$immigrationDetailsexist += 1;
		}
		else 
		{
			$desc =  $this->reports->showdesc($data);
			array_push($fields, array($desc,1,20,1));
			
			array_push($subfields, array("",1,20,1));
			
			array_push($merge, $i);
		}
		$i++;
    }   

    $numfield = count( $subfields )-1;

    if($numfield < 5) {
    	$numfield = 5;
    	$offset = 0;
    	$hr = 10;	
    }else{
    	$offset = intval(($numfield - 2) / 2);
		$hr = 0;
    }

    $sheet = &$xls->addWorksheet("Sheet 1");
   
    $sheet->setMerge(0, 0, 0, $numfield);
    $sheet->setMerge(1, 0, 1, $numfield);
    $sheet->setMerge(2, 0, 2, $numfield);
    $sheet->setMerge(3, 0, 3, $numfield);
    $sheet->setMerge(4, 0, 4, $numfield);
	
    foreach($merge as $m)
	{
		$sheet->setMerge(6, $m, 7, $m);
	}
    // $sheet->setMerge(5, 0, 5, $numfield);
    // $sheet->setMerge(6, 0, 6, $numfield);

    $c = 0;$r = 0;

    $bitmap = "images/school_logo.bmp";
    
    $sheet->insertBitmap( $r , $c  + $offset , $bitmap , $hr , 5 , .10 ,.20 );
    $r++;$c++;
    $sheet->write(1,0,$SCHOOL_NAME,$boldcenter);
    $r++;
	$sheet->write(2,0,$SCHOOL_CAPTION,$boldcenter);
    $r++;
    $sheet->write(3,0,"PERSONNEL ROSTER REPORT",$bigboldcenter);
    $r++;
    $sheet->write(4,0,"As of ".date("F Y"),$normalcenter);

    $r = 6;
    $c = 0;
    displaytablefields($sheet,$r,$c,$fields,$coltitle);
	$r = 7;
    displaytablefieldssubfields($sheet,$r,$c,$subfields,$coltitle);

    $r = 8;
	
    // foreach ($cdata as $obj) {
        // foreach ($obj as $key) {
          // $sheet->write($r,$c,$key,$normal);
          // $c++;
        // }
      // $r++;
      // $c=0;
    // }
	$r1 = 0;
	$r2 = 1;
	$r3 = 0;
	$empcount = 1;


	foreach ($cdata as $obj) {
		$sheet->write($r,$c,$empcount,$normal);
		$c++;
		$columncount = 1;
		foreach(explode(',',$edata) as $col){
			$columncount++;
			$r1 = $r;
			$r3 = 0;
			if(in_array($col,$empstathistory))
			{
				$empstathistoryquery = $this->reports->empstathistoryquery($obj->employeeid,$col);
				foreach($empstathistoryquery as $q)
				{
					$sheet->write($r1,$c,utf8_decode($q->$col.' '),$normal);
					$r1++;
					$r3++;
				}
				if($r3 > $r2){$r2 = $r3; $r3 = 0;}
			}
			else if(in_array($col,$family))
			{
				$familyquery = $this->reports->familyquery($obj->employeeid,$col);
				foreach($familyquery as $q)
				{
					
					$sheet->write($r1,$c,utf8_decode($q->$col.' '),$normal);
					$r1++;
					$r3++;
				}
				if($r3 > $r2){$r2 = $r3; $r3 = 0;}
			}
			else if(in_array($col,$children))
			{
				$childrenquery = $this->reports->childrenquery($obj->employeeid,$col);
				foreach($childrenquery as $q)
				{
					
					$sheet->write($r1,$c,utf8_decode($q->$col.' '),$normal);
					$r1++;
					$r3++;
				}
				if($r3 > $r2){$r2 = $r3; $r3 = 0;}
			}
			else if(in_array($col,$taxDependents))
			{
				$taxDependentsquery = $this->reports->taxDependentsquery($obj->employeeid,$col);
				foreach($taxDependentsquery as $q)
				{
					
					$sheet->write($r1,$c,utf8_decode($q->$col.' '),$normal);
					$r1++;
					$r3++;
				}
				if($r3 > $r2){$r2 = $r3; $r3 = 0;}
			}
			else
			{
				
				$sheet->write($r1,$c,utf8_decode($obj->$col.' '),$normal);
				$r1++;
			}
			$c++;
		}
      $r = $r2 + $r;
	  $r2 = 1;
      $c=0;
      $empcount++;
    }

    $empcount = $empcount - 1;
    $totlDescStart = $columncount - 2;
    for ($i=0; $i < $columncount; $i++) { 
        if($i == $totlDescStart){
            $sheet->write($r,$i,"TOTAL:",$boldcenter);
            $sheet->write($r,$i+1,$empcount,$boldcenter);
        }else{
            $sheet->write($r,$i,' ',$boldcenter);
        }
    }

    
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