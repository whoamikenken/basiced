<?php

/**
 * @author Justin
 * @copyright 2016
 */
$id     = $this->input->post("idkey");
$dh = $dhs = $dhd = $ups = $hr = $hrd = $hrs = "";
if($id){
    $ishead = $this->employee->getDeptCode($this->session->userdata("username"));
    $ishead = $ishead ? "Cluster Head" : "Head/Dean";
    
    $query = $this->employeemod->getChangeSchedHead($id);
    if($query->num_rows() > 0){
        foreach($query->result() as $row){
            $ch  = $row->chead;
            $chs = $row->cheadstatus ? "<a class='btn ".($row->cheadstatus == "DISAPPROVED" ? "red" : ($row->cheadstatus == "APPROVED" ? "green" : ""))." '>".$row->cheadstatus."</a>" : "";
            $chd = $row->cheaddate;
            $hr  = $row->hrd;
            $hrs = $row->hrdstatus ? "<a class='btn ".($row->hrdstatus == "DISAPPROVED" ? "red" : ($row->cheadstatus == "APPROVED" ? "green" : ""))." '>".$row->hrdstatus."</a>" : "";
            $hrd = $row->hrddate;
        }
    }
}
$isdisabled = "disabled";
?>
<style>
.modal-title{
    color: #9E488F
}
.modal{
    width: 40%;
    left: 0;
    right: 0;
    margin: auto;
}
#leave_app_view th{
    background: #505050;
    color: white;
    font-size: 14px;
}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td><h4 class="modal-title"><strong>APPROVAL STATUS</strong></h4></td>
                </tr>
            </table>
        </div>
        <div class="modal-body" id="leave_app_view">
        
          
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>