<div class="container" style="width: 100%;">
    <form id="form_share_to">
        <div class="form-group">
            <label class="field_name align_right">Share To</label>
            <div class="field">
                <select class="chosen share_to" id="share_to" multiple>
                    <?=$this->employee->loadallofficeheadempid($share_to, true)?>
                </select>
            </div>
        </div>
    </form>
</div>
<script>
  $("#button_save_modalshare").click(function(){
    $.ajax({
          url: "<?=site_url('applicant/saveSharing')?>",
          type: "POST",
          data: {share_to:$("#share_to").val(), app_id:"<?=$app_id?>"},
          success: function(msg){
             Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "Share Tohas been save successfully!" ,
                showConfirmButton: true,
                timer: 1000
            })

             $("#modalcloseshare").click();
          }
      }); 
  })
  $('.chosen').chosen();
</script>