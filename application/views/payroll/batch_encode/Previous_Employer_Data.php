


<style>
	select{
       font-family: Cursive;
       font-size: 13px;
       -moz-appearance: none;
     }
</style>

<script src="<?=base_url()?>jsbstrap/bootstrap.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
</script>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/fixedColumns.dataTables.min.css">


<div class="well blue">
    <div class="well-header">
        <h5>Previous Employer Data</h5>
    </div>
    <div class="well-content">
        	  
        <div class="form_row"><br>
            <label class="align_left span2">Encoding Type : </label>&nbsp;&nbsp;
            <select class="chosen span6" id="encodetype">
                <?php $encodeType = array("" => "---Select Type---", "1" => "Minimum Wage", "2" => "Non Minimum Wage");?>
                <?php foreach($encodeType as $key => $row): ?>
                <option value="<?= $key?>"><?=$row?></option>
            <?php endforeach ?>
            </select>
      <select class="chosen" name="pyear" id="pyear" style="width: 100px;margin-left: 200px;"><?=$this->payrolloptions->periodyear();?></select>


        </div>
     
        
        </div>
           <div class="form_row" id="encodeTable">
        </div>
    </div>
</div>


<!-- do script -->

<script type="text/javascript">
    var $j = jQuery.noConflict();
    var employeeid;
    $j("#employeeid").change(function(){
        employeeid = $("#employeeid").val();
    });

   $j("#encodetype").change(function(){
       var selectedType = $("#encodetype").val();
      if(selectedType == "1"){
            $.ajax({
                type: "POST",
                url: "<?= site_url('payroll_/PreviousEmployerDataBatchEncodeMinimumWage')?>",
                data: {employeeid: employeeid},
                success:function(response){
                    $("#encodeTable").html(response);
                }

            });
       }
      if(selectedType == "2"){
            $.ajax({
                type: "POST",
                url: "<?= site_url('payroll_/PreviousEmployerDataBatchEncodeNonMinimumWage')?>",
                data: {employeeid: employeeid},
                success:function(response){
                    $("#encodeTable").html(response);
                }

            }); 
       }
   });

   $j(".chosen").chosen();
   $j(".chzn-select").chosen();
</script>
<script src="<?=base_url()?>jsbstrap/jquery-1.12.4.js"></script>
<script src="<?=base_url()?>jsbstrap/library/bootstrap-datepicker.js"></script>
<script src="<?=base_url()?>jsbstrap/library/chosen.jquery.min.js"></script>
<script src="<?=base_url()?>jsbstrap/library/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>jsbstrap/library/dataTables.fixedColumns.min.js"></script>