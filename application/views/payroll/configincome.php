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
<table class="table table-striped table-bordered table-hover" id="incomelist">
    <thead>
        <tr>
            <th><a class="btn btn-primary" id="addnewincome" href="#" data-toggle="modal" data-target="#myModal">Add Income</a></th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th>Description</th>
            <th>Taxable</th>
            <th>Deducted by</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>  
            <?
                //$query = $this->payroll->displayIncome();
                $CI =& get_instance();
                $CI->load->model('income');
                $query = $CI->income->getIncomeSetupList(array('ismainaccount'=>'0'));
                if($query->num_rows() > 0){
                    foreach($query->result() as $row){
                        $is_used = $CI->income->isIncomeUsed($row->id);
            ?>
                <tr>
                    <td><?=$row->description?></td>
                    <td><?php echo ($row->taxable == "notax")? "Non-Taxable":"Taxable" ?></td>
                    <td><?=$row->deductedby?></td>
                    <td class="align_center">
                    <a class='btn btn-info edit_data_income glyphicon glyphicon-edit' id="<?=$row->id?>" data-toggle="modal" data-target="#myModal"></a>
                    <a class='btn btn-danger delete_data_income glyphicon glyphicon-trash' id="<?=$row->id?>" is_used = "<?=$is_used?>" <?= ($is_used > 0) ? " style='pointer-events:none;' disabled " : "" ?> ></a>
                    
                    </td>
                </tr>   
            <?
                    }
                }else{
            ?>
                <tr>
                    <td>&nbsp;</td>
                    <td class="align_center"><i>No Data Exists..</i></td>
                </tr>
            <?}?>   
    </tbody>
</table>
<script>

$("#incomelist").delegate(".delete_data_income", "click", function(){
    if($(this).attr("is_used") > 0) return;
    const swalWithBootstrapButtons = Swal.mixin({
         customClass: {
           confirmButton: 'btn btn-success',
           cancelButton: 'btn btn-danger'
         },
         buttonsStyling: false
       });

       swalWithBootstrapButtons.fire({
         title: 'Are you sure?',
         text: "You won't be able to revert this!",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: 'Yes, proceed!',
         cancelButtonText: 'No, cancel!',
         reverseButtons: true
       }).then((result) => {
        if (result.value) {
            var form_data   =  {
                toks: toks,
                id      : GibberishAES.enc($(this).attr("id"), toks),
                model   : GibberishAES.enc("delIncome", toks)
            }
           $.ajax({
                url: "<?=site_url("payroll_/loadmodelfunc")?>",
                type:"POST",
                data     :   form_data,
                success : function(msg){
                    if(msg == "Income successfully deleted."){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: msg,
                            showConfirmButton: true,
                            timer: 2000
                        });
                    }else{
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: msg,
                            showConfirmButton: true,
                            timer: 2000
                        });
                    }
                loadincomeconfig();
                }
            });
         } else if (
           result.dismiss === Swal.DismissReason.cancel
         ) {
           swalWithBootstrapButtons.fire(
             'Cancelled',
             'Data is safe.',
             'error'
           )
         }
       });
});

$(".edit_data_income").click(function(){
    var form_data   =   {
                            id      : $(this).attr("id"),
                            view    :   "incomeconfig"                            
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

$("#addnewincome").click(function(){  

    $.ajax({
        url      : "<?=site_url('payroll_/payrollconfig')?>",
        type     : "POST",
        data     : {view   :   "incomeconfig"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

$(document).ready(function(){
    var table = $('#incomelist').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

</script>