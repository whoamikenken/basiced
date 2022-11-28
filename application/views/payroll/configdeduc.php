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

<table class="table table-striped table-bordered table-hover" id="deductlist">
    <thead>
        <tr>
         <th colspan="7"><a class="btn btn-primary addnewdeduction" href="#" data-toggle="modal" data-target="#myModal">Add Deduction</a> </th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th>Description</th>
            <!-- <th>Taxable</th> -->
            <!-- <th>Loan Account</th> -->
            <!-- <th>Part OF GI</th> -->
            <th>Added By</th>
            <!-- <th>Arithmetic</th> -->
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
            <?
                $CI =& get_instance();
                $CI->load->model('deduction');
                $query = $this->payroll->displayDeduction();
                if($query->num_rows() > 0){
                    foreach($query->result() as $row){
                        $is_used = $CI->deduction->isDeductionUsed($row->id);

            ?>
                <tr>
                    <td><?=$row->description?></td>
                    <td><?=$row->addedby?></td>
                    <td class="align_center">
                    <a class='btn btn-info edit_data_deduc glyphicon glyphicon-edit' id="<?=$row->id?>" data-toggle="modal" data-target="#myModal"></a>
                    <a class='btn btn-danger delete_data_deduc glyphicon glyphicon-trash' id="<?=$row->id?>" is_used = "<?=$is_used?>" <?= ($is_used > 0) ? " style='pointer-events:none;' disabled " : "" ?> ></a>
                    </td>
                    
                </tr>   
            <?
                    }
                }else{
            ?>
                <tr>
                    <td class="align_center"><i></i></td>
                    <td class="align_center"><i>No Data Exists..</i></td>
                    <td class="align_center"><i></i></td>
                </tr>
            <?}?>   
    </tbody>
</table>
<script>
$("#deductlist").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});

$(".addnewdeduction").click(function(){
    var form_data   =   {
                            
                            view    :   "deductionconfig"                            
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

$("#deductlist").delegate(".delete_data_deduc", "click", function(){
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
                model   : GibberishAES.enc("delDeduction", toks)
            }
           $.ajax({
                url: "<?=site_url("payroll_/loadmodelfunc")?>",
                type:"POST",
                data     :   form_data,
                success : function(msg){
                    if(msg == "Deduction successfully deleted."){
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
                loaddeducconfig();
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

$(".edit_data_deduc").click(function(){
    var form_data   =   {
                            id      : $(this).attr("id"),
                            view    :   "deductionconfig"                            
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


</script>