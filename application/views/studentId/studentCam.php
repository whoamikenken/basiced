<?php 

/**
 * @author Kennedy Hipolito
 * @copyright 
 */

?>

<? $this->load->view('studentId/header'); ?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/login.css">


<!-- Modal -->
<div class="modal fade" id="signup_modal" role="dialog"></div>

<div class="container-fluid animated zoomIn">
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <div class="panel">
           <div class="panel-heading"><h4><b>Student I.D</b></h4></div>
           <div class="panel-body">
                <form>
                    <div class="form-group">
                        <div class="col-md-6 animated zoomIn delay-2s">
                        <div class="col-md-12">
                            <input type=button class="btn btn-primary" value="Take Snapshot" onClick="take_snapshot()">&nbsp;&nbsp;
                            <input type=button class="btn btn-primary" value="Save" id="save">&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="radio-inline"><input type="radio" name="user_choose" id="studentRadio" value="Student">Student</label>
                            <label class="radio-inline"><input type="radio" name="user_choose" id="employeeRadio" value="Employee">Employee</label>
                        </div>
                        <br><br><br>
                        <div id="studentSelect" class="col-md-8">
                              <label for="Student">Student</label>
                              <select class="chosen" id="Student">
                                <option value="">Choose Student</option>
                                <?
                                $opt_type = $this->studentt->getStudent();
                                foreach($opt_type as $val){
                                ?>      
                                <option value="<?=$val->studentid?>">
                                  <?=$val->fullname?>
                                </option>
                                <?    
                                }
                                ?>
                              </select>
                        </div>
                        <br><br><br><br>
                        <div id="employeeSelect" class="col-md-8">
                              <label for="Employee">Employee</label>
                              <select class="chosen" id="Employee">
                                <option value="">Choose Employee</option>
                                <?
                                $opt_type = $this->employee->getEmployeeList("");
                                foreach($opt_type as $val){
                                ?>      
                                <option value="<?=$val['employeeid']?>">
                                  <?=$val['fullname']?>
                                </option>
                                <?    
                                }
                                ?>
                              </select>
                        </div>
                        <br><br><br><br><br><br><br><br>
                        <div class="col-md-12">
                            <h4><b>Captured</b></h4>
                            <div id="results">
                              <img src="">
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6 animated zoomIn delay-2s">
                            <h4><b>Webcam</b></h4><a href="<?=base_url()?>index.php/student_id" class="btn btn-primary">PRINT</a>
                            <div id="my_camera" style="margin-left: 14%!important;"></div>
                        </div>
                    </div>
                </form>
           </div>
        </div>
    </div>
</div>

<script language="JavaScript">
  $(document).ready(function(){
      $("#employeeSelect").hide();
      $("#studentSelect").hide();
  });
    Webcam.set({
            width: 600,
            height: 600,
            crop_width: 600,
            crop_height:600,
            image_format: 'jpeg',
            jpeg_quality: 90,
            flip_horiz: true
        });

    Webcam.set('constraints', {
      optional: [
        { minWidth: 600 }
      ]
    });
        Webcam.attach('#my_camera');
</script>

<script>

function take_snapshot(){
    // take snapshot and get image data
    Webcam.snap( function(data_uri) {
        // display results in page
        $('#results').html('<img style="width: 249px;height: 226px" id="image" src="'+data_uri+'"/>');
    });
};

$("#studentRadio").click(function(){
  $("#studentSelect").show();
  $("#employeeSelect").hide();
  $("#employeeRadio").prop("checked", false);
});
$("#employeeRadio").click(function(){
  $("#studentSelect").hide();
  $("#employeeSelect").show();
  $("#studentRadio").prop("checked", false);
});

$("#save").click(function(){

    var image = $('#image').attr("src");
    if($("#studentRadio:checked").val() == "Student"){
      if ($("#Student").val()) {
        var name  = $("#Student option:selected").text();
        var id = $("#Student").val();
            if (image) {
              Webcam.upload( image, '<?=site_url('student_id/saveStudentImage')?>'+'?studentname=' + name + '&id='+ id +'', function(code, text) {
                alert('Upload Success!');
              });
            }else alert('Please Take A Snapshot');
      }else alert('Please Select A Student')
    }else if($("#employeeRadio:checked").val() == "Employee"){
      if ($("#Employee").val()) {
        var name  = $("#Employee option:selected").text();
        var id = $("#Employee").val();
            if (image) {
              Webcam.upload( image, '<?=site_url('student_id/saveStudentImage')?>'+'?studentname=' + name + '&id='+ id +'', function(code, text) {
                alert('Upload Success!');
              });
            }else alert('Please Take A Snapshot');
            
      }else alert('Please Select A Employee')
    }else{alert("Please select a student or employee")}
});

$('.chosen').chosen();
</script>