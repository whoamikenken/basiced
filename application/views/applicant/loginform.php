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
<form id="loginform" action="<?=site_url('applicant/login')?>" method="POST">
<div class="row">
  <input type="hidden" name="positionid" value="<?=$positionid?>">
  <div class="col s12">
    <div class="col s12 col s3">
      <label style="    color: #1d1b1b;font-weight: 700;">Email :</label>
    </div>
    <div class="col s12 col s7">
      <input type="email" class="waves-effect waves-yellow validate" name="email" id="email" style="text-transform: uppercase;">
    </div>
  </div>
  <div class="col s12">
    <div class="col s12 col s3">
      <label style="    color: #1d1b1b;font-weight: 700;">Password :</label>
    </div>
    <div class="col s12 col s7">
      <input type="password" class="waves-effect waves-yellow" name="password" id="password">
      <span><i>The default password is your surname (UPPER CASE)</i></span>
    </div>
  </div>
</div>
</form>
<script src="<?=base_url()?>js/sweetalert.js"></script>
<script type="text/javascript">
var toks = hex_sha512(" ");
$('select').formSelect();
    $('#logsubmitexisting').on('click', function(){
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

          if ($("#password").val() == "") {
              $("#password").addClass("invalid");
              Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please Input Your Password!',
                    showConfirmButton: true,
                    timer: 1000
              })
              return;
          }else{
            $("#password").removeClass("invalid");
          }
          var form_data = "";  
          $('#loginform input, #loginform select, #loginform textarea').each(function(){
            if(form_data) form_data += '&'+$(this).attr('name')+'='+$(this).val();
            else form_data = $(this).attr('name')+'='+$(this).val();
          })
          $.ajax({
             url: "<?=site_url("applicant/validateLogin")?>",
             data : {formdata:GibberishAES.enc(form_data, toks), toks:toks},
             dataType: 'JSON',
             type : "POST",
             success:function(msg){
                if(msg.err_code==0){
                  if(!checkIfHasDataLogin(form_data)){
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

    function checkIfHasDataLogin(form_data){
      var iscontinue = true;
      var confirm = "";
      $.ajax({
         url: "<?=site_url("applicant/checkIfHasDataLogin")?>",
         data : {formdata:GibberishAES.enc(form_data, toks), toks:toks},
         type : "POST",
         dataType: "json",
         async: false,
         success:function(response){
            if(response.isexist && response.seqno == 0 && response.submitted == 0 && response.email == 0 && (response.redtag == 0 || response.redtag == null) && response.isactive != 0 && (response.datehired == 0 || response.datehired == null)){
              $("#loginform").unbind().submit();
            }
            else if(response.isexist && response.seqno && response.submitted && response.email == 0 && (response.redtag == 0 || response.redtag == null) && response.isactive != 0 && (response.datehired == 0 || response.datehired == null)){ 
              $("#loginform").unbind().submit();
            }else if(response.datehired != 0 && response.datehired != null){
               Swal.fire({
                      icon: 'warning',
                      title: 'The user of this account is already hired',
                      showConfirmButton: true,
                      timer: 1000
                })
              return;
            }else if(response.isactive == 0){ 
              // Swal.fire({
              //         icon: 'warning',
              //         title: 'Warning!',
              //         text: 'Your application has been archived. It cannot be retrieved anymore. Thank you for taking interest in our institution.',
              //         showConfirmButton: true,
              //         timer: 1000
              //   })
              // return;
              $("#loginform").unbind().submit();
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
              Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'The username or password you entered is incorrect.',
                      showConfirmButton: true,
                      timer: 1000
                })
              return;
            }

         }
      }); 
      return iscontinue;
    }

</script>