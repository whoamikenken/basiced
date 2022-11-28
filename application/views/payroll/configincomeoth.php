<?php

/**
 * @author Justin
 * @copyright 2016
 */
/**
 * @Kennedy Hipolitp
 * @2019
 * @Updated UI
 */

?>
<table class="table table-striped table-bordered table-hover" id="incomeothlist">
    <thead>
        <tr>
            <th><a class="btn btn-primary" id="addnewincomeoth" href="#" data-toggle="modal" data-target="#myModal">Add Other Income</a></th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th>Description</th>
            <th>Taxable</th>
            <th>Part of GI</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>  
            <?
                $query = $this->payroll->displayIncomeOth();
                if($query->num_rows() > 0){
                    foreach($query->result() as $row){
            ?>
                <tr>
                    <td><?=$row->description?></td>
                    <td><?=$row->taxable?></td>
                    <td><?=$row->grossinc?"YES":"NO"?></td>
                    <td>
                    <a class='btn btn-info edit_data_income glyphicon glyphicon-edit' id="<?=$row->id?>" data-toggle="modal" data-target="#myModal"></a>
                    </td>
                </tr>   
            <?
                    }
                }
            ?> 
    </tbody>
</table>
<script>
    
$(".delete_data_income").click(function(){
    var prmpt = confirm("Do you really want to delete this data?.");
    if(prmpt == true){
        var form_data   =   {
                                id      : $(this).attr("id"),
                                model   : "delIncome"
                            }
        $.ajax({
           url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            alert(msg);
            loadincomeothconfig();
           }
        });
    }else{
        return false;
    }
});

$(".edit_data_income").click(function(){
    var form_data   =   {
                            id      : $(this).attr("id"),
                            view    :   "incomeothconfig"                            
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

$("#addnewincomeoth").click(function(){  
    $.ajax({
        url      : "<?=site_url('payroll_/payrollconfig')?>",
        type     : "POST",
        data     : {view   :   "incomeothconfig"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});
$("#incomeothlist").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
</script>