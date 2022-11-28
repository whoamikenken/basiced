<?php

/**
 * @author Max Consul
 * @copyright 2019
 */

$curr_date = date('Y-m-d');
$employeeid = $this->session->userdata('username');
$inputAttr = "";
if($status != "PENDING") $inputAttr = " readonly";
?>
<style type="text/css">
    .form_row{
        padding-bottom: 15px;
    }

    .fields{
        padding: 0px;
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
            <center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Edit Document Request</h3></b></center>
        </div>
        <div class="modal-body">
            <form id="process_form">
                <div class="form_row">
                    <input type="hidden" name="app_id" id="app_id" value="<?= $id ?>">
<!--                     <div class="form_row">
                      <legend><b>Fullname: <?= $fullname ?></b></legend><br>
                    </div> -->
                </div>
                <?php
                    if($this->session->userdata('usertype') == 'ADMIN'){
                        ?>
                            <div class="form_row">
                                <div class="fields">
                                    <div class="field_name col-md-4">
                                        <label class="align_left">Employee ID</label>
                                    </div>
                                    <div class="fields no-search col-md-8" >  
                                        <input type='text' class="form-control" name="employeeid" id="employeeid" value="<?= $empid ?>" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="form_row">
                                <div class="fields">
                                    <div class="field_name col-md-4">
                                        <label class="align_left">Employee Name</label>
                                    </div>
                                    <div class="fields no-search col-md-8" >  
                                        <input type='text' class="form-control" name="employeename" id="employeename" value="<?= $fullname ?>" disabled/>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                ?>
                <div class="form_row">
                    <div class="fields">
                        <div class="field_name col-md-4">
                            <label class="align_left">Date Request</label>
                        </div>
                        <div class="input_field input-group date col-md-8">
                            <input type='text' class="form-control" name="date_req" id="date_req" value="<?= $dateapplied ?>" disabled/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="fields">
                        <div class="field_name col-md-4">
                            <label class="align_left">Documents to be claimed</label>
                        </div>
                        <div class="fields col-md-8">
                            <div style="margin-bottom: 7px; margin-left: 0.7%"><input type="checkbox" class="cbox" name="documents" value="<?= $doc_requested ?>" checked disabled>&nbsp;&nbsp;<b><p style="display:inline;"><?= $doc_desc ?></p></b></div>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="fields">
                        <div class="field_name col-md-4">
                            <label class="align_left">Purpose</label>
                        </div>
                        <div class="fields col-md-8">
                           <textarea class="form-control" rows="4" name="purpose" value="<?= $reason?>" <?=$inputAttr?> ><?= $reason?></textarea>
                        </div>
                    </div>
                </div>
                <?php
                    if($this->session->userdata('usertype') != "EMPLOYEE"){
                ?>
                    <div class="form_row">
                        <div class="fields">
                            <div class="field_name col-md-4">
                                <label class="align_left">Personnel Remarks</label>
                            </div>
                            <div class="fields col-md-8">
                               <textarea class="form-control" rows="4" name="remarks" value="<?= $remarks?>" <?=$inputAttr?>  ><?= $remarks?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form_row">
                        <div class="fields">
                            <div class="field_name col-md-4">
                                <label class="align_left">Status</label>
                            </div>
                            <div class="fields no-search col-md-8" >  
                                <select class="select blue chosen" id="update_stat" name="update_stat">
                                    <?php foreach(Globals::documentStatusList() as $key => $desc): ?>
                                        <option value="<?= $key ?>" <?= ($key == $status) ? " selected" : "" ?> ><?= $desc ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form_row" id="dateto_claim" hidden>
                        <div class="fields">
                            <div class="field_name col-md-4">
                                <label class="align_left">Date to Claim</label>
                            </div>
                            <div class="input_field input-group date col-md-8">
                                <input type='text' class="form-control" name="dateclaim" id="dateclaim" value="<?= $curr_date ?>" <?=$inputAttr?> />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php
                    }
                ?>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="process_app">Submit</button>
        </div>
    </div>
</div>

<script src="<?=base_url()?>js/hr_setup/document_app.js">