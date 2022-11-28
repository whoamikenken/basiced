<?php

/**
 * @author Justin
 * @copyright 2015
 */

if(empty($category)) $category = "";
$employeeleave = $this->employee->loadallabsent($dfrom);

?>
<div class="well orange">
    <div class="well-header">
        <h5>Absent Management</h5>
    </div>
    <table class="table table-striped table-bordered table-hover datatable" >                                                              
        <thead>
            <tr>
                <th></th>
                <th class="sorting_asc">Employee ID</th>
                <th>Full Name</th>
            </tr>
        </thead>
        <tbody id="manageleave">                                                               
    <?
    foreach($employeeleave as $row){
    ?>
      <tr employeeid='<?=$row['employeeid']?>' style="cursor: pointer;">
        <td class="align_center col-md-1">
          <div class="btn-group">
            <a class="btn" tag='add_leave' data-toggle="modal" code="<?=$row['employeeid']?>" ><i class="glyphicon glyphicon-plus-sign"></i></a>
          </div>
        </td>
        <!--<td style="text-align: center;"><input type="checkbox" style="-webkit-transform: scale(2);" name="chkabsent" value="<?=$row['employeeid']?>" /></td>-->
        <td><?=$row['employeeid']?></td>
        <td><?=$row['fullname']?></td>
      </tr>
    <?
    }
    ?>
    </tbody>
    </table>
</div>
<script>
$("a[tag='add_leave']").click(function(){
    var addleave = confirm("Do you really want to credit this as leave?.");
    if(addleave == true){
    var form_data = {
        eid     : $(this).attr("code"),
        dfrom   : "<?=$dfrom?>"
    };
    $.ajax({
        url: "<?=site_url('process_/absent_to_leave')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            alert(msg);
            /*
            $("#manageabsent").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");
            form_data = {
                        category: "absent",
                        dfrom   : "<?=$dfrom?>"
                        }
            $.ajax({
                url: "<?=site_url('process_/view_leave_status')?>",
                type: "POST",
                data: form_data,
                success: function(msg){
                    $("#manageabsent").html(msg);
                }
            });
            */
        }
    });
    }else return false;  
});
$('table').dataTable({
    bJQueryUI: true,
    "sPaginationType": "full_numbers",
    "responsive": true,
    "bDestroy": true,
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 }
});

</script>