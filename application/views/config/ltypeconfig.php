<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
?>
<style>
.input { font-size:16px; border-color:#cccccc; border-style:solid; padding:9px; border-width:3px; border-radius:12px; text-align: center; font-weight: bolder; } 
.input:focus { outline:none; } 
</style>
<div id="content" class="well"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header">
                        <h5>Leave Type</h5>
                    </div>
                    <div class="well-content no-search">
                    <form id="frmltype">
                        <div class="form_row">
                            <label class="field_name align_right span">Employee Series No.</label>
                            <div class="field">
                                <select name="eid" class="form-control"><?=$this->employee->eseries();?></select>
                            </div>
                        </div>
                        
                        <div class="form_row">
                            <label class="field_name align_right span">Type</label>
                            <div class="field">
                                <select name="type" class="form-control"><?=$this->extras->leavetype();?></select>
                            </div>
                        </div>
                        
                        <div class="form_row">
                            <div class="field">
                                <input id="btnupdate" class="btn btn-primary" type="button" value="Update" />
                            </div>
                        </div>
                    </form>
                    
                    <div class="form_row">
                        <table class="table table-hovered table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>Series No.</th>
                                    <th>Type</th>
                                    <th>User</th>
                                    <th>Date Created</th>
                                </tr>                                
                            </thead>
                            <tbody>
                                <?=$this->extras->showLeaveTrail();?>
                            </tbody>
                        </table>
                    </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
<script>
$("#btnupdate").click(function(){
    var form_data = $("#frmltype").serialize();
    $.ajax({
       url      :   "<?=site_url("configuration_/updateltype")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
            alert(msg);
            location.reload();
       }
    });
});
$(".chosen").chosen();
</script> 