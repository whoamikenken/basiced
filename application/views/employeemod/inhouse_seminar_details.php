<thead style="background-color: #0072c6;">
    <tr>
        <th class="align_left span1">Employee ID</th>
        <th class="align_left span1">Fullname</th>
        <th class="align_left span1">Department</th>
        <th class="align_left span1" width="5%">Details</th>
        <th class="align_left span1"  <?= $isgoing == "1" ? "style=display:none": ''; ?>>Reason</th>
        <th class="align_left span1">Mark as read &emsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
    </tr>
</thead>   
<tbody id="attendees_list">
    <?php foreach($record as $records): ?>
        <tr id="<?=$records['id']?>" <?=(!$records['isread'] ? "style=background-color:#B4CDC6" : '')?>>
            <td><?=$records["employeeid"]?> </td>
            <td><?=Globals::_e($records["fullname"])?></td>
            <td><?=Globals::_e($records["description"])?></td>
            <td class="align_center"><a seminarid="<?= $records['seminarid'] ?>" href="#modal-view" data-toggle="modal" class="align_center seminarDetails"><span class="glyphicon glyphicon-eye-open align_center" style="cursor: pointer; font-size: 16px;"></span></a></td>
            <td <?= $isgoing == "1" ? "style=display:none": ''; ?>><?=Globals::_e($records["reason"])?></td>
            <td class="align_center"><input class="double-sized-cb mar" type="checkbox" value="1" name="mar" idkey="<?=$records['id']?>" <?=($records['isread'] ? " checked disabled" : "") ;?> /></td>
        </tr>
    <?php endforeach ?>
</tbody>

<input type="hidden" id="last_query" value="<?= $lastQuery ?>">
<script>

    var adminnotif = 0;
    attendeesAdminNotifCount();

    $("#selectall").click(function(){
        $("input[name='mar']").each(function(){
            $(this).click();
        })
    })

    $("input[name='mar']").change(function(){
        var notifcount = "";
        var id = $(this).attr("idkey");
        $.ajax({
            url: "<?= site_url('seminar_/attendeesMarkread') ?>",
            type: "POST",
            data: {id, id},
            success:function(response){
                $("tr[id='"+ id +"']").find(".mar").attr("disabled", true);
                $("tr[id='"+ id +"']").css("background-color", "unset");
                if("<?=$this->session->userdata('usertype')?>" == "EMPLOYEE"){
                    notifcount = $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").find(".notifcount").find("b").text();
                    notifcount -= 1;
                    if(notifcount <= 0) $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").hide();
                }else{
                    adminnotif = adminnotif;
                    adminnotif -= 1;
                    if(adminnotif <= 0) $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").hide();
                }
            }
        });
    });

    $(".seminarDetails").click(function(){
        loadSeminarDetails($(this).attr("seminarid"));
    })

    function attendeesAdminNotifCount(){
        $.ajax({
            url: "<?= site_url('seminar_/attendeesAdminNotifCount') ?>",
            async: false,
            success:function(response){
                adminnotif = response;
            }
        });
        return adminnotif;
    }

    function loadSeminarDetails(seminarid=''){
        $("#modal-view").find("h3[tag='title']").text("Seminar Details");
        $("#modal-view").find("#button_save_modal").css("display", "none");
        $.ajax({
            url: "<?= site_url('seminar_/seminar_details') ?>",
            type: "POST",
            data: {seminarid:seminarid},
            success:function(res){
                $("#modal-view").find("div[tag='display']").html(res);
            }
        })
    }

    $("#modalclose").click(function(){
        unhidesave();
    })

    document.onkeydown = function(evt) {
        evt = evt || window.event;
        if (evt.keyCode == 27) {
            unhidesave();
        }
    };

    function unhidesave(){
        setTimeout(function(){
            $("#modal-view").find("#button_save_modal").css("display", "unset");
        }, 500)
    }
</script>