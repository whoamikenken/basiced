<?php
    $CI =& get_instance();
    $CI->load->model('disciplinary_action');
	$datetoday = "";
    $offense_types = $CI->disciplinary_action->getOffensesTypes();
    $sanction = $CI->disciplinary_action->getSanctions();
	
?>
<style>
    .modal{
        width: 50%;
        left: 0;
        right: 0;
        margin: auto;
    }
    table {
        width: 70%;
    }
    td{
        width: 20px; 
    }
</style>
<div class="widgets_area">
    <div class="row-fluid">  
        <div class="span12">
            <div class="panel">
                <div class="panel-heading">
                        <label style="color:#000000;font-size:2em;padding-top:1%"><b>OFFENSES<b></label>
                </div>
                <form id="formOffense">
                    <div class="panel-body" style="border:.5px solid black;border-bottom:0px">
                    <br>
                    <div class="form-row" style="width:50%;">
                        <div class="media">
                            <div class="media-body">
                                <h4 class="media-heading"><b>Name</b>  <?=$fname?></h4>
                                <p style="margin-bottom: 0px;"><b>Employee ID</b>: <?=$employeeid?></p>
                                <p style="margin-bottom: 0px;"><b>Department</b>: <?= $office ?></p>
                                <p style="margin-bottom: 0px;"><b>Position</b>: <?=($positionid) ? $this->extras->showPosDesc($positionid) : 'No position' ?></p>
                            </div>
                        </div>
                    </div><br>
                    <div class="col-md-12" style="margin-top:1%;">
                        <div class="col-md-6">
                            <label class="field_name align_right"><b>Date of Warning</b></label>
                            <div class="field">
                                <div class='input-group date' id="dateWarning" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="dateWarning" id="dateWarning2" type="text" value="<?=$datetoday?>"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:1%;">
                        <div class="col-md-6">
                            <label class="field_name align_right"><b>Type of Offense</b></label>
                            <div class="field" id='offenseDiv'>
                                <select class="chosen span5" id="offense" name="offense">
                                    <option value="ET">EXCESSIVE TARDINESS</option>
                                    <option value="EA">EXCESSIVE ABSENCES</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            &nbsp;&nbsp;&nbsp;&nbsp;<b>Date of Violation</b>&nbsp;&nbsp;
                            <div class='input-group date' id="dateViolation" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control" size="16" name="dateViolation" id="dateViolation2" type="text" value="<?=$datetoday?>"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:1%;">
                        <div class="col-md-6">
                            <label class="field_name align_right"><b>Employer's Statement</b></label>
                            <div class="field no-search">
                                <textarea class="form-control" rows="4" style="resize: none;" class="span8 isreq" name="employeersStatement" id="employeersStatement" placeholder=""></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="field_name align_right"><b>Employee Statement</b></label>
                            <div class="field no-search">
                                <textarea class="form-control" rows="4" style="resize: none;" class="span8 isreq" name="empStatement" id="empStatement" placeholder=""></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top:1%;">
                        <div class="col-md-6">
                            <label class="field_name align_right"><b>Given Action</b></label>
                            <div class="field no-search" id="sanctionDiv">
                                <select class="chosen span5" id="sanction" name="sanction">
                                <?
                                foreach ($sanction as $row) {?>
                                <option value="<?=$row->code?>"><?=$row->description?></option>
                                <?}
                                ?>
                                </select>
                                <a class="btn btn-primary addbtnsanction pull-center"  href="#modal-view" data-toggle="modal" style="margin-top: 1%;"><i class="icon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New Diciplinary Action </span></a>
                            </div>
                        </div>
                    </div>
                    <div class="form-row align_right">
                        <div id="loading" hidden=""></div>
                        <div id="saving">
                            <button style="width: 120px;" type="button" id="save" action="add" class="btn btn-primary">Add Offense</button>
                            <button style="width: 80px;" type="button" id="cancelEdit" action="cancelEdit" class="btn grey">Cancel</button>
                            <button style="width: 120px;" type="button" id="edit" action="edit" class="btn orange">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">  
    <div class="span12">
        <div class="panel">
            <div class="panel-body">
                <div id="a_history"  style="padding-bottom:31px;"></div>
            </div>
        </div>
    </div>
</div>    

<script>
    
    $(".chosen").chosen();
    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });
    
    $(document).ready(function(){  
        loadOffenseHistory("<?=$employeeid?>");
        $("#edit, #cancelEdit").hide();
        getSanction();
    });

    function getSanction(){
        $.ajax({
            url: "<?=site_url('disciplinary_action_/getSanction')?>",
            type: "POST",
            // data: form_data,
            success: function(msg){
                $("#sanction").html(msg).trigger('liszt:updated');
                // alert(msg);;
            }
        });
    }

    $(".addbtnsanction").click(function(){
        var infotype = "code_disciplinary_action_sanction";
        var code = $("sanction").val();
        if(!code){
            alert("Please select given action first!");
            return;
        }
        $("#modal-view").find("h3[tag='title']").text(code ? "Edit Disciplinary Sanction" : "Add Disciplinary Sanction");
        $("#button_save_modal").text("Save");
        var form_data = {
            info_type:  GibberishAES.enc( infotype, toks),
            action:  GibberishAES.enc(code , toks),
            toks:toks
        };
        $.ajax({
            url: "<?=site_url('disciplinary_action_/viewForm')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            
            }
        });  
    });

    $("#save").click(function(){
        var iscontinue  = true;

        $("#formOffense .isreq").each(function(){
            if($(this).val() == ""){
                $(this).css("border-color","red").attr("placeholder", "This field is required!.");  
                iscontinue = false;
            }
            else
            {
                $(this).css("border-color","");  
            }
        });
        
        $( "#offenseAlert" ).remove();
        if($('#offense').val() == '')
        {	
            $( "<label id='offenseAlert' style='color:red;margin-left:23%'><b>This field is required!.</b></label>" ).insertAfter( "#offenseDiv" );
            iscontinue = false;
        }
        
        $( "#sanctionAlert" ).remove();
        if($('#sanction').val() == '')
        {
            $( "<label id='sanctionAlert' style='color:red;margin-left:23%'><b>This field is required!.</b></label>" ).insertAfter( "#sanctionDiv" );
            iscontinue = false;
        }

        if(!iscontinue)  return false;
        else{
            $("#saving").hide();
            $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

            var form_data   =   $("#formOffense").serialize();
                form_data   += "&employeeid=<?=$employeeid?>";
            $.ajax({
            url      :   "<?=site_url("disciplinary_action_/saveEmployeeOffense")?>",
            type     :   "POST",
            dataType :   "json",
            data     :   form_data,
            success  :   function(msg){
                alert(msg.msg);
                $("#saving").show();
                $("#loading").hide();
                loadOffenseHistory("<?=$employeeid?>");
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) { 
                    console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
                }      
            });

            clearEntries();

        }
        
    });

    $("#edit").click(function(){
        var iscontinue  = true;
        var idkey = $(this).attr('idkey');

        $("#formOffense .isreq").each(function(){
            if($(this).val() == ""){
                $(this).css("border-color","red").attr("placeholder", "This field is required!.");  
                iscontinue = false;
            }
            else
            {
                $(this).css("border-color","");  
            }
        });
        
        $( "#offenseAlert" ).remove();
        if($('#offense').val() == ''){	
            $( "<label id='offenseAlert' style='color:red;margin-left:23%'><b>This field is required!.</b></label>" ).insertAfter( "#offenseDiv" );
            iscontinue = false;
        }
        
        $( "#sanctionAlert" ).remove();
        if($('#sanction').val() == ''){
            $( "<label id='sanctionAlert' style='color:red;margin-left:23%'><b>This field is required!.</b></label>" ).insertAfter( "#sanctionDiv" );
            iscontinue = false;
        }

        if(!iscontinue)  return false;
        else{
            $("#saving").hide();
            $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

            var form_data   =   $("#formOffense").serialize();
                form_data   += "&employeeid=<?=$employeeid?>";
                form_data   += "&def_id="+idkey;

            $.ajax({
            url      :   "<?=site_url("disciplinary_action_/saveEmployeeOffense")?>",
            type     :   "POST",
            dataType :   "json",
            data     :   form_data,
            success  :   function(msg){
                alert(msg.msg);
                $("#saving").show();
                $("#loading").hide();
                loadOffenseHistory("<?=$employeeid?>");
            },
            error : function(XMLHttpRequest, textStatus, errorThrown) { 
                    console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
                }      
            });

        }
        $("#save").show();
        $("#edit, #cancelEdit").hide();
        clearEntries();
        
    });

    $("#cancelEdit").click(function(){
        $("#save").show();
        $("#edit, #cancelEdit").hide();
        clearEntries();
    });

    $("a[name='backlist']").click(function(){
        var obj = $(".inner_navigation .main li[class='active'] a"); 
        var site = $(obj).attr("site");
        var root = $(obj).attr("root");
        var menuid = $(obj).attr("menuid");
        var titlebar = $(obj).text();
        
        $("#mainform").attr("action","<?=site_url("main/site")?>");
        $("input[name='sitename']").val(site);
        $("input[name='rootid']").val(root);
        $("input[name='menuid']").val(menuid);
        $("input[name='titlebar']").val(titlebar);
        
        if(site) $("#mainform").submit();

    });

    function loadOffenseHistory(employeeid){
        $("#a_history").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
        $.ajax({
            url      :   "<?=site_url("disciplinary_action_/loadOffenseHistory")?>",
            type     :   "POST",
            data     :   {employeeid : employeeid},
            success  :   function(msg){ 
                $("#a_history").html(msg);
            }
        });

    }
    function clearEntries(){
        $('#employeersStatement, #empStatement').val('');
        $('#dateViolation2, #dateWarning2').val('<?=$datetoday?>');
        $('#offense, #sanction').val("").trigger("liszt:updated");
    }

</script>