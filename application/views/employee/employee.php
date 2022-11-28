<style type="text/css">
    .pointer{
        cursor:pointer;
    }

    .typebox{
        -webkit-transform: scale(1.5); 
        padding-left: 0px; 
        margin-left: -50px;
        margin-top: 5px; 
    }
    h4.media-heading {
        font-weight: bold;
    }
</style>
<?php
    $campus_list = $this->extras->getCampusDescription();
?>
<div id="content">
    <div class="animated fadeIn delay-1s">
        <br><br>
        <?if($this->session->userdata('usertype') != "PAYROLL"){?>
            <a href="#" class="btn btn-primary" id="addnewemployee" style="margin-left: 17px;"><i class="glyphicon glyphicon-plus-sign"></i> Add New</a> 
            <a href="#" class="btn btn-primary" id="sync_api_emp" style="float: right;margin-right: 20px;"><i class="glyphicon glyphicon-cloud"></i> Sync employee to ALLCARD</a> 
            <span id="loading_syncemp" style="display: none;float: right;"><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</span>
           <!-- <a href="#" class="btn btn-primary" id="sync_api_stud" style="float: right;margin-right: 20px;"><i class="glyphicon glyphicon-cloud"></i> Sync student to ALLCARD</a> -->
        <?}?>
        <style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
.panel > .panel-heading {
    background-color: #0072c6;
    color: black;
}
</style>
        <div class="widgets_area">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-heading"><h4><b>EMPLOYEE 201 FILE</b></h4></div>
                            <div class="panel-body" id="201filter">
                               <br>
                               <div class="col-md-6">
                                <div class="form-row" style="display: none;">
                                    <div class="col-md-12" >
                                        <label class="field_name col-md-3">Campus :</label>
                                        <div class="field col-md-9" >
                                            <select class="form chosen-select" id="campus" name="campus">
                                                <?php
                                                    if(count($campus_list) > 1){
                                                        ?>
                                                            <option value="">All Campus</option>
                                                        <?php
                                                    }    
                                                ?>
                                                <?php foreach ($campus_list as $key => $value): ?>
                                                    <option value="<?=$key?>"><?=$value?></option>
                                                <?php endforeach; ?>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <br><br>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <label class="field_name col-md-3">Status :</label>
                                        <div class="field col-md-9">
                                            <select class="chosen-select col-md-4" name="status" id="status">
                                                <option value="all">All</option>
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br><br>    
                                <div class="form-row" >
                                    <div class="col-md-12" id="edept" >
                                        <label class="field_name col-md-3">Type :</label>
                                        <div class="field col-md-9">
                                            <select class="chosen-select" id="teachingType" name="teachingType">
                                                <option value="">All</option>
                                                <option value="teaching">Teaching</option>
                                                <option value="nonteaching">Non Teaching</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <br><br>
                                <div class="form-row" >
                                    <div class="col-md-12" id="edept" >
                                        <label class="field_name col-md-3">Employee :</label>
                                        <div class="field col-md-9">
                                            <select class="chosen-select col-md-4" name="employeeid" id="employeeid">
                                                <option value="">All Employee</option>
                                                <?
                                                $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")));
                                                foreach($opt_type as $val){
                                                    ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . " " . $val['fname'] . " " . $val['mname'])?></option><?    
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                 
                                   
                            </div>
                            <div class="col-md-6">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <label class="field_name col-md-3">Department :</label>
                                        <div class="field col-md-9">
                                            <select class="form chosen-select" id="department" name="department"><?=$this->extras->getDeptpartment()?></select>
                                        </div>
                                    </div>
                                </div> 
                                <br><br>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <label class="field_name col-md-3">Office :</label>
                                        <div class="field col-md-9">
                                            <select class="form chosen-select" id="office" name="office"><?=$this->extras->getOffice()?></select>
                                        </div>
                                    </div>
                                </div> 
                                <br><br>
                                <div class="form-row" >
                                    <div class="col-md-12">
                                        <label class="field_name col-md-3">Employment&nbsp;Status&nbsp;:</label>
                                        <div class="field col-md-9">
                                            <select class="chosen-select col-md-4" name="empstat" id="empstat">
                                                <?php
                                                  $empstatuses = $this->extras->showemployeestatus('All Employment Status');
                                                  foreach ($empstatuses as $key => $item) {
                                                    ?>
                                                    <option value='<?=$key?>'><?= ucfirst (strtolower ($item)); ?></option>
                                                    <?php
                                                  }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <br><br>
                                <div class="form-row" >
                                    <div class="col-md-12" >
                                        <div class="col-md-12">
                                            <button class="btn btn-success" id="search" name="search" style="float: right; margin-bottom: 17px; padding-left: 20px; padding-right: 20px;">Search</button>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="panel user_datatable" hidden>
                        <div class="panel-heading"></div>
                        <div class="panel-body" id="empList">
                            <table id="user_datatable" class="table table-striped table-bordered table-hover" style="width: 100%!important">
                                <thead>
                                    <tr>
                                        <td class="align_left">EMPLOYEE 201</td>
                                    </tr>
                                </thead>   
                                <tbody ></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
</div>           
    <div id="add_employee" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="media">
                        <div class="media-left">
                            <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                        </div>
                        <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                            <h4 class="media-heading" style="font-weight: bold; font-family: Avenir;">Pinnacle Technologies Inc.</h4>
                            <p style="font-family:Avenir; margin-top: -1%;">D`Great</p>
                        </div>
                    </div>
                    <center><b><h3 tag="title" class="modal-title">Add New Employee</h3></b></center>
                </div>
                <div class="modal-body">
                    <form id="form_employee">
                        <div class="form-group">
                            <label class="field_name align_right">Employee ID</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                                <input class="form-control isrequired" name="employeeid" type="text">
                                <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                            </div>
                            <span style="color: red;display: none;" id="warning">&nbsp;&nbsp;Employee ID already exist!</span>
                        </div>
                        <div class="form-group">
                            <label class="field_name align_right">Last Name:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                                <input class="form-control isrequired" name="lname" id="lname" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="field_name align_right">First Name:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                                <input class="form-control isrequired" name="fname" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="field_name align_right">Middle Name:</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                                <input class="form-control isrequired" name="mname" type="text">
                            </div>
                        </div>
                        <div class="form-group" style="<?= (count($campus_list) > 1) ? '' : 'pointer-events: none;'; ?>">
                            <label class="field_name align_right">Campus:</label>
                                <select class="form chosen-select" id="campus_code" name="campus_code">
                                    <?php
                                        if(count($campus_list) > 1){
                                            ?>
                                                <option value="">Select Campus</option>
                                            <?php
                                        }    
                                    ?>
                                    <?php foreach ($campus_list as $key => $value): ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php endforeach; ?>
                                    
                                </select>
                        </div>
                        <div class="form-group">
                            <label class="field_name align_right">Teaching Type: &nbsp;&nbsp;&nbsp;</label>
                               Teaching:  &nbsp;&nbsp;&nbsp;<input type="checkbox" name="teachingtype" id="teaching" value="teaching" class="typebox aimstnt"> &nbsp;&nbsp;&nbsp;
                               Non teaching: &nbsp;&nbsp;&nbsp; <input type="checkbox" name="teachingtype" id="nonteaching" value="nonteaching" class="typebox aimstnt">
                        </div>

                        <div class="form-group">
                            <label class="field_name align_right">Add user to Aims: &nbsp;&nbsp;&nbsp;</label>
                               <input type="checkbox" name="aimcheckbox" value="1" class="typebox"> &nbsp;&nbsp;&nbsp;
                        </div>

                        <div class="form-group" id="loading" style="display: none;"><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">Close</a>
                    <a href="#" id="save_emp" class="btn btn-success">Save</a>
                </div>
            </div>
        </div>
    </div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="<?=base_url()?>js/employee/employeelist.js"></script>
<script>
    $('.aimstnt').click(function(){     
          $('.aimstnt').not(this).attr('checked', false);          
    });
</script>