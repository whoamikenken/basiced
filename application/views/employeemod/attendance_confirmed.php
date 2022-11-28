<?php 
/**
 * @author Angelica Arangco
 * @copyright 2018
 */

?>

<style>
#attstbl tr th,#asctblnt tr th{
    background-color: #0072c6;
    color: black;
}

#attstbl tbody tr.is-consecutive{
    background-color: rgb(255, 102, 102); 
    color: rgb(255, 255, 255);
}
#attstbl tbody tr.is-consecutive:hover{
    background-color: rgb(255, 102, 102); 
    color: black;
}  
p{
    color:black;
}

</style>

<h3>Attendance Confirmed <?=$teachingtype?strtoupper($teachingtype):''?></h3>
<p><?=$dateRange?></p>

<div class="well_content">
	<div class="pull-right">
	    <span id="cmsg" style="color: red;font-weight: bold;"></span>
	    
	</div>
  	<table class="table table-bordered datatable" id="asctblnt">
	  	<thead>
	  		 <tr style="background-color: #0072c6;">
                <th class="sorting_asc" rowspan="2" style="background-color: #0072c6;">Employee ID</th>
                <th rowspan="2" style="background-color: #0072c6;">Name</th>
                <th class="align_center" colspan="3" style="background-color: #0072c6;">Overtime (hr:min)</th>
                <th class="align_center" style="background-color: #0072c6;">Late/Undertime Deduction</th>
                <th class="align_center" rowspan="2" style="background-color: #0072c6;">Absent</th>                        
                <th class="align_center" colspan="3" style="background-color: #0072c6;">Leaves</th>
                <th class="align_center" rowspan="2" style="background-color: #0072c6;">No. of Days</th>
                <th class="align_center" rowspan="2" style="background-color: #0072c6;">Holiday</th>
            </tr>
            <tr >
                <th class="align_center" style="background-color: #0072c6;">Regular</th>
                <th class="align_center" style="background-color: #0072c6;">Rest Day</th>
                <th class="align_center" style="background-color: #0072c6;">Holiday</th>
                <th class="align_center" style="background-color: #0072c6;">Hr:min</th>            
                <th class="align_center" style="background-color: #0072c6;">VL</th>
                <th class="align_center" style="background-color: #0072c6;">SL</th>
                <th class="align_center" style="background-color: #0072c6;">Other</th>
            </tr>
	  	</thead>
	  	

	  		<? foreach ($attendance_list as $deptid => $dept_det) { ?>

	  				<tr>
	  					<td colspan="16">Department: <b> <?=isset($dept_list[$deptid]) ? $dept_list[$deptid] : ''?></b></td>
	  				</tr>

	  				<? foreach ($dept_det as $employeeid => $emp_det) { /*echo "<pre>"; print_r($emp_det); die;*/ ?>	
	  						<tbody class="hover">
				  				<tr class="pdata">
				                    <td class="align_center"><?=$employeeid?></td>
				                    <td class="align_center"><?=Globals::_e($emp_det['fullname'])?></td>
				                    <td class="align_center"><?=$emp_det['otreg']?></th>
				                    <td class="align_center"><?=$emp_det['otrest']?></th>
				                    <td class="align_center"><?=$emp_det['othol']?></th>
				                    <td class="align_center"><?=$emp_det['lateut']?></th>
				                    <?php  
				                    	if($emp_det["perdept_arr"]){
				                    		foreach($emp_det["perdept_arr"] as $perdept){
				                    ?>
				                    			<td class="align_center"><?=$perdept["ADMIN"]["deduc_hours"]?></td>
				                    <?php 
				                    		}
				                    	} ?>
				                    <td class="align_center"><?=$emp_det['vleave']?></td>
				                    <td class="align_center"><?=$emp_det['sleave']?></td>
				                    <td class="align_center"><?=$emp_det['oleave']?></td>
				                    <td class="align_center"><?=$emp_det['workdays']?></td>
				                    <td class="align_center"><?=$emp_det['isholiday']?></td>
				                </tr>
	  		
	  						</tbody>
	  				<?}
	  		} ?>

  	
  	</table>
</div>

<!-- ///< @Angelica >> script from views\employeemod\viewattconfirm.php -->
<input type="button" id="generate" class="btn btn-info align_right" value="Generate" style="cursor: pointer;margin: 0px 5px 5px 5px; float: right;" />
<?if($showfinalize){?>
    <div class="pull-right">
    <span id="cmsg" style="color: red;font-weight: bold;"></span>
        <input type="button" id="finalize" class="btn btn-primary blue" value="Finalize" style="cursor: pointer;" />

    </div>
<?}?>



<script>
	$("#finalize").click(function(){
	    $('.pdata').each(function() {
	        var eid = $(this).attr('employeeid');
	        $.ajax({ 
	            url      : "<?=site_url("employeemod_/loadmodelfunc")?>",
	            type     : "POST",
	            data     : {
	                            model: "payrollconfirm",
	                            tnt  : "<?=$teachingtype?>",
	                            dfrom: "<?=$sdate?>",
	                            dto  : "<?=$edate?>",
	                            eid  : eid
	                        },
	            success  : function(msg){
	                var data = $.parseJSON(msg);
	                $("#finalize").hide();
	                $("#generate").hide();
	                $("#cmsg").text(data[1]);
	                
	            }
	        });
	    });
	});

	$(document).ready(function(){
		$('.pdata').each(function() {
	        var isConsecutive = $(this).attr('isConsecutive');
	        if(isConsecutive == 1){
	        	$(this).css({"background-color": "#ff6666", "color": "white"});
	        }
	    });
	});

	$("#generate").click(function(){
		var cutoff = "<?= $cutoff ?>";
		var teachingtype = "<?= $teachingtype ?>";
		window.open("<?=site_url("forms/generateConfirmedAttendance")?>?cutoff="+cutoff+"&teachingtype="+teachingtype); 
	});


    if("<?=$this->session->userdata('canwrite')?>" == 0) $("#finalize").css("pointer-events", "none");
    else $("#finalize").css("pointer-events", "");

</script>