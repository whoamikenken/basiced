<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */


$que = $this->timesheet->loglist_user($limits,date("Y-m-d")); 
$lq = $this->db->last_query();
$macadd = $this->extras->returnmacaddress();
list($stat,$smessage,$sdescription) = $this->timesheet->machinedisplaystatus($macadd);

$stat = 1;
?>
<user>
  <candisplay><?=$stat?></candisplay>
  <display>
      <table class="listview">
          <tr>
            <th style="text-align: center;" width='100px'>DATE</th>
            <th style="text-align: center;" width='100px'>TIME</th>
            <th style="text-align: center;" width='50px'>TYPE</th>
            <th style="text-align: center;" width='100px'>TERMINAL</th>
            <th width='200px'>NAME</th>
          </tr>
        <?
        if(count($que)>0){
            # for($c=0;$c<$que->num_rows();$c++){
            foreach($que as $mrow){   
                # $mrow = $que->row($c);
                list($stat,$smessage,$sdescription) = $this->timesheet->machinedisplaystatus($mrow->mac_add);
        ?>
          <tr>
            <td style="text-align: center;"><?=date("m/d/Y",strtotime($mrow->logtime))?></td>
            <td style="text-align: center;"><?=date("h:i A",strtotime($mrow->logtime))?></td>
            <td style="text-align: center;"><?=$mrow->log_type?></td>
            <td style="text-align: center;"><?=$sdescription?></td>
            <td><?=$mrow->lname.", ".$mrow->fname.($mrow->mname ? " ".substr($mrow->mname,0,1) : "")?></td>
          </tr>
        <?    
            }
        }else{
        ?>
          <tr>
            <td colspan="5" style="text-align: center;">EMPTY LIST </td>
          </tr>
        <?    
        }
        ?>  
      </table>
  </display>
</user>