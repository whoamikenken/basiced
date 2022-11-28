<?
$datetoday = date('Y-m-d');
?>
<style type="text/css">
#tabless
{
  width: 100%;
}
#tabless_wrapper{
  padding: 24px;
} 
.modal{
    width:auto;
    margin: auto;
}
@media (min-width: 992px){
  .modal-lg {
      width: 800px;
  }
}
.form_row{
  padding-bottom: 10px;
}

#request_form{
  margin-top: 30px;
}
</style>

<form id="request_form">
          <div class="form_row">
              <label class="field_name align_right">Date</label>
              <div class="field no-search">
                <div class="col-md-12">
                  <div class='input-group date' id='datePicker' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" size="16" name="date" id="date" type="text" value="<?=$datetoday?>"/>
                    <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                  </div>
                </div>
              </div>
          </div>
          <div class="form_row">
              <label class="field_name align_right">Status</label>
              <div class="field">
                <div class="col-md-12">
                  <select class="chosen col-md-4" id="status" name="status">
                      <option value="all">All</option>
                      <option value="1">Active</option>
                      <option value="0">Inactive</option>
                  </select>
                </div>
              </div>
          </div>
          <div class="form_row">
              <label class="field_name align_right">Department</label>
              <div class="field">
                <div class="col-md-12">
                  <select class="chosen col-md-4" id="department" name="department">
                      <option value="">All Department</option>
                      <?php foreach ($this->extras->getOfficeDescription() as $key => $value): ?>
                        <option value="<?=$key?>"><?=$value?></option>
                      <?php endforeach ?>
                  </select>
                </div>
              </div>
          </div>
          <div class="form_row">
              <label class="field_name align_right">Office</label>
              <div class="field">
                <div class="col-md-12">
                  <select class="chosen col-md-4" id="office" name="office">
                      <option value="">All Office</option>
                      <?php foreach ($this->extras->getDepartmentDescription() as $key => $value): ?>
                        <option value="<?=$key?>"><?=$value?></option>
                      <?php endforeach ?>
                  </select>
                </div>
              </div>
          </div>
          <div class="form_row">
              <label class="field_name align_right">Type</label>
              <div class="field no-search">
                <div class="col-md-12">
                  <select class="chosen col-md-4" id="etype" name="etype">
                      <option value="">All Type</option>
                      <option value="teaching">Teaching</option>
                      <option value="nonteaching">Non-teaching</option>
                  </select>
                </div>
              </div>
          </div>
  <!-- <div class="form_row">
      <label class="field_name align_right">Campus</label>
      <div class="field">
        <div class="col-md-12">
          <select class="chosen col-md-4" id="campus" name="campus">
              <option value="">All Campus</option>
              <?=$this->extras->getCampuses()?>
          </select>
        </div>
      </div>
  </div> -->

    <div class="form_row">
      <label class="field_name align_right">Batch Scheduling</label>
      <div class="field">
        <div class="col-md-12">
          <select class="chosen col-md-4" id="cluster" name="cluster">
              <option value="">All Cluster</option>
              <?=$this->extras->loadclustertype()?>
          </select>
        </div>
      </div>
  </div>



  <div class="form_row" style="padding-bottom: 0px;">
      <div class="field">
          <a href='#' class='btn btn-primary' style="margin-left: 15px;" id='savebatch'>Search</a>   
      </div>
  </div>
  
 
     <div class='adjustment'></div>

</form>

<script type="text/javascript">
  var toks = hex_sha512(" "); 
$(document).ready(function(){
  $(".datatable").DataTable();
  $(".savebatch").hide();
});


$("#savebatch").unbind('click').click(function()
{
  $("#savedata").hide();
  if ($("#date").val() == "" || $("#date").val() == null) {
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Date is required',
        showConfirmButton: true,
        timer: 1000
    })
  }
  else 
  {
      var form_data = {
      date: GibberishAES.enc($("#date").val(), toks),
      status: GibberishAES.enc($("#status").val(), toks),
      cluster: GibberishAES.enc($("#cluster").val(), toks),
      office: GibberishAES.enc($("#office").val(), toks),
      department: GibberishAES.enc($("#department").val(), toks),
      etype:GibberishAES.enc($("#etype").val(), toks),
      toks: toks
      }
      console.log(form_data);
      $(".adjustment").html("Loading... Please wait!");
      $.ajax({
          url: "<?=site_url('process_/showAdjustment')?>",
          type: "POST",
          data: form_data,
          success: function(msg){
            $("#savedata,.savedata").hide();
              $(".adjustment").html(msg);
              $("#saving").append("<a href='#' class='btn btn-success savedata' id='savedata'>Save</a>");
          }
      });
  } 
});



// $(".modal-footer").find(".savedata").click(function()
// {
//   alert("HAYSSS");return;
// });
$("#datePicker").datetimepicker(
  {
    format: "YYYY-MM-DD",
    maxDate: new Date
  });
$(".chosen").chosen();


// $(document).on("click","#savedata",function()
// {
//   var ids = [];
//   alert($("#employeelist").find("tr[tag='empdata']").attr('employeeid'));
//   $("#employeelist").find("tr[tag='empdata']").each(function()
//   {
//       ids.push($(this).attr('employeeid'));
//   });
//   console.log(ids);
// });

$("#department").change(function(){
    $.ajax({
        url: "<?=site_url('setup_/getOffice')?>",
        type: "POST",
        data: {department:$(this).val()},
        success: function(msg){
            $("#office").html(msg).trigger("chosen:updated");
        }
    });
});
</script>