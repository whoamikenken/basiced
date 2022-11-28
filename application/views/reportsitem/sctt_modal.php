<?php
    $sm_date = date("Y-m-d");
    $ID = $subj_code = $description = $remarks = '';

    if($naction=='add'){
      $category = $category;
    }

    if($naction=='edit'){
        foreach($eb_data as $key=>$row){
          $subj_code = $row->subj_code;
          $description = $row->description;
          $remarks = $row->remarks;
          $ID = $row->id;
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

    <form id="educForm">
    <div class="form_row">
      <div class="col-md-12">
      <div class="col-md-3">
        <label class="field_name align_right" style="width: 90%;">Subject Code</label>
      </div>
        <input type='hidden' name ='eb_id' value='<?=$ID?>'>
        <input type='hidden' name ='eb_category' value='<?=$category?>'>
        <input type='hidden' name ='displaydata' value='<?=$displaydata?>'>
        <input type='hidden' name ='naction' value='<?=$naction?>'>
        <div class="col-md-7">
            <input type="text" name="eb_subjcode" class="form-control" value='<?=$subj_code?>'  onkeydown="upperCaseF(this)"  />
        </div>
    </div>
  </div>
    <div class="form_row">
      <div class="col-md-12">
      <div class="col-md-3">
        <label class="field_name align_right" style="width: 90%;">Description</label>
      </div>
        <div class="col-md-7">
            <input type="text" name="eb_description" class="form-control" value='<?=$description?>'  onkeydown="upperCaseF(this)"  />
        </div>
    </div>
  </div>
    <div class="form_row">
      <div class="col-md-12">
      <div class="col-md-3">
        <label class="field_name align_right" style="width: 90%;">Remarks</label>
      </div>
        <div class="col-md-7">
            <input type="text" name="eb_remarks" class="form-control" value='<?=$remarks?>'  onkeydown="upperCaseF(this)"  />
        </div>
    </div>
  </div>
    </form>
  



<script>

  function upperCaseF(a){
    setTimeout(function(){
        a.value = a.value.toUpperCase();
    }, 1);
}
$("#button_save_modal").unbind("click").click(function(){
 var validator = $("#educForm").validate({
        rules: {
            eb_subjcode: {
              required: true
            },
            eb_description: {
              required: true
              // digits : true
            },
            eb_remarks: {
              required: true
              // digits : true
            }
        }
    });

 if($("#educForm").valid()){
  var form_data = '';
    $('#educForm input, #educForm select, #educForm textarea').each(function(){
        if(form_data) form_data += '&'+$(this).attr('name')+'='+$(this).val();
        else form_data = $(this).attr('name')+'='+$(this).val();
    })
    var displaydata = $("input[name='displaydata']").val();
    var naction = $("input[name='naction']").val();
    var desc = {
    'eb_data'     : 'Educational Background',
    'pts_data'   : 'Venue',
    'pts_pdp_data' : 'PSSFP',
    'pts_pdp1_data'  : 'Professional Development Program',
    'pts_pdp2_data'  : 'Pep Development Program',
    'pts_pdp3_data'  : 'Psycosocial - Cultural',
    'ect_data'   : 'Emergency Contact Type',
    'e_data'     : 'Eligibility',
    'oc_data'    : 'Other Credentials',
    'sctt_data'    : 'Subject Competent to Teach',
    'pgd_data'   : 'Professional Growth and Development',
    'r_data'     : 'Researches',
    'ar_data'    : 'Awards & Recognition',
    'pub_data'    : 'Publication',
    's_data'     : 'Scholarship',
    'pi_data'    : 'Professional Involvements',
    'tw_data'    : 'Related Training/Workshop',
    'se_data'    : 'Speaking Engagements/Resource Speaker',
    'mapo_data'    : 'Membership',
    'afh_data'   : 'Administrative Functions'
    };
    var title_desc = "";
    $.each(desc, function (value, description) {
        if(displaydata == value){
          naction == 'add' ? title_desc = description+" has beed saved successfully" : title_desc = description+" has beed updated successfully";
        }
    }) 
    console.log(displaydata);
    $.ajax({
      url     :    "<?=site_url("reportsitem_/saveSubjCompetentToTeach")?>",
      type    :    "POST",
      data    :    {formdata:GibberishAES.enc( form_data, toks), toks:toks},
      success :    function(msg){
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