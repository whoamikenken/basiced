<?php

/**
 * @author Justin
 * @copyright 2016
 */
?>
<div id="content"></div>           
<script>
var form_data = {   job : "edit",   employeeid : "<?=$this->session->userdata("username")?>",view: "employee/personal_info"     }; // modified by justin (with e) for ica-hyperion 21442
$.ajax({
    url : "<?=site_url("main/siteportion")?>",
    type: "POST",
    data: form_data,
    success: function(msg){
        $("#content").html(msg);
    }
});
</script>