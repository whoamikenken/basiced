<?php
$dept_list = $this->extras->showdepartment();
// unset($dept_list[0]);
?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="panel animated fadeIn delay-1s" id="manage">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage In/Out</b></h4></div>
               <div class="panel-body">
                  <form id="request_form">
                    <div class="col-md-12 well-content" style="margin-bottom: 20px;">
                      <div class="form-row col-md-12" style="margin-bottom: 10px;">
                        <label class="col-md-3 align_right">Teaching Type</label>
                        <div class="field col-md-5">
                          <select class="chosen col-md-4 filter" id="tnt">
                            <option value="">All Teaching Type</option>
                            <option value="teaching">Teaching</option>
                            <option value="nonteaching">Non-Teaching</option>
                          </select>
                        </div>
                      </div>
                      <div class="form-row col-md-12" style="margin-bottom: 10px;">
                        <label class="col-md-3 align_right">Department</label>
                        <div class="field col-md-5">
                          <select class="chosen col-md-4 filter" id="deptid">
                            <option value="">All Department</option>
                            <?php foreach($dept_list as $key => $value): ?>
                              <option value="<?=$key?>"><?=$value?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-row col-md-12" style="margin-bottom: 10px;">
                        <label class="col-md-3 align_right">Employment&nbsp;Status</label>
                        <div class="field col-md-5">
                          <select class="chosen col-md-4 filter" id="empstat" name="empstat">
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
                      </div>
                      <div class="form-row col-md-12" style="margin-bottom: 10px;">
                        <label class="col-md-3 align_right">Status</label>
                        <div class="field col-md-5 filter">
                          <select class="chosen col-md-4" id="status">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="well-content" id="empList">
                        
                    </div>
                  </form>
            </div>
            </div>
            <button class="btn btn-success" id='backlist' style="margin-bottom: 10px;display: none;">Back to employee list</button>
            <div class="panel animated fadeIn result" hidden>
               <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage In/Out</b></h4></div>
                 <div class="panel-body">
                      <div id="employeesched" style="padding: 5px;color:black;"></div>
                   </div>
              </div>
</div>
</div>
</div>
</div>
</div>

<script>
loadEmployees();
 var toks = hex_sha512(" "); 
function loadEmployees(tnt='', deptid='', status='', empstat=''){
  $.ajax({
    url : "<?=site_url("process_/loadEmployees")?>",
    data: {tnt:GibberishAES.enc(tnt, toks), deptid:GibberishAES.enc(deptid, toks), status:GibberishAES.enc(status, toks), empstat:GibberishAES.enc(empstat, toks), toks:toks},
    type: "POST",
    success:function(res){
      $("#empList").html(res);
    }
  })
}

$(".filter").change(function(){
  $("#empList").html("Loading, please wait..");
  loadEmployees($("#tnt").val(), $("#deptid").val(), $("#status").val(), $("#empstat").val());
})

$(".chosen").chosen();
</script>