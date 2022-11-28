<?php
$datetoday = date("Y-m-d");
?>
<input type="hidden" id="datetoday" value="<?=$datetoday?>">
<div style="margin-left: 40px;">
    <form id="info_form">
        <input type="hidden" name="employeeid" value="<?=$employeeid?>" />
        <div class="row">
            <label class="col-md-12">Department</label>
            <div class="col-md-11">
                <select class="chosen" name="deptid" id="deptid" <?= (isset($retirement) && $retirement == "yes" ? 'disabled' : '') ?>>
                    <?
                        $opt_department = $this->extras->showdepartment();
                        foreach($opt_department as $c=>$val){
                            ?><option<?=($c==$deptid ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-md-12">Office</label>
            <div class="col-md-11">
                <select class="chosen" name="office" id="office" <?= (isset($retirement) && $retirement == "yes" ? 'disabled' : '') ?>>
                    <?
                        $opt_office = $this->extras->showoffice();
                        foreach($opt_office as $c=>$val){
                            ?><option<?=($c==$office ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                        }
                    ?>
                </select>
            </div>
        </div>
<!--         <div class="row">
            <label class="col-md-12">Division Level</label>
            <div class="col-md-11">
                <select class="chosen" name="managementid" id="managementid">
                    <?
                        $opt_type = $this->extras->showManagement();
                        foreach($opt_type as $c=>$val){
                            ?><option<?=($c==$management ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                        }
                    ?>
                </select>
            </div>
        </div> -->
            <div class="row">
                <label class="col-md-12">Employee Status</label>
                <div class="col-md-11">
                    <select class="chosen" name="employmentstat" <?= (isset($retirement) && $retirement == "yes" ? 'disabled' : '') ?>>
                        <?
                            if(isset($retirement) && $retirement == "yes") $employmentstat = "R";
                            $opt_status = $this->extras->showemployeestatus();
                            foreach($opt_status as $c=>$val){
                                ?><option<?=($c==$employmentstat ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                            }
                        ?>
                    </select>
                </div>
            </div>
        <div class="row">
            <label class="col-md-12">Position</label>
            <div class="col-md-11">
                <select class="chosen" name="positionid" <?= (isset($retirement) && $retirement == "yes" ? 'disabled' : '') ?>>
                    <?
                        $opt_type = $this->extras->showPostion();
                        foreach($opt_type as $c=>$val){
                            ?><option<?=($c==$position ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-md-12">Start Date</label>
            <div class="col-md-11" >
                <div class='input-group date datepos' data-date="<?=($datepos != date("Y-m-d") ? date("Y-m-d",strtotime($datepos)) : "")?>" data-date-format="yyyy-mm-dd" <?= (isset($retirement) && $retirement == "yes" ? 'style="pointer-events: none" readonly' : '') ?>>
                    <input class="form-control col-md-12" type="text" id="startDate" name="datepos" value="<?=$datepos?>" value="<?=($datepos != date("Y-m-d") ? date("Y-m-d",strtotime($datepos)) : "")?>"  >
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="row">
            <label class="col-md-12">Date Resigned</label>
            <div class="col-md-11">
                <div class='input-group date dateresigned' data-date="<?=($dateresigned != '0000-00-00' ? date("Y-m-d",strtotime($dateresigned)) : "")?>" data-date-format="yyyy-mm-dd">
                    <input class="form-control col-md-12" type="text" id="DateofSep" name="dateresigned" value="<?=($dateresigned != '0000-00-00' && $dateresigned != '' ? date("Y-m-d",strtotime($dateresigned)) : "")?>">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
                <div class="row" style="display: none;" id="inputsample">
                    <label class="col-md-12">Reason for Leaving:</label>
                    <div class="col-md-12">
                        <div class="">
                            <input type="text" class="form-control" name="reason" value="<?=$resigned_reason?>" />
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <br>
        <input type="button" value="Clear Date Resigned" id="clearResignedModal" class="btn btn-primary col-md-4"/>

        <div class="field">
            <span id="errmsg"></span>
        </div>
    </form>
</div>
<script>
    var toks = hex_sha512(" ");
    // $("input[name='dateresigned']").val('');
    loadReason();
    $("#clearResignedModal").click(function(){
    $("input[name='dateresigned']").val('');
    $("input[name='reason']").val('');
    $('#currentDateres').show();

});

    $('#startDate,#DateofSep').blur(function(){
        var d1 = new Date($("#startDate").val());
        var d2 = new Date($("#DateofSep").val());
        if(d1 > d2){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please fill-up a valid date.",
                showConfirmButton: true,
                timer: 2000
            })
            $(this).val("");
        }
    });

$("#button_save_modal").unbind().click(function(){
    $("#errmsg").html("<h6>This may take a while, please wait...</h6>");

    $('#managementid').prop('disabled', false);
    var form_data = $('#info_form').serialize();
    form_data += "&datepos_old="+$('input[name="dateposition"]').attr('oldvalue');
    form_data += "&dateresigned_old="+$('input[name="dateresigned"]').attr('oldvalue');
    $('#managementid').prop('disabled', true);

    if($("#info_form").valid()){
        $('#currentDateres').show();
        $("#resigned_reason").val($("input[name='reason']").val());
        $(".dateresigned").val($("#DateofSep").val());
        $(".dateresigned").change();
        $.ajax({
            url:"<?=site_url("employee_/editEStat")?>",
            type:"POST",
            data:form_data,
            dataType: 'JSON',
            success: function(msg){

                /*update api*/
                var allcard_data = {
                    "PersonType": "E",
                    "employeeid": $("input[name='employeeid']").val(),
                    "deptid": $("select[name='deptid'] option:selected").text(),
                    "positionid": $("select[name='positionid'] option:selected").text()
                };

                $.ajax({
                    url: "<?=site_url("employee_/updateEmployeeInfoApi")?>",
                    data: allcard_data,
                    type: "POST",
                    success:function(response){
                        Swal.fire({
                          icon: 'success',
                          title: 'success!',
                          text: 'Successfully Saved!',
                          showConfirmButton: true,
                          timer: 1000
                      })
                    }
                });
                /*end*/

                // $('#modalclose').click();
                // $(".isactive").prop("checked", false);
                // $('#currentDept').html(msg.currentDeptDesc);
                // $('#currentOffice').html(msg.currentOfficeDesc);
                // $('#currentEStatus').html(msg.currentEStatDesc);
                // $('#currentPos').html(msg.currentPosDesc);
                // $('#currentDatepos').html(msg.currentDatepos);
                // $("#currentDateres").html(msg.currentDateres);
                // $("#reason").val(msg.currentReason);

                // $('.edit_estat_history').attr('dept',msg.currentDept);
                // $('.edit_estat_history').attr('estat',msg.currentEStatus);
                // $('.edit_estat_history').attr('pos',msg.currentPos);
                // $('.edit_estat_history').attr('datepos',msg.currentDatepos);
                // $(".edit_estat_history").attr("dateresigned",msg.currentDateres);
                // $(".edit_estat_history").attr("resigned_reason",msg.currentReason);
                // if($("#DateofSep").val() && $("#DateofSep").val() <= $("#datetoday").val()) {
                //     $(".isactivecb_inactive").prop("checked", true);
                //     $("#account_status").css("pointer-events", "none");
                // }
                // else {
                //     $(".isactivecb_active").prop("checked", true);
                //     $("#account_status").css("pointer-events", "unset");
                // }
                // $('#estatHistory').prepend(msg.prepend);
                if(msg.err_code == 0){
                    $("#modalclose").click();
                    loadEmpHistoryTable();
                } else {
                    $('#errmsg').html(msg.msg);
                }
            }
        });
    }else {
        $validator.focusInvalid();
        return false;
    }
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(".chosen").chosen();

//Added 5-26-17
$( "#deptid" ).change(function() {
    deptid = $( "#deptid" ).val();
    $.ajax({
        url:"<?=site_url("employee_/getManagementLevel")?>",
        type:"POST",
        data:{deptid : deptid},
        success: function(management){
            $("#managementid").val(management);
        },
        error: function(management){
            Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: management,
              showConfirmButton: true,
              timer: 1000
          })
        }
    });

});

$("#deptid").change(function(){
        $.ajax({
            url : $("#site_url").val() + "/setup_/getOffice",
            type: "POST",
            data: {department:GibberishAES.enc($(this).val(), toks), toks:toks},
            success: function(msg){
                $("#office").html(msg).trigger("chosen:updated");
            }
        });
    });

function loadReason(){
    var dateresigned = $("#DateofSep").val();
    if(dateresigned && dateresigned !='') $("#inputsample").show();
}

$("input[name='dateresigned']").blur(function(){
    $("#inputsample").show();
});

$("#button_save_retirement").click(function(){
    var employeeid = $("input[name='employeeid']").val(),
        dateresigned = $("input[name='dateresigned']").val(),
        resigned_reason = $("input[name='reason']").val();
    if(!dateresigned){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Date Resigned is required.",
            showConfirmButton: true,
            timer: 1000
        })
    }else if(!resigned_reason){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Reason for leaving is required.",
            showConfirmButton: true,
            timer: 1000
        })
    }else{
        $.ajax({
            url:"<?=site_url("retirement_/saveRetirement")?>",
            type:"POST",
            data:{employeeid:GibberishAES.enc(employeeid, toks),dateresigned:GibberishAES.enc(dateresigned, toks),resigned_reason:GibberishAES.enc(resigned_reason, toks), toks:toks},
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "Employment status has been updated successfully.",
                    showConfirmButton: true,
                    timer: 1000
                })
                $("#modalclose").click();
                loadRetireeData($("#status").val(), $("#department").val(), $("#office").val(), $("#month").val());
                $("a[menuid='203']").find(".notifcount").text(response);
                if(response == 0){
                    $("a[menuid='203']").find(".notifdiv").hide();
                }
            }
        });
    }
        
})

</script>