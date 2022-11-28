<table class="table table-bordered table-striped table-hover" id="accomplish_table">
    <thead>
        <tr>
            <td class="align_center">Employee ID</td>
            <td>Fullname</td>
            <td class="align_center">Check In</td>
            <td class="align_center">Check Out</td>
            <td class="align_center">Attachment</td>
            <td class="align_center">Remarks</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>    
            <tr>
                <td class="align_center"><?=$row["employeeid"] ?></td>
                <td class="align_center"><?=$this->extensions->getEmployeeName($row["employeeid"]) ?></td>
                <td class="align_center"><?=date("F d, Y h:i A", strtotime($row["timefrom"])) ?></td>
                <td class="align_center"><?=date("F d, Y h:i A", strtotime($row["timeto"])) ?></td>
                <td class="align_center attachment" file="<?= $row["content"] ?>" mime="<?= $row["mime"] ?>"><i><?=$row["filename"]?></i></td>
                <td class="align_center"><?=$row["remarks"] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<script>
    $(document).ready(function(){
        var table = $('#accomplish_table').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#accomplish_table").on("click", ".attachment", function(){
        if($(this).attr("file")){
            var data = $(this).attr("file");
            var mime = $(this).attr("mime");
            var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';

            window.open(objectURL);
        }else{
            var file_url = $(this).attr("content");
            window.open(file_url);
        }
    });

    function b64toBlob(b64Data, contentType) {
        var byteCharacters = atob(b64Data)
        var byteArrays = []
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            var slice = byteCharacters.slice(offset, offset + 512),
                byteNumbers = new Array(slice.length)
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i)
            }
            var byteArray = new Uint8Array(byteNumbers)

            byteArrays.push(byteArray)
        }

        var blob = new Blob(byteArrays, { type: contentType })
        return blob
    }

</script>