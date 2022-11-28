<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Overload</b></h4></div>
                   <div class="panel-body">
                   	<div class="form-group">
			                <label  class="col-sm-2 align_right">Type</label>
			                <div class="col-sm-4">
			                   <select class="chosen" id="tnt" name="tnt">
                                  <?
                                    $type = array("teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                    foreach($type as $c=>$val){
                                    ?><option value="<?=$c?>"><?=$val?></option><?
                                    }
                                  ?>
                                  </select>
			                </div>
			                <div class="form-group">			                
			            	<label  class="col-sm-2 align_right">Employee</label>
			                <div class="col-sm-4">
			                	<select class="chosen" name="employeeid" id="employeeid" multiple="" >
									
								</select>
			                </div>
			            </div>
			            <br><br>
			            </div>
	                   	<div class="form-group">
			                <label  class="col-sm-2 align_right">Department</label>
			                <div class="col-sm-4">
			                   <select class="chosen-select form-control" name="deptid" id="deptid">
									 <option value="">All Department</option>
									<?
									  $opt_department = $this->extras->showdepartment();
									  foreach($opt_department as $c=>$val){
									  ?><option value="<?=$c?>"><?=$val?></option><?
									  }
									?>
								</select>
			                </div>
			                <div class="form-group">			                
				            	<label  class="col-sm-2 align_right">HOURS</label>
				                <div class="col-sm-4">
				                   	<input class="form-control" type="number" name="hours"/>
				                </div>
				            </div><br><br>
			            </div>
			            <div class="form-group">
			                <label  class="col-sm-2 align_right">Employee Status</label>
			                <div class="col-sm-4">
			                   <select class="chosen-select form-control" name="employmentstat">
								<?
								$opt_status = $this->extras->showemployeestatus("All Status");
								foreach($opt_status as $c=>$val){
								?><option value="<?=$c?>"><?=$val?></option><?    
								}
								?>
							  </select>
			                </div>
						<a href="#" class="btn btn-primary" id="multipleencode" style="margin-left: 17.7%;">Encode</a> (Click here for multiple employee encode)

			            </div>
			            <br><br>
			            
					</div>
				</div>
				<div id="empList"></div>
			</div>
		</div>
	</div>
</div>

<script>

	setTimeout(
	  function() 
	  {
	    $("#removeAni").removeClass("animated fadeIn delay-1s");
	  }, 2000);
	$(document).ready(function(){
		loadEmpList();
		loadEmployeeList();
		$('.chosen-select').chosen();
		$('.chosen').chosen();
	});
	
	$("#employeeid").on("change", function(){
		var emplist = $(this).val();
		console.log(emplist);
		if(emplist !== null){
			$.each( emplist, function( key , value ) {
				if(!value){
					$("#employeeid_chosen .chosen-drop").css("pointer-events", "none");
				}
				else{
					if(emplist != null){
					var itemToDisable = $("option:contains('All Employee')");
					itemToDisable.css("pointer-events", "none");
					$("#employeeid").trigger("chosen:updated");
					}
					else{
					
					}
					// $("#employeeid option[value='']").attr("disabled", "disabled");
				}
			});
		}else{
			$('#employeeid').trigger("chosen:updated"); 
			$(".chosen-drop").css("pointer-events", "");
			var itemToEnable = $("option:contains('All Employee')");
			itemToEnable.css("pointer-events", "");
			$("#employeeid").trigger("chosen:updated");
		}

	});

	$("#multipleencode").click(function(){
		var employeeid = $("select[name='employeeid']").val();
		var tnt = $("select[name='tnt']").val();
		var deptid = $("select[name='deptid']").val();
		var employmentstat = $("select[name='employmentstat']").val();
		var hours = $("input[name='hours']").val();
		$.ajax({
			url: "<?=site_url('batch_encode_/encodeOverloadRate')?>",
			type: "POST",
			data:{
				employeeid: employeeid,
				tnt: tnt,
				deptid: deptid,
				employmentstat: employmentstat,
				hours: hours
			},
			dataType: "json",
			success:function(response){
				if(response.total == 0){ 
					alert("Total of 0 employee processed. Please check your filters.");
					return;
				}else if(response.success == 0 && response.failed > 0){
					alert("All employee processed failed.");
					return;
				}
				alert("Successfully save " + response.success + ". Failed to save " + response.failed + ". Total of " + response.total + " employee.");
				$("input").val("");
				$("select").val("").trigger("chosen:updated");
				loadEmpList();
			}
		});
	});

	$("select[name='deptid']").change(function(){
		loadEmployeeList();
	});
	
	$("select[name='tnt']").change(function(){
		loadEmployeeList();
	});

	$("select[name='employmentstat']").change(function(){
		loadEmployeeList();
	});

	function loadEmployeeList(){
		var form_data = "&estatus="+$("select[name='employmentstat']").val() + "&deptid="+$("select[name='deptid']").val() + "&etype="+$("select[name='tnt']").val();
		$.ajax({
			url: "<?=site_url("process_/callemployee")?>",
			type: "POST",
			data: form_data,
			success: function(msg) {
				$("select[name='employeeid']").html(msg).trigger('chosen:updated');
			}
		});
	}
	
	function loadEmpList(){
		$.ajax({
			url: "<?=site_url("payroll_/showOverloadEmpList")?>",
			success: function(msg){
				$("#empList").html(msg);
			}
		});
	}

</script>