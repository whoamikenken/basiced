
<style>
	@media (min-width: 992px){
	.modal-lg {
	    width: 1041px!important;
	}
}
</style>
<?
	$username = $this->session->userdata('username');
    $utype = $this->session->userdata('usertype');
    if($utype == "EMPLOYEE"){
    	$office = $this->extensions->getAllOfficeUnder($this->session->userdata("username"));
  		$office = implode(',', $office);
    	$deptid = $this->extras->getHeadOffice($username);
    	$empDef = $this->employeemod->employeedeficiencynotif('','','','',true, false, $office)->result();
    }
	else{
		$empDef = $this->employeemod->employeedeficiencynotif('','','','',true)->result();
	}
?>
<div style="margin:3%;">
	<table class="table table-striped table-bordered table-hover datatable" width="100%">
		<tr style="background-color: #0072c6; color:black;">
			<th>Employee ID</th>
			<th>Employee Name</th>
			<th>Concerned Office</th>
			<th>Remarks</th>
			<th>Submission Date</th>
		</tr>
		<?
			foreach($empDef as $row)
			{
				?>
				<tr>
					<td><?=$row->employeeid?></td>
					<td><?=Globals::_e($row->fullname)?></td>
					<td><?=Globals::_e($row->department)?></td>
					<td><?=Globals::_e($row->deficiency_desc)?></td>
					<td><?=$row->submission_date?></td>
				</tr>
				<?
			}
		?>
	</table>
</div>
<script>
	var toks = hex_sha512(" ");
   $("#button_save_modal").unbind('click').click(function(){
	   var form_data   =   "form=" +GibberishAES.enc("deficiency_list", toks);
	   form_data		+=	"&notif=" + GibberishAES.enc('true', toks);
	   form_data		+=	"&toks=" + toks;
	   var encodedData = encodeURIComponent(window.btoa(form_data));
	   openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
   });
</script>