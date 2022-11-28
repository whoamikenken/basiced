<div class="panel animated">
 <div class="panel-heading" style="background-color: #0072c6;"><h4 class="h4white"><b>History</b></h4></div>
 <div class="panel-body">
  <table class="table table-striped table-bordered table-hover "  id="timeconfig">
    <thead style="background-color: #0072c6;">
        <tr>
            <th style="width: 7%">Actions</th>
            <th style="width: 30%">Total of Hours(s) per Minute</th>
            <th>Tardy Start</th>
            <th>Absent Start</th>
            <th>Early Dismissal Start</th>
            <th>Sequence</th>
            <th>Year</th>
        </tr>
    </thead>
    
    <tbody id="employeelist">
 <?
 $query = $this->employee->earlydismissal("",true,"");
 foreach($query->result() as $data){
 $exploded_hours = explode(":", $this->time->minutesToHours($data->rangeto));
 // echo "<pre>"; print_r($query); die;
 ?>
    <tr>
        <td><a class="btn btn-info editbtn" href="#dtr-modal" data-toggle="modal" idnumber="<?=$data->id?>"><i class="glyphicon glyphicon-edit"></i></a>
         <a class="btn btn-danger delbtn "  idnumber="<?=$data->id?>"><i class="glyphicon glyphicon-trash"></i></a>
         </td>
        <td><?=$data->rangefrom.' - '.$data->rangeto.'  Minutes'?><b>&nbsp;&nbsp;<?php if($exploded_hours[0]) echo "(".$exploded_hours[0]. " hours & " . $exploded_hours[1]. " minutes)"?></b></td>
        <td><?=$data->tardy?></td>
        <td><?=$data->absent?></td>
        <td><?=$data->early?></td>
        <td><?=$data->sequence?></td>
        <td><?=$data->year?></td>
    </tr>
 <?
 }
?>
    </tbody>
  </table>
 </div>
</div>   

 <script type="text/javascript">
$(".editbtn").click(function(){
var id = $(this).attr("idnumber");
$("#dtr-modal").find("h3[tag='title']").text("Edit Early Dismissal");
$(".save-dtr-setup").text("Save"); 
$.ajax({
    url:"<?=site_url("process_/earlydismissals")?>",
    type:"POST",
    data:{folder:"config",view:"earlydismissaledit",id:id},
    success  :   function(msg){
           $("#dtr-modal").find("h3[tag='title']").text("Edit Subject Config");
            $("#dtr-modal").find("div[tag='display']").html(msg);
    }
});

});

$(".delbtn").click(function(){
var id = $(this).attr("idnumber");
var ans = confirm("Are you sure you want to delete this record??");
 if(ans){
     $.ajax({
        url: "<?=site_url("process_/earlydismissalsActions")?>",
        type: "POST",
        dataType:"json",
        data: {id:id},
        success: function(msg) {
            // alert(msg);
            if (msg.err_code== 0)
            {
                 alert(msg.msg);
                 $("#close").click();
                 loadlogs();
            }
             else
            {
                alert(msg.msg);
                
            }
        }
    });   
    
 }  
}); 


$("#save").unbind("click").bind("click",function(){
    
    $("#msgloads").show().html("<img src='<?=base_url()?>images/loading.gif'> Saving Please wait..");
    $("#save").hide();
    $("#close").hide();
     $.ajax({
        url: "<?=site_url("process_/earlydismissalsActions")?>",
        type: "POST",
        dataType:"json",
        data: $("#frmsc").serialize(),
        success: function(msg) {
            // alert(msg);
            if (msg.err_code== 0)
            {
                 alert(msg.msg);
                 $("#close").click();
            }
             else
            {
                alert(msg.msg);
                
            }
        }
    });   
    
});
var table = $('#timeconfig').DataTable({
});
new $.fn.dataTable.FixedHeader( table );

if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
else $(".btn").css("pointer-events", "");

 </script>