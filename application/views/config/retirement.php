<style>
  .cbox{
     -ms-transform: scale(1.5); /* IE */
     -moz-transform: scale(1.5); /* FF */
     -webkit-transform: scale(1.5); /* Safari and Chrome */
     -o-transform: scale(1.5); /* Opera */
  }
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                    <div class="panel-heading"><h4><b>Retirement Plan</b></h4></div>
                    <br>
                    <div class="col-md-12" style="padding: 0px 0px 15px 0px;">
                    	<div class="fieldTitle">
                    		<label class="col-md-2 align_right">Department:</label>
                    		<div class="field col-md-4">
	                    		<select class="form chosen-select" id="department" name="department"><?=$this->extras->getDeptpartment()?></select>
                    		</div>
                    	</div>
                    </div>

                    <div class="col-md-12" style="padding: 0px 0px 15px 0px;">
                    	<div class="fieldTitle">
                            <label class="col-md-2 align_right">Office:</label>
                            <div class="field col-md-4">
                                <select class="form chosen-select" id="office" name="office"><?=$this->extras->getOffice()?></select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="col-md-12" style="padding: 0px 0px 20px 0px;">
                        <div class="fieldTitle">
                            <label class="col-md-2 align_right">Month:</label>
                            <div class="field col-md-4">
                                <select class="chosen-select col-md-4" name="month" id="month">
                                    <option value="">All Month</option>
                                    <?
                                    foreach(Globals::monthList() as $key => $val){
                                        ?>      <option value="<?=$key?>"><?=$val?></option><?    
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div id="retiree_data">
                        
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="status" value="1">
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="<?=base_url()?>jsbstrap/library/jquery.inputmask.bundle.js"></script>
<script>
	var toks = hex_sha512(" ");

	loadRetireeData('1');
	function loadRetireeData(status='', department='',office='',month='') {
		$.ajax({
	        url: "<?=site_url('retirement_/loadEmployeeRetiree')?>",
	        data: {department: GibberishAES.enc( department, toks), office: GibberishAES.enc( office, toks), status: GibberishAES.enc( status, toks), month: GibberishAES.enc( month, toks), toks:toks},
	        type: "POST",
	        success:function(res){
	            $("#retiree_data").html(res);
	        }
	    })
	}

	$("#status, #department, #office, #month").change(function(){
		$("#retiree_data").html("Loading...");
		loadRetireeData($("#status").val(), $("#department").val(), $("#office").val(), $("#month").val());
	})

	$("#department").change(function(){
	    $.ajax({
	        url : $("#site_url").val() + "/setup_/getOffice",
	        type: "POST",
	        data: {department:GibberishAES.enc($(this).val(), toks), toks:toks},
	        success: function(msg){
	            $("#office").html(msg).trigger("chosen:updated");
	        }
	    });
	});

	

	$(".chosen").chosen();
</script>