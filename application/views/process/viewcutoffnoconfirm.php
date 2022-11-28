<?php

/**
 * @author Justin
 * @copyright 2015
 */
?>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
        <tr>
            <th class="align_center" rowspan="2">Employee ID</th>
            <th class="align_center" rowspan="2">Employee Name</th>
            <th class="align_center" colspan="2">Cut-Off Date</th>
            <th class="align_center" rowspan="2">Date Confirmed</th>
        </tr>
        <tr>
            <th width='15%'>From</th>
            <th width='15%'>To</th>
        </tr>
    </thead>
    <tbody>
    
<?
$data = $this->extras->viewCutOffNoConfirmed($dfrom,$dto,$dept);
if($data->num_rows() > 0){
foreach($data->result() as $row){
    $emp = $this->employee->getindividualemployee($row->employeeid);
    foreach($emp as $fd) $fullname = $fd->fullname;  
?>
    <tr>
        <td class="align_center"><?=$row->employeeid?></td>
        <td class="align_center"><?=$fullname?></td>
        <td><?=date('F d, Y',strtotime($dfrom))?></td>
        <td><?=date('F d, Y',strtotime($dto))?></td>
        <td class="align_center"></td>
    </tr>
<?
}
}else{
?>
<tr>
    <td colspan='5'>No Data Available..</td>
</tr> 
<?
}
?>

    </tbody>
</table>
<script>
$('table').DataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 } 
});
</script>
