<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
$today = date('Y-m-d g:i:s');
// $action
$CI =& get_instance();
$CI->load->model('facial'); 
?>
<table class="table table-striped table-bordered table-hover" id="logsTable">
    <thead>                        
        <tr >
            <th><b>Time</b></th>
            <th><b>I.P</b></th>
            <th><b>Name</b></th>
            <th><b>Employee ID</b></th>
            <th><b>Device I.D</b></th>
            <th><b>Image</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $key => $value): ?>
               <tr>
                    <td><?php echo date("Y-m-d g:i:s A", substr($value->time, 0, 10)) ?></td>
                    <td><?php echo $ip ?></td>
                    <td><?php echo ($value->personId != "STRANGERBABY")? $this->facial->getPersonNamePersonID($value->personId): "STRANGER" ?></td>
                    <td><?php echo ($value->personId != "STRANGERBABY")? $this->facial->getEmpIdFacial($value->personId): "STRANGER" ?></td>
                    <td><?php echo $deviceKey ?></td>
                    <td><img class="photo" src="" path="<?php echo $value->path ?>" width="150" height="200"></td>
                </tr> 
        <?php endforeach ?>
    </tbody>
</table>
<script>
var today = "<?= $today ?>";
$(document).ready(function(){
    var oTable = $("#logsTable").DataTable({
        lengthMenu: [[5,10, 25, 50, 100, -1], [5,10, 25, 50, 100, "All"]],
        "pageLength": 5,
    });
    loadImage();
});

$('#logsTable').on( 'draw.dt', function () {
    loadImage();
} );

$("#load").click(function(){
   loadFacialDevice(serial_number,ip);
});

function loadImage(){
    $(".photo").each(function() {  
        var path = $(this).attr("path");
        var img = $(this);
        $.ajax({
            url: "http://localhost:8098/api/converterFTP",
            type: "POST",
            contentType: 'application/json',
            data: JSON.stringify({"link": path,"base64": "string"}),
            async: false,
            success: function(response) {
                img.attr("src", "data:image/jpeg;base64,"+response.base64);
            }
        });
      });  
}

</script>