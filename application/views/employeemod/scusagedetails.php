<?php
// $CI = &get_instance();
 $this->load->model("service_credit");
$dates = $this->input->post('date');
?>
<style>
.modal-title{
    color: #9E488F
}
.modal{
    width: 80%;
    left: 0;
    right: 0;
    margin: auto;
}
#leave_app_view th{
    background: #4a4a4a;
    color: white;
    font-size: 14px;
}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
           <table width="100%">
                <tr>
                    <td><h4 class="modal-title"><strong>Detailed Usage</strong></h4></td>
                </tr>
            </table>
        </div>
        <div class="modal-body" id="leave_app_view">
        
            <table width="100%">
                <tr>
                    <th width="30%" <?= (!isset($employeeid)) ? "hidden" : "" ?> >Fullname</th>
                    <th width="10%" <?= (!isset($employeeid)) ? "hidden" : "" ?>>Date Applied</th>
                    <th width="10%">Date of Usage</th>
                    <th width="10%">Credit Used</th>
                    <th width="10%">Approving Authority</th>
                    <th width="20%">Remarks</th>
                    <th width="10%">Status</th>
                </tr>
               
                   <?
                $query = $this->service_credit->displayuseservicecredithistorybyfilter($dates)->result();
                if(count($query) > 0){
                    foreach ($query as $key) {
                    // var_dump($key->status);
                                $scd = $key->service_credit_date_use;
                                $service_credit_date_uses = explode('/', $scd);
                                // var_dump($service_credit_date_uses[0]);
                                if ($service_credit_date_uses[0] == $dates || $service_credit_date_uses[1] == $dates ) {
                                    $credituse = $key->needed_service_credit /2;
                                 } 
                            ?>
                            <tr>
                                <td class="align_center" <?= (!isset($employeeid)) ? "hidden" : "" ?> ><?= $this->extensions->getEmployeeName($employeeid) ?></td>
                                <td class="align_center" <?= (!isset($employeeid)) ? "hidden" : "" ?>><strong><?=date("Y-m-d",strtotime($key->date))?> </strong></td>
                                <td class="align_center"><?=$key->service_credit_date_use?></td>
                                <td class='align_center'> <strong><?=$service_credit_date_uses[1]==""?$key->needed_service_credit:$credituse?></strong></td>
                                <td class='align_center'><a href="#"  tag='view_app'   idkey="<?=$key->id?>" title="View Approving Authority" ><i class="icon-large icon-eye-open"></i></span>
                                </td>
                                <td class="align_center">
                                <strong><?=$key->remark?$key->remark:""?></strong>
                                </td>
                                <td class="align_center"><?=$key->status?$key->status:""?></strong></td>
                            </tr>
                        <?}
                    }else{ ?>
                        <tr><td class='align_center' colspan="7"><h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This service credit has not been used. No details available.</h4></td></tr>
                <?
                    }

                ?>
            </table>
            <br>
        
            <div class="" id="Usage" style="display: none"></div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>

<script type="text/javascript">
    $("a[tag='view_app']").click(function(){
    
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    // alert(idkey); return;
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list_serviceCreditUse"
                    };

     $("#Usage").show().html("<img src='<?=base_url()?>images/loading.gif' /> Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("service_credit_/getApprovalSeqStatusUse")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        console.log(msg);
        $("#Usage").html(msg);
       }
    });
});


$("#close").click(function(){

$("#Usage").hide();
});
</script>