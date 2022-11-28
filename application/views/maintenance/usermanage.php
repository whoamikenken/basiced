<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content" class="well"> <!-- Content start -->
<a id="adduser" class="btn btn-primary animated fadeIn" style="margin-left: 20px;" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="panel animated fadeIn delay-1s">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>User List</b></h4></div>
                   <div class="panel-body" id="userTable">
                     
                </div>
            </div>
        </div>
    </div>
</div>    
</div> 

<script>
user_setup();

  function user_setup(){
      $.ajax({
              url: "<?= site_url('setup_/loadUserSetup')?>",
              success:function(response){
                  $("#userTable").html(response);
              }
      });
  }
</script>