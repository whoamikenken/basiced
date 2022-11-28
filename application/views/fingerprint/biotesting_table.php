 <table class="table table-striped table-bordered table-hover" id="tablesbio" >
    <thead>
        <tr>
            <th align="center">Employee ID</th>
            <th align="center">Name</th>
            <th align="center">Campus</th>
            <th align="center">Teaching Type</th>
        </tr>
    </thead>
    <tbody id="employeelist" style="cursor: pointer;">
        <?php foreach($employee as $row): 
            ?>
            <tr employeeid='<?=$row['employeeid']?>' rfid='<?=$row['rfid']?>'>
                <td><?=$row['employeeid']?></td>
                <td><?=$row['fullname']?></td>
                <td><?=$row['campusid']?></td>
                <td><?=$row['teachingtype']?></td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>
<script>
    var toks = hex_sha512(" ");
    $(document).ready(function(){
        var table = $('#tablesbio').DataTable();
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#tablesbio #employeelist").delegate("tr", "click", function() {
        if($(this).attr("employeeid")){
            var form_data = {
                toks:toks,
                employeeid : GibberishAES.enc($(this).attr("employeeid"), toks),
                rfid : GibberishAES.enc($(this).attr("rfid"), toks),
                view: GibberishAES.enc("fingerprint/finger_Testing", toks)
            }; 

            $.ajax({
                url : $("#site_url").val() + "/main/siteportion",
                type: "POST",
                data: form_data,
                success: function(msg){
                    $("#ResgiteredEmp").html(msg);
                }
            });
        }
    });
    
</script>`