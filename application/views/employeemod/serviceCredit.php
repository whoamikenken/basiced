<style type="text/css">
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
                <div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Service Credit</b></h4></div>
                   <div class="panel-body" id="scucontent">
                   </div>
                </div>
                <div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Position</b></h4></div>
                   <div class="panel-body" id="sccontent">
                   </div>
               </div>
            </div>
        </div>
    </div>        
</div>        
<div class="modal fade" id="myModal" data-backdrop="static"></div>

<script>
	$(document).ready(function(){
		loadsc();
	            
	});

	function loadsc(){
		$("#sccontent").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
		$.ajax({
		   url      :   "<?=site_url("employeemod_/fileconfig")?>",
		   type     :   "POST",
		   data     :   {folder: "employeemod", view:"scdetails"},
		   success  :   function(msg){
		   	loadschistory("load",'');
		   	// alert(msg);
			$("#sccontent").html(msg); 
			
		   }
		});
	}
	
	function loadschistory(action,status){
		$.ajax({
			url      :   "<?=site_url("employeemod_/fileconfig")?>",
			type     :   "POST",
			data     :   {folder: "employeemod", view: "schistory",action:action,status:status},
			success  :   function(msg){
				$("#sch").remove();
				$("#sccontent").append("<div id='sch'>"+msg+"</div>");
				
			}
		});
		loadscuhistory(action,status);
	}
	
	function loadscuhistory(action = '',status = ''){
		$.ajax({
			url      :   "<?=site_url("employeemod_/fileconfig")?>",
			type     :   "POST",
			data     :   {folder: "employeemod", view: "scuhistory", action:action, status:status},
			success  :   function(msg){
				$("#scuh").remove();
				$("#scucontent").append("<div id='scuh'>"+msg+"</div>");
			}
		});
	}


</script>