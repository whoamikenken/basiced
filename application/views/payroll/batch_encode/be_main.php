<link href="<?=base_url();?>css/batch_encode/be_list.css" rel="stylesheet">
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;

}
#IncomeClr{
    color: blue;
    text-decoration: underline;
    font-style: italic;
    font-weight: bold;
    font-size: 13px;
    float: right;
    margin-top: 5%;
}

#DeducClr{
    color: blue;
    text-decoration: underline;
    font-style: italic;
    font-weight: bold;
    font-size: 13px;
    float: right;
    margin-top: 5%;
}
#LoanClr{
    color: blue;
    text-decoration: underline;
    font-style: italic;
    font-weight: bold;
    font-size: 13px;
    float: right;
    margin-top: 5%;
}

.emplist{
        width: 100%;
        margin-bottom: 15px;
        overflow: visible !important;
        overflow-y: visible !important;
        overflow-x: visible !important; 
        -ms-overflow-style: -ms-autohiding-scrollbar !important;
        border: 1px solid #ddd;
        -webkit-overflow-scrolling: touch !important;
}

    .datepicker:before, .datepicker:after {
    content: none;
}

</style>
<?php
    $campus_list = $this->extras->getCampusDescription();
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
                <div class="panel animated fadeIn delay-1s">
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Batch Encoding</b></h4></div>
                    <div class="panel-body">
                         <label id="batch_process" style="margin-left: 1%;">Batch Process Same Amount: </label>
                         <label id="income-modal">Generate Income (13th Month, 14th Month, Year End Bonus): </label>
                         <br>
                        <div class="col-md-6">
                        <div class="form-row" style="<?= (count($campus_list) > 1) ? '' : 'pointer-events: none;'; ?>">
                            <div class="col-md-12" >
                                <label class="field_name col-md-3">Campus :</label>
                                <div class="field col-md-9" >
                                    <select class="form form-control " id="campus" name="campus">
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
                        </div>
                        <br><br>
                        <div class="form-row" >
                            <div class="col-md-12" id="edept" >
                                <label class="field_name col-md-3">Status :</label>
                                <div class="field col-md-9">
                                    <select class="form form-control " name="status" id="status" >
                                        <option value="">All Status</option>
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
                                    <select class="form form-control " name="teachingtype" id="teachingtype">
                                        <option value="">All Type</option>
                                        <option value="teaching">Teaching</option>
                                        <option value="nonteaching">Non Teaching</option>
                                    </select>
                                </div>
                            </div>
                        </div>    
                        <br><br>
                        
                            
                        </div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-md-12">
                                <label class="field_name col-md-3">Department :</label>
                                <div class="field col-md-9">
                                    <select class="form form-control " name="deptid" id="department">
                                        <?=$this->extras->getDeptpartment()?>
                                    </select>
                                </div>
                            </div>
                            <br><br>
                            <div class="col-md-12">
                                <label class="field_name col-md-3">Offices :</label>
                                <div class="field col-md-9">
                                    <select class="form form-control " name="office" id="office" >
                                        <?=$this->extras->getOffice()?>
                                    </select>
                                </div>
                            </div>
                			<div class="col-md-12" hidden=''>
                				<label class="field_name col-md-3">Employee Status :</label>
                				<div class="field col-md-9">
                					<select class="form form-control " name="employmentstat">
                				    </select>
                				</div>
                			</div>
                            <br><br>
                            <div class="form-row">
                            <div class="col-md-12">
                                <label class="field_name col-md-3">Category :</label>
                                 <div class="field col-md-9">
                                    <select class="form form-control  category" name="category" id="category">
                                    </select>
                                </div>
                            </div><br><br>
                            <div class="col-md-12">
                                <label class="field_name col-md-3"></label>
                                <div class="field col-md-9">
                                    <button class="btn btn-primary" id="be_search">Search</button>
                                </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div id="encode_body" class="panel" style="display: none;"  >
                    <div class="panel-heading income_categ" style="background-color: #0072c6;"  id="income_categ"><h4><b>Income</b></h4></div>
                    <div class="panel-heading Deduction" style="background-color: #0072c6;" id="deduc_categ"  ><h4><b>Deduction</b></h4></div>
                    <div class="panel-heading " style="background-color: #0072c6;" id="loan_categ"  ><h4><b>Loan</b></h4></div>
                    <div class="panel-body" id="incomeCateg">
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-12" >
                                    <label class="field_name col-md-3 categorytype"></label>
                                    <div class="field col-md-9" >
                                        <select class="chosen " name="code_type" id="code_type" > </select>
                                    </div>
                                </div>
                            </div>
                            <br><br>   
                            <div class="form-row">
                                <div class="col-md-12" id="edept">
                                    <label class="field_name col-md-3">Payroll Cut-off:</label>
                                    <div class="field col-md-9">
                                        <div class="span12 no-search">
                                            <select class="chosen span6" name="schedule" id="schedule">
                                                <option value="">Select Cut-off</option>
                                                <option value="1">1st Cut-Off</option>
                                                <option value="2">2nd Cut-Off</option>
                                                <option value="3">All Cut-Off</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>    
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-row">
                                <div class="col-md-12" id="IncomeClr" style="margin-top: 5%;" >
                                     <label><u><a id="ClrIncome" style="font-weight: 700; cursor: pointer;">Clear all Zero no. of cut off</a></u></label>
                                </div>
                                <div class="col-md-12" id="DeducClr" style="margin-top: 5%;" >
                                     <label><u><a id="ClrDeduction" style="font-weight: 700; cursor: pointer;">Clear all Zero no. of cut off</a></u></label>
                                </div>
                                <div class="col-md-12" id="LoanClr" style="margin-top: 5%;" >
                                     <label><u><a id="ClrLoan" style="font-weight: 700; cursor: pointer;">Clear all Zero no. of cut off</a></u></label>
                                </div>     
                            </div>
                        </div>
                    </div> 
                </div>
                <div id="wrapListEncode" style="position: static;">  
                </div>
                    <a id="showsetup" href="#" data-toggle="modal" data-target="#encode_process" hidden="" ></a>
                    <div class="modal fade" id="encode_process" data-backdrop="static"></div>
        </div>
    </div>
</div>

<div id="loading"><img src='<?=base_url()?>images/loading.gif'/> Saving.. Please wait.</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<input type="hidden" id="canwrite" value="<?=$this->session->userdata('canwrite')?>">
<input type="hidden" id="username" value="<?= $this->session->userdata('username') ?>">
<!-- <script type="text/javascript">
    setTimeout(function(){ 
        $("#code_type").removeClass("input[name='code_type']");
        $("#removeAni").removeClass("animated fadeIn delay-1s");
    }, 2000);
</script> -->
<script src="<?=base_url()?>js/batch_encode/filter.js"></script>
<script>
    validateCanWrite();
    function validateCanWrite(){
        if("<?=$this->session->userdata('canwrite')?>" == 0){
            $("#batch_process").css("pointer-events", "none");
            $("#income-modal").css("pointer-events", "none");
        }
        else{
            $("#batch_process").css("pointer-events", "");
            $("#income-modal").css("pointer-events", "");
        }
    }
</script>