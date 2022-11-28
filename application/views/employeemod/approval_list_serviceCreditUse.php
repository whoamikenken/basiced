



    <center>
 <table width="70%" >
                <tr>
                   
                    <th>Position</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Date Updated</th>
                </tr>
                <?
                    foreach ($arr_aprvl_seq as $key => $obj) {
                        // echo'<pre>';var_dump($arr_aprvl_seq);
                        ?>

                        <tr>
                            
                            <td class="align_center"><strong><?=$obj['position_name']?> </strong></td>
                            <td class="align_center"><strong><?=$obj['fullname']?>      </strong></td>
                            <td class="align_center">
                                <strong><?=$obj['status']?($obj['status'] <> 'PENDING' ? "<a class='btn ".($obj['status'] == "DISAPPROVED" ? "red" : "green")." '>".$obj['status']."</a>" : ""):"";?></strong>
                            </td>
                            <td class="align_center"><strong><?=($obj['date'] && $obj['date'] != "0000-00-00") ? date("F d, Y",strtotime($obj['date'])) : ""?></strong></td>
                        </tr>
                    <?}

                ?> 
            </table>
            <br>
            <div id="saving" style="text-align: right;width:70%" >
                <button type="button" id="close" class="btn btn-danger" >Close</button>
            </div>
</center>
   
<script type="text/javascript">
    $("#close").click(function()
    {
        $("#Usage").hide();
    });
</script>