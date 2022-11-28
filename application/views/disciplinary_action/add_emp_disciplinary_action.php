<?php
	//Added (6-2-2017)
    $CI =& get_instance();
    $CI->load->model('disciplinary_action');
	$datetoday = date('Y-m-d');
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
.auto{
    width: auto !important;
}
textarea.form-control {
    width: 78% !important;
}
.form_row label.field_name, .form_row span.field_name {
    width: 19%;
}
</style>
<div class="widgets_area">
    <a href="#" class="btn btn-success" name='backlist' style="margin-bottom: 20px;">Back to employee list</a>
    <div class="row">  
        <div class="col-md-12">
            <div class="panel animated fadeIn">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Offenses</b></h4></div>
                   <div class="panel-body">
			         <form id="formOffense">
                        <br>
                        <div class="form_row">
                            <div class="col-md-4 col-md-offset-1" style="margin-left: 12.2%;">
                                <label class="field_name align_right"><b>Name:</b></label>
                                <div class="field">
                                    <span style="float: left; padding-top: 5px; margin-bottom: 5px;">
                                        <b><?=$fname?></b>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right"><b>Employee ID:</b></label>
                                <div class="field">
                                    <span style="float: left; padding-top: 5px; margin-bottom: 5px;" id="employeeid" employeeid="<?=$employeeid?>">
                                        <b><?=$employeeid?></b>
                                    </span>
                                </div>
                            </div>  
                        </div>
                        <div class="form_row">
                            <div class="col-md-4 col-md-offset-1" style="margin-left: 12.2%;">
                                <label class="field_name align_right"><b>Department:</b></label>
                                <div class="field">
                                    <span style="float: left; padding-top: 5px; margin-bottom: 5px;" id="employeeid" employeeid="<?=$employeeid?>">
                                        <b><?=$this->extensions->getDeparmentDescriptionReport($deptid)?></b>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right"><b>Position:</b></label>
                                <div class="field">
                                    <span style="float: left; padding-top: 5px; margin-bottom: 5px;" id="employeeid" employeeid="<?=$employeeid?>">
                                        <b><?=$this->extensions->getPositionDescription($positionid)?></b>
                                    </span>
                                </div>
                            </div>  
                        </div>
                        <br><br>
                        <div class="form_row">
                            <div class="col-md-4 col-md-offset-1" style="margin-left: 9.4%;">
                                <label class="field_name align_right" style="margin-right: 2%;width: 28%;"><b>Date of Warning</b></label>
                                <div class="field">
                                    <div class='input-group date' id="dateWarning" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" name="dateWarning" id="dateWarning2" value="<?=$datetoday?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right" style="margin-right: 2%;width: 28%;"><b>Date of Violation</b></label>
                                <div class="field"  class='offenseDiv' style="margin-left: 30%;">
                                    <div class='input-group date' id="dateViolation" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style="    width: 133%;">
                                        <input type='text' class="form-control" name="dateViolation" id="dateViolation2" value="<?=$datetoday?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>  
                        </div><br>
                        <div class="form_row">
                            <div class="col-md-4 col-md-offset-1" style="margin-left: 9.4%;">
                                <label class="field_name align_right" style="margin-right: 2%;width: 28%;"><b>Type of Offense</b></label>
                                <div class="field" class='offenseDiv' style="margin-left: 30%;">
                                    <select class="chosen col-md-5" id="offense" name="offense">
                                        <option value="">Select</option>
                                        <?
                                            foreach ($offense_types as $row) {?>
                                                <option value="<?=Globals::_e($row->code)?>"><?=Globals::_e($row->description)?></option>
                                            <?}
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right" style="margin-right: 2%;width: 28%;"><b>Given Action</b></label>
                                <div class="field"  class='offenseDiv' style="margin-left: 30%;width: 93%;">
                                    <select class="chosen col-md-5" id="sanction" name="sanction" style="">
                                    <!--    <?
                                            foreach ($sanction as $row) {?>
                                                <option value="<?=$row->code?>"><?=$row->description?></option>
                                            <?}
                                        ?> -->
                                    </select>
                                </div>
                            </div>
                            <!-- <div class="col-md-2" style="margin-left: 7%;">
                                <a class="btn btn-primary addbtnsanction pull-center"  href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New Diciplinary Action </span></a>
                            </div>  -->  
                        </div>
                        <br>
                		<div class="form_row">
                		    <label class="field_name align_right"><b>Employer's Statement</b></label>
                		    <div class="field no-search">
                		        <textarea rows="4" style="resize: none;" class="form-control isreq" name="employeersStatement" id="employeersStatement" placeholder=""></textarea>
                		    </div>
                		</div>
                        <br>
						<div class="form_row">
                		    <label class="field_name align_right"><b>Employee Statement</b></label>
                		    <div class="field no-search">
                		        <textarea rows="4" style="resize: none;" class="form-control isreq" name="empStatement" id="empStatement" placeholder=""></textarea>
                		    </div>
                		</div>
                        <br>
                		<div class="form_row align_right" style="margin-right: 3%">
                			<div id="loading" hidden=""></div>
                			<div id="saving">
                				<button style="width: 120px;" type="button" id="save" action="add" class="btn btn-success">Save</button>
                                <button style="width: 80px;" type="button" id="cancelEdit" action="cancelEdit" class="btn btn-danger">Cancel</button>
                                <button style="width: 80px;" type="button" id="edit" action="edit" class="btn btn-success">Save</button>
                			</div>
                		</div>
                        <br>
                	</form>
                </div>
            </div>
        </div>
    </div> 
    <div class="row">  
        <div class="col-md-12">
            <div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employee Offense History</b></h4></div>
                   <div class="panel-body">
                	<div id="a_history"  style="padding-bottom:31px;"></div>
                 </div>
            </div>
        </div>
    </div>       
</div>        

<script>
    var toks = hex_sha512(" ");
if("<?=$this->session->userdata('canwrite')?>" == 0) $("#formOffense").css("pointer-events", "none");
else $("#formOffense").css("pointer-events", "");
$(document).ready(function(){  
    loadOffenseHistory("<?=$employeeid?>");
    $("#edit, #cancelEdit").hide();
    getSanction();
});
function getSanction()
{
    $.ajax({
        url: "<?=site_url('disciplinary_action_/getSanction')?>",
        type: "POST",
        // data: form_data,
        success: function(msg){
            $("#sanction").html(msg).trigger('chosen:updated');
            // alert(msg);;
        }
    });
}
$(".addbtnsanction").click(function(){
    var infotype = "code_disciplinary_action_sanction";
    var code = "";
    if($(this).attr("id")) code = $(this).attr("id");
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
	        $(this).css("border-color","red").attr("placeholder", "This field is required.");  
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
		$( "<label id='offenseAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( ".offenseDiv" );
		iscontinue = false;
	}
	
	$( "#sanctionAlert" ).remove();
	if($('#sanction').val() == '')
	{
		$( "<label id='sanctionAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( "#sanctionDiv" );
		iscontinue = false;
	}

    if(!iscontinue)  return false;
    else{
	    $("#saving").hide();
	    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

        var formdata   =  '';
            $('#formOffense input, #formOffense select, #formOffense textarea').each(function(){
              if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
              else formdata = $(this).attr('name')+'='+$(this).val();
           })
            formdata   += "&employeeid=<?=$employeeid?>";
	    $.ajax({
	       url      :   "<?=site_url("disciplinary_action_/saveEmployeeOffense")?>",
	       type     :   "POST",
           dataType :   "json",
	       data     :   {form_data:GibberishAES.enc(formdata , toks), toks:toks},
	       success  :   function(msg){
	        if(msg.err_code == 0){
                Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
            }else{
                Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
            }
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
	        $(this).css("border-color","red").attr("placeholder", "This field is required.");  
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
		$( "<label id='offenseAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( ".offenseDiv" );
		iscontinue = false;
	}
	
	$( "#sanctionAlert" ).remove();
	if($('#sanction').val() == '')
	{
		$( "<label id='sanctionAlert' style='color:red;margin-left:23%'><b>This field is required.</b></label>" ).insertAfter( "#sanctionDiv" );
		iscontinue = false;
	}

    if(!iscontinue)  return false;
    else{
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

            var form_data   =  '';
            $('#formOffense input, #formOffense select, #formOffense textarea').each(function(){
              if(form_data) form_data += '&'+$(this).attr('name')+'='+$(this).val();
              else form_data = $(this).attr('name')+'='+$(this).val();
           })
            form_data   += "&employeeid=<?=$employeeid?>";
            form_data   += "&def_id="+idkey;

         $.ajax({
           url      :   "<?=site_url("disciplinary_action_/saveEmployeeOffense")?>",
           type     :   "POST",
           dataType :   "json",
           data     :   {form_data:GibberishAES.enc( form_data, toks),toks:toks},
           success  :   function(msg){
            if(msg.err_code == 0){
                Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
            }else{
                Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
            }
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
    const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "Do you really want to cancel?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            $("#edit, #cancelEdit").hide();
            $("#save").show();
            clearEntries();
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

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$("a[name='backlist']").click(function(){
   var obj = $(".inner_navigation .main li[class='active'] a"); 
   var site = 'disciplinary_action/disciplinary_action_emplist';
   var root = '2';
   var menuid = '101';
   var titlebar = 'Disciplinary Action';
  
   $("#mainform").attr("action","<?=site_url("main/site")?>");
   $("input[name='sitename']").val(site);
   $("input[name='rootid']").val(root);
   $("input[name='menuid']").val(menuid);
   $("input[name='titlebar']").val(titlebar);
   if($(this).attr("reload") == "yes"){
        location.reload();
   }else{
        if(site) $("#mainform").submit();
   }
   

});

function loadOffenseHistory(employeeid){
   $("#a_history").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("disciplinary_action_/loadOffenseHistory")?>",
      type     :   "POST",
      data     :   {employeeid :  GibberishAES.enc(employeeid , toks), toks:toks},
      success  :   function(msg){
       $("#a_history").html(msg);
       if("<?=$this->session->userdata('canwrite')?>" == 0) $("#a_history").find(".btn").css("pointer-events", "none");
       else $("#a_history").find(".btn").css("pointer-events", "");
      }
   });
}
function clearEntries(){
    $('#employeersStatement, #empStatement').val('');
    $('#dateViolation2, #dateWarning2').val('<?=$datetoday?>');
	$('#offense, #sanction').val("").trigger("chosen:updated");
}
$(".chosen").chosen();
</script>