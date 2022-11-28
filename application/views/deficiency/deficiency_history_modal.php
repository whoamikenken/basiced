<style>

.modal{
    left: 0;
    right: 0;
    margin: auto;
}
#clearanceHistory th{
    background: #0072c6;
    font-size: 14px;
    width: 10% !important;
     text-align: center;
}


</style>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;" >
                    <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                    <p>D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">Employee Clearance History</h3></b></center>
        </div>
        <div class="modal-body" id="clearanceHistory">
            <div class="row" style="padding: 5px;">
                <div tag='display'>
                <table class="table table-striped table-bordered table-hover" id="clearanceHistoryTable" width="100%">
                    <thead>
                        <tr>
                            <th>Concerned Office</th>
                            <th>Look For</th>
                            <th>Requirement</th>
                            <th>School Year</th>
                            <th>Remarks</th>
                            <th>Agreed SubmissionDate </th>
                            <th>Status</th>
                            <th>Date Completed</th>
                            <th>Added By</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php
                       if($d_list->num_rows() > 0){
                           foreach ($d_list->result() as $key => $row) {
                            $subdate = $row->submission_date != null && $row->submission_date != '0000-00-00' ? date('F d, Y',strtotime($row->submission_date)) : '';
                            $comdate = $row->date_completed != null && $row->date_completed != '0000-00-00' ? date('F d, Y',strtotime($row->date_completed)) : '';
                            ?>
                                <tr>
                                
                                <td><?=Globals::_e($this->extensions->getOfficeDescriptionReport($row->concerned_dept))?></td>
                                <td ><?=Globals::_e($row->lookfor)?></td>
                                <td ><?=Globals::_e($row->defdesc)?></td>
                                <td ><?=$row->sy?></td>
                                <td ><?=Globals::_e($row->remarks)?></td>
                                <td><?=($subdate != "00-00-0000" || $subdate != NULL)?$subdate:""?></td>
                                <td ><?=$row->is_completed==1?"CLEARED":"PENDING"?></td>
                                <td><?=($comdate != "00-00-0000" || $comdate != NULL)?$comdate:""?></td>
                                <td ><?=($this->extensions->getEmployeeName($row->user) ? Globals::_e($this->extensions->getEmployeeName($row->user)) : Globals::_e($row->user) )?></td>
                                <td><?=date('F d, Y',strtotime($row->date_created))?></td>
                                <!-- <td ><?=($row->status) ? "Confirmed" : "Not Confirmed"?></td> -->
                            </tr>
                        <?}
                    }
                    ?>
                </tbody>
                </table>
                <br>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            </div>  
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var table = $('#clearanceHistoryTable').DataTable({
            "aoColumnDefs": [
                { "bSortable": false, "aTargets": [ 0, 1, 2, 3,4,5,6,7,8,9 ] }
            ]
        });
    });
</script>