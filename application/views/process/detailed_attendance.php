    <?php
    /**
     * @author Max Consul
     * @copyright 2018
     */
    list($from_date, $to_date) = explode(",", $cdate);
    $futureDate = mktime(0, 0, 0, date("m")+30, date("d"), date("Y"));

    ?>
    <div class="panel">
        <div class="panel-heading"><h4><b>Detailed Report</b></h4></div>
        <div class="panel-body">
            <div class="form_row">
                <label class="field_name align_right">Select Category</label>
                <div class="col-md-6">
                    <select class="chosen col-md-4" id="category_selected" name="category_selected">
                        <option value="">All Category</option>
                        <option value="overtime">Overtime</option>
                        <option value="absents">Absences</option>
                        <option value="lateut">Tardiness and Undertime</option>
                        <option value="att_adj">Attendance Adjustment Report</option>
                        <option value="att_terminal">Terminal</option>
                    </select>
                </div>
                <div class="col-md-2"><a href="#" class="btn btn-primary" id="generate_attendance">Detailed Attendance Report</a></div>
            </div>  
            <div class="form_row for_terminal">
                <label class="field_name align_right">Select Terminal</label>
                <div class="col-md-6">
                    <select class="chosen col-md-4" id="terminal" name="terminal">
                        <?=$this->extensions->getTerminals()?>
                    </select>
                </div>
            </div>
            <div class="form_row for_terminal">
                <label class="field_name align_right">Select Logs</label>
                <div class="col-md-6">
                    <select class="chosen col-md-4" id="logs" name="logs">
                        <option value="IN/OUT">IN/OUT</option>
                        <option value="IN">IN</option>
                        <option value="OUT">OUT</option>
                    </select>
                </div>
            </div>
            <div class="form_row">
                <div class="col-md-2" style="margin-left: 283px;width: auto;">
                    &nbsp;<input type="checkbox" id="specific" class="double-sized-cb"> &nbsp; <b>Check if specific range</b>
                </div>
                <div class="col-md-6 hide_div" style="padding-left: 0px;">
                    <div class="col-md-2" style="font-size: 17px;width: auto;padding-right: 0px; padding-left: 0px"><label>From</label></div>
                        <div class="col-md-4">
                            <div class='input-group date'  data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control" name="datefrom" value="<?=$from_date?>"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1" style="width: auto;padding-left: 0px; padding-right: 0px"><b>To</b></div>
                    <div class="col-md-4">
                        <div class='input-group date' data-date="<?=$to_date?>" data-date-format="yyyy-mm-dd">
                            <input type='text' class="form-control" name="dateto" value="<?=$to_date?>" />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
<!--  <label  / id="dater"><?echo(date("F d, Y "));?></label><br>
      <table class="table table-striped table-bordered table-hover" id="tables" >
                          <thead style="background-color: #0072c6;">
                              <tr>
                                  <th>Employee ID</th>
                                  <th>Fullname</th>
                                  <th>Schedule Date</th>
                                  <th>Hours</th>
                              </tr>
                          </thead>
                          </table> -->
            <!-- <div class="col-md-2" id="generate"><a href="#" class="btn btn-primary">Generate</a></div> -->

            <div id="main_table"></div>
        </div>
    </div>

    <script>
      var toks = hex_sha512(" ");
      $(".for_terminal").hide();
            $(".hide_div").hide();
        $("#specific").change(function(){
            if(this.checked) $(".hide_div").show();
            else $(".hide_div").hide();
        });

          $("#generate").hide();
          $("#dater").hide();
          $('#tables').hide();
        $("#generate_attendance").click(function(){
          $("#dater").show();
          $("#generate").show();
          $("#tables").show();
        });



    	  $("#generate_attendance").click(function(){
          $('#loading').removeAttr('hidden');
          var cutoff = $("#cutoff").val();
          var office = $("#office").val();
          var deptid = $("#deptid").val();
          var cutoff_arr = cutoff.split(',');
          var datesetfrom = '',
          datesetto = '';
          var category_selected = $("#category_selected").val();
          if(cutoff_arr != ''){
              $('#cutoffMsg').html('');
              datesetfrom = cutoff_arr[0];
              datesetto = cutoff_arr[1];
          }else{
              $('#cutoffMsg').html('Please select cutoff.');
              return;
          }
          if(!category_selected){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Choose Category',
                showConfirmButton: true,
                timer: 1000
            })
          	return;
          }

          if($("#specific").prop("checked")){
              datesetfrom = $("input[name='datefrom']").val();
              datesetto = $("input[name='dateto']").val();
          }
          if (category_selected == "att_terminal") {
            $("#tables").hide();
          }else{
            $("#tables").show();
          }
          
          $.ajax({
            url: "<?=site_url("reports_/showDetailedAttendanceReport")?>",
            type: "POST",
            data: {
               datesetfrom:  GibberishAES.enc(datesetfrom , toks), 
               datesetto:  GibberishAES.enc(datesetto , toks),
               deptid: GibberishAES.enc(deptid , toks),
               office: GibberishAES.enc(office , toks),
               fv :  GibberishAES.enc($('select[name=employeeid]').val() , toks),
               category_selected :  GibberishAES.enc( category_selected, toks),
               terminal :  GibberishAES.enc($("#terminal").val(), toks),
               logs :  GibberishAES.enc( $("#logs").val(), toks),
               gate :  GibberishAES.enc($("#terminal option:selected").attr('gate'), toks),
               toks:toks
            },
            success: function(msg) {
                $("#main_table").html(msg);
            }
          });   
      return false;  
    });

    $("#category_selected").change(function(){
      if($(this).val() == 'att_terminal') $(".for_terminal").show();
      else $(".for_terminal").hide();
    })


    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $(".chosen").chosen();
    </script>
