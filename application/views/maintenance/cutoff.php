<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */



?>
<div id="content"> <!-- Content start -->
<div class="field">
    <a href="#modal-view" class="btn btn-primary" id="generatecutoff" data-toggle="modal">Generate New Cut-off</a>
    <a href="#" class="btn btn-primary" id="resetentry">Reset</a>
</div>

<div class="widgets_area">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well blue">
                            <div class="well-header">
                                <h5>Manage Cut-off</h5>
                            </div>

                            <div class="well-content">
                                <table class="table table-striped table-bordered table-hover datatable">
                                    <thead>
                                      <tr>
                                        <th></th>
                                        <th class="sorting_desc">From</th>
                                        <th>To</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                      </tr>
                                    </thead>   
                                    <tbody>
<?
$sql = $this->db->query("select id,datefrom,dateto,cutoff_type,is_process from cutoff_summary order by id desc");

if($sql->num_rows()>0){
for($r=0;$r<$sql->num_rows();$r++){
 $mrow = $sql->row($r);   
?>
  <tr>
    <td class="align_center col-md-1">
      <div class="btn-group">
        <a class="btn" href="#modal-view" tag='view_d' data-toggle="modal" code='<?=$mrow->id?>'><i class="icon-list"></i></a>
      </div>
    </td>
    <td class="align_center"><?=$mrow->datefrom?></td>
    <td class="align_center"><?=$mrow->dateto?></td>
    <td class="align_center"><?=$this->extras->getincomebase($mrow->cutoff_type)?></td>
    <td data-value="<?=((bool)$mrow->is_process ? "active" : "disabled")?>"><?if((bool)$mrow->is_process){?><span class="label label-success">Processed</span><?}else{?><span class="label label-warning">Pending</span><?}?></td>
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
$("#generatecutoff").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Generate Cut-off");  
    $.ajax({
        url: "<?=site_url('maintenance_/cutoff_entry')?>",
        type: "POST",
        success: function(msg){
           $("#modal-view").find("div[tag='display']").html(msg);
        }
    }); 
});
$("a[tag='view_d']").click(function(){
    $("#modal-view").find("h3[tag='title']").text("List of days");  
    $.ajax({
        url: "<?=site_url('maintenance_/cutoff_details')?>",
        type: "POST",
        data:{sid: $(this).attr("code")},
        success: function(msg){
           $("#modal-view").find("div[tag='display']").html(msg);
        }
    }); 
});
$("#resetentry").click(function(){
    $.ajax({
       url: "<?=site_url("maintenance_/resetentry")?>",
       type: "POST",
       success: function(msg){
         alert("Done reseting");
         $("#modalclose").click();
         $(".inner_navigation .main li .active a").click();
       }
    });
});
</script>