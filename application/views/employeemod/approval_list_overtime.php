<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

$count = 1;
$order_arr = array(1=>"1ST", 2=>"2ND", 3=>"3RD", 4=>"4TH", 5=>"5TH", 6=>"6TH", 7=>"7TH", 8=>"8TH", 9=>"9TH", 10=>"10TH");

?>
<style>

.modal{
    left: 0;
    right: 0;
    margin: auto;
}
#leave_app_view th{
    background: #5C5C5C;
    color: white;
    font-size: 14px;
}
th{
        text-align: center;
}
</style>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;" >
                    <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                    <p>D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">APPROVAL STATUS</h3></b></center>
        </div>
        <div class="modal-body" id="leave_app_view">
            <div class="row">
                <table class="table">
                    <tr>
                        <th>Sequence</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Date Updated</th>
                    </tr>
                    <?
                        if(count($arr_aprvl_seq) > 0){
                                foreach ($arr_aprvl_seq as $key => $obj) {?>
                                    <tr>
                                        <td class="align_center" style="padding: 3px;"><strong><?=$order_arr[$count]?> APPROVER</strong></td>
                                        <td class="align_center" style="padding: 3px;">                     <strong><?=Globals::_e($obj['fullname'])?>      </strong></td>
                                        <td class="align_center" style="padding: 3px;">
                                            <strong><?=$obj['status'] <> 'PENDING' ? "<a class='btn ".($obj['status'] == "DISAPPROVED" ? "btn-danger" : "btn-success")." '>".$obj['status']."</a>" : "";?></strong>
                                        </td>
                                        <td class="align_center" style="padding: 3px;"><strong><?=($obj['date'] && $obj['date'] != "0000-00-00") ? date("F d, Y",strtotime($obj['date'])) : ""?></strong></td>
                                    </tr>
                                <?
                                $count++;
                            }
                        }

                    ?>
                </table>
                <br>
                <?php if(count($arr_aprvl_seq) < 1){ 
                    ?> <div class="container" style="background: #b3ffb3;width:60%;float:center;text-align:center;border-radius:10px;"><h4>
                    <?= (isset($approver_admin) && $approver_admin != '' ? '<b>This application was approved by: </b>'.$approver_admin : 'Your request has been approved by the admin.') ?>
                    </h4></div> 
                <?php } ?>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            </div>  
        </div>
    </div>
</div>
