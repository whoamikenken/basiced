<table class="table table-hover table-bordered datatable" id="seminarh">
    <a class="button deleteschedule" style="color:white;">DELETE</a>
    <thead style="background-color: #0072c6;">
        <tr>
            <th align="center">Action</th>
            <th align="center">Employee Number</th>
            <th align="center">Employee Name</th>
            <th align="center">Date Applied</th>
            <th align="center">Effectivity Date</th>                        
            <th align="center">Details</th>                        
            <th align="center">Approving Authority</th>
            <th align="center">Reason</th>
            <th align="center">Status</th>
        </tr>
    </thead>
    <tbody id="manageot">                                                               
            <?
              # displayed list here
              foreach ($cs_list as $list) {
                // echo "<pre>"; print_r($cs_list); die;
                extract($list); # gawin variable yung mga list sa cs_list
                /*status*/
            ?>
                <tr>
                  <!-- approval -->
                  <td align="center" width="10%">
                      <?php if($status=="PENDING"){ ?><a tag='view_d' data-toggle="modal" data-target="#myModalatt" base_id="<?=$base_id?>" idkey="<?=$csid?>" class="btn btn-info" ><span class="glyphicon glyphicon-edit"></span></a><?php } ?>
                      <a tag='delete_app' base_id="<?=$base_id?>" idkey="<?=$csid?>" class="btn btn-danger" ><span class="glyphicon glyphicon-trash"></span></a>
                  </td>
                  
                  <!-- employee number -->
                  <td align="center"><?=$empId?></td>
                  
                  <!-- employee name -->
                  <td align="center"><?=$fullname?></td>
                  
                  <!-- date applied -->
                  <td align="center"><?=date('F d, Y',strtotime($timestamp))?></td>

                  <!-- effective date -->
                  <td align="center"><?=$date_effective?></td>

                  <!-- details -->
                  <td align="center"><!--<?=$filename?>--></td>

                  <!-- approving authority -->
                  <td align="center"><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" base_id="<?=$base_id?>" idkey="<?=$csid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>

                  <!-- reason -->
                  <td align="center"><?=$reason?></td>

                  <!-- status -->
                  <td align="center"><?=$status?></td>

                </tr>
            <?} # end of foreach for $cs_list
            ?>
    </tbody>
</table>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");

    var colhead = $(this).attr('colhead'),
        colstatus = $(this).attr('colstatus'),
        isLastApprover = $(this).attr('isLastApprover');

    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        colhead         : colhead,
                        isLastApprover  : isLastApprover,
                        job             : "edit",
                        view            : "cs_details_manage"
                    };
    $.ajax({
       url      :   "<?=site_url("schedule_/getSchedDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});

$("a[tag='delete_app']").click(function()
{
    if($(this).attr("idkey")) id = $(this).attr("idkey");
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
        var form_data = 
        {
            id:id,
            base_id:base_id,
            job  : "delete"
        };
    var window = confirm("Are you sure you want to delete this application");
    if(window)
    {
        $.ajax
        ({
          url: "<?= site_url('schedule_/SCHEDactions') ?>",
          type: "POST",
          data: form_data,
          success:function(response){
            alert('Successfully Deleted');
            location.reload();
          }
        });
    }
});

$("a[tag='view_app']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey
                    };
    $.ajax({
       url      :   "<?=site_url("schedule_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "change_sched_app_emplist"},
           success  : function(msg){
            location.reload();
           }
        });
});
$(function(){
   $(".par").each(function(){
    if($(this).text() == "")    $("#newrequest").prop("disabled",true);
   }); 
});

$("#seminarh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});

$(".deleteschedule").click(function(){
  $.ajax({
    url: "<?= site_url('schedule_/deleteScheduleApp') ?>",
    success:function(response){

    }
  })
});

$(".no-sort").removeClass("sorting");
</script>