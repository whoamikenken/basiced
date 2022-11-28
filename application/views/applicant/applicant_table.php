<?php
    $CI =& get_instance();
    $CI->load->model('applicantt');
    if (!isset($applicantStatus) || $applicantStatus == 'undefined') $applicantStatus = "";
    if ($status == "all") $status = "";
    $a_list = $CI->applicantt->getApplicantList($status, $applicantStatus);
    // echo "<pre>"; print_r($this->db->last_query()); die;
?>

<table class="table table-striped table-bordered table-hover" id="applicantTable" style="width: 100%">
    <thead >
        <?php
            if($status != ""){
                ?>
                    <tr>
                        <th  style="border-bottom-width: 0px; border: 0px;"><a class="btn btn-primary batch_archive" id="batch_archive">Batch <?=($status == 1 ? 'Archive' : 'Active')?></a></th>
                        <th  style="border-bottom-width: 0px; border: 0px;"><a class="btn btn-danger batch_delete" id="batch_delete">Batch Delete</a></th>
                    </tr>
                <?php
            }
        ?>
        <tr style="background-color: #ffc72c">
            <th class="col-md-1 align_center">#</th>
            <th class="align_center">Applicant ID</th>
            <th class="sorting_asc align_center">Name</th>
            <th class="align_center">Position Applied</th>
            <th class="align_center">Applicant Status</th>
            <th class="align_center">Date Applied</th>
            <th class="align_center">Date Hired</th>
            <th width="20%" class="align_center">Actions</th>
            <?php
                if($status != ""){
                    ?>
                        <th class="align_center" width="5%">Mark all<br><input type="checkbox" class="cbox double-sized-cb"  name="checkall" /></th>
                    <?php
                }
            ?>
        </tr>
    </thead>
    <tbody id="employeelist">
        <?php if($a_list->num_rows() > 0){ $o=1;?>
            <?php
                $app_list = Globals::result_XHEP($a_list->result());
                // echo "<pre>"; print_r($app_list); die;
            ?>
            <?php foreach($app_list as $key => $row){
                $current_status = $CI->applicantt->getLatestStatus($row->applicantId);
                $isendorsed = $CI->applicantt->checkApplicationEndorsement($row->applicantId);
                $code_status = $CI->applicantt->getNextApplicantStatus($current_status);
            ?>
                <tr employeeid='<?=$row->applicantId?>' style="cursor: pointer; <?= ($row->redtag == 1) ? 'background: #ffb6b6' : ''; ?>">
                    <td class="align_center"><?=$o?></td>
                    <td class="align_center"><?=$row->applicantId?></td>
                    <td class="align_left"><?=$row->fullname?></td>
                    <td class="align_center"><?=$row->posdesc?></td>
                    <td >
                        <?php if($isendorsed->num_rows() > 0){ 
                             $endorsement = "[".$isendorsed->row()->status."] Endorsed by ".$this->extensions->getEmployeeName($isendorsed->row()->endorsed_by);
                            ?>
                             <input type="text" class="status_list form-control" readonly="" value="<?=$endorsement?>" style="width: 100%;">
                        <?php }else{ ?>
                            <?php if(!$current_status || $current_status == '80'){ ?>
                                <select class="status_list form-control" style="width: 100%;">
                                    <option value=""> - Select an application status - </option>
                                    <?php foreach($CI->applicantt->getApplicantSetup($row->is_teaching) as $stat_list): ?>
                                        <option value="<?= $stat_list['id'] ?>" <?= ($stat_list['id'] == $current_status) ? "selected" : "" ?> ><?= $stat_list['description'] ?></option>
                                    <?php endforeach ?>
                                </select>
                            <?php }else{ ?>
                                <input type="text" class="status_list form-control" readonly="" value="<?=$this->extensions->getApplicantStatusDesc($code_status)?>" style="width: 100%;">
                            <?php } ?>
                        <?php } ?>
                    </td>
                    <td class="align_center"><?=date("Y-m-d", strtotime($row->dateApplied))?></td>
                    <td class="align_center"><?= $row->datehired != null || $row->datehired != '' ? date("Y-m-d", strtotime($row->datehired)) : '';?></td>
                    <!-- <?php if($applicantStatus == 'COMPLETE') echo '<th class="align_center">'.date("Y-m-d", strtotime($row->datehired)).'</th>'; ?> -->
                    <td class="align_center" <?=($row->datehired? 'style="pointer-events: none"' : '')?> ><button class="btn btn-primary update_stat" type="button" status="<?=$row->isactive?>"><?=($row->isactive == '1' ? 'Mark as archive' : 'Move to active')?></button>&nbsp;<a class="btn btn-danger delbtn"><span class="glyphicon glyphicon-trash"></span></a></td>
                    <?php
                        if($status != ""){
                            ?>
                                <td class="align_center"><input type="checkbox" name="applicantCheck" id="applicantCheck" class="double-sized-cb applicantCheck" applicant_id="<?=$row->applicantId ?>" trid="<?= $row->applicantId ?>"></td>
                            <?php
                        }
                    ?>
                </tr>
            <?  
            $o++;  
            // $row['deptid']
            // $this->extras->getemployeedepartment($row['deptid'])
            }
        }
        ?>  
    </tbody>
</table>
<script>

    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");
    
    $(document).ready(function(){
        $('#applicantTable').DataTable().destroy();
        if("<?=$status?>" != ""){
            var table = $('#applicantTable').DataTable({
                "aoColumnDefs": [
                    { "bSortable": false, "aTargets": [ 7,8 ] }
                ]
            });
        }else{
            var table = $('#applicantTable').DataTable({
            });
        }
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#updateaims").click(function(){
        $("#employeelist").html("<td colspan='5' style='text-align: center'>Updating Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");
        $.ajax({
            url : "<?=site_url("main/aimsupdate")?>",
            success : function(msg){
                alert(msg);
                location.reload();
            } 
        });
    });

    $("#employeelist").delegate("td", "click", function(){
        if($(this).find("button").length) return;
        if($(this).find("input").length) return;
        var employeeid = $(this).closest("tr").attr("employeeid");

        if(employeeid && ($(this).hasClass("align_center") || $(this).hasClass("align_left") ) ){
            var form_data = {
                job :  GibberishAES.enc("edit" , toks),
                applicantId :  GibberishAES.enc(employeeid , toks),
                view:  GibberishAES.enc("applicant/applicant_info" , toks), 
                applicant_status: GibberishAES.enc($(this).closest("tr").find(".status_list").val()  , toks),
                toks:toks
            }; 
            $.ajax({
                url : "<?=site_url("main/siteportion")?>",
                type: "POST",
                data: form_data,
                success: function(msg){
                    $("#content").html(msg);
                }
            });
        }
    });  

    $(".status_list").change(function(){
        var trid = $(this).closest("tr").attr("employeeid");
        var stat_select = $(this).val();
        var formdata = {
            applicantid:  GibberishAES.enc( trid, toks),
            status:  GibberishAES.enc( stat_select, toks),
            toks:toks
        }

        $.ajax({
            url: "<?= site_url('applicant/modifyApplicantStatus') ?>",
            type: "POST",
            data: formdata,
            success:function(response){
                location.reload();
            }
        });

    });

    $("table").delegate(".update_stat", "click", function(e){
        var success_msg = confirm_msg = "";
        if($(this).text() == "Mark as archive") confirm_msg = "You want to move the aplication to applicant archive?";
        else confirm_msg = "You want to move the archive aplication into active application?";
        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: confirm_msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
          if (result.value) {
                e.stopPropagation();
                var trid = $(this).closest("tr").attr("employeeid");
                var status = $(this).attr("status");
                if(status == "1"){
                    status = "0";
                    success_msg = "Application has been archived successfully";
                }
                else{
                    status = "1";
                    success_msg = "Application has been active successfully";
                }
                var formdata = {
                    applicantid: GibberishAES.enc( trid, toks),
                    status: GibberishAES.enc( status, toks),
                    toks:toks
                }

                $.ajax({
                    url: "<?= site_url('applicant/updateActiveStatus') ?>",
                    type: "POST",
                    data: formdata,
                    success:function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: success_msg,
                            showConfirmButton: true,
                            timer: 1000
                        })
                        loadApplicantTable();
                    }
                });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
                e.stopPropagation();
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Application is safe.',
                    'error'
                )
                return;
            }
        })
    });

    $("table").delegate(".delbtn", "click", function(e){
        var success_msg = confirm_msg = "";
        if($(this).text() == "Mark as archive") confirm_msg = "You want to move the aplication to applicant archive?";
        else confirm_msg = "You want to move the archive aplication into active application?";
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
                e.stopPropagation();
                var trid = $(this).closest("tr").attr("employeeid");
                var formdata = {
                    applicantid: GibberishAES.enc( trid, toks),
                    toks:toks
                }
                $.ajax({
                    url: "<?= site_url('applicant/deleteApplication') ?>",
                    type: "POST",
                    data: formdata,
                    success:function(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Application has been deleted successfully',
                            showConfirmButton: true,
                            timer: 1000
                        })
                        loadApplicantTable();
                    }
                });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
                e.stopPropagation();
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Application is safe.',
                    'error'
                )
                return;
            }
        })
    });

    $("input[name='checkall'], #applicantTable_paginate").click(function(){
        if($("input[name='checkall']").prop("checked")){
            $('#applicantTable input[name="applicantCheck"]').each(function(){
                this.checked = true; 
            });
        }else{
            $('#applicantTable input[name="applicantCheck"]').each(function(){
                this.checked = false;
            });
        } 
    });

    $("#batch_archive").click(function(){
        var counter = 0;
        var idlist = '';
        var status = "<?=$status?>";
        $('#applicantTable input[name="applicantCheck"]').each(function(){
            if($(this).prop("checked") == true){
                var trid = $(this).attr("trid");
                idlist += trid+'~';
                counter++;
            }
        });

        if(counter > 0){
            var success_msg = confirm_msg = "";
            if(status == "1") confirm_msg = "You want to move "+counter+" aplication to applicant archive?";
            else confirm_msg = "You want to move "+counter+" aplication into active application?";
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: confirm_msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                  if (result.value) {
                       if(status == "1"){
                            status = "0";
                            success_msg = "Successfully archived "+counter+" applications!";
                        }
                        else{
                            status = "1";
                            success_msg = "Successfully set as active "+counter+" applications!";
                        }
                        $.ajax({
                            url: "<?= site_url('applicant/updateActiveStatusBatch') ?>",
                            type:"POST",
                            data: {idlist:  GibberishAES.enc(idlist, toks), status:  GibberishAES.enc(status, toks), toks:toks},
                            async: false,
                            success:function(response){
                              Swal.fire({
                                  icon: 'success',
                                  title: 'Success!',
                                  text: success_msg,
                                  showConfirmButton: true,
                                  timer: 1000
                              });
                              loadApplicantTable();
                            }
                        });
                  } else if (
                    result.dismiss === Swal.DismissReason.cancel
                  ) {
                        swalWithBootstrapButtons.fire(
                            'Cancelled',
                            'Application is safe.',
                            'error'
                        )
                        return;
                    }
                })
        }else{
            var msg = '';
            if("<?=$status?>" == "1") msg = 'Select data to archive first..';
            else msg = 'Select data to set as active first..'
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: msg,
                showConfirmButton: true,
                timer: 1000
            })
        }
    });

    $("#batch_delete").click(function(){
        batchdelete  = 1;
        var counterDelete = 0;
        var idlist = '';
        $('#applicantTable input[name="applicantCheck"]').each(function(){
            if($(this).prop("checked") == true){
                var trid = $(this).attr("trid");
                idlist += trid+'~';
                counterDelete++;
            }
        });
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You're deleting "+counterDelete+" applications, you won't able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            
            if(counterDelete > 0){
                $.ajax({
                    url: "<?= site_url('applicant/deleteApplicationBatch') ?>",
                    type:"POST",
                    data: {idlist:  GibberishAES.enc(idlist, toks), toks:toks},
                    async: false,
                    success:function(response){
                      Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: "Successfully deleted "+counterDelete+" applications!",
                          showConfirmButton: true,
                          timer: 1000
                      })
                      $("input[name='checkall']").click();
                       loadApplicantTable();
                    }
                });
            }else{
                Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: "Select data to delete first..",
                        showConfirmButton: true,
                        timer: 1000
                    })
            }
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