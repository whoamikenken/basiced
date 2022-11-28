<style type="text/css">
  .btn-danger {
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}

.btn-danger:hover{
  background-color: #d43f3a;
}

.btn-success {
    color: #fff;
    background-color: #5cb85c;
    border-color: #4cae4c;
}


.btn-success:hover{
  background-color: #4cae4c;
}

.swal2-cancel {
    margin-right: 20px;
}
</style>
<form id="signupform" action="<?=site_url('applicant/signup')?>" method="POST">
<div class="row">
  <div class="col s12">
    <div class="col s12 col s3">
      <label style="    color: #1d1b1b;font-weight: 700;">First Name :</label>
    </div>
    <div class="col s12 col s7">
      <input type="text" class="waves-effect waves-yellow validate" name="fname" id="fname" autocomplete="off" style="text-transform: uppercase;">
    </div>
  </div>
  <div class="col s12">
    <div class="col s12 col s3">
      <label style="    color: #1d1b1b;font-weight: 700;">Middle Name :</label>
    </div>
    <div class="col s12 col s7">
      <input type="text" class="waves-effect waves-yellow validate" name="mname" id="mname" style="text-transform: uppercase;">
    </div>
  </div>
  <div class="col s12">
    <div class="col s12 col s3">
      <label style="    color: #1d1b1b;font-weight: 700;">Last Name :</label>
    </div>
    <div class="col s12 col s7">
      <input type="text" class="waves-effect waves-yellow validate" name="lname" id="lname" style="text-transform: uppercase;">
    </div>
  </div>
  <div class="col s12">
    <div class="col s12 col s3">
      <label style="    color: #1d1b1b;font-weight: 700;">Email :</label>
    </div>
    <div class="col s12 col s7">
      <input type="email" class="waves-effect waves-yellow validate" name="email" id="email" style="text-transform: uppercase;">
    </div>
  </div>
  <?
    $q = $this->db->query("SELECT * from code_position a INNER JOIN code_position_hiring b ON a.positionid = b.base_id WHERE b.hiring = 'YES'");
  ?>
  <div class="col s12" style="pointer-events: none">
    <div class="col s12 col s3">
      <label style="    color: #1d1b1b;font-weight: 700;">Position :</label>
    </div>
    <div class="col s12 col s7">
      <select name="positionid" id="positionid" searchable="Search Here." >
  <?
    foreach ($q->result() as $key => $row) {?>
      <?php if ($selected == $row->description){ ?>
        <option value="<?=$row->positionid?>" selected ><?=strtoupper($row->description)?></option>
      <?php }else{ ?>
        <option value="<?=$row->positionid?>" ><?=$row->description?></option>
    <?}}
  ?>
      </select>
    </div>
  </div>
  <?php if($isteaching): ?>
      <div class="col s12">
        <div class="col s12 col s3">
          <label style="    color: #1d1b1b;font-weight: 700;">Department :</label>
        </div>
        <div class="col s12 col s7">
          <input type="text" class="waves-effect waves-yellow" value="<?=$course?>" disabled style="color: black;text-transform: uppercase;">
        </div>
      </div>
      <div class="col s12">
        <div class="col s12 col s3">
          <label style="    color: #1d1b1b;font-weight: 700;">Subject :</label>
        </div>
        <div class="col s12 col s7">
          <input type="text" class="waves-effect waves-yellow" value="<?=$subject?>" disabled style="color: black">
        </div>
      </div>
    <?php endif ?>
</div>
</form>
<script src="<?=base_url()?>js/sweetalert.js"></script>
<script type="text/javascript">
var toks = hex_sha512(" ");
$("#login_here_div, #logsubmit").show();
$("#logsubmitexisting").hide();
$('select').formSelect();
    $('#logsubmit').on('click', function(){
          if ($("#fname").val() == "") {
              $("#fname").addClass("invalid");
              Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please Input Your First Name!',
                    showConfirmButton: true,
                    timer: 1000
              })
              return;
          }else{
            $("#fname").removeClass("invalid");
          }
          if ($("#mname").val() == "") {
              $("#mname").addClass("invalid");
              Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please Input Your Middle Name!',
                    showConfirmButton: true,
                    timer: 1000
              })
              return;
          }else{
            $("#mname").removeClass("invalid");
          }
          if ($("#lname").val() == "") {
              $("#lname").addClass("invalid");
              Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please Input Your Last Name!',
                    showConfirmButton: true,
                    timer: 1000
              })
              return;
          }else{
            $("#lname").removeClass("invalid");
          }
          if ($("#email").val() == "") {
              $("#email").addClass("invalid");
              Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please Input Your Email!',
                    showConfirmButton: true,
                    timer: 1000
              })
              return;
          }else{
            $("#email").removeClass("invalid");
          }
          if(IsEmail($("#email").val())==false){
            $("#email").addClass("invalid");
            Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please Input a valid Email!',
                    showConfirmButton: true,
                    timer: 1000
              })
            return false;
          }
          var form_data = "";  
          $('#signupform input, #signupform select, #signupform textarea').each(function(){
            if(form_data) form_data += '&'+$(this).attr('name')+'='+$(this).val();
            else form_data = $(this).attr('name')+'='+$(this).val();
          })
          $.ajax({
             url: "<?=site_url("applicant/validate")?>",
             data : {formdata:GibberishAES.enc(form_data, toks), toks:toks},
             dataType: 'JSON',
             type : "POST",
             success:function(msg){
                if(msg.err_code==0){
                  if(!checkIfHasData(form_data)){
                    $("#login_button").show();
                    $("#login_loading").hide();
                    return;
                  }
                }
                else{
                  $("#login_button").show();
                  $("#login_loading").hide();
                  alert(msg.msg);  
                }
             }
          }); 
    });

    function IsEmail(email) {
      var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      if(!regex.test(email)) {
        return false;
      }else{
        return true;
      }
    }

    function checkIfHasData(form_data){
      var iscontinue = true;
      var confirm = "";
      $.ajax({
         url: "<?=site_url("applicant/checkIfHasData")?>",
         data : {formdata:GibberishAES.enc(form_data, toks), toks:toks},
         type : "POST",
         dataType: "json",
         async: false,
         success:function(response){
            if(response.isexist && response.seqno == 0 && response.submitted == 0 && response.email == 0 && (response.redtag == 0 || response.redtag == null) && response.isactive != 0 && (response.datehired == 0 || response.datehired == null)){
              // confirm = window.confirm("You have existing incomplete application. Click OK to continue.");
              // if(confirm) iscontinue = true;
              // else iscontinue = false;
              const swalWithBootstrapButtons = Swal.mixin({
                  customClass: {
                      confirmButton: 'btn btn-success',
                      cancelButton: 'btn btn-danger'
                  },
                  buttonsStyling: false
              })

              swalWithBootstrapButtons.fire({
                  title: 'You have existing incomplete application.',
                  text: "Click yes to proceed in your application",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, proceed!',
                  cancelButtonText: 'No, cancel!',
                  reverseButtons: true
              }).then((result) => {
                if (result.value) {
                    $("#signupform").unbind().submit();
                } else if (
                  result.dismiss === Swal.DismissReason.cancel
                ) {
                      swalWithBootstrapButtons.fire(
                          'Cancelled',
                          'Application is safe.',
                          'error'
                      )
                      iscontinue = false;
                  }
              })
            }
            else if(response.isexist && response.seqno && response.submitted && response.email == 0 && (response.redtag == 0 || response.redtag == null) && response.isactive != 0 && (response.datehired == 0 || response.datehired == null)){ 
              // confirm = window.confirm("You already submit your application. Your application already on process, click to view your application");
              // if(confirm) iscontinue = true;
              // else iscontinue = false;
              const swalWithBootstrapButtons = Swal.mixin({
                  customClass: {
                      confirmButton: 'btn btn-success',
                      cancelButton: 'btn btn-danger'
                  },
                  buttonsStyling: false
              })

              swalWithBootstrapButtons.fire({
                  title: 'You already have complete application',
                  text: "Click yes to proceed in your application",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, proceed!',
                  cancelButtonText: 'No, cancel!',
                  reverseButtons: true
              }).then((result) => {
                if (result.value) {
                    $("#signupform").unbind().submit();
                } else if (
                  result.dismiss === Swal.DismissReason.cancel
                ) {
                      swalWithBootstrapButtons.fire(
                          'Cancelled',
                          'Application is safe.',
                          'error'
                      )
                      iscontinue = false;
                  }
              })
            }else if(response.datehired != 0 && response.datehired != null){
               Swal.fire({
                      icon: 'warning',
                      title: 'The user of this account is already hired.',
                      showConfirmButton: true,
                      timer: 1000
                })
              return;
            }
            else if(response.isactive == 0){ 
              // Swal.fire({
              //         icon: 'warning',
              //         title: 'Warning!',
              //         text: 'Your application has been archived. It cannot be retrieved anymore. Thank you for taking interest in our institution.',
              //         showConfirmButton: true,
              //         timer: 1000
              //   })
              // return;
              $("#signupform").unbind().submit();
            }else if(response.redtag > 0){ 
              Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'Your application has been tagged as red flag, you are not allowed to continue your application',
                      showConfirmButton: true,
                      timer: 1000
                })
              return;
            }
            else{ 
              if(response.email > 0){
                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'Email already exist.',
                      showConfirmButton: true,
                      timer: 1000
                })
              return;
              }else{
                // confirm = alert("Signing-up successful! Click OK to proceed now.");
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                    title: 'Success!',
                    text: "Sign-up successfully. Click yes to proceed now.",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                  if (result.value) {
                      $("#signupform").unbind().submit();
                  } else if (
                    result.dismiss === Swal.DismissReason.cancel
                  ) {
                        swalWithBootstrapButtons.fire(
                            'Cancelled',
                            'Application is safe.',
                            'error'
                        )
                        iscontinue = false;
                    }
                })
              }
                
            }

         }
      }); 
      return iscontinue;
    }

</script>