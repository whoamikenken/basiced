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
      <label for="employeeid" class="col-sm-3 align_right">Dependent Code:</label>
      <div class="col-sm-9">
         <input class="form-control ucase" id="dependent_code" name="dependent_code" type="text" readonly/>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Status Name:</label>
      <div class="col-sm-9">
          <select class="form-control" name="select_desc">
                <option value="no value">-- Select Dependent ---</option>
                <?
                  $i = 0;
                  $opt_income = $this->extras->showtaxstatus('get');

                  foreach(($opt_income->result()) as $c){
                    echo "<option value='".$c->status_code."~".$c->status_desc."~".$c->status_exemption."'>".$c->status_desc."</option>";
                  }
                ?>
            </select>
            <input class="form-control ucase" id="status_name" name="status_name" type="text" hidden=""/>
            <script>
                    $("#status_name").hide();
            </script>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Tax Excemption:</label>
      <div class="col-sm-9">
        <input class="form-control" id="tax_excemption" name="tax_excemption" type="text" />
      </div>
    </div>
</form>

<script>
    var toks = hex_sha512(" ");
    // select dependent
    $("[name=select_desc]").change(function(){
        if($(this).val() == "no value") return;
        var strArray = this.value.split("~");
        
        // show dependent info & hide select tags
        $("#status_name").show();
        $("#dependent_code").val(strArray[0]);
        $("#status_name").val(strArray[1]);
        $("#tax_excemption").val(strArray[2]);

        $("#hideSelect").hide();
    });

    // discard dependent
    $(".modal-footer").find("#cancel_btn").click(function(){
        if($("[name=select_desc]").val() == "no value") return;
        var res = confirm("Are you sure, you want to discard this Dependet Setup?");
        if(res === true){
            // clear dependent & show select tags
            $("#status_name").hide();
            $("#hideSelect").show();
            $("[name=select_desc]").val($("[name=select_desc] option:first").val());
            $("#dependent_code").val('');
            $("#status_name").val('');
            $("#tax_excemption").val('');
        }
    });

    // delete dependent
    $(".modal-footer").find("#delete_btn").click(function(){
        if($("[name=select_desc]").val() == "no value") return;
        var strArray = $("[name=select_desc]").val().split("~");
        var res = confirm("Are you sure, you want to delete this Dependet Setup ('"+strArray[1]+"')?");
        if(res === true){
            // delete here
            $.ajax({
                url : "<?=site_url('maintenance_/saveDependentSetup')?>",
                type : "POST",
                data : {
                         toks: toks,
                         job : GibberishAES.enc("deleteDependent", toks),
                         dep_code : GibberishAES.enc($("#dependent_code").val(), toks),
                         stat_name : GibberishAES.enc($("#status_name").val(), toks),
                         tax_exc : GibberishAES.enc($("#tax_excemption").val(), toks)
                       },
                success: function(msg){
                    alert(msg);
                    if(msg == "Successfully Deleted!.")$("#modalclose").click();
                }
            });
        }
    });

    // save update dependent setup
    $(".modal-footer").find("#button_save_modal").click(function(){
        if($("[name=select_desc]").val() == "no value") return;
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
                         job : GibberishAES.enc("updateDependent", toks),
                         dep_code : GibberishAES.enc($("#dependent_code").val(), toks),
                         stat_name : GibberishAES.enc($("#status_name").val(), toks),
                         tax_exc : GibberishAES.enc($("#tax_excemption").val(), toks)
                       },
                success: function(msg){
                    alert(msg);
                    if(msg == "Successfully Saved!.")$("#modalclose").click();
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