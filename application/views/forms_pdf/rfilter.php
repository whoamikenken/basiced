<?php
/**
 * @author Justin
 * @copyright 2016
 */

$CI =& get_instance();
$CI->load->model('utils');
$dfrom = $dto = $datetoday = date("d-m-Y");
$emplist   = $CI->utils->getEmplist('','','Select employee...');
$CI->load->model('leave');
$types = $CI->leave->getRequestType('','','1');
if(isset($cutoff)) list($dfrom, $dto) = explode(",", $cutoff);
?>

<style>

  fieldset.group  { 
    margin: 0; 
    padding: 0; 
    margin-bottom: 1.25em; 
    padding: .125em; 
  } 

  fieldset.group legend { 
    margin: 0; 
    padding: 0; 
    font-weight: bold; 
    margin-left: 20px; 
    font-size: 100%; 
    color: black; 
  } 


  ul.checkbox  { 
    margin: 0; 
    padding: 0; 
    margin-left: 20px; 
    list-style: none; 
  } 

  ul.checkbox li input { 
    margin-right: .25em; 
  } 

  ul.checkbox li { 
    border: 1px transparent solid; 
    display:inline-block;
    width:12em;
  } 

  ul.checkbox li label { 
    margin-left: ; 
  } 

</style>

<form id="myFrm">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
           <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">Filter</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
              <br>
                <?if(in_array($rtype,array("personalroster","personalrosterxls"))){?>
                  <div class="form_row">
                      <label class="field_name align_right">Status By</label>
                      <div class="field">
                           
                         <select class="chosen" name="isactive">
                          <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                        </select>
                      </div>
                </div>
              <div class="form_row" style="display: none;">
                              <label class="field_name align_right">Division Level</label>
                              <div class="field">
                                    <select class="chosen col-md-4" id="division" name="division">
                        <option value="">All Division</option>
                                        <?
                        $opt_management = $this->extras->showManagement();
                        foreach($opt_management as $c=>$val){
                        ?><option value="<?=$c?>"><?=$val?></option><?
                        }
                        ?>
                                    </select>
                              </div>
                        </div>
              <div class="form_row">
                              <label class="field_name align_right">Department</label>
                              <div class="field">
                                    <select class="chosen col-md-4" id="department" name="department">
                        <option value="">All Department</option>
                        <?
                        $opt_department = $this->extras->showdepartment();
                        foreach($opt_department as $c=>$val){
                        ?><option value="<?=$c?>"><?=$val?></option><?
                        }
                        ?>
                                    </select>
                              </div>
                        </div>
                        <div class="form_row">
                              <label class="field_name align_right">Employee</label>
                              <div class="field">
                                    <select class="chosen col-md-4" id="employee" name="employee">
                                        <option value="">All Employee</option>
                        <?
                                            foreach ($emplist as $code => $desc) {?>
                                                <option value="<?=$code?>"><?=$desc?></option>
                                            <?}
                                        ?>
                                    </select>
                              </div>
                        </div>
                        <div class="form_row" style="display: none;">
                              <label class="field_name align_right">Select&nbsp;Campus</label>
                              <div class="field">
                                    <select class="chosen col-md-4" id="campus" name="campus">
                                        <option value="">All Campus</option>
                                        <?=$this->extras->getCampuses()?>
                                    </select>
                              </div>
                        </div> 
                        <div class="form_row" >
                          <label class="field_name align_right">&nbsp;</label>
                          <div class="field">
                              <input type='checkbox' id='selectall'>&nbsp; Select All
                          </div>
                        </div>   
                        <br><br>
                        <div class="form_row">
                            <?=$this->reports->showEmpDetailCols("General Information")?>
                            <?=$this->reports->showEmpDetailCols("Identification Numbers")?>
                            <?=$this->reports->showEmpDetailCols("Employee Information")?>
                            <?=$this->reports->showEmpDetailCols("Employment Details")?>
                            <?=$this->reports->showEmpDetailCols("Family Members")?>
                            <?=$this->reports->showEmpDetailCols("Personal Information")?>
                        </div>

                <?}?>
                <?if(in_array($rtype,array("headcount"))){
                        $educlevel = $this->extras->showreportseduclevel('','EB');
                        $statuslevel = $this->extras->showstatusdescription();
                        unset($educlevel['']);
                    ?>

                          <div class="form_row">
                                <label class="field_name align_right">Status</label>
                                <div class="field">
                                  <div class="col-md-10">
                                      <select class="chosen" name="isactive">
                                        <option value="">All Status</option>
                                        <option value="1" selected>Active</option>
                                        <option value="2">Inactive</option>
                                      </select>
                                  </div>
                                </div>
                          </div> 


                          <div class="form_row">
                                <label class="field_name align_right">Teaching</label>
                                <div class="field">
                                  <div class="col-md-10">
                                      <select class="chosen" name="tnt" id="tnt">
                                              <option value="">All Types</option>
                                            <?=$this->reports->employeetype()?>
                                        </select>
                                  </div>
                                </div>
                          </div> 

                          <!-- <div class="form_row">
                                <label class="field_name align_right">Department</label>
                                <div class="field">
                                  <div class="col-md-10">
                                      <select class="chosen" name="deptid" id="deptid">
                                            <option value="">All Department</option>
                                            <?
                                            $opt_department = $this->extras->showdepartment();
                                            foreach($opt_department as $c=>$val){
                                            ?><option value="<?=$c?>"><?=$val?></option><?
                                            }
                                            ?>
                                        </select>
                                  </div>
                                </div>
                          </div>  -->

                          <div class="form_row">
                                <label class="field_name align_right">Office</label>
                                <div class="field">
                                  <div class="col-md-10">
                                      <select class="chosen" name="officeid" id="officeid">
                                          <option value="">All Office</option>
                                          <?
                                          $opt_department = $this->extras->showoffice();
                                          foreach($opt_department as $c=>$val){
                                          ?><option value="<?=$c?>"><?=$val?></option><?
                                          }
                                          ?>
                                      </select>
                                  </div>
                                </div>
                          </div> 

                          <!-- <div class="form_row">
                                <label class="field_name align_right">Select Campus</label>
                                <div class="field">
                                  <div class="col-md-10">
                                      <select class="chosen col-md-4" id="campus" name="campus">
                                          <option value="">All Campus</option>
                                          <?=$this->extras->getCampuses()?>
                                      </select>
                                  </div>
                                </div>
                          </div>  
                          <div class="form_row">
                                <label class="field_name align_right">As of Date</label>
                                <div class="field">
                                  <div class="col-md-10">
                                    <div class='input-group date' id="dfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                      <input type='text' class="form-control" size="16" name="dfrom" type="text" value="<?=$datetoday?>"/>
                                      <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                  </div>
                                  </div>
                                </div>
                          </div> -->
                          <div class="form_row" id="category">
                                <label class="field_name align_right">Sort By</label>
                                <div class="field">
                                  <div class="col-md-10">
                                    <select class="chosen col-md-4" name="status" id="status">
                                        <option value="bystat">Employment Status</option>
                                        <option value="byedu">Educational Background</option>
                                    </select>
                                  </div>
                                </div>
                          </div>
                          <br>
                          <div class="form_row educ" style="margin-left: 50px;display: none" >
                              <b>With Educational Background:</b><br>
                              <?
                                  foreach ($educlevel as $key => $desc) {?>
                                       &nbsp;&nbsp;<input type="checkbox" name="educlevel[]" value="<?=$key?>"> <?=$desc?><br>
                                  <?}
                              ?>
                            &nbsp;&nbsp;<input type="checkbox" name="licensed" value="prc"> LICENSED
                          </div>
                          <div class="form_row stat" style="margin-left: 50px;display: none">
                                <b>By Employment Status:</b><br>
                                <?
                                    foreach ($statuslevel as $key => $desc) {?>
                                         &nbsp;&nbsp;<input type="checkbox" name="statuslevel[]" value="<?=$key?>"> <?=$desc?><br>
                                    <?}
                                ?>
                            
                          </div>
                    <?}
                ?>

                <?if(in_array($rtype,array("leavereport"))){?>

                      <div class="form_row">
                          <div class="field" style="margin-left: 8%;">
                              &nbsp;<input type="checkbox" id="specific" class="double-sized-cb"> &nbsp; <b>Check if specific range</b>     
                          </div>
                      </div>  
                      <!-- <div class="form_row hide_div" style="display: none;">
                        <div class="form_row">
                            <label class="field_name align_right">Month:</label>
                            <div class="field no-search">
                                <select class="form-control" name="period" id="period"><?=$monthlist?></select>
                            </div>
                        </div>
                         <div class="form_row">
                            <label class="field_name align_right">Year:</label>
                            <div class="field no-search">
                                <select class="form-control" name="pyear" id="pyear" ><?=$yearlist?></select>
                            </div>
                        </div>
                      </div> -->

                      <div class="form_row hide_div">
                          <label class="field_name align_right">Date From</label>
                          <div class="field">
                              <div class="col-md-12" style="padding-left: 0px;">
                                  <div class="col-md-5" style="padding-left: 0px;">
                                      <div class='input-group date' id="dfrom" data-date="<?=$dfrom?$dfrom:$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 100%;">
                                          <input type='text' class="form-control" size="16" name="dfrom" type="text" value="<?=$dfrom?>"/>
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                      </div>
                                  </div>
                                  <span class="col-md-1" style="display: block;">&nbsp;<b>To</b>&nbsp;</span>
                                  <div class="col-md-5">
                                      <div class='input-group date' id="dto" data-date="<?=$dto?$dto:$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 100%;">
                                          <input type='text' class="form-control" size="16" name="dto" type="text" value="<?=$dto?>"/>
                                          <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="form_row" >
                        <label class="field_name align_right">Format</label>
                        <div class="field">
                              <div class="options">
                                      <label title="">
                                          <input type="radio"  name="pdfreport" value="summary" checked="" /> 
                                          <img />
                                          Summary Per Leave Type
                                      </label>
                                      <label title="" >
                                          <input type="radio" name="pdfreport" value="detailed" checked="">
                                          <img />
                                          Detailed Per Employee
                                      </label>   
                              </div>
                        </div>
                      </div> 
                      <div class="form_row" >
                        <label class="field_name align_right">&nbsp;</label>
                        <div class="field">
                            <input type='checkbox' id='selectall'>&nbsp; Select All
                        </div>
                      </div> 
                      <div class="form_row">
                        <label class="field_name align_right">Type</label>
                        <div class="field">
                            <ul class="checkbox"> 
                              <?php foreach ($types as $code => $desc): ?>
                                <li><input type='checkbox' class="selectall" name='type[]' value='<?= $code ?>'><?=$desc?><br></li>
                              <?php endforeach ?>
                            </ul>  
                        </div>                                                 
                      </div>
                <?}?>

                 <?if(in_array($rtype,array("obreport"))){?>

                            <div class="form_row hide_div">
                                <label class="field_name align_right">Date From</label>
                                  <div class="field">
                                        <div class="input-group date" id="dfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                                            <input class="align_center" size="16" name="dfrom" type="text" value="<?=$dfrom?>" readonly>
                                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                        Date To: 
                                        <div class="input-group date" id="dto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                                            <input class="align_center" size="16" name="dto" type="text" value="<?=$dto?>" readonly>
                                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                  </div>
                            </div>
                            <div class="form_row">
                                  <label class="field_name align_right">Type</label>
                                  <div class="field">
                                    <select class="chosen col-md-4" name="type" id="type">
                                        <option value=""> - All - </option>
                                        <option value="ABSENT"> ABSENT </option>
                                        <option value="DIRECT"> OFFICIAL BUSSINESS </option>
                                        <option value="NO PUNCH IN/OUT"> CORRECTION FOR TIME IN/OUT </option>
                                    </select>                                                 
                                  </div>
                            </div>
                            <div class="form_row">
                                  <label class="field_name align_right">Format</label>
                                  <div class="field">
                                        <div class="options">
                                                <label title="PDF">
                                                    <input type="radio"  name="reportformat" value="pdf" checked="" /> 
                                                    <img />
                                                    PDF
                                                </label>
                                                <label title="XLS" style="display: none;">
                                                    <input type="radio" name="reportformat" value="xls" hidden>
                                                    <img />
                                                    XLS
                                                </label>   
                                        </div>
                                  </div>
                            </div>               
                <?}?>

                 <?if(in_array($rtype,array("attendancereportperday"))){?>
                            <div class="form_row">
                                  <label class="field_name align_right">Category</label>
                                  <div class="field" style="width: 75%;">
                                      <select class="chosen col-md-4" name="categoryfilter" id="categoryfilter">
                                          <option value="PRESENT"> Present </option>
                                          <option value="Leave/OB"> Leave/OB </option>
                                          <option value="ABSENT"> Absent </option>
                                      </select>                                            
                                  </div><br>
                                  <label class="field_name align_right">Sort By</label>
                                  <div class="field" style="width: 75%;">
                                      <select class="chosen col-md-4" name="sort" id="sort">
                                          <option value="deptid"> Department </option>
                                          <option value="office"> Office </option>
                                      </select>                                            
                                  </div><br>
                                  <label class="field_name align_right">Select Date</label>
                                  <div class="field" style="width: 75%;">
                                    <div class='input-group date' id="scheddfrom" data-date="<?=date('Y-m-d')?>" data-date-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" name="scheddfrom" type="text" value="<?=date('Y-m-d')?>">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                  </div>
                            </div>
                <?}?>


        <?if(in_array($rtype,array("listemployeeschedule"))){?>
                            <div class="form_row">
                                  <label class="field_name align_right">Status By</label>
                                  <div class="field">
                                       
                                     <select class="chosen" name="isactive">
                                      <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                                    </select>
                                  </div>
                            </div>
                            <!-- <div class="form_row" id="scheddiv">
                                  <label class="field_name align_right">Division Level</label>
                                  <div class="field">
                                        <select class="chosen col-md-4" name="scheddiv" id="scheddiv">
                                            <option value="">All Division</option>
                                            <?
                                            $opt_management = $this->extras->showManagement();
                                            foreach($opt_management as $c=>$val){
                                            ?><option value="<?=$c?>"><?=$val?></option><?
                                            }
                                            ?>
                                        </select>
                                  </div>
                            </div> -->
                    <div class="form_row" id="scheddept">
                                  <label class="field_name align_right">Department</label>
                                  <div class="field">
                                        <select class="chosen col-md-4" name="scheddeptid" id="scheddeptid">
                                            <option value="">All Department</option>
                                            <?
                                            $opt_department = $this->extras->showdepartment();
                                            foreach($opt_department as $c=>$val){
                                            ?><option value="<?=$c?>"><?=$val?></option><?
                                            }
                                            ?>
                                        </select>
                                  </div>
                            </div>
                    <div class="form_row">
                                <label class="field_name align_right">Type</label>
                                <div class="field no-search">
                                    <select class="chosen" name="schedtnt" id="schedtnt"><?=$this->reports->employeetype()?></select>
                                </div>
                            </div>
                    <div class="form_row">
                                  <label class="field_name align_right">Format</label>
                                  <div class="field no-search">
                                        <select class="chosen" name="schedformat" id="schedformat">
                              <option value="PDF">PDF</option>
                              <option value="EXCEL">EXCEL</option>
                            </select>
                                  </div>
                            </div>
                    <!-- <div class="form_row">
                                  <label class="field_name align_right">As of Date</label>
                                  <div class="field">
                                        <div class='input-group date' id="scheddfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                            <input type='text' class="form-control" size="16" name="scheddfrom" type="text" value="<?=$datetoday?>"/>
                                            <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                  </div>
                            </div> -->
                <?}?>

        <?if(in_array($rtype,array("employeelistdeptreport"))){?>
                    <div class="form_row">
                          <label class="field_name align_right">Status By</label>
                          <div class="field">
                            <div class="col-md-8">
                               <select class="chosen" name="isactive">
                                <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                              </select>
                            </div>
                          </div>
                    </div>
                    <div class="form_row">
                      <label class="field_name align_right">Format</label>
                      <div class="field no-search">
                        <div class="col-md-8">
                          <select class="form-control chosen" name="schedformat" id="schedformat">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                            </select>
                        </div>
                      </div>
                    </div>
                <?}?>

        <?if(in_array($rtype,array("employeelistbyemployment"))){?>
                    <div class="form_row">
                          <label class="field_name align_right">Status By</label>
                          <div class="field">
                            <div class="col-md-8">
                               <select class="chosen" name="isactive">
                                <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                              </select>
                            </div>
                          </div>
                    </div>
                    <div class="form_row">
                      <label class="field_name align_right">Format</label>
                      <div class="field no-search">
                         <div class="col-md-8">
                          <select class="form-control chosen" name="schedformat" id="schedformat">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                            </select>
                        </div>
                      </div>
                    </div>
                <?}?>

       <?if(in_array($rtype,array("employeelistperoffice"))){?>
                    <div class="form_row">
                          <label class="field_name align_right">Status By</label>
                          <div class="field">
                            <div class="col-md-8">
                               <select class="chosen" name="isactive">
                                <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                              </select>
                            </div>
                          </div>
                    </div>
                    <div class="form_row">
                      <label class="field_name align_right">Format</label>
                      <div class="field no-search">
                          <div class="col-md-8">
                          <select class="form-control chosen" name="schedformat" id="schedformat">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                            </select>
                        </div>
                      </div>
                    </div>
                <?}?>

       <?if(in_array($rtype,array("employeelistbycivilstatus"))){?>
                    <div class="form_row">
                          <label class="field_name align_right">Status By</label>
                          <div class="field">
                            <div class="col-md-8">
                               <select class="chosen" name="isactive">
                                <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                              </select>
                            </div>
                          </div>
                    </div>
                    <div class="form_row">
                      <label class="field_name align_right">Format</label>
                      <div class="field no-search">
                          <div class="col-md-8">
                          <select class=" chosen form-control" name="schedformat" id="schedformat">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                            </select>
                        </div>
                      </div>
                    </div>
                <?}?>
         <?if(in_array($rtype,array("employmenthistoryreport"))){?>
            <div class="form_row">
                  <label class="field_name align_right">Status By</label>
                  <div class="field">
                       <select class="chosen" name="isactive">
                        <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="2">Inactive</option>
                      </select>
                  </div>
            </div>
            <div class="form_row">
                  <label class="field_name align_right">Office</label>
                  <div class="field">
                        <select class="chosen col-md-4" name="officeid" id="officeid">
                            <option value="">All Office</option>
                            <?
                            $opt_department = $this->extras->showoffice();
                            foreach($opt_department as $c=>$val){
                            ?><option value="<?=$c?>"><?=$val?></option><?
                            }
                            ?>
                        </select>
                  </div>
            </div>
            <br>
            <div class="form_row"  <?=($rtype == "employeewoseminar") ? 'hidden' : '' ?>>
                  <label class="field_name align_right">Employee</label>
                  <div class="field">
                        <select class="chosen col-md-4" id="employee" name="employee">
                            <option value="">All Employee</option>
                              <?
                                foreach ($emplist as $code => $desc) {?>
                                    <option value="<?=$code?>"><?=$desc?></option>
                                <?}
                            ?>
                        </select>
                  </div>
            </div>
            <br>
            <div class="form_row">
              <label class="field_name align_right">Format</label>
              <div class="field">
                <select class="chosen col-md-4" name="schedformat" id="schedformat">
                  <option value="PDF">PDF</option>
                  <option value="EXCEL">EXCEL</option>
                  </select>
              </div>
            </div>

        <?}?>
        <?if(in_array($rtype,array("seminarreport")) || in_array($rtype,array("employeewoseminar"))){?>
            <div class="form_row" id="scheddept">
                  <label class="field_name align_right">Department</label>
                  <div class="field">
                        <select class="chosen col-md-4" name="scheddeptid" id="scheddeptid">
                            <option value="">All Department</option>
                            <?
                            $opt_department = $this->extras->showdepartment();
                            foreach($opt_department as $c=>$val){
                            ?><option value="<?=$c?>"><?=$val?></option><?
                            }
                            ?>
                        </select>
                  </div>
            </div>
            <br>
            <div class="form_row">
                  <label class="field_name align_right">Office</label>
                  <div class="field">
                        <select class="chosen col-md-4" name="officeid" id="officeid">
                            <option value="">All Office</option>
                            <?
                            $opt_department = $this->extras->showoffice();
                            foreach($opt_department as $c=>$val){
                            ?><option value="<?=$c?>"><?=$val?></option><?
                            }
                            ?>
                        </select>
                  </div>
            </div>
            <br>
            <div class="form_row"  <?=($rtype == "employeewoseminar") ? 'hidden' : '' ?>>
                  <label class="field_name align_right">Employee</label>
                  <div class="field">
                        <select class="chosen col-md-4" id="employee" name="employee">
                            <option value="">All Employee</option>
                              <?
                                foreach ($emplist as $code => $desc) {?>
                                    <option value="<?=$code?>"><?=$desc?></option>
                                <?}
                            ?>
                        </select>
                  </div>
            </div>
            <br>
            <div class="form_row">
                  <label class="field_name align_right">Status By</label>
                  <div class="field">
                     <select class="chosen" name="isactive">
                      <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                    </select>
                  </div>
            </div>
            <br>
            <div class="form_row"  <?=($rtype == "employeewoseminar") ? 'hidden' : '' ?>>
                  <label class="field_name align_right">Seminar</label>
                  <div class="field">
                        <select class="chosen col-md-4" id="seminar_type" name="seminar_type">
                            <option value="">All Seminar</option>
                            <option value="employee_pts">T/A Pinnacle Spirituality</option>
                            <option value="employee_pts_pdp1">Professional Development Program</option>
                            <option value="employee_pts_pdp2">PEP Development Program</option>
                            <option value="employee_pts_pdp3">Psychosocial - Cultural</option>
                        </select>
                  </div>
            </div>
            <br  <?=($rtype == "employeewoseminar") ? 'hidden' : '' ?>>
            <div class="form_row">
                  <label class="field_name align_right">&nbsp;</label>
                  <div class="field">
                        <input type="checkbox" class="sortby" name="sortby" value="office" checked style="margin-top: 0px;"> &emsp;Sort By Office&emsp;
                        <input type="checkbox" class="sortby" name="sortby" value="alphabets"> &emsp;Sort Alphabetically
                  </div>
            </div>
            <br>
            <div class="form_row" hidden>
                  <label class="field_name align_right">&nbsp;</label>
                  <div class="field">
                        <input type="checkbox" class="radios withamount" name="withAmount" value="1" checked style="margin-top: 0px;"> &emsp;With Amount&emsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" class="radios withamount" name="withAmount" value="0"> &emsp;Without Amount 
                  </div>
            </div>
            <br  <?=($rtype == "employeewoseminar") ? 'hidden' : '' ?>>
            <div class="form_row">
              <label class="field_name align_right">Date From</label>
              <div class="field">
                <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($datetoday))?>" data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" size="16" name="dateFrom" id="dateFrom" value="<?=date("Y-m-d",strtotime($datetoday))?>"/>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
              </div>
            </div>
            <br>
            <div class="form_row">
              <label class="field_name align_right">Date To</label>
              <div class="field">
                <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($datetoday))?>" data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control" size="16" name="dateTo" id="dateTo" value="<?=date("Y-m-d",strtotime($datetoday))?>"/>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
              </div>
            </div>
            <br>
            <div class="form_row">
              <label class="field_name align_right">Format</label>
              <div class="field">
                <select class="chosen col-md-4" name="schedformat" id="schedformat">
                  <option value="PDF">PDF</option>
                  <option value="EXCEL">EXCEL</option>
                  </select>
              </div>
            </div>
        <?}?>

       <?if(in_array($rtype,array("employeelistbygender"))){?>
              <div class="form_row">
                    <label class="field_name align_right">Status By</label>
                    <div class="field">
                      <div class="col-md-8">
                         <select class="chosen" name="isactive">
                          <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                        </select>
                      </div>
                    </div>
              </div>
              <div class="form_row">
                <label class="field_name align_right">Format</label>
                <div class="field no-search">
                    <div class="col-md-8">
                    <select class="form-control chosen" name="schedformat" id="schedformat">
                      <option value="PDF">PDF</option>
                      <option value="EXCEL">EXCEL</option>
                      </select>
                  </div>
                </div>
              </div>
          <?}?>

      <?if(in_array($rtype,array("employeelistbyposition"))){?>
                    <div class="form_row">
                            <label class="field_name align_right">Status By</label>
                            <div class="field">
                              <div class="col-md-8">
                                 <select class="chosen" name="isactive">
                                  <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                                </select>
                              </div>
                            </div>
                      </div>
                    <div class="form_row">
                      <label class="field_name align_right">Format</label>
                      <div class="field no-search">
                          <div class="col-md-8">
                          <select class="form-control chosen" name="schedformat" id="schedformat">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                            </select>
                        </div>
                      </div>
                    </div>
                <?}?>

      <?if(in_array($rtype,array("employeelistbysalary"))){?>
                    <div class="form_row">
                            <label class="field_name align_right">Status By</label>
                            <div class="field">
                              <div class="col-md-8">
                                 <select class="chosen" name="isactive">
                                  <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                                </select>
                              </div>
                            </div>
                      </div>
                    <div class="form_row">
                      <label class="field_name align_right">Format</label>
                      <div class="field no-search">
                          <div class="col-md-8">
                          <select class="form-control chosen" name="schedformat" id="schedformat">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                            </select>
                        </div>
                      </div>
                    </div>
                <?}?>
                
      <?if(in_array($rtype,array("employeelistbyservice"))){?>
                    <div class="form_row">
                            <label class="field_name align_right">Status By</label>
                            <div class="field">
                              <div class="col-md-8">
                                 <select class="chosen" name="isactive">
                                  <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                                </select>
                              </div>
                            </div>
                      </div>
                    <div class="form_row">
                      <label class="field_name align_right">Format</label>
                      <div class="field no-search">
                          <div class="col-md-8">
                          <select class="form-control chosen" name="schedformat" id="schedformat">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                            </select>
                        </div>
                      </div>
                    </div>
                <?}?>


                <?if(in_array($rtype,array("employeebirthdayreport"))){?>
                            <div class="form_row">
                                  <label class="field_name align_right">Status By</label>
                                  <div class="field">
                                    <div class="col-md-8">
                                       <select class="chosen" name="isactive">
                                        <option value="">All Status</option>
                          <option value="1" selected>Active</option>
                          <option value="0">Inactive</option>
                                      </select>
                                    </div>
                                  </div>
                            </div>
                            <div class="form_row" id="employeebirthdayreport">
                                  <label class="field_name align_right">Sort By</label>
                                  <div class="field">
                                    <div class="col-md-8">
                                        <select class="chosen col-md-4" name="birthdayInfo" id="birthdayInfo">
                                            <option value="Month">Month</option>
                                            <option value="Age">Age</option>
                                        </select>
                                      </div>
                                  </div>
                            </div>
                            <div class="form_row" id="empBirthdayMonthSelect">
                                  <label class="field_name align_right">Month</label>
                                  <div class="field">
                                    <div class="col-md-8">
                                        <select class="chosen col-md-4" name="empBirthdayMonth" id="empBirthdayMonth">
                                            <option value="All">All Month</option>
                                            <?
                                                $months = array("1" => "January", "2" => "February", "3" => "March", "4" => "April", "5" => "May", "6" => "June", "7" => "July", "8" => "August", "9" => "September", "10" => "October", "11" => "November", "12" => "December");
                                                foreach ($months as $key => $month) {
                                                    echo "<option value=\"" . $key . "\">" . $month . "</option>";
                                                }
                                            ?>
                                        </select>
                                      </div>
                                  </div>
                            </div>
                            <div class="form_row">
                                <label class="field_name align_right">Format</label>
                                  <div class="field">
                                        <div class="col-md-8">
                                          <select class="form-control chosen" name="schedformat" id="schedformat">
                                            <option value="PDF">PDF</option>
                                            <option value="EXCEL">EXCEL</option>
                                            </select>
                                        </div>
                                  </div>
                            </div>
                <?}?>

                <?if(in_array($rtype,array("empsynclogs", "unlockedACcount", "lockACcount"))){?>
                           <!--  <div class="form_row">
                                  <label class="field_name align_right">Status By</label>
                                  <div class="field">
                                    <div class="col-md-8">
                                       <select class="chosen" name="isactive">
                                        <option value="">All Status</option>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                      </select>
                                    </div>
                                  </div>
                            </div> -->
                            <div class="form_row" id="edept">
                                  <label class="field_name align_right">Department</label>
                                  <div class="field">
                                        <select class="chosen col-md-4" name="deptid" id="deptid">
                                            <option value="">All Department</option>
                                            <?
                                            $opt_department = $this->extras->showdepartment();
                                            foreach($opt_department as $c=>$val){
                                            ?><option value="<?=$c?>"><?=$val?></option><?
                                            }
                                            ?>
                                        </select>
                                  </div>
                            </div>
                            <br>
                           <div class="form_row">
                              <label class="field_name align_right">Date From</label>
                              <div class="field">
                                <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($datetoday))?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="dateFrom" id="dateFrom" value="<?=date("Y-m-d",strtotime($datetoday))?>"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                              </div>
                            </div>
                            <br>
                            <div class="form_row">
                              <label class="field_name align_right">Date To</label>
                              <div class="field">
                                <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($datetoday))?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="dateTo" id="dateTo" value="<?=date("Y-m-d",strtotime($datetoday))?>"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                              </div>
                            </div>
                            <br>

                            <div class="form_row">
                                <label class="field_name align_right">Format</label>
                                  <div class="field">
                                          <select class="form-control chosen" name="schedformat" id="schedformat">
                                            <option value="PDF">PDF</option>
                                            <option value="EXCEL">EXCEL</option>
                                            </select>
                                  </div>
                            </div>
                <?}?>

                <?if(in_array($rtype,array("confirmed_history"))){?>
                    <div class="form_row">
                          <label class="field_name align_right">Cutoff</label>
                          <div class="field">
                                <select class="chosen col-md-4" id="cutoff" name="cutoff"><?=$this->employeemod->displayCutOff()?></select>
                          </div>
                    </div>
                    <br>

                     <div class="form_row">
                        <label class="field_name align_right">Date From</label>
                        <div class="field">
                          <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($datetoday))?>" data-date-format="yyyy-mm-dd">
                              <input type='text' class="form-control" size="16" name="dateFrom" id="dateFrom" value="<?=date("Y-m-d",strtotime($datetoday))?>"/>
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div>
                        </div>
                      </div>
                      <br>
                      <div class="form_row">
                        <label class="field_name align_right">Date To</label>
                        <div class="field">
                          <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($datetoday))?>" data-date-format="yyyy-mm-dd">
                              <input type='text' class="form-control" size="16" name="dateTo" id="dateTo" value="<?=date("Y-m-d",strtotime($datetoday))?>"/>
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div>
                        </div>
                      </div>
                      <br>

                      <div class="form_row">
                          <label class="field_name align_right">Format</label>
                            <div class="field">
                                    <select class="form-control chosen" name="schedformat" id="schedformat">
                                      <option value="PDF">PDF</option>
                                      <option value="EXCEL">EXCEL</option>
                                      </select>
                            </div>
                      </div>
                <?}?>
                
                <?if(in_array($rtype,array("mureport","summaryabsences","otreport","detailedsummaryabsences","summaryabsenceswelsl"))){?>
                            <div class="form_row">
                                  <label class="field_name align_right">Select Campus</label>
                                  <div class="field">
                                        <select class="chosen col-md-4" id="campus" name="campus">
                                            <option value="">All Campus</option>
                                            <?=$this->extras->getCampuses()?>
                                        </select>
                                  </div>
                            </div>    
                            <div class="form_row">
                                  <label class="field_name align_right">Date From</label>
                                  <div class="field">
                                        <div class="input-group date" id="dfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                            <input class="align_center" size="16" name="dfrom" type="text" value="<?=$datetoday?>" readonly>
                                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                   Date To: 
                                   <div class="input-group date" id="dto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                            <input class="align_center" size="16" name="dto" type="text" value="<?=$datetoday?>" readonly>
                                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                  </div>
                            </div>
                            <div class="form_row">
                                  <label class="field_name align_right">Teaching</label>
                                  <div class="field no-search">
                                        <select class="form-control" name="tnt" id="tnt">
                                              <option value="">All Types</option>
                                            <?=$this->reports->employeetype()?>
                                                
                                        </select>
                                  </div>
                            </div>
                            <div class="form_row" id="edept">
                                  <label class="field_name align_right">Department</label>
                                  <div class="field">
                                        <select class="chosen col-md-4" name="deptid" id="deptid">
                                            <option value="">All Department</option>
                                            <?
                                            $opt_department = $this->extras->showdepartment();
                                            foreach($opt_department as $c=>$val){
                                            ?><option value="<?=$c?>"><?=$val?></option><?
                                            }
                                            ?>
                                        </select>
                                  </div>
                            </div>
                            <div class="form_row" id="estat" hidden="">
                                  <label class="field_name align_right">Employment Status</label>
                                  <div class="field">
                                        <select class="chosen col-md-4" name="estatus" id="estatus">
                                            <option value="">All Status</option>
                                            <?
                                            $opt_status = $this->extras->showStatus();
                                            foreach($opt_status->result() as $row){
                                            ?><option value="<?=$row->code?>"><?=$row->description?></option><?
                                            }
                                            ?>
                                        </select>
                                  </div>
                            </div>
                <?}?>
              </div>
        </div>

        <input type="hidden" name="form" value="<?=$rtype?>"> 
        <input type="hidden" name="reportTitle" value="<?=isset($report_title) ? $report_title : ''?>">        
        <div class="modal-footer">
              <div id="loading" hidden=""></div>
                    <div id="saving">
                        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="button" id="generates" class="btn btn-success generate">Generate</button>
                    </div>
              </div>
        </div>
</div>
</form>
<script>
var toks = hex_sha512(" ");
$("#selectall").click(function(){
  if($(this).is(":checked")) $(".selectall").prop("checked", true)
  else $(".selectall").prop("checked", false)
})

$('.sortby').on('change', function() {
   $('.sortby').not(this).prop('checked', false);
});

$('.withamount').on('change', function() {
   $('.withamount').not(this).prop('checked', false);
});

$("#division, #department, #campus, #officeid, select[name='isactive']").unbind().change(function(){
    var deptid = $("#department").val();
    var campusid = $("#campus").val();
    var officeid = $("#officeid").val();
    var isactive = $("select[name='isactive']").val();
    $.ajax({
      type:'POST',
      url:"<?=site_url('reports_/loadEmployee')?>",
      data: {deptid: GibberishAES.enc( deptid, toks), campusid: GibberishAES.enc(campusid , toks), officeid: GibberishAES.enc(officeid , toks), isactive: GibberishAES.enc(isactive , toks), toks:toks},
      success:function(html){
          $("select[name='employee']").html(html).trigger('chosen:updated');
      }
    }); 
  });

if("<?=$rtype?>" == "attendancereportperday") $(".modal-title").text("Attendance Report Per Day");

$("#specific").change(function(){
    if(this.checked) $(".hide_div").show();
    else $(".hide_div").hide();
});

$('#birthdayInfo').on('change', function() {
  if (this.value == "Month") {
        $("#empBirthdayMonthSelect").show();
        }else{
        $('#empBirthdayMonth').val('All');
        $("#empBirthdayMonthSelect").hide();
        }
});

$(".generate").click(function(){
  var encodedData = '';
    var edata = $("input[name=edata]:checked").map(function () {return this.value;}).get().join(","); 
    var form_data   =   "form=<?=$rtype?>";
        form_data   +=  "&"+$("#myFrm").serialize();
        $('#myFrm input, #myFrm select, #myFrm textarea').each(function(){
          if($(this).attr("name") == "sortby"){
            if($(this).is(":checked")) form_data += '&'+$(this).attr('name')+'='+GibberishAES.enc($(this).val(), toks);
          }else if($(this).attr("name") == "withAmount"){
            if($(this).is(":checked")) form_data += '&'+$(this).attr('name')+'='+GibberishAES.enc($(this).val(), toks);
          }else{
            form_data += '&'+$(this).attr('name')+'='+GibberishAES.enc($(this).val(), toks);
          }

        })
        // console.log(form_data);return;
if(edata)
        form_data   +=  "&edata="+GibberishAES.enc(edata, toks);
        form_data   +=  "&toks="+toks;
    if("<?=$rtype?>" == "listemployeeschedule")
    {
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/listemployeeschedulexls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
      // console.log(form_data);
    }else if("<?=$rtype?>" == "empsynclogs")
    {
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/empsynclogs", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
      // console.log(form_data);lockACcount
    }else if("<?=$rtype?>" == "unlockedACcount")
    {
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/unlockedACcount", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
      // console.log(form_data);
    }else if("<?=$rtype?>" == "lockACcount")
    {
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/lockACcount", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
      // console.log(form_data);
    }else if("<?=$rtype?>" == "confirmed_history")
    {
      encodedData = encodeURIComponent(window.btoa(form_data));
      openWindowWithPost("<?=site_url("forms/confirmed_history")?>", {formdata: encodedData});
    }else if("<?=$rtype?>" == "employeelistdeptreport"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employeelistdeptreportxls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData}); 
      }
    }else if("<?=$rtype?>" == "attendancereportperday"){
      if($("#categoryfilter").val() == "PRESENT"){
      form_data += "&view="+ GibberishAES.enc("reports_excel/Attendance_Report_Presentxls", toks);
      }else if($("#categoryfilter").val() == "Leave/OB"){
      form_data += "&view="+ GibberishAES.enc("reports_excel/Attendance_Report_Presentxls" , toks);
      // form_data += "&view=reports_excel/Attendance_Report_LeaveOBxls";
      }else{
      form_data += "&view="+GibberishAES.enc("reports_excel/Attendance_Report_Absentsxls", toks);
      }
      encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData}); 
  
    }else if("<?=$rtype?>" == "employeelistbysalary"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employeelistbysalaryxls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData}); 
      }
      console.log(form_data);
    }else if("<?=$rtype?>" == "employeelistbyposition"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employeelistbypositionxls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
         openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData}); 
      }
    }else if("<?=$rtype?>" == "employeelistperoffice"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employeelistperofficexls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
         openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData}); 
      }
    }else if("<?=$rtype?>" == "employeelistbycivilstatus"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employeelistbycivilstatusxls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
    }else if("<?=$rtype?>" == "seminarreport"){
      if($("#schedformat").val() == "PDF")
      {

        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      { 
        form_data += "&view="+GibberishAES.enc("reports_excel/seminarreport", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
    }else if("<?=$rtype?>" == "employeewoseminar"){
      if($("#schedformat").val() == "PDF")
      {
         encodedData = encodeURIComponent(window.btoa(form_data));
         openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employeewoseminar", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
    }else if("<?=$rtype?>" == "employmenthistoryreport"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employmenthistoryreport", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
    }else if("<?=$rtype?>" == "employeelistbygender")
    {
        if($("#schedformat").val() == "PDF")
        {
            encodedData = encodeURIComponent(window.btoa(form_data));
            openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
        }
        else
        {
            form_data += "&view="+GibberishAES.enc("reports_excel/employeelistpergenderexls", toks); 
            encodedData = encodeURIComponent(window.btoa(form_data));
            openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
        }
    } 
// birthdayformat
    else if("<?=$rtype?>" == "employeebirthdayreport")
    {
        if($("#schedformat").val() == "PDF")
        {
            encodedData = encodeURIComponent(window.btoa(form_data));
            openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
        }
        else
        {
            form_data += "&view="+GibberishAES.enc("reports_excel/employeebirthdayreportxls", toks); 
            encodedData = encodeURIComponent(window.btoa(form_data));
            openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
        }
    } 
    // adsfadfasfsfas

    else if("<?=$rtype?>" == "employeelistbyservice"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      { 
        form_data += "&view="+GibberishAES.enc("reports_excel/employeelistbyservicexls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
    }else if("<?=$rtype?>" == "employeelistbyemployment"){
      if($("#schedformat").val() == "PDF")
      {
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }
      else
      {
        form_data += "&view="+GibberishAES.enc("reports_excel/employeelistbyemploymentxls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }
    }else if("<?=$rtype?>" == "personalrosterxls"){
      if(edata){ 
        form_data += "&view="+GibberishAES.enc("reports_excel/personalrosterxls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
      }else{
        alert("Please select filters!");
      }
    }
    else if("<?=$rtype?>" == "personalroster"){
      if(edata){
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
      }else{
        alert("Please select filters!");
      }
    }
        else if("<?=$rtype?>" == "summaryabsences"){
            $("#myFrm").attr("action", "<?=site_url("reports_/showSummaryOfAbsencesNoLeaveFiled")?>");
            $("#myFrm").attr("target", "_blank");
            $("#myFrm").attr("method", "post");
            $("#myFrm").submit();
        }else if("<?=$rtype?>" == "leavereport"){
            
             if($("#specific").prop("checked")){

                /*month = $("select[name='period']").val();
                year = $("select[name='pyear']").val();

                var cutmonth = month.replace(/0/g, "");
                var FDay = new Date(year, cutmonth - 1, 1);
                var LDay = new Date(year, cutmonth, 0);

                var firstday = FDay.getDate();
                var lastday = LDay.getDate();

                datesetfrom = year.concat("-",month,"-",firstday);
                datesetto = year.concat("-",month,"-",lastday);
                $("input[name='dfrom']").val(datesetfrom);
                $("input[name='dto']").val(datesetto);*/
              }
            $("#myFrm").attr("target", "_blank");
            $("#myFrm").attr("action", "<?=site_url("leave_/loadLeaveReports")?>");
            $("#myFrm").attr("method", "post");
            $("#myFrm").submit();
            //window.open("<?=site_url("reports_/loadHRReport")?>?"+form_data);
        }
    else if($("#birthdayformat").val() == "PDF"){
           encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
        }else if($("#birthdayformat").val() == "EXCEL"){
            form_data += "&view="+GibberishAES.enc("reports_excel/employeebirthdayreportxls", toks); 
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("reports_/reportloader")?>", {formdata: encodedData});
        }
        else{
        encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
    }
        //NEW CONDITION ASK KENNEDY
    // else if($("#birthdayformat").val() == "PDF")
    // {
    //     window.open("<?=site_url("forms/loadForm")?>?"+form_data,"");
    // }
    // else if($("#birthdayformat").val() == "EXCEL")
    // {
    //     form_data += "&view=reports_excel/employeebirthdayreportxls"; 
    //     window.open("<?=site_url("reports_/reportloader")?>?"+form_data,"");
    // }
    // else
    // {
    //     window.open("<?=site_url("forms/loadForm")?>?"+form_data,"");
    // }
});
$("#tnt").change(function(){
   if($(this).val() == "teaching"){
    $("#estat").hide();
    $("#estatus").val("");
    $("#edept").show();
   }else if($(this).val() == "nonteaching"){
    $("#edept").hide();
    $("#deptid").val("");
    $("#estat").show();
   }
});

<?if(!isset($cutoff)):?>
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
<?else:?>
$(".hide_div").hide();
<?endif;?>

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(".chosen").chosen();

$("#scheddeptid").change(function(){
    $.ajax({
        url:"<?=site_url('setup_/getOffice')?>",
        type: "POST",
        data: {department:$(this).val()},
        success: function(msg){
            $("#officeid").html(msg).trigger("chosen:updated");
        }
    });
});

</script>