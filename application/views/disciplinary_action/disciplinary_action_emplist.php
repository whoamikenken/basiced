<?
	//Added (6-2-2017)
	$CI =& get_instance();
	$CI->load->model('disciplinary_action');
	$excessiveTardinessCount = $CI->disciplinary_action->empWithExcessiveTardiness(true);
	$excessiveAbsenteismCount = $CI->disciplinary_action->empWithExcessiveAbsenteism(true);
?>
<style>
	.toolbar {
		float:left;
	}
 .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}

#pyear_chosen{
	width: 85px !important;
}
	
</style>
<div id="content" style="margin-right: 10px;">
	<div style='float:right;padding-right:1%;padding-top:1%;font-size:12px; margin-bottom: 10px;'>
		<b>Year</b> &nbsp; <select class="chosen" id="pyear" style="width: 100px;"><?=$this->payrolloptions->periodyear();?></select>&nbsp;
		<a href='#modal-view' data-toggle='modal' style="font-weight:bold;text-decoration: underline;font-size:1.5em;margin-right: 10px;" id="empWithDisciplinaryAction">List of Employee w/ Disciplinary action</a>
		<a href='#' style="font-weight:bold;text-decoration: underline;font-size:1.5em;margin-right: 10px;" id="excessiveTardiness" class='list' type='Tardiness'>List of Employee with Excessive Tardiness
		<div class='notifdiv'><i class='icon-bell large' style='color:black'></i><span class='notifcount'><b id="excessiveTardinessCount">*</b></span></div>
		</a>
		<a href='#' style="font-weight:bold;text-decoration: underline;font-size:1.5em;margin-right: 10px;" id="excessiveAbsenteism" class='list' type='Absenteism'>List of Employee with Excessive Absenteeism
		<div class='notifdiv'><i class='icon-bell large' style='color:black'></i><span class='notifcount'><b id="excessiveAbsenteismCount">*</b></span></div>
		</a>
	</div>
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>List of Employees</b></h4></div>
                   <div class="panel-body">
						<a class="btn btn-primary batchaddbtnsanction" href="#modal-view" data-toggle="modal" style='margin-bottom:1%'><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add by Batch </span>
						</a>
							<table class="table table-striped table-bordered table-hover" id="table">
							<thead style="background-color: #0072c6;">
								<tr>
									<th class="col-md-1">#</th>
									<th class="sorting_asc">Employee ID</th>
									<th>Fullname</th>
									<th>Type</th>
									<th>Department</th>
								</tr>
							</thead>
							<tbody id="employeelist">
							<?
								$employee = $this->employee->loadallemployee('','','','','','','','active');

								if(count($employee)>0){
									$o=1;
									foreach($employee as $row){
							?>
								<tr employeeid='<?=$row['employeeid']?>' positionid='<?=$row['position']?>' deptid='<?=$row['deptid']?>' style="cursor: pointer;">
									<td><?=$o?></td>
									<td><?=$row['employeeid']?></td>
									<td id="tdFName"><?=(Globals::_e($row['lname'] . ", " . $row['fname'] . ($row['mname']!="" ? " " . substr($row['mname'],0,1) . "." : "")))?></td>
									<td><!-- <?=$this->extras->getemployeetype($row['emptype'])?> --><?=$row['emptype']; ?></td>
									<td><?= $this->extras->getemployeedepartment($row['deptid']) ?></td>
								</tr>
							<?  
								$o++;  
									}
								}
							?>                                        
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>    
</div>           

<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
	$("#excessiveAbsenteism").attr("year", $("#pyear").val());
	$("#excessiveTardiness").attr("year", $("#pyear").val());
	loadNotifCount($("#pyear").val());
    var table = $('#table').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$("#pyear").change(function(){
	$("#excessiveAbsenteism").attr("year", $(this).val());
	$("#excessiveTardiness").attr("year", $(this).val());
	$("#excessiveAbsenteismCount").html("*");
    $("#excessiveTardinessCount").html("*");
	loadNotifCount($(this).val());
})

function loadNotifCount(year){
	$.ajax({
		url: "<?=site_url('disciplinary_action_/loadNotifCount')?>",
        type: "POST",
        data: {year: GibberishAES.enc(year , toks), toks:toks},
        dataType: "JSON",
        success: function(msg){
        	$("#excessiveAbsenteismCount").html(msg.absent);
            $("#excessiveTardinessCount").html(msg.tardy);
        }
	})
}
	
$(".addbtnsanction,.editbtnsanction").click(function(){
    var infotype = "code_disciplinary_action_sanction";
    var code = "";
    if($(this).attr("id")) code = $(this).attr("id");
    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Disciplinary Sanction" : "Add Disciplinary Sanction");
    $(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
    $("#button_save_modal").text("Save");
    var form_data = {
        info_type:  GibberishAES.enc( infotype, toks),
        action:  GibberishAES.enc( code, toks),
        toks:toks
    };
    $.ajax({
        url: "<?=site_url('disciplinary_action_/viewForm')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});
$("#employeelist tr").click(function(){
   console.log($(this).find('td#tdFName').html());

   if($(this).attr("employeeid")){
       var form_data = {
        employeeid :  GibberishAES.enc($(this).attr("employeeid") , toks),
        fname :  GibberishAES.enc($(this).find('td#tdFName').html() , toks),
        deptid :  GibberishAES.enc( $(this).attr("deptid"), toks),
		positionid :  GibberishAES.enc($(this).attr("positionid") , toks),
        view:  GibberishAES.enc("disciplinary_action/add_emp_disciplinary_action.php" , toks),
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
   }
});  
$('select').chosen();

$(".batchaddbtnsanction").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Batch Employee Offense");
    $(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
    $("#button_save_modal").text("Save");
    $.ajax({
        url: "<?=site_url('disciplinary_action_/viewBatchAdd')?>",
        type: "POST",
        success: function(msg){
        $("#modal-view").find("div[tag='display']").html(msg);
		}
    });  
});

$("#empWithDisciplinaryAction").click(function(){
	$("#modal-view").find("h3[tag='title']").text("List of Employee with Disciplinary Action");
	$("#button_save_modal").text("Generate");
	var form_data = {
		folder :  GibberishAES.enc( "disciplinary_action", toks), 
		page   :  GibberishAES.enc( "disciplinary_action_report", toks),
		toks:toks
	};
	$.ajax({
		url: "<?=site_url('employee_/viewModal')?>",
		type: "POST",
		data: form_data,
		success: function(msg){
			$("#modal-view").find("div[tag='display']").html(msg);
		}
	});  
});

$(".list").click(function(){

	if($(this).attr("type")){
       var form_data = {
        type :  GibberishAES.enc( $(this).attr("type"), toks),
        year:  GibberishAES.enc($(this).attr("year") , toks),
        view:  GibberishAES.enc( "disciplinary_action/emp_list_with_excessive_disciplinary_action.php", toks),
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
   }
});  
</script>