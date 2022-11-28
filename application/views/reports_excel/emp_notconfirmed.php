<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
    $this->load->library('lib_includer');
    $this->lib_includer->load("excel/Writer");
    $xls = New Spreadsheet_Excel_Writer();
    $xls->send("Employee Not Yet Confirmed.xls");
    
    $dfrom = $_GET['dfrom'];
    $dto   = $_GET['dto'];
    $dept  = $_GET['dept'];
    
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
    $coltitle->setAlign("center");
    $coltitle->setBold();
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
    $dateform->setNumFormat("MM-DD-YYYY");
    $dateform->setLocked();
    
    $timeform =& $xls->addFormat(array('Size' => 8));
    $timeform->setNumFormat("h:mm:ss AM/PM");
    $timeform->setLocked();

    /** End of Font Format */
    $sheet = &$xls->addWorksheet("Sheet 1");
    $c = 0;$r = 0;
    $sheet->write($r,$c,"Employee Not Yet Confirmed",$bigboldcenter);
    $sheet->setMerge($r, 0, $r, 4);$r++;
    $sheet->setMerge($r, 0, $r, 4);$r++;
    
    
    if(!empty($dept)){
        $sheet->write($r,$c,$this->extras->getemployeedepartment($dept),$bigboldcenter);
        $sheet->setMerge($r, 0, $r, 4);$r++;
        $sheet->setMerge($r, 0, $r, 4);$r++;
    }
    
    $sheet->write($r,$c,"Employee ID",$coltitle);
    $sheet->setMerge($r, $c, $r+1, $c);
    $sheet->setColumn($c,$c,30);$c++;
    
    $sheet->write($r,$c,"Employee Name",$coltitle);
    $sheet->setMerge($r, $c, $r+1, $c);
    $sheet->setColumn($c,$c,40);$c++;
    
    $sheet->write($r,$c,"Cut-Off Date",$coltitle);
    $sheet->setMerge($r, $c, $r, $c+1);$c+=2;
    
    $sheet->write($r,$c,"Date Confirmed",$coltitle);
    $sheet->setMerge($r, $c, $r+1, $c);
    $sheet->setColumn($c,$c,30);$c++;
    $r++;
    
    $c = 2;
    $sheet->write($r,$c,"Date From",$coltitle);
    $sheet->setColumn($c,$c,20);$c++;
    $sheet->write($r,$c,"Date To",$coltitle);
    $sheet->setColumn($c,$c,20);$c++;
    
    $r++;
    
    $data = $this->extras->viewCutOffNoConfirmed($dfrom,$dto,$dept);
    foreach($data->result() as $row){
        $emp = $this->employee->getindividualemployee($row->employeeid);
        foreach($emp as $fd) $fullname = $fd->fullname;  
        
        $c = 0;
        $sheet->write($r,$c,$row->employeeid,$normalcenter);$c++;
        $sheet->write($r,$c,$this->extras->changeenye($fullname),$normalcenter);$c++;
        $sheet->write($r,$c,date('F d, Y',strtotime($dfrom)),$normalcenter);$c++;
        $sheet->write($r,$c,date('F d, Y',strtotime($dto)),$normalcenter);$c++;
        $sheet->write($r,$c,"",$normalcenter);$c++;
        $r++;
    }
    
    $xls->close();
?>
