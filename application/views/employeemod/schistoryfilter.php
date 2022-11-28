<?php
$CI = &get_instance();
$CI->load->model("service_credit");
$ishead = $this->employee->getDeptCode($this->session->userdata("username"));
$ishead = $ishead ? "Cluster Head" : "Head/Dean";
$stat   = isset($stat) ? $stat : "";
$show   = false;


echo $status;
// $status = isset($this->input->post('status'))?$this->input->post('status'):"";
$CI->load->model('utils');
$notif = $CI->utils->getNotif('sc_app_emplist');
$user = $this->session->userdata("username");
?>
               
<table class="table table-hover table-bordered datatable" id="servicecredith">
   
    <thead>
        <tr>
            <th>Actions</th>
            <th>Date of Service Credit</th>
            <th>Service Credit</th>
            <th>Approving Status</th>
            <th>Approving Authority</th>
            <th>Status</th>
            <th>Usage Details</th>
            <th>Remaining Balance</th>
            <th>Mark as read</th>
        </tr>
    </thead>
    <?
        $query = $CI->service_credit->displayservicecredithistory($status);
        // var_dump($query->result());
        if($query->num_rows() > 0){
    ?>
    <tbody>
        <?
       $usage = "";
            foreach($query->result() as $row){    
                  $checkstat = $this->db->query("SELECT * FROM sc_app_emplist WHERE base_id ='$row->id'");
                  for ($i=0; $i <$checkstat->num_rows() ; $i++) { 
                      $data = $checkstat->row($i);
                  }

               
                $bold = $row->isread ? "" : "style='font-weight: bold;'";
          
        ?>
        <!-- <?=$data->dstatus?> -->
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>
            <td>
              <?php if ($row->status != "DISAPPROVED" && $row->status != "APPROVED" && (($data->dstatus != "APPROVED")  )): ?>
                <a class="btn btn-info editbtn" href="#modal-view" data-toggle="modal" idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-edit"></i></a>
                  <a class="btn btn-danger delbtn"  idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-trash"></i></a>
              <?php endif ?>
             </td>
                <td <?=$bold?> class='align_center'><?=date('F d, Y',strtotime($row->date))?></td>
                <td <?=$bold?> class='align_center'><?=$row->service_credit?></td>
                <td <?=$bold?> class='align_center'><?=$row->status?$row->status:"PENDING"?></td>
                <td class='align_center'><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalleaves" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td <?=$bold?> class='align_center'><?=$row->total_sc?($row->total_sc != $row->available_sc?"Used":"Un use"):"Un use"?></td>
                 <td class='align_center'><?php if ($row->status=="APPROVED"): ?>
                   <a href="#" tag='view_usage' data-toggle="modal" data-target="#myModalleaves" 
                  data-date="<?=$row->date?>" idkey="<?=$row->id?>" title="View Usage Details" ><i class="icon-large icon-eye-open"></i></a>
                 <?php endif ?></td>
                 <?php
                 $remaining = $this->db->query("SELECT a.id,a.date,a.needed_service_credit,a.service_credit_date_use,b.status,b.isread,a.date_applied,a.remark
                     FROM sc_app_use a
                     LEFT JOIN sc_app_use_emplist b ON a.id = b.base_id
                     WHERE a.service_credit_date_use LIKE '%$row->date%' AND a.applied_by = '$user' AND b.status='APPROVED'");
                 if ($remaining->num_rows() >0) {
                    $usage = 0;
                     $scdate = $remaining->row(0)->service_credit_date_use;
                     $scuse = $remaining->row(0)->needed_service_credit;
                     $arraySC = explode("/",$scuse);
                     foreach ($arraySC as  $value) {
                       // echo'<pre>';var_dump($value);
                       $usage += $value;
                     
                     // echo '<pre>';echo $usage;
                     // echo $usage;
                     ?>
                      <td><?=$row->status != "APPROVED" && ($row->service_credit == $usage && $row->total_sc == $row->available_sc) ?"0": $row->service_credit - $usage?></td>
                <?php 
                 }
                 }
                 else 
                 {?>
                     <td><?=$row->status == "APPROVED"?$row->service_credit:"0"?></td>
                 <?php
                  }
                 ?>
                
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
<script type="text/javascript">
    $(document).on('click',".delbtn",function(e)
{
var id = $(this).attr("idnumber");
// alert(id);
var ans = confirm("Are you sure you want to delete this record??");
 if(ans){
     $.ajax({
        url:"<?=site_url("service_credit_/SCactions")?>",
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

$("a[tag='view_app']").click(function(){
   $("#myModalleaves").html('');
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list_serviceCredit"
                    };
    $.ajax({
       url      :   "<?=site_url("service_credit_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalleaves").html(msg);
       }
    });
});
$("a[tag='view_usage']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "scusagedetails",
                        date     : $(this).data('date')
                    };
    $.ajax({
       url      :   "<?=site_url("service_credit_/getViewSCUsage")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
       
        $("#myModalleaves").html(msg);
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "sc_app_emplist"},
           success  : function(msg){
            loadschistory();
            location.reload();
           }
        }); 
});

$("#servicecredith").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$(".editbtn").click(function()
{
   var id = $(this).attr("idnumber");
 
        $("#modal-view").find("h3[tag='title']").text("Edit Service Credit");
         $("#button_save_modal").text("Save"); 
        var form_data = {
            code: id
        };
        $.ajax({
            url: "<?=site_url('service_credit_/editSC')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        }); 
});
</script>