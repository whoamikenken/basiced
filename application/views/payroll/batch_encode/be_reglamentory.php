<div id="removeAni" class="panel animated fadeIn delay-1s">
    <div class="panel-heading"><h4><b>Reglementary Deductions</b></h4></div>
    <div class="panel-body emplist">
            <div class="form_row">
            	<div class="align_left col-md-12" style='float: left; padding-left: 0px; margin-top: 10px; margin-bottom: 10px;' id="div_save">
            		
            		<label class="col-md-2" style="padding-left: 0px; width: 20%; margin-top: 7px;">Reglementary Category&nbsp;: </label>
                    <div class="col-md-7"  style="width: 300px;">
                		<select class="form-control" id="reglamentory">
                        <option value=''> -Select Regulatory- </option>
                        <?
                        foreach ($reglamentory as $code) {?>
                            <option value='<?=strtolower($code)?>'><?=$code?></option>
                        <?}
                        ?>
                        </select>
                    </div>
            	</div>
                <div class="align_left col-md-12" style='float: left; padding-left: 0px; margin-top: 10px; margin-bottom: 10px;' id="div_save">
                    
                    <label class="col-md-2" style="padding-left: 0px; width: 20%; margin-top: 7px;">Quarter&nbsp;: </label>
                    <div class="col-md-7"  style="width: 300px;">
                        <select class="form-control span6" name="schedule" id="schedule">
                            <option value="">- Select Cut-Off -</option>
                            <option value="weekly">1st Cut-Off</option>
                            <option value="semimonthly">2nd Cut-Off</option>
                            <option value="monthly">All Cut-Off</option>
                        </select>
                    </div>
                </div>
            	<div class="align_right" id="div_loading" style="color: red" hidden>
            		<img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.
            	</div>
            </div>
            <div class="form_row" id='reglamentoryTable'>
            	<table class="table table-bordered table-hover datatable reglamentory_table" id="reglamentory_table" width="100%">
            		<thead style="background-color: #0072c6">
            			<tr valign="center">
            				<th style="text-align: center; width: 10%;">Employee ID</th>
	            			<th style="text-align: center; width: 20%;">Fullname</th>
	            			<th style="text-align: center; width: 30%; min-width: 160px">Amount</th>
	            			<th style="text-align: center; width: 20%;">Quarter</th>
	            			<th style="text-align: center; width: 10%;">Status</th>
	            			<th style="text-align: center; width: 15%;">Clear</th>
            			</tr>
            		</thead>
            		<tbody id='tbl_content'>
            		<?
            			$total_amount = 0;
        				foreach ($emplist as $empid => $info) {
        					$arr_code_deduction = array("sss", "philhealth", "pagibig");
        			?>
        				<tr id="tr~<?=$empid?>">
        					<td class="align_center"><?=$empid?></td>
        					<td class="align_center"><?=$info["fullname"]?></td>
        					<?
        						$arr_tag = array("new", "old");
        						foreach ($arr_tag as $tag) {
        							$isHidden = ($tag == "old") ? "hidden" : "";

        					?>		
        						<td class="align_left" tag="<?=$tag?>" tdname="amount" <?=$isHidden?>>
		        					<?
		        						foreach ($arr_code_deduction as $code_deduction) {
		        							$key = $code_deduction ."amount";
		        							$label = strtoupper($code_deduction);

		        							if(!$isHidden){
		        								$total_amount += $info[$key];
		        					?>
				        						<p id="<?=$code_deduction?>">
				        							<label><?=$label?></label>
				        							<input type="text" class="<?=$key?> form-control" style="width: 65%;float: right;display: inline;" name="<?=$key?>" id="<?=$key?>" value="<?=($info[$key]) ?  number_format((double)$info[$key], 2) : '' ?>">
				        						</p>
		        					<?	
		        							}else{
		        					?>
		        								<input type="text" id="<?=$key?> form-control" class="<?=$key?>" value="<?=number_format((double)$info[$key], 2)?>">
		        					<?		}
		        						} # end of foreach for arr_code_deduction
		        					?>
	        					</td>
	        					<td class="align_left" tag="<?=$tag?>" tdname="schedule" <?=$isHidden?>>
		        					<?
		        						foreach ($arr_code_deduction as $code_deduction) {
		        							$key = $code_deduction ."quarter";
		        							
		        							if(!$isHidden){
		        					?>
				        						<p id="<?=$code_deduction?>">
				        							<select class="form-control" name="<?=$key?>" id="<?=$key?>" >
														<?=$this->payrolloptions->quarter($info[$key],FALSE,$info['schedule'],TRUE);?>
													</select>
				        						</p>
		        					<?		}else{
		        					?>
		        								<input type="text" id="<?=$key?>" value="<?=number_format((double)$info[$key], 2)?>">
		        					<?		}
		        						} # end of foreach for arr_code_deduction
		        					
		        					?>
	        					</td>
	        					<td style="text-align: center;vertical-align: middle;" class="align_center" tag="<?=$tag?>" tdname="status" <?=$isHidden?>>
	        						<p id="regstatus"></p>
	        					</td>
        					<?	} # end of foreach for arr_tag
        					?>

                            <td style="text-align: center;vertical-align: middle;"><center><button id="clear" name="clear" class="btn btn-warning clear">CLEAR</button></center></td>
        				</tr>
        			<?
        				} # end of foreach for emplist
            		?>
            		</tbody>
            		<tfoot>
            			<tr>
            				<td class="align_right" colspan="2"><strong>Total Amount : </strong></td>
            				<td class="align_right"><strong><?=number_format($total_amount,2)?>&nbsp;&nbsp;</strong></td>
            				<td>&nbsp;</td>
            				<td>&nbsp;</td>
            			</tr>
            		</tfoot>
            	</table>
            	<br>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        setTimeout(function(){ 
            $("#reglamentory_table").removeClass("table-bordered table-hover datatable");
            $("#removeAni").removeClass("animated fadeIn delay-1s");
        }, 2000);
    </script>
    <script src="<?=base_url()?>js/batch_encode/be_reglamentory.js"></script>
