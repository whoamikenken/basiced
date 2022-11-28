<style type="text/css">
    .panel {
        border: 5px solid #0072c6 !important;
        box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
        margin-bottom: 49px !important;
    }
    #sidebar ul li.active>a, a[aria-expanded="true"] {
        color: #090909 !important;
        background: #0072c6 !important;
    }
</style>
<div id="content"> <!-- Content start -->
     <div class="well blue">
        <div class="well-content no_padding">
             <ul class="nav nav-tabs" id="pinfotab">
               <li class="active"><a href="#tab1" data-toggle="tab">Main Leave Type</a></li>
               <li><a href="#tab2" data-toggle="tab">Validity Date</a></li>
               <li><a href="#tab3" data-toggle="tab">Other Leave Type</a></li>
             </ul><br>
             <div class="tab-content">
               <div class="tab-pane fade in active" id="tab1" ld='maintenance/leave'>
                 <?php $this->load->view('maintenance/leave'); ?>
               </div>
              <div class="tab-pane fade" id="tab2" ld='maintenance/leave_appdate'></div>
              <div class="tab-pane fade" id="tab3" ld='maintenance/other_request'></div>
             </div>
        </div>
     </div>
</div>           
<script>
var toks = hex_sha512(" ");
var cancontinue = true;
var message = "";
$(document).ready(function(){
    $.ajax({
            url: "<?php echo site_url("employee_/checkhasssession")?>",
            type:"POST",
            success: function(msg){
                if($(msg).find("status:eq(0)").text()==0){
                   message = $(msg).find("message:eq(0)").text();  
                }else{
                   cancontinue = true;
                };
            }
        });
});
function refreshtab(tabn){
    var form_data = { 
      view : GibberishAES.enc($(tabn).attr("ld"), toks),
      toks:toks
    }
    $.ajax({
            url: "<?php echo site_url("main/siteportion")?>",
            data: form_data,
            type:"POST",
            success: function(msg){
                $(tabn).html(msg);
                if("<?php echo $this->session->userdata('canwrite')?>" == 0) $(tabn).css("pointer-events", "none");
                else $(tabn).css("pointer-events", "");
            }
        });
}

$("#pinfotab li").click(function(){
  var obj = $(this).find("a").attr("href");  
  if(!cancontinue) alert(message);
  else{
    refreshtab(obj);
  }  
  return cancontinue;
});
</script>