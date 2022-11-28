<?php

/**
 * @author Justin
 * @copyright 2015
 */

if(!empty($job) == "save"){
    $query = $this->db->query("UPDATE messages SET status='$stat' WHERE id='$id'");
    if($query) $msg = "Successfully Saved..";
    ?>
    <user>
        <msg><?=$msg?></msg>
    </user>
    <?
}

$queue = $this->extras->messages("",$id);
foreach($queue as $row){
    $sender = $row->sender;
    $status = $row->status;
    $tstamp = date('F d, Y',strtotime($row->timestamp)); 
    $msgdate = "";$msgrem = "";
    $dt  = explode('*',$row->date);
    $rem = explode('|',$row->description);
                                    
    for($x = 0; $x<count($dt); $x++){
        if(!empty($msgdate)){
            $msgdate .= "<br />";
            $msgrem  .= "<br />";
        } 
        $msgdate .= $dt[$x] == "" ? "" : date('F d, Y',strtotime($dt[$x])) ; 
        $msgrem  .= $rem[$x];
    }
}
?>
<table width='100%' class="table table-bordered table-striped table-hover">
    <tr>
        <td colspan="3" class="align_center"><strong>Messages</strong></td>
    </tr>
    <tr>
        <td class="field_name"><strong>Sender</strong></td>
        <td colspan="2"><?=$sender?></td>
    </tr>
    <tr>
        <td class="field_name"><strong>Date</strong></td>
        <td><?=$msgdate?></td>
        <td><?=$msgrem?></td>
    </tr>
    <tr>
        <td class="field_name"><strong>Status</strong></td>
        <td colspan="2">
        
            <select id="stat">
                <?foreach($this->extras->showcstat() as $key=>$val){
                $selected = ($key == $status) ? " selected" : "";
                ?>
                <option value="<?=$key?>" <?=$selected?>><?=$val?></option>
                <?}?>
            </select>
            </td>
    </tr>
    <tr>
        <td class="field_name"><strong>Date Sent</strong></td>
        <td colspan="2"><?=$tstamp?></td>
    </tr>
</table>
<script>
$('.chosen').chosen();     
$("#stat").change(function(){
   var form_data =  {
                        job : "save",
                        stat: $(this).val(),
                        view: "process/svmsg",
                        id  : "<?=$id?>"
                    }
   $.ajax({
    url : "<?=site_url("main/siteportion")?>",
    type: "POST",
    data: form_data,
    success: function(msg){
        var message = $(msg).find("msg").text();
        alert(message);
        location.reload();
    }
   });
    
});
</script>