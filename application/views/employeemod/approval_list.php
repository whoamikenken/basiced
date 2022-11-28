<?php

/**
 * @author Justin
 * @copyright 2016
 */
$id     = $this->input->post("idkey");

$approval_position = array("depthead"=>"Dept. Head","hrhead"=>"HR Head","chead"=>"Cluster Head","cphead"=>"Campus Principal","uphy"=>"Univ. Physician","budgetoff"=>"Budget Officer", "financedir"=>"Finance Director", "pres"=>"President");
$seq = array();
$dh = $dhs = $dhd = $up = $ups = $upd = $ups = $hr = $hrd = $hrs = "";
if($id){
    $ishead = $this->employee->getDeptCode($this->session->userdata("username"));
    $ishead = $ishead ? "Cluster Head" : "Head/Dean";
    
    $query = $this->employeemod->getLeaveHead($id);
    if($query->num_rows() > 0){

        foreach($query->result() as $row){
            if($row->dhseq != 0 ){
                $stat = $row->deptheadstatus ? "<a class='btn ".($row->deptheadstatus == "DISAPPROVED" ? "red" : "green")." '>".$row->deptheadstatus."</a>" : "";
                $seq[$row->dhseq] = array("position"=>$approval_position['depthead'], "name"=>$row->depthead, "status"=> $stat, "dateupdated"=>$row->deptheaddate);
            }
            if($row->hhseq != 0 ){
                $stat = $row->hrdirstatus ? "<a class='btn ".($row->hrdirstatus == "DISAPPROVED" ? "red" : "green")." '>".$row->hrdirstatus."</a>" : "";
                $seq[$row->hhseq] = array("position"=>$approval_position['hrhead'], "name"=>$row->hrdir, "status"=> $stat, "dateupdated"=>$row->hrdirdate);
            }  
            if($row->chseq != 0 ){
                $stat = $row->clusterheadstatus ? "<a class='btn ".($row->clusterheadstatus == "DISAPPROVED" ? "red" : "green")." '>".$row->clusterheadstatus."</a>" : "";
                $seq[$row->chseq] = array("position"=>$approval_position['chead'], "name"=>$row->clusterhead, "status"=> $stat, "dateupdated"=>$row->clusterheaddate);
            }
            if($row->cpseq != 0 ){
                $stat = $row->campusprincipalstatus ? "<a class='btn ".($row->campusprincipalstatus == "DISAPPROVED" ? "red" : "green")." '>".$row->campusprincipalstatus."</a>" : "";
                $seq[$row->chseq] = array("position"=>$approval_position['cphead'], "name"=>$row->campusprincipal, "status"=> $stat, "dateupdated"=>$row->campusprincipaldate);
            }            
            if($row->upseq != 0 ){
                $stat = $row->univphystatus ? "<a class='btn ".($row->univphystatus == "DISAPPROVED" ? "red" : "green")." '>".$row->univphystatus."</a>" : "";
                $seq[$row->upseq] = array("position"=>$approval_position['uphy'], "name"=>$row->univphy, "status"=> $stat, "dateupdated"=>$row->univphydate);
            }
            if($row->boseq != 0 ){
                $stat = $row->budgetoffstatus ? "<a class='btn ".($row->budgetoffstatus == "DISAPPROVED" ? "red" : "green")." '>".$row->budgetoffstatus."</a>" : "";
                $seq[$row->boseq] = array("position"=>$approval_position['budgetoff'], "name"=>$row->budgetoff, "status"=> $stat, "dateupdated"=>$row->budgetoffdate);
            }
            if($row->fdseq != 0 ){
                $stat = $row->financedirstatus ? "<a class='btn ".($row->financedirstatus == "DISAPPROVED" ? "red" : "green")." '>".$row->financedirstatus."</a>" : "";
                $seq[$row->fdseq] = array("position"=>$approval_position['financedir'], "name"=>$row->financedir, "status"=> $stat, "dateupdated"=>$row->financedirdate);
            }
            if($row->pseq != 0 ){
                $stat = $row->presidentstatus ? "<a class='btn ".($row->presidentstatus == "DISAPPROVED" ? "red" : "green")." '>".$row->presidentstatus."</a>" : "";
                $seq[$row->pseq] = array("position"=>$approval_position['pres'], "name"=>$row->president, "status"=> $stat, "dateupdated"=>$row->presidentdate);
            }
        }
    }
}ksort($seq);
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
    background: #5C5C5C;
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
        
            <table width="100%">
                <tr>
                    <th>Position</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Date Updated</th>
                </tr>
                <!-- <tr>
                    <td class="align_center"><strong><?=$ishead?></strong></td>
                    <td><strong><?=$this->employee->getfullname($dh)?></strong></td>
                    <td class="align_center"><strong><?=$dhs?></strong></td>
                    <td class="align_center"><strong><?=($dhd ? date("F d, Y",strtotime($dhd)) : "")?></strong></td>
                </tr>
                <?if($up){?>
                <tr>
                    <td class="align_center"><strong>University Physician</strong></td>
                    <td><strong><?=$this->employee->getfullname($up)?></strong></td>
                    <td class="align_center"><strong><?=$ups?></strong></td>
                    <td class="align_center"><strong><?=$upd ? date("F d, Y",strtotime($upd)) : ""?></strong></td>
                </tr>
                <?}?>
                <tr>
                    <td class="align_center"><strong>HR DIRECTOR</strong></td>
                    <td><strong><?=$this->employee->getfullname($hr)?></strong></td>
                    <td class="align_center"><strong><?=$hrs?></strong></td>
                    <td class="align_center"><strong><?=$hrd ? date("F d, Y",strtotime($hrd)) : ""?></strong></td>
                </tr> -->

                <? 
                    foreach($seq as $key => $value){?>
                        <tr>
                            <td class="align_center"><strong><?=$value['position']?></strong></td>
                            <td><strong><?=$this->employee->getfullname($value['name'])?></strong></td>
                            <td class="align_center"><strong><?=$value['status']?></strong></td>
                            <td class="align_center"><strong><?=$value['dateupdated'] ? date("F d, Y",strtotime($value['dateupdated'])) : ""?></strong></td>
                        </tr>
                   <?}
                ?>

            </table>
            
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>