<?
#Modified and Created by Glen Mark 2017
$toks = $this->input->post('toks');
$date =  $this->gibberish->decrypt( $this->input->post('date'), $toks );
$campus = $this->gibberish->decrypt( $this->input->post('campus'), $toks );
$cluster = $this->gibberish->decrypt( $this->input->post('cluster'), $toks );
$deptid = $this->gibberish->decrypt( $this->input->post('department'), $toks );
$etype = $this->gibberish->decrypt( $this->input->post('etype'), $toks );
$status = $this->gibberish->decrypt( $this->input->post('status'), $toks );
$office = $this->gibberish->decrypt( $this->input->post('office'), $toks );

$no=1;

?>

<style type="text/css">
#employeelist .timetrail td
{
  background-color: #C8C8C8;
}
</style>

      <!-- <a class="btn blue pull-right" href="#modal-view" data-toggle='modal' href="#" id="EncodeByBatch"><i class="glyphicon glyphicon-plus-sign"></i> Encode by Batch</a> -->
    

<table class="table table-striped table-bordered table-hover" id="tabless">
    <thead style="background-color: #0072c6;">
        <tr>
            <!-- <th class="col-md-1">#</th> -->
            <th class="sorting_asc">Employee ID</th>
            <th>Fullname</th>
            <th>Time In</th>
            <th>Time Out</th>
            <!-- <th></th> -->
        </tr>
    </thead>
    <tbody id="employeelist">
                                                      
              <?
              $datas = $datatimesheet =  array();
              $employee = $this->employee->loademployeeforadjustment($date,$campus,$cluster,$deptid,$etype, $status, $office);
              // echo "<pre>"; print_r($this->db->last_query()); die;
              foreach ($employee as $info) {
                   $timesheetdata = $this->employee->loadtimesheet($date,$campus,$cluster,$info->employeeid);
                   // echo "<pre>";print_r($this->db->last_query());
                   $timein = $timeout= $timeinonly= "";
                   ?>
              <?
              if(count($timesheetdata) > 0){
                $timeid = "";
                $timeinonly = "";
                $timein = "";
                $timeout = "";
                
                $count = 0;
              foreach ($timesheetdata as $value) 
                {        
                    $alreadyexist = false;           
                    $eid = $value->employeeid;
                    $timein = $value->timein && $value->timein != '0000-00-00 00:00:00'?$value->timein:"";
                    $timeout = $value->timeout && $value->timeout != '0000-00-00 00:00:00' ?$value->timeout:"";
                    $timeinonly = $this->employee->loadtimeonly($date,$campus,$cluster,$value->employeeid);
                   
                    if ($timeinonly == $timein) 
                    {
                      $alreadyexist = true;
                    }

                    $time = DATE('h:i',strtotime($timeinonly));
                    $existingTimein = $this->employee->timesheetrailExist($time,$date,$eid);
                                   
                    ?>

                    <?php if ($eid == $info->employeeid): ?>
                    <?
                      $count++;
                    ?>
                       
                        <?php if (($timein != "") && ($timeout != "" ) ): ?>
                            <tr tag='empdata' employeeid='<?=$info->employeeid?>' time="<?=$value->timeid?>">
                              <td class='id' name='employeeid' value='<?=$info->employeeid?>'><?=$info->employeeid?></td>   
                              <td><?=$info->lname.",".$info->fname?></td> 
                              <td >
                                <input type="hidden" name="tag" value='<?=$timeid?>'>
                                  <div class='input-group time'>
                                      <input type='text' class="form-control" id="u_timein" name="timein" type="text" employeeid='<?=$info->employeeid?>' value='<?=$timein?date("h:i:s A",strtotime($timein)):""?>'/>
                                      <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                      </span>
                                  </div>
                              </td>  
                              <td>
                                  <div class='input-group time'>
                                      <input type='text' class="form-control" id="u_timeout" name="timeout" type="text" employeeid='<?=$info->employeeid?>' value="<?=$timeout?date("h:i:s A",strtotime($timeout)):""?>"/>
                                      <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                      </span>
                                  </div>
                                <a  class="adddate" href="#" class="btn btn-default pull-center" id='<?=$info->employeeid?>'  empname='<?=$info->lname.",".$info->fname?>'><i class='glyphicon glyphicon-plus'>
                              </td>
                            </tr>

                        <?php if ($timeinonly != "" && $count == 1  && !$existingTimein): ?>

                              <tr tag='empdata' class='timetrail'  employeeid="<?=$info->employeeid?>"  >
                                <td class='id' name='employeeid' value='<?=$info->employeeid?>'><?=$info->employeeid?></td>   
                                <td><?=$info->lname.",".$info->fname?> </td> 
                                  <td>
                                    <div class='input-group time'>
                                      <input type='text' class="form-control" id="u_timein" name="timein" type="text" employeeid='<?=$info->employeeid?>' value='<?=$timeinonly?date("h:i:s A",strtotime($timeinonly)):""?>'/>
                                      <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                      </span>
                                    </div>
                                  </td>  
                                  <td>
                                    <div class='input-group time'>
                                      <input type='text' class="form-control" id="u_timeout" name="timeout" type="text" employeeid='<?=$info->employeeid?>' value=''/>
                                      <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-time"></span>
                                      </span>
                                    </div>
                                    <!-- <a id="adddate" href="#" class="btn btn-default pull-center" tag='<?=$info->employeeid?>' ><i class='glyphicon glyphicon-plus'  > -->
                                  </td>
                            </tr>

                        <?php endif ?>
                        <?php else: ?>

                               <tr tag='empdata' class='timetrail'  employeeid="<?=$info->employeeid?>"  >
                              <td class='id' name='employeeid' value='<?=$info->employeeid?>'><?=$info->employeeid?></td>   
                              <td><?=$info->lname.",".$info->fname?></td> 
                              <td>
                                <div class='input-group time'>
                                    <input type='text' class="form-control" id="u_timein" name="timein" type="text" employeeid='<?=$info->employeeid?>' value='<?=$timeinonly?date("h:i:s A",strtotime($timeinonly)):""?>'/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                              </td>  
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control" id="u_timeout" name="timeout" type="text" employeeid='<?=$info->employeeid?>' value=''/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                </div>
                              </td>
                            </tr>

                        <?php endif ?>
                      
                    <?php endif ?>
                <?}

              }  else {?>
                  <tr tag='empdata' employeeid='<?=$info->employeeid?>' value="<?=$info->employeeid?>">
                  <td class='id' name='employeeid' value='<?=$info->employeeid?>'><?=$info->employeeid?></td>   
                  <td><?=$info->lname.",".$info->fname?></td> 
                  <td>
                    <div class='input-group time'>
                      <input type='text' class="form-control" id="u_timein" name="timein" type="text" employeeid='<?=$info->employeeid?>' value='<?=$timein?date("h:i:s A",strtotime($timein)):""?>'/>
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
                      </span>
                    </div>
                  </td>  
                  <td>
                    <div class='input-group time'>
                      <input type='text' class="form-control" id="u_timeout" name="timeout" type="text" employeeid='<?=$info->employeeid?>' value="<?=$timeout?date("h:i:s A",strtotime($timeout)):""?>"/>
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-time"></span>
                      </span>
                    </div>
                    <a  class="adddate" href="#" class="btn btn-default pull-center" tag='<?=$info->employeeid?>' id='<?=$info->employeeid?>'  empname='<?=$info->lname.",".$info->fname?>' ><i class='glyphicon glyphicon-plus'  >
                  </td>         

              <? } ?>
              
            <? } ?>
             
    </tbody>
    
</table>
<div class="col-md-12">
  <div class="form_row">
      <div class="col-md-8">
        <label class="field_name align_right col-md-12" style="float: right;">Remarks</label>
      </div>
      <div class="field col-md-4" style="margin: 0px; padding-right: 9px; padding-left: 0px; ">
        <div class="col-md-12" style="padding: 0px;">
          <select class="chosen col-md-12" id="remark" name="remark">
              <option value="">Select Remark</option>
              <?=$this->extras->showrequesttype()?>
          </select>
        </div>
      </div>
  </div>
</div>

<script type="text/javascript">
var toks = hex_sha512(" "); 

$(document).ready(function()
{
    $('#u_timeout, #u_timein, .timein').datetimepicker({
        format: "LT"
    });

    $("#datePicker").datetimepicker(
    {
      autoclose:true
    });

    var table = $('#tabless').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );

});


$( '#tabless' ).delegate( ".adddate", "click", function() {
  var tr = $(this).closest('tr');
  var id = $(this).attr("id");
  var name = $(this).attr("empname");
  $markup = $("<tr tag='empdata' employeeid='"+id+"'><td class='id' name='employeeid' value='"+id+"'>"+id+"</td><td>"+name+"</td><td><div class='input-group time'><input name='timein' class='form-control' type='text' employeeid='"+id+"' value=''/><span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span></div></td><td><div class='input-group time'><input name='timeout' class='form-control' type='text' employeeid='"+id+"' value='' /><span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span></div><a  class='adddate' href='#' class='btn btn-default pull-center' tag='"+id+"' id='"+id+"'  empname='"+name+"' ><i class='glyphicon glyphicon-plus'  ></td></tr>");
  tr.after($markup);
  $markup.find('.time').datetimepicker({
        format: "LT"
  });
   // $("table tbody").after(markup);
})


// $('.chosen').chosen();
var arraydata = [];

// $("tr[tag='empdata']").find("input[name='timein']").unbind('infoup').blur(function()
// {
//   if ($(this).val() != "") {
//     arraydata[$(this).val()] = $(this).attr('employeeid'); 
//     arraydata.push($(this).val() + "|" + $(this).attr('employeeid'));
//     console.log(arraydata);
//   }
// });

$(document).unbind('click').on('click',".grey",function()
{
  location.reload();
});

$(document).bind('click').on("click","#savedata",function()
{

  if($("#remark").val() == ''){
      Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Remark is required!',
            showConfirmButton: true,
            timer: 1000
        })
      return false;
    }
  var iscontinue = true;
  var timeout = counter = empCounter = timeCounter = 0;
  var data = time1 = time2 = aDate = bDate = empid = timein1 = timein2 = date1 = date2 = '';
  var parse = "~u~";
  $("#tabless").find("tr[tag='empdata']").each(function()
  {
    // alert($(this).attr('employeeid'));
        time1 = $("#date").val() + " " +  $(this).find("input[name='timein']").val();
        time2 = $("#date").val() + " " +  $(this).find("input[name='timeout']").val();
        if($(this).find("input[name='timein']").val() != '' && $(this).find("input[name='timeout']").val() != '' ) {
           aDate = new Date(time1).getTime();
           bDate = new Date(time2).getTime();
           if(aDate >= bDate){
            $(this).find("input[name='timeout']").css("border", "1px solid red");  
            iscontinue = false;
           }else{
            if(empid != $(this).attr("employeeid") && counter != 0){
                $(this).find("input[name='timeout']").css("border", "1px solid #ccc");
                empCounter = 0;
            }else{
              timeCounter = 0;
              if(empCounter == 0){
                $("#tabless").find("tr[employeeid='"+empid+"']").each(function(){
                  // empCounter++;
                    timein1 = $("#date").val() + " " +  $(this).find("input[name='timein']").val();
                    timein2 = $("#date").val() + " " +  $(this).find("input[name='timeout']").val();
                    date1 = new Date(timein1).getTime();
                    date2 = new Date(timein2).getTime();
                    if(aDate == date1 && bDate == date2) timeCounter++;
                    if((aDate > date1 && aDate < date2) || (bDate > date1 && bDate < date2) || timeCounter > 1){
                        $(this).find("input[name='timein']").css("border", "1px solid red"); 
                        $(this).find("input[name='timeout']").css("border", "1px solid red"); 
                        iscontinue = false;
                    }else{
                        $(this).find("input[name='timein']").css("border", "1px solid #ccc"); 
                        $(this).find("input[name='timeout']").css("border", "1px solid #ccc"); 
                    }
                })
              }
            }
           }
        }else{
          $(this).find("input[name='timein']").val('');
          $(this).find("input[name='timeout']").val('');
        }

        if($(this).find("input[name='timein']").val() != "" && $(this).find("input[name='timeout']").val() != ""){
            data += data ? "|":"";
            data += GibberishAES.enc($(this).attr('employeeid'), toks);
            data += parse;
            data += GibberishAES.enc($(this).find("input[name='timein']").val(), toks);
            data += parse;
            data += GibberishAES.enc($(this).find("input[name='timeout']").val(), toks);
            data += parse;
            data += GibberishAES.enc($("#date").val(), toks);
            data += parse;
            // data += $("input[name='tag']").val();
            data += GibberishAES.enc($(this).attr("time"), toks);
            data += parse;
            data += GibberishAES.enc($("#remark").val(), toks);

        }
          
        empid = $(this).attr('employeeid');
        counter++;
  });
  // console.log(data); return;
  if(iscontinue){
      $(".savedata").css("pointer-events", "none");
      $.ajax({
        url:"<?=site_url("process_/batchApprovalDTR")?>",
        type:"POST",
        data: {data:data, toks:toks},
        success:function(msg)
        {
          if(msg=='Failed to saved data!'){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: msg,
                showConfirmButton: true,
                timer: 1000
            })
          }else{
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Time In and Time Out has been saved successfully',
                showConfirmButton: true,
                timer: 1000
            })
          }
          setTimeout(function(){
            $(".grey").click();
             $(".savedata").hide();
             location.reload();
           }, 1000)
             
        }
      });
    }else{
      Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Invalid Time In or Time Out',
                showConfirmButton: true,
                timer: 1000
            })
    }
}
);
$(".modalclose").click(function(){
  location.reload();
})

$('.chosen').chosen();


</script>