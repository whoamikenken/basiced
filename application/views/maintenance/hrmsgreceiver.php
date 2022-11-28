<?php

/**
 * @author Justin
 * @copyright 2016
 */

$val = 0;
$query = $this->db->query("SELECT msgaccess FROM user_info WHERE id='$uid'");
if($query->num_rows() > 0){
   $val = $query->row(0)->msgaccess;
}  
?>

<div class="row">
    <table width="100%" border=0>
        <tr>
            <td style="text-align: right;" width="35%">Active</td>
            <td width="15%"><input type="checkbox" name="mycbox" value="1" <?=($val ? " checked" : "")?> /></td>
            <td style="text-align: center;" width="5%">Inactive</td>
            <td><input type="checkbox" name="mycbox" value="0" <?=(!$val ? " checked" : "")?> /></td>
        </tr>
    </table>
</div>

<script>
    var toks = hex_sha512(" ");
$(function(){
    $("input[type='checkbox']").on('change', function() {
        $("input[type='checkbox']").not(this).prop('checked', false);
    });
    $("#button_save_modal").unbind("click").click(function(){
        $.ajax({
            url:"<?=site_url("maintenance_/hrmngmntf")?>",
            type:"POST",
            data:{
               uid: GibberishAES.enc( "<?=$uid?>" , toks),
               val: GibberishAES.enc( $("input[name='mycbox']:checked").val() , toks),
               toks:toks
            },
            success: function(msg){
                // alert(msg);
                Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
                $("#modalclose").click();
                user_setup();
            }
         });
    });
});

</script>