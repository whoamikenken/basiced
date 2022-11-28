<div id="content"> <!-- Content start -->
<?if($this->session->userdata('usertype') != "PAYROLL"){?>
<!-- <a href="#" id="addnewemployee"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a> -->
<?}?>
<style type="text/css">
     .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div class="widgets_area">
  <div class="row">
      <div class="col-md-12">
          <div class="form-inline">
            <b style="font-weight: 1500">Sort by: </b>
            <input type="radio" name="status" value="1" checked> Active
            <input type="radio" name="status" value="0"> Archive
            <input type="radio" name="status" value="all"> All
          </div>
          <div class="panel animated fadeIn delay-1s">
             <div class="panel-heading" style="background-color: #0072c6;"><h4><b>List of Applicants</b></h4></div>
             <br>
             <div class="col-sm-12" style="padding-left: 0px;">
              <div class="form-group col-sm-5"  style="padding-left: 0px;">
              <label class="col-sm-2 control-label" style="padding-right: 0px;">Application Status:</label>
                <div class="col-sm-10" style="width: 40%;">
                  <select id="app_status" class="chosen" >
                      <option value="">All Status</option>
                      <option value="COMPLETE">Complete</option>
                      <option value="INC">Incomplete</option>
                  </select>  
                </div>
              </div>
              <div class="form-group col-sm-7" ></div>
            </div>
              <br> 
             <div class="panel-body" id="employeelist">
                 
             </div>
          </div>
      </div>
  </div>
</div>    
</div>           

<script>
var toks = hex_sha512(" ");
loadApplicantTable();
    
$("#addnewemployee").click(function(){
   var form_data = {
    job : GibberishAES.enc("new"  , toks),
    view: GibberishAES.enc( "employee/personal_info" , toks),
    toks:toks
   }; 
   $.ajax({
      url : "<?=site_url("main/siteportion")?>",
      type: "POST",
      data: form_data,
      success: function(msg){
        $("#content").html(msg);
      }
   }); 
});

$("input[name='status'], #app_status").change(function(){
  loadApplicantTable();
});

function loadApplicantTable(){
  var status = $("input[name='status']:checked").val();
  var applicantStatus = $("#app_status").val();
  $.ajax({
    url: "<?= site_url('applicant/loadApplicantTable') ?>",
    type: "POST",
    data:{status:  GibberishAES.enc(status, toks), applicantStatus: GibberishAES.enc( applicantStatus, toks), toks:toks},
    success:function(response){
      $("#employeelist").html(response);
      updateEndorsedCount()
    }
  });
}
function updateEndorsedCount(){
    $.ajax({
        url: "<?= site_url('applicant/updateEndorsedCount') ?>",
        type: "POST",
        success: function(msg){ 
            if(msg > 0){
                $("li .active").find(".notifdiv").html('<i class="glyphicon glyphicon-bell large" style="color:white;font-weight: bold;"></i><span class="notifcount" style="color:white;font-weight: bold;">'+msg+'</span>');
                $("title").text("Applicant List "+msg);
            }
            else{
                $("li .active").find(".notifdiv").html('');
                $("title").text("Applicant List");
            }
        }
    });
}
$(".chosen").chosen();

</script>