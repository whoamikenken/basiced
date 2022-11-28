
<?php 
/**
 * @author Kennedy Hipolitp
 * @2019
 * @Updated UI
 */

?>

<table class="table table-striped table-bordered table-hover" id="banklist">
    <thead>
        <tr>
            <th><a class="btn btn-primary" id="addnewbank" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">New Bank</a></th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th class="align_center">Code</th>
            <th class="align_center">Acount Number</th>
            <th class="align_center">Bank Name</th>
            <th class="align_center">Branch</th>
            <th class="align_center">Action</th>
        </tr>
    </thead>
    <tbody>  
            <?
                $query = $this->payroll->displayBankList();
                if($query->num_rows() > 0){
                    foreach($query->result() as $row){
                        $is_used = $this->payroll->isBankUsed($row->code);
            ?>
                <tr>
                    <td class="align_center"><?=$row->code?></td>
                    <td class="align_center"><?=$row->account_number?></td>
                    <td class="align_center"><?=$row->bank_name?></td>
                    <td class="align_center"><?=$row->branch?></td>
                    <td class="align_center">
                        <a class='btn btn-info edit_data glyphicon glyphicon-edit' id="<?=$row->code?>" data-toggle="modal" data-target="#myModal"></a>
                        <a class='btn btn-danger delete_data_bank glyphicon glyphicon-trash' id="<?=$row->code?>"  is_used = "<?=$is_used?>" <?= ($is_used > 0) ? " style='pointer-events:none;' disabled " : "" ?> ></a>
                    </td>
                </tr>   
            <?
                    }
                }
            ?>
                   
    </tbody>
</table>
<script>


$(".edit_data").click(function(){
    var form_data   =   {
                            code      : $(this).attr("id"),
                            view    :   "setup/bank_form",
                            job     : 'edit'                            
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

$("#addnewbank").click(function(){  
    alert
    $.ajax({
        url      : "<?=site_url('payroll_/payrollconfig')?>",
        type     : "POST",
        data     : {view   :   "setup/bank_form" , job: 'add'},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});
$(".delete_data_bank").click(function(){
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
            var form_data   =   {
                id      : $(this).attr("id"),
                model   : "delBank"
            }
            $.ajax({
               url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
               type     :   "POST",
               data     :   form_data,
               success  :   function(msg){
                // console.log(msg);
                if (msg == 3) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Bank has been deleted successfully',
                        showConfirmButton: true,
                        timer: 2000
                    });
                    loadbankconfig();
                    // $("#close").click();
                }
                else if(msg == 2){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Failed to delete data!',
                        showConfirmButton: true,
                        timer: 2000
                    });
                    return;
                }
                else if(msg == 1){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Bank is used in current cutoff.',
                        showConfirmButton: true,
                        timer: 2000
                    });
                    return;
                }
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
$("#banklist").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    /*"sDom": 'T<"clear"><"fg-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix"lfr>t<"fg-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix"ip>',*/
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
</script>