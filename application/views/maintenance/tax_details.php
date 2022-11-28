<?php

/**
 * @author Robert Ram Bolista
 * @copyright ram_bolista@yahoo.com
 * @date 9-5-2013
 * @time 16:58
 * @modified Justin 2015
 */
 
 
 $cdisable = false;
 $description = "";
 $type = "";
 $tax_range = "";
 $tstatus = "";
 $percent = "";
 $basic = "";
 $basicamt = "";
 $exemption = "";
 if($code){
     $sql = $this->db->query("select * from code_tax WHERE tax_id='$code'");
     if($sql->num_rows()>0){
        $basic = $sql->row(0)->basic_tax;
        $type = $sql->row(0)->tax_type;
        $tstatus = $sql->row(0)->status_;
        $basicamt =$sql->row(0)->basic_amount;
        $percent = $sql->row(0)->percent;
        $tax_range = $sql->row(0)->tax_range;
        $exemption = $sql->row(0)->exemption;
        $sql_code = $this->db->query("select * from code_tax_status WHERE status_code='$tstatus'");
        $exemption = isset($sql_code->row(0)->status_exemption)?$sql_code->row(0)->status_exemption:0;

     }
     $cdisable = true;
 }
?>
<form id="form_income">
  <div class="col-md-12">
    <div class="form-group">
      <label for="employeeid" class="col-sm-3 align_right">Type</label>
      <div class="col-sm-9">
         <select class="form-control chosen-select" name="mh_code" id="mh_code"><?=$this->payrolloptions->payschedule($type);?></select>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Status</label>
      <div class="col-sm-9">
          <select class="form-control chosen-select" name="mh_description"<?=($cdisable ? " disabled=true" : "")?>>
              <option value='no value'>- Select Status -</option>
            <?
              $opt_income = $this->extras->showtaxstatus();
              foreach($opt_income as $c=>$val){
              ?><option<?=($c==$tstatus ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
              }
            ?>
            </select>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Basic Tax</label>
      <div class="col-sm-9">
        <input class="form-control" id="mh_basic" name="mh_basic" type="text" value="<?=$basic?>"/>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Basic Amount</label>
      <div class="col-sm-9">
        <input class="form-control" id="mh_basicamt" name="mh_basicamt" type="text" value="<?=$basicamt?>"/>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Percent</label>
      <div class="col-sm-9">
        <input class="form-control" placeholder="%" id="mh_percent" name="mh_percent" type="text" value="<?=($percent ? $percent : "")?>"/>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Range</label>
      <div class="col-sm-9">
        <input class="form-control" id="mh_range" name="mh_range" type="text" value="<?=$tax_range?>"/> 
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Tax Exemption</label>
      <div class="col-sm-9">
        <input class="form-control" id="mh_exempt" name="mh_exempt" type="text" value="<?=$exemption?>" readonly/>
      </div>
    </div>
  </div>
<script>
    var toks = hex_sha512(" ");
    $('.chosen-select').chosen()

  // for selecting  dependent status
  $("[name=mh_description]").change(function(){
      //alert(this.value);
      if($("[name=mh_description]").val() == "no value") return;

      $.ajax({
          url     : "<?=site_url("maintenance_/displayedTaxExemption")?>",
          type    : "POST",
          data    : {
                      toks : toks,
                      code: GibberishAES.enc(this.value, toks)
                    },
          success : function(msg){
              $("#mh_exempt").val(msg);
          }
      });
  });

  $(".modal-footer").find("#button_save_modal").unbind("click").click(function(){
    if($("select[name='mh_code']").val()==""){
      alert("Type is required");
      $("input[name='mh_code']").focus();
      return;
    }else if($("select[name='mh_description']").val()==""){
      alert("Status is required.");
      $("input[name='mh_description']").focus();
      return;
    }

    $.ajax({
      url:"<?=site_url("maintenance_/save_tax")?>",
      type:"POST",
      data:{
        toks: toks,
        taxid: GibberishAES.enc("<?=$code?>", toks), 
        taxtype: GibberishAES.enc($("select[name='mh_code']").val(), toks),
        taxstatus: GibberishAES.enc($("select[name='mh_description']").val(), toks),
        basic: GibberishAES.enc($("input[name='mh_basic']").val(), toks),
        basicamt: GibberishAES.enc($("input[name='mh_basicamt']").val(), toks),
        percent: GibberishAES.enc($("input[name='mh_percent']").val(), toks),
        taxrange: GibberishAES.enc($("input[name='mh_range']").val(),toks),
        exemption: GibberishAES.enc($("input[name='mh_exempt']").val(), toks),
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
    return false;   
  });

  // end of selecting..
</script>
