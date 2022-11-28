<?php
/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\mailleaveapp.php
 */

$curr_date = date('Y-m-d');
?>
<style type="text/css">
   .panel {
    border: 5px solid #0072c6 !important;
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

.dropdown-menu {
    z-index: 9999 !important;
}

a.chosen-single.chosen-default {
    position: inherit!important;
}
.hider{
  position: unset !important;
}
</style>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Leave Management</b></h4></div>
                   <div class="panel-body">                       
                        <div class="form_row no-search">
                            <label class="field_name align_right">Category</label>
                            <div class="field">
                              <div class="col-md-7">
                                <select class="form-control" id="category">
                                    <?
                                        $opt = $this->extras->showCategory();
                                        foreach($opt as $key=>$val){
                                    ?>      
                                            <option value="<?=$key?>"><?=$val?></option><?
                                        }
                                    ?>
                                </select>
                              </div>
                            </div>
                        </div>                            
                        <div class="form_row">
                            <label class="field_name align_right">Date From</label>
                            <div class="field">
                                <div class="col-md-12">
                                  <div class="col-md-3" style="padding-left: 0px;width: 27.4%;">
                                  <div class='input-group date' id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd" >
                                    <input type='text' class="form-control" size="16" name="ldfrom" type="text" value="<?=$curr_date?>" />
                                    <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>
                                <div class="col-md-1" style="width: auto"><b>To</b></div>
                              <div class="col-md-3" style="width: 27.4%;">
                                <div class='input-group date' id="ldto" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                  <input type='text' class="form-control" size="16" name="ldto" type="text" value="<?=$curr_date?>"/>
                                  <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                              </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="btn btn-primary" id="searchlbtn" style="float: right; margin-right: 21%">Search</a>
                        </div> 
                    </div>
                </div>
                <div class="panel" id="manageleave">

            </div>
        </div>
    </div>    
</div>
<script>
$(document).ready(function(){
    $("#leavemngmnt").hide();
    view_leave_status('','','PENDING');
});
$("#searchlbtn").click(function(){
    var category = $("#category").val(), 
        dfrom    = $("input[name='ldfrom']").val(), 
        dto      = $("input[name='ldto']").val();
        
    view_leave_status(dfrom, dto, category);
});

function view_leave_status(datefrom, dateto, status, deptid='',office=''){
    // $("#manageleave").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("leave_application_/getLeaveAppListToManage")?>",
       type     :   "POST",
       data     :   {
            datefrom    : datefrom, 
            dateto      : dateto,
            deptid      : deptid,
            office      : office,
            status      : status
        },
       success  :   function(msg){
        $("#manageleave").html(msg);
       }
    });
}

 $('.date').datetimepicker(
    { format: 'YYYY-MM-DD' }).on('dp.show', function (e) { 
      $(".chosen-container").addClass("hider");
  });

  $('.date').datetimepicker(
    { format: 'YYYY-MM-DD' }).on('dp.hide', function (e) { 
      $(".chosen-container").removeClass("hider");
  });

$("#category").chosen();
</script> 
