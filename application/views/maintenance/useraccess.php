<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
 $username = "";
 $lastname = "";
 $fistname = "";
 $middlename = "";
 $utype = "";
 $m=0;
 if($uid){
?>
<style>
	
	.modal-content{
    margin: auto;
    width: 1171px;
	}

	.modal-body{
    overflow: auto;
	}

	.list table{
		padding-left:5%;
		padding-right:5%;
	}
	
	.list table input{
		transform:scale(1);
	}

	@media (min-width: 992px){
		.modal-lg {
		    width: 1064px;
		}
	}
</style>
<div class="row" style="margin-right: 0px;margin-left: 0px;">
<?    

$usertype = $this->db->query("SELECT type FROM user_info WHERE id = {$uid}")->result();
?>
<table class='list'>
	<tr>	
	<?
	$wC = "";
	if($usertype[0]->type == "EMPLOYEE")
	{
		$wC = "AND a.emod = 1";
	}
	else
	{
		$wC = "AND a.emod = 0";
	}
	
  $sql = $this->db->query("SELECT IF(c.title IS NULL,a.menu_id,a.menu_id) as rootid,a.title,a.menu_id,b.read,b.write 
							FROM menus a
							LEFT JOIN menus c ON c.menu_id=a.root 
                            LEFT JOIN user_access b ON b.menu_id=a.menu_id AND b.userid='{$uid}' 
							WHERE a.status='SHOW' AND (a.root = '' OR a.root IS NULL)  $wC
							ORDER BY a.arranged")->result();
  
  // echo "<pre>";print_r($this->db->last_query());die;
  $AllAccess = $this->db->query("SELECT * FROM user_access WHERE userid = '$uid'")->result();
  $forDeptHead = array(85, 78, 172, 185, 203, 204);
  $username = $this->db->query("SELECT username FROM user_info WHERE id = '$uid'")->row()->username;
  if(in_array($username, $this->employee->getDeptHead($username)) || in_array($username, $this->employee->getDeptHead($uid,true)) || $this->employee->campus_principal($username)){
  	$forDeptHead = array();
  }
  

  foreach($sql as $row)
	{
		if($row->menu_id != 180){
			if(count($AllAccess) == 0) $row->read = $row->write = "YES";
			if(!in_array($row->menu_id, $forDeptHead)){
					?>
						<td style="vertical-align: top;">
							<table class="table table-striped table-condensed" width="100%">
								<tr>
									<th class='align_center'>R</th>
									<th class='align_center'>W</th>
									<th></th>
								</tr>
								<?if($row->menu_id == 64 || $row->menu_id == 85 || $row->menu_id == 119 || $row->menu_id == 185 || $row->menu_id == 172 || $row->menu_id == 178 || $row->menu_id == 179 || $row->menu_id == 203 || $row->menu_id == 204)
								  {

								  	// if($row->menu_id == 178){
							    //         if(in_array($username,$this->employee->getDeptHead($username)) || in_array($username, array($this->employee->getDeptHead($username,true))) || $this->employee->campus_principal($username)) $row->title = "Manage Clearance";
							    //         else $row->title = "My Clearance";
							    //     }

								  	?>
								  <tr class="menulist" menuid='<?=$row->menu_id?>'>
									<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=(($row->read == "YES")?" checked='checked'":"")?> ></td>
									<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=(($row->write == "YES")?" checked='checked'":"")?>></td>
									<th class='align_center'><?=Globals::_e($row->title)?></th>
								  </tr>
								<?}else{?>
								<tr class="menulist">
									<th class='align_center'><input type="checkbox" class="uniform allchecked" name="checkbox2" <?=(($row->read == "YES")?" checked='checked'":"")?> root='<?=$row->rootid?>R' tag='main'></th>
									<th class='align_center'><input type="checkbox" class="uniform allchecked" name="checkbox2" <?=(($row->write == "YES")?" checked='checked'":"")?> root='<?=$row->rootid?>W' tag='main'></th>
									<th class='align_center'><?=Globals::_e($row->title)?></th>
								</tr>
								<?}
									$query = $this->db->query("SELECT IF(c.title IS NULL,a.menu_id,c.menu_id) as rootid,a.title,a.menu_id,b.read,b.write 
											FROM menus a
											LEFT JOIN menus c ON c.menu_id=a.root 
											LEFT JOIN user_access b ON b.menu_id=a.menu_id AND b.userid='{$uid}' 
											WHERE a.status='SHOW' AND a.root = ".$row->rootid." $wC
											ORDER BY a.arranged")->result();
									// echo "<pre>"; print_r($query);
											
									
									foreach($query as $r)
									{	
										if($usertype[0]->type == "EMPLOYEE")
										{
											$notInclude = array(113,114,115,116,117,130);
											if(in_array($r->menu_id,$notInclude))
											{
												continue;
											}
										}
										if(count($AllAccess) == 0) $r->read = $r->write = "YES";
										?>
										<tr class="menulist" menuid='<?=$r->menu_id?>' rootid='<?=$r->rootid?>'>
											<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=(($r->read == "YES")?" checked='checked'":"")?> root='<?=$r->rootid?>R'></td>
											<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=(($r->write == "YES")?" checked='checked'":"")?> root='<?=$r->rootid?>W'></td>
											<td><?=$r->title?></td>
										</tr>

										<?
										if($r->menu_id == 21):
											$gateaccess = $ams_access = 0;
											$gate_q = $this->db->query("SELECT gateaccess, ams_access, gate_tap_allow, ams_tap_allow FROM user_info WHERE id='$uid'");
											if($gate_q->num_rows() > 0){
												$gateaccess = $gate_q->row(0)->gateaccess;
												$ams_access = $gate_q->row(0)->ams_access;
												$gate_allow = explode(",", $gate_q->row(0)->gate_tap_allow);
												$ams_allow = explode(",", $gate_q->row(0)->ams_tap_allow);
											}

											$allow_arr = array(
												"ST" => "Student",
												"ET" => "Employee (Teaching)",
												"ENT" => "Employee (Non Teaching)"
											);
										?>

										<tr style="display: none;">
										  	<td>&nbsp;</td>
											<td>&nbsp;</td>
											<td>
												<table width="100%">
													<tr class="gate">
														<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=($gateaccess?" checked='checked'":"")?> ></td>
														<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=($gateaccess?" checked='checked'":"")?>></td>
														<td class='align_center'>Gate</td>
													</tr>

													<?foreach($allow_arr as $value => $description):?>
													<tr class="gate-allow">
														<td class='align_center'>&nbsp;</td>
														<td class='align_center'>&nbsp;</td>
														<td class=''><input type="checkbox" class="uniform menucheck" name="checkbox2" value="<?=$value?>" <?=(in_array($value, $gate_allow)) ? "checked" : ""?>>&nbsp;<?=$description?></td>
													</tr>
													<?endforeach;?>

													<tr class="ams">
														<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=($ams_access?" checked='checked'":"")?> ></td>
														<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=($ams_access?" checked='checked'":"")?>></td>
														<td class='align_center'>AMS</td>
													</tr>
													<?foreach($allow_arr as $value => $description):?>
													<tr class="ams-allow">
														<td class='align_center'>&nbsp;</td>
														<td class='align_center'>&nbsp;</td>
														<td class=''><input type="checkbox" class="uniform menucheck" name="checkbox2" value="<?=$value?>" <?=(in_array($value, $ams_allow)) ? "checked" : ""?>>&nbsp;<?=$description?></td>
													</tr>
													<?endforeach;?>
												</table>
											</td>
										</tr>


										<? 
										endif;

										if($r->menu_id == 3)
										{
											?>
												<tr>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>
														<table width="100%">
											<?
											$que = $this->db->query("SELECT IF(c.title IS NULL,a.menu_id,c.menu_id) as rootid,a.title,a.menu_id,b.read,b.write 
												FROM menus a
												LEFT JOIN menus c ON c.menu_id=a.root 
												LEFT JOIN user_access b ON b.menu_id=a.menu_id AND b.userid='{$uid}' 
												WHERE a.status='SHOW' AND a.root = 63
												ORDER BY a.arranged")->result();
												
												foreach($que as $q)
												{
													
													
													?>
														<tr class="menulist" menuid='<?=$q->menu_id?>' rootid='<?=$q->rootid?>'>
															<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=($q->read?" checked='checked'":"")?> root='<?=$q->rootid?>R'></td>
															<td class='align_center'><input type="checkbox" class="uniform menucheck" name="checkbox2" <?=($q->write?" checked='checked'":"")?> root='<?=$q->rootid?>W'></td>
															<td><?=$q->title?></td>
														</tr>
													<?

													//for payroll access
													if($q->menu_id == 113){?>
														<tr class="payroll-access" menuid='<?=$q->menu_id?>' rootid='<?=$q->rootid?>'>
															<td>&nbsp;</td>
															<td>&nbsp;</td>
															<td class='align_center'>
																<?
																	$payroll_acc_q = $this->db->query("SELECT DISTINCT a.id, description, 
																									(SELECT DISTINCT b.`userid` FROM user_access_payroll b WHERE b.position_type=a.id AND b.userid='$uid') AS access
																										FROM code_position_type a");
																	if($payroll_acc_q->num_rows() > 0){
																		foreach ($payroll_acc_q->result() as $key => $row) {?>
																			<input type="checkbox" class="uniform menucheck" name="checkbox2" <?=($row->access!=null?" checked='checked'":"")?> root='<?=$row->id?>'><?=$row->description?>&nbsp;
																		<?}
																	}
																?>
															</td>
														</tr>
													<?}



												}
											?>
														</table>
													</td>
												</tr>
											<?
										}
									}
								?>
							</table>
						</td>
					<?
			}
	    }
	}
 }
?>
	</tr>
</table>
<script>
$(function(){
	var toks = hex_sha512(" ");
	$('.chosen').chosen();
	
    $(".allchecked").click(function(){
       var root = $(this).attr("root");
       var cstat = $(this).is(":checked");
       $("input[root='" + root + "']").prop("checked",cstat); 
    });
    $(".menucheck").click(function(){
       var root = $(this).attr("root");
       var uncheck = false;
       $("input[root='" + root + "']").each(function(){
          if(!$(this).is(":checked") && !uncheck && $(this).attr("tag")!="main") uncheck = true;
       }); 
       
       $("input[root='" + root + "'][tag='main']").prop("checked",!uncheck);
    });
    
    function figurestat(){
      $("input[tag='main']").each(function(){
        var root = $(this).attr("root");
        var uncheck = false;
        
        $("input[root='" + root + "']").each(function(){
          if(!$(this).is(":checked") && !uncheck && $(this).attr("tag")!="main") uncheck = true;
         }); 
       
        $(this).prop("checked",!uncheck);
        
      });
    }
    
    $("#button_save_modal").unbind("click").click(function(){
        var accesslist = "";
        var gate = "";
        var payroll = "";
        var lastroot = "";
        $(".menulist").each(function(){
          if($(this).find(".menucheck:first").is(":checked") || $(this).find(".menucheck:last").is(":checked")){
             accesslist += accesslist?",":"";
             accesslist += $(this).find(".menucheck:first").is(":checked")?1:0;
             accesslist += ":";
             accesslist += $(this).find(".menucheck:last").is(":checked")?1:0;
             accesslist += ":";
             accesslist += $(this).attr("menuid"); 
             if(lastroot!=$(this).attr("rootid")){
                 accesslist += accesslist?",":"";
                 accesslist += 1;
                 accesslist += ":";
                 accesslist += 1;
                 accesslist += ":";
                 accesslist += $(this).attr("rootid");
                 lastroot = $(this).attr("rootid");
             } 
          }
        }); 
        $(".gate").each(function(){
          /*if($(this).find(".menucheck:first").is(":checked") || $(this).find(".menucheck:last").is(":checked")){*/
             gate += gate?",":"";
             gate += $(this).find(".menucheck:first").is(":checked")?1:0;
             gate += ":";
             gate += $(this).find(".menucheck:last").is(":checked")?1:0;
             gate += ":";
             gate += "gateaccess";
          /*}*/
        });  

        var gate_allow = ams_allow = "";
        var allow_arr = ["gate-allow", "ams-allow"];
        for(i in allow_arr){
        	var allow_tmp = "";

        	$("."+ allow_arr[i]).find(".menucheck").each(function(){
        		if($(this).is(":checked")){
        			allow_tmp += (allow_tmp) ? "," : "";
	        		allow_tmp += $(this).val();
        		}
        	});

        	if(allow_arr[i] == "gate-allow") gate_allow = allow_tmp;
        	else 							 ams_allow = allow_tmp;
        }

        var ams = "";
        $(".ams").each(function(){
          /*if($(this).find(".menucheck:first").is(":checked") || $(this).find(".menucheck:last").is(":checked")){*/
             ams += ams?",":"";
             ams += $(this).find(".menucheck:first").is(":checked")?1:0;
             ams += ":";
             ams += $(this).find(".menucheck:last").is(":checked")?1:0;
             ams += ":";
             ams += "ams_access";
          /*}*/
        }); 

        $(".payroll-access .menucheck").each(function(){
          if($(this).is(":checked")){
             payroll += payroll?",":"";
             payroll += $(this).attr("root");
             // payroll += ":";
             // payroll += $(this).is(":checked")?1:0;
          }
        }); 

        $.ajax({
            url:"<?=site_url("maintenance_/saveaccess")?>",
            type:"POST",
            data:{
               uid:  GibberishAES.enc("<?=$uid?>" , toks),
               accesslist: GibberishAES.enc( accesslist, toks),
               gate :  GibberishAES.enc( gate, toks),
               payroll:  GibberishAES.enc(payroll , toks),
               ams :  GibberishAES.enc(ams , toks),
               gate_allow :  GibberishAES.enc( gate_allow, toks),
               ams_allow :  GibberishAES.enc( ams_allow, toks),
               toks:toks
            },
            success: function(msg){
            	Swal.fire({
			            icon: 'success',
			            title: 'Success!',
			            text: "User access saved successfully.",
			            showConfirmButton: true,
			            timer: 1000
			        })
                $("#modalclose").click();
                user_setup();
            }
         });
    });
    
    figurestat();
});
</script>