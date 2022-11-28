<?php

/**
 * @author Max Consul
 * @copyright 2019
 */

$curr_date = date('Y-m-d');
$employeeid = $this->session->userdata('username');
?>
<style type="text/css">
    .input_field{
        padding: 0px;
    }

</style>
<div class="modal-dialog modal-md">

  <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                    <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
                    <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Add Document Request</h3></b></center><center>
        </div>
        <div class="modal-body">
            <form id="doc_form">
                <div class="form_row">
                    <input type="hidden" id="employee" name="employee" value="<?= $employeeid ?>">
                    <div class="field" style="margin-left: 0px; padding-top: 15px;">
                        <div class="field_name col-md-4">
                            <label class="align_left">Date Requested</label>
                        </div>
                        <div class="input_field input-group date col-md-8">
                            <input type='text' class="form-control" name="date_req" id="date_req" value="<?= $curr_date ?>" readonly/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="field" style="margin-left: 0px; padding-top: 15px;">
                        <div class="field_name col-md-4">
                            <label class="align_left">Select documents to be request</label>
                        </div>
                        <div class="input_field col-md-8">
                            <?php foreach($doc_setup as $value): ?>
                            <div style="margin-bottom: 7px;"><input type="checkbox" class="cbox docsbox" name="documents" value="<?= $value['code']?>">&nbsp;&nbsp;<b><p style="display:inline;"><?= $value['description'] ?></p></b></div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="field" style="margin-left: 0px; padding-top: 15px;">
                        <div class="field_name col-md-4">
                            <label class="align_left">Purpose</label>
                        </div>
                        <div class="input_field col-md-8">
                            <textarea class="form-control" rows="4" id="purpose" name="purpose"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="apply_emp">Submit</button>
        </div>
    </div>
</div>
<script src="<?=base_url()?>js/hr_setup/document_app.js">