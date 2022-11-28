<?php
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    require_once(APPPATH."constants.php");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("SSS Contribution ".date('F Y',strtotime($_GET['dfrom'])).".xls");
    

    $year = date('Y');
    $year = date('Y',strtotime($year."- 1 year"));
    $fixedSSS = "";
    // $campus = (isset($_GET['campus']) ? $_GET['campus'] : $_POST['campus']);
    $dfrom = (isset($_GET['dfrom']) ? $_GET['dfrom'] : $_POST['dfrom']);
    $dto = (isset($_GET['dto']) ? $_GET['dto'] : $_POST['dto']);
    $schedule = (isset($_GET['schedule']) ? $_GET['schedule'] : $_POST['schedule']);
    $quarter = (isset($_GET['quarter']) ? $_GET['quarter'] : $_POST['quarter']);
    $eid = (isset($_GET['eid']) ? isset($_GET['eid']) : isset($_POST['eid']));
   
   
    /** Fonts Format */
    $normal =& $xls->addFormat(array('Size' => 10));
    $normal->setFontFamily('Arial');
    $normal->setLocked();
    $normalcenter =& $xls->addFormat(array('Size' => 10));
    $normalcenter->setAlign("center");


    // $normalcenter->setColumn(0,40,1);
    $normalcenter->setLocked();
    $normalunderlined =& $xls->addFormat(array('Size' => 10));
    $normalunderlined->setBottom(1);
    $normalunderlined->setLocked();


    $normalright =& $xls->addFormat(array('Size' => 10));
    $normalright->setHAlign("right");
     $normalright->setFontFamily('Arial');
    // $normalcenter->setColumn(0,40,1);
    $normalright->setLocked();

    
    
    $tardycenter =& $xls->addFormat(array('Size' => 10));
    $tardycenter->setAlign("center");
    $tardycenter->setColor("red");
    $tardycenter->setLocked();
    
    $grayBgCenter =& $xls->addFormat(array('Size' => 10));
    $grayBgCenter->setBorder(0);
    $grayBgCenter->setAlign("left");
    $grayBgCenter->setFontFamily('Arial');
    // $grayBgCenter->setBold();
    // $xls->setCustomColor(12, 192, 192, 192);
    // $grayBgCenter->setBgColor(12);
    // $grayBgCenter->setFgColor(12);
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
    $halfcenter->setBgColor("yellow");
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


    $sheet = &$xls->addWorksheet("SSSPREMIUM72016");
   
  
    
    $c = 0;$r = 0;
   
    $sheet->setColumn(0,0,15);
    $sheet->setColumn(0,1,15);
    $sheet->setColumn(0,2,15);
    $sheet->setColumn(0,3,15);
    $sheet->setColumn(0,4,15);
    $sheet->setColumn(0,5,15);
    $sheet->setColumn(0,6,15);
    $sheet->setColumn(0,7,15);
    $sheet->setColumn(0,8,15);
    $sheet->setColumn(0,9,15);
    $sheet->setColumn(0,10,15);

    $sheet->write(0,0,"SSS_NUMBER",$grayBgCenter);
    $r++;
    $sheet->write(0,1,"SURNAME",$grayBgCenter);
    $r++;
    $sheet->write(0,2,"FIRST_NAME",$grayBgCenter);
    $r++;
    $sheet->write(0,3,"MIDDLE_INI",$grayBgCenter);
    $r++;
    $sheet->write(0,4,"BIRTHDATE",$grayBgCenter);
    $r++;
    $sheet->write(0,5,"DATE_HIRED ",$grayBgCenter);
    $r++;
    $sheet->write(0,6,"DATE_SEPD ",$grayBgCenter);
    $r++;
    $sheet->write(0,7,"SSS_AMT ",$grayBgCenter);
    $r++;
    $sheet->write(0,8,"MC_AMT ",$grayBgCenter);
    $r++;
    $sheet->write(0,9,"EC_AMT ",$grayBgCenter);
    $r++;
    $sheet->write(0,10,"E_STATUS ",$grayBgCenter);
    $r++;
    // $sheet->write(0,8,"Longevity Pay Per Month ".date("Y",strtotime("01-01-".$year."- 1 year"))." - ".date("Y",strtotime("01-01-".$year)),$grayBgCenter);
    // $r++;
    // $sheet->write(0,9,"Proposed Increase Per Month",$grayBgCenter);
    // $r++;


    $r = 1;
    $c = 0;
    $totalSSS = $totalEC  = 0;
 $query = $this->payroll->getSSSContributionExcel($eid,$schedule,$quarter,$dfrom,$dto,$dept);

    foreach ($query->result() as $row) {
        // $fixedSSS = $row->fixeddeduc;
        // $splitSSS = explode("/", $fixedSSS);
        // if (isset($splitSSS[2])) {
        //      $SSSsplit = explode("=", $splitSSS[2]);
        //      $SSS = $SSSsplit[1];
        // }
        // else
        // {
        //     $SSS = 0.00;
        // }

        $fixeddeduc = $row->fixeddeduc;
        ($fixeddeduc == "" || $fixeddeduc == NULL)? $x = "wala": $x="meron" ;
        if($fixeddeduc == "" || $fixeddeduc == NULL){
        $SSS = 0;
        }else{

                $efixeddeduc = explode("/",$fixeddeduc);

                for($x=0;$x < count($efixeddeduc); $x++){
                $eefixeddeduc = explode("=",$efixeddeduc[$x]);

                        ($eefixeddeduc[0] == "SSS") ? $SSS = $eefixeddeduc[1]: $SSS = 0;
                }
        }
        $sql = $this->db->query("SELECT ec_er FROM sss_contribution WHERE ss_ee ='$SSS'; ");
        ($sql->num_rows() != 0)? $EC_AMT = $sql->row()->ec_er : $EC_AMT = 0.00;
        

        
        // $totalSSS += $this->payroll->getSSScontribution($SSS,'amount');
        // $totalEC +=$this->payroll->getSSScontribution($SSS,'EC');
        $totalSSS += $SSS;
        $sheet->writeString($r,$c,$row->emp_sss,$normal);
        $c++;
        $sheet->write($r,$c,$row->lname,$normal);
        $c++;
        $sheet->write($r,$c,$row->fname,$normal);
        $c++;
        $sheet->write($r,$c,$row->mname,$normal);
        $c++;
        $sheet->write($r,$c,date("m/d/Y",strtotime($row->bdate)),$normal);
        $c++;
        $sheet->write($r,$c,date("m/d/Y",strtotime($row->dateemployed)),$normal);
        $c++;
        $sheet->write($r,$c,'',$normal);
        $c++;
        $sheet->writeString($r,$c,number_format($SSS,2),$normalright);
        $c++;
        $sheet->writeString($r,$c,'',$normalright);
        $c++;
        $sheet->writeString($r,$c,number_format($EC_AMT,2),$normalright);
        $c++;
        $sheet->write($r,$c,'3',$normal);
      
        // $c++;
        // $sheet->write($r,$c,$totallongevity,$normal);
        // $c++;
        // $sheet->write($r,$c,$totallongevity,$normal);
        $r++;
        $c=0;   
    
    }
    $sheet->writeString($r,6,"TOTAL:",$normalright);
    $sheet->writeString($r,7,number_format($totalSSS,2),$normalright);
    // $sheet->writeString($r,9,$totalEC,$normalright);
    // $sheet->writeString($r+1,9,$totalSSS - $totalEC,$normalright);
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