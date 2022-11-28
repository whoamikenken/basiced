<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

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
    background: #2E5266;
    color: white;
    font-size: 14px;
}

.admin-approved{
    background : #b3ffb3;
    width : 50%;
    float : center;
    border-radius : 10px;    
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
                <?

                    foreach ($arr_aprvl_seq as $key => $obj) {?>
                        <tr>
                            <td class="align_center"><strong><?=$obj['position_name']?> </strong></td>
                            <td>                     <strong><?=$obj['fullname']?>      </strong></td>
                            <td class="align_center">
                                <strong><?=$obj['status'] <> 'PENDING' ? "<a class='btn ".($obj['status'] == "DISAPPROVED" ? "red" : "green")." '>".$obj['status']."</a>" : "";?></strong>
                            </td>
                            <td class="align_center"><strong><?=$obj['date'] && $obj['date'] != "0000-00-00" ? date("F d, Y",strtotime($obj['date'])) : ""?></strong></td>
                        </tr>
                    <?}
                ?>
            </table>
            
        <? if($is_admin_approved): ?>
            <br>
            <center>
                <div class="admin-approved">
                    <h4>
                        Your request has been <?=strtolower($status)?> by the admin.
                    </h4>
                </div>
            </center>
        <? endif; ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>