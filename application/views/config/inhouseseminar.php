<div class="panel">
    <div class="panel-heading"><h4><b>Inhouse Seminar</b></h4></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover table-condensed" id="seminarTable" width="100%">
            <thead>                            
                <tr style="background-color: #0072c6;">
                    <th width='10%' align="center"><b>Actions</b></th>
                    <th><b>Username</b></th>
                    <th><b>Seminar Category</b></th>
                    <th><b>Seminar Title</b></th>
                    <th><b>Date</b></th>
                    <th><b>Time</b></th>
                    <th><b>Status</b></th>
                    <th><b>Organizer</b></th>
                    <th><b>Venue</b></th>
                    <!-- <th><b>Location</b></th> -->
                    <!-- <th><b>Attendees</b></th> -->
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach($seminarInfo as $seminarInfos): 
                    if($seminarInfos['attendees'] == 'null') $seminarInfos['attendees'] = 'All';
                    $attendees = "Year: ".$seminarInfos['attendees'];
                    ?>
                    <tr>
                        <td widtd='10%' align="center"><button type="button" class='btn btn-info editSeminar btn_<?= $seminarInfos["id"] ?>' tbl_id = "<?=$seminarInfos["id"]?>"><i class='glyphicon glyphicon-edit'></i></button>&nbsp;<button type="button" class='btn btn-danger deleteSeminar btn_<?= $seminarInfos["id"] ?>' tbl_id = "<?=$seminarInfos["id"]?>" ><i class='glyphicon glyphicon-trash'></i></button></td>
                        <td><?=$seminarInfos["username"]?></td>
                        <td><?=$seminarInfos["Description"]?></td>
                        <!-- <td><?=$seminarInfos["title"]?></td> -->
                        <td><?=$seminarInfos["level"]?></td>
                        <td><?=date("F d, Y", strtotime($seminarInfos["date_from"]))." - ". date("F d, Y", strtotime($seminarInfos["date_to"]))?></td>
                        <td><?=date("h:i A", strtotime($seminarInfos["time_from"]))." - ". date("h:i A", strtotime($seminarInfos["time_to"]))?></td>
                        <td class="align_center">
                            <div class="onoffswitch">
                                <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox gateswitchs" id="<?='id-'.$seminarInfos["username"]?>" username="<?=$seminarInfos["username"]?>" online_id="<?=$seminarInfos["online_id"]?>" tbl_id="<?=$seminarInfos["id"]?>"  <?= $this->setup->checkIfGateIsActive($seminarInfos["username"]) ? ' checked activity="Yes"':' disabled activity="No"' ?>>
                                <label class="onoffswitch-label" for="<?='id-'.$seminarInfos["online_id"]?>">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>  
                        </td>
                        <td><?=$seminarInfos["organizer"]?></td>
                        <td><?=$seminarInfos["venue"]?></td>
                        <!-- <td><?=$seminarInfos["location"]?></td> -->
                        <!-- <td><?= $attendees ?></td> -->
                        <!--<td><b><?=$seminarInfos["regfee"]?></b></td>
                        <td><b><?=$seminarInfos["regdeadline"]?></b></td>-->
                    </tr>
                <?php endforeach ?>
            </tbody>
            
        </table>
    </div>
</div>
<script type="text/javascript">
    var toks = hex_sha512(" ");
    $(".gateswitchs").each(function(){
        var isActive = $(this).attr("activity");
        var username = $(this).attr("tbl_id")
        if(isActive == "Yes") $(".btn_"+username).css("display", "none");
    });

    $(".gateswitchs").click(function(){
        var isActive = $(this).attr("activity");
        var username = $(this).attr("tbl_id");
        if(isActive == "Yes") $(".btn_"+username).css("display", "unset");
    });

    $(".onoffswitch-label").click(function(){
        $("input[name='onoffswitch']").click();
    })

    $("input[name='onoffswitch']").on('click',function(e){
        $(this).attr('disabled','true');

        var online_id = $(this).attr('online_id');
        var username = $(this).attr('username');

        if(online_id){
         $.ajax({
            url: "<?= site_url('maintenance_/forceLogout'); ?>",
            type:"POST",
            data:{
               online_id :  GibberishAES.enc( online_id, toks),
               toks:toks
            },
            success: function(msg){
                if(msg=='1'){
                    $('#errormsg').html('User '+username+' : Successfully logged out.').css('color','green').show().delay(10000).fadeOut();
                }else{
                    $('#errormsg').html('User '+username+' : Failed to log out.').css('color','red').show().delay(10000).fadeOut();
                }
            }
         }); 
     }   

    });

    var table = $('#seminarTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader(table);

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $(".chosen").chosen();

    $('.time').datetimepicker({
        format: 'LT'
    });

    $('#button_save_modal').unbind('click').bind('click', function (e) {
        var iscontinue = true;
        var tbl_id = $(this).attr('tbl_id');
        var formdata   =  '';
        $('#inhouse_form input, #inhouse_form select, #inhouse_form textarea').each(function(){
          if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
          else formdata = $(this).attr('name')+'='+$(this).val();
       })
        if(tbl_id){
            formdata += '&id='+tbl_id;
        }
        $("#inhouse_form input, select").each(function(){
            if(!$(this).val() && $(this).attr("name") != "id" && $(this).attr("name") != "attendees[]" && !typeof $(this).attr("name") === "undefined"){
                $(this).css("border", "1px solid red");
                iscontinue = false;
            }else{
                $(this).css("border", "1px solid #ccc");
            }
        });

        if(!iscontinue) return;

        var issameid = checkIfExisting(tbl_id);
        if(issameid > 0){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Username already exists. Please try another username",
                showConfirmButton: true,
                timer: 1000
            })
            $("input[name='username']").css("border-color", "1px solid red");
            return;
        }else{
            $("input[name='username']").css("border-color", "1px solid #ccc");
        }

        $.ajax({
            url: $("#site_url").val() + "/seminar_/validateInhouseSeminar",
            type: "POST",
            data: {formdata:GibberishAES.enc(formdata, toks), toks:toks},
            dataType: "json",
            success:function(response){
                if(response.stat){
                    if(response.msg == 'Successfully save inhouse seminar.'){
                        if(response.update == 0){
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Inhouse Seminar has been saved successfully.',
                                showConfirmButton: true,
                                timer: 1000
                            })
                        }else{
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Inhouse Seminar has been updated successfully.',
                                showConfirmButton: true,
                                timer: 1000
                            })
                        }
                            
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: response.msg,
                            showConfirmButton: true,
                            timer: 1000
                        })
                    }
                    $("#modalclose").click();
                    loadSeminar();
                    // $("#modal-view").modal("toggle");
                }else{
                    $("#msg_header").removeClass("alert alert-success");
                    $("#msg_header").addClass("alert alert-danger");
                    $("#msg_header").find("strong").text("Failed! ");
                    $("#msg_header").find("span").text(response.msg);
                    $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
                }
            }
        });
    });

    function checkIfExisting(tbl_id){
        var ret_obj = '';
        $.ajax({
            url: "<?=site_url('seminar_/checkIfExisting')?>",
            type: "POST",
            data:{username: GibberishAES.enc( $("input[name='username']").val(), toks), tbl_id: GibberishAES.enc( tbl_id, toks), toks:toks},
            async: false,
            success:function(response){
                ret_obj = response;
            }
        });
        return ret_obj;
    }

    function workshopSelection() {
        $.ajax({
            url: $("#site_url").val() + "/setup_/workshopSelection",
            success:function(response){
                $("#workshop").html(response).trigger("chosen:updated");
            }
        });
    }

    $('.editSeminar').on('click', function(){
        addInhouseSeminar($(this), $(this).attr("tbl_id"));
    })

    $('.deleteSeminar').on('click', function(){
        var tblid = $(this).attr("tbl_id");
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
                url: "<?= site_url('setup_/deleteWorkshop') ?>",
                data: {tblid: GibberishAES.enc(tblid , toks), toks:toks},
                type: "POST",
                success:function(response){
                    if(response){
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Inhouse Seminar has been deleted successfully.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                    }
                    loadSeminar();
                }
            })

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


</script>