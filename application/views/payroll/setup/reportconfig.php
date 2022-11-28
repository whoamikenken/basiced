<?php

/**
 * @author Justin
 * @copyright 2017
 */
$toks = $this->input->post("toks");
$rfile = ($toks)  ? $this->gibberish->decrypt($this->input->post("rfile"), $toks) : $this->input->post("rfile");

$CI =& get_instance();
$CI->load->model('utils');
$divisions      = $CI->utils->getManagementLevels('- All division level -');
$departments    = $CI->utils->getOffice('- All department -');
?>
<style>
.two-col {
    -webkit-column-count: 2; /* Chrome, Safari, Opera */
    -moz-column-count: 2; /* Firefox */
    column-count: 2;
}
  .form_row{
    padding-bottom: 15px;
  }

</style>

<form id="frmsetup">
<input name="form" value="<?=$rfile?>" hidden="" />
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
            <center><b><h3 tag="title" class="modal-title"><?=(isset($title) && $title != "undefined"? $title : "Setup")?></h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
                <?if($rfile == 'incomereportxls' || $rfile == 'incomereportemployeexls'){ ?>
                
                        <div class="form_row">
                              <label class="field_name align_right">Type:</label>
                              <div class="field no-search">
                                    <select class="chosen-select col-md-4" name="tnt" id="tnt">
                                          <?
                                                $type = array("" => "-Select All-","teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                                foreach($type as $c=>$val){
                                                ?><option value="<?=$c?>"><?=$val?></option><?
                                                }
                                          ?>
                                    </select>
                              </div>
                        </div>
                          <div class="form_row">
                                <label class="field_name align_right">Cut-Off:</label>
                                <div class="field no-search">
                                     <select class="chosen-select col-md-4" id="cutoff" name="cutoff"><?=$this->employeemod->displayIncomeReportCutOff()?></select>
                                </div>
                          </div>
                              <?
                                
                                $income = $this->reports->getPayrollIncomeConfig("payroll_income_config","selectAll");

                               ?>
                          <div class="form_row">
                                <label class="field_name align_right">Income:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" multiple name='income[]' id="income">
                                        <option value="allincome">SELECT ALL</option>
                                            <?foreach ($income as $key => $value) {?>
                                                  <option type='checkbox'  value='<?=$value["id"]?>'><?=$value["description"]?></option>>
                                                <?}?>
                                      </select>
                                </div>
                          </div>
                          
                          <div class="form_row">
                                <label class="field_name align_right">Status:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" name="status" id="status">
                                          <option value="PROCESSED">Processed</option>
                                          <option value="PENDING">Pending</option>
                                          <option value="SAVED">Saved</option>
                                      </select>
                                </div>
                          </div>
                          
                          <div class="form_row">
                                <label class="field_name align_right">Sort:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" name="sort_by" id="sort_by">
                                          <option value="department">Office</option>
                                          <option value="employee">Alphabetical Order</option>
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
                                            </label>&emsp;<label title="XLS">
                                                <input type="radio" name="reportformat" value="xls"  />
                                                <img />
                                                EXCEL
                                            </label>   
                                            
                                      </div>
                                </div>
                          </div>
             <?}else if($rfile == 'incomeadj' || $rfile == 'incomeadjemp'){?>
                          <div class="form_row">
                                <label class="field_name align_right">Type:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" name="tnt" id="tnt">
                                          <?
                                            $type = array("" => "-Select All-","teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                            foreach($type as $c=>$val){
                                            ?><option value="<?=$c?>"><?=$val?></option><?
                                            }
                                          ?>
                                      </select>
                                </div>
                          </div>

                          <div class="form_row">
                                <label class="field_name align_right">Cut off:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" id="cutoff" name="cutoff"><?=$this->employeemod->displayIncomeReportCutOff()?></select>
                                </div>
                          </div>
                                  <?
                                    $income = $this->reports->getPayrollIncomeConfig("payroll_income_config","selectAll", true);
                                  ?>
                          <div class="form_row">
                                <label class="field_name align_right">Income:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" multiple name='income[]' id="income">
                                      <option type='checkbox' value='selectAll'>Select All</option>
                                        
                                            <?foreach ($income as $key => $value) {?>
                                                  <option type='checkbox'  value='<?=$value["id"]?>'><?=$value["description"]?></option>>
                                                <?}?>
                                      </select>
                                </div>
                          </div>

                          <div class="form_row">
                                <label class="field_name align_right">Status:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" name="status" id="status">
                                          <option value="PROCESSED">Processed</option>
                                          <option value="PENDING">Pending</option>
                                          <option value="SAVED">Saved</option>
                                      </select>
                                </div>
                          </div>

                          <div class="form_row">
                                <label class="field_name align_right">Sort:</label>
                                <div class="field no-search">
                                      <select class="chosen-select col-md-4" name="sort_by" id="sort_by">
                                          <option value="department">Department</option>
                                          <option value="employee">Employee</option>
                                      </select>
                                </div>
                          </div>   
             <?}else if($rfile == 'netPayHistory'){?>
                          <input type="hidden" name="reportname" value="<?=$rfile?>">
                          <div class="form_row">
                                <label class="field_name align_right">Period Cover</label>
                                <div class="field no-search">
                                      <select class="form-control" name="period" id="period"><?=$this->payrolloptions->monthname();?></select>
                                      Year
                                      <select class="form-control" name="pyear" id="pyear"><?=$this->payrolloptions->periodyear();?></select>
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
                                            </label>&emsp;<label title="XLS">
                                                <input type="radio" name="reportformat" value="xls"  />
                                                <img />
                                                EXCEL
                                            </label>   
                                            
                                      </div>
                                </div>
                          </div>

            <?}else if($rfile == 'mrrReport'){?>
                          <div class="form_row">
                                <label class="field_name align_right">Division:</label>
                                <div class="field">
                                      <select class="chosen-select col-md-4" id="division" name="division">
                                          <?
                                            
                                            foreach ($divisions as $code => $desc) {?>
                                               <option value="<?=$code?>"><?=$desc?></option>
                                            <?}
                                          ?>
                                      </select>
                                </div>
                          </div>
                          <div class="form_row">
                                <label class="field_name align_right">Department:</label>
                                <div class="field">
                                      <select class="chosen-select col-md-4" id="department" name="department">
                                          <?
                                            foreach ($departments as $code => $desc) {?>
                                              <option value="<?=$code?>"><?=$desc?></option>
                                            <?}
                                          ?>
                                      </select>
                                </div>
                          </div>
                          <div class="form_row">
                                <label class="field_name align_right">Cut off:</label>
                                <div class="field no-search">
                                    <?
                                        $isPerMonth = ($rfile == 'mrrReport') ? true : false;
                                    ?>
                                      <select class="chosen-select col-md-4" id="cutoff" name="cutoff"><?=$this->employeemod->displayIncomeReportCutOff($isPerMonth)?></select>
                                </div>
                          </div>

                          <div class="form_row">
                              <label class="field_name align_right">Sort:</label>
                              <div class="field no-search">
                                    <select class="chosen-select col-md-4" name="sort" id="sort">
                                        <option value="department">Office</option>
                                        <!-- <option value="campus">Campus</option> -->
                                        <option value="name">Alphabetical Order</option>

                                    </select>
                              </div>
                          </div>

                          <div class="form_row">
                                <div class="field no-search">
                                      <div style="padding-top: 10px;">
                                            <input type="radio" name="format" value="PDF" checked="">&nbsp;PDF &nbsp;&nbsp;
                                        <input type="radio" name="format" value="XLS">&nbsp;EXCEL
                                      </div>
                                </div>
                          </div>

      			<?}else if($rfile == 'rdcForm'){?>
                        <!-- <div class="form_row">
                              <label class="field_name align_right">Division:</label>
                              <div class="field">
                                    <select class="chosen-select col-md-4" id="division" name="division">
                                        <?
                                          
                                          foreach ($divisions as $code => $desc) {?>
                                             <option value="<?=$code?>"><?=$desc?></option>
                                          <?}
                                        ?>
                                    </select>
                              </div>
                        </div> -->
                        <div class="form_row">
                              <label class="field_name align_right">Type:</label>
                              <div class="field">
                                    <select class="chosen-select col-md-4" id="tnt" name="tnt">
                                          <?
                                            $type = array("" => "-Select All-","teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                            foreach($type as $c=>$val){
                                            ?><option value="<?=$c?>"><?=$val?></option><?
                                            }
                                          ?>
                                    </select>
                              </div>
                        </div>
                        <div class="form_row">
                              <label class="field_name align_right">Department:</label>
                              <div class="field">
                                    <select class="chosen-select col-md-4" id="department" name="department">
                                        <?=$this->extras->getDeptpartment()?>
                                    </select>
                              </div>
                        </div>
                        <div class="form_row">
                              <label class="field_name align_right">Office:</label>
                              <div class="field">
                                   <select class="col-md-4 chosen-select" id="office" name="office"><?=$this->extras->getOffice()?></select>
                              </div>
                        </div>
                        <div class="form_row">
                              <label class="field_name align_right">Cut-Off:</label>
                              <div class="field no-search">
                                  <?
                                      $isPerMonth = ($rfile == 'rdcForm') ? true : false;
                                  ?>
                                    <select class="chosen-select col-md-4" id="cutoff" name="cutoff"><?=$this->employeemod->displayIncomeReportCutOff($isPerMonth)?></select>
                              </div>
                        </div>
                        <div class="form_row">
                              <label class="field_name align_right">Reglementary:</label>
                              <div class="field no-search">
                                    <select class="chosen-select col-md-4" name="deduction" id="deduction">
                                    <?
                                      $type = array(""=>"Select Regulatory", "SSS"=>"SSS","PHILHEALTH"=>"Philhealth","PAGIBIG"=>"Pag-Ibig");
                                      foreach($type as $c=>$val){
                                      ?><option value="<?=$c?>"><?=$val?></option><?
                                      }
                                    ?>
                                    </select>
                              </div>
                        </div>
                        <div class="form_row">
                              <label class="field_name align_right">Sort:</label>
                              <div class="field no-search">
                                    <select class="chosen-select col-md-4" name="sort" id="sort">
                                        <option value="department">Office</option>
                                        <!-- <option value="campus">Campus</option> -->
                                        <option value="name">Alphabetical Order</option>

                                    </select>
                              </div>
                        </div>
                        <!-- <div class="form_row">
                              <label class="field_name align_right">&nbsp;</label>
                              <div class="field no-search">
                                    <select class="chosen-select col-md-4" name="sd_filter" id="sd_filter">
                                        <option value="detailed">Detailed</option>
                                        <option value="summary">Summary</option>
                                    </select>
                              </div>
                        </div> -->
                        <div class="form_row">
                              <label class="field_name align_right">Status:</label>
                              <div class="field no-search">
                                    <select class="chosen-select col-md-4" name="status" id="status">
                                        <option value="PROCESSED">Processed</option>
                                        <option value="PENDING">Pending</option>
                                        <option value="SAVED">Saved</option>
                                    </select>
                              </div>
                        </div>

                        <div class="form_row">
                              <div class="field no-search">
                                    <div style="padding-top: 10px;">
                                        <input type="radio" name="format" value="PDF" checked="">&nbsp;PDF &nbsp;&nbsp;
                                        <input type="radio" name="format" value="XLS">&nbsp;EXCEL
                                    </div>
                              </div>
                        </div>

              <?}else if($rfile == 'employeeBalancesPerEmployee' || $rfile == 'employeeBalancesPerDeduction'){ 
                          $checked = ($rfile == 'employeeBalancesPerEmployee') ? "perEmp" : "perDed";
                        ?>
                            <div class="form_row" hidden>
                                  <label class="field_name align_right">Format:</label>
                                  <div class="field no-search">
                                        <div style="padding-top: 10px;">
                                          <input type="radio" name="format" value="perEmp" <?=($checked == "perEmp") ? "checked" : ""?>>&nbsp;Per Employee &nbsp;&nbsp;
                                          <input type="radio" name="format" value="perDed" <?=($checked == "perDed") ? "checked" : ""?>>&nbsp;Per Deduction
                                        </div>
                                  </div>
                            </div>
                            
                            <div class="form_row">
                                  <label class="field_name align_right">Cut off:</label>
                                  <div class="field no-search">
                                        <select class="chosen-select col-md-4" id="cutoff" name="cutoff"><?=$this->employeemod->displayIncomeReportCutOff()?></select>
                                  </div>
                            </div>
                            
                            <div class="form_row">
                                  <label class="field_name align_right">Employee</label>
                                  <div class="field">
                                        <select class="chosen-select col-md-4" name="employeeid">
                                                  <option value="">All Employee</option>
                                                  <?
                                                      $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),'','',true);
                                                      foreach($opt_type as $val){
                                                  ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                                      }
                                                  ?>
                                        </select>
                                  </div>
                            </div>
                            
                            <div class="form_row">
                                  <div class="field no-search">
                                        &nbsp;<input type="checkbox" name="tag" tag="deductions" value="DEDUCTION" checked> DEDUCTION</input>          
                                        &nbsp; <input type="checkbox" name="tag" tag="deductions" value="LOAN"> LOAN</input>          
                                  </div>           
                            </div>
                            
                            <div class="form_row deduction">
                                  <label class="field_name align_right">Deduction:</label>
                                  <div class="field ">
                                        <select class="chosen-select col-md-4" name="deduction" id="deduction">
                                              <option value="">All Deduction</option>
                                              <?=$this->extras->showDeductionsConfig();?>
                                        </select>
                                  </div>
                            </div>
                            
                            <div class="form_row loan" style="display: none">
                                  <label class="field_name align_right">Loan Deduction:</label>
                                  <div class="field">
                                        <select class="chosen-select col-md-4" name="loandeduction" id="deduction">
                                               <option value="">All Loan Deduction</option>
                                               <?=$this->extras->showLoanConfig();?>
                                        </select>
                                  </div>
                            </div>
                                                    
                            <div class="form_row"  style="display: none">
                                  <div class="field">
                                        <select class="chosen-select col-md-4" name="viewtype" id="viewtype">
                                            <?
                                              $type = array("summary"=>"Summary","detailed"=>"Detailed");
                                              foreach($type as $c=>$val){
                                              ?><option value="<?=$c?>"><?=$val?></option><?
                                              }
                                            ?>
                                        </select>
                                  </div>
                            </div>
                            <div class="form_row detailedstatus">
                                  <label class="field_name align_right">Status:</label>
                                  <div class="field no-search">
                                        <select class="chosen-select col-md-4" name="status" id="status">
                                            <option value="PROCESSED">Processed</option>
                                            <option value="PENDING">Pending</option>
                                            <option value="SAVED">Saved</option>
                                        </select>
                                  </div>
                            </div>
                            
                            <div class="form_row">
                                  <label class="field_name align_right">Sort:</label>
                                  <div class="field no-search">
                                        <select class="chosen-select col-md-4" name="sort_by" id="sort_by">
                                          <option value="department">Office</option>
                                          <option value="employee">Alphabetical Order</option>
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
                                            </label>&emsp;<label title="XLS">
                                                <input type="radio" name="reportformat" value="xls"  />
                                                <img />
                                                EXCEL
                                            </label>   
                                            
                                      </div>
                                </div>
                          </div>

      		<?}else if($rfile == 'alphalistform_new'){ ?>
                      		<div class="form_row ">
                                  <label class="field_name align_right">Year:</label>
                                  <div class="field no-search">
                                        <input type="number" class="form-control" value="<?=date("Y")?>" name="year" id="year"  />
                                  </div>
                            </div>
                            
                            <div class="form_row ">
                                  <label class="field_name align_right">Schedule:</label>
                                  <div class="field no-search">
                                        <select name='schedule' id='schedule' class='chosen-select'>
                                            <option value='all'>All Schedule</option>
                                            <option value='7.1'>Schedule 7.1</option>
                                            <option value='7.3'>Schedule 7.3</option>
                                            <option value='7.4'>Schedule 7.4</option>
                                        </select>
                                  </div>
                            </div>

                            <div class="form_row ">
                                  <label class="field_name align_right">File Type:</label>
                                  <div class="field no-search">
                                        <select name='file' id='file' class='chosen-select'>
                                            <option value='XLS'>Excel</option>
                                            <!-- <option value='PDF'>PDF</option> --> 
                                        </select>
                                  </div>
                            </div>
            <?}
            else if($rfile == 'empledgerForm'){ ?>
                            <div class="form_row">
                                              <label class="field_name align_right">Department:</label>
                                  <div class="field">
                                        <select class="chosen-select col-md-4" name="deptid">
                                                         <option value="">All Department</option>
                                                          <?
                                                              $opt_department = $this->extras->showdepartment();
                                                              foreach($opt_department as $c=>$val){
                                                 ?>      <option value="<?=$c?>"><?=$val?></option><?
                                                        }
                                                 ?>
                                        </select>
                                  </div>
                            </div>
                            <div class="form_row">
                                  <label class="field_name align_right">Employment Status</label>
                                  <div class="field no-search">
                                        <select class="form-control" name="estatus" id="estatus">
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
                            <div class="form_row">
                                  <label class="field_name align_right">Employee</label>
                                  <div class="field">
                                        <select class="chosen-select col-md-4" name="employeeid">
                                                          <option value="">All Employee</option>
                                                          <?
                                                              $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),'','',true);
                                                              foreach($opt_type as $val){
                                                          ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                                              }
                                                          ?>
                                        </select>
                                  </div>
                            </div>
                            <div class="form_row">
                                  <label class="field_name align_right">Year:</label>
                                  <div class="field no-search">
                                               <?php
                                               $Startyear=date('Y');
                                               $endYear=$Startyear-4;
                                               $yearArray = range($Startyear,$endYear);
                                               ?>
                                                     
                                        <select class="form-control" name="year" id="year">
                                                   <option value="">- Select Year- </option>
                                                   <?php
                                                   foreach ($yearArray as $year) {
                                                     // this allows you to select a particular year
                                                   $selected = ($year == $Startyear) ? 'selected' : '';
                                                   echo '<option value="'.$year.'" '.$selected.' >'.$year.'</option>';
                                                 }
                                                 ?>
                                        </select>
                                  </div>
                            </div>
            <?}
            else if($rfile == 'ssscontri' || $rfile == 'philcontri' || $rfile == 'hdmfcontri' || $rfile == "mp2contri" || $rfile == "pagibigvolcontri"){?>
                            <input type="hidden" name="reportname" value="<?=$rfile?>">
                            <div class="form_row">
                                  <label class="field_name align_right">Type:</label>
                                  <div class="field no-search">
                                        <select class="chosen-select col-md-4" name="tnt" id="tnt">
                                                <?
                                                  $type = array("" => "-Select All-","teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                                  foreach($type as $c=>$val){
                                                  ?><option value="<?=$c?>"><?=$val?></option><?
                                                  }
                                                ?>
                                        </select>
                                  </div>
                            </div>
                            
                            <div class="form_row">
                                  <label class="field_name align_right">Employee</label>
                                  <div class="field">
                                        <select class="chosen-select col-md-4" name="employeeid" id="employeeid">
                                                  <option value=''>--Select Employee--</option>
                                                  <?
                                                  $opt_type = $this->employee->loadallemployees();
                                                    foreach($opt_type as $val){
                                                  ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                                    }
                                                  ?>
                                        </select>
                                  </div>
                            </div>
                            
                            <div class="form_row">
                                  <label class="field_name align_right">Period Cover From</label>
                                  <div class="field no-search">
                                    <div class="col-md-5" style="padding-left: 0px;"><select class="form-control chosen-select" name="pfrom" id="pfrom"><?=$this->payrolloptions->monthname();?></select></div>
                                    <div class="col-md-2"><span><b>Year</b></span></div>
                                    <div class="col-md-5" style="padding-right: 0px;"><select class="form-control chosen-select" name="pyearfrom" id="pyearfrom"><?=$this->payrolloptions->periodyear();?></select></div>  
                                  </div>
                            </div>
                            <div class="form_row">
                                  <label class="field_name align_right">Period Cover To</label>
                                  <div class="field no-search">
                                    <div class="col-md-5" style="padding-left: 0px;"><select class="form-control chosen-select" name="pto" id="pto"><?=$this->payrolloptions->monthname();?></select></div>
                                    <div class="col-md-2"><span><b>Year</b></span></div>
                                    <div class="col-md-5" style="padding-right:0px;" ><select class="form-control chosen-select" name="pyearto" id="pyearto"><?=$this->payrolloptions->periodyear();?></select></select></div>  
                                  </div>
                            </div>
                            <div class="form_row">
                                  <label class="field_name align_right">Certified Correct</label>
                                  <div class="field">
                                        <select class="chosen-select col-md-4" name="certifiedcorrect" id="certifiedcorrect">
                                                  <option value=''>--Select Employee--</option>
                                                  <?
                                                  $opt_type = $this->employee->loadallemployees();
                                                    foreach($opt_type as $val){
                                                  ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                                    }
                                                  ?>
                                        </select>
                                  </div>
                            </div>

                            <!-- div class="form_row">
                                  <label class="field_name align_right">Sort:</label>
                                  <div class="field no-search">
                                        <select class="chosen-select col-md-4" name="sort_by" id="sort_by">
                                          <option value="department">Office</option>
                                          <option value="employee">Alphabetical Order</option>
                                        </select>
                                  </div>
                            </div> -->

                            <div class="form_row">
                                <label class="field_name align_right">Sort</label>
                                <div class="field">
                                      <div class="options">
                                            <label title="PDF">
                                                <input type="radio"  name="sort_by" value="employee" checked/> 
                                                <img />
                                                Alphabetical Order
                                            </label>
                                      </div>
                                </div>
                          </div>

                            <div class="form_row">
                                <label class="field_name align_right">Format</label>
                                <div class="field">
                                      <div class="options">
                                            <label title="PDF">
                                                <input type="radio"  name="reportformat" value="pdf" checked/> 
                                                <img />
                                                PDF
                                            </label>
                                      </div>
                                </div>
                          </div>
            <?}else{?>

                            <div class="form_row">
                                  <label class="field_name align_right">Period Cover</label>
                                  <div class="field no-search">
                                    <div class="col-md-5" style="padding-left: 0px;"><select class="form-control chosen-select" name="period" id="period"><?=$this->payrolloptions->monthname($this->input->post("rfile"));?></select></div>
                                    <div class="col-md-2"><span><b>Year</b></span></div>
                                    <div class="col-md-5" style="padding-right: 0px;"><select class="form-control chosen-select" name="pyear" id="pyear"><?=$this->payrolloptions->periodyear();?></select></select></div>  
                                  </div>
                            </div>
                            
                            <div class="form_row">
                                  <label class="field_name align_right">Type</label>
                                  <div class="field no-search">
                                        <select class="form-control chosen-select" name="tnt" id="tnt">
                                        <?
                                          $type = array(""=>"-- Select All --","teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                          foreach($type as $c=>$val){
                                          ?><option value="<?=$c?>"><?=$val?></option><?
                                          }
                                        ?>
                                        </select>
                                  </div>
                            </div>
                            
                            <div class="form_row" id="estat">
                                  <label class="field_name align_right">Employment Status</label>
                                  <div class="field no-search">
                                        <select class="form-control chosen-select" name="estatus" id="estatus">
                                        <?
                                          $opt_status = $this->extras->showStatus();
                                          foreach($opt_status->result() as $row){
                                          ?><option value="<?=$row->code?>"><?=$row->description?></option><?
                                          }
                                        ?>
                                        </select>
                                  </div>
                            </div>
                            <div class="form_row" id="edept">
                                  <label class="field_name align_right">Department</label>
                                  <div class="field no-search">
                                        <select class="chosen-select col-md-4" name="deptid" id="deptid">
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
                                        <select class="chosen-select col-md-4" name="employeeid" id="employeeid">
                                        <?
                                          $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",false,'teaching');
                                          foreach($opt_type as $val){
                                          ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                          }
                                        ?>

                                        </select>
                                  </div>
                            </div>
            <?}?>
                      </div>
                </div>
                      <div class="modal-footer">
                                  <div id="loading" hidden=""></div>
                                        <div id="saving">    
                                              <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                                              <button type="button" id="gen" class="btn btn-success">Generate</button>
                                        </div>
                                  </div>
                            </div>
                      </div>
</form>
<script>
  var toks = hex_sha512(" ");
  $("#deptid, #campusid, #tnt").unbind().change(function(){
    var deptid = $("#deptid").val();
    var campusid = $("#campusid").val();
    var etype = $("#tnt").val();
    $.ajax({
      type:'POST',
      url:"<?=site_url('reports_/loadEmployee')?>",
      data: {deptid: GibberishAES.enc(deptid , toks), campusid: GibberishAES.enc(campusid , toks), etype: GibberishAES.enc( etype, toks), toks:toks},
      success:function(html){
          $("select[name='employeeid']").html(html).trigger('chosen:updated');
      }
    }); 
  });


  $("input[tag='deductions']").on('change',function(){
   $("input[tag='deductions']").not(this).prop("checked",false);
     if ($(this).is(":checked")) {
         if ($(this).val() == "DEDUCTION") {
            select = "<?=$this->employeemod->displayIncomeReportCutOff()?>";
            $(".deduction").show();
            $(".loan").hide();
            $(".all").hide();
            $("select[name='cutoff']").html(select).trigger('liszt:updated');
         }
         else if ($(this).val() == "LOAN") {
            select = "<?=$this->employeemod->displayIncomeReportCutOff(true)?>";
            $(".deduction").hide();
            $(".loan").show();
            $(".all").hide();
            $("select[name='cutoff']").html(select).trigger('liszt:updated');
         }
         else
         {
            $(".deduction").hide();
            $(".loan").hide();
            $(".all").show();
         }
    }
    else
    {
          $(".deduction").hide();
          $(".loan").hide();
          $(".all").hide();
    }
  });

 $('#viewtype').change(function(){
    var viewtype = $("#viewtype").val();
    if(viewtype == 'detailed'){
        $(".detailedstatus").show();
    }
     if(viewtype == 'summary'){
        $(".detailedstatus").hide();
    }
 });

  $("select[name='deptid']").change(function(){
  $.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: {
           deptid :  GibberishAES.enc($(this).val() , toks),
           toks:toks
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('liszt:updated');
        }
    });   
});



$("#gen").click(function(){
	
   var form_data   =   $("#frmsetup").serialize();  
   var rfile = "<?=$rfile?>";
   
   iscontinue = true;
   
  if(rfile == "rdcForm")
	{
		if($("#cutoff").val() == "" || rfile == "mrrReport")
		{
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Please select cutoff!',
          showConfirmButton: true,
          timer: 1000
      })
			iscontinue = false;
		}

    if($("#deduction").val() == "")
    {
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Reglementary is required.',
          showConfirmButton: true,
          timer: 1000
      })
      iscontinue = false;
    }
	}
	
	if(rfile == "alphalistform_new")
	{
		if($("#year").val() <= 0)
		{
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Please input year!',
          showConfirmButton: true,
          timer: 1000
      })
			iscontinue = false;
		}
	}

  // added by justin (with e) for ica-hyperion 21578
	if(rfile == "ssscontri" || rfile == "philcontri" || rfile == "hdmfcontri")
  {
    if ($("#pyearfrom").val() > $("#pyearto").val() ) {
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Check Year Range.',
          showConfirmButton: true,
          timer: 1000
      })
      iscontinue = false;
    }else if( ($("#pyearfrom").val() == $("#pyearto").val()) && ($("#pfrom").val() > $("#pto").val()) ){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Check Month Range.',
          showConfirmButton: true,
          timer: 1000
      })
      iscontinue = false;
    }
  }
    // console.log(form_data);
   // /return false;    
	if(iscontinue)
	{
	   if(rfile == "incomereportxls"){
      if($("#cutoff").val() == ""){ 
        Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Cutoff Required.',
          showConfirmButton: true,
          timer: 1000
      })
      }else if($("#income").val() == null){ 
        Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Income Required.',
          showConfirmButton: true,
          timer: 1000
      })
      }else{
        $("#frmsetup").attr("target", "_blank");
        $("#frmsetup").attr("method", "post");
        $("#frmsetup").append("<input type='hidden' value='"+ $('input[name="reportformat"]:checked').val() +"' name='format_'>");
        $("#frmsetup").attr("action", "<?=site_url("forms/showIncomeAndOtherIncomeTransactionReportPerIncome")?>");
        $("#frmsetup").submit();
      }
     }else if(rfile == "incomeadj" || rfile == "incomeadjemp"){
        var error = "";
        if(!$("#cutoff").val()) error = "Cutoff is required.";
        if($("#income").val() == null && !error) error = "Income is required.";

        if(!error){
            $("#frmsetup").attr("target", "_blank");
            $("#frmsetup").attr("action", "<?=site_url("forms/showIncomeAdjustmentReport")?>");
            $("#frmsetup").attr("method", "post");
            $("#frmsetup").submit();
        }else{
          Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: error,
          showConfirmButton: true,
          timer: 1000
      })
        }
	   }else if(rfile == "incomereportemployeexls"){
        if($("#cutoff").val() == ""){ 
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: "Cutoff Required",
              showConfirmButton: true,
              timer: 1000
          })
        }else if($("#income").val() == null){ 
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: "Income Required",
              showConfirmButton: true,
              timer: 1000
          })
        }else{
          
          $("#frmsetup").attr("target", "_blank");
          $("#frmsetup").append("<input type='hidden' value='"+ $('input[name="reportformat"]:checked').val() +"' name='format_'>");
          $("#frmsetup").attr("action", "<?=site_url("forms/showIncomeAndOtherIncomeTransactionReportPerEmployee")?>");
          $("#frmsetup").attr("method", "post");
          $("#frmsetup").submit();
        }
     }else if(rfile == "rdcForm"){
        // window.open("<?=site_url("reports_/loadRDCForms")?>?"+ form_data);
        $("#frmsetup").attr("target", "_blank");
        $("#frmsetup").attr("action", "<?=site_url("reports_/loadRDCForms")?>");
        $("#frmsetup").attr("method", "post");
        $("#frmsetup").submit();
    
     }
     // added by justin (with e) for ica-hyperion 21578
     else if (rfile == "ssscontri" || rfile == "philcontri" || rfile == "hdmfcontri" || rfile == "mp2contri" || rfile == "pagibigvolcontri") {
      if(($("#pfrom").val() == '' || $("#pto").val() == '')){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Check Month Range.',
          showConfirmButton: true,
          timer: 1000
      })
      return;
    }
    if(($("#pfrom").val() == '' || $("#pto").val() == '')){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Check Month Range.',
          showConfirmButton: true,
          timer: 1000
      })
      return;
    }
      if($("#certifiedcorrect").val() == ''){
        Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: "Certified Correct is required",
              showConfirmButton: true,
              timer: 1000
          })
        return;
      }
      if ($("#pyearfrom").val() > $("#pyearto").val() ) {
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Check Year Range.',
          showConfirmButton: true,
          timer: 1000
      })
      return;
    }
    if( ($("#pyearfrom").val() == $("#pyearto").val()) && ($("#pfrom").val() > $("#pto").val()) && (("#pfrom").val() == '' || ("#pto").val() == '') ){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Check Month Range.',
          showConfirmButton: true,
          timer: 1000
      })
      return;
    }   
        // alert($('input[name="reportformat"]:checked').val());
        form_data = $("#frmsetup").serialize();
        $('#frmsetup input, #frmsetup select, #frmsetup textarea').each(function(){
          form_data += '&'+$(this).attr('name')+'='+GibberishAES.enc($(this).val(), toks);
        })
        form_data += "&toks="+toks;
        var encodedData = encodeURIComponent(window.btoa(form_data));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
     }
    else if(rfile == "alphalistform_new")
     {
        var site_url = "<?=site_url("forms/showAlphalistNew")?>";

        $("#frmsetup").attr("target", "_blank");
        $("#frmsetup").attr("action", site_url);
        $("#frmsetup").attr("method", "post");
        $("#frmsetup").submit();
     }

     else if(rfile == "mrrReport"){
       
        form_data = $("#frmsetup").serialize();
        // form_data = 'f=1';
        $('#frmsetup input, #frmsetup select, #frmsetup textarea').each(function(){
          form_data += '&'+$(this).attr('name')+'='+GibberishAES.enc($(this).val(), toks);
        })
        form_data += "&isMRRReport="+GibberishAES.enc("1", toks);
        form_data += "&toks="+toks;
        // alert(form_data);
        var encodedData = encodeURIComponent(window.btoa(form_data));
        if($('input[name=format]:checked').val() == 'PDF') openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
        else                                               openWindowWithPost("<?=site_url("forms/loadExcelReportPost")?>", {formdata: encodedData});

    }

     else if(rfile == 'employeeBalancesPerEmployee' || rfile == 'employeeBalancesPerDeduction')
     {
       if(!$("select[name='cutoff']").val()){
          alert("Select Cut-Off");
          return;
       }

       var site_url = (rfile == 'employeeBalancesPerEmployee') ? "<?=site_url("forms/showEmployeeBalancesPerEmployee")?>" : "<?=site_url("forms/showEmployeeBalancesPerDeduction")?>";

       $("#frmsetup").attr("target", "_blank");     
       $("#frmsetup").append("<input type='hidden' value='"+ $('input[name="reportformat"]:checked').val() +"' name='format_'>");
       $("#frmsetup").attr("action", site_url);
       $("#frmsetup").attr("method", "post");
       $("#frmsetup").submit();


        /*$.ajax({
          url: "<?= site_url('reports_/checkIfhasData') ?>",
          type: "POST",
          data:{cutoff:$("#cutoff").val()},
          success:function(response){
            if(response){
               //window.open("<?=site_url("forms/showPDFReport")?>?"+form_data);
               $("input[name='form']").val("employeeBalances");
               $("#frmsetup").attr("target", "_blank");
               $("#frmsetup").attr("action", "<?=site_url("forms/showPDFReport")?>");
               $("#frmsetup").attr("method", "post");
               $("#frmsetup").submit();
            }
            else alert("No existing data to generate. Choose another date.");
          }
        });*/
     }
	}
});

$("#estat").hide();
$("#tnt").change(function(){
    if($(this).val() != ""){
      $('#employeeid, #deptid, #estatus').removeAttr("disabled").trigger('liszt:updated');
    }else{
      $('#employeeid, #deptid, #estatus').attr("disabled","disabled").trigger('liszt:updated');
    }
   if($(this).val() == "teaching"){
    $("#estat").hide();
    $("#edept").show();
    loadempopt($(this).val());
   }else if($(this).val() == "nonteaching"){
    $("#edept").hide();
    $("#estat").show();
    loadempopt($(this).val())
   }
});
function loadempopt(etype = ""){
    $.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: {
           etype :  GibberishAES.enc( etype, toks),
           toks:toks
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('liszt:updated');
        }
    });   
}
// #Naces for chosen-select... adding Select All option in multiple select
$("#income").on("change", function(){
  var selectAll = $(this).val();
  var elementId = $(this).attr("id");
  
      if(jQuery.inArray("selectAll",selectAll) != -1){
        $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").hide();});
      }else{
        $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
      }

});

$("#income").change(function(){
    if($(this).val()){
      if(!$(this).val().includes("allincome")){
        $('#income option[value="allincome"]').attr("disabled", true).trigger("chosen:updated");
        $('#income option[value!="allincome"]').attr("disabled", false).trigger("chosen:updated");
      }else{
        $('#income option[value!="allincome"]').attr("disabled", true).trigger("chosen:updated");
        $('#income option[value="allincome"]').attr("disabled", false).trigger("chosen:updated");
      }
    }else{
      $('#income option[value="allincome"]').attr("disabled", false).trigger("chosen:updated");
      $('#income option[value!="allincome"]').attr("disabled", false).trigger("chosen:updated");
    }
  });

$("#department").change(function(){
    $.ajax({ 
        url : "<?=site_url('setup_/getOffice')?>",
        type: "POST",
        data: {department:GibberishAES.enc($(this).val(), toks), toks:toks},
        success: function(msg){
            $("#office").html(msg).trigger("chosen:updated");
        }
    });
});

$(".chosen-select").chosen();
</script>