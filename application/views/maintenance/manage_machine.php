<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                    <p>D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">Modal Header</h3></b></center>
        </div>
        <div class="modal-body">
            <form id = "machine_form">
                <div class="form_row">
                    <label class="field_name align_right">Terminal</label>
                    <div class="field">
                        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
                        <input type="text" name="username" class="span4" value="<?= isset($username) ? $username : '' ?>"/>
                    </div>
                </div>
                 <div class="form_row">
                    <label class="field_name align_right">Campus</label>
                    <div class="field" style="margin-top: 8px";>
                        <select class="chzn-select span4" multiple name="campus_allowed[]" id="campus_allowed">
                            <?php foreach($campus_list as $key => $value): ?>
                                <option value="<?= $key ?>" <?= (in_array($key, $campus_allowed)) ? " selected" : "" ?> ><?= $value ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Building</label>
                    <div class="field" style="margin-top: 8px";>
                        <select class="chzn-select span4" multiple name="campus_allowed[]" id="campus_allowed">
                            <?php foreach($campus_list as $key => $value): ?>
                                <option value="<?= $key ?>" <?= (in_array($key, $campus_allowed)) ? " selected" : "" ?> ><?= $value ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Floor</label>
                    <div class="field" style="margin-top: 8px";>
                        <select class="chzn-select span4" multiple name="employee_allowed[]" id="employee_allowed">
                            <?php foreach($emplist as $key => $value): ?>
                                <option value="<?= $key ?>" <?= (in_array($key, $employee_allowed)) ? " selected" : "" ?> ><?= $value ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Password</label>
                    <div class="field">
                        <input type="password" name="password" class="span4" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Re-Type Password</label>
                    <div class="field">
                        <input type="password" name="password" class="span4" value=""/>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="add_gate_acc">Save</button>
        </div>
    </div>

</div>
<script src="<?=base_url()?>js/terminal_setup/terminal_manage.js"></script>