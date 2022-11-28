<?
	$empDefcount = 0;
	$empDefcount = $this->employeemod->employeedeficiencynotif('','','','',true)->num_rows();
  // echo "<pre>";print_r($this->db->last_query());die;
?>
<style type="text/css">
       .panel {
            border: 5px solid #0072c6 !important;
            box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
            margin-bottom: 49px !important;
        }

       #pyear_chosen{
          width: 100px !important;
          padding-bottom: 5px;
        }
</style>
<div id="content"> <!-- Content start -->
  <div style='float:left;'>
    <a href="#" class="btn btn-primary" name='tag_clearance' style="margin-left: 20px; margin-bottom: 10px; margin-top: 10px;">Tag Clearance</a>
</div>
<div style='float:right;padding-right:1%;padding-top:1%;font-size: 10px;'>
  <!-- <span style="font-size: 12px;font-weight: bold;">Year</span> &nbsp; <select class="chosen" id="pyear" style="width: 100px;"><?=$this->payrolloptions->periodyear();?></select>&nbsp; -->
	<a href='#modal-view' data-toggle='modal' style="font-weight:bold;text-decoration: underline;font-size:1.5em;margin-bottom: 10px;margin-right: 5px;" id="printDeficiencyReport">Print Clearance Report</a>
<?
	if($empDefcount !=0)
	{?>
		|<a href='#modal-view' data-toggle='modal' style="font-weight:bold;text-decoration: underline;font-size:1.5em" id="deficiencyUpdates">Clearance Updates 
		<div class='notifdiv'><i class='glyphicon glyphicon-bell large' style='color:black'></i><span class='notifcount'><b><?=$empDefcount?></b></span></div>
		</a>
	<?}
?>
</div>
<br>
<div class="widgets_area">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel animated fadeIn delay-1s">
                           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>List of Employees</b></h4></div>
                           <div class="panel-body">
                               <table class="table table-striped table-bordered table-hover" id="ListTable">
                                    <thead style="background-color: #0072c6;">
                                        <tr>
                                            <th class="col-md-1">#</th>
                                            <th class="sorting_asc">Employee</th>
                                            <th>Fullname</th>
                                            <th>Type</th>
                                            <th>Department</th>
                                            <th>Clearace History</th>
                                        </tr>
                                    </thead>
                                    <tbody id="employeelist">
                                        
<?
$employee = $this->employee->loadallemployee();

if(count($employee)>0){
    $o=1;
    $employee = Globals::resultarray_XHEP($employee);
foreach($employee as $row){
?>
  <tr employeeid='<?=$row['employeeid']?>' positionid='<?=$row['position']?>' deptid='<?=$row['deptid']?>' style="cursor: pointer;">
    <td class="addemp" ><?=$o?></td>
    <td class="addemp" ><?=$row['employeeid']?></td>
    <td id="tdFName" class="addemp tdFName" ><?=($row['lname'] . ", " . $row['fname'] . ($row['mname']!="" ? " " . substr($row['mname'],0,1) . "." : ""))?></td>
    <td class="addemp" ><?=$this->extensions->getEmployeeTeachingType($row['employeeid'])?></td>
    <td class="addemp"><?= $this->extras->getemployeedepartment($row['deptid']) ?></td>
    <td style='text-align:center'>
        <a href="#" tag='viewClearanceList' data-toggle="modal" data-target="#modal-view" employeeid="<?=$row['employeeid']?>" title="View Clearance History" ><i class="icon-large icon-eye-open"></i></a>
    </td>
  </tr>
<?  
$o++;  
// $row['deptid']
// $this->extras->getemployeedepartment($row['deptid'])
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
    var table = $('#ListTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
$("#employeelist tr td.addemp").click(function(){
   if($(this).closest("tr").attr("employeeid")){
       var form_data = {
        toks: toks,
        employeeid : GibberishAES.enc($(this).closest("tr").attr("employeeid"), toks),
        positionid : GibberishAES.enc($(this).closest("tr").attr("positionid"), toks),
        fname : GibberishAES.enc($(this).closest('tr td.tdFName').html(), toks),
        deptid : GibberishAES.enc($(this).closest("tr").attr("deptid"), toks),
        concerneddept : GibberishAES.enc('', toks),
        view: GibberishAES.enc("deficiency/add_emp_deficiency.php", toks)
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

$('#employeelist').on('click', 'a[tag="viewClearanceList"]', function () {
  $("#modal-view").find("h3[tag='title']").text("Employee Clearance History");
    if($(this).attr("employeeid")) employeeid = $(this).attr("employeeid");
    var form_data = {
        toks     : toks,
        employeeid    : GibberishAES.enc(employeeid, toks),
        forviewing : GibberishAES.enc('yes', toks)
    };
    $.ajax({
       url      :   "<?=site_url("deficiency_/loadDeficiencyHistory")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#modal-view").html(msg);
       }
    });
});
$('select').chosen();

$("#deficiencyUpdates").click(function(){
	$("#modal-view").find("h3[tag='title']").text("List of Clearance Update");
  $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
  $("#button_save_modal").text("Generate").css("display", "none");
	var form_data = {
    toks: toks,
		folder : GibberishAES.enc("deficiency", toks),
		page   : GibberishAES.enc("matured_list", toks)
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

$("#printDeficiencyReport").click(function(){
	$("#modal-view").find("h3[tag='title']").text("Clearance Report");
  $("#button_save_modal").text("Generate").css("display", "");
	var form_data = {
    toks: toks,
		folder : GibberishAES.enc("deficiency", toks),
		page   : GibberishAES.enc("deficiency_report", toks)
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

$("a[name='tag_clearance']").click(function(){
  var form_data = {
        toks: toks,
        view: GibberishAES.enc("deficiency/tag_clearance.php", toks)
  };
  $.ajax({
      url : "<?=site_url("main/siteportion")?>",
      type: "POST",
      data: form_data,
      success: function(msg){
        $("#content").html(msg);
      }
   });
})
</script>