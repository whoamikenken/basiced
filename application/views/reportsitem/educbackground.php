<?php 
 ?>

<form id='reportsform'>
<? if ($category == "LD") :?>
        <table class="table table-hover myTable">
        <thead>
        <tr>
            <th>
                <a class="btn btn-primary"  href="#modal-view" tag="add_eb" data-toggle="modal" category='<?=$category?>' displaydata='<?=$displaydata?>' style="margin-bottom: 10px; "><i class="icon glyphicon glyphicon-plus-sign" style="margin-right: 5px;"></i><b>Add New</b></a>
            </th>
        </tr>
          <tr style="background-color: #0072c6;">
            <th class="col-md-10"><b>Leave Category</b></th>
            <th class="col-md-2">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
            <?
            if(count($eb_list)>0){
                foreach ($eb_list as $key => $row) {?>
                    <tr> 
                        <td><?=Globals::_e(ucwords(strtolower(strtr($row->level, '^~', ')('))))?></td>
                        <td ><a class="btn btn-danger pull-right" tag="delete_eb" data-toggle="modal" delete_eb="<?=$row->ID?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-trash"></i></a>&nbsp;&nbsp;&nbsp;<a class="btn btn-info pull-right" href="#modal-view" tag="edit_eb" data-toggle="modal" name='edit_eb' style="margin-right: 10px;" edit_eb="<?=$row->ID?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-edit"></i></a></td>
                    </tr>
                <?}
            }else{?>    
                <tr>  
                    <td></td>
                    <td></td>
                </tr>
           <? }?>
        </tbody>
    </table>
<? elseif ($category <> "SCTT") :?>
<table class="table table-hover myTable">
            <thead >
                <tr>
                    <th>
                        <a class="btn btn-primary"  href="#modal-view" tag="add_eb" data-toggle="modal" category='<?=$category?>' displaydata='<?=$displaydata?>' style="margin-bottom: 10px; "><i class="icon glyphicon glyphicon-plus-sign" style="margin-right: 5px;"></i><b>Add New</b></a>
                    </th>
                </tr>
              <tr style="background-color: #0072c6;" width="100%">
                <th class="col-md-10" id="th_label"><b><?=(isset($th_label) ? $th_label : 'EDUCATIONAL LEVEL')?></b></th>
                <!-- <th class="col-md-3"><b>POINTS</b></th> -->
                <th class="col-md-2">&nbsp;</th>
              </tr>
            </thead>
            <tbody>
            	<?
                if(count($eb_list)>0){
                	foreach ($eb_list as $key => $row) {?>
                        <tr>
                            <td><?=Globals::_e(ucwords(strtolower(strtr($row->level, '^~', ')('))))?></td>
                            <!-- <td><?=$row->points?></td> -->
                            <td >
                                <a class="btn btn-danger pull-right"  tag="delete_eb" data-toggle="modal" delete_eb="<?=$row->ID?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-trash"></i></a>
                                <a class="btn btn-info pull-right" href="#modal-view" tag="edit_eb" data-toggle="modal" name='edit_eb' style="margin-right: 10px;" edit_eb="<?=$row->ID?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-edit"></i></a>
                            </td>
                        </tr>
                	<?}
                }else{?>
                    <tr>  
                        <td></td>
                        <td></td>
                    </tr>
               <? }?>
            </tbody>
        </table>
        <? elseif ($category <> "OC" && $category <> "SCTT") :?>
            <table class="table table-hover myTable">
            <thead>
              <tr style="background-color: #0072c6;" width="100%">
                <th class="col-md-8" id="th_label"><b><?=(isset($th_label) ? $th_label : 'EDUCATIONAL LEVEL')?></b></th>
                <!-- <th class="col-md-8"><b>POINTS</b></th> -->
                 <!-- <th class="col-md-2">&nbsp;</th> -->
                <th class="col-md-2">&nbsp;</th>
              </tr>
            </thead>
            <tbody>
                <?
                if(count($eb_list)>0){
                    foreach ($eb_list as $key => $row) {?>
                        <tr> 
                            <td><?=Globals::_e(ucwords(strtolower(strtr($row->level, '^~', ')('))))?></td>
                            <!-- <td><?=$row->points?></td> -->
                            <td ><a class="btn btn-danger pull-right"  tag="delete_eb" data-toggle="modal" delete_eb="<?=$row->id?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-trash"></i></a><a style = "margin-right: 10px;" class="btn btn-info pull-right" href="#modal-view" tag="edit_eb" data-toggle="modal" name='edit_eb'
                             edit_eb="<?=$row->id?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-edit"></i></a>
                            </td>
                        </tr>
                    <?}
                }else{?>    
                    <tr>  
                        <td></td>
                        <td></td>
                        <!-- <td></td> -->
                    </tr>
               <? }?>
            </tbody>
        </table>
    <? else : ?>
        <table class="table table-hover othertables">
            <thead>
                <tr>
                    <th>
                        <a class="btn btn-primary"  href="#modal-view" tag="add_eb" data-toggle="modal" category='<?=$category?>' displaydata='<?=$displaydata?>' style="margin-bottom: 10px; "><i class="icon glyphicon glyphicon-plus-sign" style="margin-right: 5px;"></i><b>Add New</b></a>
                    </th>
                </tr>
              <tr style="background-color: #0072c6;">
                <th class="col-md-3"><b>SUBJECT CODE</b></th>
                <th class="col-md-4"><b>DESCRIPTION</b></th>
                <th class="col-md-3"><b>REMARKS</b></th>
                <th class="col-md-2">&nbsp;</th>
              </tr>
            </thead>
            <tbody>
                <?
                if(count($eb_list)>0){
                    foreach ($eb_list as $key => $row) {?>
                        <tr> 
                            <td><?=$row->subj_code?></td>
                            <td><?=Globals::_e($row->description)?></td>
                            <td><?=Globals::_e($row->remarks)?></td>
                            <td ><a class="btn btn-danger pull-right"  tag="delete_eb" data-toggle="modal" delete_eb="<?=$row->id?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-trash"></i></a><a style = "margin-right: 10px;" class="btn btn-info pull-right" href="#modal-view" tag="edit_eb" data-toggle="modal" name='edit_eb'
                             edit_eb="<?=$row->id?>" displaydata='<?=$displaydata?>' category='<?=$category?>'><i class="icon glyphicon glyphicon-edit"></i></a>
                            </td>
                        </tr>
                    <?}
                }else{?>
                    <tr>  
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
               <? }?>
            </tbody>
    <? endif; ?>
</table>
</form>

<script type="text/javascript">
    $(document).ready( function () {
        // $('.myTable').DataTable();

        ///< end for batch approving
        $('.myTable').DataTable().destroy();
        $(".myTable").dataTable();
    } );
    

    // $(document).ready( function () {
    //     // $('.othertable').DataTable();
    //     ///< end for batch approving
    //     $('.othertable').DataTable().destroy();
    //     $(".othertable").dataTable({
    //         "sPaginationType": "full_numbers",
    //         "oLanguage": {
    //                          "sEmptyTable":     "No Data Available.."
    //                      },
    //         "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    //         "columnDefs": [{ "orderable": false , "targets": [1,2,3] }]
    //     });
    // } );

    $(document).ready( function () {
        // $('.othertable').DataTable();
        ///< end for batch approving
        $('.othertables').DataTable().destroy();
        $(".othertables").dataTable();
    } );


</script>




