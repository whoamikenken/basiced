<?php

/**
 * @author Justin
 * @copyright 2015
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
            <form id="frm">
                <div class="well-header">
                    <h5>Mail Payslip</h5>
                </div>
                <div class="well-content">
                    <input class="btn blue col-md-2" type="button" id="sendslip" value="Send Mail" />
                </div>
            </form>
            </div>
        </div>
    </div>
</div>    
</div>
<script>
$("#sendslip").click(function(){
    $.ajax({
       url      :   "<?=site_url("email/send_mail")?>",
       success  :   function(msg){
        alert(msg);
        console.log(msg);
       }
    });
});
</script> 