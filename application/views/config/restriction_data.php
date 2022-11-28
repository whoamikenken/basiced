<style type="text/css">
    .swal2-cancel{
    margin-right: 20px;
}
</style>
<table class="table table-striped table-bordered table-hover" id="EmpList">
    <thead style="background-color: #0072c6;">
        <tr>
            <th>&nbsp;</th>
            <th>Employee</th>
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
                    <td class="align_center"><a class='btn btn-danger delete_data glyphicon glyphicon-trash' id="delbtn" eid="<?=$row->employeeid?>" dfrom="<?=$row->datefrom?>" dto="<?=$row->dateto?>" ></a></td>
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
<script type="text/javascript">
    $(document).ready(function(){
    var table = $('#EmpList').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );

    $("#EmpList").on('click', '.delete_data', function(){
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
            deleteRow($(this), $(this).attr("eid"), $(this).attr("dfrom"), $(this).attr("dto"));

          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe.',
              'error'
            )
          }
        })
    // var mtable = $("#EmpList").find("tbody");
    // if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    // table
    //     .row( $(this).parents('tr') )
    //     .remove()
    //     .draw(false);
    //     deleteRow($(this), $(this).attr("eid"), $(this).attr("dfrom"), $(this).attr("dto"));
    });
});
</script>