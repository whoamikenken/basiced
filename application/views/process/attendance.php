  <?php

  /**
   * @author Justin
   * @copyright 2016
   */
  $cdatefrom = date("Y-m-d");
  $cdateto = date("Y-m-d");

  ?>

  <style type="text/css">
    .form_row{
      padding-bottom: 10px;
    }

    .panel-body{
      margin-top: 30px;
      margin-bottom: 20px;
    }  
         .panel {
    border: 5px solid #0072c6 !important;
    margin-bottom: 49px !important;
}
  </style>

  <div id="content"> <!-- Content start -->
  <div class="widgets_area">
  <div class="row">
      <div class="col-md-12">
          <div class="panel">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employee Attendance</b></h4></div>
            <div class="panel-body">
              <div style="width: 53%;">
                <div class="form_row" style="width: 100%; ">
                    <label class="field_name align_right">Date From</label>
                    <div class="field">
                      <div class="col-sm-5" style="width: 45%;">
                        <div class='input-group date' id="datesetfrom" data-date="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd">
                          <input class="form-control" size="16" name="datesetfrom" type="text" value="<?=$cdatefrom?>"/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>
                      <div class="col-sm-1 control-label" style="width: 10%;">
                        <label> To</label>
                      </div>
                      <div class="col-sm-5" style="width: 45%;">
                        <div class='input-group date' id="datesetto" data-date="<?=$cdateto?>" data-date-format="yyyy-mm-dd">
                          <input class="form-control" size="16" name="datesetto" type="text" value="<?=$cdateto?>"/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="form_row" id="edept">
                    <label class="field_name align_right">Status</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-6" name="isactive" id="isactive">
                              <option value="">All Status</option>
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form_row" id="edept">
                    <label class="field_name align_right">Department</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-6" name="deptid" id="deptid">
                              <option value="">All Department</option>
                            <?
                              $opt_department = $this->extras->showdepartment();
                              foreach($opt_department as $c=>$val){
                              ?><option value="<?=$c?>"><?=$val?></option><?
                              }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form_row" id="edept">
                    <label class="field_name align_right">Office</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-6" name="officeid" id="officeid">
                              <option value="">All Office</option>
                            <?
                              $opt_department = $this->extras->showoffice();
                              foreach($opt_department as $c=>$val){
                              ?><option value="<?=$c?>"><?=$val?></option><?
                              }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Teaching Types</label>
                    <div class="field">
                        <div class="col-md-12 no-search">
                            <select class="chosen col-md-6" id="tnt">
                            <?
                              $type = array("teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                              foreach($type as $c=>$val){
                              ?><option value="<?=$c?>"><?=$val?></option><?
                              }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Employment&nbsp;Status</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-6" name="estatus" id="estatus">
                              <option value="">All Status</option>
                            <?
                              $opt_status = $this->extras->showStatus();
                              foreach($opt_status->result() as $row){
                              ?><option value="<?=Globals::_e($row->code)?>"><?=Globals::_e($row->description)?></option><?
                              }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form_row">
                    <label class="field_name align_right">Employee</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-6" name="employeeid">
                                <option value="">All Employee</option>
                            <?
                              $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",false,'teaching');
                              foreach($opt_type as $val){
                              ?><option value="<?=Globals::_e($val['employeeid'])?>"><?=(Globals::_e($val['employeeid']) . " - " . Globals::_e($val['lname']) . ", " . Globals::_e($val['fname']) . " " . Globals::_e($val['mname']))?></option><?    
                              }
    // echo "<pre>"; print_r($opt_type);
                            ?>
                            </select>
                            
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Data</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-6" name="edata" >
                                <option value="NEW">ACTUAL DATA</option>
                                <option value="OLD">OLD DATA ( OLDER THAN 2 MONTHS )</option>

                            </select>
                        </div>
                    </div>
                </div>    
                <br>
                <?
                #echo "test".Globals::seturl();
                ?>                                    
                 <div class="form_row" style="margin-left: 18px;">
                    <div class="field">
                        <!-- <a href="#" class="btn btn-primary" id="butt_displaylogs" style="display: none;">Summary Report</a> --><!-- ica-hyperion 21967 -->
                        <!-- <a href="#" class="btn btn-primary" id="butt_displaysummary" style="display: none;">Timesheet</a> --><!-- ica-hyperion 21967 -->
                        <a href="#" class="btn btn-primary" id="butt_displayindividual" style="float: center; margin-right: 15px;">Individual Report</a>
                        <!-- <a href="#" class="btn btn-primary" id="butt_detailed" style="display: none;">Detailed Attendance Report</a> -->
                        <!-- <a href="#" class="btn btn-primary" id="butt_printresult" style="display: none;">Print Result</a> -->
                        <a href="#" class="btn btn-primary" id="displayAttendanceModal" style="float: center;">Print Individual (Batch)</a><!-- ica-hyperion 21630 by justin (with e) -->
                    </div>
                </div> 
              </div>
            </div>
            

  </div>
  </div>
                 <div id="displaylogs" style="padding: 5px;"></div>
  </div>
  </div>
  </div>
  <div class="modal fade" id="print_batch_attendance" role="dialog" data-backdrop="static">
      <div class="modal-dialog modal-md">
          <div class="modal-content">
              <div class="modal-header">
                  <div class="media">
                      <div class="media-left">
                          <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                      </div>
                      <div class="media-body" style="font-weight: bold;padding-top: 10px; font-family: Avenir;">
                          <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                      </div>
                  </div>
                  <center><b><h3 tag="title" class="modal-title">Print Attendance</h3></b></center>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div tag='display'>
                            <div class="col-md-2" style="text-align: right;" id="pdf-alert" class="hide">
                                <label >FORMAT</label>
                            </div> 
                            <div class="col-md-10">
                                <select class="chosen attendanceReport">
                                    <option value="butt_print_att" id="butt_print_att" name="attendancePDF">PDF</option>
                                    <!-- <option value="" id="attendanceExcel" name="attendanceExcel">EXCEL</option> -->
                                </select>
                            </div> 
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-success" id='GenerateReport'>Generate</button>
                  <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
              </div>
          </div>
      </div>
  </div>
  <form id="attFrm">
    <input type="hidden" name="form">
    <input type="hidden" name="datesetfrom">
    <input type="hidden" name="datesetto">
    <input type="hidden" name="fv">
    <input type="hidden" name="deptid">
    <input type="hidden" name="officeid">
    <input type="hidden" name="edata">
    <input type="hidden" name="tnt">
    <input type="hidden" name="estatus">
  </form>
  <script>
  var print_report = '';
  var toks = hex_sha512(" ");
  $("#displayAttendanceModal").click(function(){
      $("#print_batch_attendance").modal("toggle");
  });
  
  $("select[name='isactive']").change(function(){
    $.ajax({
          url: "<?=site_url("process_/callemployee")?>",
          type: "POST",
          data: {
             isactive :  GibberishAES.enc($("#isactive").val(), toks),
             etype :  GibberishAES.enc($("#tnt").val(), toks),
             deptid :  GibberishAES.enc($("#deptid").val(), toks),
             officeid :  GibberishAES.enc($("#officeid").val(), toks),
             toks:toks
          },
          success: function(msg) {
              $("select[name='employeeid']").html(msg).trigger('chosen:updated');
          }
      });   
  });

  $("select[name='deptid']").change(function(){
    $.ajax({
          url: "<?=site_url("process_/callemployee")?>",
          type: "POST",
          data: {
             isactive :  GibberishAES.enc($("#isactive").val(), toks),
             etype :  GibberishAES.enc($("#tnt").val(), toks),
             deptid :  GibberishAES.enc($(this).val(), toks),
             officeid :  GibberishAES.enc($("#officeid").val(), toks),
             toks:toks
          },
          success: function(msg) {
              $("select[name='employeeid']").html(msg).trigger('chosen:updated');
          }
      });   
  });

  $("select[name='officeid']").change(function(){
    $.ajax({
          url: "<?=site_url("process_/callemployee")?>",
          type: "POST",
          data: {
             isactive :  GibberishAES.enc($("#isactive").val(), toks),
             etype :  GibberishAES.enc($("#tnt").val(), toks),
             deptid :  GibberishAES.enc($("#deptid").val(), toks),
             officeid : GibberishAES.enc($(this).val(), toks),
             toks:toks
          },
          success: function(msg) {
              $("select[name='employeeid']").html(msg).trigger('chosen:updated');
          }
      });   
  });

  $("select[name='estatus']").change(function(){
      $.ajax({
          url: "<?=site_url("process_/callemployee")?>",
          type: "POST",
          data: {
             isactive :  GibberishAES.enc($("#isactive").val(), toks),
             estatus : GibberishAES.enc($(this).val(), toks),
             toks:toks
          },
          success: function(msg) {
              $("select[name='employeeid']").html(msg).trigger('chosen:updated');
          }
      });   
  });

  $("#butt_displaylogs").click(function(){
       if($("input[name='datesetfrom']").val()=="" || $("input[name='datesetto']").val()==""){
          alert("Please set a range of date first");
          return;
       }
       $("#displaylogs").html("Loading, please wait...");
       $.ajax({
          url: "<?=site_url("process_/showalllogs")?>",
          type: "POST",
          data: {
             datesetfrom: GibberishAES.enc($("input[name='datesetfrom']").val(), toks), 
             datesetto: GibberishAES.enc($("input[name='datesetto']").val(), toks),
             fv :  GibberishAES.enc($("select[name='employeeid']").val(), toks),
             deptid :  GibberishAES.enc($("select[name='deptid']").val(), toks),
             edata : GibberishAES.enc($("select[name='edata']").val(), toks),
             toks:toks
          },
          success: function(msg) {
              print_report = 'summary';
              $("#displaylogs").html(msg);
          }
      });   
    return false;  
  });

  // button for attendance summary is clicked
  $("#butt_displaysummary").click(function(){
       if($("input[name='datesetfrom']").val()=="" || $("input[name='datesetto']").val()==""){
          alert("Please set a range of date first");
          return;
       }
       /*
       //report
       var params = "?";
         params += "&datesetfrom=" + $("input[name='datesetfrom']").val(); 
         params += "&datesetto=" + $("input[name='datesetto']").val();
         params += "&fv=" + $("select[name='employeeid']").val();
         params += "&deptid=" + $("select[name='deptid']").val();
         params += "&view=reports_excel/attendance_summary_combine";
         window.open("<?=site_url("reports_/reportloader")?>"+params,"summary_combine"); 
       */
       var pmpt = confirm("Do you want to view this report?");
       if(pmpt === true){
          $("#displaylogs").html("Loading, please wait...");
           $.ajax({
              url: "<?=site_url("process_/showattendancesummary")?>",
              type: "POST",
              data: {
                 datesetfrom: $("input[name='datesetfrom']").val(), 
                 datesetto: $("input[name='datesetto']").val(),
                 fv : $("select[name='employeeid']").val(),
                 deptid : $("select[name='deptid']").val(),
                 edata : $("select[name='edata']").val(),
                 tnt  :   $("#tnt").val(),
                 estatus  :   $("#estatus").val()
              },
              success: function(msg) {
                  print_report = 'summary_combine';
                  $("#displaylogs").html(msg);
              }
          });   
      }else{
          print_report = 'summary_combine';
          //alert("You can now print Attendance Summary report..");
      }
    return false;  
  });
  /*
  $("#samplesave").click(function(){
           $.ajax({
              url: "<?=site_url("process_/saveattsummary")?>",
              type: "POST",
              data: {
                 datesetfrom: $("input[name='datesetfrom']").val(), 
                 datesetto: $("input[name='datesetto']").val(),
                 fv : $("select[name='employeeid']").val(),
                 deptid : $("select[name='deptid']").val()
              },
              success: function(msg) {
                  alert("Successfully Saved!.");
              }
          });   
  });
  */
  // button for individual report is clicked
  $("#butt_displayindividual").click(function(){
    var datesetfrom = $("input[name='datesetfrom']").val();
    var datesetto = $("input[name='datesetto']").val();
    
    if((datesetfrom == "") && (datesetto == "") || datesetfrom > datesetto){
           Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please fill a valid date',
            showConfirmButton: true,
            timer: 1000
          })
           return;

         }
         if(datesetfrom == ""){
           Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please fill a valid date',
            showConfirmButton: true,
            timer: 1000
          })
           return;

         }
         if($("select[name='employeeid']").val()==""){
           Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Employee is required',
            showConfirmButton: true,
            timer: 1000
          })
           return;

         }
         // if(datesetfrom > datesetto){
         //   Swal.fire({
         //    icon: 'warning',
         //    title: 'Warning!',
         //    text: 'Please fill a valid date',
         //    showConfirmButton: true,
         //    timer: 1000
         //  })
         //   return;

         // }

       
       // if($("select[name='employeeid']").val()==""){
       //    alert("Employee is required.");
       //    $("select[name='employeeid']").focus();
       //    return;
       // }
       $("#displaylogs").html("Loading, please wait...");
       $.ajax({
          url: "<?=site_url("process_/showallindividual")?>",
          type: "POST",
          data: {
             datesetfrom:  GibberishAES.enc($("input[name='datesetfrom']").val(), toks), 
             datesetto:  GibberishAES.enc($("input[name='datesetto']").val(), toks),
             fv :  GibberishAES.enc($("select[name='employeeid']").val(), toks),
             edata : GibberishAES.enc($("select[name='edata']").val() , toks),
             toks:toks
          },
          success: function(msg) {
              print_report = 'individual';
              $("#displaylogs").html(msg);
          }
      });   
    return false;  
  });

  // button for individual report is clicked
  $("#butt_detailed").click(function(){
       $("#displaylogs").html("Loading, please wait...");
       $.ajax({
          url: "<?=site_url("reports_/showDetailedAttendance")?>",
          type: "POST",
          data: {
             datesetfrom: $("input[name='datesetfrom']").val(), 
             datesetto: $("input[name='datesetto']").val(),
             fv : $("select[name='employeeid']").val(),
             edata : $("select[name='edata']").val()
          },
          success: function(msg) {
              $("#displaylogs").html(msg);
          }
      });   
    return false;  
  });

  $('.chosen').chosen();
  $(".date").datetimepicker({
     format: "YYYY-MM-DD"
  });
  // excel report 
  // $( "attendanceReport" ).delegate('tbody.emergencycontactlist tr td .delete_entry', 'click', function(){
  //           checkEmCon();
  //       });
  
  // $("#butt_printresult_excel").click(function(){
  //    var params = "?";
  //        params += "&datesetfrom=" + $("input[name='datesetfrom']").val(); 
  //        params += "&datesetto=" + $("input[name='datesetto']").val();
  //        params += "&fv=" + $("select[name='employeeid']").val();
  //        params += "&deptid=" + $("select[name='deptid']").val();
  //        params += "&edata=" + $("select[name='edata']").val();       
  //    switch(print_report){
  //       case "summary":
  //            params += "&view=reports_excel/attendance_summary"; 
  //            window.open("<?=site_url("reports_/reportloader")?>"+params,"summary"); 
  //       break;
  //       case "individual":
  //            params += "&view=reports_excel/attendance_individual";
  //            window.open("<?=site_url("reports_/reportloader")?>"+params,"individual"); 
  //       break;
  //       case "summary_combine":
  //            params += "&view=reports_excel/attendance_summary_combine";
  //            //window.open("<?=site_url("reports_/reportloader")?>"+params,"summary_combine");
  //            window.open("<?=site_url("forms/loadForm")?>"+params,"summary_combine");
  //       break;
  //       case "tardiness_for_payroll":
  //            params += "&view=reports_excel/tardiness_for_payroll";
  //            window.open("<?=site_url("reports_/reportloader")?>"+params,"tardiness_for_payroll"); 
  //       break;
  //       default:
  //            alert("Please select a type of report first.");
  //            return;
  //       break;
  //    } 
  //    return false;
  // });


  // pdf report
  $("#butt_printresult").click(function(){
     var params = "?";
         params += "form="+print_report;
         params += "&datesetfrom=" + $("input[name='datesetfrom']").val(); 
         params += "&datesetto=" + $("input[name='datesetto']").val();
         params += "&fv=" + $("select[name='employeeid']").val();
         params += "&deptid=" + $("select[name='deptid']").val();
         params += "&edata=" + $("select[name='edata']").val();       
         params += "&tnt=" + $("#tnt").val();
         params += "&estatus=" + $("#estatus").val();
     switch(print_report){
        case "summary":
             params += "&view=reports_excel/attendance_summary"; 
             window.open("<?=site_url("reports_/reportloader")?>"+params,"summary"); 
        break;
        case "individual":
             params += "&view=process/reports_pdf/individual_attendance";
             // window.open("<?=site_url("reports_/reportloader")?>"+params,"individual_attendance"); ///< OLD REPORT
             window.open("<?=site_url("attendance_/loadAttendanceReport")?>"+params,"individual_attendance"); ///< NEW TESTING
             // alert("No report available."); 
        break;
        case "summary_combine":
             window.open("<?=site_url("forms/loadForm")?>"+params,"summary_combine");
        break;
        case "tardiness_for_payroll":
             params += "&view=reports_excel/tardiness_for_payroll";
             window.open("<?=site_url("reports_/reportloader")?>"+params,"tardiness_for_payroll"); 
        break;
        default:
             alert("Please select a type of report first.");
             return;
        break;
     } 
     return false;
  });

  $("#GenerateReport").click(function(){
      $("input[name='form']").val(print_report);
      $("input[name='datesetfrom']").val($("input[name='datesetfrom']").val());
      $("input[name='datesetto']").val($("input[name='datesetto']").val());
      $("input[name='fv']").val($("select[name='employeeid']").val());
      $("input[name='deptid']").val($("select[name='deptid']").val());
      $("input[name='officeid']").val($("select[name='officeid']").val());
      $("input[name='edata']").val($("select[name='edata']").val());
      $("input[name='tnt']").val($("#tnt").val());
      $("input[name='estatus']").val($("#estatus").val());
      $("input[name='isactive']").val($("#isactive").val());

      $("#attFrm").attr("action", "<?=site_url('attendance_/loadAttendanceReport')?>");
      $("#attFrm").attr("target", "_blank");
      $("#attFrm").attr("method", "post");
      $("#attFrm").submit();
      return false;
  });

  $("#butt_pdf").click(function(){
     var params = "?form=pdftest";
       window.open("<?=site_url("forms/loadForm")?>"+params,"pdftest");
  });

  $("select[name='whatForm']").change(function(){
    var formtype = $("select[name='whatForm']").find("option:selected").val();
    if (formtype != "NONE") {
      var formParams = "?form=" +  formtype;
      window.open("<?=site_url("forms/loadForm")?>"+formParams,formtype);
    }
    //$("select[name='whatForm']").find("option:selected").val() = "NONE";
  });

  $( "#upload" ).submit(function( event ) {
    if ( $('#file').get(0).files.length === 0 ) {
      $( "#msg" ).text( "No files selected.." ).show();
      return false;
    }
  });

  $("#estat").hide();
  $("#tnt").change(function(){
      loadempopt($(this).val());
  });
  function loadempopt(etype = ""){
      $.ajax({
          url: "<?=site_url("process_/callemployee")?>",
          type: "POST",
          data: {
             isactive :  GibberishAES.enc($("#isactive").val(), toks),
             etype :  GibberishAES.enc(etype, toks), 
             deptid : GibberishAES.enc($("#deptid").val() , toks),
             officeid :  GibberishAES.enc($("#officeid").val(), toks),
             toks:toks
          },
          success: function(msg) {
              $("select[name='employeeid']").html(msg).trigger('chosen:updated');
          }
      });   
  }

  //Addedd 6-3-2017 LACKING OF IN/OUT
  $("#butt_displayLackInOut").click(function(){
       $("#displaylogs").html("Loading, please wait...");
       $.ajax({
          url: "<?=site_url("process_/showLackInOut")?>",
          type: "POST",
  		    data: {type : GibberishAES.enc("ABSENT", toks), toks:toks},
          success: function(msg) {
              $("#displaylogs").html(msg);
          }
      });   
    return false;  
  });

  </script>