<div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>EMPLOYMENT HISTORY</b></h4></div>
                <div class="panel-body">
                    <div class="row">
<!--                         <span class="col-md-2">
                            <span class="col-md-12 text-center"><b>Division Level</b></span>
                            <span id="currentMgmt" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->getemployeemlevel($management)?></span>
                        </span> -->
                        <span class="col-xs-12 col-md-2">
                            <span class="col-md-12 text-center"><b>Department</b></span>
                            <span id="currentDept" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->getemployeedepartment($deptid)?></span>
                        </span>
                        <span class="col-xs-12 col-md-1">
                            <span class="col-md-12 text-center"><b>Office</b></span>
                            <span id="currentOffice" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->getemployeeoffice($office)?></span>
                        </span>
                        <span class="col-xs-12 col-md-2">
                            <span class="col-md-12 text-center"><b>Employee Status</b></span>
                            <span id="currentEStatus" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->getemployeestatus($employmentstat)?></span>
                        </span>
                        <span class="col-xs-12 col-md-2">
                            <span class="col-md-12 text-center"><b>Position</b></span>
                            <span id="currentPos" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->showPosDesc($position)?></span>
                        </span>
                        <span class="col-xs-12 col-md-2">
                            <span class="col-md-12 text-center"><b>Start Date</b></span>
                            <span id="currentDatepos" class="col-md-12 text-center" style="margin-left: 0px;"><?=$datepos?></span>
                        </span>
                        <span class="col-xs-12 col-md-2">
                            <span class="col-md-12 text-center"><b>Date Resigned</b></span>
                            <span name="currentDateres" id="currentDateres"  class="col-md-12 text-center" style="margin-left: 0px;" dateSep="<?=$dateresigned2?>"><i>Present</i></span>
                        </span>
                        <span class="col-xs-12 col-md-2">
                            <span class="col-md-12 text-center"></span>
                            <span class="pull-right">
                                <a class='btn btn-primary edit_estat_history' mgmt="<?=$management?>" dept="<?=$deptid?>" office="<?= $office ?>" estat="<?=$employmentstat?>" pos="<?=$position?>" datepos="<?=$datepos?>" dateresigned="<?=$dateresigned2?>" resigned_reason="<?=$resigned_reason?>" href='#modal-view' data-toggle='modal' <?= ($ishidden) ? 'style="display: none;"': '' ?>><i class='glyphicon glyphicon-plus'></i></a>
                            </span>
                        </span>
                    </div>
                    <!-- div class="row">
                        <hr><b><i>&nbsp;&nbsp;History</i></b>
                    </div><br> -->
                    <!-- <div class="row">
                        <span class="col-md-2 text-center"><b>Division Level</b></span>
                        <span class="col-xs-12 col-md-2 text-center"><b>Department</b></span>
                        <span class="col-xs-12 col-md-2 text-center"><b>Office</b></span>
                        <span class="col-xs-12 col-md-1 text-center"><b>Employee Status</b></span>
                        <span class="col-xs-12 col-md-2 text-center"><b>Position</b></span>
                        <span class="col-xs-12 col-md-1 text-center"><b>Start Date</b></span>
                        <span class="col-xs-12 col-md-2 text-center"><b>Date Resigned</b></span>
                        <span class="col-xs-12 col-md-2"></span>
                    </div> -->
                    <div id="estatHistory">
                        <?php foreach ($employment_history as $key => $obj): ?>
                            <?php
                                if($obj->dateresigned == "0000-00-00"){
                                    $obj->dateresigned = "";
                                }
                                else{
                                    $obj->dateresigned;
                                }
                            ?>
                            <div class="row" style="padding-bottom: 2px;">
                                <span class="col-xs-12 col-md-2 text-center"><?=$obj->deptdesc?></span>
                                <span class="col-xs-12 col-md-1 text-center"><?=$obj->officedesc?></span>
                                <span class="col-xs-12 col-md-2 text-center"><?=$obj->statdesc?></span>
                                <span class="col-xs-12 col-md-2 text-center"><?=$obj->posdesc?></span>
                                <span class="col-xs-12 col-md-2 text-center"><?=$obj->dateposition?></span>
                                <span class="col-xs-12 col-md-2 text-center"><?=$obj->dateresigned?></span>
                                <span class="col-xs-12 col-md-2">
                                    <span class="pull-right">
                                        <a class='btn btn-info view_seperation_reason' dstatid="<?=$obj->id?>" href='#modal-view' data-toggle='modal' <?= ($ishidden) ? 'style="display: none;"': '' ?>><i class='glyphicon glyphicon-eye-open'></i></a>&nbsp;
                                        <a class='btn btn-warning delete_estat_history' dstatid="<?=$obj->id?>" <?= ($ishidden) ? 'style="display: none;"': '' ?>><i class='glyphicon glyphicon-trash'></i></a>        
                                    </span>
                                </span>
                            </div>

                        <?php endforeach; ?>
                    </div>

                    <div id="delete-alert" class="align_center hide">
                        <div><h5>Are You sure you want to delete <span id="chosen-select-row" class="text-error"></span> ?</h5></div>
                        <div>
                            <input type="hidden" class="hiddenid" />
                        </div>
                    </div>
                </div>
            </div>