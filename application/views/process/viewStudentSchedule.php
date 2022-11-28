<style type="text/css">
    #message {
      /*background-color: #E0E0E0;*/
      /*-webkit-box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);
      -moz-box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);
      box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);*/

      -webkit-box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);
      -moz-box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);
      box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);

      padding: 5px;
      border-radius: 3px;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1;
      font-weight: bold;
      color: #FFFFFF;
      font-size: 16px;
    background-color:black ;
    }

    .hidden{
      visibility: hidden;
    }
</style>


<div id="message" class="hidden" >
  <span id="msgEIDtext"></span>
  <span id="msgEID" style="font-weight: bold;"></span>
  <span id="msgCnumtext"></span>
  <span id="msgCnum" style="font-weight: bold;"></span>
  <span id="msgEnd"></span>
</div>

<table id="dt_listcardsetup" class="table table-striped table-bordered table-hover">
<thead style="background-color: #0072c6;">
    <tr>
        <th class="col-md-1">#</th>
        <th class="sorting_asc">SY</th>
        <th class="sorting_asc">Department</th>
        <th class="sorting_asc">Year Level</th>
        <th class="sorting_asc">Section</th>
        <th class="sorting_asc">Time Start</th>
        <th class="sorting_asc">Time End</th>
        <th class="sorting_asc">Tardy Start</th>
        <th class="sorting_asc">Halfday Start</th>
        <th class="sorting_asc">Absent Start</th>
        <th class="sorting_asc">Applicable Date</th>
        
    </tr>
</thead>
<tbody>
	<?php
	$sql = $this->db->query("SELECT * FROM student_schedule_batch");
	$number = 1;
	foreach ($sql->result() as $row) {
	
	?>
	<tr  style="cursor: pointer;">
              <td><?=$number?></td>
              <td><?=$row->sy?></td>
              <td>
              	<?=$this->extras->showStudentDepartmentType($row->department,"");?>
              </td>
              <td><?=$row->yl?></td>
              <td><?=$row->section?></td>
              <td><?=date("H:i a",strtotime($row->timeStart))?></td>
              <td><?=date("H:i a",strtotime($row->timeEnd))?></td>
              <td><?=date("H:i a",strtotime($row->tardyStart))?></td>
              <td><?=date("H:i a",strtotime($row->halfdayStart))?></td>
              <td><?=date("H:i a",strtotime($row->absentStart))?></td>
              <td><?=$row->applicableDate?></td>
              
    </tr>
    <?php
    $number++;
    }
    ?>
	
</tbody>
</table>

<script type="text/javascript">
$(document).ready(function(){
    var table = $('#dt_listcardsetup').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
</script>