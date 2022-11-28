<form id="frmleave">
    <div class="modal-dialog modal-lg">
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
                <center><b><h3 tag="title" class="modal-title">Seminar Application Details</h3></b></center>
            </div><br>
            <div class="modal-body">
                <div class="content">
                    <div class="row">
                        <input type="hidden" name="date_applied" value="<?= date("Y-m-d") ?>">
                        <input type="hidden" name="base_id" value="<?= isset($base_id) ? $base_id : '' ?>">
                        <div class="form_row">
                            <label class="field_name align_right" style="margin-left: -1%;">Employee</label>
                            <div class="field" style="padding-bottom: 10px;width: 75%;pointer-events: none;">
                                <select class="chosen col-md-6" id="employeeid" name="employeeid" disabled="">
                                    <?php foreach($employeelist as $row): ?>
                                        <option value="<?= $row["employeeid"] ?>" <?= ($row["employeeid"] == $applied_by) ? "selected" : "" ?> ><?= $row["employeeid"]." - ".$row["fullname"] ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Date From</label>
                                    <div class="col-md-8">
                                        <div class="input-group date" data-date="<?= isset($datesetfrom) ? $datesetfrom : "" ?>" data-date-format="yyyy-mm-dd">
                                            <input type="text" class="form-control" name="datesetfrom" value="<?= isset($datesetfrom) ? $datesetfrom : "" ?>" disabled="" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Date To</label>
                                    <div class="col-md-8">
                                        <div class="input-group date" data-date="<?= isset($datesetto) ? $datesetto : '' ?>" data-date-format="yyyy-mm-dd">
                                            <input type="text" class="form-control" name="datesetto" value="<?= isset($datesetto) ? $datesetto : '' ?>" disabled=""/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Time From</label>
                                    <div class="col-md-8">
                                        <div class='input-group time'>
                                            <input type='text' class="form-control" name="fromtime" value="<?= isset($timefrom) ? $timefrom : '' ?>" disabled="" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Time From</label>
                                    <div class="col-md-8">
                                        <div class='input-group time'>
                                            <input type='text' class="form-control" name="totime" value="<?= isset($timeto) ? $timeto : '' ?>" disabled="" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: 0px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar Category</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="category" style="display: inline;margin-left: 5px;width: 97%;" disabled="">
                                        <option value=""> - Select Seminar Category - </option>
                                        <?php
                                            $seminarList = Globals::seminarList();
                                            foreach($seminarList as $c=>$val){
                                                ?><option value="<?=$c?>" <?= (isset($category) && $category==$c) ? "selected" : "" ?> ><?=$val?></option><?    
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: 0px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar - Workshop/Training</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="seminar" style="display: inline;margin-left: 5px;width: 97%;" disabled="">
                                        <?php
                                            $educlevel = $this->extras->showreportseduclevel(' - Select POVEDAN SPIRITUAL and SPIRITUAL - ','PTS_PDP');
                                            foreach($educlevel as $c=>$val){
                                                ?><option value="<?=$c?>" <?= (isset($seminar) && $seminar==$c) ? "selected" : "" ?> ><?=$val?></option><?    
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Type of Seminar Title</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <textarea class="form-control" id="seminar_title" value="<?= isset($title) ? $title : '' ?>" style="width: 97%;height: 80px;margin-left: 5px;" disabled=""><?= isset($title) ? $title : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Organizer</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" class="form-control" name="organizer" value="<?= isset($organizer) ? $organizer : '' ?>" disabled="" style="margin-left: 5px;width: 97%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: 0px;padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Venue</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="venue" style="display: inline;margin-left: 5px;width: 35%;" disabled="">
                                        <option value="sample">Sample</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Location</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" name="location" value="<?= isset($location) ? $location : '' ?>" class="form-control" disabled="" style="margin-left: 5px;width: 97%;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Registration Fee</label>
                                    <div class="col-md-8">
                                        <input type="text" name="fee" value="<?= isset($fee) ? $fee : '' ?>" class="form-control" value="" disabled="" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Deadline of Registration</label>
                                    <div class="col-md-8">
                                        <div class="input-group date" data-date="<?= isset($datesetto) ? $datesetto : '' ?>" data-date-format="yyyy-mm-dd">
                                            <input type="text" name="deadline" class="form-control" value="<?= isset($deadline) ? $deadline : '' ?>" disabled="" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Other Remarks</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <textarea id="remarks" value="<?= isset($remarks) ? $remarks : '' ?>" class="form-control" style="width: 97%;height: 80px;margin-left: 5px;" disabled="" ><?= isset($remarks) ? $remarks : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<input type="hidden" id="site_url" value="<?=site_url()?>">
<script>
    $('.chosen').chosen();
</script>