<?php

/**
 * @author Justin
 * @copyright 2015
 */

?>
<div class="span12">
    <div class="well blue">
        <div class="well-header">
            <h5>Employee Access List</h5>
        </div>
        <table class="table table-striped table-bordered table-hover datatable">
            <thead>
                <tr>
                    <th></th>
                    <th class="sorting_asc">Employee</th>
                    <th>Fullname</th>
                    <th>Access Date From</th>
                    <th>Access Date To</th>
                </tr>
            </thead>
            <tbody>        
            <?
            $data = $this->loaddata->loadeprofileconfig()->result();
            foreach($data as $row){
            ?>
                <tr>
                    <td class="align_center"><a class='btn grey edit_data icon-trash' id="delbtn" eid="<?=$row->employeeid?>" dfrom="<?=$row->datefrom?>" dto="<?=$row->dateto?>" ></a></td>
                    <td><?=$row->employeeid?></td>
                    <?if($row->employeeid != "All Employee"){?>
                    <td><?=$this->employee->getfullname($row->employeeid)?></td>
                    <?}else{?>
                    <td>All Employee</td>
                    <?}?>
                    <td><?=$row->datefrom?></td>
                    <td><?=$row->dateto?></td>
                </tr>
            <?
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
$('table').DataTable({
    bJQueryUI: true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
});
$("#delbtn").click(function(){
   var eid   = $(this).attr("eid");
   var dfrom = $(this).attr("dfrom");
   var dto   = $(this).attr("dto");
   $.ajax({
    url     :   "<?=site_url("maintenance_/proconfig")?>",
    type    :   "POST",
    data    :   {eid : eid, dfrom : dfrom, dto : dto},
    success :   function(msg){
        alert(msg);
        loadeprofile();
    }
   }); 
});
</script>