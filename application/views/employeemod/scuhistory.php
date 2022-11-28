<?php
$CI = &get_instance();
$CI->load->model("service_credit");
$ishead = $this->employee->getDeptCode($this->session->userdata("username"));
$ishead = $ishead ? "Cluster Head" : "Head/Dean";
$stat   = isset($stat) ? $stat : "";
$show   = false;

$status = isset($status) ? $status : "";
$action = isset($action) ? $action : "";

$CI->load->model('utils');
$notif = $CI->utils->getNotif('sc_app_use_emplist');
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#servicecredituseh tr td,#servicecredituseh tr th{
    text-align: center;
}
input[name=mar]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}
</style>
<h5>History of Used Service Credit <?=$notif > 0 ? "<div class='notifdiv' style='color:black'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$notif."</b></span></div>":""?></h5>
<table class="table table-hover table-bordered datatable" id="servicecredituseh">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Date of Service Credit</th>
            <th>Date Used</th>
            <th>Service Credit Used</th>
            <th>Approving Authority</th>
            <th>Approving Status</th>
            <th>Mark as read</th>
        </tr>
    </thead>
    <?
        $query = $CI->service_credit->displayuseservicecredithistory($status);
        if($query->num_rows() > 0){
    ?>
    <tbody>
        <?
            foreach($query->result() as $row){             
                $bold = $row->isread ? "" : "style='font-weight: bold;'";
                $checkstat = $this->db->query("SELECT * FROM sc_app_use_emplist WHERE base_id ='$row->id'");
                for ($i=0; $i <$checkstat->num_rows() ; $i++) { 
                      $data = $checkstat->row($i);
                
                }
        ?>
            <tr <?=($row->status == "APPROVED" && !$row->isread) ? " style='background: #B4CDC6'" : "" ?> >
                <td>
                <?php if(isset($data)){ ?>
                    <?php if (!in_array("APPROVED", get_object_vars($data)) || $row->status == "DISAPPROVED"): ?>
                       <a class="btn btn-info editbtnscuse" href="#modal-view" data-toggle="modal" idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-edit"></i></a>
                       <a class="btn btn-danger delbtnscuse"  idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-trash"></i></a>
                    <?php endif ?>
                <?php } ?>
                </td>
                <td <?=$bold?> class='align_center'><?=date('F d, Y',strtotime($row->date))?></td>
                <td <?=$bold?> class='align_center'>
                  <? 
                    $used_date = "";
                    foreach(explode("/",$row->service_credit_date_use) as $date){
                      if($date){
                        $used_date .= ($used_date) ? " / " : "";
                        $used_date .= date("F d, Y", strtotime($date));
                      }
                    }

                    echo $used_date;
                  ?>					
				        </td>
                <td <?=$bold?> class='align_center'><?=$row->needed_service_credit?></td>
                <td <?=$bold?> class='align_center'><?=$row->status?$row->status:"PENDING"?></td>
                <td class='align_center'>
                  <a href="#" tag='view_app_cs_use' data-toggle="modal" data-target=".sc-use-modal" idkey="<?=$row->id?>" title="View Approval Status" >
                    <i class="icon-large icon-eye-open"></i>
                  </a>
                </td>
                <td width="10%" class='align_center'><input type="checkbox" value="1" name="mar" idkey="<?=$row->id?>"  <?=($row->isread ? " checked disabled" : "")?>  /></td>
            </tr>   
        <?
            }
        ?>
    </tbody>
    <?
        }
    ?>
    
</table>
<div class="modal fade sc-use-modal" id="myModalleave" data-backdrop="static"></div>
<?if($show){?><script>$(".mh").show();</script><?}?>
<script>
$("a[tag='view_app_cs_use']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list_serviceCredit"
                    };
    $.ajax({
       url      :   "<?=site_url("service_credit_/getApprovalSeqStatusUse")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $(".sc-use-modal").html(msg);
       }
    });
});
$("input[name='mar']").click(function(){
   var cval  = $(this).val();
   var idkey = $(this).attr("idkey");
   $(this).attr("disabled",true);
   $(this).closest("tr").removeAttr("style");
   $.ajax({
           url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "sc_app_use_emplist"},
           success  : function(msg){
            loadschistory();
           }
        }); 
});

$("#servicecredituseh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});

$(".delbtnscuse").click(function(){
var id = $(this).attr("idnumber");
var ans = confirm("Are you sure you want to delete this record??");
 if(ans){
     $.ajax({
        url:"<?=site_url("service_credit_/SCUseActions")?>",
        type:"POST",
        data:{
           code: $(this).attr("idnumber"),
           job: "delete"  
        },
        success: function(msg){
            
            $("#modalclose").click();
            $(".inner_navigation .main li .active a").click(); 
             //location.reload();
             alert(msg);
             //alert("Machine has been succefully deleted!");
        }
     }); 
 }  
}); 

$(".editbtnscuse").click(function(){
   var id = $(this).attr("idnumber");
        $("#modal-view").find("h3[tag='title']").text("Edit Service Credit");
        $("#button_save_modal").text("Save"); 
        var form_data = {
            code: id
        };
        $.ajax({
            url: "<?=site_url('service_credit_/editSCU')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        }); 
});
</script>