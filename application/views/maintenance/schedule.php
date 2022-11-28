

<style>
.modal-overflow.modal.fade.in {
    top: 13%;
}

.modal.container {
    margin-left: 20%;
}
     .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="panel animated fadeIn delay-1s" style="display: none;">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Schedule</b></h4></div>
                   <div class="panel-body">
                    <span><b>(NOTE : CSV File Extension Only)</b></span>
                    <form id="upload" class="form-horizontal well" action="<?=site_url('uploadcsv/import');?>" method="post" name="upload_excel" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="sched" />
                        <input type="file" name="file" id="file" class="input-large" /><br>
                        <button type="submit" id="submit" name="Importsched" id="Importsched" class="btn btn-primary">Upload</button>
                    </form>
                    <div id="msg"></div>
                </div>
            </div>
            <br />
            <div class="panel animated fadeIn delay-1s">
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Schedule List</b></h4></div>
                   <div class="panel-body" id="schedtable">
                    
                </div>
            </div>
        </div>
    </div>
</div>    
</div> 
<script>
SCtable();
validateCanWrite();
function SCtable(){
    $.ajax({
            url: "<?= site_url('setup_/loadScheduleTable')?>",
            success:function(response){
                $("#schedtable").html(response);
            }
    });
}

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#file, .btn").css("pointer-events", "none");
    else $("#file, .btn").css("pointer-events", "");
}

$( "#upload" ).submit(function() {
  if ( $('#file').get(0).files.length === 0 ) {
    $( "#msg" ).text( "No files selected.." ).show();
    return false;
  }else{
    $("#msg").html("<img class='pull-left' src='<?=base_url()?>images/loading.gif' />  Uploading, Please Wait..");
  }
});
</script>
