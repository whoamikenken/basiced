<?php

/**
 * @author Justin
 * @copyright 2015
 */

$data = $this->extras->viewCutOff($dfrom,$dto);

foreach($data as $row){

?>
<tr>
    <td class="align_center">
        <a key="<?=$row->ID?>" name="deletecutoff" class="btn btn-danger">
        <i class="icon glyphicon glyphicon-trash"></i></a>
        <a href="#modal-view" key="<?=$row->ID?>" data-toggle="modal" name="editcutoff" class="btn btn-info">
        <i class="icon glyphicon glyphicon-edit"></i></a>
    </td>
    <td><?=date('F d, Y',strtotime($row->CutoffFrom))?></td>
    <td><?=date('F d, Y',strtotime($row->CutoffTo))?></td>
    <td><?=$row->schedule;?></td>
    <td><?=$row->quarter;?></td>
    <td><?=date('F d, Y',strtotime($row->startdate))?></td>
    <td><?=date('F d, Y',strtotime($row->enddate))?></td>
    <td><?=date('F d, Y',strtotime($row->ConfirmFrom))?></td>
    <td><?=date('F d, Y',strtotime($row->ConfirmTo))?></td>
    <!-- <td class="align_center"><?=( $row->TPostedDate ? date('F d, Y h:i A',strtotime($row->TPostedDate)) : "")?></td> -->
    <!-- <td class="align_center"><?=( $row->NTPostedDate ? date('F d, Y h:i A',strtotime($row->NTPostedDate)) : "")?></td> -->

</tr>
<?
}
?>
<script>
$("a[name='editcutoff']").click(function(){
    $("#modal-view").find("h3[tag='title']").html("Edit Cut-Off"); 
    $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
    $("#modal-view").find(".err").remove();
    $("#button_save_modal").text("Save");
    $.ajax({
        url: "<?=site_url("process_/editcutoff")?>",
        type: "POST",
        data: {dkey : $(this).attr("key")},
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);

        }
    }); 
});
$("a[name='deletecutoff']").click(function(){
    
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

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
            $.ajax({
                url: "<?=site_url("process_/deletecutoff")?>",
                type: "POST",
                data: {dkey : $(this).attr("key")},
                success: function(res){
                    if(res == 1){
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: "Unable to delete cutoff. Already processed a attendance.",
                            showConfirmButton: true,
                            timer: 1000
                        });
                    }
                    else{
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: "Cutoff has been deleted.",
                            showConfirmButton: true,
                            timer: 1000
                        });
                    }
                    setTimeout(function(){ 
                        loadContent();
                    }, 1500);
                }
            });

        } else if (
            result.dismiss === Swal.DismissReason.cancel
        ) {
        swalWithBootstrapButtons.fire(
                'Cancelled',
                'Cutoff is safe.',
                'error'
            )
        }
    });

});

function loadContent(){
    var loading = $("#loading").html(); /*loading gif*/
    var form_data = {
                        cat: $("#category").val(),
                        cutoff: $("#cutoff").val(),
                        view: "process/displaycutoff"
    };
    if($("#category").val() != ""){
        $("#contents").show();
        $("#contents").html(loading);
        $.ajax({
            url: "<?=site_url('main/siteportion')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#contents").html(msg);
            }
        });
    }
}
</script>
