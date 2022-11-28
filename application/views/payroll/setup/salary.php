<?php
/**
 * @author Justin
 * @copyright 2016
 */
$CI =& get_instance();
$CI->load->model('utils');
$hasAccess = true;
$utype = $this->session->userdata('usertype');
    $emp = "";
    if($this->input->post("eid")){
        foreach($this->input->post("eid") as $key=>$val){
            if($emp)    $emp .= ",";
            $emp .= $val;
			if($utype<>"SUPER ADMIN")
			{
				if(!$CI->utils->getUserAccessPayroll($this->session->userdata("userid"), $val)) $hasAccess = false;
			}
		}    
    }
    $data['eid'] = $emp;

?>
<style>
#myModal{
    width: 1160px;
    left: 0;
    right: 0;
    margin: 0 auto;
}
</style>
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <span class="pull-right"><a href="#" class="btn btn-danger" close>X</a></span>
            <form id="myform">  
                <!-- <div class="col-md-12"> -->
                    <div class="col-md-8" >
                    <!--<input type="hidden" name="model" value="esalary" />-->
                    <input type="hidden" name="model"   value="batchencode" />
                    <input type="hidden" name="dept"    value="<?=$this->input->post("dept")?>" />
                    <input type="hidden" name="tnt"     value="<?=$this->input->post("tnt")?>" />
                    <input type="hidden" name="estat"   value="<?=$this->input->post("estat")?>" />
                    <input type="hidden" name="eid"     value="<?=$emp?>" />
                    <input type="hidden" name="cat"     value="<?=$this->input->post("cat")?>" />
                    
                    <?if($this->input->post("cat") == 1){
                        if(!$hasAccess){
                            echo '<h4>Payroll</h4>';
                            echo '<div>No payroll access for this employee.</div>';

                        }else $this->load->view('batch_encode/be_payroll_info',$data);
                    }?>
                    </div>
                    
            </form>

                <?if($this->input->post("cat") == 1){
                    if($hasAccess){?>
                        <div class="col-md-4 pull-right" style="margin-top: 5px;margin-bottom: 5px;" >
                            <div class="form_row">
                            </div>
                            <div class="form_row">
                                <div class="field">
                                    <div id="dhide" hidden=""></div>
                                    <div id="dshow">
                                        <a href="#" class="btn btn-primary" id="savesalary">Save</a>
                                        <a href="#" class="btn btn-danger" close>Close</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form_row">
                            </div>
                        </div>

                    <?}

                }else{?>
                    <div class="col-md-4 pull-right" style="margin-top: 5px;margin-bottom: 5px;" >
                        <div class="form_row">
                        </div>
                        <div class="form_row">
                            <div class="field">
                                <div id="dhide" hidden=""></div>
                                <div id="dshow">
                                    <a href="#" class="btn btn-primary" id="savesalary">Save</a>
                                    <a href="#" class="btn btn-danger" close>Close</a>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                        </div>
                    </div>
                <?}?>
                <!-- </div> -->
        </div>
    </div>

</div>
<script>
$(document).ready(function(){
    /*
     * Save Data
     */
    $("#savesalary").click(function(){
        
        var form_data = $("#myform").serialize();
        $("#dhide").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $("#dshow").hide();
        $.ajax({
            url     :   "<?=site_url("payroll_/loadmodelfunc")?>",
            type    :   "POST",
            data    :   form_data,
            success :   function(msg){
                $("#dhide").hide();
                $("#dshow").show();
                alert(msg);
                $("#myModal").modal('toggle'); 
            }
        });
    });
 });   

$("a[close]").click(function(){   $("#myModal").modal('toggle');  });

$("#schedule").change(function(){
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
           $("select[name='period_drop']").html(msg).trigger("liszt:updated");
        }
    });
});

$("#dschedule").change(function(){
    $("#qshow").hide();
    $("#qload").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-5"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
           $("#qload").hide();
           $("select[name='dperiod_drop']").html(msg).trigger("liszt:updated");
           $("#qshow").show();
        }
    });
});

/*
 *  Other Functions
 */
 
function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
    return nStr;
}

function numbersonly(myfield, e, dec, id)
{
    var key;
    var keychar;
        
    if (window.event)   key = window.event.keyCode;
    else if (e)         key = e.which;
    else                return true;
    keychar = String.fromCharCode(key);
        
    // control keys
    if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;
        
    // numbers
    else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;
        
    // decimal point jump
    else if (dec && (keychar == "."))
    {
        myfield.form.elements[dec].focus();
        return false;
    }
    else    return false;
}
    
/*
 *  Jquery Plug-ins.
 */
 
$(".chosen").chosen();
// $('#datefrom,#dateto,#ddatefrom').datepicker();
</script>