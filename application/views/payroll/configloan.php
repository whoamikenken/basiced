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

<table class="table table-striped table-bordered table-hover" id="loanlist">
    <thead>
        <tr>
            <th><a class="btn btn-primary" id="addnewloan" href="#" data-toggle="modal" data-target="#myModal">Add Loan</a></th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th>Description</th>
            <!-- <th>Taxable</th> -->
            <!-- <th>Part of GI</th> -->
            <th>Added By</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>  
            <?
                $CI =& get_instance();
                $CI->load->model('loan');
                $query = $this->payroll->displayLoan();
                if($query->num_rows() > 0){
                    foreach($query->result() as $row){
                        $is_used = $CI->loan->isLoanUsed($row->id);
            ?>
                <tr>
                    <td><?=$row->description?></td>
                    <!-- <td><?=$row->taxable?></td> -->
                    <!-- <td><?=$row->grossinc?"YES":"NO"?></td> -->
                    <td><?=$row->addedby?></td>
                    <td class="align_center">
                    <a class='btn btn-info edit_data_loan glyphicon glyphicon-edit' id="<?=$row->id?>" data-toggle="modal" data-target="#myModal"></a>
                    <a class='btn btn-danger delete_data_loan glyphicon glyphicon-trash' id="<?=$row->id?>" is_used = "<?=$is_used?>" <?= ($is_used > 0) ? " style='pointer-events:none;' disabled " : "" ?> ></a>
                    <!--
                    <a class='btn grey delete_data_loan glyphicon glyphicon-trash' id="<?=$row->id?>"></a>
                    -->
                    </td>
                    
                </tr>   
            <?
                    }
                }else{
            ?>
                <tr>
                    <td>&nbsp;</td>
                    <td class="align_center"><i>No Data Exists..</i></td>
                    <td>&nbsp;</td>
                </tr>
            <?}?>   
    </tbody>
</table>
<script>


$("#loanlist").delegate(".delete_data_loan", "click", function(){
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
                model   : GibberishAES.enc("delLoan", toks)
            }
           $.ajax({
                url: "<?=site_url("payroll_/loadmodelfunc")?>",
                type:"POST",
                data     :   form_data,
                success : function(msg){
                    if(msg == "Loan successfully deleted."){
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
                loadloanconfig();
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

$(".edit_data_loan").click(function(){
    var form_data   =   {
                            id      : $(this).attr("id"),
                            view    :   "loanconfig"                            
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

$("#addnewloan").click(function(){  
    $.ajax({
        url      : "<?=site_url('payroll_/payrollconfig')?>",
        type     : "POST",
        data     : {view   :   "loanconfig"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});
$("#loanlist").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
</script>