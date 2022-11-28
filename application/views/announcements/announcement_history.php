<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#offbus tr td,#offbus tr th{
    text-align: center;
}
#offbus tr th{
    background-color: #0072c6;
    color: black;
}
input[name=mar]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}
.td-limit {
    max-width: 100%;
    text-overflow: ellipsis;
    white-space: nowrap;
    overflow: hidden;
}
/*table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc, table.dataTable thead .sorting_asc_disabled, table.dataTable thead .sorting_desc_disabled {
    position: relative!important;
}*/
#offbus {
  table-layout: fixed;
  width: 100% !important;
}
#offbus td,
#offbus th{
  width: auto !important;
}
</style>
<table class="table table-striped table-bordered table-hover" id="offbus" style="width: 100%;">
    <thead >
        <tr>
            <th rowspan="2">&nbsp;</th>
            <th rowspan="2">Departments</th>
            <th colspan="2">Inclusive Dates</th>
            <th colspan="2">Time</th>
            <th rowspan="2">Event</th>
            <th rowspan="2">Venue</th>
            <th rowspan="2">Posted Until</th>
            <th rowspan="2">Date Created</th>
        </tr>
        <tr>
            <th>From</th>
            <th>To</th>
            <th>Start</th>
            <th>End</th>
        </tr>
    </thead>
    <tbody>
        <?
        foreach ($a_list as $key => $row) {?>
            <tr>
                <td style="width: 20%;">
                    <a class="btn btn-info editrequest" id="editrequest" href="#" idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                    <a class="btn btn-danger delbtn" id="delrequest" href="#" idnumber="<?=$row->id?>" ><i class='glyphicon glyphicon-trash'></i></a>
                </td>
                <!-- <td style="width: 10%;" class="td-limit"></td> -->
                <?php 
                    $departmentDisplay = "";
                    $deptData = explode(',' , $row->deptids);
                    if (count($deptData) == $totalDept) $departmentDisplay = "All Department";
                    else $departmentDisplay = $row->deptids;
                ?>
                <td style="width: 10%;" class="td-limit"><?= $departmentDisplay ?></td>
                <td style="width: 10%;"><?= $row->datefrom == "0000-00-00" ? "" : date('F d, Y',strtotime($row->datefrom))?></td>
                <td style="width: 10%;" ><?=$row->datefrom == "0000-00-00" ? "" : date('F d, Y',strtotime($row->dateto))?></td>
                <td style="width: 10%;" ><?= date('h:i A',strtotime($row->timefrom))?></td>
                <td style="width: 10%;" ><?= date('h:i A',strtotime($row->timeto))?></td>
                <td style="width: 10%;" ><?=Globals::_e($row->event)?></td>
                <td style="width: 10%;" ><?=Globals::_e($row->venue)?></td>
                <td style="width: 10%;"><?=$row->posted_until == "0000-00-00" ? "" : date('F d, Y',strtotime($row->posted_until))?></td>
                <td style="width: 10%;"><?=date('F d, Y',strtotime($row->date_created))?></td>
            </tr>
        <?}?>
    </tbody>
</table>

<div class="modal fade" id="myModal" data-backdrop="static"></div>

<script type="text/javascript">
    var toks = hex_sha512(" ");
// $(document).ready(function(){
//     var table = $('#offbus').DataTable({
//         responsive: true
//     });
//     new $.fn.dataTable.FixedHeader( table );
// });
$("#offbus").dataTable();


 $(".editrequest").unbind().click(function(){
        var id = $(this).attr("idnumber");
        var form_data = {
            code:  GibberishAES.enc(id , toks),
            toks:toks
        };

        $('.department_list option').prop('selected', false);
        $.ajax({
            url: "<?=site_url('announcements_/editAnnouncement')?>",
            type: "POST",
            data: form_data,
            dataType: "JSON",
            success: function(response){
                $("#cancel_edit").show();
                $("input[name='ids']").val(response.id);
                $("input[name='datesetfrom']").val(response.datefrom);
                $("input[name='datesetto']").val(response.dateto);
                $("input[name='tfrom']").val(response.timefrom);
                $("input[name='tto']").val(response.timeto);
                $("input[name='venue']").val(response.venue);
                $("#event").val(response.event);
                var myarr = response.deptid.split(",");
                console.log(myarr);
                myarr.forEach(function(item, index) {
                    $('.department_list option[value="'+item+'"]').prop('selected', true);
                });
                $("#departments").trigger("chosen:updated");
                $("input[name='posted_until']").val(response.posted_until);
            }
        });  

    });

    $(document).unbind().on('click',".delbtn",function(e){
        var id = $(this).attr("idnumber");

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
                url:"<?=site_url("announcements_/actionAnnouncement")?>",
                type:"POST",
                data:{
                   code: GibberishAES.enc($(this).attr("idnumber")  , toks),
                   job:    GibberishAES.enc("delete" , toks),
                   toks:toks
                },
                success: function(msg){
                    msg = msg.replace(/["']/g, "");
                    $("#modalclose").click();
                    $(".inner_navigation .main li .active a").click(); 
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    });
                    loadAnnouncementHistory();
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
</script>
 