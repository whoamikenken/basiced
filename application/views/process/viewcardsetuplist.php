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
<div class="panel-group" id>
<form id="hoy">
<div class="form_row">
    <label class="field_name align_right">School Year</label>
    <div class="field no-search">
        <div class="col-md-6">
            <select id="sy" name="sy" class="chosen col-md-4">
              <?php
                $sy1 = "";
                foreach ($schlyr as $key => $value) {
                  $value["SY"] = Globals::_e($value["SY"]);
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
          </div>
    </div>
</div>

<div class="form_row">
    <label class="field_name align_right">Semester</label>
    <div class="field no-search">
        <div class="col-md-6">
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
          </div>   
    </div>
</div>

<div class="form_row">
    <label class="field_name align_right">Department</label>
    <div class="field">
        <div class="col-md-6">
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
        <div class="col-md-6">
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
        <div class="col-md-6">
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
        <div class="align_right">
          <b><a href="javascript:loadxls()" class="btn btn-primary"><i class="icon-print"></i> Printer-Friendly</a></b>
          <b><a href="#" id="removeid" class="btn btn-primary"><i class="glyphicon glyphicon-remove"></i> Remove All ID</a></b>
          <b><a href="#" id="syncaims" class="btn btn-primary"><i class="glyphicon glyphicon-import"></i> Sync Aims Student</a></b>
        </div>
    </div>
</div>

<br>

</form>
<?    
}else{
  $empdept = $departmentid;
  ?>
  <form>
    <div class="form_row">
        <label class="field_name align_right">Department</label>
        <div class="field">
            <div class="col-md-6">
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
            </div>
            
        </div>
    </div>
    <div class="form_row">
        <label class="field_name align_right">Office</label>
        <div class="field">
            <div class="col-md-6">
                <select class="chosen col-md-5" id="office" name="office"><?=$this->extras->getOffice($office, $departmentid)?></select>
            </div>
            
        </div>
    </div>
    <div class="form_row">
        <label class="field_name align_right">Type</label>
        <div class="field">
            <div class="col-md-6">
                <select class="chosen col-md-5" id="teachingtype" name="teachingtype">
                    <option value="">All</option>
                    <option value="teaching" <?= ($teachingtype == "teaching")? "selected" : "" ?>>Teaching</option>
                    <option value="nonteaching" <?= ($teachingtype == "nonteaching")? "selected" : "" ?>>Non Teaching</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form_row">
        <label class="field_name align_right">Employment&nbsp;Status</label>
        <div class="field">
            <div class="col-md-6">
                <select class="chosen col-md-5" id="empstat" name="empstat">
                <?php
                  $arrayDept = $this->extras->showemployeestatus('All Employment Status');
                  foreach ($arrayDept as $key => $item) {
                    ?>
                    <option <?php print("value=\"{$key}\""); print(($empstat == $key) ? "selected=\"true\"" : ""); ?>><?= ucfirst (strtolower ($item)); ?></option>
                    <?php
                  }
                ?>
                </select>
            </div>
            
        </div>
    </div>
    <div class="form_row">
    <label class="field_name align_right">Status</label>
    <div class="field no-search">
        <div class="col-md-6">
            <select class="chosen col-md-4" id="status" name="status">
               <!-- <option value="all" <?=($status == "all") ? 'selected' : '' ;?>>All Status</option>  -->
              <option value="active" <?=($status == "active") ? 'selected' : '' ;?>>Active</option> 
              <option value="inactive" <?=($status == "inactive") ? 'selected' : '' ;?>>Inactive</option> 
            </select>
        </div>
        <div class="col-md-6">
          <div class="align_right">
            <b><a href="javascript:loadxls()" class="btn btn-primary"><i class="icon-print"></i> Printer-Friendly</a></b>
            <b><a href="#" id="removeid" class="btn btn-primary"><i class="glyphicon glyphicon-remove"></i> Remove All ID</a></b>
          </div>
        </div>
    </div>
</div>
    <br>  
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
    .swal2-cancel{
   margin-right: 20px;
 }
</style>


<div id="message" class="hidden" >
  <span id="msgEIDtext"></span>
  <span id="msgEID" style="font-weight: bold;"></span>
  <span id="msgCnumtext"></span>
  <span id="msgCnum" style="font-weight: bold;"></span>
  <span id="msgEnd"></span>
</div>
<table id="dt_listcardsetup" class="table table-striped table-bordered table-hover">
<thead style="background-color: #0072c6; ">
    <tr>
        <th class="col-md-1">#</th>
        <th class="sorting_asc">Employee ID</th>
        <th>Fullname</th>
        <th>RFID #</th>
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
        // echo $this->db->last_query();
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
      $empwhere = "WHERE 1";
      if ($empdept != "") {
        $empwhere .= " AND a.deptid = \'{$empdept}\' ";
      }
      if ($empstat != "") {
        $empwhere .= " AND a.employmentstat = \'{$empstat}\' ";
      }
      if ($office != "") {
        $empwhere .= " AND a.office = \'{$office}\' ";
      }
      if ($teachingtype != "") {
       $empwhere .= " AND a.teachingtype = \'{$teachingtype}\' ";
      }
      if($status != "all"){
        if($status == "active") $empwhere .= " AND a.isactive = 1";
        else if($status == "inactive") $empwhere .= " AND a.isactive = 0";
      }
      $que = "CALL prc_employee_card_get(@a,@b,' {$empwhere} ')";
      $employee = $this->db->query($que)->result();
      foreach($employee as $row){
      ?>
          <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">
            <td><?=Globals::_e($o)?></td>
            <td><?=Globals::_e($row->employeeid)?></td>
            <td><?=Globals::_e($this->extras->htmlchangeenye($row->fullname))?></td>
            <td><label style="display: none;"><?=($row->employeecode)?></label><div class="field"><input name="bcode" type="text" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" class="text-center col-md-12" encodetype='E' encode='<?=Globals::_e($row->employeeid)?>' value='<?=Globals::_e($row->employeecode)?>'/></div></td>
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
var toks = hex_sha512(" ");
validateCanWrite();
$(function(){

    var table = $('#dt_listcardsetup').DataTable({
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
                                toks: toks,
                                newcode:GibberishAES.enc(newcode, toks),
                                eid:GibberishAES.enc(eid, toks),
                                entype:GibberishAES.enc(entype, toks)
                            },
                            success:function(msg){
                                // $("#message").text("Card Number : "+newcode+" is successfully saved!");
                                // alert(msg);
                                if (msg == "EXIST") 
                                {
                                  Swal.fire({
                                      icon: 'warning',
                                      title: 'Warning!',
                                      text: "Employee Number : "+ eid +" with Card Number : "+ newcode +" is already existing!",
                                      showConfirmButton: true,
                                      timer: 1500
                                  })
                                  obj.val("");
                                }
                                else
                                {
                                  Swal.fire({
                                      icon: 'success',
                                      title: 'Success!',
                                      text: "Employee Number : "+ eid +" with Card Number : "+ newcode +" is successfully saved!",
                                      showConfirmButton: true,
                                      timer: 1500
                                  })
                                  $("input[name='bcode']").eq(obj.index("input[name='bcode']")+1).focus(); 
                                    
                                }
                            }
                          });
                       } 
                    }); 
          }
    });
    new $.fn.dataTable.FixedHeader( table );

    $('.no-search .dataTables_length select').chosen();
    $("input[name='bcode']").click(function(){
       $(this).select(); 
       return false;
    });
    
    $("#sy,#sem,#dept,#sectionid,#yearlevelid, #status, #empstat, #office, #teachingtype").change(function(){
      // var data = $("#hoy").serialize();
      // alert($("#dept").val());
      loadlisttodisplay(
        $("#dept").val(), 
        $("#yearlevelid").val(), 
        $("#sectionid").val(), 
        $("#sy").val(), 
        $("#sem").val(),
        $("#departmentid").val(),
        $("#status").val(),
        $("#empstat").val(),
        $("#office").val(),
        $("#teachingtype").val()
      );
      // console.log($("#yearlevelid").val() + " -> " + $("#sectionid").val());
    });

    $("#departmentid").change(function(){
      $("#office").val('');
      // var data = $("#hoy").serialize();
      // alert($("#dept").val());
      loadlisttodisplay(
        $("#dept").val(), 
        $("#yearlevelid").val(), 
        $("#sectionid").val(), 
        $("#sy").val(), 
        $("#sem").val(),
        $("#departmentid").val(),
        $("#status").val(),
        $("#empstat").val(),
        $("#office").val(),
        $("#teachingtype").val()
      );
      // console.log($("#yearlevelid").val() + " -> " + $("#sectionid").val());
    });
    
    $(".chosen").chosen();
});

$("#syncaims").click(function(){
  var formdata = {
    dept: $("#dept").val(), 
    yearlevelid: $("#yearlevelid").val(), 
    sectionid: $("#sectionid").val(), 
    sy: $("#sy").val(), 
    sem: $("#sem").val(),
  };
  var r = confirm("Are you sure you want sync aims student? ");
  if(r){
    $.ajax({
      url: "<?= site_url("student_/syncAimsStudent") ?>",
      type: "POST",
      data: formdata,
      success:function(response){
        alert(response);
      }
    });
  }
});

$("#removeid").click(function(){
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to delete all ID No.?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
       $.ajax({
        url : "<?=site_url("process_/removeID")?>",
        data: {ltype : "<?=$ltype?>"},
        type: "POST",
        success: function(msg){
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: msg,
              showConfirmButton: true,
              timer: 1000
          });
            location.reload();
        }
       }); 
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'RFID is safe.',
         'error'
       )
     }
   })
});

function loadxls(){
    var params  = "view="+ GibberishAES.enc("reports_excel/listempstudcard", toks);
        params += "&ltype="+ GibberishAES.enc("<?=$ltype?>", toks);
        params += "&dept="+ GibberishAES.enc($("#dept").val(), toks);
        params += "&yrlvl="+ GibberishAES.enc($("#yearlevelid").val(), toks);
        params += "&sect="+ GibberishAES.enc($("#sectionid").val(), toks);
        params += "&sy="+GibberishAES.enc($("#sy").val(), toks);
        params += "&sem="+GibberishAES.enc($("#sem").val(), toks);
        params += "&deptid="+GibberishAES.enc($("#departmentid").val(), toks);
        params += "&status="+GibberishAES.enc($("#status").val(), toks);
        params += "&toks="+toks;

        encodedData = encodeURIComponent(window.btoa(params));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
}

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#removeid, input").css("pointer-events", "none");
    else $("#removeid, input").css("pointer-events", "");
}

</script>