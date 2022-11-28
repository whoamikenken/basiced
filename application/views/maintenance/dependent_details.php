<!-- 
    * Title  : For dependent setup 
    * Author : Justin (with e)
    * Date   : 8-24-2017
    *
-->
<style>
    .ucase{
        text-transform: uppercase;
    }
</style>
<form id="form_dependent">
 <div class="col-md-12">
    <div class="form-group">
      <label for="employeeid" class="col-sm-3 align_right">Type</label>
      <div class="col-sm-9">
         <select class="form-control" name="mh_code" id="mh_code"><?=$this->payrolloptions->payschedule($type);?></select>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Dependent Code:</label>
      <div class="col-sm-9">
         <input class="form-control ucase" id="dependent_code" name="dependent_code" type="text" />
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Status Name:</label>
      <div class="col-sm-9">
        <input class="form-control ucase" id="status_name" name="status_name" type="text" />
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Tax Excemption:</label>
      <div class="col-sm-9">
        <input class="form-control" id="tax_excemption" name="tax_excemption" type="text" />
      </div>
    </div>
</div>
</form>

<script>
var toks = hex_sha512(" ");
$('.chosen-select').chosen()

    // save dependent setup
    $(".modal-footer").find("#button_save_modal").click(function(){
        
        // validate
        var $validate;
        $validate = $('#form_dependent').validate({
            rules:{
                dependent_code :{
                    required : true,
                    minlength : 1
                },
                status_name :{
                    required : true,
                    minlength : 4
                },
                tax_excemption :{
                    required : true,
                    minlength : 3
                }
            }
        });
        // end of validate

        if($('#form_dependent').valid()){
            $.ajax({
                url : "<?=site_url('maintenance_/saveDependentSetup')?>",
                type : "POST",
                data : {
                         toks: toks,
                         job : GibberishAES.enc("saveNewDependent", toks),
                         dep_code : GibberishAES.enc($("#dependent_code").val(), toks),
                         stat_name : GibberishAES.enc($("#status_name").val(), toks),
                         tax_exc : GibberishAES.enc($("#tax_excemption").val(), toks)
                       },
                success: function(msg){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    });
                    setTimeout(function(){ 
                      location.reload();
                    }, 2000);
                }
            });
        }
    });

    // number only
    $("#tax_excemption").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
             // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

    });
    // end of number only

    // Two decimal point
    $('#tax_excemption').blur(function(){
        if($(this).val().indexOf('.')!=-1){         
            if($(this).val().split(".")[1].length > 2){                
                if( isNaN( parseFloat( this.value ) ) ) return;
                this.value = parseFloat(this.value).toFixed(2);
            }  
         }            
         return this;
    });
    // end of two decimal point

</script>
<!-- End file -->