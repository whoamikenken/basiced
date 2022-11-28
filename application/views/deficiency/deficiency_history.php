<?php
    // echo "<pre>"; print_r($d_list->result());
$datetoday = date('Y-m-d');
    $d_list = $d_list->result();
  $utype = $this->session->userdata('usertype');
  $username = $this->session->userdata("username");
?>

<style>
   
    #offbus {
      table-layout: fixed;
      width: 100% !important;
  }
  #offbus td,
  #offbus th{
      width: auto !important;
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
  table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc, table.dataTable thead .sorting_asc_disabled, table.dataTable thead .sorting_desc_disabled {
    position: relative!important;
}
</style>

<div class="panel">
 <div class="panel-heading" style="background-color: #0072c6;"><h4><b> <?=($utype == "ADMIN") ? 'Employee Clearance History' : 'My Clearance'?></b></h4></div>
 <div class="panel-body">

    <table class="table table-striped table-bordered table-hover" id="offbus">
        <thead style="background-color: #0072c6;">
            <tr>
                <?php if($def != 'def'): ?>
                    <th>&nbsp;</th>
                <?php endif ?>
                <th>Concerned Office</th>
                <th>Look For</th>
                <th>Requirement</th>
                <th>School Year</th>
                <th>Remarks</th>
                <th>Agreed Submission Date</th>
                <th>Status</th>
                <th>Date Completed</th>
                <th>Added By</th>
                <th>Date Created</th>
                <!-- <th>Status</th> -->
                <?php if($def == 'def'): ?>
                  <th class="align_center">Mark All as read <br><input type='checkbox' id="selectall" class="double-sized-cb"></th>
                <?php endif ?>
            </tr>
        </thead>
        <tbody>
           <?php
           if(count($d_list) > 0){
           // echo "<pre>"; print_r($d_list->result()); die;

               foreach ($d_list as $key => $row) {
                $subdate = $row->submission_date != null && $row->submission_date != '0000-00-00' ? date('F d, Y',strtotime($row->submission_date)) : '';
                $comdate = $row->date_completed != null && $row->date_completed != '0000-00-00' ? date('F d, Y',strtotime($row->date_completed)) : '';
                ?>
                <tr <?php echo ($row->isread == 0 && $def == 'def' && $row->is_completed == 1 ? " style='background: #94ff9b'" : ($row->isread == 0 && $def == 'def' && $row->is_completed == 0 && $row->submission_date >= $datetoday ? " style='background: #ffdddd'" : ($row->isread == 0 && $def == 'def' && $row->is_completed == 0 && $row->submission_date < $datetoday ? " style='background: #ff7272'" : $row->submission_date) ) )?>>
                    <?php if($def != 'def'): ?>
                     <td>
                        <div class="align_center">
                            <a class="btn btn-info editbtn"  href="#" data-toggle="modal" data-target="#myModal" idkey="<?=$row->empdef_id?>"><i class="icon glyphicon glyphicon-edit"></i></a>
                            <a class="btn btn-danger delbtn" href="#" idkey="<?=$row->empdef_id?>" ><i class="icon glyphicon glyphicon-trash"></i></a>
                        </div>
                    </td>
                <?php endif ?>
                <td><?=Globals::_e($this->extensions->getOfficeDescriptionReport($row->concerned_dept))?></td>
                <td ><?php echo $this->extensions->getEmployeeName($row->lookfor)?></td>
                <td ><?=Globals::_e($row->defdesc)?></td>
                <td ><?=$row->sy?></td>
                <td ><?=Globals::_e($row->remarks)?></td>
                <td><?=($subdate != "00-00-0000" || $subdate != NULL)?$subdate:""?></td>
                <td ><?=$row->is_completed==1?"CLEARED":"PENDING"?></td>
                <td><?=($comdate != "00-00-0000" || $comdate != NULL)?$comdate:""?></td>
                <td ><?=($this->extensions->getEmployeeName($row->user) ? Globals::_e($this->extensions->getEmployeeName($row->user)) : Globals::_e($row->user) )?></td>
                <td><?=date('F d, Y',strtotime($row->date_created))?></td>
                <!-- <td ><?=($row->status) ? "Confirmed" : "Not Confirmed"?></td> -->
                <?php if($def == 'def'): ?>
                    <td width="1%" class="align_center"><input type="checkbox" value="1" name="mar" idkey="<?php echo $row->empdef_id?>" <?php echo ($row->isread == 1 ? " checked disabled" : "")?> /></td>
                <?php endif ?>
            </tr>
            <? }
        }
        ?>
    </tbody>
<!-- <div id="delete-alert" class="hide">
    <div style="text-align: center"><h5>Are You sure you want to delete this?</h5></div>
</div> -->
</table>


<script type="text/javascript">
  var toks = hex_sha512(" ");
    $(".editbtn").click(function(){
        var idkey = $(this).attr('idkey');

        $.ajax({
         url      :   "<?=site_url("deficiency_/getEmpDefDetails")?>",
         type     :   "POST",
         dataType :   "json",
         data     :   {def_id: GibberishAES.enc(idkey , toks), toks:toks},
         success  :   function(data){
            console.log(data);
            $('#departments').val(data.concerned_dept).trigger("chosen:updated");
            loadDeficiencies(data.concerned_dept, data.def_id);
            
            $('#sySelect').val(data.sy).trigger("chosen:updated");
            $('#lookfor').val(data.lookfor);
            $('#remarks').val(data.remarks);
            // $('#dnow').val(data.date_created);
            $('#dsub').val(data.submission_date);
            if(data.date_completed != '0000-00-00') $('#dcompleted').val(data.date_completed);

            if(data.is_completed == "1") $('#isCompleted').prop('checked',true);
            else                         $('#isCompleted').removeAttr('checked');
            checkCompleted();

            lookforData(data.concerned_dept, data.lookfor);

            $("#edit").attr('idkey',idkey);
            $("#save").hide();
            $("#edit, #cancelEdit").show();
        },
        error : function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
        }        
    });

    });
    $("#offbus").delegate(".delbtn", "click", function() {
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
      }).then((result) => {
          if (result.value) {
              var id = $(this).attr('empdef_id');
              var id = $(this).attr("idkey");
              console.log(id);
              $.ajax({
                url: "<?=site_url('deficiency_/deleteEmpDef')?>",
                type: "POST",
                data: {id:id},
                success: function(msg){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
                    // location.reload();
                    // table.fnDraw();
                    loadDeficiencyHistory($('#employeeid').attr('employeeid'));
                }
            });

          } else if (
            result.dismiss === Swal.DismissReason.cancel
            ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe.',
              'error'
              )
        }
    })
  });


// $(".delbtn").click(function(){
//     var id = $(this).attr("idkey");
// });

// $(document).on("click", '#button_save_modal', function(){
//     var id = $(this).attr('empdef_id');
//     $("#modal-view").find("div[tag='display']").html("<h3>Deleting...</h3>");
//     $.ajax({
//         url: "<?=site_url('deficiency_/deleteEmpDef')?>",
//         type: "POST",
//         data: {id:id},
//         success: function(msg){
//             $("#modal-view").find("div[tag='display']").html(msg);
//             $("#modal-view").modal('hide');
//             loadDeficiencyHistory($('#employeeid').attr('employeeid'));
//     // alert('alert');
//         }
//     });
// });

$(document).ready(function(){
    var table = $('#offbus').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
$('.chosen').chosen();

$('#selectall').on('click',function(){
  $('input[name=mar]').each(function(){
    if(!$(this).is(":checked")){
      $(this).click();
    }
  })
  
  // $('input[name=mar]').prop('checked',true);
});
$("input[name='mar']").click(function(){
   var cval  = $(this).val();
   var idkey = $(this).attr("idkey");

   $(this).attr("disabled",true);
   $(this).closest("tr").removeAttr("style");
   $.ajax({
           url      :   "<?php echo site_url("employeemod_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "employee_deficiency"},
           success  : function(msg){
            //loadbushistory();
            location.reload();
           }
        }); 
});

</script>
