<div class="panel  animated fadeIn delay-1s">
    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Other Type</b></h4></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" id="oth_request" width="100%">
            <thead style="background-color: #0072c6; color: black;">
              <tr>
                <th class="align_center">Actions</th>
                <th>Description</th>
              </tr>
            </thead>   
            <tbody id="app_list_other">

            </tbody>
        </table>
    </div>
</div>
<script>

loadOnlineApplicationListOther();

function loadOnlineApplicationListOther(){
    $.ajax({
        url: "<?php echo  site_url('leave_/onlineApplicationList') ?>",
        type: "POST",
        data:{type:"other"},
        success:function(response){
            $("#app_list_other").html(response);
        }
    });
}

</script>