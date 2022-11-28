<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */



?>
<div id="content"> <!-- Content start -->
<a id="addincome" href="#modal-view" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a>
<div class="widgets_area">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well blue">
                            <div class="well-header">
                                <h5>Manage Income</h5>
                            </div>

                            <div class="well-content">
                                <table class="table table-striped table-bordered table-hover datatable">
                                    <thead>
                                      <tr>
                                        <th class="align_center col-md-1"></th>
                                        <th class="sorting_asc">Code</th>
                                        <th>Description</th>
                                        <th class="align_center col-md-3">Taxable</th>
                                      </tr>
                                    </thead>   
                                    <tbody>
<?
$sql = $this->db->query("select code_income,description,taxable from incomes");
if($sql->num_rows()>0){
for($r=0;$r<$sql->num_rows();$r++){
 $mrow = $sql->row($r);   
?>
  <tr>
    <td tag='deduct' class="align_center col-md-1">
      <div class="btn-group">
        <a class="btn" href="#modal-view" tag='edit_d' data-toggle="modal" code='<?=$mrow->code_income?>'><i class="glyphicon glyphicon-edit"></i></a>
        <a class="btn" href="#" tag='delete_d' code='<?=$mrow->code_income?>'><i class="glyphicon glyphicon-trash"></i></a>
      </div>
    </td>
    <td class="align_center col-md-2"><?=$mrow->code_income?></td>
    <td><?=$mrow->description?></td>
    <td class="align_center col-md-3"><?=$mrow->taxable?></td>
  </tr>  
<?    
}
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
$("#addincome,a[tag='edit_d']").click(function(){
    var code = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Income" : "Add Income");  
    var form_data = {
        code: code
    };
    $.ajax({
        url: "<?=site_url('maintenance_/manage_income')?>",
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
        url:"<?=site_url("maintenance_/save_income")?>",
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