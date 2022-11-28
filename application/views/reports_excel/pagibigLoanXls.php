<?php
    // $empdata = $this->payroll->getpagibigLoanExcel($empid,$schedule,$quarter,$dfrom,$dto,$dept);
       
    // foreach ($empdata->result() as $info) {
    //     $exploded = explode("/",$info->fixeddeduc);
    //     $explodeMore = explode("=",$exploded[0]);
    //     ($explodeMore[1] == "" || $explodeMore[1] == null) ? $pagIbig = "0": $pagIbig = $explodeMore[1];
    //     echo $info->employeeid."====".$pagIbig."<br>";
    // }

    // die();
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("PAGIBIGLOAN".date("mY",strtotime($dfrom)).".xls");
 
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");
    // $normalcenter->setColumn(0,40,1);
    $normalcenter->setLocked();
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();


    $normalleft =& $xls->addFormat(array('Size' => 10));
    $normalleft->setHAlign("left");
    $normalleft->setFontFamily('Arial');
    // $normalcenter->setColumn(0,40,1);
    $normalleft->setLocked();

     
    $boldleft =& $xls->addFormat(array('Size' => 10));
    $boldleft->setHAlign("left");
    $normalleft->setFontFamily('Arial');
    $boldleft->setBold();
    $boldleft->setLocked();

    
    
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


    $dept         = (isset($_GET['dept']) ? $_GET['dept'] : $_POST['dept']);
    $schedule         = (isset($_GET['schedule']) ? $_GET['schedule'] : $_POST['schedule']);
    $sdate         = (isset($_GET['dfrom']) ? $_GET['dfrom'] : $_POST['dfrom']);
    $edate         = (isset($_GET['dto']) ? $_GET['dto'] : $_POST['dto']);
    $quarter         = (isset($_GET['quarter']) ? $_GET['quarter'] : $_POST['quarter']);



    $sheet =& $xls->addWorksheet("Sheet 1");
   
    
    $c = 0;$r = 0;
    // $bitmap = "images/school_logo_bm.bmp";
    
    // $sheet->insertBitmap( $r , $c  + $offset , $bitmap , $hr , 5 , .20 ,.40 );
    // $r++;$c++;
    date_default_timezone_set('Asia/Manila');

    $curDate = date('m/d/Y');
    $payrollDate = date('m/d/Y',strtotime($edate));

    $sheet->write(0,0,"Corporate Collection",$boldleft);
    // $sheet->setColumn(0,0,30);  
    $r++;
    $sheet->write(2,0,"PagIBIG Branch: ",$boldleft);    $sheet->writeString(2,1,'03',$normalleft); #$sheet->write(2,1,$SCHOOL_NAME,$normalcenter);
    $r++;
    $sheet->write(3,0,"Period Covered: ",$boldleft);    $sheet->write(3,1,date("Ym",strtotime($dfrom)),$normalleft); $sheet->write(3,2,'(YYYYMM)',$normalleft); 
    $r++;
    $sheet->write(4,0,"PagIBIG Employeer:",$boldleft);  $sheet->writeString(4,1,201740710005,$normalleft);   
    $r++;
    $sheet->write(5,0,"Employer Type:",$boldleft);      $sheet->write(5,1,'PRIVATE',$normalleft); 
    $r++;
    $sheet->write(6,0,"Pay Type:",$boldleft);       $sheet->write(6,1,'ST',$normalleft);  $sheet->write(6,2,'(MC/CL/ST)',$normalleft);
    $r++;
    $sheet->write(7,0,"Employer Name:",$boldleft);       $sheet->write(7,1,'Pinnacle Technologies Inc., INC.',$normalleft); 
    $r++;
    $sheet->write(8,0,"Employer Address:",$boldleft);       $sheet->write(8,1,changeenye('POBLACION, CITY OF DASMARIÑAS, CAVITE'),$normalleft); 
    $r++;
    $sheet->write(9,0,"Zip Code:",$boldleft);       $sheet->write(9,1,'4114',$normalleft); 
    $r++;
    $sheet->write(10,0,"Telephone Number:",$boldleft);       $sheet->write(10,1,'046-416-02-60',$normalleft); 
    $r++;
    $sheet->write(11,0,"Bank Code:",$boldleft);       $sheet->write(11,1,'AUB',$normalleft); 
    $r++;


    $tbl_header = array('PagIBIG No.','Employee ID','Last Name','First Name','Middle Initial','Employee\'s Contribution','Employeer\'s Contribution','Tax Identification No.','Date of Birth (YYYYMMDD)
');
    $table_size = array(22,12,22,22,22,22,22,22,22);
    $r = writeTable($sheet,array(0 => $tbl_header), 13, 0, $boldleft, $table_size);
    $table_line = array("'------------------------------------------","'------------------------------------------","'------------------------------------------","'------------------------------------------","'------------------------------------------","'------------------------------------------","'------------------------------------------","'------------------------------------------","'------------------------------------------");
    $r = writeTable($sheet,array(0 => $table_line), 14, 0, $normalleft, $table_size);
    
$empdata = $this->payroll->getpagibigLoanExcel($empid,$schedule,$quarter,$dfrom,$dto,$dept);    

    foreach ($empdata->result() as $info) {
        $exploded = explode("/",$info->fixeddeduc);
        $explodeMore = explode("=",$exploded[0]);
        ($explodeMore[1] == "NULL" || $explodeMore[1] == NULL) ? $pagIbig = "0": $pagIbig = $explodeMore[1];
        
        ($info->emp_pagibig == "NULL" || $info->emp_pagibig == NULL) ? $pagibigNumber = "" : $pagibigNumber = $info->emp_pagibig;
        ($info->emp_tin == "NULL" || $info->emp_tin == NULL) ? $emptin = "" : $emptin = $info->emp_tin;
            $tbl_content = array(
                $pagibigNumber,
                $info->employeeid,
                changeenye($info->lname),
                changeenye($info->fname),
                changeenye($info->mname),
                $pagIbig,
                "",
                $emptin,
                date('Ymd',strtotime($info->bdate))
            );
            $r = writeTable($sheet,array(0 => $tbl_content), $r, 0, $normal, $table_size);	    	

	    }
          
    
	$r1 = 0;
	$r2 = 1;
	$r3 = 0;


    
    $xls->close();
    function changeenye($enye = "") {
        $return = "";
        $return = str_replace("Ã‘","Ñ",$enye);
        return utf8_decode($return);
    }
    
    function writeText(&$sheet,$text,&$font,$row1, $col1, $row2 = '', $col2 = ''){
        # > para sa merge
        if($row2 != '' || $col2 != ''){
            $sheet->setMerge($row1,$col1,$row1 + $row2, $col1 + $col2);
        }

        #echo $row2;die;
        # > diplayed sa excel
        $sheet->write($row1,$col1,$text,$font);

    }

    function writeTable(&$sheet, $tableData, $startRow, $startCol, &$font, $size){
        $row = $startRow;
        #echo "<pre>";
        #var_dump($size);
        # displayed here per row
        foreach ($tableData as $rowData) {
            $col = $startCol;
            # displayed here per column
            $idx = 0;
            foreach ($rowData as $text) {
                #echo $size[$idx] .',';
                $sheet->writeString($row,$col,$text,$font);

                # para sa size
                $sheet->setColumn($col, $col, $size[$idx]);
                
                $col += 1;
                $idx += 1;
            }
            $row += 1;
        }

        return $row;
    }

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