<?
$username = $this->session->userdata('username');
$utype = $this->session->userdata('usertype');
if ($utype == "EMPLOYEE") {
  $deptid = $this->extras->getHeadOffice($username);
}else{
  $deptid = "";
}

// echo "<pre>";print_r($departmentDeflist);die;

    $defModel = new Deficiency();
    $def_data = $defModel->getDeficiencyInfo($id);
    $action = ($def_data) ? "edit" : "add";
    $type = $desc = "";
    if ($id == "" AND $utype == "EMPLOYEE") $deptid = $this->extras->getHeadOffice($username);
    else $deptid = "";
    if($def_data){
      foreach ($def_data as $row) {
        $type = $row->type;
        $desc = $row->description;
        $deptid = isset($row->deptid) ? $row->deptid : $deptid;
      }
    }
?>
<style type="text/css">
  .form_row{
    margin-bottom: 20px;
  }
</style>
   <input type="hidden" id="isdeptid" value="<?=$departmentid;?>" />
<form id="info_form">

    <input type="hidden" name="id" value="<?=$id;?>" />
    <div class="form_row" style="margin-top: 20px;">
      <label class="field_name align_right">Type</label>
      <div class="field">
        <div class="col-md-12">
          <input class="form-control required" id="type" name="type" type="text" value="<?=$type?>"/>
        </div>
      </div>
    </div>
    <div class="form_row">
      <label class="field_name align_right">Description</label>
      <div class="field">
        <div class="col-md-12">
          <input class="form-control required" id="description" name="description" type="hidden" value="<?=$desc?>"/>
          <input class="form-control required" id="desc" name="desc" type="text" value="<?=$desc?>"/>
        </div>
      </div>
    </div>
    <div class="form_row">
      <label class="field_name align_right">Concerned Office</label>
      <div class="field">
        <div class="col-md-12">
          <select id="departmentid" class="chosen" name="departmentid" <?= ($utype == "EMPLOYEE" ) ? 'disabled' : ''; ?>>
            <option value="">Select Office</option>
            <?php foreach ($departmentDeflist as $value): ?>
              <option value="<?= $value['code'] ?>" <?= ($deptid == $value['code']) ? 'selected' :''; ?>><?= $value['description'] ?></option>  
            <?php endforeach ?>
          </select>
        </div>
      </div>
    </div>
    <div class="field">
      <div>
        <span id="errmsg"></span>
      </div>
    </div>
</div>
</form>

<script>
  var toks = hex_sha512(" ");
    $("#button_save_modal").unbind("click").click(function(){
        // $("#errmsg").html("<h6>This may take a while, please wait...</h6>");
        var $validator = $("#info_form").validate({
            rules: {
                type: {
                  required: true,
                  minlength: 2
                },
                desc: {
                  required: true,
                  minlength: 2
                },
                departmentid: {
                  required: true
                }
            }
        });
        var form_data = $('#info_form').serialize();
        var formdata = "";  
        $('#info_form input, #info_form select, #info_form textarea').each(function(){
          if(formdata) formdata += '&'+$(this).attr('name')+'='+GibberishAES.enc( $(this).val() , toks);
          else formdata = $(this).attr('name')+'='+ GibberishAES.enc($(this).val(), toks);
        })
            formdata += "&action="+ GibberishAES.enc("<?=$action?>", toks);
            formdata += "&toks="+toks;
        var encodedData = encodeURIComponent(window.btoa(formdata));
        var isdeptid = $("#isdeptid").val();
        // $('#info_form input').attr("disabled",true);
        // $('#info_form').find(".chosen").attr("disabled",true);
        // $('#info_form').find(".btn").attr("disabled",true);
        if($("#info_form").valid()){
             $.ajax({
                url:"<?=site_url("deficiency_/saveForm")?>",
                type:"POST",
                data:{formdata:encodedData},
                dataType: 'JSON',
                success: function(msg){
                    if(msg.err_code == 0){
                        $("#modalclose").click();
                        if("<?=$action?>" == "add"){
                          Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: 'Clearance has been saved successfully.',
                              showConfirmButton: true,
                              timer: 1000
                          })
                        }else{
                          Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: 'Clearance has been updated successfully.',
                              showConfirmButton: true,
                              timer: 1000
                          })
                        }
                        loaddeficiencydata(isdeptid);
                        // $(".inner_navigation .main li .active a").click();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: msg.msg,
                            showConfirmButton: true,
                            timer: 1000
                        })
                    }
                }
             });
       }else {
           $validator.focusInvalid();
           return false;
       }
    });
$(".chosen").chosen();    
</script>