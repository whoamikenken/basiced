<?php
  function formatAmount($amount=''){
    if($amount){
        $amount = number_format( $amount, 2 );
    }else{
        $amount = '0.00';
    }
    return $amount;
}
  header("Content-Type: text/csv");
  header("Content-Disposition: attachment; filename=Philhealth File Generator.csv");
  header("Cache-Control: no-cache, no-store, must-revalidate"); 
  header("Pragma: no-cache"); 
  header("Expires: 0");

  function outputCSV($contentData) {
    $output = fopen("php://output", "r+");
    foreach ($contentData as $row) {
      fputcsv($output, $row);     
    }
    die;
    fclose($output);  
  } 

  $content = array();

  $content[] = array("PIN","MONTHLY BASIC SALARY","STATUS","EFFECTIVITY DATE","BIRTHDATE");

  foreach($contriList as $philhealthid=>$contriInfo){
    $content[$philhealthid] = array($contriInfo['PhilHealthId']."\t",formatAmount($contriInfo['Salary']),$contriInfo['Status'],$contriInfo['EffectivityDate'],$contriInfo['BDate']);
  }

  outputCSV($content);
  
?>