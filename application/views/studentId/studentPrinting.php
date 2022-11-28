<?php 

/**
 * @author Kennedy Hipolito
 * @copyright 
 */

?>

<? $this->load->view('studentId/header'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/login.css">

<!-- Modal -->
<div class="modal fade" id="signup_modal" role="dialog"></div>

<div class="container-fluid animated zoomIn">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="panel">
           <div class="panel-heading"><h4><b>ID PRINTING</b></h4></div>
           <div class="panel-body">
                <form>
                    <div class="form-group">
                        <div class="col-md-6 animated zoomIn delay-2s">
                        <div class="col-md-12">
                            <input type=button class="btn btn-primary" value="print" id="print">&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="<?=base_url()?>index.php/student_id/camview" class="btn btn-primary">TAKE PICTURE</a>
                        </div>
                        <br><br><br>
                        <div id="Select" class="col-md-8">
                              <label for="people">Choose From List</label>
                              <select class="chosen" id="people">
                                <option value="">Please Select</option>
                                <?
                                $opt_type = $this->studentt->getDataForPrinting();
                                foreach($opt_type as $val){
                                ?>      
                                <option value="<?=$val->title?>">
                                  <?=$val->name?>
                                </option>
                                <?    
                                }
                                ?>
                              </select>
                        </div>
                        <br><br><br>
                        <div id="Selectmulti" class="col-md-8">
                              <label for="people">Choose From List</label>
                              <select class="chosen" id="peoplemulti" name="tags[]" multiple="">
                                <option value="">Please Select</option>
                                <?
                                $opt_type = $this->studentt->getDataForPrinting();
                                foreach($opt_type as $val){
                                ?>      
                                <option value="<?=$val->title?>">
                                  <?=$val->name?>
                                </option>
                                <?    
                                }
                                ?>
                              </select>
                        </div>
                        <br><br><br><br>
                        <div class="col-md-8">
                            <label for="people">Choose Template</label>
                                <select class="chosen" id="IDtem">
                                    <label for="template">Template</label>
                                    <option value="student">Student</option>
                                    <option value="Employee">Employee</option>
                                </select>
                            </div>
                        <br><br>
                        <input type="checkbox" id="multiple" value="multi" style="margin-left: 20px;">&nbsp;&nbsp; <b>Multiple printing?</b>
                        </div>
                        <div class="col-md-6 animated zoomIn delay-2s">
                            
                            <div class="form-group" id="listResult">

                            </div>
                        </div>
                    </div>
                </form>
           </div>
        </div>
    </div>
</div>

<script>
$('.chosen').chosen();
$(document).ready(function(){
$('#Selectmulti').hide();
});


 $('#multiple').change(function() {
    if($(this).is(":checked")) { 
        $('#Selectmulti').show();
        $('#Select').hide();  
    }else{
        $('#Selectmulti').hide();
        $('#Select').show();    
    }    
    
});

 $("#people").change(function(){
        var id  = $("#people").val();
        $.ajax({
            type: "POST",
            data: {id: id},
            url: "<?= site_url('student_id/viewResult')?>",
            success:function(response){
                $("#listResult").html(response);
            }
        });
    });
        

$("#print").click(function(){
    var template = $("#IDtem").val();
    if ($("#multiple").is(":checked")) {
        var id = $("#peoplemulti").val();
    }else var id  = $("#people").val();
    window.open("<?=site_url("student_id/printID")?>?id="+ id +"&template="+ template +"");

});


</script>