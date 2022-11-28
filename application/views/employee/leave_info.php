<?php

/**
 * @author Robert Ram Bolista
 * @copyright ram_bolista@yahoo.com
 * @date 5-27-2014
 * @time 15:12
 * 
 * @edited by : Carlos Pacheco
 * @date-time : 5/27/2014 6:48pm
 */

// The complete employee information of the logged-in user.
// upon non-existence of the said information, a data gather 
// via session access will execute
if(isset($empinfo)){
   $empdetails = $empinfo;    
 }else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo;
 }

// the unique identifier/employee id of "logger".
$employeeid = $empdetails[0]['employeeid'];

// instance of the employee model
// Note: Employee() Model/Class Should contain most of the 
//          necessary data needed.
$empModel = new Employee();

// gather predefined Allowed Leaves resultset
$allowed_leaves = $empModel->getEmployeeAllowedLeaves($employeeid);
// echo "<pre>"; print_r($this->db->last_query()); die;
$applied_leaves = $empModel->getEmployeeLeaveInfo($employeeid);

$leavehistory = $pendinghistory = $leavecounts = array();
// calculate leaves and applied leaves and arrange data
foreach( $applied_leaves as $each ) {
    // check if pending or approved, approved goes to leave history
    if( $each->status == 'PENDING' ) $pendinghistory[] = $each;
    elseif( $each->status == 'APPROVED' ) {
        $leavehistory[] = $each;
        if( isset($leavecounts[$each->leavetype]) )
            $leavecounts[$each->leavetype] += $each->no_days;
        else
            $leavecounts[$each->leavetype] = $each->no_days;
    }
}

?>
<div id=""> <!-- Content start -->
    <div class="">
        <div class="well blue">
            <div class="well-header"><h5></h5></div>
            <div class="well-content">
                <table class="table table-striped table-bordered table-hover datatable table-condensed">
                    <tr class="well-header">
                        <th class="align_center">CODE 1</th>
                        <th class="align_center">DESCRIPTION</th>
                        <th class="align_center">YEAR ALLOWED</th>
                        <th class="align_center">LEAVE CREDITS</th>
                        <th class="align_center">LEAVE AVAILMENTS</th>
                        <th class="align_center">BALANCE</th>
                    </tr>
                    
                    <? if( count($allowed_leaves) > 0 ): ?>
                        <? foreach( $allowed_leaves as $k=>$each ){ 
                            ?>
                            <? $currentcount = ( isset($leavecounts[$each->code_request]) ) ? $leavecounts[$each->code_request] : "0"; ?>
                            <tr class="well-content">
                                <td><?=$each->code_request;?></td>
                                <td><?=Globals::_e($each->description);?></td>
                                <td><?=$each->code_request;?></td>
                                <td class="align_right"><?=number_format(intval($each->credits) ,2);?></td>
                                <td class="align_right"><?=number_format(intval($currentcount),2);?></td>
                                <td class="align_right"><?=number_format(intval($each->credits-$currentcount),2);?></td>
                            </tr>
                        <? } ?>
                    <? else: ?>
                        <tr><td colspan="6" class="align_center">There are No Allowable Leaves Assigned.</td></tr>
                    <? endif; ?>
                </table> 
            </div>
        </div>
        
        <div class="well red">
            <div class="well-header"><h5>APPLIED LEAVES</h5></div>
            <div class="well-content">
                <table class="table table-striped table-bordered table-hover datatable table-condensed">
                    <tr>
                        <th rowspan="2" class="align_center">DATE APPLIED</th>
                        <th colspan="2" class="align_center">INCLUSIVE DATES</th>
                        <th rowspan="2" class="align_center">TYPE OF LEAVE</th>
                        <th rowspan="2" class="align_center">NO. OF DAY(S)</th>
                        <th rowspan="2" class="align_center">STATUS</th>
                    </tr>
                    <tr>
                        <th class="align_center">FROM</th>
                        <th class="align_center">TO</th>
                    </tr>
                    
                    <? if( count($pendinghistory) > 0 ): ?>
                        <? foreach( $pendinghistory as $each ): ?>
                            <tr>
                                <td class="align_center"><?=date('m/d/Y', strtotime($each->dateapplied));?></td>
                                <td class="align_center"><?=date('m/d/Y', strtotime($each->fromdate));?></td>
                                <td class="align_center"><?=date('m/d/Y', strtotime($each->todate));?></td>
                                <td><?=$each->leavetype;?></td>
                                <td><?=$each->no_days;?></td>
                                <td><?=$each->status;?></td>
                            </tr>
                        <? endforeach; ?>
                    <? else: ?>
                        <tr><td colspan="6" class="align_center">There are No Leaves Applied.</td></tr>
                    <? endif; ?>
                </table>
            </div>
        </div>
        
        <div class="well dark_green">
            <div class="well-header"><h5>HISTORY OF LEAVES</h5></div>
            <div class="well-content">
                <table class="table table-striped table-bordered table-hover datatable table-condensed">
                    <tr><th class="align_center" colspan="8">HISTORY OF LEAVE(S)</th></tr>
                    <tr>
                        <th rowspan="2" class="align_center">DATE APPLIED</th>
                        <th colspan="2" class="align_center">INCLUSIVE DATES</th>
                        <th rowspan="2" class="align_center">TYPE OF LEAVE</th>
                        <th rowspan="2" class="align_center">NO. OF DAY(S)</th>
                        <th rowspan="2" class="align_center">STATUS</th>
                        <th rowspan="2" class="align_center">DATE APPROVED</th>
                        <th rowspan="2" class="align_center">APPROVER</th>
                    </tr>
                    <tr>
                        <th class="align_center">FROM</th>
                        <th class="align_center">TO</th>
                    </tr>
                    
                    <? if( count($leavehistory) > 0 ): ?>
                        <? foreach( $leavehistory as $each ): ?>
                            <tr>
                                <td class="align_center"><?=date('m/d/Y', strtotime($each->dateapplied));?></td>
                                <td class="align_center"><?=date('m/d/Y', strtotime($each->fromdate));?></td>
                                <td class="align_center"><?=date('m/d/Y', strtotime($each->todate));?></td>
                                <td><?=$each->leavetype;?></td>
                                <td><?=$each->no_days;?></td>
                                <td><?=$each->status;?></td>
                                <td class="align_center"><?=date('m/d/Y', strtotime($each->dateapproved));?></td>
                                <td><?=$each->approvedby;?></td>    
                            </tr>
                        <? endforeach; ?>
                    <? else: ?>
                        <tr><td colspan="8" class="align_center">There are No Previous Leaves Approved.</td></tr>
                    <? endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>