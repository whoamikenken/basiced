<?php
 /**
 * @author Justin
 * @copyright 2016
 */

?>
<div id="content"> <!-- Content start -->
<a id="addholiday" href="#modal-view" data-toggle="modal"><i class="icon-plus-sign"></i> Add New</a>
<div class="widgets_area">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="well blue">
                            <div class="well-header">
                                <h5>Department List</h5>
                            </div>

                            <div class="well-content">
                                <table class="table table-striped table-bordered table-hover datatable">
                                    <thead>
                                      <tr>
                                        <th class="align_center span1"></th>
                                        <th class="sorting_asc">Code</th>
                                        <th>Department</th>
										<th>Division Level</th>
                                        <th>Department Head</th>
                                        <th>Cluster Head</th>
                                      </tr>
                                    </thead>   
                                    <tbody>
<?
$dhead = "";
$sql = $this->db->query("select * from code_department");
foreach($sql->result() as $mrow){
?>
  <tr>
    <td class="align_center span1">
      <div class="btn-group">
        <a class="btn" href="#modal-view" tag='edit_d' data-toggle="modal" code='<?=$mrow->code?>'><i class="icon-edit"></i></a>
        <a class="btn" href="#" tag='delete_d' code='<?=$mrow->code?>'><i class="icon-trash"></i></a>
      </div>
    </td>
    <td class="align_center"><?=$mrow->code?></td>
    <td><?=$mrow->description?></td>
	<td><?=($mrow->managementid)?$this->extras->getManagementLevelDescription($mrow->managementid):""?></td>
    <td><?=$this->employee->getfullname($mrow->head)?></td>
    <td><?=$this->employee->getfullname($mrow->divisionhead)?></td>
  </tr> 
<?    
}

?>  
</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
</div>    
</div> 
<script>
$("#addholiday,a[tag='edit_d']").click(function(){
    var code = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Department" : "Add Department");
    $("#button_save_modal").text("Save");    
    var form_data = {
        code: code
    };
    $.ajax({
        url: "<?=site_url('maintenance_/manage_department')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});
$("a[tag='delete_d']").click(function(){
 var ans = confirm("Are you sure you want to continue?");
 if(ans){
     $.ajax({
        url:"<?=site_url("maintenance_/save_department")?>",
        type:"POST",
        data:{
           code: $(this).attr("code"),
           job: "delete"  
        },
        success: function(msg){
            $("#modalclose").click();
            $(".inner_navigation .main li .active a").click(); 
        }
     }); 
 }   
 return false;   
});
</script>