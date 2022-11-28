<table class="table table-striped table-bordered table-hover" id="ListTable_def" width="100%">
    <thead >
        <?php
            if($category == 0){
                ?>
                <tr>
                    <th colspan="12"  style="border-bottom-width: 0px; border: 0px; text-align: left;"><a class="btn btn-success batchComplete" id="batchComplete">Batch Completion</a></th>
                </tr><?php
            }
        ?>
        <tr style="background-color: #0072c6;">
            <?php
                if($category == 0){
                    ?>
                        <th class="align_center">Select&nbsp;all <br><input type='checkbox' id="selectall" class="double-sized-cb"></th>
                    <?php
                }
            ?>
            <th class="align_center">Employee ID</th>
            <th class="align_center">Name</th>
            <th>Concerned Office</th>
            <th>Look For</th>
            <th>Type of Clearance</th>
            <th>School Year</th>
            <th>Remarks</th>
            <th>Agreed Date Submission</th>
            <th>Completed</th>
            <th>Date Completed</th>
            <th>Added By</th>
            <th>Date Created</th>
            <?php
                if($category == 0){
                    ?>
                        <th class="align_center">Action</th>
                    <?php
                }
            ?>
        </tr>
    </thead>
    <tbody id="employeelist">
    <?php
        if(count($result) > 0){
               foreach ($result as $key => $row) {
                $subdate = $row->submission_date != null && $row->submission_date != '0000-00-00' ? date('F d, Y',strtotime($row->submission_date)) : '';
                $comdate = $row->date_completed != null && $row->date_completed != '0000-00-00' && $category == 1 ? date('F d, Y',strtotime($row->date_completed)) : '';
                ?>
                <tr>
                <?php
                if($category == 0){
                        ?>
                            <td width="1%" class="align_center"><input type="checkbox" value="1" name="mar" idkey="<?php echo $row->empdef_id?>" /></td>
                        <?php
                    }
                ?>
                <td class="align_center"><?php echo Globals::_e($row->employeeid)?></td>
                <td class="align_center"><?php echo $this->extensions->getEmployeeName($row->employeeid)?></td>
                <td class="align_center"><?=Globals::_e($this->extensions->getOfficeDescriptionReport($row->concerned_dept))?></td>
                <td class="align_center"><?php echo $this->extensions->getEmployeeName($row->lookfor)?></td>
                <td class="align_center"><?=Globals::_e($row->defdesc)?></td>
                <td class="align_center"><?=$row->sy?></td>
                <td class="align_center"><?=Globals::_e($row->remarks)?></td>
                <td class="align_center"><?=($subdate != "00-00-0000" || $subdate != NULL)?$subdate:""?></td>
                <td class="align_center"><?=$row->is_completed==1?"YES":"NO"?></td>
                <td class="align_center"><?=($comdate != "00-00-0000" || $comdate != NULL)?$comdate:""?></td>
                <td class="align_center"><?=($this->extensions->getEmployeeName($row->user) ? Globals::_e($this->extensions->getEmployeeName($row->user)) : Globals::_e($row->user) )?></td>
                <td class="align_center"><?=date('F d, Y',strtotime($row->date_created))?></td>
                <?php
                    if($category == 0){
                        ?>
                            <td class="align_center" style="white-space: nowrap;">
                                <a class='btn btn-primary confirm_def' tbl_id = "<?php echo $row->empdef_id?>" ><i class='glyphicon glyphicon-edit'></i></a>
                                                        <a class='btn btn-warning delete_def' tbl_id = "<?php echo $row->empdef_id?>"><i class='glyphicon glyphicon-trash'></i></a>
                            </td>
                        <?php
                    }
                ?>
            </tr>
            <?php } 
        }
    ?>                                        
    </tbody>
</table>
<div class="modal fade" id="modal-views" role="dialog" data-backdrop="static"></div>
<script type="text/javascript">
    var toks = hex_sha512(" ");

    $(document).ready(function(){
        var table = $('#ListTable_def').DataTable({
            "bSort" : false
        });
        new $.fn.dataTable.FixedHeader( table );
    });

    $('#selectall').on('click',function(){
        var selectallVal = 0;
        if($(this).is(":checked")) selectallVal = 1;
        $('input[name=mar]').each(function(){
            if(selectallVal == 1){
                if(!$(this).is(":checked")){
                    $(this).click();
                }
            }else{
                if($(this).is(":checked")){
                    $(this).click();
                }
            }
            
        })
    })

    $('#ListTable_def').on('click', '.confirm_def', function () {
        var tbl_id = $(this).attr("tbl_id");
        clearingClearance(tbl_id);
    })

    $("#batchComplete").click(function(){
        var counter = 0;
        var tbl_id = '';
        $('#ListTable_def input[name="mar"]').each(function(){
            if($(this).prop("checked") == true){
                if(tbl_id) tbl_id += '~~'+$(this).attr("idkey");
                else tbl_id = $(this).attr("idkey");
                counter++;
            }
        });
        if(counter > 0){
            clearingClearance(tbl_id);
        }else{
            Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: "Select clearance to complete first!",
                    showConfirmButton: true,
                    timer: 1000
                })
        }
    });

    $('#ListTable_def').on('click', '.delete_def', function () {
        var tbl_id = $(this).attr("tbl_id");
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
              deleteClearance(tbl_id);
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
    })

    function clearingClearance(tbl_id=''){
        $('#modal-views').modal('show');
        var form_data = {
            toks     : toks,
            tbl_id    : GibberishAES.enc(tbl_id, toks),
        };
        $.ajax({
           url      :   "<?=site_url("deficiency_/completionOfClearance")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            $("#modal-views").html(msg);
           }
        });
    }

    function deleteClearance(tbl_id=''){
        var form_data = {
            toks     : toks,
            tbl_id    : GibberishAES.enc(tbl_id, toks),
        };
        $.ajax({
           url      :   "<?=site_url("deficiency_/deleteClearance")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'Employee Clearance has been deleted successfully!',
                  showConfirmButton: true,
                  timer: 1000
            })
            loadUnderEmployee();
           }
        });
    }
</script>