<?php
    $usertype = $this->session->userdata("usertype");
    $datetoday = date("Y-m-d");
    $usertype = $this->session->userdata("usertype");
    $stat_dateresigned2 = $this->extensions->getEemployeeCurrentData($employeeid, "dateresigned");
    $stat_dateresigned2 = ($stat_dateresigned2 && $stat_dateresigned2 != "-0001-11-30" && $stat_dateresigned2 != "1970-01-01" && $stat_dateresigned2 != "0000-00-00")  ? $stat_dateresigned2 : '';
    $accai = $this->extensions->getEemployeeCurrentData($employeeid, 'isactive');
    $accai = ($stat_dateresigned2 == '' && $accai) ? 1 : ($datetoday < $stat_dateresigned2 && $accai) ? 1 : 0;
?>
 <div class="scrollbar">
    <table class="table table-hover table-responsive" id="employeeHistoryTable" width="100%">
        <thead>
            <tr>
                <th>Department</th>
                <th>Office</th>
                <th>Employee Status</th>
                <th>Position</th>
                <th>Start Date</th>
                <th>Date Resigned</th>
                <th class="col-md-2">&emsp;&emsp;</th>
            </tr>
        </thead>
        <tbody class="employeeHistoryTable" >
            <tr >
                <td style="font-weight: 400"><?=$this->extras->getemployeedepartment($deptid)?></td>
                <td style="font-weight: 400" id="currentDept"><?=$this->extras->getemployeeoffice($office)?></td>
                <td style="font-weight: 400"><?=$this->extras->getemployeestatus($employmentstatus)?></td>
                <td style="font-weight: 400" id="currentPos"><?=$this->extras->showPosDesc($position)?></td>
                <td style="font-weight: 400"><?=$datepos?></td>
                <td style="font-weight: 400"><?= ($dateresigned != '0000-00-00' && $dateresigned != null ? $dateresigned : '<i>(Present)</i>' ) ?></td>
                <td class="col-md-1">
                    <div style="float: right;">
                        <a <?=(($usertype === "ADMIN" || $usertype === "admin") || ($usertype === "SUPER ADMIN" || $usertype === "super admin") ? '' : 'style="pointer-events:none;" disabled')?> class='btn btn-primary edit_estat_history' mgmt="<?=$management?>" dept="<?=$deptid?>" office="<?= $office ?>" estat="<?=$employmentstatus?>" pos="<?=$position?>" datepos="<?=$datepos?>" dateresigned="<?=$dateresigned?>" resigned_reason="<?=$resigned_reason?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-plus'></i></a>
                    </div>
                </td>
            </tr> 
            <?php 
            $counter = 1;
            foreach ($employment_history as $key => $obj): ?>
                <?php
                    if($obj->dateresigned == "0000-00-00"){
                        $obj->dateresigned = "";
                    }
                    else{
                        $obj->dateresigned;
                    }
                if($counter > 0){
                ?>
                    <tr >
                        <td style="font-weight: 400"><?=$obj->deptdesc?></td>
                        <td style="font-weight: 400"><?=$obj->officedesc?></td>
                        <td style="font-weight: 400"><?=$obj->statdesc?></td>
                        <td style="font-weight: 400"><?=$obj->posdesc?></td>
                        <td style="font-weight: 400"><?=$obj->dateposition?></td>
                        <td style="font-weight: 400"><?=$obj->dateresigned?></td>
                        <td class="col-md-1">
                            <div style="float: right;">
                                <a class='btn btn-info view_seperation_reason' dstatid="<?=$obj->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-eye-open'></i></a>&nbsp;<a <?=(($usertype === "ADMIN" || $usertype === "admin") || ($usertype === "SUPER ADMIN" || $usertype === "super admin") ? '' : 'style="pointer-events:none;" disabled')?> class='btn btn-warning delete_estat_history' dstatid="<?=$obj->id?>"><i class='glyphicon glyphicon-trash'></i></a> 
                            </div>
                        </td>
                    </tr>

            <?php
                }
                $counter++;
             endforeach; ?>               
        </tbody>
    </table>
</div>
<script type="text/javascript">
     $(document).ready(function(){
        $(".isactive").prop('checked', false);
        if("<?=$accai?>" == 0){
            $(".isactivecb_inactive").prop('checked', true);
        }else{
            $(".isactivecb_active").prop('checked', true);
        }
    })
    $("#employeeHistoryTable .edit_estat_history").unbind().click(function(){
        if("<?=$usertype?>" === "EMPLOYEE"){
            Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Access denied!',
              showConfirmButton: true,
              timer: 2000
            })
            $("#modal-view").modal('toggle');
            return;
        }
        $("#modal-view").find(".modal-footer").html('<button type="button" data-dismiss="modal" class="btn btn-danger modalclose" id="modalclose">Close</button> <button type="button" class="btn btn-success button_save_modal" id="button_save_modal">Save</button>');
        
        var employeeid      = $("#employeeid").val(),
            management      = $(this).attr('mgmt'),
            deptid          = $(this).attr('dept'),
            office          = $(this).attr('office'),
            employmentstat  = $(this).attr('estat'),
            position        = $(this).attr('pos'),
            datepos         = $(this).attr('datepos');
            dateresigned    = $(this).attr('dateresigned');
            resigned_reason = $(this).attr('resigned_reason');

        $("#modal-view").find("h3[tag='title']").text("Edit Employment Status");
        $("#button_save_modal").text("Save");
        var form_data = {
            employeeid:  GibberishAES.enc(employeeid , toks),
            management:  GibberishAES.enc(management , toks),
            deptid:  GibberishAES.enc( deptid, toks), 
            office:  GibberishAES.enc(office , toks),
            employmentstat:  GibberishAES.enc(employmentstat , toks),
            position:  GibberishAES.enc( position, toks),
            datepos:  GibberishAES.enc( datepos, toks),
            dateresigned:  GibberishAES.enc(dateresigned , toks),
            resigned_reason:  GibberishAES.enc(resigned_reason , toks),
            folder :  GibberishAES.enc("employee" , toks),
            page   :  GibberishAES.enc( "estat_modal", toks),
            toks:toks
        };
        $.ajax({
            url: $("#site_url").val() + "/employee_/viewModal",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });  
    });

    $("#employeeHistoryTable .view_seperation_reason").unbind().click(function(){
        $("#modal-view").find("h3[tag='title']").text("Reason of Resignation");
        $("#button_save_modal").hide();
        var id = $(this).attr("dstatid");
        var form_data = {
            id:  GibberishAES.enc(id , toks),
            folder :  GibberishAES.enc("employee" , toks),
            page   :  GibberishAES.enc( "estat_reason_modal", toks),
            toks:toks
        };
        $.ajax({
            url: $("#site_url").val() + "/employee_/viewModal",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        }); 
    });

    $(document).on("click",".delete_estat_history",function(){
        if("<?=$usertype?>" === "EMPLOYEE"){
            Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Access denied!',
              showConfirmButton: true,
              timer: 2000
            })
            return;
        }
        var id = $(this).attr("dstatid");
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
            $.ajax({
                url: $("#site_url").val() + "/employee_/deleteEStatHistory",
                type: "POST",
                data: {estatid:GibberishAES.enc(id, toks), toks:toks},
                dataType: 'JSON',
                success: function(msg){
                    if(msg.err_code == 0){
                        Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully Deleted!',
                          showConfirmButton: true,
                          timer: 1000
                        })
                        loadEmpHistoryTable();
                    }
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
</script>