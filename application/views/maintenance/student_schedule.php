
<div id="content"> <!-- Content start -->
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="well blue">
                <div class="well-header">
                    <h5>Student Schedule</h5>
                </div>
                <div class="well-content">
                    <span><b>(NOTE : CSV File Extension Only)</b></span>
                    <form id="upload" class="form-horizontal well" action="<?=site_url('uploadcsv/import');?>" method="post" name="upload_excel" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="sched" />
                        <input type="file" name="file" id="file" class="input-large" />
                        <button type="submit" id="submit" name="Importsched" id="Importsched" class="btn btn-primary">Upload</button>
                    </form>
                    <div id="msg"></div>
                </div>
            </div>
            <br />
            <div class="well blue">
                <div class="well-header">
                    <h5>Student Schedule</h5>
                </div>
                <div class="well-content">
                    <table id="user_datatable" class="table table-striped table-bordered table-hover">
                        <thead>
                          <tr>
                            <th class="align_center col-md-1"></th>
                            <th class="sorting_asc">Code</th>
                            <th class="sorting_asc">Description</th>
                          </tr>
                        </thead>   
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>    
</div> 
<script>
var ulist;
$(function(){
    ulist = $('#user_datatable').dataTable({
        "sPaginationType": "bootstrap",
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "",
        "sDom": "<'tableHeader'<l><'clearfix'f>r>t<'tableFooter'<i><'clearfix'p>>",
        "iDisplayLength": 15,
        "asSorting": [[ 2, "asc" ]],
        "aoColumns": [
    		{ "bSortable": false },
    		{ "bSortable": true },
        { "bSortable": true }
    	],
        "sServerMethod": "POST",
        "fnDrawCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // codes here 
            $("a[tag='edit_d']").click(function(){

            });
        }
    }); 
    $("#user_datatable_length").append('&nbsp;<a id="addschedule" class="btn btn-primary" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>');
    $('.no-search .dataTables_length select').chosen();
    
    $("#addschedule").click(function(){  
       dotoggleuserinfo("New Schedule",{job:"new"});
    });
    function dotoggleuserinfo(title,data){
       $("#modal-view").find("h3[tag='title']").html(title); 
       $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
       $("#modal-view").addClass("container");
       $("#button_save_modal").text("Save");   
       $.ajax({
           url:"<?=site_url("maintenance_/addnewstudschedule")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#modal-view").find("div[tag='display']").html(msg);
           }
       }); 
    }
    
});
</script>
