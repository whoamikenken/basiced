<?php

$CI =& get_instance();
$CI->load->model('leave');
$credit = $avail = $bal = '';
if(isset($empinfo)){
   $empdetails = $empinfo;    
 }else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo;
 }
$employeeid = $empdetails[0]['employeeid'];
$ishidden = $isdisabled = $isreadonly = "";
$cansave = true;

	if($this->session->userdata("usertype") == "EMPLOYEE"){
		$ishidden   = " hidden";
		$isdisabled = " disabled";
		$isreadonly = " readonly";
		$cansave   = $this->db->query("SELECT * FROM employee_restriction WHERE employeeid='$employeeid'")->num_rows();
	
	
	}
	$userid = $this->session->userdata("userid");
	$readWrite = $this->db->query("SELECT * FROM user_access WHERE userid = $userid AND menu_id = 97");
	if($readWrite->num_rows() != 0)
	{
		foreach($readWrite->result() as $rw)
		{
			// $read = $rw->read;
			$write = $rw->write;
		}
		// if($read == "YES"){ $isreadonly = " readonly"; $isdisabled = " disabled";$ishidden   = " hidden";}
		if($write == "YES") {$isreadonly = ""; $isdisabled = "";$ishidden   = "";$cansave=true; }
		else {
			$ishidden   = " hidden";
			$isdisabled = " disabled";
			$isreadonly = " readonly";
			$cansave   = $this->db->query("SELECT * FROM employee_restriction WHERE employeeid='$employeeid'")->num_rows();
		}
	}

    $leaves = $CI->leave->getEmpLeaveCredit($employeeid);
    $leaves = $CI->leave->recalculateEmpLeaveCredit($employeeid, $leaves->result());

    
    
    // echo "<pre>"; print_r($leavesHistory->result()); die;


?>
<style>
    input, .table th, .table td{ 
        text-align: center; 
    }
</style>
<br><br>
<div class="widgets_area animated fadeIn delay-1s">
<div class="row">
<form id="leaveCredit">  
	<div class="col-md-12">
		<div class="well-content no-search" style="border: 0 !important;">
            <span class='pull-left' id="msg" style="color: red;"></span>
			<?if($cansave){?>
			<div class='align_left'>
                <a id="addleavecredit" href="#modal-view" class="btn btn-primary" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign pull-left" ></i> Add Leave Credit</a>
					<!-- <a href="#" class="btn btn-primary" id="saveLeaveCredit" style="float: right; ">Save All Information</a> -->
                    <!-- <label class="text-info" style="float: right; margin-top: 10px; margin-right: 10px">
					<b>(Click SAVE for each tab you accomplish)</b></label>  -->
			</div>
			<?}?>
			<br>
			
            <? 
              $utype = $this->session->userdata('usertype');

              if( $utype == "ADMIN" || $utype = "SUPER ADMIN") { 

            ?>
                <div class="panel">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Leave</b></h4></div>
                   <div class="panel-body">
                        <table class="table form">
                            <thead style="background-color: #0072c6;">
                                <th>Code</th>
                                <th>Description</th>
                                <th>Credit</th>
                                <th>Availed</th>
                                <th>Balance</th>
                                <th></th>
                                <th>From</th>
                                <th>To</th>
                            </thead>
                            <tbody>
                                <?

                                if($leaves->num_rows() > 0){

                                    foreach ($leaves->result() as $key => $row):
                                        if($row->leavetype != "SC"):
                                ?>
                                        <tr>
                                            <td><?=$row->leavetype?></td>
                                            <td><?=$row->description?></td>
                                            <td><input type="number" lt="<?=$row->leavetype?>" min="<?=$row->avail?>"  class="credit form-control" name="<?=$row->leavetype . 'credit'?>" value="<?=$row->credit?>" style="border-color: #82B1FF;"></td>
                                            <td><input type="number" class="avail form-control" name="<?=$row->leavetype . 'avail'?>" value="<?=$row->avail?>" readonly style="border-color: #82B1FF;"></td>
                                            <td><input type="number" lt="<?=$row->leavetype?>" class="credit form-control" name="<?=$row->leavetype . 'balance'?>" class="balance" value="<?=$row->balance?>" style="border-color: #82B1FF;" readonly></td>
                                            <td><span class="glyphicon glyphicon-ok-circle" id="<?=$row->leavetype.'check'?>"  style="color: green; font-size: 20px; display: none"><p style="font-family: 'Poppins', sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span></td>
                                            <td><?=$row->dfrom?></td>
                                            <td><?=$row->dto?></td>
                                        </tr>

                                <?
                                        endif;
                                    endforeach;
                                }
                                else{
                                    ?>
                                        <tr>
                                            <td>No leave setup..</td>
                                        </tr>
                                    <?
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <? } ?>

            <?php 
                $leavesHistory = $CI->leave->getEmpLeaveCreditHistoryByYear($employeeid);
            ?>
            <div class="panel  animated fadeIn delay-1s">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Leave Credit History</b></h4></div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" id="leave_request" width="100%">
                        <thead style="background-color: #0072c6; color: black;">
                          <tr>
                            <th class="align_center" width="10%">Actions</th>
                            <th width="90%">Year</th>
                          </tr>
                        </thead>   
                        <tbody id="yearList">
                            <?php if (count($leavesHistory) > 0 ): ?>
                                <?php foreach ($leavesHistory as $key => $value): ?>
                                    <tr>
                                        <td tag='deduct' class="align_center col-md-1">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#content-<?php echo  $value->id ?>"><span class="glyphicon glyphicon-arrow-down"></span></button>
                                            </div>
                                        </td>
                                        <td>
                                            <b><?php echo date("Y", strtotime($value->dfrom)) ?></b>
                                            <div id="content-<?php echo  $value->id ?>" class="collapse">
                                                <table class="table table-hover table-striped table-bordered app_base_list">
                                                    <thead> 
                                                        <tr>    
                                                            <th>Leave Type</th>
                                                            <th>Balance</th>
                                                            <th>Credit</th>
                                                            <th>Availed</th>
                                                            <th>Validity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                            $recordLeavew = $CI->leave->getEmpLeaveCreditHistory($value->employeeid, "", date("Y", strtotime($value->dfrom)));
                                                                foreach($recordLeavew->result() as $row){ 
                                                                    ?>
                                                                    <tr>
                                                                        <td class=" align_center"><?php echo  strtoupper($row->description) ?></td>
                                                                        <td class=" align_center"><?php echo  strtoupper($row->balance) ?></td>
                                                                        <td class=" align_center"><?php echo  strtoupper($row->credit) ?></td>
                                                                        <td class=" align_center"><?php echo  strtoupper($row->avail) ?></td>
                                                                        <td class=" align_center"><?php echo  $row->dfrom." - ".$row->dto ?></td>
                                                                    </tr>
                                                            <?php
                                                                }
                                                        ?>
                                                    </tbody>
                                                </table>                       
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
</form>
</div>
</div>

<script>
	$("#saveLeaveCredit").click(function(){

		var form_data = $("#leaveCredit").serialize();
            form_data += "&employeeid=<?=$employeeid?>";
		$.ajax({
            url: "<?=site_url("leave_/saveLeaveCredit")?>",
            data : form_data,
            type : "POST",
            success:function(msg){
				$('#msg').html(msg);
            }
         }); 		
	});

    $('#leaveCredit').on('input, change','.credit',function(){
        var lt      = $(this).attr('lt'),
            credit  = $('input[name='+lt+'credit').val(),

            avail   = $('input[name='+lt+'avail').val(),
            bal     = credit - avail;
            // console.log(lt+'bal');
            $('input[name='+lt+'balance').val(bal);
    });

    $('#leaveCredit').on('change, blur','.credit',function(){
        var lt      = $(this).attr('lt');
        var avail = 0;
        var balance = $("input[name='"+lt+"balance']").val();
        var credit = $("input[name='"+lt+"credit']").val();
        
        if(credit > balance) var avail = credit - balance;
        $.ajax({
            url: "<?=site_url("leave_/updateLeaveCredit")?>",
            data : {balance:balance, credit:credit, avail:avail, employeeid:"<?=$employeeid?>", lt:lt},
            type : "POST",
            success:function(msg){
                $("#"+lt+"check").css("display", "unset");
                $("input[name='"+lt+"avail']").val(avail);
                setTimeout(function(){ $("#"+lt+"check").css("display", "none"); }, 1000);
            }
        })

    });


    $('#addleavecredit').on('click',function(){
        $("#modal-view").find("h3[tag='title']").text("Add Leave Credit");
        $("#button_save_modal").text("Save");   
        $(".grey,#button_save_modal").show();
        $(".loading").hide(); 
        $.ajax({
            url: "<?=site_url("leave_/loadLeavePage")?>",
            data : {'view':'employee/leave_add_credit',employeeid:"<?=$employeeid?>"},
            type : "POST",
            success:function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
         }); 
    });


    function recountSC(){
        $.ajax({
            url : "<?=site_url("service_credit_/displayRecountSC")?>",
            type : "POST",
            data : { employeeid : "<?=$employeeid?>" },
            dataType : "json",
            success : function(response){
                for(tag in response) $(".tbl-sc-counter").find("td[tag='"+ tag +"']").html(response[tag]);
            }
        });
    }

    function displayAvailableSC(){
        recountSC();
        $.ajax({
            url : "<?=site_url("service_credit_/displayAvailableSC")?>",
            type : "POST",
            data : { employeeid : "<?=$employeeid?>" },
            success : function(content){
                $(".available-sc-div").html(content);
            }
        });
    }

    displayAvailableSC();

     setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn");
  }, 2000);
     
</script>