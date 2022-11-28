<?php

/**
 * @author Max Consul
 * @copyright 2019
 */

$curr_date = date('Y-m-d');
?>

<style type="text/css">
    .form_row{
        padding-bottom: 15px;
    }
</style>
<div class="modal-dialog">

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
            <center><h3 class="modal-title" style="margin-left: 5%;">Add Document Request</h3></center>
        </div>
        <div class="modal-body">
            <br>
            <form id="doc_form">
                <div class="form_row">
                    <label class="field_name align_right">Employees</label>
                    <div class="field">
                        <select class="chosen col-md-6" name="employee" id="employee">
                            <option value=""> Select employee </option>
                            <?php foreach($emp_list as $val): ?>
                                <option value="<?= $val['employeeid']?>"><?= $val['fullname']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Documents</label>
                    <div class="field">
                        <select class="chosen col-md-6" name="documents" id="documents">
                            <option value=""> Select document </option>
                            <?php foreach($doc_setup as $val): ?>
                                <option value="<?= $val['code']?>"><?= $val['description']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Date Requested</label>
                    <div class="field">
                        <div class="input_field input-group date">
                            <input type='text' class="form-control  col-md-8" name="date_req" id="date_req" value="<?= $curr_date ?>"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Purpose</label>
                    <div class="field">
                       <textarea class="col-md-8 form-control" rows="4" name="purpose" id="purpose"></textarea>
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