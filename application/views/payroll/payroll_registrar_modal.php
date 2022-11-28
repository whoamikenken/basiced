<?php
/**
 * @author justin (with e)
 * @copyright 2018
 */
?>
<style>
.modal-title{
    color: #9E488F
}
.modal{
    width: 40%;
    left: 0;
    right: 0;
    margin: auto;
}
.good{
    color: green;
    font-weight: bold;
}
.bad{
    color: red;
    font-weight: bold;
}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td><h4 class="modal-title"><strong>Payroll Registrar Report</strong></h4></td>
                </tr>
            </table>
        </div>
        <div class="modal-body" id="leave_app_view">
        
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>