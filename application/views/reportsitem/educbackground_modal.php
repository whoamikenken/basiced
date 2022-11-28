<?php

/**
 * @author P4
 * @copyright 2017
 */
$sm_date = date("Y-m-d");
$level = '';
$points = '';
$ID = '';
if($naction=='add'){
  $category = $category;
}

if($naction=='edit'){
   foreach($eb_data as $key=>$row){
    $level = $row->level;
    $points = $row->points;
    $ID = $row->ID;
  }
}
 
?>

<style type="text/css">
  .form_row{
    padding-bottom: 10px;
  }

  #educForm{
    margin-top: 10px;
  }

  .modal-overflow .modal-body {
    margin-bottom: 0px;
    /* overflow: auto; */
    /*-webkit-overflow-scrolling: touch;*/
}
.error {
      color: red;
   }

  
</style>
<?php   
$level = strtr($level, '^~', ')(');
?>
<form id="educForm">
<div class="form_row">
  <div class="col-md-11">
  <div class="col-md-4">
    <label class="field_name align_right" id="desc_label" style="width: 90%;"></label>
  </div>  
    <input type='hidden' name ='eb_id' value='<?=$ID?>'>
    <input type='hidden' name ='eb_category' value='<?=$category?>'>
    <input type='hidden' name ='displaydata' value='<?=$displaydata?>'>
    <input type='hidden' name ='naction' value='<?=$naction?>'>
    <?php if ($category){ ?>
      <div class="col-md-7">
        <input type="text" name="eb_educlevel" class="form-control" value='<?=$level?>' />
    </div>
    <?php }else{ ?>
    <div class="col-md-7">
        <input type="text" name="eb_educlevel" class="form-control" value='<?=$level?>' />
    </div>
</div>
</div>
<div class="form_row">
  <div class="col-md-11">
    <div class="col-md-4">
      <label class="field_name align_right" style="width: 90%;">Points</label>
    </div>
      <div class="col-md-7">
        <input type="text" name="eb_points" id="eb_points" class="form-control" value='<?=$points?>' />
      </div>
    </div>
  </div>
<?php } ?>
</form>
<script>
    $(document).ready(function() {
      $("#eb_points").bind("keypress", function (e) {
          var keyCode = e.which ? e.which : e.keyCode
               
          if (!(keyCode >= 48 && keyCode <= 57)) {
            $(".error").css("display", "inline");
            return false;
          }else{
            $("label.error").css("display", "none");
          }
      });
    });

function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}

$("#button_save_modal").unbind("click").click(function(){
 var validator = $("#educForm").validate({
        rules: {
            eb_educlevel: {
              required: true
            },
            eb_points: {
              required: true,
              digits : true
            }
        }
    });

 if($("#educForm").valid()){
    var form_data = '';
    $('#educForm input, #educForm select, #educForm textarea').each(function(){
      // if (your_string.indexOf('hello') > -1)
        if ($(this).val().indexOf('alert(') > -1) {
          var tmptext = $(this).val().replace('alert(', 'alert~');
          var text = tmptext.replace(')', '^');
              if(form_data) form_data += '&'+$(this).attr('name')+'='+text;
              else form_data = $(this).attr('name')+'='+text;
        }else{
          if(form_data) form_data += '&'+$(this).attr('name')+'='+$(this).val();
          else form_data = $(this).attr('name')+'='+$(this).val();
        }
        
    })
    var displaydata = $("input[name='displaydata']").val();
    var naction = $("input[name='naction']").val();
    var desc = {
    'eb_data'     : 'Educational Background',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'PSSFP',
    'pts_pdp1_data'  : 'Professional Development Program',
    'pts_pdp2_data'  : 'Pep Development Program',
    'pts_pdp3_data'  : 'Psychosocial - Cultural',
    'ect_data'   : 'Emergency Contact Type',
    'e_data'     : 'Eligibility',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Awards And Recognition',
    'pub_data'    : 'Publication',
    's_data'     : 'Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions Handled',
    'leave_data'   : 'Leave Category'
    };
    var title_desc = "";
    var savedesc = "";
    $.each(desc, function (value, description) {
        if(displaydata == value){
          naction == 'add' ? title_desc = description+" has been saved successfully" : title_desc = description+" has been updated successfully";
          savedesc = description;
        }
    }) 
    // form_data = $("#educForm").serialize();
    form_data = form_data+"&reportdesc="+savedesc;
    $.ajax({
      url     :    "<?=site_url("reportsitem_/saveeducbackground")?>",
      type    :    "POST",
      data    :    {formdata:GibberishAES.enc( form_data, toks), toks:toks},
      success :    function(msg){
        // console.log(form_data);
        $("#"+displaydata).html(msg);
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: title_desc,
            showConfirmButton: true,
            timer: 1000
        })
      }
    });
    
    $("#modalclose").click();
    return false;
   }else{
      validator.focusInvalid();
      return false;
   }
    
   
});
$(".date_issued").datepicker({
    autoclose: true,
    todayBtn: true
});
$('.chosen').chosen();
</script>