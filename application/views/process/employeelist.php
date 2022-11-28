<?php
$type_list = $this->extras->getEmpTypeDescription();
$dept_list = $this->extras->showdepartment();
$campus_list = $this->extras->getCampusDescription();
// var_dump($campus_list);
$dtoday = date('Y-m-d');

?>
<div class="col-md-12" style="padding: 0px; margin-bottom: 20px;">
  <a class="btn btn-primary pull-left" href="#dtr-modal" data-toggle='modal' href="#" id="EncodeByBatch"><i class="glyphicon glyphicon-plus-sign"></i> Encode by Batch</a>
</div>
<table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr style="background-color: #0072c6;">
            <th  class="align_center">#</th>
            <th class="align_center sorting_asc">Employee</th>
            <th class="align_center">Fullname</th>
            <th class="align_center">Type</th>
            <th class="align_center">Department</th>
            <th class="align_center">Campus</th>
        </tr>
    </thead>
    <tbody id="employeelist" value="yes">                            
              <?
              if(count($employee)>0){
                  $o=1;
              foreach($employee as $row){
                $row['employeeid'] = Globals::_e($row['employeeid']);
                // echo '<pre>';print_r($campus_list);
              ?>
                <tr class='<?=$row['employeeid']?>' employeeid='<?=$row['employeeid']?>' value='<?=$row['employeeid']?>' <?=$row['employeeid']?> style="cursor: pointer;">
                  <td><?=$o?></td>
                  <td value="<?=$row['employeeid']?>"><?=$row['employeeid']?></td>
                  <td><?=(Globals::_e($row['lname']) . ", " . Globals::_e($row['fname']) . ($row['mname']!="" ? " " . substr(Globals::_e($row['mname']),0,1) . "." : ""))?></td>
                  <td><?= $row['teachingtype'] ?></td>
                  <!-- <td><?= isset($type_list[$row['emptype']])?$type_list[$row['emptype']]:''?></td> -->
                  <td><?= isset($dept_list[$row['deptid']])?strtoupper(Globals::_e($dept_list[$row['deptid']])):"<?=Globals::_e($dept_list)?>" ?></td>
                  <td><?= isset($campus_list[$row['campusid']])?Globals::_e($campus_list[$row['campusid']]):'' ?></td>
                </tr>
              <?  
              $o++;  
              // echo "<pre>"; print_r($campus_list); die;
              }
              }
              ?>                                        
    </tbody>
</table>

<script type="text/javascript">
validateCanWrite();
 var toks = hex_sha512(" "); 
  var table = $('#tables').DataTable({
});
new $.fn.dataTable.FixedHeader( table );

$("#employeediv").hide(); 
$("#datediv").hide();
$("#editdiv").hide();
$(".result").hide();

$("#dto,#dto2,select[name='employeeid']").change(function(){
   $("#employeesched").hide(); 
});

$("input[type='checkbox']").on('change', function() {
    $("input[type='checkbox']").not(this).prop('checked', false);
    //alert($("#chk1:checked").val());
    if($("#chk1:checked").prop('checked',true)){
    
        if($(this).val() == "chkemp"){
            $("#employeediv").show('slow','linear');
            $(this).hide('slow','linear');
            $("#datediv").hide('slow','linear');
            $("#editdiv").hide('slow','linear');
            $("input[type='checkbox']").not(this).show('slow','linear');
            $("#employeesched").hide();
            //$("#emptype").val("");
        }
        if($(this).val() == "chkdate"){
            $("#datediv").show('slow','linear');
            $(this).hide('slow','linear');
            $("#employeediv").hide('slow','linear');
            $("#editdiv").hide('slow','linear');
            $("input[type='checkbox']").not(this).show('slow','linear');
            $("#employeesched").hide();
            //$("#employeeid").val("");
        }
        if($(this).val() == "chkedit"){
            $("#editdiv").show('slow','linear');
            $(this).hide('slow','linear');
            $("#employeediv").hide('slow','linear');
            $("#datediv").hide('slow','linear');
            $("input[type='checkbox']").not(this).show('slow','linear');
            $("#employeesched").hide();
            //$("#employeeid").val("");
        }
    }
});


                    
$("#search_button").click(function(){
var chkbox = $("#chk1:checked").val();
if(chkbox == undefined){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: "Please select atleast one checkbox..",
        showConfirmButton: true,
        timer: 1000
    })
}else{
if(chkbox == "chkemp"){
   if($("select[name='employeeid']").val()){
       $.ajax({
          url : "<?=site_url("process_/verifyemployee")?>",
          type: "POST",
          data: $("#request_form").serialize(),
          success: function(msg){
            var status = $(msg).find("status:eq(0)").text();
            var fullname = $(msg).find("fullname:eq(0)").text();
            var employeeid = $(msg).find("employeeid:eq(0)").text();
            
            if(status==0){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: "Employee does not exists...",
                    showConfirmButton: true,
                    timer: 1000
                });
                $("#employeesched").html("");
                return;
            }else{
                $.ajax({
                    url: "<?=site_url("process_/displayschedule")?>",
                    type: "POST",
                    data:{employeeid:employeeid, chkbox:chkbox},
                    success: function(msg){
                        /** Clear fields first **/
                        $("#employeesched").show().html(msg);
                    }
                });
            }
          }
       }); 
   }else {
     Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: "Employee does not exists...",
          showConfirmButton: true,
          timer: 1000
      });
       return false;
   }
}else if(chkbox == "chkdate"){
    var dto = $("input[name='dto']").val();
$.ajax({
    url: "<?=site_url("process_/displayschedule")?>",
    type: "POST",
    data:{dto:dto, chkbox:chkbox},
    success: function(msg){
    $("#employeesched").show().html(msg);
    }
});
}else{
    var dtoedit = $("input[name='dto2']").val();    
$.ajax({    
    url: "<?=site_url("process_/displayschedule")?>",
    type: "POST",
    data:{dtoedit:dtoedit, chkbox:chkbox},
    success: function(msg){
    $("#employeesched").show().html(msg);
    }
});
}
}
});

// newly added by justin (with e) for #ica-hyperion 20984
function loadAddjustment(){
  var chkbox = $("#chk1:checked").val();
  if(chkbox == "chkemp"){
     if($("select[name='employeeid']").val()){
         $.ajax({
            url : "<?=site_url("process_/verifyemployee")?>",
            type: "POST",
            data: $("#request_form").serialize(),
            success: function(msg){
              var status = $(msg).find("status:eq(0)").text();
              var fullname = $(msg).find("fullname:eq(0)").text();
              var employeeid = $(msg).find("employeeid:eq(0)").text();
              
              if(status==0){
                  Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: "Employee does not exists...",
                      showConfirmButton: true,
                      timer: 1000
                  });
                  $("#employeesched").html("");
                  return;
              }else{
                  $.ajax({
                      url: "<?=site_url("process_/displayschedule")?>",
                      type: "POST",
                      data:{employeeid:employeeid, chkbox:chkbox},
                      success: function(msg){
                          /** Clear fields first */
                          $("#employeesched").show().html(msg);
                      }
                  });
              }
            }
         }); 
     }else {
       Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: "Please select an employee first.",
                      showConfirmButton: true,
                      timer: 1000
                  });
         return false;
     }
  }
}

$("#employeelist").on("click", "tr", function(){
   if($(this).attr("employeeid")){
   $("#manage").hide();
       var form_data = {
        job : GibberishAES.enc("edit", toks),
        chkbox : GibberishAES.enc("chkemp", toks), 
        employeeid : GibberishAES.enc($(this).attr("employeeid"), toks),
        view: GibberishAES.enc("plot_schedule", toks),
        toks:toks
       }; 
       $("#employeesched").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");
       $.ajax({
          url : "<?=site_url("process_/displayschedule")?>",
          type: "POST",
          data: form_data,
          success: function(msg){
            $("#backlist").show();
            $(".result").show();
            $("#employeesched").html(msg);

          }
       });
   }
}); 
$("#backlist").click(function()
{
  $(this).hide();
  $(".result").hide();
   $("#manage").show();
});

$("#EncodeByBatch").unbind().click(function(){ 
   $('.savebatch').hide();
   $('#savedata').hide();
   $('#save').hide();
   $("#dtr-modal").find("h3[tag='title']").html("Batch Adjustment"); 
   $("#dtr-modal").find("div[tag='display']").html("Loading, please wait...");
   $(".save-dtr-setup").text("Save");
   $(".save-dtr-setup").hide();
   $('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
   $("#dtr-modal").find('.modal-footer').append("<a href='#' class='btn btn-danger savebatch' id='savebatch'>Save</a>");
    $.ajax({
     url:"<?=site_url("process_/addbyBatchEncode")?>",
       type: "POST",
       success: function(msg){
          $("#dtr-modal").find("div[tag='display']").html(msg);
       }
   }); 
});

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#EncodeByBatch").css("pointer-events", "none");
    else $("#EncodeByBatch").css("pointer-events", "");
}
</script>