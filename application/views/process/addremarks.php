<?php
 // $data = ($this->input->post())?$this->input->post():"";
 $cdate = "";
 $starttime = "";
 $endtime = "";
 $dayofweek = "";
 $remarks = "";
 // if($data){
 // 	$id = $this->input->post('bID');
 // 	$sql = $this->db->query("SELECT * FROM employee_schedule_adjustment WHERE id={$id}");
 // 	$cdate = $sql->row()->cdate;
 // 	$remarks = $sql->row()->remarks;
 // }
 ?>
<style>
.modal{
    width:600px;
    left: 0;
    right: 0;
    margin: auto;

}
@media (min-width: 992px){
  .modal-lg {
      width: 600px;
  }
}
.modal-footer{
	margin-top: -3%;
}
</style>
<form id="form_remarks" method="POST" action="#" style="margin-top: 3%;">
	<!-- <div class="form_row" hidden="hidden">
		<label class="field_name align_right">Code</label>
		<div class="field">
			<input class="form-control required" id="code" name="code" type="text" value="" style="width: 90%;"/>
		</div>
    </div> -->
    <div class="form_row" style="margin-right: -20%;">
		<label class="field_name align_right">Description</label>
		<div class="field">
			<input class="form-control required isrequired" id="desc" name="desc" type="text" value="" style="width: 70%;"/>
			<!-- <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span> -->
		</div>
    </div>
</form>
<script type="text/javascript">
	var toks = hex_sha512(" "); 
	 $(".saves").click(function(){
	 	var iscontinue = validateForm($("#form_remarks"));
	 	if(iscontinue){
	 		$.ajax({
	            url     :   "<?=site_url("process_/saveRemarks")?>",
	            type    :   "POST",
	            data    :   {toks:toks, desc: GibberishAES.enc($("input[name='desc']").val(), toks)},
	            success : function(msg){
	            	$("#modalclose").click();
	              	if (msg == "This code was already taken!") {
	              		Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: msg,
                          showConfirmButton: true,
                          timer: 1000
                      })
	              	}else{
	              		// alert(msg);
	              		Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Remark has been saved successfully',
                          showConfirmButton: true,
                          timer: 1000
                      })
	              		$(".grey").click();
	              	}
	            }
	        });
	 	}else{
	 		Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: 'Description is required',
                          showConfirmButton: true,
                          timer: 1000
                      })
	 	}
	});
</script>

