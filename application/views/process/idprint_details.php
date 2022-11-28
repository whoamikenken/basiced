<?php

if($ltype=="S"){

$yrs = array();

$deptarray = array(
  '' => "All Department",
  'N' => "NURSERY",
  'K' => "KINDERGARTEN",
  'GS' => "ELEMENTARY",
  'HS' => "HIGH SCHOOL (K12)",
  'HSOLD' => "HIGH SCHOOL (OLD)",
);

switch ($dept) {
  case 'N':
    $yrs = array(
      "" => "All year level",
      '1' => "Nursery"
    );
  break;
  case 'K':
    $yrs = array(
      "" => "All year level",
      '1' => "Kinder 1",
      '2' => "Kinder 2",
    );
    break;
  case 'GS':
    #$yrs = $this->extras->showYearLevel($section, $dept);
    $yrs = array(
      '' => "All Year Level",
      '1' => "Grade 1",
      '2' => "Grade 2",
      '3' => "Grade 3",
      '4' => "Grade 4",
      '5' => "Grade 5",
      '6' => "Grade 6",
    );
    break;
  case 'HS':
    $yrs = array(
      '' => "All Year Level",
      '7' => "Grade 7",
      '8' => "Grade 8",
      '9' => "Grade 9",
      '10' => "Grade 10",
      '11' => "Grade 11",
      '12' => "Grade 12",
    );
    break;
  case 'HSOLD':
    $yrs = array(
      '' => "All Year Level",
      '1' => "First Year",
      '2' => "Second Year",
      '3' => "Third Year",
      '4' => "Fourth Year",
    );
    break;
  default:
    $yrs = array("" => "All year level");
  break;
}

$schlyr = $this->extras->listSchoolYears();

$sems = array(
  'A' => "First",
  'B' => "Second",  
  'C' => "Summer",
);

?>
<form id="hoy">
<div class="form_row">
    <label class="field_name align_right">School Year</label>
    <div class="field no-search">
        <div class="col-md-12">
            <select id="sy" name="sy" class="chosen col-md-4">
              <?php
                $sy1 = "";
                foreach ($schlyr as $key => $value) {
                  if ($sy1 == "") {
                    $sy1 = $value['SY'];
                  }
                  ?>
                  <option 
                    <?php print("value=\"{$value['SY']}\""); 
                        print(($sy == $value['SY']) ? "selected=\"true\"" : ""); 
                    ?>><?php print($value['SY']); ?></option>
                  <?php
                }
              ?>
            </select>
            <div class="align_right" style="float: right;"><b><a href="javascript:loadxls()"><i class="icon-print"> Printer-Friendly</i></a></b></div>
        </div>
    </div>
</div>

<div class="form_row">
    <label class="field_name align_right">Semester</label>
    <div class="field no-search">
        <div class="col-md-12">
            <select id="sem" name="sem" class="chosen col-md-4">
              <?php
                $sem1 = "";
                foreach ($sems as $key => $value) {
                  if ($sem1 == "") {
                    $sem1 = $key;
                  }
                  ?>
                    <option 
                      <?php 
                        print("value=\"{$key}\""); 
                        print(($key == $sem) ? "selected=\"true\"" : "");
                      ?>><?php print($value); ?></option>
                  <?php
                } // end foreach
              ?>
            </select>
            <div class="align_right" style="float: right;"><b><a href="#" id="removeid"><i class="glyphicon glyphicon-remove-sign-sign"> Remove All ID</i></a></b></div>
        </div>
    </div>
</div>

<div class="form_row">
    <label class="field_name align_right">Department</label>
    <div class="field">
        <div class="col-md-12">
            <select id="dept" name="dept" class="chosen col-md-4">
             <?=$this->extras->showStudentDepartmentType($depts='',$dept);?> 
             <!-- <?php
              foreach ($deptarray as $key => $value) {
              ?>
                <option value="<?php print($key); ?>" <?php print(($key == $dept) ? "selected=\"true\"": ""); ?>><?php print($value); ?></option>              <?php 
              }
            ?>  -->
            </select>
        </div>
    </div>
</div>

<div class="form_row">
    <label class="field_name align_right">Year Level</label>
    <div class="field no-search">
        <div class="col-md-12">
            <select class="chosen col-md-4" id="yearlevelid" name="yearlevelid">
               <?=$this->extras->showStudentYL($yearlevel,$dept);?> 
           <!--   <?php
              foreach ($yrs as $key => $value) {
              ?>
                <option value="<?php print($key); ?>" <?php print(($key == $yearlevel) ? "selected=\"true\"": ""); ?>><?=$value?></option>
              <?php
              } // end foreach
            ?>  -->
            </select>
        </div>
    </div>
</div>

<div class="form_row">
    <label class="field_name align_right">Section</label>
    <div class="field">
        <div class="col-md-12">
            <select class="chosen col-md-4" id="sectionid" name="sectionid">
            <?=$this->extras->showStudentSection($section,$dept,$sy,$sem,$yearlevel);?>
            <!-- <?
              $opt_type = $this->extras->showSection($yearlevel, $dept);
              foreach($opt_type as $key=>$val){
              ?><option value="<?=$key?>"
                <?php print($key==$section?" selected":""); ?>>
                <?php print((($key != "") ?  "Section " : "").($val)); ?></option>
            <?php
              } // end foreach
            ?> -->
            </select>
            
        </div>
    </div>
</div>    
</form>
<?    
}else{
  $empdept = $departmentid;
  ?>
  <form>
    <div class="form_row">
        <label class="field_name align_right">Departments</label>
        <div class="field">
            <div class="col-md-12">
                <select class="chosen col-md-5" id="departmentid" name="departmentid">
                <?php
                  $arrayDept = $this->extras->showdepartment("All Departments");
                  foreach ($arrayDept as $key => $item) {
                    ?>
                    <option <?php print("value=\"{$key}\""); print(($empdept == $key) ? "selected=\"true\"" : ""); ?>><?php print($item); ?></option>
                    <?php
                  }
                ?>
                </select>
                <div class="align_right" style="float: right;"><b><a href="javascript:loadxls()"><i class="icon-print"> Printer-Friendly</i></a></b></div>
                <div class="align_right"><b><a href="#" id="removeid"><i class="glyphicon glyphicon-remove-sign-sign"> Remove All ID</i></a></b></div>
            </div>
        </div>
    </div>    
  </form>
  <?php
} // end else
?>


<style type="text/css">
    #message {
      /*background-color: #E0E0E0;*/
      /*-webkit-box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);
      -moz-box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);
      box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);*/

      -webkit-box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);
      -moz-box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);
      box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);

      padding: 5px;
      border-radius: 3px;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1;
      font-weight: bold;
      color: #FFFFFF;
      font-size: 16px;
    background-color:black ;
    }

    .hidden{
      visibility: hidden;
    }
</style>


<div id="message" class="hidden" >
  <span id="msgEIDtext"></span>
  <span id="msgEID" style="font-weight: bold;"></span>
  <span id="msgCnumtext"></span>
  <span id="msgCnum" style="font-weight: bold;"></span>
  <span id="msgEnd"></span>
</div>

<table id="dt_listcardsetup" class="table table-striped table-bordered table-hover datatable">
<thead>
    <tr>
        <th class="col-md-1">#</th>
        <th class="sorting_asc">Code</th>
        <th>Fullname</th>
        <th>ID #</th>
    </tr>
</thead>
<tbody>                  
<?
switch ($ltype){
    case "S":
      $o = 1;
      $where = "";

      if ($sy) {
        $where .= " WHERE a.SY='{$sy}'";
      }else{
        $where .= " WHERE a.SY='{$sy1}'";
      } //end else

      if ($sem) {
        $where .= " AND a.Sem='{$sem}'";
      }else{
        $where .= " AND a.Sem='{$sem1}'";
      } // end else
            
        if($yearlevel) $where .= " AND a.yearlevel='{$yearlevel}'";
        if($section) $where .= " AND a.section='{$section}'";
        if($dept) $where .= " AND a.depttype='{$dept}'";
        
        $student = $this->db->query("SELECT SQL_CALC_FOUND_ROWS
  a.studentid,  
  a.studentcode,
  a.yearlevel,
  a.section,
  a.coursecode,
  a.depttype,
  CONCAT (a.lname, ',', a.fname,' ',a.lname) AS fullname
FROM student AS a {$where}
GROUP BY a.studentid
ORDER BY a.lname ASC, a.fname ASC, a.mname ASC")->result();
        // echo "<br>".$sy;
        // echo "<br>".$sem;
        // echo "<br>".$dept;
        // echo "<br>".$yearlevel;
        // echo "<br>".$section;
        foreach($student as $row){
        ?>
            <tr studentno='<?=$row->StudNo?>' style="cursor: pointer;">
              <td><?=$o?></td>
              <td><?=$row->studentid?></td>
              <td><?=$this->extras->htmlchangeenye($row->fullname)?></td> 
              <td><label style="display: none;"><?=$row->studentid?></label><div class="field"><input name="bcode" type="text" class="text-center col-md-12" encodetype='S' encode='<?=$row->studentid?>' value='<?=$row->studentcode?>'/></div></td>
            </tr>
        <?  
          $o++;  
        } // end foreach
    break;
    default:
      $o = 1;
      $empwhere = "";
      if ($empdept != "") {
        $empwhere = " WHERE a.deptid = \'{$empdept}\' ";
      }
      $que = "CALL prc_employee_card_get(@a,@b,' {$empwhere} ')";
      $employee = $this->db->query($que)->result();
      foreach($employee as $row){
      ?>
          <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">
            <td><?=$o?></td>
            <td><?=$row->employeeid?></td>
            <td><?=$this->extras->htmlchangeenye($row->fullname)?></td>
            <td><label style="display: none;"><?=$row->employeecode?></label><div class="field"><input name="bcode" type="text" class="text-center col-md-12" encodetype='E' encode='<?=$row->employeeid?>' value='<?=$row->employeecode?>'/></div></td>
          </tr>
      <?  
        $o++;  
      }  
    break;
}
?>
</tbody>
</table>
<script>
$(function(){
    $('#dt_listcardsetup').dataTable({
        "sPaginationType": "bootstrap",
        "sDom": "<'tableHeader'<l><'clearfix'f>r>t<'tableFooter'<i><'clearfix'p>>",
          "iDisplayLength": 15,
          "aLengthMenu": [[15, 30, 50, -1], [15, 30, 50, "All"]],
          "aoColumnDefs": [{
              'bSortable': false,
              'aTargets': [0]
          }],

          "fnDrawCallback": function(){
                    $("input[name='bcode']").unbind('keyup').keyup(function(e){
                       if(e.keyCode==13){

                          var obj = $(this); 
                          var newcode = obj.val().replace('-','');
                              obj.val(newcode);
                          var eid = obj.attr("encode");
                          var entype = obj.attr("encodetype");
                         
                          $.ajax({
                            url: "<?=site_url("process_/savecardid")?>",
                            type:"POST",
                            data:{
                                newcode:newcode,
                                eid:eid,
                                entype:entype
                            },
                            success:function(msg){
                                // $("#message").text("Card Number : "+newcode+" is successfully saved!");
                                // alert(msg);
                                if (msg == "EXIST") 
                                {
                                    $('#message').removeClass('hidden');
                                    $('#message').hide();
                                    $('#message').show();
                                    $('#msgEIDtext').text("Employee Number : ");
                                    $('#msgEID').text(eid);
                                    $('#msgCnumtext').text(" with Card Number : ");
                                    $('#msgCnum').text(newcode + " is ");
                                    $('#msgEnd').text("already exist!");
                                    $('#msgEnd').css("color","red");
                                    $('#message').delay(2000).fadeOut();
                                    obj.val("");
                                    // $("input[name='bcode']").eq(obj.index("input[name='bcode']")+1).focus(); 
                                    // location.reload();
                                   
                                }
                                else
                                {
                                    $('#message').removeClass('hidden');
                                    $('#message').hide();
                                    $('#message').show();
                                    $('#msgEIDtext').text("Employee Number : ");
                                    $('#msgEID').text(eid);
                                    $('#msgCnumtext').text(" with Card Number : ");
                                    $('#msgCnum').text(newcode);
                                    $('#msgEnd').text(" is successfully saved!");
                                    $('#msgEnd').css("color","white");
                                    $('#message').delay(2000).fadeOut();
                                    $("input[name='bcode']").eq(obj.index("input[name='bcode']")+1).focus(); 
                                    
                                }
                            }
                          });
                       } 
                    }); 
          }

    });

    $('.no-search .dataTables_length select').chosen();
    $("input[name='bcode']").click(function(){
       $(this).select(); 
       return false;
    });
    
    $("#sy,#sem,#departmentid,#dept,#sectionid,#yearlevelid").change(function(){
      // var data = $("#hoy").serialize();
      // alert($("#dept").val());
      loadlisttodisplay(
        $("#dept").val(), 
        $("#yearlevelid").val(), 
        $("#sectionid").val(), 
        $("#sy").val(), 
        $("#sem").val(),
        $("#departmentid").val()
      );
      // console.log($("#yearlevelid").val() + " -> " + $("#sectionid").val());
    });
    
    $(".chosen").chosen();
});

$("#removeid").click(function(){
    var con = confirm("Do you really want to delete all ID No.?");
    if(con == true){
       $.ajax({
        url : "<?=site_url("process_/removeID")?>",
        data: {ltype : "<?=$ltype?>"},
        type: "POST",
        success: function(msg){
            alert(msg);
            location.reload();
        }
       }); 
    }else{
        return false;
    }
});

function loadxls(){
    var params  = "?view=reports_excel/listempstudcard";
        params += "&ltype=<?=$ltype?>";
        params += "&dept="+$("#dept").val();
        params += "&yrlvl="+$("#yearlevelid").val();
        params += "&sect="+$("#sectionid").val();
        params += "&sy="+$("#sy").val();
        params += "&sem="+$("#sem").val();
        params += "&deptid="+$("#departmentid").val();
    window.open("<?=site_url("reports_/reportloader")?>"+params,"Confirmed"); 
}
</script>