<?php

/**
 * @author Justin
 * @copyright 2015
 */
/**
 * @Kennedy Hipolitp
 * @2019
 * @Updated UI
 */

?>
<table class="table table-striped table-bordered table-hover" id="cutofflist">
    <thead>
        <tr>
            <th><a class="btn btn-primary" id="addnewcutoff" href="#" data-toggle="modal" data-target="#myModal">New Cut-Off</a></th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th class="align_center">Schedule</th>
            <th class="align_center">Quarter</th>
            <th class="align_center">Start Date</th>
            <th class="align_center">End Date</th>
            <th class="align_center">Added By</th>
            <th class="align_center">Updated By</th>
            <th class="align_center">Action</th>
        </tr>
    </thead>
    <tbody>  
            <?
                $query = $this->payroll->displayCutoff();
                if($query->num_rows() > 0){
                    foreach($query->result() as $row){
            ?>
                <tr>
                    <td class="align_center"><?=$this->payrolloptions->payscheduledesc($row->schedule)?></td>
                    <td class="align_center"><?=$this->payrolloptions->quarterdesc($row->quarter,TRUE,$row->schedule)?></td>
                    <td class="align_center"><?=date('F d, Y',strtotime($row->startdate))?></td>
                    <td class="align_center"><?=date('F d, Y',strtotime($row->enddate))?></td>
                    <td class="align_center"><?=$row->addedby?></td>
                    <td class="align_center"><?=$row->lastupdate?></td>
                    <td class="align_center">
                    <a class='btn btn-info grey edit_data_cutoff glyphicon glyphicon-edit' id="<?=$row->id?>" data-toggle="modal" data-target="#myModal"></a>
                    </td>
                </tr>   
            <?
                    }
                }
            ?>
                   
    </tbody>
</table>
<script>

$(".edit_data_cutoff").click(function(){
    var form_data   =   {
                            id      : $(this).attr("id"),
                            view    :   "cutoffconfig"                            
                        }
    $.ajax({
        url      :   "<?=site_url("payroll_/payrollconfig")?>",
        type     :   "POST",
        data     :   form_data,
        success  :   function(msg){
            $("#myModal").html(msg);
        }
    });
});

$("#addnewcutoff").click(function(){  
    $.ajax({
        url      : "<?=site_url('payroll_/payrollconfig')?>",
        type     : "POST",
        data     : {view   :   "cutoffconfig"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});
$("#cutofflist").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    /*"sDom": 'T<"clear"><"fg-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix"lfr>t<"fg-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix"ip>',*/
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
</script>