<div id="payslipcontent" class="well-content" style="padding-bottom: 32px;background: white;">
	
	<div class="content">
		<div class="form-group">
            <div class="col-md-12">
                <label  for="employeeid" class="col-sm-4">Enter Password</label>
                <div class="col-sm-8">
                    <input class="form-control required" id="password" type="password"/>
                </div>
            </div>
        </div><br><br><br>
        <div class="form-group">
            <div class="col-md-12">
                <label  for="employeeid" class="col-sm-4">Confirm Password</label>
                <div class="col-sm-8">
                    <input class="form-control required" id="confirmpassword" type="password"/>
                </div>
                <label for="notIdentical" class="col-sm-4"></label>
                <div class="col-sm-8">
                    <label id="notIdentical" style="display: none;color: red;">Password Mismatch</label>
                </div>
            </div>
        </div>
	</div>
</div>

<script type="text/javascript">
	var toks = hex_sha512(" ");
	  $("#save_payslip").click(function(){
	  	if ($("#confirmpassword").val() != $("#password").val()) {
	      	$("#notIdentical").show();
	      	Swal.fire({
				        icon: 'warning',
				        title: 'Warning!',
				        text: "Password Mismatch!",
				        showConfirmButton: true,
				        timer: 1000
			    })
	      	return false;
	    }
	  	var ppassword = $("#password").val();
	  	var uid = "<?=$uid?>";
	  	$.ajax({
	  	    url:"<?=site_url("maintenance_/saveuser")?>",
	  	    data: {ppassword: GibberishAES.enc( ppassword, toks),uid: GibberishAES.enc(uid , toks),job:GibberishAES.enc("editpayslipPassword" , toks), toks:toks},
	  	    type: "POST",
	  	    success: function(msg){
	  	    	Swal.fire({
			        icon: 'success',
			        title: 'Saved!',
			        text: $(msg).find('message').text(),
			        showConfirmButton: true,
			        timer: 1000
			    })
			    setTimeout(function() {
			    	$("#modalclose").click();
			    	user_setup();
			    }, 1500);
	  	    }
	  	});
	  });

	  $("#confirmpassword").change(function() {
	      if ($(this).val() == $("#password").val()) {
	      	$("#notIdentical").hide();
	      }else{
	      	$("#notIdentical").show();
	      }
	    });
</script>
	