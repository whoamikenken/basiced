<a id="addleave" class="btn btn-primary" data-toggle="modal" data-target="#modal-view" style="margin-bottom: 5px;"><span><i class="glyphicon glyphicon-plus-sign"></i></span> Add New</a>
<div class="panel  animated fadeIn delay-1s">
    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Leave Type</b></h4></div>
    <div class="panel-body">
        <table class="table table-striped table-bordered table-hover" id="leave_request" width="100%">
            <thead style="background-color: #0072c6; color: black;">
              <tr>
                <th class="align_center">Actions</th>
                <th>Description</th>
              </tr>
            </thead>   
            <tbody id="app_list">

            </tbody>
        </table>
    </div>
</div>
<script>

loadOnlineApplicationList();

function loadOnlineApplicationList(){
    $.ajax({
        url: "<?php echo  site_url('leave_/onlineApplicationList') ?>",
        success:function(response){
            $("#app_list").html(response);
        }
    });
}

</script>