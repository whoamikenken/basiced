<?php

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=Atm Payroll List.csv");
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");
$info = array();
$total = 0;
$header = array("ACCOUNT NUMBER", "NET SALARY", "EMPLOYEE NAME", "BANK");
$info[0] = $header;
if(isset($list)){
	foreach($list as $employeeid => $emp_info){
		$net_salary = (float) $emp_info['net_salary'];
		$info[] = array('account_num'=> "	".$emp_info['account_num'], 'net_salary' => trim("	".number_format($net_salary, 2, '.', '')), 'fullname' => utf8_encode(strtoupper($emp_info['fullname'])), 'bank' => $bank_name );
		$total += $emp_info['net_salary'];
	}
}

$info[10000] = array('account_num'=> "TOTAL", 'net_salary' => "	".number_format($total, 2, '.', ''), 'fullname' => "" );

// ksort($info);
$fp = fopen('php://output', 'wb');
foreach ( $info as $data ) {
	if(isset($data['account_num'])) $data['fullname'] = utf8_decode($data['fullname']);
    fputcsv($fp, $data);
}
fclose($fp);

?>