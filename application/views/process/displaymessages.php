<?php

/**
 * @author Justin
 * @copyright 2015
 */

#if($this->session->userdata("userid") == 3 || $this->session->userdata("userid") == 7){
$acc = $this->extras->showAccessmsg($this->session->userdata("userid"));    
if($acc = true){
$inbox = $this->extras->messages($cat,'',$dfrom,$dto);
if(count($inbox) > 0){
foreach($inbox as $row){
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
?>
<tr msgid='<?=$row->id?>' style="cursor: pointer;">
    <td><?=$row->sender?></td>
    <td><?=$msgdate?></td>
    <td><?=$msgrem?></td>
    <td><?=date('F d, Y',strtotime($row->timestamp))?></td>
</tr>
<?
}
}else{
?>
<tr>
    <td colspan="4" class="align_center">No Data Available..</td>
</tr>
<?
}
}else{
?>
    <tr>
    <td colspan="4" class="align_center">No Data Available..</td>
    </tr>
<?
}
?>
<script>

$("#contents tr").click(function(){
    var form_data = {
                        id  : $(this).attr("msgid"), 
                        view: "process/svmsg"
                    };
    $.ajax({
        url : "<?=site_url("main/siteportion")?>",
        type : "POST",
        data : form_data,
        success: function(msg){
           $("#dialog").html(msg).dialog({
                dialogClass: "no-close",
                title: "Messages",
                modal: 'false',
                position: ["center", 200],
                show: { effect: "blind", duration: 1000 },
                hide: { effect: "explode", duration: 1000 },
                width: 'auto',
                height: 'auto',
                resizable: false,
                closeOnEscape: false,
                draggable: false,
                buttons: [
                {
                  text: "Close",
                  click: function() {
                    $(this).dialog("close");
                  }
                }
               ]
           });
          // $("#dialog").css("background","#99CCFF");
       }
    });
});

</script>
                                