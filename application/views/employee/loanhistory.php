<?
$filter = $this->input->post('filter');
$empdetails = $this->input->post('empdetails');
$wc = "";

if ($filter == "transaction") {$wc =" AND mode='CUTOFF'";}
else if ($filter == "action") {$wc =" AND (mode='UPDATE' OR mode='DELETED')";}
else
{
	$wc = "AND mode='CUTOFF'";
}
?>
	<?
		$query = $this->db->query("SELECT * FROM employee_loan_history WHERE employeeid='$empdetails' $wc ORDER BY timestamp");
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row){
				?>
					<tr>
						<td><?=$this->payrolloptions->loandesc($row->code_loan)?></td>
						<td><?=($row->cutoffstart != "0000-00-00" ? ($row->cutoffend != "0000-00-00" ? date('F d, Y',strtotime($row->cutoffstart))." - ".date('F d, Y',strtotime($row->cutoffend)):date('F d, Y',strtotime($row->cutoffstart))) : "")?></td>
						<td><?=$row->startBalance?></td>
						<td><?=$row->amount?></td>
						<td><?=$row->remainingBalance?></td>
						<td><?=$row->mode?></td>
						<td><?=$row->timestamp?></td>
					</tr>
				<?
			}
		}
		else{?>
			<td colspan='7' style="text-align: center">NO RECORD FOUND....</td>	
		<?}	
	?>
