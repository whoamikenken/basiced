<?php

 /**
 * @author Kennedy Hipolito
 * @copyright 2019
 */

$CI =& get_instance();
$CI->load->model('fingerprint');
?>

<div id="content">
    <div class="widgets_area">
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Exclude Finger Print</b></h4></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label class="field_name align_right col-md-3">Department</label>
                                <div class="field col-md-6 col-sm-6">
                                    <select class="form chosen-select" id="department" name="department"><?=$this->extras->getDeptpartment()?></select>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-row">
                            <div class="col-md-12">
                                <label class="field_name align_right col-md-3">Status</label>
                                <div class="field col-md-6 col-sm-6">
                                    <select class="chosen-select col-md-4" name="status" id="status">
                                        <option value="all">All</option>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-row" >
                            <div class="col-md-12">
                                <label class="field_name align_right col-md-3">Employee</label>
                                <div class="field col-md-6 col-sm-6">
                                    <select class="chosen col-md-4" name="employeeid" id="employeeid">
                                        <option value="">Select an employee</option>
                                        <?
                                        $opt_type =  $CI->fingerprint->getEmployeeListExcluded();
                                        foreach($opt_type as $val){
                                            ?>      <option value="<?=Globals::_e($val['employeeid'])?>"><?=Globals::_e($val['employeeid'])?> - <?=Globals::_e($val['fullname'])?></option><?    
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <a type="button" class="btn btn-primary" id="addemp"><span class="glyphicon glyphicon-plus"></span>Add</a>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="row">
                    <div class="col-md-12" id="ExcludeFP">

                    </div>  
                </div>                                                                    
            </div>
        </div>
    </div>   
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
});

getEmployeeExcluded();

function getEmployeeExcluded(){

    $.ajax({
        url:  $("#site_url").val() + "/fingerprint_/getEmployeeExcluded",
        type: "POST",
        data:{isactive: $("#status").val(), deptid: $("#department").val()},
        success:function(response){
            $("#ExcludeFP").html(response);
        }
    });
}

$(".chosen").chosen();

$(".chosen-select").change(function(){
    getEmployeeExcluded();
});

function employeeListManage(type, selected){
    $.ajax({
        url: $("#site_url").val() + "/fingerprint_/loadEmployeeListDropdownExcluded",
        type: "POST",
        data:{},
        success:function(response){
            $("#employeeid").html(response);
            $("#employeeid").trigger("chosen:updated");
        }
    });
}

$("#addemp").click(function(){
        var code = '';
        code = $("#employeeid option:selected").val();
        if (!code) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please Select An Employee!',
                showConfirmButton: true,
                timer: 1000
            })
            return
        }
        $.ajax({
            type: "POST",
            url: "<?= site_url('fingerprint_/addToExcluded')?>",
            data: {toks: toks, code:GibberishAES.enc(code, toks), name:GibberishAES.enc($("#employeeid option:selected").attr("data"), toks)},
            success:function(response){
                if (response) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Success',
                      text: "Employee's fingerprint has been excluded successfully.",
                      showConfirmButton: true,
                      timer: 1500
                    })
                  }else{
                    Swal.fire({
                      icon: 'error',
                      title: 'Oops...',
                      text: 'Your Error Please Coordinate With Developer!',
                      timer: 1500
                    })
                  }
                employeeListManage();
                getEmployeeExcluded();
            }
        });
    });

</script>