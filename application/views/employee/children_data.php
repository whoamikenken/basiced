<?php
$youngestCounter = 1;
$applicant_child = $childrenlist;
$b_order = Globals::birthOrder();
if(count($applicant_child)>0){
    foreach($applicant_child as $eb){
        ?>
        <tr>
            <td class="testinglang"><?=strtoupper($eb->name)?></td>
            <td><?=strtoupper($eb->gender)?></td>
            <td><?= ($youngestCounter != 1 && $youngestCounter == count($applicant_child)) ? "Youngest" : $b_order[$eb->birthorder] ; ?> </td>
            <td><?=strtoupper($eb->birthdate)?></td>
            <td><?=strtoupper($eb->age)?></td>
            <td>
                <button type="button" class='btn btn-info echildren' href='#infoModal' tbl_id="<?=$eb->id?>" data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></button>&nbsp;
                <button type="button" tbl_id="<?=$eb->id?>" class='btn btn-warning delete_entry'><i class='glyphicon glyphicon-trash'></i></button>
            </td>
        </tr>  

        <?   
        $youngestCounter++;                         
    }
}else{
    ?>
    <tr>
        <td colspan="6">No existing data</td>
    </tr>
    <?                    
}
?>
<script>
    $(".echildren").click(function(){
        addchildren($(this));
    });

    $(".delete_entry").click(function(){
        var mtable = $("#childrenlist").find("tbody");
        if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
        $(this).parent().parent().remove();
        delete_entry($(this), $(this).attr("tbl_id"));
    });
</script>