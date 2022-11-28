<?php
/**
 * @modified Angelica Arangco  2017
 */
$datetoday = date("Y-m-d");
?>
<style>
  .dataTables_wrapper{
  margin-top: 1.4%;
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
  z-index: -1 !important;
  -ms-overflow-style: -ms-autohiding-scrollbar !important;

 }

 table.dataTable thead .sorting:after, table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after, table.dataTable thead .sorting_asc_disabled:after, table.dataTable thead .sorting_desc_disabled:after{
    position: inherit;
    bottom: 8px;
    right: 8px;
    display: flex;
    overflow: inherit !important;
    font-family: 'Glyphicons Halflings';
    opacity: 0.5;
    float: right;
    margin-right: 5%;
    -ms-overflow-style: -ms-autohiding-scrollbar !important;
    /*z-index: auto !important;*/

 }
table.dataTable thead>tr>th.sorting, table.dataTable thead>tr>th.sorting_asc{
  padding-right: 0px !important;
}

.bootstrap-datetimepicker-widget.dropdown-menu{
  z-index: 999 !important;
  overflow: inherit;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Change Schedule Management</b></h4></div>
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
                                <div class="col-md-7" style="width: 65.03%">
                                  <div class="col-md-5" style="padding-left: 0px; padding-right: 0px;">
                                  <div class='input-group date' id="ldfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="ldfrom" type="text" value="<?=$datetoday?>"/>
                                    <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>
                                <div class="col-md-2" style="width: auto"><b>To</b></div>
                              <div class="col-md-5" style="padding-left: 0px; padding-right: 0px;">
                                <div class='input-group date' id="ldto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                  <input type='text' class="form-control" size="16" name="ldto" type="text" value="<?=$datetoday?>"/>
                                  <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                              </div>
                                </div>
                            </div>
                        </div>
                         <div class="form_row">
                          <label class="field_name align_right" >&emsp;</label>
                          <div class="field">
                            <div class="col-md-7">
                                <a href="#" class="btn btn-primary" id="search">Search</a>
                            </div> 
                          </div>
                        </div>
                                                
                        <div style="width: 99.7%;text-align: right;padding: 2px; display: none;"><a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal">New Request</a></div>          
                    </div>        
                </div>
                <div id="removeAni" class="panel animated fadeIn">
                </div>
            </div>
        </div>        
    </div>        
</div>
<div class="modal fade" id="myModal" data-backdrop="static"></div>
<script>
$(document).ready(function(){  
    changesched('','','PENDING');

    setTimeout(
      function() 
      {
        $("#removeAni").removeClass("animated fadeIn delay-1s");
      }, 2000);
});

$("#search").click(function(){
    var category = $("#category").val(), 
        dfrom    = $("input[name='ldfrom']").val(), 
        dto      = $("input[name='ldto']").val();
        
    changesched(dfrom, dto, category);
});

$("#newrequest").click(function(){  
    if($(this).prop("disabled")) alert("Please Attach Post Activity first.");
    $.ajax({
        url      : "<?=site_url("schedule_/loadApplyCSForm")?>",
        type     : "POST",
        data     : {
                        // folder: "employeemod", 
                        // view: "changesched_apply"
                    },
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

/*
*  FUNCTIONS
*/

function changesched(datefrom, dateto, status){
   $("#removeAni").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("schedule_/getCSAppListToManage")?>",
      type     :   "POST",
      data     :   {
            datefrom    : datefrom, 
            dateto      : dateto,
            status      : status
        },
      success  :   function(msg){
       $("#removeAni").html(msg);
      }
   });
}

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();

$(document).ready(function()
    {

        
    });
</script>