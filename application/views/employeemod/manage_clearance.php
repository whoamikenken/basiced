<style type="text/css">
   .panel {
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
  .dataTables_wrapper{
  width: 100%;
  overflow: initial !important;
  overflow-y: initial !important;
  overflow-x: initial !important; 
  position: initial !important;
  -ms-overflow-style: -ms-autohiding-scrollbar !important;
  z-index: 1 !important;
  -webkit-overflow-scrolling: touch !important;
 }

 .dataTables_wrapper div.col-sm-6, .dataTables_wrapper thead, .dataTables_wrapper div.col-sm-12, .dataTables_wrapper tr, .dataTables_wrapper td, .dataTables_wrapper table, .dataTables_wrapper th, label, div.row, div.col-md-12, div.col-md-6{
  overflow: inherit !important;
  overflow-y: initial !important;
  overflow-x: initial !important; 
  position: initial !important;
  z-index: 1 !important;
  -ms-overflow-style: -ms-autohiding-scrollbar !important;

 }

 table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after{
    position: inherit;
    bottom: 8px;
    right: 8px;
    display: block;
    font-family: 'Glyphicons Halflings';
    opacity: 0.5;
    float: right;
    margin-right: 5%;
    z-index: -1 !important;
 }
table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>th.sorting_asc{
  padding-right: 0px !important;
}
<?php
  $deptid = $this->extensions->getEemployeeCurrentData($this->session->userdata("username"), 'deptid');
?>

</style>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                   <div class="panel-heading" ><h4><b>Manage Clearance</b></h4></div>
                   <div class="panel-body">
                        <br>
                        <div class="form_row" style="pointer-events: none">           
                            <label class="field_name align_right">Department</label>             
                            <div class="field no-search">
                                <div class="col-md-6">  
                                    <select class="form-control chosen manageSelect" id="deptid" name="deptid">
                                        <?php echo $this->extras->getDeptpartment($deptid)?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">           
                            <label class="field_name align_right">Office</label>             
                            <div class="field no-search">
                                <div class="col-md-6">  
                                    <select class="form-control chosen manageSelect" id="office" name="office">
                                        <?php echo $this->extras->getOffice()?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">           
                            <label class="field_name align_right">Employee</label>             
                            <div class="field no-search">
                                <div class="col-md-6">  
                                    <select class="form-control chosen manageSelect" id="employee" name="employee">
                                        <option value="">All Employee</option>
                                        <?
                                        $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")), "", "", false, "", $deptid);
                                        foreach($opt_type as $val){
                                            ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . " " . $val['fname'] . " " . $val['mname'])?></option><?    
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row withClearance" name="withClearance">           
                            <label class="field_name align_right">Status</label>             
                            <div class="field no-search">
                                <div class="col-md-6">  
                                    <select class="form-control chosen manageSelect" id="category" name="category">
                                        <!-- <option value="all">All Status</option> -->
                                        <option value="1">Completed</option>
                                        <option value="0">Incomplete</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">           
                            <label class="field_name align_right">&nbsp;</label>             
                            <div class="field no-search">
                                <div class="col-md-6">  
                                    <button class="btn btn-success" id="search" name="search" style="float: left; padding-left: 20px; padding-right: 20px;">Search</button>
                                </div>
                            </div>
                        </div>
                        <div id="emplistOffice"></div>                          
                    </div>
                </div>   
            </div>
        </div>
    </div>    
</div>
<script>
var toks = hex_sha512(" ");
loadOfficeDropdown();


$("#search").click(function(){
  loadUnderEmployee();
})


function loadUnderEmployee(){
    $("#emplistOffice").html("<img src='<?php echo base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?php echo site_url("deficiency_/loadUnderEmployee")?>",
       type     :   "POST",
       data     :   {office :  GibberishAES.enc($("select[name='office']").val() , toks), deptid :  GibberishAES.enc($("select[name='deptid']").val() , toks), category: GibberishAES.enc($("select[name='category']").val() , toks), employee: GibberishAES.enc($("select[name='employee']").val() , toks), toks:toks},
       success  :   function(msg){
        $("#emplistOffice").html(msg);

       }
    });
}

$("select[name='deptid']").change(function(){
    $.ajax({
        url      :   "<?php echo site_url("setup_/getOffice")?>",
        type: "POST",
        data: {department:GibberishAES.enc($(this).val() , toks), toks:toks},
        success: function(msg){
            $("select[name='office']").html(msg).trigger("chosen:updated");
        }
    });
});

function loadOfficeDropdown(){
  $.ajax({
        url      :   "<?php echo site_url("setup_/getOffice")?>",
        type: "POST",
        data: {department:GibberishAES.enc($("select[name='deptid']").val() , toks), toks:toks},
        success: function(msg){
            $("select[name='office']").html(msg).trigger("chosen:updated");
        }
    });
}

$("select[name='office']").on('change',function(){
    var office = GibberishAES.enc($('#office').val(), toks);
    var department = GibberishAES.enc($('#deptid').val(), toks);
    $.ajax({
        type : "POST",
        url  : "<?php echo site_url("employee_/load201sort")?>",
        data: { department:department, office:office, toks:toks},
        success: function(data){
            $("select[name='employee']").html(data).trigger("chosen:updated");
        }
    });
});

$(".chosen").chosen();

</script> 
