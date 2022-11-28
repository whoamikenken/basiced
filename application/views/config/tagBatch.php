<div class="container" style="width: 100%;">
    <form id="form_Office">
        <div class="form-group">
            <label class="field_name align_right">Batch Scheduling:</label>
            <div class="field">
                <select class="chosen-select" name="emptype" id="emptype" >
                       <?
                          $opt_type = $this->extras->showemployeetype();
                          foreach($opt_type as $c=>$val){
                          ?><option value="<?=$c?>"><?=$val?></option><?    
                          }
                      ?>
                  </select>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Department:</label>
            <div class="field">
                <select class="chosen-select selectTrigger" id="department" name="department"><?=$this->extras->getDeptpartment()?></select>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Office:</label>
            <div class="field">
                <select class="form chosen-select selectTrigger" id="office" name="office"><?=$this->extras->getOffice()?></select>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Teaching Type:</label>
            <div class="field">
                <select class="chosen-select selectTrigger" id="teachingType" name="teachingType">
                    <option value="">All</option>
                    <option value="teaching">Teaching</option>
                    <option value="nonteaching">Non Teaching</option>
                </select>
            </div>
        </div>
        <!-- <div class="form-group">
            <label class="field_name align_right">Employment&nbsp;Status&nbsp;:</label>
            <div class="field">
                <select class="chosen-select selectTrigger" name="empstat" id="empstat">
                    <?php
                      $empstatuses = $this->extras->showemployeestatus('All Employment Status');
                      foreach ($empstatuses as $key => $item) {
                        ?>
                        <option value='<?=$key?>'><?= ucfirst (strtolower ($item)); ?></option>
                        <?php
                      }
                    ?>
                </select>
            </div>
        </div> -->
        <div class="form-group">
            <label class="field_name align_right">Status:</label>
            <div class="field">
                <select class="chosen-select selectTrigger" name="status" id="status">
                    <option value="all">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="field_name align_right">Employee:</label>
            <div class="field">
                <select class="chosen-select" name="employeeid" id="employeeid" multiple>
                  <option value='all'> All Employee </option>
                    <?
                    $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")));
                    foreach($opt_type as $val){
                        ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . " " . $val['fname'] . " " . $val['mname'])?></option><?    
                    }
                    ?>
                </select>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
  $(".chosen-select").chosen();

  $("#department").change(function(){
      $.ajax({
          url : $("#site_url").val() + "/setup_/getOffice",
          type: "POST",
          data: {department:$(this).val()},
          success: function(msg){
              $("#office").html(msg).trigger("chosen:updated");
          }
      });
  });


  $('.selectTrigger').on('change',function(){
      var campus = GibberishAES.enc($('#campus').val(), toks);
      var teachingType = GibberishAES.enc($('#teachingType').val(), toks);
      var office = GibberishAES.enc($('#office').val(), toks);
      var department = GibberishAES.enc($('#department').val(), toks);
      var status = GibberishAES.enc($('#status').val(), toks);
      
      $.ajax({
          type : "POST",
          // url: $("#site_url").val() + "/employee_/load201sort",
          url: "<?=site_url('employee_/load201sort2')?>",
          data: {campus: campus, teachingType:teachingType, department:department, status:status, office:office,toks:toks},
          success: function(data){
              $("select[name='employeeid']").html(data).trigger("chosen:updated");
          }
      });
  });

  // $("#button_save_modal").click(function(){
  $("#button_save_modal").unbind('click').bind('click', function (e) {
    if($("select[name='employeeid']").val()) { 
      var employeeid = $("select[name='employeeid']").val();
      var emptype = $("#emptype").val();
      var campus = $('#campus').val();
      var teachingType = $('#teachingType').val();
      var office = $('#office').val()
      var department = $('#department').val();
      var status = $('#status').val();
      if(emptype){
        Swal.fire({
            html: "<h4 id='processingMsg'>Tagging Employee(s) <br> Please wait..</h4>",
            allowOutsideClick: false,
            allowEscapeKey: false,
            onRender: function() {
                $('.swal2-content').prepend(sweet_loader);
            }
        });
        $.ajax({
            type : "POST",
            url: "<?=site_url('configuration_/savingBatchSched')?>",
            data: {employeeid:employeeid, emptype:emptype, campus: campus, teachingType:teachingType, department:department, status:status, office:office},
            success: function(data){
                if(data == "done"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "Employee/s has been tagged successfully!",
                        showConfirmButton: true,
                        timer: 1000
                    }) 
                    $(".modalclose").click();
                }
            }
        });
      }else{
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Batch Scheduling is required!",
            showConfirmButton: true,
            timer: 1000
        }) 
      }
    }else{
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please select employee first!",
            showConfirmButton: true,
            timer: 1000
        })  
    }
  })

  $("select[name='employeeid']").change(function(){
    if($(this).val()){
      if(!$(this).val().includes("all")){
        $('#employeeid option[value="all"]').attr("disabled", true).trigger("chosen:updated");
        $('#employeeid option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
      }else{
        $('#employeeid option[value!="all"]').attr("disabled", true).trigger("chosen:updated");
        $('#employeeid option[value="all"]').attr("disabled", false).trigger("chosen:updated");
      }
    }else{
      $('#employeeid option[value="all"]').attr("disabled", false).trigger("chosen:updated");
      $('#employeeid option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
    }
  });

</script>