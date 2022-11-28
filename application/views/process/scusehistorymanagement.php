<?php
$CI = &get_instance();
$CI->load->model("service_credit");
$ishead = $this->employee->getDeptCode($this->session->userdata("username"));
$ishead = $ishead ? "Cluster Head" : "Head/Dean";
$stat   = isset($stat) ? $stat : "";
$show   = false;
$user   = $this->session->userdata("username");
$status = $this->input->post('status');
$dfrom  = $this->input->post('dfrom');
$dto    = $this->input->post('dto');
$CI->load->model('utils');
$notif = $CI->utils->getNotif('sc_app_use_emplist');
?>
<style>
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
<div class="panel">
 <div class="panel-heading"><h4><b>USED SERVICE CREDIT LIST <?=$notif > 0 ? "<div class='notifdiv' style='color:black'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$notif."</b></span></div>":""?></b></h4></div>
 <div class="panel-body">
<table class="table table-striped table-bordered table-hover" id="servicecredituseh">
    <thead style="background-color: #0072c6;">
        <tr>
            <th>Actions</th>
            <th>Employee</th>
            <th>Fullname</th>
            <th>Date of Service Credit</th>
            <th>Date Used</th>
            <th>Service Credit Used</th>
            <th>Approving Authority</th>
            <th>Approving Status</th>
            <th>Usage Details</th>
            <th style="display:none;">Mark as read</th>
        </tr>
    </thead>
    <?
        $query = $CI->service_credit->displayuseservicecredithistoryManagement($user,$status,$dfrom,$dto);


        if($query->num_rows() > 0){
    ?>
    <tbody>

        <?
            foreach($query->result() as $row){   

                $bold = $row->isread ? "" : "style='font-weight: bold;'";
        ?>
            <tr>
              <td>
                <?php if ($row->status !="APPROVED"): ?>
                  <a class="btn btn-info editbtns"  href="#modal-view" data-toggle="modal" idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-edit"></i></a>
                <?php endif ?>
                <a class="btn btn-danger delbtns" idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-trash"></i></a>
              </td>
                 <td><?=$row->employeeid?></td>
                 <td><?= $this->extensions->getEmployeeName($row->employeeid) ?></td>
                <td <?=$bold?> class='align_center'><?=date('F d, Y',strtotime($row->date))?></td>
                <td <?=$bold?> class='align_center'>
          <?
            $return="";
            foreach(explode("/",$row->service_credit_date_use) as $k => $v)
            {
              if($return) $return .= " / ";
              $return .= $v!=""?date("F d, Y",strtotime($v)):"";
            }
            echo $return;
          ?>
        </td>
         <td <?=$bold?> class='align_center'><?=$row->needed_service_credit?></td>
        <td class='align_center'><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalSCUse" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
        <td <?=$bold?> class='align_center'><?=$row->status?$row->status:"PENDING"?></td>
        <td class='align_center'>
                   <a  tag='view_usage_use' href="#modal-view" data-toggle="modal" idnumber="<?=$row->id?>" ><i class="icon-large icon-eye-open"></i></a>
        </td>
        <td width="10%" class='align_center' style="display:none;"><input type="checkbox" value="1" name="mar" idkey="<?=$row->id?>"  <?=($row->isread ? " checked disabled" : "")?>  /></td>
            </tr>   
        <?
            }
        ?>
    </tbody>
    <?
        }
    ?>
    
</table>
<div class="modal fade" id="myModalleave" data-backdrop="static"></div>
<div class="modal fade" id="myModalSCUse" data-backdrop="static"></div>
</div>
<?if($show){?><script>$(".mh").show();</script><?}?>
<script>

  $(document).ready(function(){
    var table = $('#servicecredituseh').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$("a[tag='view_app']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    // alert(idkey);return;
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
     
        $("#myModalSCUse").html(msg);
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

$(".delbtns").click(function()
{
  var id = $(this).attr('idnumber');
  var ans = confirm("Are you sure do you want to delete data?");
  if (ans) {
    $.ajax({
      url:"<?=site_url("service_credit_/serviceCreditActions")?>",
      type:"POST",
      data:{job:"delete",id:id},
      dataType:"JSON",
      success:function(msg)
      { 

        if (msg.err_code == 0) {
          alert(msg.msg);
          
          location.reload();
        }
        else
        {
          alert(msg.msg);
           
          location.reload();
        }

      }
    })
  }
});

$(".editbtns").click(function()
{
   var id = $(this).attr("idnumber");
 
        $("#modal-view").find("h3[tag='title']").text("Edit Service Credit Used");
         $("#button_save_modal").text("Save"); 

        var form_data = {
            code: id
        };
        $.ajax({
            url: "<?=site_url('service_credit_/editSCUseManagement')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        }); 
});

$("a[tag='view_usage_use']").click(function(){
    var id = $(this).attr("idnumber");
 
        $("#modal-view").find("h3[tag='title']").text("Edit Service Credit Used");
         $("#button_save_modal").text("Save"); 

        var form_data = {
            code: id,
            details: true
        };
        $.ajax({
            url: "<?=site_url('service_credit_/editSCUseManagement')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        }); 
});
</script>